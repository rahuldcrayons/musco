<x-layouts.app>
    <x-slot name="title">{{ $product->name }} - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
        <link rel="canonical" href="{{ route('products.show', $product->slug) }}">
        <meta property="og:title" content="{{ $product->name }}">
        <meta property="og:description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
        <meta property="og:image" content="{{ $product->primary_image_url }}">
        <meta property="og:type" content="product">
        <meta property="og:url" content="{{ route('products.show', $product->slug) }}">
        <meta property="product:price:amount" content="{{ $product->current_price }}">
        <meta property="product:price:currency" content="INR">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $product->name }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
        <meta name="twitter:image" content="{{ $product->primary_image_url }}">

        {{-- JSON-LD Structured Data --}}
        <x-product-schema :productSchema="$productSchema ?? null" :faqSchema="$faqSchema ?? null" />
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-[#E3E6E6]">
        <div class="container mx-auto px-4 py-2">
            <x-breadcrumb :items="$breadcrumbs" />
        </div>
    </div>

    <div class="container mx-auto px-4 py-4 lg:py-6">
        <!-- Product Main Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6">
            <!-- Image Gallery — Col 1 -->
            <div x-data="{
                    images: @js($product->images->pluck('url')->values()),
                    activeIndex: 0,
                    touchStartX: 0,
                    touchEndX: 0,
                    showZoom: false,
                    get activeImage() { return this.images[this.activeIndex] || '{{ $product->primary_image_url }}'; },
                    select(index) {
                        if (index !== this.activeIndex) {
                            this.activeIndex = index;
                        }
                    },
                    next() { this.activeIndex = (this.activeIndex + 1) % this.images.length; },
                    prev() { this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length; },
                    handleSwipe() {
                        const diff = this.touchStartX - this.touchEndX;
                        if (Math.abs(diff) > 50) {
                            diff > 0 ? this.next() : this.prev();
                        }
                    }
                 }"
                 class="lg:col-span-5 space-y-3">

                <div class="flex gap-3">
                    <!-- Thumbnails (Desktop vertical) -->
                    @if($product->images->count() > 1)
                        <div class="hidden lg:flex flex-col gap-2 w-16 shrink-0">
                            @foreach($product->images as $index => $image)
                                <button @click="select({{ $index }})"
                                        class="w-16 h-16 rounded border-2 overflow-hidden shrink-0 transition-all duration-200 cursor-pointer"
                                        :class="activeIndex === {{ $index }}
                                            ? 'border-[#C7511F] shadow-sm'
                                            : 'border-[#E3E6E6] hover:border-[#C7511F]'">
                                    <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Main Image -->
                    <div class="relative bg-white rounded-lg overflow-hidden border border-[#E3E6E6] group flex-1"
                         @touchstart="touchStartX = $event.changedTouches[0].screenX"
                         @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()">
                        <div x-show="images.length > 0"
                             class="relative overflow-hidden cursor-zoom-in aspect-square"
                             x-data="{ zooming: false, zoomX: 50, zoomY: 50 }"
                             @mouseenter="zooming = true"
                             @mouseleave="zooming = false"
                             @mousemove="let r = $el.getBoundingClientRect(); zoomX = ((($event.clientX - r.left) / r.width) * 100); zoomY = ((($event.clientY - r.top) / r.height) * 100)"
                             @click="showZoom = true">
                            <img :src="activeImage"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-contain p-4 transition-transform duration-200"
                                 :style="zooming ? 'transform: scale(2); transform-origin: ' + zoomX + '% ' + zoomY + '%' : ''">
                        </div>

                        <div x-show="images.length === 0" class="flex items-center justify-center py-20">
                            <svg class="w-20 h-20 text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <!-- Image counter -->
                        <template x-if="images.length > 1">
                            <div class="absolute bottom-3 left-3 bg-white/90 text-[#0F1111] text-xs font-medium px-2.5 py-1 rounded shadow-sm border border-[#E3E6E6]" x-text="(activeIndex + 1) + ' / ' + images.length"></div>
                        </template>

                        <!-- Nav Arrows -->
                        <template x-if="images.length > 1">
                            <div>
                                <button @click="prev()" aria-label="Previous image"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white hover:bg-[#F7FAFA] rounded-full flex items-center justify-center shadow  opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4 text-[#0F1111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button @click="next()" aria-label="Next image"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white hover:bg-[#F7FAFA] rounded-full flex items-center justify-center shadow  opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4 text-[#0F1111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Mobile Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="flex lg:hidden gap-2 overflow-x-auto pb-1 scrollbar-thin">
                        @foreach($product->images as $index => $image)
                            <button @click="select({{ $index }})"
                                    class="w-14 h-14 rounded border-2 overflow-hidden shrink-0"
                                    :class="activeIndex === {{ $index }} ? 'border-[#C7511F]' : 'border-[#E3E6E6]'">
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Lightbox Modal -->
                <div x-show="showZoom" x-cloak
                     @keydown.escape.window="showZoom = false"
                     @keydown.left.window="if(showZoom) prev()"
                     @keydown.right.window="if(showZoom) next()"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
                     @touchstart="touchStartX = $event.changedTouches[0].screenX"
                     @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()">
                    <button @click="showZoom = false" class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors z-10" aria-label="Close zoom">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <template x-if="images.length > 1">
                        <div>
                            <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white" aria-label="Previous"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                            <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white" aria-label="Next"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                    </template>
                    <img :src="activeImage" alt="{{ $product->name }}" class="max-w-[90vw] max-h-[85vh] object-contain" @click.stop>
                    <template x-if="images.length > 1">
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/70 text-sm" x-text="(activeIndex + 1) + ' / ' + images.length"></div>
                    </template>
                </div>
            </div>

            <!-- Product Info — Col 2 (Middle) -->
            <div class="lg:col-span-4 space-y-3">
                <!-- Brand -->
                @if($product->brand)
                    <a href="{{ route('brands.show', $product->brand) }}" class="text-sm text-[#007185] hover:text-[#C7511F] hover:underline">
                        Visit the {{ $product->brand->name }} Store
                    </a>
                @endif

                <!-- Title -->
                <h1 class="text-lg lg:text-xl font-normal text-[#0F1111] leading-snug">{{ $product->name }}</h1>

                <!-- Rating Row -->
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span class="text-sm text-[#007185]">{{ number_format($product->rating, 1) }}</span>
                    <a href="#customer-reviews" class="inline-flex items-center gap-0.5 group">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-[16px] h-[16px] {{ $i <= round($product->rating) ? 'text-[#FFA41C]' : 'text-[#E0E0E0]' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </a>
                    <a href="#customer-reviews" class="text-sm text-[#007185] hover:text-[#C7511F] hover:underline">{{ number_format($product->review_count) }} ratings</a>
                </div>

                <hr class="border-[#E3E6E6]">

                <!-- Price Block -->
                <div class="space-y-1">
                    @if($product->mrp > $product->price)
                        <div class="inline-flex items-center gap-1.5 bg-[#CC0C39] text-white text-xs font-bold px-2.5 py-1 rounded-sm">
                            Limited Time Deal
                        </div>
                    @endif
                    <div class="flex items-baseline gap-2">
                        @if($product->mrp > $product->price)
                            <span class="text-[#CC0C39] text-xl font-normal">-{{ round($product->discount_percentage) }}%</span>
                        @endif
                        <span class="text-[28px] font-medium text-[#0F1111]">@price($product->price)</span>
                    </div>
                    @if($product->mrp > $product->price)
                        <div class="text-sm text-[#565959]">
                            M.R.P.: <span class="line-through">@price($product->mrp)</span>
                        </div>
                    @endif
                    <p class="text-xs text-[#565959]">Inclusive of all taxes</p>
                </div>

                <!-- Offers Section -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#CC0C39]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm2 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/><path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/></svg>
                        <span class="text-sm font-bold text-[#0F1111]">Offers</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div class="border border-[#E3E6E6] rounded-lg p-3">
                            <p class="text-xs font-bold text-[#0F1111] mb-0.5">Cashback</p>
                            <p class="text-[11px] text-[#565959]">Upto ₹100 cashback on UPI payments</p>
                            <a href="#" class="text-[11px] text-[#007185] hover:text-[#C7511F] hover:underline">1 offer ›</a>
                        </div>
                        <div class="border border-[#E3E6E6] rounded-lg p-3">
                            <p class="text-xs font-bold text-[#0F1111] mb-0.5">Bank Offer</p>
                            <p class="text-[11px] text-[#565959]">10% off on HDFC, ICICI Cards</p>
                            <a href="#" class="text-[11px] text-[#007185] hover:text-[#C7511F] hover:underline">5 offers ›</a>
                        </div>
                        <div class="border border-[#E3E6E6] rounded-lg p-3">
                            <p class="text-xs font-bold text-[#0F1111] mb-0.5">No Cost EMI</p>
                            <p class="text-[11px] text-[#565959]">EMI from ₹{{ number_format(ceil($product->price / 6)) }}/mo</p>
                            <a href="#" class="text-[11px] text-[#007185] hover:text-[#C7511F] hover:underline">View Plans ›</a>
                        </div>
                    </div>
                </div>

                <!-- Trust Badges (Free Delivery, Pay on Delivery, etc.) -->
                <div class="grid grid-cols-4 gap-2 py-3">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-11 h-11 bg-[#F0F8F8] rounded-full flex items-center justify-center mb-1.5">
                            <svg class="w-5 h-5 text-[#205258]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <p class="text-[10px] font-medium text-[#0F1111] leading-tight">Free Delivery</p>
                        <p class="text-[9px] text-[#565959]">Above ₹499</p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-11 h-11 bg-[#F0F8F8] rounded-full flex items-center justify-center mb-1.5">
                            <svg class="w-5 h-5 text-[#205258]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <p class="text-[10px] font-medium text-[#0F1111] leading-tight">Pay on Delivery</p>
                        <p class="text-[9px] text-[#565959]">Cash, UPI & Cards</p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-11 h-11 bg-[#F0F8F8] rounded-full flex items-center justify-center mb-1.5">
                            <svg class="w-5 h-5 text-[#205258]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </div>
                        <p class="text-[10px] font-medium text-[#0F1111] leading-tight">7 Days Return</p>
                        <p class="text-[9px] text-[#565959]">Easy returns</p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-11 h-11 bg-[#F0F8F8] rounded-full flex items-center justify-center mb-1.5">
                            <svg class="w-5 h-5 text-[#205258]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-[10px] font-medium text-[#0F1111] leading-tight">Secure Payment</p>
                        <p class="text-[9px] text-[#565959]">100% secure</p>
                    </div>
                </div>

                <!-- About this item -->
                @if($product->short_description)
                    <div>
                        <h3 class="text-sm font-bold text-[#0F1111] mb-2">About this item</h3>
                        <ul class="space-y-1.5 text-sm text-[#333] list-disc pl-4">
                            @foreach(preg_split('/[\.\n]+/', $product->short_description, -1, PREG_SPLIT_NO_EMPTY) as $point)
                                @if(strlen(trim($point)) > 3)
                                    <li class="leading-relaxed">{{ trim($point) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Key Attributes -->
                @if(is_array($product->attributes) && count($product->attributes))
                    <div class="border-t border-[#E3E6E6] pt-3">
                        @foreach($product->attributes as $attrName => $attrValue)
                            <div class="flex items-center gap-3 text-sm py-1">
                                <span class="text-[#565959] w-28 shrink-0 font-medium">{{ $attrName }}</span>
                                <span class="text-[#0F1111]">{{ $attrValue }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Buy Box — Col 3 (Right sidebar) -->
            <div class="lg:col-span-3" x-data="quantitySelector()">
                <div class=" rounded-lg p-4 space-y-3 lg:sticky lg:top-4">
                    <!-- Price (repeated in buy box) -->
                    <div class="text-[24px] font-medium text-[#0F1111]">@price($product->price)</div>

                    @if($product->price >= 499)
                        <div class="text-sm">
                            <span class="text-[#007600] font-medium">FREE delivery</span>
                            <span class="text-[#0F1111] font-medium">{{ now()->addDays(3)->format('D, d M') }}</span>
                        </div>
                        <div class="text-xs text-[#565959]">Or fastest delivery <span class="font-medium text-[#0F1111]">Tomorrow</span></div>
                    @endif

                    <!-- Stock Status -->
                    @if($product->stock_quantity > 0)
                        <p class="text-lg font-medium text-[#007600]">In stock</p>
                    @else
                        <p class="text-lg font-medium text-[#B12704]">Currently unavailable</p>
                    @endif

                    <!-- Quantity -->
                    @if($product->stock_quantity > 0)
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-[#0F1111]">Qty:</label>
                        <select x-model="quantity" class=" rounded-lg bg-[#F0F2F2] text-sm py-1.5 px-3 shadow-sm focus:ring-[#007185] focus:border-[#007185]">
                            @for($i = 1; $i <= min($product->stock_quantity, 10); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    @endif

                    <!-- CTA Buttons -->
                    <div class="flex flex-col gap-2" data-sticky-trigger>
                        @if($product->stock_quantity > 0)
                            <button x-data="{ adding: false, added: false }"
                                    @click="if(adding) return; adding = true; await $store.cart.add({{ $product->id }}, quantity); adding = false; added = true; setTimeout(() => added = false, 2000)"
                                    :disabled="adding"
                                    class="w-full flex items-center justify-center gap-2 bg-[#F8931D] hover:bg-[#E07E0A] text-white font-medium py-2 px-4 rounded-full text-sm transition-colors  disabled:opacity-70 shadow-sm">
                                <span x-text="adding ? 'Adding...' : (added ? 'Added to Cart' : 'Add to Cart')"></span>
                            </button>
                            <button @click="$store.cart.add({{ $product->id }}, quantity); setTimeout(() => window.location.href = '{{ route('checkout.index') }}', 300)"
                                    class="w-full flex items-center justify-center gap-2 bg-[#FFD814] hover:bg-[#F7CA00] text-[#0F1111] font-medium py-2 px-4 rounded-full text-sm transition-colors  shadow-sm">
                                Buy Now
                            </button>
                        @else
                            <button @click="$dispatch('notify-stock', { productId: {{ $product->id }}, productName: '{{ addslashes($product->name) }}' })"
                                    class="w-full flex items-center justify-center gap-2 bg-[#F8931D] hover:bg-[#E07E0A] text-white font-medium py-2 px-4 rounded-full text-sm transition-colors ">
                                Notify Me When Available
                            </button>
                        @endif
                    </div>

                    <!-- Secure Transaction -->
                    <div class="flex items-center gap-1.5 text-xs text-[#565959]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Secure transaction
                    </div>

                    <!-- Sold by -->
                    <div class="text-xs text-[#565959] space-y-0.5">
                        <div>Ships from <span class="text-[#0F1111] font-medium">{{ config('app.name') }}</span></div>
                        <div>Sold by <span class="text-[#007185]">{{ $product->seller?->store_name ?? config('app.name') }}</span></div>
                    </div>

                    <hr class="border-[#E3E6E6]">

                    <!-- Wishlist -->
                    <button @click="$store.wishlist.toggle({{ $product->id }})"
                            class="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium rounded-full border transition-colors"
                            :class="$store.wishlist.has({{ $product->id }}) ? 'text-red-500 border-red-200 bg-red-50' : 'text-[#0F1111] border-[#D5D9D9] hover:bg-[#F7FAFA]'"
                            aria-label="Toggle wishlist">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span x-text="$store.wishlist.has({{ $product->id }}) ? 'Added to Wishlist' : 'Add to Wishlist'"></span>
                    </button>

                    <!-- Share -->
                    <div x-data="{ copied: false }" class="flex items-center justify-center gap-3 pt-1">
                        <svg class="w-4 h-4 text-[#565959]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        <span class="text-xs text-[#565959]">Share</span>
                        <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . route('product.show', $product)) }}" target="_blank" rel="noopener" class="text-[#565959] hover:text-[#25D366]" aria-label="WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)" class="text-[#565959] hover:text-[#0F1111]" aria-label="Copy link">
                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            <svg x-show="copied" x-cloak class="w-4 h-4 text-[#007600]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description (A+ Content — inline, no tabs) -->
        @if($product->description)
            <section class="mt-8 border-t border-[#E3E6E6] pt-6">
                <h2 class="text-lg font-bold text-[#0F1111] mb-4">Product Description</h2>
                <div class="prose prose-neutral max-w-none text-sm text-[#333] leading-relaxed">
                    {!! $product->description !!}
                </div>
            </section>
        @endif

        <!-- Specifications -->
        @if($product->sku || $product->weight || $product->dimensions || $product->brand || ($product->attributes && count($product->attributes)))
            <section class="mt-6 border-t border-[#E3E6E6] pt-6">
                <h2 class="text-lg font-bold text-[#0F1111] mb-4">Product Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                    @if($product->sku)
                        <div class="flex border-b border-[#E3E6E6] py-2.5">
                            <span class="w-1/3 text-sm text-[#565959] font-medium">SKU</span>
                            <span class="w-2/3 text-sm text-[#0F1111]">{{ $product->sku }}</span>
                        </div>
                    @endif
                    @if($product->brand)
                        <div class="flex border-b border-[#E3E6E6] py-2.5">
                            <span class="w-1/3 text-sm text-[#565959] font-medium">Brand</span>
                            <span class="w-2/3 text-sm text-[#0F1111]">{{ $product->brand->name }}</span>
                        </div>
                    @endif
                    @if($product->weight)
                        <div class="flex border-b border-[#E3E6E6] py-2.5">
                            <span class="w-1/3 text-sm text-[#565959] font-medium">Weight</span>
                            <span class="w-2/3 text-sm text-[#0F1111]">{{ $product->weight }} kg</span>
                        </div>
                    @endif
                    @if($product->dimensions)
                        <div class="flex border-b border-[#E3E6E6] py-2.5">
                            <span class="w-1/3 text-sm text-[#565959] font-medium">Dimensions</span>
                            <span class="w-2/3 text-sm text-[#0F1111]">{{ $product->dimensions }}</span>
                        </div>
                    @endif
                    @if($product->category)
                        <div class="flex border-b border-[#E3E6E6] py-2.5">
                            <span class="w-1/3 text-sm text-[#565959] font-medium">Category</span>
                            <span class="w-2/3 text-sm text-[#0F1111]">{{ $product->category->name }}</span>
                        </div>
                    @endif
                    @if(is_array($product->specifications) && count($product->specifications))
                        @foreach($product->specifications as $specName => $specValue)
                            <div class="flex border-b border-[#E3E6E6] py-2.5">
                                <span class="w-1/3 text-sm text-[#565959] font-medium">{{ $specName }}</span>
                                <span class="w-2/3 text-sm text-[#0F1111]">{{ $specValue }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>
        @endif

        <!-- Compare with similar items -->
        @if(isset($compareProducts) && $compareProducts->count())
            <section class="mt-8 border-t border-[#E3E6E6] pt-6">
                <h2 class="text-lg font-bold text-[#0F1111] mb-4">Compare with similar items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse min-w-[600px]">
                        <thead>
                            <tr>
                                <td class="p-3 w-32"></td>
                                <td class="p-3 text-center border-l border-[#E3E6E6]">
                                    <a href="{{ route('product.show', $product) }}" class="block">
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-24 h-24 object-contain mx-auto mb-2">
                                        <p class="text-xs text-[#007185] line-clamp-3 hover:text-[#C7511F]">{{ Str::limit($product->name, 80) }}</p>
                                    </a>
                                </td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#E3E6E6]">
                                        <a href="{{ route('product.show', $cp) }}" class="block">
                                            <img src="{{ $cp->primary_image_url }}" alt="{{ $cp->name }}" class="w-24 h-24 object-contain mx-auto mb-2">
                                            <p class="text-xs text-[#007185] line-clamp-3 hover:text-[#C7511F]">{{ Str::limit($cp->name, 80) }}</p>
                                        </a>
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-[#E3E6E6]">
                                <td class="p-3 text-sm font-medium text-[#0F1111]">Customer Rating</td>
                                <td class="p-3 text-center border-l border-[#E3E6E6]">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="text-sm">{{ number_format($product->rating, 1) }}</span>
                                        <svg class="w-4 h-4 text-[#FFA41C]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </div>
                                    <p class="text-xs text-[#007185]">{{ $product->review_count }}</p>
                                </td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#E3E6E6]">
                                        <div class="flex items-center justify-center gap-1">
                                            <span class="text-sm">{{ number_format($cp->rating ?? 0, 1) }}</span>
                                            <svg class="w-4 h-4 text-[#FFA41C]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </div>
                                        <p class="text-xs text-[#007185]">{{ $cp->review_count ?? 0 }}</p>
                                    </td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#E3E6E6]">
                                <td class="p-3 text-sm font-medium text-[#0F1111]">Price</td>
                                <td class="p-3 text-center border-l border-[#E3E6E6] text-sm font-medium text-[#0F1111]">@price($product->price)</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#E3E6E6] text-sm font-medium text-[#0F1111]">@price($cp->price)</td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#E3E6E6]">
                                <td class="p-3 text-sm font-medium text-[#0F1111]">Brand</td>
                                <td class="p-3 text-center border-l border-[#E3E6E6] text-sm text-[#0F1111]">{{ $product->brand?->name ?? '-' }}</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#E3E6E6] text-sm text-[#0F1111]">{{ $cp->brand?->name ?? '-' }}</td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#E3E6E6]">
                                <td class="p-3 text-sm font-medium text-[#0F1111]">Availability</td>
                                <td class="p-3 text-center border-l border-[#E3E6E6] text-sm {{ $product->stock_quantity > 0 ? 'text-[#007600]' : 'text-[#B12704]' }}">{{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#E3E6E6] text-sm {{ $cp->stock_quantity > 0 ? 'text-[#007600]' : 'text-[#B12704]' }}">{{ $cp->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}</td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#E3E6E6] bg-[#F7F8FA]">
                                <td class="p-3"></td>
                                <td class="p-3 text-center border-l border-[#E3E6E6] text-sm font-medium text-[#0F1111] italic">This product</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#E3E6E6]">
                                        <a href="{{ route('product.show', $cp) }}" class="text-sm text-[#007185] hover:text-[#C7511F] hover:underline">View details →</a>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Customer Reviews Section -->
        <section class="mt-8 border-t border-[#E3E6E6] pt-6" id="customer-reviews">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Rating Summary -->
                <div class="lg:col-span-4">
                    <h2 class="text-lg font-bold text-[#0F1111] mb-3">Customer Reviews</h2>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($product->rating) ? 'text-[#FFA41C]' : 'text-[#E0E0E0]' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-[#0F1111]">{{ number_format($product->rating, 1) }} out of 5</span>
                    </div>
                    <p class="text-sm text-[#565959] mb-4">{{ number_format($product->review_count) }} global ratings</p>

                    <!-- Rating Bars -->
                    @php
                        $totalReviews = max($product->review_count, 1);
                        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                        foreach($product->reviews as $r) {
                            if(isset($ratingCounts[$r->rating])) $ratingCounts[$r->rating]++;
                        }
                    @endphp
                    <div class="space-y-1.5">
                        @for($star = 5; $star >= 1; $star--)
                            @php $pct = $totalReviews > 0 ? round(($ratingCounts[$star] / $totalReviews) * 100) : 0; @endphp
                            <div class="flex items-center gap-2">
                                <a href="#" class="text-sm text-[#007185] hover:underline whitespace-nowrap w-14">{{ $star }} star</a>
                                <div class="flex-1 h-5 bg-[#F0F2F2] rounded-sm overflow-hidden">
                                    <div class="h-full bg-[#FFA41C] rounded-sm" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-sm text-[#007185] w-10 text-right">{{ $pct }}%</span>
                            </div>
                        @endfor
                    </div>

                    <hr class="border-[#E3E6E6] my-4">

                    <!-- Write a Review CTA -->
                    <h3 class="text-base font-bold text-[#0F1111] mb-1">Review this product</h3>
                    <p class="text-sm text-[#565959] mb-3">Share your thoughts with other customers</p>
                    @auth
                        <a href="{{ route('account.reviews.create', $product) }}"
                           class="block w-full text-center py-1.5 text-sm font-medium text-[#0F1111] bg-white rounded-full hover:bg-[#F7FAFA] shadow-sm transition-colors">
                            Write a customer review
                        </a>
                    @else
                        <div x-data="{ showForm: false }">
                            <button @click="showForm = !showForm"
                                    class="w-full text-center py-1.5 text-sm font-medium text-[#0F1111] bg-white rounded-full hover:bg-[#F7FAFA] shadow-sm transition-colors">
                                Write a customer review
                            </button>

                            <form x-show="showForm" x-cloak method="POST" action="{{ route('product.guest-review', $product) }}" class="mt-4 space-y-3 bg-[#F7F8FA] rounded-lg p-4 border border-[#E3E6E6]">
                                @csrf
                                <input type="text" name="honeypot" class="hidden" value="" tabindex="-1" autocomplete="off">

                                <div>
                                    <label class="block text-sm font-medium text-[#0F1111] mb-1">Your Name</label>
                                    <input type="text" name="guest_name" required class="w-full  rounded-lg px-3 py-2 text-sm focus:ring-[#007185] focus:border-[#007185]" placeholder="e.g. Priya S.">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-[#0F1111] mb-1">Email Address</label>
                                    <input type="email" name="guest_email" required class="w-full  rounded-lg px-3 py-2 text-sm focus:ring-[#007185] focus:border-[#007185]" placeholder="your@email.com">
                                    <p class="text-[11px] text-[#565959] mt-1">We will send you a discount code as a thank you!</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-[#0F1111] mb-1">Rating</label>
                                    <div x-data="{ rating: 0, hover: 0 }" class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" @click="rating = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0">
                                                <svg class="w-7 h-7 cursor-pointer transition-colors" :class="(hover || rating) >= {{ $i }} ? 'text-[#FFA41C]' : 'text-[#E0E0E0]'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" :value="rating">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-[#0F1111] mb-1">Title <span class="text-[#565959]">(optional)</span></label>
                                    <input type="text" name="title" class="w-full  rounded-lg px-3 py-2 text-sm focus:ring-[#007185] focus:border-[#007185]" placeholder="Sum up your experience">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-[#0F1111] mb-1">Your Review</label>
                                    <textarea name="content" required rows="4" minlength="20" class="w-full  rounded-lg px-3 py-2 text-sm focus:ring-[#007185] focus:border-[#007185]" placeholder="What did you like or dislike?"></textarea>
                                </div>

                                <button type="submit" class="bg-[#F8931D] hover:bg-[#E07E0A] text-white font-medium py-2 px-6 rounded-full text-sm  shadow-sm transition-colors">
                                    Submit Review
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>

                <!-- Right: Reviews List -->
                <div class="lg:col-span-8">
                    <h3 class="text-base font-bold text-[#0F1111] mb-4">Top Reviews</h3>

                    @if($product->reviews->count())
                        <div class="space-y-6">
                            @foreach($product->reviews as $review)
                                <div class="border-b border-[#E3E6E6] pb-5">
                                    <!-- Reviewer -->
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <div class="w-8 h-8 bg-[#F0F2F2] rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-[#565959]">{{ $review->reviewer_initial }}</span>
                                        </div>
                                        <span class="text-sm text-[#0F1111]">{{ $review->reviewer_name }}</span>
                                    </div>

                                    <!-- Stars + Title -->
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-[#FFA41C]' : 'text-[#E0E0E0]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @if($review->title)
                                            <span class="text-sm font-bold text-[#0F1111]">{{ $review->title }}</span>
                                        @endif
                                    </div>

                                    <!-- Date + Verified -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs text-[#565959]">Reviewed on {{ $review->created_at->format('j F Y') }}</span>
                                        @if($review->is_verified_purchase)
                                            <span class="text-xs font-bold text-[#C45500]">Verified Purchase</span>
                                        @else
                                            <span class="text-xs text-[#565959]">Unverified</span>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <p class="text-sm text-[#0F1111] leading-relaxed">{{ $review->content }}</p>

                                    <!-- Pros/Cons -->
                                    @if($review->pros && count($review->pros))
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach($review->pros as $pro)
                                                <span class="inline-flex items-center gap-1 text-xs bg-[#F0FFF4] text-[#007600] px-2 py-0.5 rounded-full border border-[#C6F6D5]">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    {{ $pro }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($review->cons && count($review->cons))
                                        <div class="mt-1 flex flex-wrap gap-1.5">
                                            @foreach($review->cons as $con)
                                                <span class="inline-flex items-center gap-1 text-xs bg-[#FFF5F5] text-[#B12704] px-2 py-0.5 rounded-full border border-[#FED7D7]">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    {{ $con }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Helpful -->
                                    <div class="mt-3 flex items-center gap-3">
                                        <span class="text-xs text-[#565959]">{{ $review->helpful_count ?? 0 }} {{ ($review->helpful_count ?? 0) == 1 ? 'person' : 'people' }} found this helpful</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-[#F7F8FA] rounded-lg border border-[#E3E6E6]">
                            <p class="text-sm text-[#565959] mb-2">No reviews yet.</p>
                            <p class="text-sm text-[#0F1111]">Be the first to review this product!</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Related Products -->
        @if($relatedProducts->count())
            <section class="mt-8 border-t border-[#E3E6E6] pt-6">
                <h2 class="text-lg font-bold text-[#0F1111] mb-4">Products related to this item</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Sticky Mobile Add-to-Cart Bar --}}
    @if($product->stock_quantity > 0)
        <div x-data="{ showBar: false }"
             x-init="
                const target = document.querySelector('[data-sticky-trigger]');
                if (target) {
                    const obs = new IntersectionObserver(([e]) => { showBar = !e.isIntersecting; }, { threshold: 0 });
                    obs.observe(target);
                }
             "
             x-show="showBar"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             x-cloak
             class="fixed bottom-16 lg:hidden left-0 right-0 z-40 bg-white border-t border-[#D5D9D9] shadow-lg px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-base font-medium text-[#0F1111]">@price($product->price)</span>
                        @if($product->mrp > $product->price)
                            <span class="text-[11px] text-[#565959] line-through">@price($product->mrp)</span>
                        @endif
                    </div>
                </div>
                <button @click="$store.cart.add({{ $product->id }})"
                        class="shrink-0 bg-[#F8931D] hover:bg-[#E07E0A] text-white font-medium py-2 px-4 rounded-full text-sm transition-colors shadow-sm">
                    Add to Cart
                </button>
            </div>
        </div>
    @endif

    {{-- GA4 view_item + FB ViewContent tracking --}}
    @if(config('services.ga4.measurement_id') || config('services.facebook.pixel_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(config('services.ga4.measurement_id'))
            gtag('event', 'view_item', {
                currency: 'INR',
                value: {{ (float) $product->price }},
                items: [{
                    item_id: '{{ $product->sku ?? $product->id }}',
                    item_name: @json($product->name),
                    item_category: @json($product->category?->name ?? ''),
                    item_brand: @json($product->brand?->name ?? ''),
                    price: {{ (float) $product->price }},
                    quantity: 1
                }]
            });
            @endif

            @if(config('services.facebook.pixel_id'))
            fbq('track', 'ViewContent', {
                content_name: @json($product->name),
                content_category: @json($product->category?->name ?? ''),
                content_ids: ['{{ $product->id }}'],
                content_type: 'product',
                value: {{ (float) $product->price }},
                currency: 'INR'
            }@if(!empty($fbEventId)), {eventID: '{{ $fbEventId }}'}@endif);
            @endif
        });
    </script>
    @endif
</x-layouts.app>
