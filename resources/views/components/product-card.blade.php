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

    // Secondary image for hover swap
    $secondaryImage = null;
    if ($product->relationLoaded('images') && $product->images->count() > 1) {
        $secondaryImage = $product->images->skip(1)->first()?->image_url;
        if ($secondaryImage) {
            $secondaryImage = asset('storage/' . $secondaryImage);
        }
    }
@endphp

@if($compact)
    {{-- Compact card for horizontal scrollable rows --}}
    <div {{ $attributes->merge(['class' => 'group shrink-0 w-full flex flex-col h-full']) }}>
        <a href="{{ route('product.show', $product) }}" class="block relative">
            <div class="aspect-square rounded-lg overflow-hidden mb-2 bg-[#FAF7F5] relative">
                <img src="{{ $product->primary_image_url }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover rounded-lg transition-transform duration-500 {{ $secondaryImage ? 'group-hover:translate-x-full' : 'group-hover:scale-105' }}"
                     loading="lazy"
                     onerror="this.src='{{ $placeholderImage }}'">
                @if($secondaryImage)
                    <img src="{{ $secondaryImage }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover rounded-lg absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500"
                         loading="lazy"
                         onerror="this.style.display='none'">
                @endif
            </div>
            @if($hasDiscount)
                <span class="absolute top-2 left-2 bg-[#222222] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ round($discount) }}%</span>
            @endif
        </a>

        <a href="{{ route('product.show', $product) }}" class="block px-0.5">
            <h3 class="text-[13px] text-[#2b2b2b] line-clamp-2 mb-1 leading-snug hover:text-[#c29958] transition-colors" style="min-height: 2.5em;">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Bottom section pushed down --}}
        <div style="margin-top:auto;" class="px-0.5">
            {{-- Star Rating --}}
            <div class="flex items-center gap-1 mb-1">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($rating))
                            <svg class="w-3.5 h-3.5 text-[#C9A96E]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.25)
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20">
                                <defs><linearGradient id="half-star-compact-{{ $product->id }}"><stop offset="50%" stop-color="#C9A96E"/><stop offset="50%" stop-color="#E0E0E0"/></linearGradient></defs>
                                <path fill="url(#half-star-compact-{{ $product->id }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-[#E0E0E0]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endif
                    @endfor
                </div>
                <span class="text-[11px] text-[#747474]">({{ $reviewCount }})</span>
            </div>

            <div class="flex items-baseline gap-1.5 flex-wrap mb-1">
                <span class="text-[15px] font-semibold text-[#2b2b2b]">@price($product->price)</span>
                @if($hasDiscount)
                    <span class="text-[11px] text-[#747474] line-through">@price($product->mrp)</span>
                @endif
            </div>

            <div style="height:16px;" class="mb-1">
                @if($hasDiscount)
                    <p class="text-[11px] text-[#B76E79] font-medium">Save {{ round($discount) }}%</p>
                @endif
            </div>

        {{-- Add to Cart --}}
        @if($showAddToCart)
            <div>
                @unless($outOfStock)
                    <button @click="$store.cart.add({{ $product->id }})"
                            class="w-full py-2.5 sm:py-2 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#c29958] rounded-full transition-all shadow-sm hover:shadow-md tracking-wide uppercase min-h-11 sm:min-h-0"
                            style="letter-spacing: 0.05em;">
                        Add to Cart
                    </button>
                @else
                    <button @click="$dispatch('notify-stock', { productId: {{ $product->id }} })"
                            class="w-full py-2 text-xs font-medium text-[#747474] bg-[#f0f0f0] rounded-full transition-colors tracking-wide uppercase">
                        Notify Me
                    </button>
                @endunless
            </div>
        @endif
        </div>{{-- end mt-auto bottom section --}}
    </div>
