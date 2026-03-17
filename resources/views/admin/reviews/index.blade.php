<x-layouts.admin>
    <x-slot name="title">Reviews</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Reviews</h1>
            <p class="text-neutral-600">Manage product reviews</p>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $totalReviews    = \App\Models\Review::count();
        $approvedReviews = \App\Models\Review::where('status', 'approved')->count();
        $pendingReviews  = \App\Models\Review::where('status', 'pending')->count();
        $rejectedReviews = \App\Models\Review::where('status', 'rejected')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Total</p>
                <p class="text-2xl font-bold text-neutral-900">{{ $totalReviews }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-success-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Approved</p>
                <p class="text-2xl font-bold text-success-600">{{ $approvedReviews }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-warning-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Pending</p>
                <p class="text-2xl font-bold text-warning-600">{{ $pendingReviews }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-danger-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Rejected</p>
                <p class="text-2xl font-bold text-danger-600">{{ $rejectedReviews }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'status', 'rating']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="text-sm font-medium text-neutral-700">Filters & Search</span>
                @if(request()->hasAny(['search', 'status', 'rating']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-5 h-5 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-collapse class="px-5 pb-4 border-t border-neutral-200">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-input w-full" placeholder="Product name or customer...">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select w-full">
                            <option value="">All Statuses</option>
                            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                            <option value="flagged" @selected(request('status') === 'flagged')>Flagged</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select w-full">
                            <option value="">All Ratings</option>
                            <option value="5" @selected(request('rating') === '5')>5 Stars</option>
                            <option value="4" @selected(request('rating') === '4')>4 Stars</option>
                            <option value="3" @selected(request('rating') === '3')>3 Stars</option>
                            <option value="2" @selected(request('rating') === '2')>2 Stars</option>
                            <option value="1" @selected(request('rating') === '1')>1 Star</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                    @if(request()->hasAny(['search', 'status', 'rating']))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        @if($reviews->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $reviews->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Product</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Customer</th>
                        <th class="px-5 py-3 text-center text-xs font-medium text-neutral-600 uppercase tracking-wider">Rating</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Review</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider hidden sm:table-cell">Date</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-neutral-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-neutral-900 truncate max-w-37.5 text-sm">{{ $review->product->name ?? 'Deleted Product' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-neutral-600">
                                {{ $review->user ? $review->user->first_name . ' ' . $review->user->last_name : 'Guest' }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-neutral-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($review->title)
                                    <p class="font-medium text-neutral-900 text-sm truncate max-w-45">{{ $review->title }}</p>
                                @endif
                                @if($review->content)
                                    <p class="text-sm text-neutral-600 truncate max-w-45">{{ $review->content }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-col gap-1">
                                    @if($review->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($review->status === 'rejected')
                                        <span class="badge badge-error">Rejected</span>
                                    @elseif($review->status === 'flagged')
                                        <span class="badge badge-warning">Flagged</span>
                                    @else
                                        <span class="badge badge-info">Pending</span>
                                    @endif
                                    @if($review->is_verified_purchase)
                                        <span class="badge badge-success">Verified</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-neutral-600 hidden sm:table-cell">
                                {{ $review->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="btn-icon" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-danger-500 hover:text-danger-700 hover:bg-danger-50" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-neutral-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-neutral-700 mb-1">No reviews found</p>
                                    <p class="text-sm text-neutral-600 mb-3">
                                        @if(request()->hasAny(['search', 'status', 'rating']))
                                            No reviews match your current filters.
                                        @else
                                            No reviews have been submitted yet.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'status', 'rating']))
                                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
