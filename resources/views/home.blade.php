<x-layouts.app>
    <x-slot name="title">{{ $siteSettings['site_name'] }} - {{ $siteSettings['site_tagline'] }}</x-slot>

    @push('meta')
        <meta name="description" content="{{ $siteSettings['site_tagline'] }} - Shop certified gold, diamond & silver jewellery online at {{ $siteSettings['site_name'] }}.">
        <link rel="canonical" href="{{ url('/') }}">
        <meta property="og:title" content="{{ $siteSettings['site_name'] }} - {{ $siteSettings['site_tagline'] }}">
        <meta property="og:description" content="Shop certified gold, diamond & silver jewellery online at {{ $siteSettings['site_name'] }}. Rings, necklaces, earrings, bangles & more.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        @if($siteSettings['site_logo'])
        <meta property="og:image" content="{{ asset('images/' . $siteSettings['site_logo']) }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $siteSettings['site_name'] }} - {{ $siteSettings['site_tagline'] }}">
        <meta name="twitter:description" content="Shop certified gold, diamond & silver jewellery online at {{ $siteSettings['site_name'] }}.">

        {{-- Organization + WebSite JSON-LD --}}
        <?php
        $homeSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => url('/') . '#organization',
                    'name' => $siteSettings['site_name'],
                    'url' => url('/'),
                    'logo' => ['@type' => 'ImageObject', 'url' => asset('images/musco-logo.png')],
                    'description' => $siteSettings['site_tagline'] . ' - Shop certified gold, diamond & silver jewellery online.',
                    'email' => 'support@musco.com',
                    'telephone' => '+919354567705',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressCountry' => 'IN',
                        'addressLocality' => 'Delhi',
                        'addressRegion' => 'DL',
                    ],
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'contactType' => 'customer service',
                        'telephone' => '+919354567705',
                        'email' => 'support@musco.com',
                        'url' => url('/contact'),
                        'availableLanguage' => ['English', 'Hindi'],
                    ],
                    'sameAs' => [
                        'https://www.instagram.com/musco',
                        'https://www.facebook.com/musco',
                    ],
                    'areaServed' => ['@type' => 'Country', 'name' => 'India'],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => url('/') . '#website',
                    'name' => $siteSettings['site_name'],
                    'url' => url('/'),
                    'publisher' => ['@id' => url('/') . '#organization'],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => ['@type' => 'EntryPoint', 'urlTemplate' => url('/products') . '?search={search_term_string}'],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ];
        ?>
        <script type="application/ld+json">{!! json_encode($homeSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
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
                <div class="relative bg-gradient-to-br from-[#B76E79] via-[#222222] to-[#B76E79] px-6 pt-8 pb-6 text-center overflow-hidden">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full"></div>
                    <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="relative inline-flex items-center justify-center w-14 h-14 bg-white/15 rounded-full mb-4 ring-4 ring-white/10">
                        <svg class="w-7 h-7 text-[#c29958]" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
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
                       class="inline-flex items-center justify-center gap-2 w-full py-2 bg-[#B76E79] hover:bg-[#222222] text-white text-sm font-bold rounded-xl shadow-lg transition-all hover:-translate-y-0.5">
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
    <style>
        .hero-slides { height: auto !important; }
        .hero-banner img { height: 100% !important; width: 100%; object-fit: cover; display: block; }
        .hero-slide { position: absolute; inset: 0; }
        .hero-banner::after, .hero-banner::before { display: none !important; }
        .hero-banner { border-bottom: none !important; margin-bottom: 0 !important; }
    </style>

    @if(isset($banners) && $banners->count())
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
                <button @click="prev()" class="hero-arrow hero-arrow--prev" aria-label="Previous">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="next()" class="hero-arrow hero-arrow--next" aria-label="Next">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="hero-dots">
                    <template x-for="(slide, index) in slides" :key="'dot-'+index">
                        <button @click="goTo(index)" class="hero-dot" :class="current === index ? 'active' : ''"></button>
                    </template>
                </div>
            </div>
        </section>
    @else
        {{-- DUMMY HERO BANNERS - Jewelry with model images --}}
        <section class="hero-banner"
                 x-data="{ current: 0, timer: null,
                    init() { this.timer = setInterval(() => this.current = (this.current + 1) % 3, 5000); },
                    goTo(i) { this.current = i; clearInterval(this.timer); this.init(); }
                 }">
            <div class="hero-slides">
                {{-- Slide 1: Pearl Collection - Text LEFT, model on right --}}
                <div x-show="current === 0" class="hero-slide relative">
                    <img src="{{ asset('images/banner1.jpg') }}" alt="Pearl Jewelry Collection" class="w-full h-full object-cover">
                    <div class="absolute inset-0 flex items-center">
                        <div class="px-8 sm:px-12 lg:px-20 max-w-2xl hero-text-animate">
                            <p class="hero-eyebrow text-xs sm:text-sm uppercase tracking-[0.2em] text-[#B76E79] mb-2 sm:mb-3 font-medium">Pearl Collection</p>
                            <h2 class="hero-heading text-2xl sm:text-4xl lg:text-5xl font-bold text-neutral-800 mb-2 sm:mb-4" style="font-family:'Playfair Display',Georgia,serif; letter-spacing:-0.01em;">Timeless Elegance</h2>
                            <p class="hero-subtext text-sm sm:text-base text-neutral-600 mb-4 sm:mb-6">Discover handcrafted pearl jewelry that tells your story</p>
                            <a href="{{ route('products.index') }}" class="hero-cta inline-flex items-center gap-2 px-6 sm:px-8 py-2.5 sm:py-3 bg-[#B76E79] text-white rounded-full font-semibold text-sm hover:bg-[#222222] transition-all shadow-lg">
                                Shop Now
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                {{-- Slide 2: Earrings - Text RIGHT, model on left --}}
                <div x-show="current === 1" x-cloak class="hero-slide relative">
                    <img src="{{ asset('images/banner2.jpg') }}" alt="Diamond Earrings Collection" class="w-full h-full object-cover">
                    <div class="absolute inset-0 flex items-center justify-end">
                        <div class="px-8 sm:px-12 lg:px-20 max-w-2xl text-right hero-text-animate">
                            <p class="hero-eyebrow text-xs sm:text-sm uppercase tracking-[0.2em] text-[#B76E79] mb-2 sm:mb-3 font-medium">Luxury Earrings</p>
                            <h2 class="hero-heading text-2xl sm:text-4xl lg:text-5xl font-bold text-neutral-800 mb-2 sm:mb-4" style="font-family:'Playfair Display',Georgia,serif; letter-spacing:-0.01em;">Flat 50% Off</h2>
                            <p class="hero-subtext text-sm sm:text-base text-neutral-600 mb-4 sm:mb-6">On all Anti-Tarnish Collections</p>
                            <a href="{{ route('products.index') }}?on_sale=1" class="hero-cta inline-flex items-center gap-2 px-6 sm:px-8 py-2.5 sm:py-3 bg-[#B76E79] text-white rounded-full font-semibold text-sm hover:bg-[#222222] transition-all shadow-lg">
                                Shop Deals
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                {{-- Slide 3: Rings & Pendants - Text LEFT, jewelry on right --}}
                <div x-show="current === 2" x-cloak class="hero-slide relative">
                    <img src="{{ asset('images/banner3.jpg') }}" alt="Diamond Ring Collection" class="w-full h-full object-cover">
                    <div class="absolute inset-0 flex items-center">
                        <div class="px-8 sm:px-12 lg:px-20 max-w-2xl hero-text-animate">
                            <p class="hero-eyebrow text-xs sm:text-sm uppercase tracking-[0.2em] text-[#B76E79] mb-2 sm:mb-3 font-medium">Just Launched</p>
                            <h2 class="hero-heading text-2xl sm:text-4xl lg:text-5xl font-bold text-neutral-800 mb-2 sm:mb-4" style="font-family:'Playfair Display',Georgia,serif; letter-spacing:-0.01em;">New Season, New Sparkle</h2>
                            <p class="hero-subtext text-sm sm:text-base text-neutral-600 mb-4 sm:mb-6">Explore our latest arrivals in gold & silver</p>
                            <a href="{{ route('new-arrivals') }}" class="hero-cta inline-flex items-center gap-2 px-6 sm:px-8 py-2.5 sm:py-3 bg-[#B76E79] text-white rounded-full font-semibold text-sm hover:bg-[#9a7209] transition-all shadow-lg">
                                New Arrivals
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="hero-dots">
                    <template x-for="i in 3" :key="'dot-'+i">
                        <button @click="goTo(i-1)" class="hero-dot" :class="current === (i-1) ? 'active' : ''"></button>
                    </template>
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         CATEGORY CAROUSEL - Square Tiles
         ========================================== -->
    @if(isset($carouselCategories) && $carouselCategories->count())
        <section class="luxury-category-section">
            <div class="container mx-auto px-4">
                <div class="luxury-category-scroll" data-reveal="up" data-reveal-stagger>
                    @foreach($carouselCategories->take(6) as $cat)
                        <a href="{{ route('category.show', $cat) }}" class="luxury-category-item group">
                            @php
                                $catImage = null;
                                if ($cat->image_url) { $catImage = asset('storage/' . $cat->image_url); }
                                elseif ($cat->products->first()?->primary_image_url) { $catImage = $cat->products->first()->primary_image_url; }
                            @endphp
                            <div class="luxury-category-circle">
                                @if($catImage)
                                    <img src="{{ $catImage }}" alt="{{ $cat->name }}" loading="lazy">
                                @else
                                    <svg class="w-7 h-7 text-[#B76E79]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="luxury-category-label">{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        {{-- DUMMY CATEGORIES - Jewelry with images --}}
        @php
            $dummyCats = [
                ['name' => 'Necklaces', 'img' => asset('images/dummy/cat-necklace.jpg')],
                ['name' => 'Earrings', 'img' => asset('images/dummy/cat-earrings.jpg')],
                ['name' => 'Bracelets', 'img' => asset('images/dummy/cat-bracelet.jpg')],
                ['name' => 'Rings', 'img' => asset('images/dummy/cat-ring.jpg')],
                ['name' => 'Hair Pins', 'img' => asset('images/dummy/product-1.jpg')],
                ['name' => 'Anklets', 'img' => asset('images/dummy/product-2.jpg')],
                ['name' => 'Mangalsutra', 'img' => asset('images/dummy/product-3.jpg')],
                ['name' => 'Pendants', 'img' => asset('images/dummy/product-4.jpg')],
            ];
        @endphp
        <section class="luxury-category-section">
            <div class="container mx-auto px-4">
                <div class="luxury-category-scroll" data-reveal="up" data-reveal-stagger>
                    @foreach($dummyCats as $dc)
                        <a href="{{ route('products.index') }}" class="luxury-category-item group">
                            <div class="luxury-category-circle">
                                <img src="{{ $dc['img'] }}" alt="{{ $dc['name'] }}" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            <span class="luxury-category-label">{{ $dc['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         CORANO-STYLE SERVICES BAR
         ========================================== -->
    <section class="musco-services" data-reveal="up">
        <div class="container mx-auto px-4">
            <div class="musco-services-grid">
                <div class="musco-service-item">
                    <div class="musco-service-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                    </div>
                    <div class="musco-service-text">
                        <h4>Free Shipping</h4>
                        <p>Free shipping on orders above ₹499</p>
                    </div>
                </div>
                <div class="musco-service-item">
                    <div class="musco-service-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                    </div>
                    <div class="musco-service-text">
                        <h4>Support 24/7</h4>
                        <p>Contact us 24 hours a day, 7 days</p>
                    </div>
                </div>
                <div class="musco-service-item">
                    <div class="musco-service-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                    </div>
                    <div class="musco-service-text">
                        <h4>7 Days Return</h4>
                        <p>Simply return it within 7 days</p>
                    </div>
                </div>
                <div class="musco-service-item">
                    <div class="musco-service-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <div class="musco-service-text">
                        <h4>100% Payment Secure</h4>
                        <p>We ensure secure payment</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         CATEGORY BANNERS (2x2 Grid)
         ========================================== -->
    <section class="musco-banners">
        <div class="container mx-auto px-4">
            <div class="musco-banners-grid">
                <a href="{{ route('products.index') }}?category=rings" class="musco-banner-item" data-reveal="scale">
                    <div class="musco-banner-img">
                        <img src="{{ asset('images/img1.jpg') }}" alt="Wedding Rings" loading="lazy">
                    </div>
                    <div class="musco-banner-content" style="right:0;left:auto;text-align:right;">
                        <span class="musco-banner-eyebrow">Beautiful</span>
                        <h3 class="musco-banner-title">Wedding<br>Rings</h3>
                        <span class="musco-banner-btn">Shop Now</span>
                    </div>
                </a>
                <a href="{{ route('products.index') }}?category=earrings" class="musco-banner-item" data-reveal="scale">
                    <div class="musco-banner-img">
                        <img src="{{ asset('images/img2.jpg') }}" alt="Earrings" loading="lazy">
                    </div>
                    <div class="musco-banner-content" style="right:0;left:auto;text-align:right;">
                        <span class="musco-banner-eyebrow">Tangerine Floral</span>
                        <h3 class="musco-banner-title">Earrings</h3>
                        <span class="musco-banner-btn">Shop Now</span>
                    </div>
                </a>
                <a href="{{ route('products.index') }}?category=necklaces" class="musco-banner-item" data-reveal="scale">
                    <div class="musco-banner-img">
                        <img src="{{ asset('images/img3.jpg') }}" alt="Pearl Necklaces" loading="lazy">
                    </div>
                    <div class="musco-banner-content" style="right:0;left:auto;text-align:right;">
                        <span class="musco-banner-eyebrow">New Arrivals</span>
                        <h3 class="musco-banner-title">Pearl<br>Necklaces</h3>
                        <span class="musco-banner-btn">Shop Now</span>
                    </div>
                </a>
                <a href="{{ route('products.index') }}?category=bracelets" class="musco-banner-item" data-reveal="scale">
                    <div class="musco-banner-img">
                        <img src="{{ asset('images/img4.jpg') }}" alt="Diamond Jewelry" loading="lazy">
                    </div>
                    <div class="musco-banner-content" style="right:0;left:auto;text-align:right;">
                        <span class="musco-banner-eyebrow">New Design</span>
                        <h3 class="musco-banner-title">Diamond<br>Jewelry</h3>
                        <span class="musco-banner-btn">Shop Now</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- ==========================================
         ANTI-TARNISH / QUALITY GUARANTEE SECTION
         ========================================== -->
    @if($featuredProducts->count())
        <section class="luxury-quality-section">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="luxury-quality-content" data-reveal="left">
                        <span class="luxury-quality-eyebrow">Premium Quality</span>
                        <h2 class="luxury-quality-title">Anti-Tarnish Collection</h2>
                        <p class="text-lg text-[#B76E79] font-medium mb-3" style="font-family:'Playfair Display',Georgia,serif;">Beauty That Doesn't Fade</p>
                        <p class="luxury-quality-desc">Our anti-tarnish jewelry is crafted to maintain its shine and beauty for years. Premium quality that stands the test of time.</p>
                        <a href="{{ route('products.index') }}" class="luxury-quality-btn">
                            Shop Anti-Tarnish
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <div>
                        <div class="product-slider" data-reveal="up">
                            @foreach($featuredProducts->take(6) as $product)
                                <div class="slide-item">
                                    <x-product-card :product="$product" :compact="true" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        {{-- DUMMY Anti-Tarnish Section --}}
        @php
            $dummyAntiTarnish = [
                ['name' => 'Pearl Drop Necklace', 'price' => '₹1,299', 'mrp' => '₹2,499', 'discount' => '48', 'rating' => 4.5, 'reviews' => 128, 'img' => asset('images/dummy/product-1.jpg')],
                ['name' => 'Gold Hoop Earrings', 'price' => '₹899', 'mrp' => '₹1,799', 'discount' => '50', 'rating' => 4.7, 'reviews' => 215, 'img' => asset('images/dummy/product-2.jpg')],
                ['name' => 'Diamond Tennis Bracelet', 'price' => '₹2,499', 'mrp' => '₹4,999', 'discount' => '50', 'rating' => 4.6, 'reviews' => 94, 'img' => asset('images/dummy/product-3.jpg')],
                ['name' => 'Emerald Statement Ring', 'price' => '₹1,599', 'mrp' => '₹2,999', 'discount' => '47', 'rating' => 4.4, 'reviews' => 67, 'img' => asset('images/dummy/product-4.jpg')],
            ];
        @endphp
        <section class="luxury-quality-section">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="luxury-quality-content" data-reveal="left">
                        <span class="luxury-quality-eyebrow">Premium Quality</span>
                        <h2 class="luxury-quality-title">Anti-Tarnish Collection</h2>
                        <p class="text-lg text-[#B76E79] font-medium mb-3" style="font-family:'Playfair Display',Georgia,serif;">Beauty That Doesn't Fade</p>
                        <p class="luxury-quality-desc">Our anti-tarnish jewelry is crafted to maintain its shine and beauty for years. Premium quality that stands the test of time.</p>
                        <a href="{{ route('products.index') }}" class="luxury-quality-btn">
                            Shop Anti-Tarnish
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <div>
                        <div class="product-slider" data-reveal="up">
                            @foreach($dummyAntiTarnish as $i => $dp)
                                <div class="slide-item">
                                    <div class="group shrink-0 w-full flex flex-col h-full">
                                        <a href="{{ route('products.index') }}" class="block relative">
                                            <div class="aspect-square rounded-lg overflow-hidden mb-2 bg-[#FAF7F5]">
                                                <img src="{{ $dp['img'] }}" alt="{{ $dp['name'] }}" class="w-full h-full object-cover" loading="lazy">
                                            </div>
                                            <span class="absolute top-2 left-2 bg-[#1346af] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ $dp['discount'] }}%</span>
                                        </a>
                                        <a href="{{ route('products.index') }}" class="block px-0.5">
                                            <h3 class="text-[13px] text-[#2b2b2b] line-clamp-2 mb-1 leading-snug hover:text-[#c29958] transition-colors" style="min-height: 2.5em;">{{ $dp['name'] }}</h3>
                                        </a>
                                        <div style="margin-top:auto;" class="px-0.5">
                                            <div class="flex items-center gap-1 mb-1">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <svg class="w-3.5 h-3.5 {{ $s <= floor($dp['rating']) ? 'text-[#B76E79]' : 'text-[#E0E0E0]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endfor
                                                <span class="text-[11px] text-[#747474]">({{ $dp['reviews'] }})</span>
                                            </div>
                                            <div class="flex items-baseline gap-1.5 flex-wrap mb-1">
                                                <span class="text-[15px] font-semibold text-[#2b2b2b]">{{ $dp['price'] }}</span>
                                                <span class="text-[11px] text-[#747474] line-through">{{ $dp['mrp'] }}</span>
                                            </div>
                                            <div style="height:16px;" class="mb-1">
                                                <p class="text-[11px] text-[#B76E79] font-medium">Save {{ $dp['discount'] }}%</p>
                                            </div>
                                            <button class="w-full py-2 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#222222] rounded-full transition-all shadow-sm hover:shadow-md tracking-wide uppercase" style="letter-spacing: 0.05em;">Add to Cart</button>
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
         SHOP BY RANGE - Price Tiers (4 columns)
         ========================================== -->
    @php
        $ranges = [
            ['label' => 'Under ₹99', 'price' => 99, 'img' => asset('images/shopyimage1.jpg')],
            ['label' => 'Under ₹199', 'price' => 199, 'img' => asset('images/shopyimage2.jpg')],
            ['label' => 'Under ₹299', 'price' => 299, 'img' => asset('images/shopyimage3.jpg')],
            ['label' => 'Under ₹499', 'price' => 499, 'img' => asset('images/shopyimage4.jpg')],
        ];
    @endphp
    <section class="luxury-range-section">
        <div class="container mx-auto px-4">
            <div class="luxury-section-header" data-reveal="up">
                <h2 class="luxury-section-title">Shop by Range</h2>
                <div class="luxury-section-line"></div>
            </div>
            <div class="luxury-range-grid" data-reveal="up" data-reveal-stagger>
                @foreach($ranges as $range)
                    <a href="{{ route('products.index') }}?max_price={{ $range['price'] }}" class="luxury-range-card group">
                        <div class="luxury-range-img">
                            <img src="{{ $range['img'] }}" alt="{{ $range['label'] }}" loading="lazy">
                            <div class="luxury-range-overlay">
                                <span class="luxury-range-label">{{ $range['label'] }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ==========================================
         FEATURED PRODUCTS - TABBED NEW ARRIVALS
         ========================================== -->
    @if($featuredProducts->count() && (!isset($sections['featured']) || $sections['featured']->is_active))
        <section class="luxury-product-section" x-data="{ activeTab: 'all' }">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">{{ $sections['featured']->title ?? 'New Arrivals' }}</h2>
                    <div class="luxury-section-line"></div>
                    <div class="luxury-tabs">
                        <button @click="activeTab = 'all'" class="luxury-tab" :class="activeTab === 'all' ? 'active' : ''">All</button>
                        <button @click="activeTab = 'necklaces'" class="luxury-tab" :class="activeTab === 'necklaces' ? 'active' : ''">Necklaces</button>
                        <button @click="activeTab = 'earrings'" class="luxury-tab" :class="activeTab === 'earrings' ? 'active' : ''">Earrings</button>
                        <button @click="activeTab = 'bracelets'" class="luxury-tab" :class="activeTab === 'bracelets' ? 'active' : ''">Bracelets</button>
                        <button @click="activeTab = 'rings'" class="luxury-tab" :class="activeTab === 'rings' ? 'active' : ''">Rings</button>
                    </div>
                    <a href="{{ route('products.index') }}" class="luxury-view-all">
                        View All
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                {{-- Show all products for all tabs when using real data --}}
                <div class="product-slider" data-reveal="up">
                    @foreach($featuredProducts->take(10) as $product)
                        <div class="slide-item">
                            <x-product-card :product="$product" :compact="true" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        {{-- DUMMY FEATURED PRODUCTS - Jewelry themed with tabs --}}
        @php
            $dummyImgs = [asset('images/dummy/product-1.jpg'), asset('images/dummy/product-2.jpg'), asset('images/dummy/product-3.jpg'), asset('images/dummy/product-4.jpg'), asset('images/dummy/cat-necklace.jpg'), asset('images/dummy/cat-earrings.jpg'), asset('images/dummy/cat-bracelet.jpg'), asset('images/dummy/cat-ring.jpg')];
            $dummyProducts = [
                ['name' => 'Pearl Drop Necklace', 'price' => '₹1,299', 'mrp' => '₹2,499', 'discount' => '48', 'rating' => 4.5, 'reviews' => 128, 'img' => $dummyImgs[0]],
                ['name' => 'Gold Hoop Earrings', 'price' => '₹899', 'mrp' => '₹1,799', 'discount' => '50', 'rating' => 4.3, 'reviews' => 86, 'img' => $dummyImgs[1]],
                ['name' => 'Diamond Tennis Bracelet', 'price' => '₹2,499', 'mrp' => '₹4,999', 'discount' => '50', 'rating' => 4.7, 'reviews' => 234, 'img' => $dummyImgs[2]],
                ['name' => 'Emerald Statement Ring', 'price' => '₹1,599', 'mrp' => '₹2,999', 'discount' => '47', 'rating' => 4.4, 'reviews' => 156, 'img' => $dummyImgs[3]],
                ['name' => 'Crystal Hair Pin Set', 'price' => '₹499', 'mrp' => '₹999', 'discount' => '50', 'rating' => 4.6, 'reviews' => 92, 'img' => $dummyImgs[4]],
                ['name' => 'Silver Charm Anklet', 'price' => '₹699', 'mrp' => '₹1,299', 'discount' => '46', 'rating' => 4.2, 'reviews' => 67, 'img' => $dummyImgs[5]],
                ['name' => 'Rose Gold Pendant', 'price' => '₹1,199', 'mrp' => '₹2,399', 'discount' => '50', 'rating' => 4.8, 'reviews' => 45, 'img' => $dummyImgs[6]],
                ['name' => 'Layered Chain Necklace', 'price' => '₹999', 'mrp' => '₹1,999', 'discount' => '50', 'rating' => 4.1, 'reviews' => 178, 'img' => $dummyImgs[7]],
            ];
        @endphp
        <section class="luxury-product-section" x-data="{ activeTab: 'all' }">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">New Arrivals</h2>
                    <div class="luxury-section-line"></div>
                    <div class="luxury-tabs">
                        <button @click="activeTab = 'all'" class="luxury-tab" :class="activeTab === 'all' ? 'active' : ''">All</button>
                        <button @click="activeTab = 'necklaces'" class="luxury-tab" :class="activeTab === 'necklaces' ? 'active' : ''">Necklaces</button>
                        <button @click="activeTab = 'earrings'" class="luxury-tab" :class="activeTab === 'earrings' ? 'active' : ''">Earrings</button>
                        <button @click="activeTab = 'bracelets'" class="luxury-tab" :class="activeTab === 'bracelets' ? 'active' : ''">Bracelets</button>
                        <button @click="activeTab = 'rings'" class="luxury-tab" :class="activeTab === 'rings' ? 'active' : ''">Rings</button>
                    </div>
                    <a href="{{ route('products.index') }}" class="luxury-view-all">
                        View All
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="product-slider" data-reveal="up">
                    @foreach($dummyProducts as $i => $dp)
                        <div class="slide-item">
                            <div class="group shrink-0 w-full flex flex-col h-full">
                                <a href="{{ route('products.index') }}" class="block relative">
                                    <div class="aspect-square rounded-lg overflow-hidden mb-2 bg-[#FAF7F5]">
                                        <img src="{{ $dp['img'] }}" alt="{{ $dp['name'] }}" class="w-full h-full object-cover" loading="lazy">
                                    </div>
                                    <span class="absolute top-2 left-2 bg-[#1346af] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ $dp['discount'] }}%</span>
                                </a>
                                <a href="{{ route('products.index') }}" class="block px-0.5">
                                    <h3 class="text-[13px] text-[#2b2b2b] line-clamp-2 mb-1 leading-snug hover:text-[#c29958] transition-colors" style="min-height: 2.5em;">{{ $dp['name'] }}</h3>
                                </a>
                                <div style="margin-top:auto;" class="px-0.5">
                                    <div class="flex items-center gap-1 mb-1">
                                        @for($s = 1; $s <= 5; $s++)
                                            <svg class="w-3.5 h-3.5 {{ $s <= floor($dp['rating']) ? 'text-[#B76E79]' : 'text-[#E0E0E0]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-[11px] text-[#747474]">({{ $dp['reviews'] }})</span>
                                    </div>
                                    <div class="flex items-baseline gap-1.5 flex-wrap mb-1">
                                        <span class="text-[15px] font-semibold text-[#2b2b2b]">{{ $dp['price'] }}</span>
                                        <span class="text-[11px] text-[#747474] line-through">{{ $dp['mrp'] }}</span>
                                    </div>
                                    <div style="height:16px;" class="mb-1">
                                        <p class="text-[11px] text-[#B76E79] font-medium">Save {{ $dp['discount'] }}%</p>
                                    </div>
                                    <button class="w-full py-2 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#222222] rounded-full transition-all shadow-sm hover:shadow-md tracking-wide uppercase" style="letter-spacing: 0.05em;">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         LUXURY CTA BANNER
         ========================================== -->
    @if(isset($sections['product_banner_1']) && $sections['product_banner_1']->is_active && $sections['product_banner_1']->image_url)
        <section class="luxury-promo-banner">
            <a href="{{ $sections['product_banner_1']->button_link ?? route('products.index') }}" class="block">
                <img src="{{ asset('storage/' . $sections['product_banner_1']->image_url) }}" alt="{{ $sections['product_banner_1']->title }}" class="w-full h-auto object-cover" loading="lazy">
            </a>
        </section>
    @else
        <section class="luxury-cta-banner relative overflow-hidden" style="min-height: 300px;">
            <img src="{{ asset('images/dummy/model-1.jpg') }}" alt="MusCo Premium Jewelry" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-transparent"></div>
            <div class="luxury-cta-content relative z-10" data-reveal="up">
                <span class="luxury-cta-eyebrow" style="color:#B76E79;">Exclusive Collection</span>
                <h2 class="luxury-cta-heading" style="color:#fff;">Discover Our Premium Range</h2>
                <p class="luxury-cta-text" style="color:rgba(255,255,255,0.8);">Curated selections for the discerning customer. Quality that speaks for itself.</p>
                <a href="{{ route('products.index') }}" class="luxury-cta-btn">Explore Now</a>
            </div>
        </section>
    @endif

    <!-- ==========================================
         SHOP OUR REELS (hidden until Instagram is configured)
         ========================================== -->
    {{-- <x-instagram-reels /> --}}

    <!-- ==========================================
         CATEGORY COLLECTIONS (Collage Style)
         ========================================== -->
    @if($categories->count() && (!isset($sections['categories']) || $sections['categories']->is_active))
        @php
            $subcatGradients = [
                'linear-gradient(135deg, #B76E79 0%, #222222 100%)',
                'linear-gradient(135deg, #8B6914 0%, #6B5010 100%)',
                'linear-gradient(135deg, #6B4C5E 0%, #503848 100%)',
                'linear-gradient(135deg, #4A6B5E 0%, #385248 100%)',
                'linear-gradient(135deg, #4A5A6B 0%, #384858 100%)',
                'linear-gradient(135deg, #7A4A4A 0%, #5E3636 100%)',
            ];
        @endphp
        @foreach($categories as $rootCategory)
            @php
                $childCats = $rootCategory->children->where('is_active', true)->filter(fn($c) => $c->products_count > 0)->sortBy('position');
                if ($childCats->count() < 1) continue;
                $totalProducts = $rootCategory->products_count + $childCats->sum('products_count');
                $topCards = $childCats->take($childCats->count() >= 2 ? 2 : 1);
                $bottomCards = $childCats->slice($topCards->count())->take(4);
                $staticBanners = [];
                $getCatImage = function($cat) {
                    if ($cat->image_url) return asset('storage/' . $cat->image_url);
                    return $cat->products->first()?->primary_image_url;
                };
            @endphp
            <section class="luxury-product-section">
                <div class="container mx-auto px-4">
                    <div class="luxury-section-header" data-reveal="up">
                        <h2 class="luxury-section-title">{{ $rootCategory->name }}</h2>
                        <div class="luxury-section-line"></div>
                        <a href="{{ route('category.show', $rootCategory) }}" class="luxury-view-all">View All ({{ $totalProducts }}) <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
                    </div>
                    <div class="collage-collection" data-reveal="up">
                        <div class="collage-collection__top">
                            <a href="{{ route('category.show', $rootCategory) }}" class="collage-collection__banner">
                                @php $rootImage = $staticBanners[$rootCategory->slug] ?? $getCatImage($rootCategory); @endphp
                                @if($rootImage)<img src="{{ $rootImage }}" alt="{{ $rootCategory->name }}" loading="lazy">
                                @else<div class="w-full h-full min-h-[320px] bg-gradient-to-br from-[#B76E79]/20 to-[#B76E79]/5"></div>@endif
                                <div class="collage-collection__banner-text"><span>Shop For</span><h2>{{ $rootCategory->name }}</h2></div>
                                <div class="collage-collection__banner-btn"><button class="collage-collection__btn">View all &rarr;</button></div>
                            </a>
                            <div class="collage-collection__top-cards">
                                @foreach($topCards as $child)
                                    <a href="{{ route('category.show', $child) }}" class="collage-collection__card">
                                        @php $childImage = $getCatImage($child); @endphp
                                        @if($childImage)<img src="{{ $childImage }}" alt="{{ $child->name }}" loading="lazy">
                                        @else<div style="background: {{ $subcatGradients[$loop->index % count($subcatGradients)] }}; width: 100%; aspect-ratio: 1/1; border-radius: var(--card-radius);"></div>@endif
                                        <div class="collage-collection__card-overlay"></div>
                                        <p class="collage-collection__label">{{ $child->name }} <span style="font-size: 11px; opacity: 0.8;">({{ $child->products_count }})</span></p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @if($bottomCards->count())
                        <div class="collage-collection__bottom">
                            @foreach($bottomCards as $child)
                                <a href="{{ route('category.show', $child) }}" class="collage-collection__card">
                                    @php $childImage = $getCatImage($child); @endphp
                                    @if($childImage)<img src="{{ $childImage }}" alt="{{ $child->name }}" loading="lazy">
                                    @else<div style="background: {{ $subcatGradients[($loop->index + 2) % count($subcatGradients)] }}; width: 100%; aspect-ratio: 1/1; border-radius: var(--card-radius);"></div>@endif
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
         BESTSELLERS
         ========================================== -->
    @if($bestsellers->count() && (!isset($sections['bestsellers']) || $sections['bestsellers']->is_active))
        <section class="luxury-product-section luxury-product-section--warm">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">{{ $sections['bestsellers']->title ?? 'Bestsellers' }}</h2>
                    <div class="luxury-section-line"></div>
                    <a href="{{ route('bestsellers') }}" class="luxury-view-all">View All <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="product-slider" data-reveal="up">
                    @foreach($bestsellers->take(10) as $product)
                        <div class="slide-item"><x-product-card :product="$product" :compact="true" /></div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        {{-- DUMMY BESTSELLERS - Jewelry themed --}}
        <section class="luxury-product-section luxury-product-section--warm">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">Bestsellers</h2>
                    <div class="luxury-section-line"></div>
                    <a href="{{ route('products.index') }}" class="luxury-view-all">View All <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="product-slider" data-reveal="up">
                    @php
                        $bestImgs = [asset('images/dummy/product-4.jpg'), asset('images/dummy/product-3.jpg'), asset('images/dummy/product-2.jpg'), asset('images/dummy/product-1.jpg'), asset('images/dummy/cat-ring.jpg'), asset('images/dummy/cat-bracelet.jpg')];
                        $dummyBest = [
                            ['name' => 'Solitaire Diamond Ring', 'price' => '₹3,499', 'mrp' => '₹6,999', 'discount' => '50', 'rating' => 4.8, 'reviews' => 312, 'img' => $bestImgs[0]],
                            ['name' => 'Pearl Stud Earrings', 'price' => '₹1,199', 'mrp' => '₹1,999', 'discount' => '40', 'rating' => 4.6, 'reviews' => 456, 'img' => $bestImgs[1]],
                            ['name' => 'Gold Mangalsutra Set', 'price' => '₹2,699', 'mrp' => '₹4,999', 'discount' => '46', 'rating' => 4.5, 'reviews' => 189, 'img' => $bestImgs[2]],
                            ['name' => 'Ruby Pendant Necklace', 'price' => '₹1,899', 'mrp' => '₹3,499', 'discount' => '46', 'rating' => 4.7, 'reviews' => 134, 'img' => $bestImgs[3]],
                            ['name' => 'Cubic Zirconia Bracelet', 'price' => '₹999', 'mrp' => '₹1,999', 'discount' => '50', 'rating' => 4.4, 'reviews' => 267, 'img' => $bestImgs[4]],
                            ['name' => 'Silver Toe Ring Set', 'price' => '₹599', 'mrp' => '₹1,199', 'discount' => '50', 'rating' => 4.3, 'reviews' => 98, 'img' => $bestImgs[5]],
                        ];
                    @endphp
                    @foreach($dummyBest as $i => $dp)
                        <div class="slide-item">
                            <div class="group shrink-0 w-full flex flex-col h-full">
                                <a href="{{ route('products.index') }}" class="block relative">
                                    <div class="aspect-square rounded-lg overflow-hidden mb-2 bg-[#FAF7F5]">
                                        <img src="{{ $dp['img'] }}" alt="{{ $dp['name'] }}" class="w-full h-full object-cover" loading="lazy">
                                    </div>
                                    <span class="absolute top-2 left-2 bg-[#1346af] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ $dp['discount'] }}%</span>
                                </a>
                                <a href="{{ route('products.index') }}" class="block px-0.5">
                                    <h3 class="text-[13px] text-[#2b2b2b] line-clamp-2 mb-1 leading-snug hover:text-[#c29958] transition-colors" style="min-height: 2.5em;">{{ $dp['name'] }}</h3>
                                </a>
                                <div style="margin-top:auto;" class="px-0.5">
                                    <div class="flex items-center gap-1 mb-1">
                                        @for($s = 1; $s <= 5; $s++)
                                            <svg class="w-3.5 h-3.5 {{ $s <= floor($dp['rating']) ? 'text-[#B76E79]' : 'text-[#E0E0E0]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-[11px] text-[#747474]">({{ $dp['reviews'] }})</span>
                                    </div>
                                    <div class="flex items-baseline gap-1.5 flex-wrap mb-1">
                                        <span class="text-[15px] font-semibold text-[#2b2b2b]">{{ $dp['price'] }}</span>
                                        <span class="text-[11px] text-[#747474] line-through">{{ $dp['mrp'] }}</span>
                                    </div>
                                    <div style="height:16px;" class="mb-1"><p class="text-[11px] text-[#B76E79] font-medium">Save {{ $dp['discount'] }}%</p></div>
                                    <button class="w-full py-2 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#222222] rounded-full transition-all shadow-sm hover:shadow-md tracking-wide uppercase" style="letter-spacing: 0.05em;">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         PRODUCT BANNER 2
         ========================================== -->
    @if(isset($sections['product_banner_2']) && $sections['product_banner_2']->is_active && $sections['product_banner_2']->image_url)
        <section class="luxury-promo-banner">
            <a href="{{ $sections['product_banner_2']->button_link ?? route('products.index') }}" class="block">
                <img src="{{ asset('storage/' . $sections['product_banner_2']->image_url) }}" alt="{{ $sections['product_banner_2']->title }}" class="w-full h-auto object-cover" loading="lazy">
            </a>
        </section>
    @endif

    <!-- ==========================================
         WHY CHOOSE US
         ========================================== -->
    @if(isset($sections['benefits']) && $sections['benefits']->is_active && is_array($sections['benefits']->content))
        @php $benefitsSection = $sections['benefits']; @endphp
        <section class="features-section">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">{{ $benefitsSection->title }}</h2>
                    <div class="luxury-section-line"></div>
                </div>
                <div class="features-grid" data-reveal-stagger>
                    @foreach($benefitsSection->content as $benefit)
                        <div class="feature-card">
                            <div class="feature-icon">@include('partials.benefit-icon', ['icon' => $benefit['icon'] ?? 'default'])</div>
                            <h3>{{ $benefit['title'] }}</h3>
                            <p>{{ $benefit['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        {{-- DUMMY BENEFITS - Jewelry specific --}}
        <section class="features-section">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">Why Choose Us</h2>
                    <div class="luxury-section-line"></div>
                </div>
                <div class="features-grid" data-reveal-stagger>
                    @php
                        $dummyBenefits = [
                            ['title' => 'Anti-Tarnish Guarantee', 'desc' => 'All jewelry coated with premium anti-tarnish layer for lasting shine', 'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'],
                            ['title' => 'Hallmark Certified', 'desc' => 'Every piece is BIS hallmark certified for purity assurance', 'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>'],
                            ['title' => 'Free Shipping', 'desc' => 'Complimentary delivery on all orders above ₹499', 'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>'],
                            ['title' => 'Easy 7-Day Returns', 'desc' => 'Hassle-free returns within 7 days of delivery', 'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'],
                            ['title' => 'Secure Payments', 'desc' => 'Multiple payment options including UPI, cards & COD', 'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'],
                            ['title' => 'Gift Wrapping', 'desc' => 'Complimentary premium gift wrapping on all orders', 'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>'],
                        ];
                    @endphp
                    @foreach($dummyBenefits as $b)
                        <div class="feature-card">
                            <div class="feature-icon">{!! $b['icon'] !!}</div>
                            <h3>{{ $b['title'] }}</h3>
                            <p>{{ $b['desc'] }}</p>
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
        <section class="luxury-product-section luxury-product-section--warm">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">{{ $sections['deals']->title ?? "Steal Deals" }}</h2>
                    <div class="luxury-section-line"></div>
                    <a href="{{ route('products.index') }}?on_sale=1" class="luxury-view-all">View All <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="product-slider" data-reveal="up">
                    @foreach($deals->take(12) as $product)
                        <div class="slide-item"><x-product-card :product="$product" :compact="true" /></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         PROMO BANNER (CTA)
         ========================================== -->
    @if(isset($sections['promo_banner']) && $sections['promo_banner']->is_active)
        @php $promo = $sections['promo_banner']; @endphp
        <section class="relative overflow-hidden" style="background-color: {{ $promo->background_color ?? '#B76E79' }};">
            @if($promo->image_url)
                <img src="{{ asset('storage/' . $promo->image_url) }}" alt="{{ $promo->title }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40"></div>
            @endif
            <div class="container mx-auto px-4 relative z-10 py-16 lg:py-24 text-center">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 tracking-wide" style="color: {{ $promo->text_color ?? '#ffffff' }};">{{ $promo->title }}</h2>
                @if($promo->subtitle)<p class="text-base sm:text-lg mb-8 max-w-xl mx-auto" style="color: {{ $promo->text_color ?? '#ffffff' }}; opacity: 0.85;">{{ $promo->subtitle }}</p>@endif
                @if($promo->button_text)
                    <a href="{{ $promo->button_link ?? route('products.index') }}" class="luxury-btn-pill">{{ $promo->button_text }} <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                @endif
            </div>
        </section>
    @endif

    <!-- ==========================================
         HAPPY CUSTOMERS / TESTIMONIALS
         ========================================== -->
    @if($testimonials->count() && (!isset($sections['testimonials']) || $sections['testimonials']->is_active))
        <section class="testimonial-section">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up" style="margin-bottom: 32px;">
                    <h2 class="luxury-section-title">{{ $sections['testimonials']->title ?? 'Happy Customers' }}</h2>
                    <div class="luxury-section-line"></div>
                </div>
                <div class="testimonial-layout" data-reveal="up">
                    <div class="testimonial-title-card">
                        <div class="testimonial-title-stars">★★★★★</div>
                        <h2>{{ $testimonials->count() }}+</h2>
                        <p>{{ $sections['testimonials']->subtitle ?? 'Reviews from happy customers' }}</p>
                    </div>
                    <div class="testimonial-carousel-wrap">
                        <div class="testimonial-carousel">
                            @foreach($testimonials as $testimonial)
                                <div class="testimonial-card">
                                    <div class="testimonial-stars">★★★★★</div>
                                    <p class="testimonial-text">"{{ Str::limit($testimonial->content, 120) }}"</p>
                                    <div class="testimonial-author">
                                        @if($testimonial->avatar_url)<img src="{{ asset('storage/' . $testimonial->avatar_url) }}" alt="{{ $testimonial->name }}" class="w-9 h-9 rounded-full object-cover">
                                        @else<div class="testimonial-avatar">{{ strtoupper(substr($testimonial->name, 0, 1)) }}</div>@endif
                                        <div><div class="testimonial-name">{{ $testimonial->name }}</div><div class="testimonial-label">Verified Buyer</div></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        {{-- DUMMY TESTIMONIALS - Indian jewelry buyers --}}
        @php
            $dummyReviews = [
                ['name' => 'Priya Sharma', 'text' => 'Absolutely love my anti-tarnish necklace! It still looks brand new after 6 months of daily wear. The quality is exceptional for the price.', 'init' => 'P'],
                ['name' => 'Ritu Kapoor', 'text' => 'Ordered the pearl earrings for my wedding. They looked stunning and received so many compliments. Will definitely order again!', 'init' => 'R'],
                ['name' => 'Ananya Mehta', 'text' => 'The mangalsutra set I received was beautifully crafted. Packaging was premium and delivery was quick. Highly recommend!', 'init' => 'A'],
                ['name' => 'Deepika Nair', 'text' => 'Best jewelry shopping experience online! The bracelet matches exactly what was shown in the pictures. True to description.', 'init' => 'D'],
                ['name' => 'Kavita Joshi', 'text' => 'Gifted the rose gold pendant to my sister. She absolutely loved it! The anti-tarnish coating really makes a difference.', 'init' => 'K'],
            ];
        @endphp
        <section class="testimonial-section">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up" style="margin-bottom: 32px;">
                    <h2 class="luxury-section-title">Happy Customers</h2>
                    <div class="luxury-section-line"></div>
                </div>
                <div class="testimonial-layout" data-reveal="up">
                    <div class="testimonial-title-card">
                        <div class="testimonial-title-stars">★★★★★</div>
                        <h2>500+</h2>
                        <p>Reviews from happy customers</p>
                    </div>
                    <div class="testimonial-carousel-wrap">
                        <div class="testimonial-carousel">
                            @foreach($dummyReviews as $dr)
                                <div class="testimonial-card">
                                    <div class="testimonial-stars">★★★★★</div>
                                    <p class="testimonial-text">"{{ $dr['text'] }}"</p>
                                    <div class="testimonial-author">
                                        <div class="testimonial-avatar">{{ $dr['init'] }}</div>
                                        <div><div class="testimonial-name">{{ $dr['name'] }}</div><div class="testimonial-label">Verified Buyer</div></div>
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
         NEW ARRIVALS
         ========================================== -->
    @if($newArrivals->count() && (!isset($sections['new_arrivals']) || $sections['new_arrivals']->is_active))
        <section class="luxury-product-section luxury-product-section--warm">
            <div class="container mx-auto px-4">
                <div class="luxury-section-header" data-reveal="up">
                    <h2 class="luxury-section-title">{{ $sections['new_arrivals']->title ?? 'New Arrivals' }}</h2>
                    <div class="luxury-section-line"></div>
                    <a href="{{ route('new-arrivals') }}" class="luxury-view-all">View All <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="product-slider" data-reveal="up">
                    @foreach($newArrivals->take(10) as $product)
                        <div class="slide-item"><x-product-card :product="$product" :compact="true" /></div>
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
        <div class="container mx-auto px-4" data-reveal="up">
            <h2>{{ $nlTitle }}</h2>
            <p>{{ $nlSubtitle }}</p>
            <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address" required>
                <button type="submit" class="newsletter-btn">{{ $nlBtnText }}</button>
            </form>
        </div>
    </section>

    <!-- ==========================================
         RECENTLY VIEWED + TRUST + FAQ
         ========================================== -->
    <div class="container mx-auto px-4">
        <x-recently-viewed />
    </div>
    <x-trust-badges />
    <x-faq-section />

    {{-- Scroll Reveal Observer (MusCo-style animations) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var reveals = document.querySelectorAll('[data-reveal], [data-reveal-stagger]');
            if (!reveals.length) return;

            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                reveals.forEach(function(el) { el.classList.add('revealed'); });
                return;
            }

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

            reveals.forEach(function(el) { observer.observe(el); });
        });
    </script>

</x-layouts.app>
