<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = $request->user()->reviews()
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('account.reviews.index', compact('reviews'));
    }

    public function create(Request $request, Product $product): View
    {
        // Check if user has purchased this product
        $hasPurchased = $request->user()->orders()
            ->where('status', 'completed')
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->exists();

        // Check if user already reviewed this product
        $existingReview = $request->user()->reviews()
            ->where('product_id', $product->id)
            ->first();

        $productReviews = Review::where('product_id', $product->id)
            ->where('is_approved', true)
            ->latest()
            ->take(10)
            ->get();

        return view('account.reviews.create', compact('product', 'hasPurchased', 'existingReview', 'productReviews'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        // Duplicate check for auth users
        if ($request->user()) {
            $existingReview = $request->user()->reviews()
                ->where('product_id', $product->id)
                ->first();
            if ($existingReview) {
                return back()->with('error', 'You have already reviewed this product.');
            }
        }

        $validated = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'title'          => 'nullable|string|max:255',
            'content'        => 'required|string|max:2000',
            'pros'           => 'nullable|string|max:1000',
            'cons'           => 'nullable|string|max:1000',
            'reviewer_name'  => 'nullable|string|max:100',
            'reviewer_email' => 'nullable|email|max:255',
        ]);

        if (!empty($validated['pros'])) {
            $validated['pros'] = array_filter(array_map('trim', explode("\n", $validated['pros'])));
        }
        if (!empty($validated['cons'])) {
            $validated['cons'] = array_filter(array_map('trim', explode("\n", $validated['cons'])));
        }

        $hasPurchased = $request->user()
            ? $request->user()->orders()
                ->where('status', 'completed')
                ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                ->exists()
            : false;

        $guestName  = $validated['reviewer_name'] ?? ($request->user()?->name);
        $guestEmail = $validated['reviewer_email'] ?? null;

        unset($validated['reviewer_name'], $validated['reviewer_email']);

        Review::create([
            'user_id'              => $request->user()?->id,
            'product_id'           => $product->id,
            'guest_name'           => $guestName,
            'guest_email'          => $guestEmail,
            'rating'               => $validated['rating'],
            'title'                => $validated['title'] ?? null,
            'content'              => $validated['content'],
            'pros'                 => $validated['pros'] ?? null,
            'cons'                 => $validated['cons'] ?? null,
            'status'               => 'pending',
            'is_approved'          => false,
            'is_verified_purchase' => $hasPurchased,
        ]);

        return redirect()->route('product.show', $product)
            ->with('review_success', 'Thank you! Your review will be published after moderation.');
    }
}
