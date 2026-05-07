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
        <meta property="product:price:amount" content="{{ $product->price }}">
        <meta property="product:price:currency" content="GBP">
        <meta property="product:availability" content="{{ $product->isInStock() ? 'in stock' : 'out of stock' }}">
        <meta property="product:condition" content="new">
        <meta property="product:retailer_item_id" content="{{ $product->id }}">
        @if($product->brand)
        <meta property="product:brand" content="{{ $product->brand->name }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $product->name }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
        <meta name="twitter:image" content="{{ $product->primary_image_url }}">

        {{-- JSON-LD Structured Data --}}
        <x-product-schema :productSchema="$productSchema ?? null" :faqSchema="$faqSchema ?? null" />
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-[#efefef]">
        <div class="container mx-auto px-4 py-2">
            <x-breadcrumb :items="$breadcrumbs" />
        </div>
    </div>

    <div class="container mx-auto px-4 py-4 lg:py-6">
        <!-- Product Main Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6">
            <!-- Image Gallery — Col 1 -->
            @php
                $galleryImages = $product->images->count() > 0
                    ? $product->images->pluck('url')->map(fn($u) => str_starts_with($u, 'http') ? $u : asset('storage/' . $u))->values()->toArray()
                    : [asset('images/no-product-image.svg')];
            @endphp
            <div x-data="{
                    images: @js($galleryImages),
                    videoUrl: '{{ $product->video_url ?? '' }}',
                    showingVideo: false,
                    activeIndex: 0,
                    touchStartX: 0,
                    touchEndX: 0,
                    get activeImage() { return this.images[this.activeIndex] || '{{ $product->primary_image_url }}'; },
                    select(index) {
                        this.showingVideo = false;
                        if (index !== this.activeIndex) {
                            this.activeIndex = index;
                        }
                    },
                    showVideo() { this.showingVideo = true; },
                    next() { this.showingVideo = false; this.activeIndex = (this.activeIndex + 1) % this.images.length; },
                    prev() { this.showingVideo = false; this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length; },
                    handleSwipe() {
                        const diff = this.touchStartX - this.touchEndX;
                        if (Math.abs(diff) > 50) {
                            diff > 0 ? this.next() : this.prev();
                        }
                    },
                 }"
                 class="lg:col-span-5 space-y-3">

                <div class="flex gap-3">
                    <!-- Thumbnails (Desktop vertical with scroll) -->
                    @if($product->images->count() > 1)
                        <div class="hidden lg:block w-16 shrink-0 relative" x-data="{ thumbEl: null }" x-init="thumbEl = $refs.thumbStrip">
                            {{-- Up arrow --}}
                            <button @click="thumbEl.scrollBy({ top: -144, behavior: 'smooth' })"
                                    class="absolute -top-1 left-0 right-0 z-10 flex justify-center py-0.5 bg-gradient-to-b from-white via-white/90 to-transparent hover:text-[#506282] text-neutral-400 transition-colors"
                                    aria-label="Scroll up">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <div x-ref="thumbStrip" class="flex flex-col gap-2 max-h-[480px] overflow-y-auto scrollbar-hide py-5">
                                @foreach($product->images as $index => $image)
                                    <button @click="select({{ $index }}); $el.scrollIntoView({ behavior: 'smooth', block: 'nearest' })"
                                            class="w-16 h-16 rounded border-2 overflow-hidden shrink-0 transition-all duration-200 cursor-pointer"
                                            :class="activeIndex === {{ $index }} && !showingVideo
                                                ? 'border-[#506282] shadow-sm'
                                                : 'border-[#efefef] hover:border-[#506282]'">
                                        <img src="{{ str_starts_with($image->url, 'http') ? $image->url : asset('storage/' . $image->url) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                                    </button>
                                @endforeach
                                @if($product->video_url)
                                    <button @click="showVideo(); $el.scrollIntoView({ behavior: 'smooth', block: 'nearest' })"
                                            class="w-16 h-16 rounded border-2 overflow-hidden shrink-0 transition-all duration-200 cursor-pointer relative"
                                            :class="showingVideo ? 'border-[#506282] shadow-sm' : 'border-[#efefef] hover:border-[#506282]'">
                                        <div class="w-full h-full bg-neutral-900 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </button>
                                @endif
                            </div>
                            {{-- Down arrow --}}
                            <button @click="thumbEl.scrollBy({ top: 144, behavior: 'smooth' })"
                                    class="absolute -bottom-1 left-0 right-0 z-10 flex justify-center py-0.5 bg-gradient-to-t from-white via-white/90 to-transparent hover:text-[#506282] text-neutral-400 transition-colors"
                                    aria-label="Scroll down">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                    @endif

                    <!-- Main Image / Video -->
                    <div class="relative bg-white border border-[#efefef] rounded-lg overflow-hidden group flex-1"
                         @touchstart="touchStartX = $event.changedTouches[0].screenX"
                         @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()">
                        {{-- Video Player --}}
                        @if($product->video_url)
                        <div x-show="showingVideo" x-cloak class="aspect-[9/16] max-h-[520px] mx-auto bg-black flex items-center justify-center">
                            <video x-show="showingVideo"
                                   class="w-full h-full object-contain"
                                   controls playsinline
                                   :src="showingVideo ? '{{ str_starts_with($product->video_url, 'http') ? $product->video_url : asset($product->video_url) }}' : ''">
                            </video>
                        </div>
                        @endif

                        {{-- Image View --}}
                        <div x-show="!showingVideo"
                             class="relative aspect-square select-none">

                            <!-- Normal image -->
                            <img x-ref="mainImg"
                                 src="{{ $galleryImages[0] ?? asset('images/placeholder-product.svg') }}"
                                 :src="activeImage"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-contain"
                                 onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                        </div>

                        <div x-show="!showingVideo && images.length === 0" class="flex flex-col items-center justify-center aspect-square bg-[#f7f7f7] rounded-lg">
                            <svg class="w-24 h-24 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-neutral-400 mt-2">No image available</p>
                        </div>

                        <!-- Image counter -->
                        <template x-if="images.length > 1">
                            <div class="absolute bottom-3 left-3 bg-white/90 text-[#222222] text-xs font-medium px-2.5 py-1.5 rounded-full shadow-sm border border-[#efefef]" x-text="(activeIndex + 1) + ' / ' + images.length"></div>
                        </template>

                        <!-- Nav Arrows -->
                        <template x-if="images.length > 1">
                            <div>
                                <button @click="prev()" aria-label="Previous image"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white hover:bg-[#f7f7f7] rounded-full flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4 text-[#222222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button @click="next()" aria-label="Next image"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white hover:bg-[#f7f7f7] rounded-full flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4 text-[#222222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Mobile Thumbnails -->
                @if($product->images->count() > 1 || $product->video_url)
                    <div class="flex lg:hidden gap-2 overflow-x-auto pb-1 scrollbar-thin">
                        @foreach($product->images as $index => $image)
                            <button @click="select({{ $index }})"
                                    class="w-14 h-14 rounded border-2 overflow-hidden shrink-0"
                                    :class="activeIndex === {{ $index }} && !showingVideo ? 'border-[#506282]' : 'border-[#efefef]'">
                                <img src="{{ str_starts_with($image->url, 'http') ? $image->url : asset('storage/' . $image->url) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                            </button>
                        @endforeach
                        @if($product->video_url)
                            <button @click="showVideo()"
                                    class="w-14 h-14 rounded border-2 overflow-hidden shrink-0 relative"
                                    :class="showingVideo ? 'border-[#506282]' : 'border-[#efefef]'">
                                <div class="w-full h-full bg-neutral-900 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </button>
                        @endif
                    </div>
                @endif

            </div>

            <!-- Product Info — Col 2 (Middle) -->
            <div class="lg:col-span-4 space-y-3">
                <!-- Brand -->
                @if($product->brand)
                    <a href="{{ route('brands.show', $product->brand) }}" class="text-sm text-[#202a40] hover:text-[#506282] hover:underline">
                        Visit the {{ $product->brand->name }} Store
                    </a>
                @endif

                <!-- Title -->
                <h1 class="text-lg lg:text-xl font-normal text-[#222222] leading-snug">{{ $product->name }}</h1>

                <!-- Rating Row -->
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span class="text-sm text-[#202a40]">{{ number_format($product->rating, 1) }}</span>
                    <a href="#customer-reviews" class="inline-flex items-center gap-0.5 group">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-[16px] h-[16px]" fill="currentColor" viewBox="0 0 20 20" style="color: {{ $i <= round($product->rating) ? '#FBBF24' : '#E0E0E0' }};">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </a>
                    <a href="#customer-reviews" class="text-sm text-[#202a40] hover:text-[#506282] hover:underline">{{ number_format($product->review_count) }} ratings</a>
                </div>

                <hr class="border-[#efefef]">

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
                        <span class="text-[28px] font-medium text-[#222222]">@price($product->price)</span>
                    </div>
                    @if($product->mrp > $product->price)
                        <div class="text-sm text-[#555555]">
                            M.R.P.: <span class="line-through">@price($product->mrp)</span>
                        </div>
                    @endif
                    <p class="text-xs text-[#555555]">Inclusive of all taxes</p>
                </div>

                <!-- Stock Urgency -->
                @if($product->stock_quantity > 0 && $product->stock_quantity <= 10)
                <div class="flex items-center gap-1.5 my-2 px-2.5 py-1.5 bg-[#202a40]/10 rounded">
                    <span class="inline-block w-2 h-2 bg-[#202a40] rounded-full animate-pulse"></span>
                    <span class="text-xs text-[#202a40] font-semibold">Only {{ $product->stock_quantity }} left in stock - order soon!</span>
                </div>
                @elseif($product->stock_quantity > 10)
                <p class="text-xs text-[#202a40] font-semibold my-1.5">&#10003; In Stock</p>
                @endif

                <!-- Available Coupons -->
                @if(isset($availableCoupons) && $availableCoupons->count())
                <div x-data="{ copied: '' }" class="rounded-xl border border-neutral-200 overflow-hidden">
                    {{-- Header --}}
                    <div class="flex items-center gap-2 px-3 py-2.5 bg-neutral-50 border-b border-neutral-200">
                        <svg class="w-4 h-4 text-[#202a40] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm2 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/><path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/></svg>
                        <span class="text-xs font-semibold text-neutral-700 uppercase tracking-wide">Available Offers</span>
                    </div>

                    {{-- Coupon list --}}
                    <div class="divide-y divide-neutral-100">
                        @foreach($availableCoupons as $coupon)
                        @php
                            $disc = $coupon->type === 'percentage'
                                ? (int)$coupon->value . '% Off'
                                : 'Flat £' . number_format($coupon->value) . ' Off';
                            $extra = ($coupon->type === 'percentage' && $coupon->max_discount)
                                ? ' (upto £' . number_format($coupon->max_discount) . ')'
                                : '';
                            $desc = trim($coupon->description ?? '');
                            if ($coupon->min_order_amount) $desc .= ($desc ? ' · ' : '') . 'Min. £' . number_format($coupon->min_order_amount);
                        @endphp
                        <div class="flex items-center gap-3 px-3 py-2.5 transition-colors"
                             :class="copied === '{{ $coupon->code }}' ? 'bg-green-50' : 'bg-white'">

                            {{-- Code badge --}}
                            <div class="shrink-0 border border-dashed rounded px-2 py-1 min-w-[80px] text-center"
                                 :class="copied === '{{ $coupon->code }}' ? 'border-green-400 bg-green-50' : 'border-[#202a40]/50 bg-[#202a40]/5'">
                                <span class="text-[11px] font-bold tracking-wider"
                                      :class="copied === '{{ $coupon->code }}' ? 'text-green-700' : 'text-[#202a40]'">{{ $coupon->code }}</span>
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-semibold text-neutral-800 leading-snug">
                                    {{ $disc }}<span class="text-[11px] font-normal text-neutral-500">{{ $extra }}</span>
                                </p>
                                @if($desc)
                                <p class="text-[11px] text-neutral-400 truncate leading-tight mt-0.5">{{ $desc }}</p>
                                @endif
                            </div>

                            {{-- Copy button --}}
                            <button @click="
                                    const code = '{{ $coupon->code }}';
                                    if (navigator.clipboard) {
                                        navigator.clipboard.writeText(code);
                                    } else {
                                        const el = document.createElement('textarea');
                                        el.value = code; el.style.position='fixed'; el.style.opacity='0';
                                        document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
                                    }
                                    copied = code; setTimeout(() => { if(copied === code) copied = ''; }, 2500);
                                "
                                class="shrink-0 flex items-center gap-1 text-[11px] font-semibold transition-colors"
                                :class="copied === '{{ $coupon->code }}' ? 'text-green-600' : 'text-[#202a40] hover:text-[#2d3a55]'">
                                <svg x-show="copied !== '{{ $coupon->code }}'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <svg x-show="copied === '{{ $coupon->code }}'" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="copied === '{{ $coupon->code }}' ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Trust Badges (Free Delivery, Pay on Delivery, etc.) -->
                <div class="grid grid-cols-3 gap-2 py-3">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-11 h-11 bg-[#F0F8F8] rounded-full flex items-center justify-center mb-1.5">
                            <svg class="w-5 h-5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <p class="text-[10px] font-medium text-[#222222] leading-tight">Free Delivery</p>
                        <p class="text-[9px] text-[#555555]">Above {{ currency_symbol() }}{{ \App\Models\Setting::get('free_shipping_threshold', 30) }}</p>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <div class="w-11 h-11 bg-[#F0F8F8] rounded-full flex items-center justify-center mb-1.5">
                            <svg class="w-5 h-5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </div>
                        <p class="text-[10px] font-medium text-[#222222] leading-tight">7 Days Return</p>
                        <p class="text-[9px] text-[#555555]">Easy returns</p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-11 h-11 bg-[#F0F8F8] rounded-full flex items-center justify-center mb-1.5">
                            <svg class="w-5 h-5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-[10px] font-medium text-[#222222] leading-tight">Secure Payment</p>
                        <p class="text-[9px] text-[#555555]">100% secure</p>
                    </div>
                </div>

                <!-- About this item -->
                @if($product->short_description)
                    <div>
                        <h3 class="text-sm font-bold text-[#222222] mb-2">About this item</h3>
                        <ul class="space-y-1.5 text-sm text-[#333] list-disc pl-4">
                            @foreach(preg_split('/[\.\n]+/', $product->short_description, -1, PREG_SPLIT_NO_EMPTY) as $point)
                                @if(strlen(trim($point)) > 3)
                                    <li class="leading-relaxed">{{ trim($point) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>

            <!-- Buy Box — Col 3 (Right sidebar) -->
            <div class="lg:col-span-3" x-data="quantitySelector()">
                <div class="border border-[#efefef] rounded-lg p-4 space-y-3 lg:sticky lg:top-4">
                    <!-- Price (repeated in buy box) -->
                    <div class="text-[24px] font-medium text-[#222222]">@price($product->price)</div>

                    @if($product->price >= \App\Models\Setting::get('free_shipping_threshold', 30))
                        <div class="text-sm">
                            <span class="text-[#202a40] font-medium">FREE delivery</span>
                            <span class="text-[#222222] font-medium">{{ now()->addDays(3)->format('D, d M') }}</span>
                        </div>
                    @endif

                    <!-- Stock Status -->
                    @if($product->stock_quantity > 0)
                        <p class="text-lg font-medium text-[#202a40]">In stock</p>
                    @else
                        <p class="text-lg font-medium text-[#CC0C39]">Currently unavailable</p>
                    @endif

                    <!-- Quantity -->
                    @if($product->stock_quantity > 0)
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-[#222222]">Qty:</label>
                        <select x-model="quantity" class="border border-[#efefef] rounded-lg bg-[#f2f2f2] text-sm py-1.5 px-3 shadow-sm focus:ring-[#202a40] focus:border-[#202a40]">
                            @for($i = 1; $i <= min($product->stock_quantity, 10); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    @else
                    <div class="flex items-center gap-2.5 px-3 py-2.5 bg-red-50 border border-red-300 rounded-lg">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636L5.636 18.364M5.636 5.636l12.728 12.728"/>
                        </svg>
                        <span class="text-sm font-semibold text-red-600">Out of Stock — This item is currently unavailable</span>
                    </div>
                    @endif

                    {{-- Inline stock-limit error (no toast) --}}
                    @if($product->stock_quantity > 0)
                    <p x-data="{ msg: '' }"
                       @cart-stock-error.window="msg = $event.detail.message; setTimeout(() => msg = '', 4000)"
                       x-show="msg"
                       x-transition.opacity
                       x-text="msg"
                       class="text-sm font-semibold text-red-600"
                       style="display:none;"></p>
                    @endif

                    <!-- CTA Buttons -->
                    <div class="flex flex-col gap-2" data-sticky-trigger>
                        @if($product->stock_quantity > 0)
                            <button x-data="{ adding: false, added: false }"
                                    @click="if(adding) return; adding = true; await $store.cart.add({{ $product->id }}, quantity); adding = false; added = true; setTimeout(() => added = false, 2000)"
                                    :disabled="adding"
                                    class="w-full flex items-center justify-center gap-2 bg-[#202a40] text-white font-semibold py-3.5 px-6 rounded-lg text-sm transition-all duration-200 hover:bg-[#506282] disabled:opacity-70"
                                    :class="added ? '!bg-emerald-600' : ''">
                                <svg x-show="!adding && !added" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                <svg x-show="adding" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <svg x-show="added" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="adding ? 'Adding...' : (added ? 'Added!' : 'Add to Cart')"></span>
                            </button>
                            @auth
                            <button @click="$store.cart.add({{ $product->id }}, quantity); setTimeout(() => window.location.href = '{{ route('checkout.index') }}', 300)"
                                    class="w-full flex items-center justify-center gap-2 bg-[#506282] text-white font-semibold py-3.5 px-6 rounded-lg text-sm transition-all duration-200 hover:bg-[#202a40] hover:shadow-lg cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Buy Now
                            </button>
                            @else
                            <button @click="
                                        $store.cart.add({{ $product->id }}, quantity);
                                        setTimeout(() => window.location.href = '{{ route('checkout.index') }}', 300)
                                    "
                                    class="w-full flex items-center justify-center gap-2 bg-[#506282] text-white font-semibold py-3.5 px-6 rounded-lg text-sm transition-all duration-200 hover:bg-[#202a40] hover:shadow-lg cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Buy Now
                            </button>
                            @endauth
                        @else
                            <button @click="$dispatch('notify-stock', { productId: {{ $product->id }}, productName: '{{ addslashes($product->name) }}' })"
                                    class="w-full flex items-center justify-center gap-2 bg-neutral-100 text-neutral-600 font-semibold py-3.5 px-6 rounded-lg text-sm transition-all duration-200 hover:bg-neutral-200 hover:text-neutral-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notify Me When Available
                            </button>
                        @endif
                    </div>

                    <!-- Secure Transaction -->
                    <div class="flex items-center gap-1.5 text-xs text-[#555555]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Secure transaction
                    </div>

                    <!-- Sold by -->
                    <div class="text-xs text-[#555555] space-y-0.5">
                        <div>Ships from <span class="text-[#222222] font-medium">{{ config('app.name') }}</span></div>
                        <div>Sold by <span class="text-[#202a40]">{{ $product->seller?->store_name ?? config('app.name') }}</span></div>
                    </div>

                    <hr class="border-[#efefef]">

                    <!-- Wishlist -->
                    <button @click="$store.wishlist.toggle({{ $product->id }})"
                            class="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium rounded-full border transition-colors"
                            :class="$store.wishlist.has({{ $product->id }}) ? 'text-[#CC0C39] border-[#CC0C39]/30 bg-[#CC0C39]/5' : 'text-[#222222] border-[#efefef] hover:bg-[#f7f7f7]'"
                            aria-label="Toggle wishlist">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span x-text="$store.wishlist.has({{ $product->id }}) ? 'Added to Wishlist' : 'Add to Wishlist'"></span>
                    </button>

                    <!-- Share -->
                    @php
                        $shareUrl   = route('products.show', $product->slug);
                        $shareTitle = $product->name;
                        $shareText  = $product->name . ' — Check this out on MusCo!';
                        $waUrl      = 'https://wa.me/?text=' . urlencode($shareText . ' ' . $shareUrl);
                        $fbUrl      = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl);
                        $twUrl      = 'https://twitter.com/intent/tweet?text=' . urlencode($shareText) . '&url=' . urlencode($shareUrl);
                    @endphp
                    <div x-data="{
                            open: false,
                            copied: false,
                            copyLink() {
                                const url = '{{ $shareUrl }}';
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(url).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2500); });
                                } else {
                                    const el = document.createElement('textarea');
                                    el.value = url; el.style.position = 'fixed'; el.style.opacity = '0';
                                    document.body.appendChild(el); el.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(el);
                                    this.copied = true; setTimeout(() => this.copied = false, 2500);
                                }
                            },
                            nativeShare() {
                                if (navigator.share) {
                                    navigator.share({ title: '{{ addslashes($shareTitle) }}', text: '{{ addslashes($shareText) }}', url: '{{ $shareUrl }}' });
                                } else { this.open = !this.open; }
                            }
                        }"
                         @click.outside="open = false"
                         class="relative flex items-center justify-center gap-2 pt-1">

                        <!-- Share trigger button -->
                        <button @click="nativeShare()"
                                class="flex items-center gap-1.5 text-xs text-[#555] hover:text-[#202a40] transition-colors font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Share
                        </button>

                        <!-- WhatsApp direct -->
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                           class="w-8 h-8 flex items-center justify-center rounded-full bg-[#25D366]/10 text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all"
                           aria-label="Share on WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>

                        <!-- Copy link button -->
                        <button @click="copyLink()"
                                class="w-8 h-8 flex items-center justify-center rounded-full transition-all"
                                :class="copied ? 'bg-green-100 text-green-600' : 'bg-neutral-100 text-[#555] hover:bg-[#202a40]/10 hover:text-[#202a40]'"
                                :title="copied ? 'Copied!' : 'Copy link'">
                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <svg x-show="copied" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>

                        <!-- More options dropdown -->
                        <div x-show="open" x-cloak x-transition
                             class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-white border border-neutral-200 rounded-xl shadow-xl p-2 z-50 min-w-[160px]">
                            <a href="{{ $fbUrl }}" target="_blank" rel="noopener"
                               class="flex items-center gap-2 px-3 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                Facebook
                            </a>
                            <a href="{{ $twUrl }}" target="_blank" rel="noopener"
                               class="flex items-center gap-2 px-3 py-2 text-sm text-neutral-700 hover:bg-neutral-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                X (Twitter)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description (A+ Content — inline, no tabs) -->
        @if($product->description)
            <section class="mt-8 border-t border-[#efefef] pt-6">
                <h2 class="text-lg font-bold text-[#222222] mb-4">Product Description</h2>
                <div class="prose prose-neutral max-w-none text-sm text-[#333] leading-relaxed">
                    {!! $product->description !!}
                </div>
            </section>
        @endif

        <!-- A+ Content Section (Product-specific rich media) -->
        @php
            $aplusPath = 'images/aplus/' . $product->slug;
            $hasAplus = file_exists(public_path($aplusPath . '/hero.jpg'));
        @endphp
        @if($hasAplus)
            <section class="mt-8 border-t border-[#efefef] pt-6">
                {{-- A+ Hero Banner --}}
                <div class="rounded-xl overflow-hidden mb-6">
                    <img src="{{ asset($aplusPath . '/hero.jpg') }}" alt="{{ $product->name }} - Premium Quality" class="w-full h-auto" loading="lazy">
                </div>

                {{-- A+ Features Grid --}}
                @if(file_exists(public_path($aplusPath . '/features.jpg')))
                <div class="rounded-xl overflow-hidden mb-6">
                    <img src="{{ asset($aplusPath . '/features.jpg') }}" alt="{{ $product->name }} - Key Features" class="w-full h-auto" loading="lazy">
                </div>
                @endif

                {{-- A+ Brand Story --}}
                @if(file_exists(public_path($aplusPath . '/brand-story.jpg')))
                <div class="rounded-xl overflow-hidden mb-6">
                    <img src="{{ asset($aplusPath . '/brand-story.jpg') }}" alt="{{ $product->name }} - Brand Story" class="w-full h-auto" loading="lazy">
                </div>
                @endif

                {{-- A+ Lifestyle --}}
                @if(file_exists(public_path($aplusPath . '/lifestyle.jpg')))
                <div class="rounded-xl overflow-hidden mb-6">
                    <img src="{{ asset($aplusPath . '/lifestyle.jpg') }}" alt="{{ $product->name }} - Lifestyle" class="w-full h-auto" loading="lazy">
                </div>
                @endif

            </section>
        @endif

        <!-- Specifications -->
        @if($product->sku || $product->weight || $product->dimensions || $product->brand || ($product->attributes && count($product->attributes)))
            <section class="mt-6 pt-2">
                <h2 class="text-lg font-bold text-[#222222] mb-4">Product Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                    @if($product->sku)
                        <div class="flex border-b border-[#efefef] last:border-b-0 py-2.5">
                            <span class="w-1/3 text-sm text-[#555555] font-medium">SKU</span>
                            <span class="w-2/3 text-sm text-[#222222]">{{ $product->sku }}</span>
                        </div>
                    @endif
                    @if($product->brand)
                        <div class="flex border-b border-[#efefef] last:border-b-0 py-2.5">
                            <span class="w-1/3 text-sm text-[#555555] font-medium">Brand</span>
                            <span class="w-2/3 text-sm text-[#222222]">{{ $product->brand->name }}</span>
                        </div>
                    @endif
                    @if($product->weight)
                        <div class="flex border-b border-[#efefef] last:border-b-0 py-2.5">
                            <span class="w-1/3 text-sm text-[#555555] font-medium">Weight</span>
                            <span class="w-2/3 text-sm text-[#222222]">{{ $product->weight }} kg</span>
                        </div>
                    @endif
                    @if($product->dimensions)
                        <div class="flex border-b border-[#efefef] last:border-b-0 py-2.5">
                            <span class="w-1/3 text-sm text-[#555555] font-medium">Dimensions</span>
                            <span class="w-2/3 text-sm text-[#222222]">{{ $product->dimensions }}</span>
                        </div>
                    @endif
                    @if($product->category)
                        <div class="flex border-b border-[#efefef] last:border-b-0 py-2.5">
                            <span class="w-1/3 text-sm text-[#555555] font-medium">Category</span>
                            <span class="w-2/3 text-sm text-[#222222]">{{ $product->category->name }}</span>
                        </div>
                    @endif
                    @if(is_array($product->specifications) && count($product->specifications))
                        @foreach($product->specifications as $specName => $specValue)
                            @php
                                if (is_array($specValue)) {
                                    $displaySpecName  = $specValue['name']  ?? $specName;
                                    $displaySpecValue = $specValue['value'] ?? '';
                                } else {
                                    $displaySpecName  = $specName;
                                    $displaySpecValue = $specValue;
                                }
                            @endphp
                            @if(in_array(strtolower($displaySpecName), ['supplier code', 'supplier_code'])) @continue @endif
                            <div class="flex border-b border-[#efefef] last:border-b-0 py-2.5">
                                <span class="w-1/3 text-sm text-[#555555] font-medium">{{ $displaySpecName }}</span>
                                <span class="w-2/3 text-sm text-[#222222]">{{ $displaySpecValue }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>
        @endif

        <!-- Frequently Bought Together -->
        @if(isset($frequentlyBought) && $frequentlyBought->count())
            <section class="mt-8 pt-6" x-data="frequentlyBought()">
                <h2 class="text-lg font-bold text-[#222222] mb-4">Frequently Bought Together</h2>
                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- Products with checkboxes --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Current product (always selected) --}}
                        <div class="flex flex-col items-center w-[130px] shrink-0">
                            <div class="relative">
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-[100px] h-[100px] object-contain rounded border border-[#efefef] bg-white p-1">
                                <span class="absolute -top-1 -left-1 w-5 h-5 bg-[#202a40] rounded flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                            <p class="text-[10px] text-[#222222] text-center mt-1.5 line-clamp-2 leading-tight">{{ Str::limit($product->name, 40) }}</p>
                            <p class="text-xs font-bold text-[#222222] mt-0.5">@price($product->price)</p>
                        </div>

                        @foreach($frequentlyBought as $idx => $fbProduct)
                            <span class="text-xl text-[#555555] font-light mx-1">+</span>
                            <div class="flex flex-col items-center w-[130px] shrink-0">
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked
                                           x-model="selected" value="{{ $fbProduct->id }}"
                                           @change="recalculate()">
                                    <img src="{{ $fbProduct->primary_image_url }}" alt="{{ $fbProduct->name }}"
                                         class="w-[100px] h-[100px] object-contain rounded border-2 bg-white p-1 transition-all peer-checked:border-[#202a40] border-[#efefef] opacity-60 peer-checked:opacity-100">
                                    <span class="absolute -top-1 -left-1 w-5 h-5 rounded flex items-center justify-center transition-colors peer-checked:bg-[#202a40] bg-[#efefef]">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </label>
                                <a href="{{ route('product.show', $fbProduct) }}" class="text-[10px] text-[#202a40] hover:text-[#506282] text-center mt-1.5 line-clamp-2 leading-tight hover:underline">{{ Str::limit($fbProduct->name, 40) }}</a>
                                <p class="text-xs font-bold text-[#222222] mt-0.5">@price($fbProduct->price)</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Total + Add All button --}}
                    <div class="flex flex-col justify-center items-start lg:items-center gap-3 lg:min-w-[200px] lg:border-l lg:border-[#efefef] lg:pl-6">
                        <div>
                            <p class="text-sm text-[#555555]">Total price for selected items:</p>
                            <p class="text-xl font-bold text-[#222222]" x-text="currencySymbol + totalPrice.toFixed(2)"></p>
                            @php
                                $fbtTotalMrp = $product->mrp + $frequentlyBought->sum('mrp');
                                $fbtTotalPrice = $product->price + $frequentlyBought->sum('price');
                            @endphp
                            <template x-if="totalSaving > 0">
                                <p class="text-xs text-[#202a40] font-semibold" x-text="'You save ' + currencySymbol + totalSaving.toFixed(2)"></p>
                            </template>
                        </div>
                        <button @click="addAllToCart()" :disabled="adding"
                                class="bg-[#202a40] text-white font-medium py-2.5 px-6 rounded-full text-sm transition-colors shadow-sm disabled:opacity-60">
                            <span x-text="adding ? 'Adding...' : 'Add all to Cart'"></span>
                        </button>
                    </div>
                </div>
            </section>

            <script>
                function frequentlyBought() {
                    const products = @json($frequentlyBought->map(fn($p) => ['id' => $p->id, 'price' => (float)$p->price, 'mrp' => (float)$p->mrp]));
                    const mainPrice = {{ (float) $product->price }};
                    const mainMrp = {{ (float) $product->mrp }};
                    return {
                        currencySymbol: '{{ currency_symbol() }}',
                        selected: products.map(p => String(p.id)),
                        adding: false,
                        get totalPrice() {
                            let total = mainPrice;
                            for (const p of products) {
                                if (this.selected.includes(String(p.id))) total += p.price;
                            }
                            return total;
                        },
                        get totalSaving() {
                            let mrpTotal = mainMrp;
                            let priceTotal = mainPrice;
                            for (const p of products) {
                                if (this.selected.includes(String(p.id))) {
                                    mrpTotal += p.mrp;
                                    priceTotal += p.price;
                                }
                            }
                            return mrpTotal - priceTotal;
                        },
                        recalculate() { /* reactivity handled by Alpine getters */ },
                        async addAllToCart() {
                            this.adding = true;
                            try {
                                await Alpine.store('cart').add({{ $product->id }});
                                for (const id of this.selected) {
                                    await Alpine.store('cart').add(parseInt(id));
                                }
                            } catch (e) { console.error(e); }
                            this.adding = false;
                        }
                    };
                }
            </script>
        @endif

        <!-- Compare with similar items -->
        @if(isset($compareProducts) && $compareProducts->count())
            <section class="mt-8 border-t border-[#efefef] pt-6">
                <h2 class="text-lg font-bold text-[#222222] mb-4">Compare with similar items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse min-w-[600px]">
                        <thead>
                            <tr>
                                <td class="p-3 w-32"></td>
                                <td class="p-3 text-center border-l border-[#efefef]">
                                    <a href="{{ route('product.show', $product) }}" class="block">
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-24 h-24 object-contain mx-auto mb-2">
                                        <p class="text-xs text-[#202a40] line-clamp-3 hover:text-[#506282]">{{ Str::limit($product->name, 80) }}</p>
                                    </a>
                                </td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#efefef]">
                                        <a href="{{ route('product.show', $cp) }}" class="block">
                                            <img src="{{ $cp->primary_image_url }}" alt="{{ $cp->name }}" class="w-24 h-24 object-contain mx-auto mb-2">
                                            <p class="text-xs text-[#202a40] line-clamp-3 hover:text-[#506282]">{{ Str::limit($cp->name, 80) }}</p>
                                        </a>
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-[#efefef]">
                                <td class="p-3 text-sm font-medium text-[#222222]">Customer Rating</td>
                                <td class="p-3 text-center border-l border-[#efefef]">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="text-sm">{{ number_format($product->rating, 1) }}</span>
                                        <svg class="w-4 h-4 text-[#202a40]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </div>
                                    <p class="text-xs text-[#202a40]">{{ $product->review_count }}</p>
                                </td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#efefef]">
                                        <div class="flex items-center justify-center gap-1">
                                            <span class="text-sm">{{ number_format($cp->rating ?? 0, 1) }}</span>
                                            <svg class="w-4 h-4 text-[#202a40]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </div>
                                        <p class="text-xs text-[#202a40]">{{ $cp->review_count ?? 0 }}</p>
                                    </td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#efefef]">
                                <td class="p-3 text-sm font-medium text-[#222222]">Price</td>
                                <td class="p-3 text-center border-l border-[#efefef] text-sm font-medium text-[#222222]">@price($product->price)</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#efefef] text-sm font-medium text-[#222222]">@price($cp->price)</td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#efefef]">
                                <td class="p-3 text-sm font-medium text-[#222222]">Brand</td>
                                <td class="p-3 text-center border-l border-[#efefef] text-sm text-[#222222]">{{ $product->brand?->name ?? '-' }}</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#efefef] text-sm text-[#222222]">{{ $cp->brand?->name ?? '-' }}</td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#efefef]">
                                <td class="p-3 text-sm font-medium text-[#222222]">Availability</td>
                                <td class="p-3 text-center border-l border-[#efefef] text-sm {{ $product->stock_quantity > 0 ? 'text-[#202a40]' : 'text-[#CC0C39]' }}">{{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#efefef] text-sm {{ $cp->stock_quantity > 0 ? 'text-[#202a40]' : 'text-[#CC0C39]' }}">{{ $cp->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}</td>
                                @endforeach
                            </tr>
                            <tr class="border-t border-[#efefef] bg-[#f7f7f7]">
                                <td class="p-3"></td>
                                <td class="p-3 text-center border-l border-[#efefef] text-sm font-medium text-[#222222] italic">This product</td>
                                @foreach($compareProducts as $cp)
                                    <td class="p-3 text-center border-l border-[#efefef]">
                                        <a href="{{ route('product.show', $cp) }}" class="text-sm text-[#202a40] hover:text-[#506282] hover:underline">View details →</a>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Customer Reviews Section -->
        <section class="mt-8 border-t border-[#efefef] pt-6" id="customer-reviews">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Rating Summary -->
                <div class="lg:col-span-4">
                    <h2 class="text-lg font-bold text-[#222222] mb-4">Customer Reviews</h2>
                    @php
                        $liveRating       = $product->approvedReviews->count() > 0 ? $product->approvedReviews->avg('rating') : 0;
                        $totalReviewCount = $product->approvedReviews->count();
                    @endphp

                    <style>
                        /* Amazon-style meter bars */
                        .rv-meter { -webkit-appearance:none; appearance:none; width:100%; height:16px; border:none; border-radius:2px; display:block; }
                        .rv-meter::-webkit-meter-bar { background:#e7e7e7; border-radius:2px; border:none; }
                        .rv-meter::-webkit-meter-optimum-value,
                        .rv-meter::-webkit-meter-suboptimum-value,
                        .rv-meter::-webkit-meter-even-less-good-value { background:#f5a623; border-radius:2px; }
                        .rv-meter::-moz-meter-bar { background:#f5a623; border-radius:2px; }
                        /* Star label link style */
                        .rv-star-link { font-size:12px; color:#007185; text-decoration:underline; white-space:nowrap; min-width:42px; cursor:pointer; }
                        .rv-star-link:hover { color:#C7511F; }
                        .rv-pct { font-size:12px; color:#007185; min-width:32px; text-align:right; }
                    </style>

                    {{-- Big rating + half-star stars + "out of 5" --}}
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
                        <span style="font-size:44px; font-weight:700; color:#222; line-height:1;">{{ number_format($liveRating, 1) }}</span>
                        <div>
                            {{-- Half-star aware orange stars --}}
                            <div style="display:flex; align-items:center; gap:1px; margin-bottom:3px;">
                                @for($i = 1; $i <= 5; $i++)
                                    @php
                                        $filled = $liveRating >= $i;
                                        $half   = !$filled && $liveRating >= ($i - 0.5);
                                    @endphp
                                    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        @if($half)
                                        <defs>
                                            <linearGradient id="hg{{ $i }}">
                                                <stop offset="50%" stop-color="#f5a623"/>
                                                <stop offset="50%" stop-color="#e7e7e7"/>
                                            </linearGradient>
                                        </defs>
                                        @endif
                                        <path fill="{{ $filled ? '#f5a623' : ($half ? 'url(#hg'.$i.')' : '#e7e7e7') }}"
                                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span style="font-size:13px; color:#555;">{{ number_format($liveRating, 1) }} out of 5</span>
                        </div>
                    </div>
                    <p style="font-size:13px; color:#555; margin-bottom:14px;">
                        {{ number_format($totalReviewCount) }} global {{ Str::plural('rating', $totalReviewCount) }}
                    </p>

                    {{-- Rating bars 5→1 using <meter> tag --}}
                    <div style="display:flex; flex-direction:column; gap:6px; margin-bottom:18px;">
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $ratingDistribution[$star] ?? 0;
                                $pct   = $totalReviewCount > 0 ? round(($count / $totalReviewCount) * 100) : 0;
                            @endphp
                            <div style="display:flex; align-items:center; gap:8px;">
                                <a href="#customer-reviews" class="rv-star-link" onclick="event.preventDefault();">{{ $star }} star</a>
                                <meter class="rv-meter"
                                       value="{{ $pct }}"
                                       min="0" max="100" optimum="100"
                                       aria-label="{{ $star }} star – {{ $pct }}%"
                                       style="flex:1; height:16px;">
                                </meter>
                                <span class="rv-pct">{{ $pct }}%</span>
                            </div>
                        @endfor
                    </div>

                    <hr class="border-[#efefef] my-4">

                    <!-- Write a Review CTA -->
                    <h3 class="text-base font-bold text-[#222222] mb-1">Review this product</h3>
                    <p class="text-sm text-[#555555] mb-3">Share your thoughts with other customers</p>
                    @auth
                        <a href="{{ route('account.reviews.create', $product) }}"
                           class="block w-full text-center py-1.5 text-sm font-medium text-[#222222] bg-white border border-[#efefef] rounded-full hover:bg-[#f7f7f7] shadow-sm transition-colors">
                            Write a customer review
                        </a>
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('account.reviews.create', $product)) }}"
                           class="block w-full text-center py-1.5 text-sm font-medium text-[#222222] bg-white border border-[#efefef] rounded-full hover:bg-[#f7f7f7] shadow-sm transition-colors">
                            Write a customer review
                        </a>
                    @endauth
                </div>

                <!-- Right: Reviews List -->
                <div class="lg:col-span-8">
                    <h3 class="text-base font-bold text-[#222222] mb-4">Top Reviews</h3>

                    @if($displayReviews->total() > 0)
                        <div class="space-y-6">
                            @foreach($displayReviews as $review)
                                <div class="border-b border-[#efefef] pb-5">
                                    <!-- Reviewer -->
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <div class="w-8 h-8 bg-[#f2f2f2] rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-[#555555]">{{ $review->reviewer_initial }}</span>
                                        </div>
                                        <span class="text-sm text-[#222222]">{{ $review->reviewer_name }}</span>
                                    </div>

                                    <!-- Stars + Title -->
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color: {{ $i <= $review->rating ? '#f5a623' : '#e7e7e7' }};">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @if($review->title)
                                            <span class="text-sm font-bold text-[#222222]">{{ $review->title }}</span>
                                        @endif
                                    </div>

                                    <!-- Date + Verified -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs text-[#555555]">Reviewed on {{ $review->created_at->format('j F Y') }}</span>
                                        @if($review->is_verified_purchase)
                                            <span class="text-xs font-bold text-[#C45500]">Verified Purchase</span>
                                        @else
                                            <span class="text-xs text-[#555555]">Unverified</span>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <p class="text-sm text-[#222222] leading-relaxed">{{ $review->content }}</p>

                                    <!-- Pros/Cons -->
                                    @if($review->pros && count($review->pros))
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach($review->pros as $pro)
                                                <span class="inline-flex items-center gap-1 text-xs bg-[#F0FFF4] text-[#202a40] px-2 py-0.5 rounded-full border border-[#C6F6D5]">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    {{ $pro }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($review->cons && count($review->cons))
                                        <div class="mt-1 flex flex-wrap gap-1.5">
                                            @foreach($review->cons as $con)
                                                <span class="inline-flex items-center gap-1 text-xs bg-[#FFF5F5] text-[#CC0C39] px-2 py-0.5 rounded-full border border-[#FED7D7]">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    {{ $con }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Helpful -->
                                    <div class="mt-3 flex items-center gap-3">
                                        <span class="text-xs text-[#555555]">{{ $review->helpful_count ?? 0 }} {{ ($review->helpful_count ?? 0) == 1 ? 'person' : 'people' }} found this helpful</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-col items-center gap-3">
                            <p class="text-xs text-[#555555]">
                                Showing {{ $displayReviews->firstItem() }}–{{ $displayReviews->lastItem() }} of {{ number_format($displayReviews->total()) }} reviews
                            </p>
                            {{-- Always-visible Prev / Next --}}
                            <div class="flex items-center gap-3">
                                @if($displayReviews->onFirstPage())
                                    <span class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-medium text-[#bbb] bg-white border border-[#e5e5e5] rounded-full cursor-not-allowed select-none">
                                        &#8592; Previous
                                    </span>
                                @else
                                    <a href="{{ $displayReviews->previousPageUrl() }}" rel="prev"
                                       class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-medium text-[#202a40] bg-white border border-[#202a40] rounded-full hover:bg-[#202a40] hover:text-white transition-colors">
                                        &#8592; Previous
                                    </a>
                                @endif

                                <span class="text-xs text-[#555]">
                                    Page {{ $displayReviews->currentPage() }} of {{ $displayReviews->lastPage() }}
                                </span>

                                @if($displayReviews->hasMorePages())
                                    <a href="{{ $displayReviews->nextPageUrl() }}" rel="next"
                                       class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-medium text-[#202a40] bg-white border border-[#202a40] rounded-full hover:bg-[#202a40] hover:text-white transition-colors">
                                        Next &#8594;
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-medium text-[#bbb] bg-white border border-[#e5e5e5] rounded-full cursor-not-allowed select-none">
                                        Next &#8594;
                                    </span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 bg-[#f7f7f7] rounded-lg border border-[#efefef]">
                            <p class="text-sm text-[#555555] mb-2">No reviews yet.</p>
                            <p class="text-sm text-[#222222]">Be the first to review this product!</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Recently Viewed -->
        <x-recently-viewed :limit="8" />

        <!-- Related Products -->
        @if($relatedProducts->count())
            <section class="mt-8 border-t border-[#efefef] pt-6">
                <div class="pc-section-header mb-4">
                    <h2 class="text-lg font-bold text-[#222222]" style="flex:1;">Products related to this item</h2>
                    <div class="pc-nav-arrows">
                        <button class="pc-nav-btn" onclick="pcScroll(this,-1)" aria-label="Previous">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button class="pc-nav-btn" onclick="pcScroll(this,1)" aria-label="Next">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="pc-glass-grid">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" compact />
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
             class="fixed bottom-16 lg:hidden left-0 right-0 z-40 bg-white border-t border-[#efefef] shadow-lg px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-base font-medium text-[#222222]">@price($product->price)</span>
                        @if($product->mrp > $product->price)
                            <span class="text-[11px] text-[#555555] line-through">@price($product->mrp)</span>
                        @endif
                    </div>
                </div>
                <button @click="$store.cart.add({{ $product->id }})"
                        class="shrink-0 bg-[#202a40] text-white font-medium py-2 px-4 rounded-full text-sm transition-colors shadow-sm">
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
                currency: 'GBP',
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
                currency: 'GBP'
            }@if(!empty($fbEventId)), {eventID: '{{ $fbEventId }}'}@endif);
            @endif
        });
    </script>
    @endif

    <x-trust-badges />
    <x-faq-section />
</x-layouts.app>
