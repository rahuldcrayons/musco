<x-layouts.app>
    <x-slot name="title">{{ $siteSettings['site_name'] }} - {{ $siteSettings['site_tagline'] }}</x-slot>

    @push('meta')
        <meta name="description" content="{{ $siteSettings['site_tagline'] }} - Shop gadgets, home essentials, and accessories online at {{ $siteSettings['site_name'] }}.">
        <link rel="canonical" href="{{ url('/') }}">
        <meta property="og:title" content="{{ $siteSettings['site_name'] }} - {{ $siteSettings['site_tagline'] }}">
        <meta property="og:description" content="Shop gadgets, home essentials, and accessories online at {{ $siteSettings['site_name'] }}.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        @if($siteSettings['site_logo'])
        <meta property="og:image" content="{{ asset('images/' . $siteSettings['site_logo']) }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $siteSettings['site_name'] }} - {{ $siteSettings['site_tagline'] }}">
        <meta name="twitter:description" content="Shop gadgets, home essentials, and accessories online at {{ $siteSettings['site_name'] }}.">

        {{-- Organization + WebSite JSON-LD --}}
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => url('/') . '#organization',
                    'name' => $siteSettings['site_name'],
                    'url' => url('/'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('images/jikra-logo.png'),
                    ],
                    'description' => $siteSettings['site_tagline'] . ' - Shop gadgets, home essentials, and accessories online.',
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'contactType' => 'customer service',
                        'url' => url('/contact'),
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => url('/') . '#website',
                    'name' => $siteSettings['site_name'],
                    'url' => url('/'),
                    'publisher' => ['@id' => url('/') . '#organization'],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => url('/products') . '?search={search_term_string}',
                        ],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    <x-slot name="styles">
        @vite(['resources/css/home.css'])
    </x-slot>

    {{-- Flash Sale Popup --}}
    @if($flashSale)
        <div x-data="flashSalePopup({{ $flashSale->remaining_time }}, '{{ $flashSale->slug }}')"
             x-show="open" x-cloak
             @keydown.escape.window="dismiss()"
             class="fixed inset-0 z-60 flex items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="dismiss()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-90 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                 class="relative w-full max-w-md overflow-hidden rounded-2xl shadow-2xl" @click.stop>
                <button @click="dismiss()" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-white/80 hover:text-white rounded-full hover:bg-white/10 transition-colors z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="relative bg-gradient-to-br from-[#F8931D] via-[#E07E0A] to-[#D47200] px-6 pt-8 pb-6 text-center overflow-hidden">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full"></div>
                    <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="relative inline-flex items-center justify-center w-14 h-14 bg-white/15 rounded-full mb-4 ring-4 ring-white/10">
                        <svg class="w-7 h-7 text-yellow-200" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="text-white/80 text-xs font-semibold tracking-widest uppercase mb-1">Limited Time Offer</p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-2">{{ $flashSale->name }}</h2>
                    @if($flashSale->description)
                        <p class="text-white/80 text-sm leading-relaxed max-w-xs mx-auto mb-4">{{ Str::limit($flashSale->description, 100) }}</p>
                    @endif
                    <div class="flex items-center justify-center gap-2 sm:gap-3">
                        <div class="bg-white/15 backdrop-blur-sm rounded-xl px-3 py-2 min-w-[60px]">
                            <span class="block text-2xl font-bold text-white tabular-nums" x-text="hours">00</span>
                            <span class="block text-[10px] text-white/70 uppercase tracking-wide">Hours</span>
                        </div>
                        <span class="text-2xl font-bold text-white/50">:</span>
                        <div class="bg-white/15 backdrop-blur-sm rounded-xl px-3 py-2 min-w-[60px]">
                            <span class="block text-2xl font-bold text-white tabular-nums" x-text="minutes">00</span>
                            <span class="block text-[10px] text-white/70 uppercase tracking-wide">Mins</span>
                        </div>
                        <span class="text-2xl font-bold text-white/50">:</span>
                        <div class="bg-white/15 backdrop-blur-sm rounded-xl px-3 py-2 min-w-[60px]">
                            <span class="block text-2xl font-bold text-white tabular-nums" x-text="seconds">00</span>
                            <span class="block text-[10px] text-white/70 uppercase tracking-wide">Secs</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white px-6 py-5 text-center">
                    <p class="text-xs text-neutral-600 mb-3">
                        <span class="font-semibold text-neutral-700">{{ $flashSale->products_count }} {{ Str::plural('product', $flashSale->products_count) }}</span> on sale
                    </p>
                    <a href="{{ route('products.index') }}?flash_sale={{ $flashSale->slug }}" @click="dismiss()"
                       class="inline-flex items-center justify-center gap-2 w-full py-2 bg-[#F8931D] hover:bg-[#E07E0A] text-white text-sm font-bold rounded-xl shadow-lg transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Shop the Sale Now
                    </a>
                    <button @click="dismiss()" class="mt-2 text-xs text-neutral-600 hover:text-neutral-600 transition-colors">No thanks, maybe later</button>
                </div>
            </div>
        </div>
        <script>
            function flashSalePopup(remainingSeconds, saleSlug) {
                return {
                    open: false, remaining: remainingSeconds, timer: null,
                    get hours() { return String(Math.floor(this.remaining / 3600)).padStart(2, '0'); },
                    get minutes() { return String(Math.floor((this.remaining % 3600) / 60)).padStart(2, '0'); },
                    get seconds() { return String(this.remaining % 60).padStart(2, '0'); },
                    init() {
                        const key = 'flash_sale_dismissed_' + saleSlug;
                        if (sessionStorage.getItem(key)) return;
                        setTimeout(() => { this.open = true; document.body.style.overflow = 'hidden'; }, 1500);
                        this.timer = setInterval(() => {
                            if (this.remaining > 0) { this.remaining--; } else { clearInterval(this.timer); this.dismiss(); }
                        }, 1000);
                    },
                    dismiss() {
                        this.open = false; document.body.style.overflow = '';
                        sessionStorage.setItem('flash_sale_dismissed_' + saleSlug, '1');
                        if (this.timer) clearInterval(this.timer);
                    }
                };
            }
        </script>
    @endif

    <!-- ==========================================
         HERO BANNER SLIDER
         ========================================== -->
    @if(true)
    <section class="hero-banner"
             x-data="{
                current: 0,
                slides: [
                    @foreach($banners as $banner)
                    { img: '{{ asset('storage/' . $banner->image_url) }}', link: '{{ $banner->link ?? route('products.index') }}' },
                    @endforeach
                ],
                timer: null,
                init() { this.startTimer(); },
                startTimer() { this.timer = setInterval(() => this.next(), 5000); },
                next() { this.current = (this.current + 1) % this.slides.length; },
                prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; },
                goTo(i) { this.current = i; clearInterval(this.timer); this.startTimer(); }
             }">
        <div class="hero-slides">
            <template x-for="(slide, index) in slides" :key="index">
                <a :href="slide.link"
                   x-show="current === index"
                   x-transition:enter="transition-opacity ease-out duration-500"
                   x-transition:enter-start="opacity-0"
                   x-transition:enter-end="opacity-100"
                   x-transition:leave="transition-opacity ease-in duration-300"
                   x-transition:leave-start="opacity-100"
                   x-transition:leave-end="opacity-0"
                   class="hero-slide block">
                    <img :src="slide.img" :alt="'{{ $siteSettings['site_name'] }}'">
                </a>
            </template>

            <!-- Dots -->
            <div class="hero-dots">
                <template x-for="(slide, index) in slides" :key="'dot-'+index">
                    <button @click="goTo(index)" class="hero-dot" :class="current === index ? 'active' : ''"></button>
                </template>
            </div>
        </div>
        {{-- Waves removed - clean edge --}}
    </section>
    @endif

    <!-- ==========================================
         CATEGORY CAROUSEL
         ========================================== -->
    @if(isset($carouselCategories) && $carouselCategories->count())
        <section class="py-5 lg:py-6 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-1" style="-ms-overflow-style:none;scrollbar-width:none;">
                    @foreach($carouselCategories as $cat)
                        <a href="{{ route('category.show', $cat) }}"
                           class="flex flex-col items-center gap-2 shrink-0 group"
                           style="min-width: 80px; max-width: 90px;">
                            @php
                                $catImage = null;
                                if ($cat->image_url) {
                                    $catImage = asset('storage/' . $cat->image_url);
                                } elseif ($cat->products->first()?->primary_image_url) {
                                    $catImage = $cat->products->first()->primary_image_url;
                                }
                            @endphp
                            <div class="w-16 h-16 lg:w-[72px] lg:h-[72px] rounded-full overflow-hidden border-2 border-transparent group-hover:border-[#205258] transition-all bg-[#f8f6f3] flex items-center justify-center shadow-sm">
                                @if($catImage)
                                    <img src="{{ $catImage }}"
                                         alt="{{ $cat->name }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                @elseif($cat->icon)
                                    <span class="text-2xl">{{ $cat->icon }}</span>
                                @else
                                    <svg class="w-6 h-6 text-[#205258]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="text-[11px] lg:text-xs font-medium text-[#0F1111] text-center leading-tight line-clamp-2 group-hover:text-[#205258] transition-colors">
                                {{ $cat->name }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         FEATURED PRODUCTS - Horizontal Slider
         ========================================== -->
    @if($featuredProducts->count() && (!isset($sections['featured']) || $sections['featured']->is_active))
        <section class="py-8 lg:py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="section-header">
                    <h2 class="section-title">{{ $sections['featured']->title ?? 'New Arrivals' }}</h2>
                    <a href="{{ route('products.index') }}" class="view-all-link">
                        View All
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="product-slider">
                    @foreach($featuredProducts->take(10) as $product)
                        <div class="slide-item">
                            <x-product-card :product="$product" :compact="true" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         SHOP OUR REELS - Shoppable Instagram Carousel
         ========================================== -->
    <x-instagram-reels />

    <!-- ==========================================
         COFFEE LOVERS COLLECTION
         ========================================== -->
    @if(isset($coffeeProducts) && $coffeeProducts->count())
        <section class="py-8 lg:py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="section-header">
                    <h2 class="section-title">Love Over Coffee</h2>
                    <a href="{{ route('products.index') }}?search=coffee" class="view-all-link">
                        View All
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="product-slider">
                    @foreach($coffeeProducts as $product)
                        <div class="slide-item">
                            <x-product-card :product="$product" :compact="true" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         CATEGORY COLLECTIONS (Collage Style)
         ========================================== -->
    @if($categories->count() && (!isset($sections['categories']) || $sections['categories']->is_active))
        @php
            $subcatGradients = [
                'linear-gradient(135deg, #205258 0%, #1b454a 100%)',
                'linear-gradient(135deg, #F8931D 0%, #E07E0A 100%)',
                'linear-gradient(135deg, #C1539C 0%, #A04080 100%)',
                'linear-gradient(135deg, #6FC2A2 0%, #4DAA85 100%)',
                'linear-gradient(135deg, #7B8CDE 0%, #5A6BC7 100%)',
                'linear-gradient(135deg, #E86F6F 0%, #D04545 100%)',
            ];
        @endphp
        @foreach($categories as $rootCategory)
            @php
                // Filter out empty child categories (no products)
                $childCats = $rootCategory->children->where('is_active', true)->filter(fn($c) => $c->products_count > 0)->sortBy('position');
                if ($childCats->count() < 1) continue;
                $totalProducts = $rootCategory->products_count + $childCats->sum('products_count');
                $topCards = $childCats->take($childCats->count() >= 2 ? 2 : 1);
                $bottomCards = $childCats->slice($topCards->count())->take(4);

                // Static banner images for root categories
                $staticBanners = [
                    'luggage-bags' => asset('images/luggage.jpg'),
                    'office-supplies' => asset('images/office.jpg'),
                ];

                // Fallback image helper: use first product image if no category image
                $getCatImage = function($cat) {
                    if ($cat->image_url) return asset('storage/' . $cat->image_url);
                    $firstProduct = $cat->products->first();
                    return $firstProduct?->primary_image_url;
                };
            @endphp

            <section class="py-6 lg:py-10 bg-white">
                <div class="container mx-auto px-4">
                    <div class="section-header">
                        <h2 class="section-title">{{ $rootCategory->name }}</h2>
                        <a href="{{ route('category.show', $rootCategory) }}" class="view-all-link">
                            View All ({{ $totalProducts }})
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <div class="collage-collection">
                        {{-- TOP ROW: Banner + 2 Cards --}}
                        <div class="collage-collection__top">
                            <a href="{{ route('category.show', $rootCategory) }}" class="collage-collection__banner">
                                @php $rootImage = $staticBanners[$rootCategory->slug] ?? $getCatImage($rootCategory); @endphp
                                @if($rootImage)
                                    <img src="{{ $rootImage }}" alt="{{ $rootCategory->name }}" loading="lazy">
                                @else
                                    <div class="w-full h-full min-h-[320px] bg-gradient-to-br from-[#205258]/20 to-[#205258]/5"></div>
                                @endif
                                <div class="collage-collection__banner-text">
                                    <span>Shop For</span>
                                    <h2>{{ $rootCategory->name }}</h2>
                                </div>
                                <div class="collage-collection__banner-btn">
                                    <button class="collage-collection__btn">View all &rarr;</button>
                                </div>
                            </a>

                            <div class="collage-collection__top-cards">
                                @foreach($topCards as $child)
                                    <a href="{{ route('category.show', $child) }}" class="collage-collection__card">
                                        @php $childImage = $getCatImage($child); @endphp
                                        @if($childImage)
                                            <img src="{{ $childImage }}" alt="{{ $child->name }}" loading="lazy">
                                        @else
                                            <div style="background: {{ $subcatGradients[$loop->index % count($subcatGradients)] }}; width: 100%; aspect-ratio: 1/1; border-radius: var(--card-radius);"></div>
                                        @endif
                                        <div class="collage-collection__card-overlay"></div>
                                        <p class="collage-collection__label">{{ $child->name }} <span style="font-size: 11px; opacity: 0.8;">({{ $child->products_count }})</span></p>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- BOTTOM ROW: Up to 4 Cards --}}
                        @if($bottomCards->count())
                            <div class="collage-collection__bottom">
                                @foreach($bottomCards as $child)
                                    <a href="{{ route('category.show', $child) }}" class="collage-collection__card">
                                        @php $childImage = $getCatImage($child); @endphp
                                        @if($childImage)
                                            <img src="{{ $childImage }}" alt="{{ $child->name }}" loading="lazy">
                                        @else
                                            <div style="background: {{ $subcatGradients[($loop->index + 2) % count($subcatGradients)] }}; width: 100%; aspect-ratio: 1/1; border-radius: var(--card-radius);"></div>
                                        @endif
                                        <div class="collage-collection__card-overlay"></div>
                                        <p class="collage-collection__label">{{ $child->name }} <span style="font-size: 11px; opacity: 0.8;">({{ $child->products_count }})</span></p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach
    @endif

    <!-- ==========================================
         BESTSELLERS - Horizontal Slider
         ========================================== -->
    @if($bestsellers->count() && (!isset($sections['bestsellers']) || $sections['bestsellers']->is_active))
        <section class="py-8 lg:py-12 bg-white" style="background-color:#fefae0">
            <div class="container mx-auto px-4">
                <div class="section-header">
                    <h2 class="section-title">{{ $sections['bestsellers']->title ?? 'Bestsellers' }}</h2>
                    <a href="{{ route('bestsellers') }}" class="view-all-link">
                        View All
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="product-slider">
                    @foreach($bestsellers->take(10) as $product)
                        <div class="slide-item">
                            <x-product-card :product="$product" :compact="true" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         PRODUCT BANNER 1 (Configurable)
         ========================================== -->
    @if(isset($sections['product_banner_1']) && $sections['product_banner_1']->is_active && $sections['product_banner_1']->image_url)
        <section class="bg-white">
            <a href="{{ $sections['product_banner_1']->button_link ?? route('products.index') }}" class="block">
                <img src="{{ asset('storage/' . $sections['product_banner_1']->image_url) }}" alt="{{ $sections['product_banner_1']->title }}" class="w-full h-auto object-cover" loading="lazy">
            </a>
        </section>
    @endif

    <!-- ==========================================
         WHY CHOOSE US - Feature Grid
         ========================================== -->
    @if(isset($sections['benefits']) && $sections['benefits']->is_active && is_array($sections['benefits']->content))
    @php $benefitsSection = $sections['benefits']; @endphp
    <section class="features-section bg-white">
        <div class="container mx-auto px-4">
            <div class="features-header">
                <h2 class="features-heading">{{ $benefitsSection->title }}</h2>
                @if($benefitsSection->button_text)
                    <a href="{{ $benefitsSection->button_link ?? route('products.index') }}" class="view-all-link">
                        {{ $benefitsSection->button_text }}
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
            <div class="features-grid">
                @foreach($benefitsSection->content as $benefit)
                    <div class="feature-card">
                        <div class="feature-icon">
                            @include('partials.benefit-icon', ['icon' => $benefit['icon'] ?? 'default'])
                        </div>
                        <h3>{{ $benefit['title'] }}</h3>
                        <p>{{ $benefit['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ==========================================
         TODAY'S DEALS
         ========================================== -->
    @if($deals->count() && (!isset($sections['deals']) || $sections['deals']->is_active))
        <section class="deals-section bg-white" style="background-color:#fefae0">
            <div class="container mx-auto px-4">
                <div class="section-header">
                    <h2 class="section-title">{{ $sections['deals']->title ?? "Steal Deals" }}</h2>
                    <a href="{{ route('products.index') }}?on_sale=1" class="view-all-link">
                        View All
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="product-slider">
                    @foreach($deals->take(12) as $product)
                        <div class="slide-item">
                            <x-product-card :product="$product" :compact="true" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         PRODUCT BANNER 2 (Configurable)
         ========================================== -->
    @if(isset($sections['product_banner_2']) && $sections['product_banner_2']->is_active && $sections['product_banner_2']->image_url)
        <section class="bg-white">
            <a href="{{ $sections['product_banner_2']->button_link ?? route('products.index') }}" class="block">
                <img src="{{ asset('storage/' . $sections['product_banner_2']->image_url) }}" alt="{{ $sections['product_banner_2']->title }}" class="w-full h-auto object-cover" loading="lazy">
            </a>
        </section>
    @endif

    <!-- ==========================================
         PROMO BANNER (CTA)
         ========================================== -->
    @if(isset($sections['promo_banner']) && $sections['promo_banner']->is_active)
        @php $promo = $sections['promo_banner']; @endphp
        <section class="relative overflow-hidden" style="background-color: {{ $promo->background_color ?? '#205258' }};">
            @if($promo->image_url)
                <img src="{{ asset('storage/' . $promo->image_url) }}" alt="{{ $promo->title }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40"></div>
            @endif
            <div class="container mx-auto px-4 relative z-10 py-14 lg:py-20 text-center">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3" style="color: {{ $promo->text_color ?? '#ffffff' }};">{{ $promo->title }}</h2>
                @if($promo->subtitle)
                    <p class="text-base sm:text-lg mb-6 max-w-xl mx-auto" style="color: {{ $promo->text_color ?? '#ffffff' }}; opacity: 0.85;">{{ $promo->subtitle }}</p>
                @endif
                @if($promo->button_text)
                    <a href="{{ $promo->button_link ?? route('products.index') }}" class="inline-flex items-center gap-2 px-8 py-2 bg-white text-[#205258] rounded-full font-semibold text-sm hover:bg-neutral-100 transition-colors shadow-lg">
                        {{ $promo->button_text }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        </section>
    @endif

    <!-- ==========================================
         HAPPY CUSTOMERS / TESTIMONIALS
         ========================================== -->
    @if($testimonials->count() && (!isset($sections['testimonials']) || $sections['testimonials']->is_active))
        <section class="testimonial-section bg-white">
            <div class="container mx-auto px-4">
                <div class="testimonial-layout">
                    {{-- Static Title Card --}}
                    <div class="testimonial-title-card">
                        <h2>{{ $sections['testimonials']->title ?? 'Happy Customers' }}</h2>
                        <p>{{ $sections['testimonials']->subtitle ?? ($testimonials->count() . '+ reviews from happy customers') }}</p>
                    </div>

                    {{-- Scrollable Testimonial Carousel --}}
                    <div class="testimonial-carousel-wrap">
                        <div class="testimonial-carousel">
                            @foreach($testimonials as $testimonial)
                                <div class="testimonial-card">
                                    <div class="testimonial-stars">★★★★★</div>
                                    <p class="testimonial-text">"{{ Str::limit($testimonial->content, 120) }}"</p>
                                    <div class="testimonial-author">
                                        @if($testimonial->avatar_url)
                                            <img src="{{ asset('storage/' . $testimonial->avatar_url) }}" alt="{{ $testimonial->name }}" class="w-9 h-9 rounded-full object-cover">
                                        @else
                                            <div class="testimonial-avatar">{{ strtoupper(substr($testimonial->name, 0, 1)) }}</div>
                                        @endif
                                        <div>
                                            <div class="testimonial-name">{{ $testimonial->name }}</div>
                                            <div class="testimonial-label">Verified Buyer</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         NEW ARRIVALS GRID
         ========================================== -->
    @if($newArrivals->count() && (!isset($sections['new_arrivals']) || $sections['new_arrivals']->is_active))
        <section class="py-8 lg:py-12 bg-white" style="background-color:#fefae0">
            <div class="container mx-auto px-4">
                <div class="section-header">
                    <h2 class="section-title">{{ $sections['new_arrivals']->title ?? 'New Arrivals' }}</h2>
                    <a href="{{ route('new-arrivals') }}" class="view-all-link">
                        View All
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="product-slider">
                    @foreach($newArrivals->take(10) as $product)
                        <div class="slide-item">
                            <x-product-card :product="$product" :compact="true" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         NEWSLETTER SIGNUP
         ========================================== -->
    @php
        $nlSection = $sections['newsletter'] ?? null;
        $nlTitle = $nlSection->title ?? \App\Models\Setting::get('newsletter_heading', 'Get 20% Off Your First Order!');
        $nlSubtitle = $nlSection->subtitle ?? \App\Models\Setting::get('newsletter_subtitle', 'Sign up for our newsletter and receive exclusive deals, new arrivals, and shopping tips. Unsubscribe anytime.');
        $nlBtnText = $nlSection->button_text ?? 'Sign Up';
    @endphp
    <section class="newsletter">
        <div class="container mx-auto px-4">
            <h2>{{ $nlTitle }}</h2>
            <p>{{ $nlSubtitle }}</p>
            <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <input type="email" name="email" class="newsletter-input" placeholder="Email" required>
                <button type="submit" class="newsletter-btn">{{ $nlBtnText }}</button>
            </form>
        </div>
    </section>

    <!-- ==========================================
         TRUST BADGES + FAQ
         ========================================== -->
    <x-trust-badges />
    <x-faq-section />

</x-layouts.app>