@else
    {{-- Full product card --}}
    <div {{ $attributes->merge(['class' => 'group card-product flex flex-col rounded-lg overflow-hidden']) }}>
        {{-- Image Section --}}
        <div class="relative aspect-square overflow-hidden bg-[#FAF7F5]">
            <a href="{{ route('product.show', $product) }}">
                <img src="{{ $product->primary_image_url }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover rounded-lg transition-transform duration-500 {{ $secondaryImage ? 'group-hover:translate-x-full' : 'group-hover:scale-105' }}"
                     loading="lazy"
                     onerror="this.src='{{ $placeholderImage }}'">
                @if($secondaryImage)
                    <img src="{{ $secondaryImage }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover rounded-lg absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500"
                         loading="lazy"
                         onerror="this.style.display='none'">
                @endif
            </a>

            {{-- Top-left badges --}}
            @if($hasDiscount)
                <div class="absolute top-2 left-2">
                    <span class="bg-[#222222] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ round($discount) }}%</span>
                </div>
            @endif

            {{-- Top-right hover actions (Wishlist + Quick View) --}}
            @if($hasHoverActions)
                <div class="absolute top-2 right-2 flex flex-col gap-1.5 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 focus-within:opacity-100 transition-opacity duration-200">
                    @if($showWishlist)
                        <button @click="$store.wishlist.toggle({{ $product->id }})"
                                class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full shadow-sm flex items-center justify-center transition-colors focus:outline-none hover:bg-white"
                                :style="$store.wishlist.has({{ $product->id }}) ? 'color: #ef4444;' : 'color: #747474;'"
                                aria-label="Toggle wishlist">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    @endif
                    @if($showQuickViewBtn)
                        <button @click="$dispatch('quick-view', { productId: {{ $product->id }} })"
                                class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full shadow-sm flex items-center justify-center text-[#747474] hover:text-[#2b2b2b] hover:bg-white transition-colors focus:outline-none"
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
                    <span class="text-xs font-semibold text-[#B76E79] bg-white px-4 py-1.5 rounded-full shadow-sm border border-[#efefef]">Out of Stock</span>
                </div>
            @endif
        </div>

        {{-- Content Section --}}
        <div class="p-3 flex flex-col flex-1">
            {{-- Product Name --}}
            <h3 class="text-[13px] text-[#2b2b2b] mb-1.5 leading-snug min-h-9">
                <a href="{{ route('product.show', $product) }}" class="line-clamp-2 hover:text-[#c29958] transition-colors">
                    {{ $product->name }}
                </a>
            </h3>

            {{-- Star Rating Row --}}
            <div class="flex items-center gap-1 mb-1.5">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($rating))
                            <svg class="w-3.5 h-3.5 text-[#C9A96E]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.25)
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20">
                                <defs><linearGradient id="half-star-full-{{ $product->id }}"><stop offset="50%" stop-color="#C9A96E"/><stop offset="50%" stop-color="#E0E0E0"/></linearGradient></defs>
                                <path fill="url(#half-star-full-{{ $product->id }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-[#E0E0E0]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                <a href="{{ route('product.show', $product) }}#reviews" class="text-[11px] text-[#747474] hover:text-[#c29958] hover:underline">({{ $reviewCount }})</a>
            </div>

            {{-- Price Row --}}
            <div class="mb-1.5">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-[16px] font-semibold text-[#2b2b2b]">@price($product->price)</span>
                    @if($hasDiscount)
                        <span class="text-[12px] text-[#747474] line-through">@price($product->mrp)</span>
                    @endif
                </div>
                @if($hasDiscount)
                    <span class="text-[11px] text-[#B76E79] font-medium">({{ round($discount) }}% off)</span>
                @endif
            </div>

            {{-- Free Delivery badge --}}
            @if($product->price >= \App\Models\Setting::get('free_delivery_threshold', 499))
                <p class="text-[11px] text-[#2b2b2b] mb-1.5">
                    <span class="text-[#747474]">FREE Delivery by</span> <span class="font-medium">{{ config('app.name') }}</span>
                </p>
            @endif

            {{-- Scarcity badge --}}
            @if(!$outOfStock && $product->stock_quantity > 0 && $product->stock_quantity <= 10)
                <p class="text-[11px] text-[#B76E79] font-medium mb-1">
                    Only {{ $product->stock_quantity }} left in stock - order soon
                </p>
            @endif

            {{-- Add to Cart / Notify --}}
            @if($showAddToCart)
                <div class="mt-auto pt-2">
                    @unless($outOfStock)
                        <button @click="$store.cart.add({{ $product->id }})"
                                class="w-full py-2.5 sm:py-2 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#c29958] rounded-full transition-all shadow-sm hover:shadow-md tracking-wide uppercase min-h-11 sm:min-h-0"
                                style="letter-spacing: 0.05em;">
                            Add to Cart
                        </button>
                    @else
                        <button @click="$dispatch('notify-stock', { productId: {{ $product->id }} })"
                                class="w-full py-2.5 sm:py-2 text-xs font-medium text-[#747474] bg-[#f0f0f0] rounded-full transition-colors tracking-wide uppercase min-h-11 sm:min-h-0">
                            Notify Me
                        </button>
                    @endunless
                </div>
            @endif
        </div>
    </div>
@endif
