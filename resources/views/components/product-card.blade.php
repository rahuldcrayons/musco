@props(['product', 'showQuickView' => true, 'compact' => false])

@php
    $discount = $product->discount_percentage ?? 0;
    $hasDiscount = $product->price < $product->mrp;
    $rating = $product->rating ?? 0;
    $reviewCount = $product->review_count ?? 0;
    $outOfStock = !$product->isInStock();

    // Read hover action settings (cached for 1hr by Setting::get)
    $showWishlist = \App\Models\Setting::get('product_card_wishlist', true);
    $showAddToCart = \App\Models\Setting::get('product_card_add_to_cart', true);
    $showQuickViewBtn = $showQuickView && \App\Models\Setting::get('product_card_quick_view', true);
    $hasHoverActions = $showWishlist || $showQuickViewBtn;

    // Placeholder image
    $placeholderImage = asset('images/placeholder-product.svg');
@endphp

@if($compact)
    {{-- Compact card for horizontal scrollable rows --}}
    <div {{ $attributes->merge(['class' => 'group shrink-0 w-full flex flex-col h-full']) }}>
        <a href="{{ route('product.show', $product) }}" class="block relative">
            <div class="aspect-square rounded-xl overflow-hidden mb-2">
                <img src="{{ $product->primary_image_url }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-300"
                     loading="lazy"
                     onerror="this.src='{{ $placeholderImage }}'">
            </div>
            @if($hasDiscount)
                <span class="absolute top-2 left-2 bg-[#CC0C39] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-sm">{{ round($discount) }}% off</span>
            @endif
        </a>

        <a href="{{ route('product.show', $product) }}" class="block px-0.5">
            <h3 class="text-[13px] text-[#0F1111] line-clamp-2 mb-1 leading-snug min-h-[2.5rem] hover:text-[#C7511F] transition-colors">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Star Rating --}}
        <div class="flex items-center gap-1 mb-1 px-0.5">
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($rating))
                        <svg class="w-3.5 h-3.5 text-[#205258]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.25)
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20">
                            <defs><linearGradient id="half-star-compact"><stop offset="50%" stop-color="#205258"/><stop offset="50%" stop-color="#E0E0E0"/></linearGradient></defs>
                            <path fill="url(#half-star-compact)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @else
                        <svg class="w-3.5 h-3.5 text-[#E0E0E0]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endif
                @endfor
            </div>
            <span class="text-[11px] text-[#007185]">({{ $reviewCount }})</span>
        </div>

        <div class="flex items-baseline gap-1 flex-wrap px-0.5 mb-1">
            <span class="text-[15px] font-medium text-[#0F1111]">@price($product->price)</span>
            @if($hasDiscount)
                <span class="text-[11px] text-[#565959] line-through">@price($product->mrp)</span>
            @endif
        </div>

        <div class="h-4 px-0.5 mb-1">
            @if($hasDiscount)
                <p class="text-[11px] text-[#CC0C39] font-medium">Save {{ round($discount) }}%</p>
            @endif
        </div>

        {{-- Add to Cart --}}
        @if($showAddToCart)
            <div class="mt-auto px-0.5">
                @unless($outOfStock)
                    <button @click="$store.cart.add({{ $product->id }})"
                            class="w-full py-1.5 text-xs font-medium text-white bg-[#F8931D] hover:bg-[#E07E0A] rounded-full transition-colors shadow-sm">
                        Add to Cart
                    </button>
                @else
                    <button @click="$dispatch('notify-stock', { productId: {{ $product->id }} })"
                            class="w-full py-1.5 text-xs font-medium text-[#565959] bg-[#F0F2F2] rounded-full transition-colors">
                        Notify Me
                    </button>
                @endunless
            </div>
        @endif
    </div>
