<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:100',
        ]);

        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->is_active) {
                $message = 'This email is already subscribed!';
                return $request->expectsJson()
                    ? response()->json(['success' => true, 'message' => $message])
                    : back()->with('newsletter_success', $message);
            }

            // Re-subscribe
            $existing->update([
                'is_active'        => true,
                'subscribed_at'    => now(),
                'unsubscribed_at'  => null,
                'source'           => $request->input('source', 'homepage'),
                'ip_address'       => $request->ip(),
            ]);

            $message = 'Welcome back! You have been re-subscribed.';
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => $message])
                : back()->with('newsletter_success', $message);
        }

        NewsletterSubscriber::create([
            'email'         => $validated['email'],
            'name'          => $validated['name'] ?? null,
            'source'        => $request->input('source', 'homepage'),
            'is_active'     => true,
            'subscribed_at' => now(),
            'ip_address'    => $request->ip(),
        ]);

        // Facebook CAPI: Subscribe
        app(AnalyticsService::class)->trackSubscribe($validated['email'], $request);

        $message = "You're subscribed! Check your inbox for your 15% off coupon.";
        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : back()->with('newsletter_success', $message);
    }
}
