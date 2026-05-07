<x-layouts.app>
    <x-slot name="title">Write a Review – {{ $product->name }}</x-slot>

    @include('account.partials.sidebar')

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-neutral-500 mb-6">
        <a href="{{ route('account.dashboard') }}" class="hover:text-[#202a40] transition-colors">Account</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-neutral-900 font-medium">Write a Review</span>
    </div>

    @if($existingReview)
        {{-- Already reviewed state --}}
        <div class="bg-white rounded-2xl border border-neutral-200 p-8 text-center max-w-md">
            <div class="w-12 h-12 mx-auto bg-green-50 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-base font-semibold text-neutral-900 mb-1">Already Reviewed</h3>
            <p class="text-sm text-neutral-500 mb-4">You've already submitted a review for this product.</p>
            <a href="{{ route('account.dashboard') }}" class="inline-flex items-center gap-2 bg-[#202a40] text-white text-sm font-semibold px-4 py-2 rounded-xl">
                Back to Dashboard
            </a>
        </div>
    @else
        <div class="max-w-2xl space-y-4">

            {{-- Product card --}}
            <div class="bg-white rounded-2xl border border-neutral-200 p-4 flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-neutral-100 shrink-0 border border-neutral-100">
                    <img src="{{ $product->primary_image_url ?? '' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('product.show', $product) }}" class="text-sm font-semibold text-neutral-900 hover:text-[#202a40] line-clamp-1 transition-colors">{{ $product->name }}</a>
                    @if($product->brand)
                        <p class="text-xs text-neutral-500 mt-0.5">{{ $product->brand->name }}</p>
                    @endif
                    @if($hasPurchased)
                        <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-semibold text-[#202a40]">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Verified Purchase
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 mt-1 text-[11px] text-neutral-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
                            Not yet purchased
                        </span>
                    @endif
                </div>
            </div>

            {{-- Write a Review — collapsible --}}
            <div x-data="{ open: false }" class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">

                {{-- Toggle button --}}
                <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-neutral-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#202a40]/8 flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-neutral-900">Write a Customer Review</span>
                    </div>
                    <svg class="w-4 h-4 text-neutral-400 transition-transform duration-200" :class="open && 'rotate-180'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Form dropdown --}}
                <div x-show="open" x-collapse>
                    <div class="border-t border-neutral-100 px-5 py-5">
                        <form action="{{ route('account.reviews.store', $product) }}" method="POST" class="space-y-4">
                            @csrf

                            {{-- Star Rating --}}
                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-2">Your Rating <span class="text-red-500">*</span></label>
                                <div x-data="{ rating: 0, hover: 0 }" class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                @click="rating = {{ $i }}"
                                                @mouseenter="hover = {{ $i }}"
                                                @mouseleave="hover = 0">
                                            <svg class="w-7 h-7 transition-colors"
                                                 :fill="(hover || rating) >= {{ $i }} ? '#f5a623' : '#e5e7eb'"
                                                 viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" :value="rating">
                                    <span class="ml-2 text-xs text-neutral-400" x-text="['','Poor','Fair','Good','Very Good','Excellent'][rating] || 'Select rating'"></span>
                                </div>
                            </div>

                            {{-- Title --}}
                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Review Title <span class="text-neutral-400 font-normal">(optional)</span></label>
                                <input type="text" name="title" value="{{ old('title') }}"
                                       placeholder="Sum up your experience in a few words"
                                       class="w-full px-3 py-2 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#202a40] focus:bg-white transition-colors">
                            </div>

                            {{-- Review body --}}
                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Your Review <span class="text-red-500">*</span></label>
                                <textarea name="content" rows="4" placeholder="What did you like or dislike? How was the quality, fit, or delivery?"
                                          class="w-full px-3 py-2 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#202a40] focus:bg-white transition-colors resize-none">{{ old('content') }}</textarea>
                            </div>

                            @if($errors->any())
                                <div class="text-xs text-red-500 space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <p>• {{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center gap-3 pt-1">
                                <button type="submit"
                                        class="px-5 py-2 bg-[#202a40] text-white text-sm font-semibold rounded-xl hover:bg-[#2d3a55] transition-colors">
                                    Submit Review
                                </button>
                                <button type="button" @click="open = false"
                                        class="px-4 py-2 text-sm font-medium text-neutral-500 hover:text-neutral-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Top Reviews --}}
            <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-neutral-900">Top Reviews</h2>
                    @if($productReviews->count())
                        <span class="text-xs text-neutral-400">{{ $productReviews->count() }} review{{ $productReviews->count() > 1 ? 's' : '' }}</span>
                    @endif
                </div>

                @if($productReviews->count())
                    <div class="divide-y divide-neutral-100">
                        @foreach($productReviews as $review)
                            <div x-data="{ open: false }">
                                <button type="button" @click="open = !open"
                                        class="w-full flex items-center justify-between px-5 py-3.5 hover:bg-neutral-50/60 transition-colors text-left">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-[#202a40]/10 flex items-center justify-center shrink-0 text-xs font-bold text-[#202a40]">
                                            {{ strtoupper(substr($review->reviewer_name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-semibold text-neutral-800 truncate">{{ $review->reviewer_name ?? 'Anonymous' }}</p>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3" fill="{{ $i <= $review->rating ? '#f5a623' : '#e5e7eb' }}" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-[11px] text-neutral-400 ml-1">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-neutral-300 shrink-0 transition-transform duration-200" :class="open && 'rotate-180'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open" x-collapse>
                                    <div class="px-5 pb-4 pt-1 border-t border-neutral-50">
                                        @if($review->title)
                                            <p class="text-[13px] font-semibold text-neutral-800 mb-1">{{ $review->title }}</p>
                                        @endif
                                        <p class="text-[13px] text-neutral-600 leading-relaxed">{{ $review->content }}</p>
                                        @if($review->is_verified_purchase)
                                            <span class="inline-block mt-2 text-[11px] font-semibold text-[#b45309]">✓ Verified Purchase</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-8 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full bg-neutral-100 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-neutral-400">No reviews yet. Be the first to review!</p>
                    </div>
                @endif
            </div>

        </div>
    @endif

    @include('account.partials.sidebar-end')
</x-layouts.app>
