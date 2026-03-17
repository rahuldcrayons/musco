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
    <div class="bg-neutral-50 border-b border-neutral-100">
        <div class="container mx-auto px-4 py-3">
            <x-breadcrumb :items="$breadcrumbs" />
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Product Main Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">
            <!-- Image Gallery -->
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
                 class="space-y-3">

                <!-- Main Image -->
                <div class="relative bg-neutral-50 rounded-lg overflow-hidden border border-neutral-100 group"
                     @touchstart="touchStartX = $event.changedTouches[0].screenX"
                     @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()">
                    <!-- Main product image (hover zoom + click to lightbox) -->
                    <div x-show="images.length > 0"
                         class="relative overflow-hidden cursor-zoom-in aspect-square lg:aspect-[4/5] max-h-[500px]"
                         x-data="{ zooming: false, zoomX: 50, zoomY: 50 }"
                         @mouseenter="zooming = true"
                         @mouseleave="zooming = false"
                         @mousemove="let r = $el.getBoundingClientRect(); zoomX = ((($event.clientX - r.left) / r.width) * 100); zoomY = ((($event.clientY - r.top) / r.height) * 100)"
                         @click="showZoom = true">
                        <img :src="activeImage"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover transition-transform duration-200"
                             :style="zooming ? 'transform: scale(2); transform-origin: ' + zoomX + '% ' + zoomY + '%' : ''">
                    </div>

                    <!-- Fallback when no images -->
                    <div x-show="images.length === 0" class="flex items-center justify-center py-20">
                        <svg class="w-20 h-20 text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <!-- Mobile swipe indicator dots -->
                    <template x-if="images.length > 1">
                        <div class="lg:hidden absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5">
                            <template x-for="(img, i) in images" :key="i">
                                <button @click="select(i)"
                                        class="w-2 h-2 rounded-full transition-all duration-300"
                                        :class="activeIndex === i ? 'bg-[#205258] w-4' : 'bg-neutral-300'"
                                        :aria-label="'View image ' + (i + 1)"></button>
                            </template>
                        </div>
                    </template>

                    <!-- Nav Arrows (visible on hover, desktop only) -->
                    <template x-if="images.length > 1">
                        <div>
                            <button @click="prev()"
                                    aria-label="Previous image"
                                    class="hidden lg:flex absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 hover:bg-white rounded-full items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button @click="next()"
                                    aria-label="Next image"
                                    class="hidden lg:flex absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 hover:bg-white rounded-full items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                        @foreach($product->images as $index => $image)
                            <button @click="select({{ $index }})"
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden border-2 shrink-0 transition-all duration-200 cursor-pointer"
                                    :class="activeIndex === {{ $index }}
                                        ? 'border-[#205258] ring-1 ring-[#205258]/30'
                                        : 'border-neutral-200 hover:border-neutral-400'">
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Image Lightbox / Zoom Modal -->
                <div x-show="showZoom" x-cloak
                     @keydown.escape.window="showZoom = false"
                     @keydown.left.window="if(showZoom) prev()"
                     @keydown.right.window="if(showZoom) next()"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
                     @touchstart="touchStartX = $event.changedTouches[0].screenX"
                     @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()">
                    <button @click="showZoom = false"
                            class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors z-10"
                            aria-label="Close zoom">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <template x-if="images.length > 1">
                        <div>
                            <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors" aria-label="Previous image">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors" aria-label="Next image">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                    <img :src="activeImage" alt="{{ $product->name }}" class="max-w-[90vw] max-h-[85vh] object-contain" @click.stop>
                    <template x-if="images.length > 1">
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/70 text-sm" x-text="(activeIndex + 1) + ' / ' + images.length"></div>
                    </template>
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Brand & Category -->
                <div class="flex items-center gap-4 text-sm">
                    @if($product->brand)
                        <a href="{{ route('brands.show', $product->brand) }}" class="text-primary-600 hover:text-primary-700 font-medium">
                            {{ $product->brand->name }}
                        </a>
                        <span class="text-neutral-300">|</span>
                    @endif
                    <a href="{{ route('category.show', $product->category) }}" class="text-neutral-600 hover:text-primary-500">
                        {{ $product->category->name }}
                    </a>
                </div>

                <!-- Title -->
                <h1 class="text-xl lg:text-2xl font-bold text-neutral-900">{{ $product->name }}</h1>

                <!-- Rating & Reviews -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $product->rating ? 'text-warning-400' : 'text-neutral-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="ml-2 text-neutral-700">{{ number_format($product->rating, 1) }}</span>
                    </div>
                    <a href="#reviews" class="text-primary-600 hover:text-primary-700 text-sm">
                        {{ $product->review_count }} reviews
                    </a>
                    <span class="text-neutral-300 hidden sm:inline">|</span>
                    <!-- Share -->
                    <div x-data="{ copied: false }" class="hidden sm:inline-flex items-center gap-2">
                        <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . route('product.show', $product)) }}"
                           target="_blank" rel="noopener"
                           class="text-neutral-600 hover:text-[#25D366] transition-colors" aria-label="Share on WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                                class="text-neutral-600 hover:text-neutral-600 transition-colors" aria-label="Copy link">
                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <svg x-show="copied" x-cloak class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Price -->
                <div class="flex items-baseline gap-3">
                    <span class="text-2xl font-bold text-neutral-900">@price($product->price)</span>
                    @if($product->mrp > $product->price)
                        <span class="text-base text-neutral-500 line-through">M.R.P: @price($product->mrp)</span>
                        <span class="text-sm font-semibold text-success-600">({{ round($product->discount_percentage) }}% off)</span>
                    @endif
                </div>

                <!-- Limited Time Deal + Urgency -->
                @if($product->price < $product->mrp)
                <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-100 rounded-lg p-3 space-y-2"
                     x-data="{
                        hours: 0, minutes: 0, seconds: 0,
                        init() {
                            // Timer: random 2-6 hours from page load, stored per product in sessionStorage
                            let key = 'deal_timer_{{ $product->id }}';
                            let end = sessionStorage.getItem(key);
                            if (!end) {
                                let h = Math.floor(Math.random() * 4) + 2;
                                end = Date.now() + (h * 3600000) + Math.floor(Math.random() * 3600000);
                                sessionStorage.setItem(key, end);
                            }
                            this.tick(parseInt(end));
                            setInterval(() => this.tick(parseInt(end)), 1000);
                        },
                        tick(end) {
                            let diff = Math.max(0, end - Date.now());
                            this.hours = Math.floor(diff / 3600000);
                            this.minutes = Math.floor((diff % 3600000) / 60000);
                            this.seconds = Math.floor((diff % 60000) / 1000);
                        }
                     }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs font-bold text-red-700 uppercase tracking-wide">Limited Time Deal</span>
                        </div>
                        <div class="flex items-center gap-1 font-mono text-sm font-bold text-red-600">
                            <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="String(hours).padStart(2,'0')">00</span>
                            <span>:</span>
                            <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="String(minutes).padStart(2,'0')">00</span>
                            <span>:</span>
                            <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="String(seconds).padStart(2,'0')">00</span>
                        </div>
                    </div>
                    @php
                        $piecesLeft = min($product->stock_quantity, rand(3, 8));
                    @endphp
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                        <span class="text-xs font-semibold text-orange-700">Only <strong>{{ $piecesLeft }}</strong> pieces left at this price!</span>
                        <div class="flex-1 h-1.5 bg-orange-100 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full" style="width: {{ max(15, min(70, $piecesLeft * 10)) }}%"></div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Mobile Share -->
                <div x-data="{ copied: false }" class="sm:hidden flex items-center gap-3">
                    <span class="text-xs text-neutral-600 uppercase tracking-wider">Share</span>
                    <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . route('product.show', $product)) }}"
                       target="_blank" rel="noopener"
                       class="w-8 h-8 flex items-center justify-center bg-neutral-100 rounded-full text-neutral-600 hover:text-[#25D366] hover:bg-[#25D366]/10 transition-colors" aria-label="Share on WhatsApp">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                            class="w-8 h-8 flex items-center justify-center bg-neutral-100 rounded-full text-neutral-600 hover:text-neutral-700 transition-colors" aria-label="Copy link">
                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <svg x-show="copied" x-cloak class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <span x-show="copied" x-cloak class="text-xs text-success-600">Copied!</span>
                </div>

                <!-- Short Description -->
                @if($product->short_description)
                    <p class="text-neutral-600">{{ $product->short_description }}</p>
                @endif

                <!-- Key Attributes -->
                @if(is_array($product->attributes) && count($product->attributes))
                    @php
                        $colorMap = cache()->remember('attr_color_map', 3600, function() {
                            $map = [];
                            foreach (\App\Models\Attribute::where('type', 'color')->with('values')->get() as $attr) {
                                foreach ($attr->values as $val) {
                                    if ($val->color_code) $map[$attr->name][$val->value] = $val->color_code;
                                }
                            }
                            return $map;
                        });
                    @endphp
                    <div class="space-y-3">
                        @foreach($product->attributes as $attrName => $attrValue)
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-neutral-600 w-28 shrink-0">{{ $attrName }}</span>
                                @if(isset($colorMap[$attrName][$attrValue]))
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full border-2 border-neutral-200 shrink-0" style="background-color: {{ $colorMap[$attrName][$attrValue] }}"></span>
                                        <span class="text-sm font-medium text-neutral-900">{{ $attrValue }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-neutral-100 text-sm font-medium text-neutral-700">{{ $attrValue }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Variants -->
                @if($product->variants->count())
                    <div x-data="{ selectedVariant: null }" class="space-y-4">
                        @foreach($product->variants->groupBy('attribute.name') as $attributeName => $variants)
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-2">{{ $attributeName }}</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($variants as $variant)
                                        <button type="button"
                                                @click="selectedVariant = {{ $variant->id }}"
                                                :class="selectedVariant === {{ $variant->id }} ? 'ring-2 ring-[#205258] border-[#205258]' : 'border-neutral-200'"
                                                class="px-4 py-2 border rounded-lg text-sm font-medium hover:border-[#205258] transition-colors {{ $variant->stock_quantity < 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            {{ $variant->attributeValues->pluck('value')->join(' / ') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Quantity & Add to Cart -->
                <div x-data="quantitySelector()" class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Quantity</label>
                            <div class="flex items-center border border-neutral-200 rounded-lg">
                                <button @click="decrement()" class="px-3 py-2 text-neutral-600 hover:text-neutral-900" aria-label="Decrease quantity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input type="number" x-model="quantity" min="1" max="{{ $product->stock_quantity }}"
                                       class="w-16 text-center border-0 focus:ring-0 text-sm" aria-label="Quantity">
                                <button @click="increment()" class="px-3 py-2 text-neutral-600 hover:text-neutral-900" aria-label="Increase quantity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="text-sm text-neutral-600 self-end pb-2">
                            @if($product->stock_quantity > 0)
                                <span class="text-success-600 font-medium">In Stock</span>
                                <span class="text-neutral-600">({{ $product->stock_quantity }} available)</span>
                            @else
                                <span class="text-error-600 font-medium">Out of Stock</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2.5" data-sticky-trigger>
                        @if($product->stock_quantity > 0)
                            <button x-data="{ adding: false, added: false }"
                                    @click="if(adding) return; adding = true; await $store.cart.add({{ $product->id }}, quantity); adding = false; added = true; setTimeout(() => added = false, 2000)"
                                    :disabled="adding"
                                    class="w-full flex items-center justify-center gap-2 bg-[#FFD814] hover:bg-[#F7CA00] text-neutral-900 font-medium py-2.5 px-6 rounded-full text-sm transition-colors border border-[#FCD200] disabled:opacity-70">
                                <template x-if="adding">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </template>
                                <span x-text="adding ? 'Adding...' : (added ? 'Added to Cart' : 'Add to Cart')"></span>
                            </button>
                            <button @click="$store.cart.add({{ $product->id }}, quantity); setTimeout(() => window.location.href = '{{ route('checkout.index') }}', 300)"
                                    class="w-full flex items-center justify-center gap-2 bg-[#FFA41C] hover:bg-[#FA8900] text-neutral-900 font-medium py-2.5 px-6 rounded-full text-sm transition-colors border border-[#FF8F00]">
                                Buy Now
                            </button>
                        @else
                            <button @click="$dispatch('notify-stock', { productId: {{ $product->id }}, productName: '{{ addslashes($product->name) }}' })"
                                    class="w-full flex items-center justify-center gap-2 bg-[#FFD814] hover:bg-[#F7CA00] text-neutral-900 font-medium py-2.5 px-6 rounded-full text-sm transition-colors border border-[#FCD200]">
                                Notify Me When Available
                            </button>
                        @endif

                        <button @click="$store.wishlist.toggle({{ $product->id }})"
                                class="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium rounded-full border transition-colors"
                                :class="$store.wishlist.has({{ $product->id }}) ? 'text-red-500 border-red-200 bg-red-50' : 'text-neutral-600 border-neutral-200 hover:bg-neutral-50'"
                                aria-label="Toggle wishlist">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span x-text="$store.wishlist.has({{ $product->id }}) ? 'Added to Wishlist' : 'Add to Wishlist'"></span>
                        </button>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-neutral-200">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <p class="text-xs text-neutral-600">Free Shipping</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <p class="text-xs text-neutral-600">Easy Returns</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-xs text-neutral-600">Secure Payment</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details Tabs -->
        <div class="mt-8" x-data="tabs('description')">
            <div class="border-b border-neutral-200">
                <nav class="flex gap-8 overflow-x-auto">
                    <button @click="activeTab = 'description'"
                            :class="activeTab === 'description' ? 'border-primary-500 text-primary-600' : 'border-transparent text-neutral-600 hover:text-neutral-700'"
                            class="py-4 px-1 border-b-2 font-medium whitespace-nowrap">
                        Description
                    </button>
                    <button @click="activeTab = 'specifications'"
                            :class="activeTab === 'specifications' ? 'border-primary-500 text-primary-600' : 'border-transparent text-neutral-600 hover:text-neutral-700'"
                            class="py-4 px-1 border-b-2 font-medium whitespace-nowrap">
                        Specifications
                    </button>
                    <button @click="activeTab = 'reviews'" id="reviews"
                            :class="activeTab === 'reviews' ? 'border-primary-500 text-primary-600' : 'border-transparent text-neutral-600 hover:text-neutral-700'"
                            class="py-4 px-1 border-b-2 font-medium whitespace-nowrap">
                        Reviews ({{ $product->review_count }})
                    </button>
                </nav>
            </div>

            <!-- Description Tab -->
            <div x-show="activeTab === 'description'" class="py-8">
                <div class="prose prose-neutral max-w-none">
                    {!! $product->description !!}
                </div>
            </div>

            <!-- Specifications Tab -->
            <div x-show="activeTab === 'specifications'" x-cloak class="py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex border-b border-neutral-100 py-3">
                        <span class="w-1/3 text-neutral-600">SKU</span>
                        <span class="w-2/3 font-medium">{{ $product->sku }}</span>
                    </div>
                    @if($product->weight)
                        <div class="flex border-b border-neutral-100 py-3">
                            <span class="w-1/3 text-neutral-600">Weight</span>
                            <span class="w-2/3 font-medium">{{ $product->weight }} kg</span>
                        </div>
                    @endif
                    @if($product->dimensions)
                        <div class="flex border-b border-neutral-100 py-3">
                            <span class="w-1/3 text-neutral-600">Dimensions</span>
                            <span class="w-2/3 font-medium">{{ $product->dimensions }}</span>
                        </div>
                    @endif
                    @if($product->brand)
                        <div class="flex border-b border-neutral-100 py-3">
                            <span class="w-1/3 text-neutral-600">Brand</span>
                            <span class="w-2/3 font-medium">{{ $product->brand->name }}</span>
                        </div>
                    @endif
                    @if($product->category)
                        <div class="flex border-b border-neutral-100 py-3">
                            <span class="w-1/3 text-neutral-600">Category</span>
                            <span class="w-2/3 font-medium">{{ $product->category->name }}</span>
                        </div>
                    @endif
                    @if(is_array($product->attributes) && count($product->attributes))
                        @foreach($product->attributes as $attrName => $attrValue)
                            <div class="flex border-b border-neutral-100 py-3">
                                <span class="w-1/3 text-neutral-600">{{ $attrName }}</span>
                                <span class="w-2/3 font-medium">{{ $attrValue }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Reviews Tab -->
            <div x-show="activeTab === 'reviews'" x-cloak class="py-8">
                @if($product->reviews->count())
                    <div class="space-y-6">
                        @foreach($product->reviews as $review)
                            <div class="border-b border-neutral-100 pb-6">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="font-medium text-primary-600">{{ $review->reviewer_initial }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-medium">{{ $review->reviewer_name }}</span>
                                            @if($review->is_verified_purchase)
                                                <span class="badge badge-success text-xs">Verified Purchase</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-warning-400' : 'text-neutral-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-neutral-600">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($review->title)
                                            <h4 class="font-medium mb-1">{{ $review->title }}</h4>
                                        @endif
                                        <p class="text-neutral-600">{{ $review->content }}</p>
                                        @if($review->pros && count($review->pros))
                                            <div class="mt-2 flex flex-wrap gap-1.5">
                                                @foreach($review->pros as $pro)
                                                    <span class="inline-flex items-center gap-1 text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded-full">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        {{ $pro }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($review->cons && count($review->cons))
                                            <div class="mt-1 flex flex-wrap gap-1.5">
                                                @foreach($review->cons as $con)
                                                    <span class="inline-flex items-center gap-1 text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        {{ $con }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-neutral-600 mb-4">No reviews yet. Be the first to review this product!</p>
                    </div>
                @endif

                {{-- Write a Review --}}
                <div class="mt-8 border-t border-neutral-200 pt-8">
                    @auth
                        <a href="{{ route('account.reviews.create', $product) }}" class="inline-flex items-center gap-2 bg-[#F8931D] hover:bg-[#E07E0A] text-white font-semibold py-2.5 px-5 rounded-lg text-sm transition-colors shadow-sm">Write a Review</a>
                    @else
                        {{-- Guest Review Form --}}
                        <div x-data="{ showForm: false }">
                            <button @click="showForm = !showForm" class="inline-flex items-center gap-2 bg-[#F8931D] hover:bg-[#E07E0A] text-white font-semibold py-2.5 px-5 rounded-lg text-sm transition-colors shadow-sm">
                                Write a Review
                            </button>

                            <form x-show="showForm" x-cloak method="POST" action="{{ route('product.guest-review', $product) }}" class="mt-6 max-w-lg space-y-4 bg-neutral-50 rounded-lg p-6">
                                @csrf
                                <input type="text" name="honeypot" class="hidden" value="" tabindex="-1" autocomplete="off">

                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-1">Your Name</label>
                                    <input type="text" name="guest_name" required class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. Priya S.">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-1">Email Address</label>
                                    <input type="email" name="guest_email" required class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="your@email.com">
                                    <p class="text-xs text-neutral-600 mt-1">We will send you a discount code as a thank you!</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-1">Rating</label>
                                    <div x-data="{ rating: 0, hover: 0 }" class="flex gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" @click="rating = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0">
                                                <svg class="w-7 h-7 cursor-pointer transition-colors" :class="(hover || rating) >= {{ $i }} ? 'text-warning-400' : 'text-neutral-200'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" :value="rating">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-1">Title <span class="text-neutral-600">(optional)</span></label>
                                    <input type="text" name="title" class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Sum up your experience">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-1">Your Review</label>
                                    <textarea name="content" required rows="4" minlength="20" class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Share your experience with this product..."></textarea>
                                </div>

                                <button type="submit" class="bg-[#F8931D] hover:bg-[#E07E0A] text-white font-semibold py-2.5 px-6 rounded-lg text-sm transition-colors shadow-sm">
                                    Submit Review
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>

        </div>

        <!-- Related Products -->
        @if($relatedProducts->count())
            <section class="mt-10 border-t border-neutral-200 pt-8">
                <h2 class="text-base lg:text-xl font-bold text-neutral-900 mb-4">Related Products</h2>
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
             class="fixed bottom-16 lg:hidden left-0 right-0 z-40 bg-white border-t border-neutral-200 shadow-lg px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-neutral-900 truncate">{{ $product->name }}</p>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-sm font-bold text-neutral-900">@price($product->price)</span>
                        @if($product->mrp > $product->price)
                            <span class="text-[11px] text-neutral-500 line-through">@price($product->mrp)</span>
                        @endif
                    </div>
                </div>
                <button @click="$store.cart.add({{ $product->id }})"
                        class="shrink-0 bg-[#FFD814] hover:bg-[#F7CA00] text-neutral-900 font-medium py-2.5 px-5 rounded-full text-sm transition-colors border border-[#FCD200]">
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
            });
            @endif
        });
    </script>
    @endif
</x-layouts.app>
