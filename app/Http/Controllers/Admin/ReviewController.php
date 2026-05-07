<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['product:id,name,slug', 'user:id,first_name,last_name,email']);

        $status = $request->input('status', '');

        if ($status === 'approved') {
            $query->where('status', 'approved')->where('is_approved', true);
        } elseif ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'rejected') {
            $query->where('status', 'rejected');
        }

        // Counts for tabs
        $counts = [
            'all'      => Review::count(),
            'pending'  => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        $perPage = $request->input('per_page', 10);
        $reviews = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'counts', 'status'));
    }

    public function pending(): View
    {
        $perPage = request()->input('per_page', 10);
        $reviews = Review::where('status', 'pending')
            ->with(['product:id,name,slug', 'user:id,first_name,last_name,email'])
            ->latest()
            ->paginate($perPage)->withQueryString();

        return view('admin.reviews.pending', compact('reviews'));
    }

    public function show(Review $review): View
    {
        $review->load(['product', 'user']);

        return view('admin.reviews.show', compact('review'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string|max:5000',
        ]);

        $review->update($validated);
        $review->product?->updateRating();

        return back()->with('success', 'Review updated successfully.');
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update([
            'is_approved'   => true,
            'status'        => 'approved',
            'moderated_by'  => auth()->id(),
            'moderated_at'  => now(),
        ]);

        return back()->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update([
            'is_approved'   => false,
            'status'        => 'rejected',
            'moderated_by'  => auth()->id(),
            'moderated_at'  => now(),
        ]);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted.');
    }

}
