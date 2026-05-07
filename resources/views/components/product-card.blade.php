@props(['product', 'showQuickView' => true, 'compact' => false])

@php
    $discount    = $product->discount_percentage ?? 0;
    $hasDiscount = $product->price < $product->mrp;
    $rating      = $product->rating ?? 0;
    $reviewCount = $product->review_count ?? 0;
    $outOfStock  = !$product->isInStock();
    $salesCount  = $product->sales_count ?? 0;
    $savedAmount = $hasDiscount ? ($product->mrp - $product->price) : 0;

    // Badge logic
    $isBestseller = $salesCount >= 50 || ($product->is_featured && $salesCount >= 10);
    $isTopRated   = $reviewCount >= 20 && $rating >= 4.5;
    $isLowStock   = !$outOfStock && $product->stock_quantity > 0 && $product->stock_quantity <= 5;
    $freeShipping = $product->price >= (float) \App\Models\Setting::get('free_shipping_threshold', 30);

    // Read hover action settings (cached for 1hr by Setting::get)
    $showWishlist = \App\Models\Setting::get('product_card_wishlist', true);
    $showAddToCart = \App\Models\Setting::get('product_card_add_to_cart', true);
    $showQuickViewBtn = false; // Disabled - using image preview overlay instead
    $hasHoverActions = $showWishlist;

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
    {{-- Compact card — Amazon-style + Glassmorphism --}}
    <div {{ $attributes->merge(['class' => 'group pc-glass-card flex flex-col cursor-pointer']) }}
         data-product-card
         onclick="window.location.href='{{ route('product.show', $product) }}'">

        {{-- ── IMAGE ZONE ─────────────────────────────── --}}
        <div class="relative w-full overflow-hidden flex-none" style="aspect-ratio:4/3; border-radius:12px 12px 0 0; background:#f7f4f1;">

            {{-- Product image --}}
            <img src="{{ $product->primary_image_url }}"
                 alt="{{ $product->name }}"
                 width="400" height="400"
                 class="w-full h-full object-cover transition-transform duration-500 {{ $secondaryImage ? 'group-hover:translate-x-full' : 'group-hover:scale-105' }}"
                 loading="lazy" decoding="async"
                 onerror="this.src='{{ $placeholderImage }}'">
            @if($secondaryImage)
                <img src="{{ $secondaryImage }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500"
                     loading="lazy" onerror="this.style.display='none'">
            @endif

            {{-- Top-left badges (stacked) --}}
            <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
                @if($isBestseller)
                    <span class="pc-badge" style="background:#c45500; color:#fff;">🏆 Bestseller</span>
                @elseif($isTopRated)
                    <span class="pc-badge" style="background:#007185; color:#fff;">⭐ Top Rated</span>
                @endif
                @if($hasDiscount)
                    <span class="pc-badge" style="background:#202a40; color:#fff;">-{{ round($discount) }}% OFF</span>
                @endif
            </div>

            {{-- Top-right action buttons --}}
            <div class="absolute top-2 right-2 flex flex-col gap-1.5 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity duration-200 z-10">
                @if($showWishlist)
                    <button @click.stop="$store.wishlist.toggle({{ $product->id }})"
                            class="pc-icon-btn"
                            :style="$store.wishlist.has({{ $product->id }}) ? 'color:#ef4444' : 'color:#aaa'"
                            aria-label="Wishlist">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                @endif
                <button @click.stop="$dispatch('open-preview', { id: {{ $product->id }}, image: {{ json_encode($product->primary_image_url) }}, name: {{ json_encode($product->name) }}, url: {{ json_encode(route('product.show', $product)) }}, price: '{{ $product->price }}', mrp: '{{ $product->mrp }}', rating: '{{ $rating }}', review_count: {{ $reviewCount }}, sales_count: {{ $salesCount }}, stock: {{ $product->stock_quantity ?? 0 }}, desc: {{ json_encode(Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 120)) }} })"
                        class="pc-icon-btn text-[#888] hover:text-[#202a40]" aria-label="Quick view">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>
            </div>

            {{-- Out of stock overlay --}}
            @if($outOfStock)
                <div class="absolute inset-0 flex items-center justify-center z-10" style="background:rgba(255,255,255,0.6); backdrop-filter:blur(2px);">
                    <span class="text-[10px] font-bold text-[#202a40] bg-white px-3 py-1 rounded-full shadow border border-[#e8e8e8]">Out of Stock</span>
                </div>
            @endif
        </div>

        {{-- ── BODY ───────────────────────────────────── --}}
        <div class="flex flex-col flex-1 px-3 pt-3 pb-3" style="min-width:0;">

            {{-- Product name --}}
            <a href="{{ route('product.show', $product) }}" onclick="event.stopPropagation()" class="block mb-1" style="min-width:0;overflow:hidden;">
                <h3 class="text-[13px] font-semibold text-[#1a1a1a] leading-snug hover:text-[#506282] transition-colors"
                    style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;"
                    title="{{ $product->name }}">
                    {{ $product->name }}
                </h3>
            </a>

            {{-- Rating row --}}
            @if($rating > 0)
            <div class="flex items-center gap-1 mb-1">
                <div class="flex items-center gap-px">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-3 h-3" fill="{{ $i <= round($rating) ? '#f5a623' : '#e0e0e0' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <span class="text-[11.5px] font-semibold" style="color:#007185;">{{ number_format($rating,1) }}</span>
                <span class="text-[11.5px] text-[#aaa]">({{ $reviewCount }})</span>
            </div>
            @endif

            {{-- Price block --}}
            <div class="flex flex-col gap-0.5 mt-auto">
                <div class="flex items-baseline gap-2 flex-wrap" style="min-width:0;">
                    <span class="text-[16px] font-bold text-[#1a1a1a] whitespace-nowrap">@price($product->price)</span>
                    @if($hasDiscount)
                        <span class="text-[12px] text-[#bbb] line-through whitespace-nowrap">@price($product->mrp)</span>
                    @endif
                </div>
                @if($hasDiscount && $savedAmount > 0)
                    <span class="text-[12px] font-semibold whitespace-nowrap" style="color:#2e9e5b;">
                        Save @price($savedAmount) &nbsp;<span class="text-[11px]">({{ round($discount) }}%)</span>
                    </span>
                @endif

                {{-- Delivery + social proof --}}
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    @if($freeShipping)
                        <span class="flex items-center gap-1 text-[11px] font-medium whitespace-nowrap" style="color:#007185;">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 10a2 2 0 002 2h8a2 2 0 002-2L19 8"/></svg>
                            Free Delivery
                        </span>
                    @endif
                    @if($isLowStock)
                        <span class="flex items-center gap-1 text-[11px] font-semibold whitespace-nowrap" style="color:#c0392b;">
                            <span class="w-2 h-2 rounded-full bg-[#c0392b] animate-pulse shrink-0 inline-block"></span>
                            Only {{ $product->stock_quantity }} left
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── ADD TO CART ─────────────────────────────── --}}
        @if($showAddToCart)
            @unless($outOfStock)
                <div style="padding:8px 10px 10px;">
                    <button @click.stop="$store.cart.add({{ $product->id }}); window.flyToCart?.($el)"
                        style="padding:10px 0; background:#202a40; color:#fff; width:100%; display:flex; align-items:center; justify-content:center; gap:6px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; border:none; cursor:pointer; transition:background 0.2s; border-radius:4px;"
                        onmouseover="this.style.background='#506282'" onmouseout="this.style.background='#202a40'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Add to Cart
                    </button>
                </div>
            @else
                <div style="padding:8px 10px 10px;">
                    <button @click.stop="$dispatch('notify-stock', { productId: {{ $product->id }} })"
                        style="padding:10px 0; background:#f0f0f0; color:#999; width:100%; font-size:12px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; border:none; cursor:pointer; border-radius:4px;">
                    Notify Me
                    </button>
                </div>
            @endunless
        @endif
    </div>
