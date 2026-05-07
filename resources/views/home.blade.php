<x-layouts.app>
    <x-slot name="title">Affordable Anti-Tarnish & Waterproof Jewellery UK</x-slot>

    @push('meta')
        <meta name="description" content="Shop affordable anti-tarnish & waterproof jewellery online in the UK. Gold-plated rings, necklaces, earrings, bracelets & pendant sets. Stainless steel, skin-friendly, won't fade or tarnish. Free UK delivery.">
        <meta name="keywords" content="anti tarnish jewellery, waterproof jewellery, anti tarnish jewellery UK, waterproof jewellery UK, affordable jewellery UK, gold plated jewellery, stainless steel jewellery, anti tarnish rings, anti tarnish necklaces, anti tarnish earrings, waterproof rings, waterproof necklaces, waterproof earrings, waterproof bracelets, jewellery that doesn't tarnish, jewellery that doesn't turn green, skin friendly jewellery, hypoallergenic jewellery UK, fashion jewellery online UK, cheap jewellery online UK, buy jewellery online UK">
        <link rel="canonical" href="{{ url('/') }}">
        <meta property="og:title" content="Affordable Anti-Tarnish & Waterproof Jewellery UK">
        <meta property="og:description" content="Shop 100+ anti-tarnish & waterproof jewellery designs. Gold-plated rings, necklaces, earrings & bracelets. Free UK delivery.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        @if($siteSettings['site_logo'])
        <meta property="og:image" content="{{ asset('images/' . $siteSettings['site_logo']) }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Affordable Anti-Tarnish & Waterproof Jewellery UK">
        <meta name="twitter:description" content="100+ anti-tarnish & waterproof jewellery designs. Rings, necklaces, earrings & more. Free UK delivery.">

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
                    'logo' => ['@type' => 'ImageObject', 'url' => asset('images/mus-removebg-preview.png')],
                    'description' => $siteSettings['site_tagline'] . ' - Shop affordable anti-tarnish and waterproof jewellery online in the UK. Free delivery.',
                    'email' => 'hello@trendymus.com',
                    'telephone' => '+447459914080',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => '182, High Street North',
                        'addressLocality' => 'East Ham',
                        'addressRegion' => 'London',
                        'postalCode' => 'E6 2JA',
                        'addressCountry' => 'GB',
                    ],
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'contactType' => 'customer service',
                        'telephone' => '+447459914080',
                        'email' => 'hello@trendymus.com',
                        'url' => url('/contact'),
                        'availableLanguage' => ['English'],
                    ],
                    'sameAs' => [
                        'https://www.instagram.com/trendimus',
                        'https://www.facebook.com/trendimus',
                    ],
                    'areaServed' => ['@type' => 'Country', 'name' => 'United Kingdom'],
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
        @if($banners->count())
        <link rel="preload" as="image" href="{{ asset('storage/' . $banners->first()->image_url) }}" fetchpriority="high">
        @endif
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
                <div class="relative bg-gradient-to-br from-[#202a40] via-[#222222] to-[#202a40] px-6 pt-8 pb-6 text-center overflow-hidden">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full"></div>
                    <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="relative inline-flex items-center justify-center w-14 h-14 bg-white/15 rounded-full mb-4 ring-4 ring-white/10">
                        <svg class="w-7 h-7 text-[#506282]" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
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
                       class="inline-flex items-center justify-center gap-2 w-full py-2 bg-[#202a40] text-white text-sm font-bold rounded-xl shadow-lg transition-all hover:-translate-y-0.5">
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


    <!-- SEO H1 - visible, keyword-rich -->
    <div class="bg-[#202a40] text-white text-center py-2.5 px-4">
        <h1 class="text-sm sm:text-base font-medium tracking-wide">
            Affordable Anti-Tarnish &amp; Waterproof Jewellery &#8212; <span class="font-semibold">Free UK Delivery</span>
        </h1>
    </div>

    <!-- ==========================================
         HERO BANNER SLIDER
         ========================================== -->
    @if($banners->count())
    <section class="hero-banner"
             x-data="{
                current: 0,
                total: {{ $banners->count() }},
                timer: null,
                init() { this.startTimer(); },
                startTimer() { this.timer = setInterval(() => this.next(), 5000); },
                next() { this.current = (this.current + 1) % this.total; },
                prev() { this.current = (this.current - 1 + this.total) % this.total; },
                goTo(i) { this.current = i; clearInterval(this.timer); this.startTimer(); }
             }">
        <div class="hero-slides">
            @foreach($banners as $i => $banner)
                <a href="{{ $banner->link ?: route('products.index') }}"
                   x-show="current === {{ $i }}"
                   x-transition:enter="transition-opacity ease-out duration-700"
                   x-transition:enter-start="opacity-0"
                   x-transition:enter-end="opacity-100"
                   x-transition:leave="transition-opacity ease-in duration-400"
                   x-transition:leave-start="opacity-100"
                   x-transition:leave-end="opacity-0"
                   class="hero-slide" style="display:block;">
                    <img src="{{ asset('storage/' . $banner->image_url) }}" alt="{{ $banner->title ?: 'Collection' }}" style="width:100%;height:100%;object-fit:cover;object-position:center;display:block;" {!! $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' !!}>
                </a>
            @endforeach
            <button @click="prev()" class="hero-arrow hero-arrow--prev" aria-label="Previous">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="next()" class="hero-arrow hero-arrow--next" aria-label="Next">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <div class="hero-dots">
                @foreach($banners as $i => $banner)
                    <button @click="goTo({{ $i }})" class="hero-dot" :class="current === {{ $i }} ? 'active' : ''"></button>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- SHOP BY BRAND - Hidden/Removed --}}

    <!-- ==========================================
         TRUST BADGES BAR (Giva-style)
         ========================================== -->
    <section class="trust-badges-bar" data-reveal="up">
        <div class="container mx-auto px-4">
            <div class="trust-badges-scroll scrollbar-hide">
                <div class="trust-badge-item">
                    <div class="trust-badge-icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                    </div>
                    <div class="trust-badge-text">
                        <span class="trust-badge-title">Free Shipping</span>
                        <span class="trust-badge-desc">On orders above £30</span>
                    </div>
                </div>
                <div class="trust-badge-divider"></div>
                <div class="trust-badge-item">
                    <div class="trust-badge-icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <div class="trust-badge-text">
                        <span class="trust-badge-title">Anti-Tarnish</span>
                        <span class="trust-badge-desc">Guaranteed quality</span>
                    </div>
                </div>
                <div class="trust-badge-divider"></div>
                <div class="trust-badge-item">
                    <div class="trust-badge-icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                    </div>
                    <div class="trust-badge-text">
                        <span class="trust-badge-title">Easy Returns</span>
                        <span class="trust-badge-desc">7-day hassle-free</span>
                    </div>
                </div>
                <div class="trust-badge-divider"></div>
                <div class="trust-badge-item">
                    <div class="trust-badge-icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div class="trust-badge-text">
                        <span class="trust-badge-title">Secure Payment</span>
                        <span class="trust-badge-desc">100% protected</span>
                    </div>
                </div>
                <div class="trust-badge-divider"></div>
                <div class="trust-badge-item">
                    <div class="trust-badge-icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div class="trust-badge-text">
                        <span class="trust-badge-title">Certified</span>
                        <span class="trust-badge-desc">BIS hallmarked</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         CATEGORY BANNERS (2x2 Grid)
         ========================================== -->
    <style>
        .banner-cards-grid { display:grid; grid-template-columns:1fr; gap:16px; }
        @media(min-width:640px){ .banner-cards-grid { grid-template-columns:1fr 1fr; } }
        .banner-card { position:relative; display:block; overflow:hidden; border-radius:16px; text-decoration:none; transition:box-shadow 0.3s ease,transform 0.3s ease; }
        .banner-card:hover { transform:translateY(-3px); box-shadow:0 16px 40px rgba(0,0,0,0.25); }
        .banner-card img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; display:block; transition:transform 0.6s ease; }
        .banner-card:hover img { transform:scale(1.07); }
        .banner-card-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.35) 45%, rgba(0,0,0,0.08) 100%); pointer-events:none; }
        .banner-card-content { position:absolute; left:0; right:0; bottom:0; padding:20px 24px 22px; }
        .banner-card-eyebrow { display:block; font-size:9px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:rgba(255,255,255,0.7); margin-bottom:5px; }
        .banner-card-title { display:block; font-size:clamp(1rem,1.8vw,1.4rem); font-weight:700; color:#fff; line-height:1.25; margin-bottom:14px; font-family:'Playfair Display',Georgia,serif; }
        .banner-card-btn { display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:600; color:#fff; border:1.5px solid rgba(255,255,255,0.75); border-radius:6px; padding:6px 16px; letter-spacing:0.06em; transition:background 0.2s,border-color 0.2s; }
        .banner-card:hover .banner-card-btn { background:rgba(255,255,255,0.18); border-color:#fff; }
    </style>
    <section style="padding:40px 20px 48px;">
        <div style="max-width:1400px;margin:0 auto;">
            <div style="text-align:center;margin-bottom:28px;">
                <p style="font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#9b8c7a;margin-bottom:8px;">Explore</p>
                <h2 style="font-size:clamp(1.4rem,3vw,2rem);font-weight:700;color:#1a1a1a;font-family:'Playfair Display',Georgia,serif;line-height:1.2;">Shop by Category</h2>
            </div>

            <div class="banner-cards-grid">
                @php
                $eyebrows = ['New Collection', 'Trending Now', 'New Arrivals', 'Best Sellers'];
                $gradients = [
                    'linear-gradient(135deg,#202a40 0%,#2d3a55 100%)',
                    'linear-gradient(135deg,#2d3a55 0%,#1a2235 100%)',
                    'linear-gradient(135deg,#1a2235 0%,#202a40 100%)',
                    'linear-gradient(135deg,#253047 0%,#2d3a55 100%)',
                ];
                @endphp

                @forelse($bannerCategories as $i => $cat)
                @php
                $rawImg = $cat->image_url;
                if ($rawImg) {
                    if (!str_starts_with($rawImg, 'http')) {
                        $rawImg = preg_replace('#^storage/#', '', ltrim($rawImg, '/'));
                        $bannerImg = asset('storage/' . $rawImg);
                    } else {
                        $bannerImg = $rawImg;
                    }
                } else {
                    $bannerImg = $cat->products->first()?->primaryImage?->url;
                }
                @endphp
                <a href="{{ route('category.show', $cat->slug) }}"
                   class="banner-card"
                   style="aspect-ratio:16/9;background:{{ $gradients[$i % 4] }};">
                    @if($bannerImg)
                    <img src="{{ $bannerImg }}" alt="{{ $cat->name }}" loading="lazy">
                    @endif
                    <div class="banner-card-overlay"></div>
                    <div class="banner-card-content">
                        <span class="banner-card-eyebrow">{{ $eyebrows[$i] ?? 'Featured' }}</span>
                        <span class="banner-card-title">{{ $cat->name }}</span>
                        <span class="banner-card-btn">
                            Shop Now
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
                @empty
                @endforelse

            </div>
        </div>
    </section>

    <!-- ==========================================
         SPOTLIGHT CATEGORY SECTION
         ========================================== -->
    @if($spotlightCategory && $spotlightProducts->count())
        <section class="luxury-quality-section">
            <div class="container mx-auto px-4">
                {{-- Section header --}}
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8" data-reveal="up">
                    <div>
                        <span class="luxury-quality-eyebrow">Premium Quality</span>
                        <h2 class="luxury-quality-title" style="margin-bottom:0;">{{ $spotlightCategory->name }}</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="pendant-prev pc-nav-btn" aria-label="Previous">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button class="pendant-next pc-nav-btn" aria-label="Next">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                        <a href="{{ route('category.show', $spotlightCategory->slug) }}" class="luxury-quality-btn" style="padding:10px 24px;font-size:12px;">
                            Shop All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Swiper --}}
                <div class="swiper pendant-swiper" data-reveal="up">
                    <div class="swiper-wrapper">
                        @foreach($spotlightProducts as $product)
                            <div class="swiper-slide">
                                <x-product-card :product="$product" :compact="true" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================
         SHOP BY RANGE - Dynamic Price Tiers
         ========================================== -->
    @if($priceRanges->count())
    <section class="luxury-range-section">
        <div class="container mx-auto px-4">
            <div class="luxury-section-header" data-reveal="up">
                <h2 class="luxury-section-title">Shop by Range</h2>
                <div class="luxury-section-line"></div>
            </div>
            <div class="luxury-range-grid" data-reveal="up" data-reveal-stagger>
                @foreach($priceRanges as $range)
                    <a href="{{ route('products.index', ['min_price' => $range['min'], 'max_price' => $range['max']]) }}"
                       class="luxury-range-card group">
                        <div class="luxury-range-img" style="aspect-ratio:1/1;overflow:hidden">
                            @if($range['img'])
                            <img src="{{ $range['img'] }}" alt="{{ $range['label'] }}" loading="lazy" style="width:100%;height:100%;object-fit:cover">
                            @endif
                            <div class="luxury-range-overlay">
                                <span class="luxury-range-label">{{ $range['label'] }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ==========================================
         FEATURED PRODUCTS - TABBED NEW ARRIVALS (Dynamic + Carousel)
         ========================================== -->
    @if($featuredProducts->count())
        <section class="luxury-product-section" data-reveal="up">
            <div class="container mx-auto px-4">
                <div class="pc-section-header">
                    <div class="luxury-section-header" style="margin-bottom:0;flex:1;">
                        <h2 class="luxury-section-title">{{ $sections['featured']->title ?? 'New Arrivals' }}</h2>
                        <div class="luxury-section-line"></div>
                    </div>
                    <div class="pc-nav-arrows">
                        <button class="pc-nav-btn" onclick="pcScroll(this,-1)" aria-label="Previous">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button class="pc-nav-btn" onclick="pcScroll(this,1)" aria-label="Next">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="pc-glass-grid" data-reveal="up">
                    @foreach($featuredProducts->take(8) as $product)
                        <x-product-card :product="$product" :compact="true" />
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
                'linear-gradient(135deg, #202a40 0%, #222222 100%)',
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

                $getCatImage = function($cat) {
                    if ($cat->image_url) return asset('storage/' . $cat->image_url);
                    $product = $cat->products->first();
                    if ($product && $product->primaryImage) return $product->primary_image_url;
                    foreach ($cat->children ?? [] as $child) {
                        if ($child->image_url) return asset('storage/' . $child->image_url);
                        $childProduct = $child->products->first();
                        if ($childProduct && $childProduct->primaryImage) return $childProduct->primary_image_url;
                    }
                    return null;
                };

                // Collect all banner images from child categories
                $bannerImages = [];
                foreach ($childCats as $child) {
                    $img = $getCatImage($child);
                    if ($img) $bannerImages[] = $img;
                }
                if (empty($bannerImages)) {
                    $fallback = $getCatImage($rootCategory);
                    if ($fallback) $bannerImages[] = $fallback;
                }
                $bannerImagesJson = json_encode(array_values(array_unique($bannerImages)));
            @endphp
            <section class="luxury-product-section">
                <div class="container mx-auto px-4">
                    <div class="luxury-section-header" data-reveal="up">
                        <h2 class="luxury-section-title">{{ $rootCategory->name }}</h2>
                        <div class="luxury-section-line"></div>
                    </div>
                    <div class="collage-collection" data-reveal="up">
                        <div class="collage-collection__top">
                            <a href="{{ route('categories.show', $rootCategory) }}" class="collage-collection__banner"
                               x-data="{
                                   images: {{ $bannerImagesJson }},
                                   current: 0,
                                   init() {
                                       if (this.images.length > 1) {
                                           setInterval(() => { this.current = (this.current + 1) % this.images.length; }, 3000);
                                       }
                                   }
                               }">
                                @if(!empty($bannerImages))
                                    <template x-for="(img, i) in images" :key="i">
                                        <img :src="img"
                                             alt="{{ $rootCategory->name }}"
                                             :style="'position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;transition:opacity 0.8s ease;opacity:' + (current === i ? '1' : '0')"
                                             loading="lazy">
                                    </template>
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#202a40]/20 to-[#202a40]/5"></div>
                                @endif
                                <div class="collage-collection__banner-text"><span>Shop For</span><h2>{{ $rootCategory->name }}</h2></div>
                                <div class="collage-collection__banner-btn"><button class="collage-collection__btn">View all &rarr;</button></div>
                            </a>
                            <div class="collage-collection__top-cards">
                                @foreach($topCards as $child)
                                    <a href="{{ route('categories.show', $child) }}" class="collage-collection__card">
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
                                <a href="{{ route('categories.show', $child) }}" class="collage-collection__card">
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
                <div class="pc-section-header" data-reveal="up">
                    <div class="luxury-section-header" style="margin-bottom:0;flex:1;">
                        <h2 class="luxury-section-title">{{ $sections['bestsellers']->title ?? 'Bestsellers' }}</h2>
                        <div class="luxury-section-line"></div>
                    </div>
                    <div class="pc-nav-arrows">
                        <button class="pc-nav-btn" onclick="pcScroll(this,-1)" aria-label="Previous">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button class="pc-nav-btn" onclick="pcScroll(this,1)" aria-label="Next">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="pc-glass-grid" data-reveal="fade">
                    @foreach($bestsellers->take(8) as $product)
                        <x-product-card :product="$product" :compact="true" />
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
    @endif

    <!-- ==========================================
         TODAY'S DEALS
         ========================================== -->
    @if($deals->count() && (!isset($sections['deals']) || $sections['deals']->is_active))
        <section class="luxury-product-section luxury-product-section--warm">
            <div class="container mx-auto px-4">
                <div class="pc-section-header" data-reveal="up">
                    <div class="luxury-section-header" style="margin-bottom:0;flex:1;">
                        <h2 class="luxury-section-title">{{ $sections['deals']->title ?? "Steal Deals" }}</h2>
                        <div class="luxury-section-line"></div>
                    </div>
                    <div class="pc-nav-arrows">
                        <button class="pc-nav-btn" onclick="pcScroll(this,-1)" aria-label="Previous">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button class="pc-nav-btn" onclick="pcScroll(this,1)" aria-label="Next">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="pc-glass-grid" data-reveal="fade">
                    @foreach($deals->take(8) as $product)
                        <x-product-card :product="$product" :compact="true" />
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
        <section class="relative overflow-hidden" style="background-color: {{ $promo->background_color ?? '#202a40' }};">
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
                <div class="luxury-section-header" data-reveal="up" style="margin-bottom: 24px;">
                    <h2 class="luxury-section-title">{{ $sections['testimonials']->title ?? 'Happy Customers' }}</h2>
                    <div class="luxury-section-line"></div>
                </div>
                <div class="testimonial-carousel-wrap" data-reveal="fade">
                    <button class="testimonial-arrow testimonial-arrow--prev" aria-label="Previous">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <div class="testimonial-grid">
                        @foreach($testimonials->take(6) as $testimonial)
                            <div class="testimonial-card">
                                <div class="testimonial-stars">★★★★★</div>
                                <p class="testimonial-text">"{{ $testimonial->content }}"</p>
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
                    <button class="testimonial-arrow testimonial-arrow--next" aria-label="Next">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
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
                <div class="pc-section-header" data-reveal="up">
                    <div class="luxury-section-header" style="margin-bottom:0;flex:1;">
                        <h2 class="luxury-section-title">{{ $sections['new_arrivals']->title ?? 'New Arrivals' }}</h2>
                        <div class="luxury-section-line"></div>
                    </div>
                    <div class="pc-nav-arrows">
                        <button class="pc-nav-btn" onclick="pcScroll(this,-1)" aria-label="Previous">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button class="pc-nav-btn" onclick="pcScroll(this,1)" aria-label="Next">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="pc-glass-grid" data-reveal="fade">
                    @foreach($newArrivals->take(8) as $product)
                        <x-product-card :product="$product" :compact="true" />
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
    <section class="newsletter-v2" data-reveal="up">
        <div class="container mx-auto px-4">
            <div class="newsletter-v2-inner">
                <div class="newsletter-v2-content">
                    <h2>{{ $nlTitle }}</h2>
                    <p>{{ $nlSubtitle }}</p>
                </div>
                <div x-data="{
                        email:'', loading:false, done:false, message:'', error:'',
                        submit() {
                            this.loading=true; this.error='';
                            fetch('{{ route('newsletter.subscribe') }}', {
                                method:'POST',
                                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                                body:JSON.stringify({email:this.email})
                            })
                            .then(r=>r.json())
                            .then(d=>{ if(d.success){ this.message=d.message; this.done=true; } else { this.error=d.message||'Something went wrong.'; } })
                            .catch(()=>{ this.error='Something went wrong. Please try again.'; })
                            .finally(()=>{ this.loading=false; })
                        }
                    }" class="newsletter-v2-form-wrap">

                    {{-- Success message (inline, hidden until submit) --}}
                    <div x-show="done" style="display:none"
                         class="newsletter-v2-success"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <svg style="width:20px;height:20px;flex-shrink:0;color:#202a40;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="message"></span>
                    </div>

                    {{-- Form (hidden after success) --}}
                    <form class="newsletter-v2-form" x-show="!done" @submit.prevent="submit()">
                        <div class="newsletter-v2-input-wrap">
                            <input type="email" x-model="email" placeholder="Enter your email address" required>
                            <button type="submit" :disabled="loading" x-text="loading ? 'Subscribing...' : '{{ $nlBtnText }}'"></button>
                        </div>
                        <p x-show="error" x-text="error" style="display:none" class="text-white/80 text-xs mt-1"></p>
                    </form>

                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         RECENTLY VIEWED + TRUST + FAQ
         ========================================== -->
    <div class="container mx-auto px-4">
        <x-recently-viewed />
    </div>
    <x-trust-badges />
    <!-- ==========================================
         SEO CONTENT SECTION (keyword-rich, crawlable)
         ========================================== -->
    <section class="bg-neutral-50 border-t border-neutral-200 py-10 sm:py-14" data-reveal="up">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-xl sm:text-2xl font-bold text-neutral-900 mb-4" style="font-family:'Playfair Display',Georgia,serif;">
                    Why Shop Anti-Tarnish Jewellery at Trendymus?
                </h2>
                <div class="prose prose-sm text-neutral-600 max-w-none" style="line-height:1.8;">
                    <p>
                        <strong>Trendymus</strong> is your destination for <strong>affordable anti-tarnish jewellery</strong> in the UK.
                        Every piece in our collection is crafted from premium <strong>stainless steel</strong> with a lasting
                        <strong>gold-plated</strong> or <strong>silver-tone finish</strong> that won't fade, tarnish, or irritate your skin.
                        Whether you're looking for everyday <strong>waterproof jewellery</strong> or a statement piece for a special occasion,
                        we've got 100+ designs to choose from.
                    </p>
                    <p>
                        Browse our curated categories including <a href="{{ route('categories.show', 'rings') }}" class="text-[#202a40] underline hover:no-underline">rings</a>,
                        <a href="{{ route('categories.show', 'necklaces') }}" class="text-[#202a40] underline hover:no-underline">necklaces</a>,
                        <a href="{{ route('categories.show', 'earrings') }}" class="text-[#202a40] underline hover:no-underline">earrings</a>,
                        <a href="{{ route('categories.show', 'bracelets') }}" class="text-[#202a40] underline hover:no-underline">bracelets</a>,
                        <a href="{{ route('categories.show', 'pendant-sets') }}" class="text-[#202a40] underline hover:no-underline">pendant sets</a>,
                        <a href="{{ route('categories.show', 'bangles') }}" class="text-[#202a40] underline hover:no-underline">bangles</a>, and
                        <a href="{{ route('categories.show', 'anklets') }}" class="text-[#202a40] underline hover:no-underline">anklets</a>.
                        From <strong>crystal earrings</strong> and <strong>charm bracelets</strong> to elegant
                        <strong>pendant necklaces</strong> and <strong>statement rings</strong> &mdash; every piece is
                        designed for modern women who want luxury without the luxury price tag.
                    </p>
                    <p>
                        All orders ship with <strong>free UK delivery</strong>, secure checkout via PayPal and Stripe,
                        and a hassle-free returns policy. Our <strong>fashion jewellery</strong> makes the perfect gift &mdash;
                        shop our <a href="{{ route('bestsellers') }}" class="text-[#202a40] underline hover:no-underline">bestsellers</a>
                        or discover the latest <a href="{{ route('new-arrivals') }}" class="text-[#202a40] underline hover:no-underline">new arrivals</a>.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8">
                    <div class="text-center p-4 bg-white rounded-xl border border-neutral-100">
                        <div class="text-2xl font-bold text-[#202a40]">100+</div>
                        <div class="text-xs text-neutral-500 mt-1">Unique Designs</div>
                    </div>
                    <div class="text-center p-4 bg-white rounded-xl border border-neutral-100">
                        <div class="text-2xl font-bold text-[#202a40]">Anti-Tarnish</div>
                        <div class="text-xs text-neutral-500 mt-1">Guaranteed Quality</div>
                    </div>
                    <div class="text-center p-4 bg-white rounded-xl border border-neutral-100">
                        <div class="text-2xl font-bold text-[#202a40]">Free</div>
                        <div class="text-xs text-neutral-500 mt-1">UK Delivery</div>
                    </div>
                    <div class="text-center p-4 bg-white rounded-xl border border-neutral-100">
                        <div class="text-2xl font-bold text-[#202a40]">Waterproof</div>
                        <div class="text-xs text-neutral-500 mt-1">Skin-Friendly</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-faq-section />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.pslider-wrap').forEach(function (wrap) {
                var track = wrap.querySelector('.pslider-track');
                var prevBtn = wrap.querySelector('.pslider-arrow--prev');
                var nextBtn = wrap.querySelector('.pslider-arrow--next');
                if (!track || !prevBtn || !nextBtn) return;

                function itemWidth() {
                    var item = track.querySelector('.pslider-item');
                    if (!item) return 200;
                    return item.offsetWidth + parseInt(getComputedStyle(track).gap || 12);
                }
                function updateArrows() {
                    prevBtn.disabled = track.scrollLeft <= 4;
                    nextBtn.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 4;
                }
                prevBtn.addEventListener('click', function () {
                    track.scrollBy({ left: -itemWidth() * 6, behavior: 'smooth' });
                });
                nextBtn.addEventListener('click', function () {
                    track.scrollBy({ left: itemWidth() * 6, behavior: 'smooth' });
                });
                track.addEventListener('scroll', updateArrows, { passive: true });
                updateArrows();
            });
        });
    </script>

    {{-- Testimonial Carousel Arrows --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var wrap = document.querySelector('.testimonial-carousel-wrap');
            if (!wrap) return;
            var grid = wrap.querySelector('.testimonial-grid');
            var prevBtn = wrap.querySelector('.testimonial-arrow--prev');
            var nextBtn = wrap.querySelector('.testimonial-arrow--next');

            function cardWidth() {
                var card = grid.querySelector('.testimonial-card');
                if (!card) return 320;
                return card.offsetWidth + parseInt(getComputedStyle(grid).gap || 16);
            }

            function updateArrows() {
                prevBtn.disabled = grid.scrollLeft <= 4;
                nextBtn.disabled = grid.scrollLeft >= grid.scrollWidth - grid.clientWidth - 4;
            }

            prevBtn.addEventListener('click', function () {
                grid.scrollBy({ left: -cardWidth(), behavior: 'smooth' });
            });
            nextBtn.addEventListener('click', function () {
                grid.scrollBy({ left: cardWidth(), behavior: 'smooth' });
            });

            grid.addEventListener('scroll', updateArrows, { passive: true });
            updateArrows();
        });
    </script>

    {{-- Scroll Reveal Observer (Trendymus-style animations) --}}

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