@else
    {{-- Full product card --}}
    <div {{ $attributes->merge(['class' => 'group card-product flex flex-col rounded-lg overflow-hidden']) }}>
        {{-- Image Section --}}
        <div class="relative aspect-square overflow-hidden">
            <a href="{{ route('product.show', $product) }}">
                <img src="{{ $product->primary_image_url }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-300"
                     loading="lazy"
                     onerror="this.src='{{ $placeholderImage }}'">
            </a>

            {{-- Top-left badges --}}
            @if($hasDiscount)
                <div class="absolute top-2 left-2">
                    <span class="bg-[#CC0C39] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-sm">{{ round($discount) }}% off</span>
                </div>
            @endif

            {{-- Top-right hover actions (Wishlist + Quick View) --}}
            @if($hasHoverActions)
                <div class="absolute top-2 right-2 flex flex-col gap-1.5 sm:opacity-0 sm:group-hover:opacity-100 focus-within:opacity-100 transition-opacity duration-200">
                    @if($showWishlist)
                        <button @click="$store.wishlist.toggle({{ $product->id }})"
                                class="w-9 h-9 bg-white rounded-full shadow-sm flex items-center justify-center transition-colors focus:outline-none"
                                :style="$store.wishlist.has({{ $product->id }}) ? 'color: #ef4444;' : 'color: #565959;'"
                                aria-label="Toggle wishlist">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    @endif
                    @if($showQuickViewBtn)
                        <button @click="$dispatch('quick-view', { productId: {{ $product->id }} })"
                                class="w-9 h-9 bg-white rounded-full shadow-sm flex items-center justify-center text-[#565959] hover:text-[#0F1111] transition-colors focus:outline-none"
                                aria-label="Quick view">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endif

            {{-- Out of stock overlay --}}
            @if($outOfStock)
                <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                    <span class="text-xs font-semibold text-[#B12704] bg-white px-3 py-1 rounded-full shadow-sm border border-[#E3E6E6]">Out of Stock</span>
                </div>
            @endif
        </div>

        {{-- Content Section --}}
        <div class="p-3 flex flex-col flex-1">
            {{-- Product Name --}}
            <h3 class="text-[13px] text-[#0F1111] mb-1.5 leading-snug min-h-9">
                <a href="{{ route('product.show', $product) }}" class="line-clamp-2 hover:text-[#C7511F] transition-colors">
                    {{ $product->name }}
                </a>
            </h3>

            {{-- Star Rating Row --}}
            <div class="flex items-center gap-1 mb-1.5">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($rating))
                            <svg class="w-3.5 h-3.5 text-[#205258]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.25)
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20">
                                <defs><linearGradient id="half-star-full-{{ $product->id }}"><stop offset="50%" stop-color="#205258"/><stop offset="50%" stop-color="#E0E0E0"/></linearGradient></defs>
                                <path fill="url(#half-star-full-{{ $product->id }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-[#E0E0E0]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                <a href="{{ route('product.show', $product) }}#reviews" class="text-[11px] text-[#007185] hover:text-[#C7511F] hover:underline">({{ $reviewCount }})</a>
            </div>

            {{-- Price Row --}}
            <div class="mb-1.5">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-[16px] font-medium text-[#0F1111]">@price($product->price)</span>
                    @if($hasDiscount)
                        <span class="text-[12px] text-[#565959] line-through">@price($product->mrp)</span>
                    @endif
                </div>
                @if($hasDiscount)
                    <span class="text-[11px] text-[#CC0C39] font-medium">({{ round($discount) }}% off)</span>
                @endif
            </div>

            {{-- Free Delivery badge --}}
            @if($product->price >= \App\Models\Setting::get('free_delivery_threshold', 499))
                <p class="text-[11px] text-[#0F1111] mb-1.5">
                    <span class="text-[#565959]">FREE Delivery by</span> <span class="font-medium">{{ config('app.name') }}</span>
                </p>
            @endif

            {{-- Add to Cart / Notify --}}
            @if($showAddToCart)
                <div class="mt-auto pt-2">
                    @unless($outOfStock)
                        <button @click="$store.cart.add({{ $product->id }})"
                                class="w-full py-1.5 text-xs font-medium text-white bg-[#F8931D] hover:bg-[#E07E0A] rounded-full transition-colors shadow-sm">
                            Add to Cart
                        </button>
                    @else
                        <button @click="$dispatch('notify-stock', { productId: {{ $product->id }} })"
                                class="w-full py-1.5 text-xs font-medium text-[#565959] bg-[#F0F2F2] rounded-full transition-colors">
                            Notify Me
                        </button>
                    @endunless
                </div>
            @endif
        </div>
    </div>
@endif