@else
    {{-- Full product card — Amazon-style + Glassmorphism --}}
    <div {{ $attributes->merge(['class' => 'group pc-glass-card flex flex-col cursor-pointer']) }} data-product-card
         onclick="window.location.href='{{ route('product.show', $product) }}'">

        {{-- ── IMAGE ──────────────────────────────────── --}}
        <div class="relative overflow-hidden flex-none" style="aspect-ratio:1/1; border-radius:12px 12px 0 0; background:#f7f4f1;">
            <img src="{{ $product->primary_image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover transition-transform duration-500 {{ $secondaryImage ? 'group-hover:translate-x-full' : 'group-hover:scale-105' }}"
                 loading="lazy"
                 onerror="this.src='{{ $placeholderImage }}'">
            @if($secondaryImage)
                <img src="{{ $secondaryImage }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500"
                     loading="lazy" onerror="this.style.display='none'">
            @endif

            {{-- Top-left badges --}}
            <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
                @if($isBestseller)
                    <span class="pc-badge" style="background:#c45500; color:#fff;">🏆 Bestseller</span>
                @elseif($isTopRated)
                    <span class="pc-badge" style="background:#007185; color:#fff;">⭐ Top Rated</span>
                @endif
                @if($hasDiscount)
                    <span class="pc-badge" style="background:#202a40; color:#fff;">-{{ round($discount) }}% OFF</span>
                @endif
            </div>

            {{-- Top-right actions --}}
            <div class="absolute top-2 right-2 flex flex-col gap-1.5 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity duration-200 z-10">
                @if($showWishlist)
                    <button @click.stop="$store.wishlist.toggle({{ $product->id }})"
                            class="pc-icon-btn"
                            :style="$store.wishlist.has({{ $product->id }}) ? 'color:#ef4444' : 'color:#aaa'"
                            aria-label="Wishlist">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                @endif
                <button @click.stop="$dispatch('open-preview', { id: {{ $product->id }}, image: {{ json_encode($product->primary_image_url) }}, name: {{ json_encode($product->name) }}, url: {{ json_encode(route('product.show', $product)) }}, price: '{{ $product->price }}', mrp: '{{ $product->mrp }}', rating: '{{ $rating }}', review_count: {{ $reviewCount }}, sales_count: {{ $salesCount }}, stock: {{ $product->stock_quantity ?? 0 }}, desc: {{ json_encode(Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 120)) }} })"
                        class="pc-icon-btn text-[#888] hover:text-[#202a40]" aria-label="Quick view">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>
            </div>

            {{-- Out of stock --}}
            @if($outOfStock)
                <div class="absolute inset-0 flex items-center justify-center z-10" style="background:rgba(255,255,255,0.6); backdrop-filter:blur(2px);">
                    <span class="text-[10px] font-bold text-[#202a40] bg-white px-3 py-1 rounded-full shadow border border-[#e8e8e8]">Out of Stock</span>
                </div>
            @endif
        </div>

        {{-- ── BODY ───────────────────────────────────── --}}
        <div class="flex flex-col flex-1 px-2.5 pt-3 pb-3" style="min-width:0;">

            {{-- Name --}}
            <a href="{{ route('product.show', $product) }}" onclick="event.stopPropagation()" class="block mb-1" style="min-width:0;overflow:hidden;">
                <h3 class="text-[11.5px] font-semibold text-[#1a1a1a] leading-snug hover:text-[#506282] transition-colors"
                    style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;"
                    title="{{ $product->name }}">
                    {{ $product->name }}
                </h3>
            </a>

            {{-- Rating --}}
            @if($rating > 0)
                <div class="flex items-center gap-1 mb-1">
                    <div class="flex items-center gap-px">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-2.5 h-2.5" fill="{{ $i <= round($rating) ? '#f5a623' : '#e0e0e0' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-[10px] font-semibold" style="color:#007185;">{{ number_format($rating,1) }}</span>
                    <span class="text-[10px] text-[#aaa]">({{ $reviewCount }})</span>
                </div>
            @endif

            {{-- Price block --}}
            <div class="flex flex-col gap-0 mt-auto">
                <div class="flex items-baseline gap-1.5 flex-wrap" style="min-width:0;">
                    <span class="text-[14px] font-bold text-[#1a1a1a] whitespace-nowrap">@price($product->price)</span>
                    @if($hasDiscount)
                        <span class="text-[10px] text-[#bbb] line-through whitespace-nowrap">@price($product->mrp)</span>
                    @endif
                </div>
                @if($hasDiscount && $savedAmount > 0)
                    <span class="text-[10px] font-semibold whitespace-nowrap" style="color:#2e9e5b;">
                        Save @price($savedAmount) &nbsp;<span class="text-[9.5px]">({{ round($discount) }}%)</span>
                    </span>
                @endif

                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    @if($freeShipping)
                        <span class="flex items-center gap-0.5 text-[9.5px] font-medium whitespace-nowrap" style="color:#007185;">
                            <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 10a2 2 0 002 2h8a2 2 0 002-2L19 8"/></svg>
                            Free Delivery
                        </span>
                    @endif
                    @if($isLowStock)
                        <span class="flex items-center gap-0.5 text-[9.5px] font-semibold whitespace-nowrap" style="color:#c0392b;">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#c0392b] animate-pulse shrink-0 inline-block"></span>
                            Only {{ $product->stock_quantity }} left
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── ADD TO CART ─────────────────────────────── --}}
        @if($showAddToCart)
            @unless($outOfStock)
                <div style="padding:8px 10px 10px;">
                    <button @click.stop="$store.cart.add({{ $product->id }}); window.flyToCart?.($el)"
                        style="padding:9px 0; background:#202a40; color:#fff; width:100%; display:flex; align-items:center; justify-content:center; gap:6px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; border:none; cursor:pointer; transition:background 0.2s; border-radius:4px;"
                        onmouseover="this.style.background='#506282'" onmouseout="this.style.background='#202a40'">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Add to Cart
                    </button>
                </div>
            @else
                <div style="padding:8px 10px 10px;">
                    <button @click.stop="$dispatch('notify-stock', { productId: {{ $product->id }} })"
                        style="padding:9px 0; background:#f0f0f0; color:#999; width:100%; font-size:10.5px; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; border:none; cursor:pointer; border-radius:4px;">
                    Notify Me
                    </button>
                </div>
            @endunless
        @endif
    </div>
@endif
