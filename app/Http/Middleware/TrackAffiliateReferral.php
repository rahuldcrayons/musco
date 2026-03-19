<?php

namespace App\Http\Middleware;

use App\Models\Affiliate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackAffiliateReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        $refCode = $request->query('ref');

        if ($refCode) {
            $affiliate = Affiliate::where('referral_code', $refCode)
                ->where('status', 'approved')
                ->first();

            if ($affiliate) {
                // Store in session for checkout
                session(['affiliate_ref' => $refCode]);

                // Debounce click tracking — only count once per session
                $sessionKey = 'affiliate_click_' . $refCode;
                if (!session()->has($sessionKey)) {
                    $affiliate->increment('total_clicks');
                    session([$sessionKey => true]);
                }

                // Set cookie and pass response through
                $response = $next($request);

                $cookieName = config('affiliate.cookie_name', 'jikra_ref');
                $cookieDays = config('affiliate.cookie_duration_days', 30);

                return $response->withCookie(
                    cookie($cookieName, $refCode, $cookieDays * 24 * 60)
                );
            }
        }

        // Also restore from cookie if no query param but cookie exists
        if (!session('affiliate_ref')) {
            $cookieName = config('affiliate.cookie_name', 'jikra_ref');
            $cookieRef = $request->cookie($cookieName);

            if ($cookieRef) {
                session(['affiliate_ref' => $cookieRef]);
            }
        }

        return $next($request);
    }
}
