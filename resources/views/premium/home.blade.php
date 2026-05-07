@extends('premium.layout')

@section('namespace', 'home')

@section('content')

{{-- ============================================================
     1. HERO SECTION
     ============================================================ --}}
<section
    class="relative h-screen min-h-[640px] flex items-center justify-center overflow-hidden bg-[#0D0D0D]"
    aria-label="Hero"
>
    {{-- Background video --}}
    <video
        data-hero-video
        autoplay
        muted
        loop
        playsinline
        class="absolute inset-0 w-full h-full object-cover opacity-30"
    >
        <source src="https://cdn.coverr.co/videos/coverr-jewelry-and-gems-3/1080p.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    {{-- Gradient overlay --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/20 to-[#0D0D0D]"></div>

    {{-- Hero content --}}
    <div class="relative z-10 text-center px-6 max-w-4xl mx-auto">
        {{-- Eyebrow --}}
        <p class="text-[#202a40] text-xs tracking-[0.35em] uppercase font-medium mb-6 opacity-0 animate-[fadeInUp_0.8s_0.2s_ease_forwards]">
            New Collection 2025
        </p>

        {{-- Headline --}}
        <h1
            data-split="chars"
            class="text-[#FAFAF8] font-serif text-5xl sm:text-6xl lg:text-8xl leading-[1.05] tracking-tight mb-6 opacity-0 animate-[fadeInUp_0.8s_0.5s_ease_forwards]"
        >
            Every Story<br>Deserves Gold
        </h1>

        {{-- Subtext --}}
        <p
            data-split="words"
            class="text-[#FAFAF8]/70 text-lg sm:text-xl max-w-xl mx-auto mb-10 leading-relaxed opacity-0 animate-[fadeInUp_0.8s_0.8s_ease_forwards]"
        >
            Handcrafted rose gold &amp; gold jewelry that tells your most intimate stories. BIS hallmarked. Made with love in India.
        </p>

        {{-- CTAs --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 opacity-0 animate-[fadeInUp_0.8s_1s_ease_forwards]">
            <a
                href="#"
                data-magnetic
                class="group relative inline-flex items-center justify-center px-9 py-4 bg-[#202a40] text-white text-sm font-medium tracking-widest uppercase overflow-hidden transition-all duration-500 hover:bg-[#C9A96E] hover:shadow-[0_0_40px_rgba(183,110,121,0.4)]"
            >
                <span class="relative z-10">Shop Collection</span>
                <span class="absolute inset-0 translate-y-full bg-[#C9A96E] transition-transform duration-500 group-hover:translate-y-0"></span>
            </a>

            <a
                href="#"
                class="inline-flex items-center gap-3 px-9 py-4 border border-[#FAFAF8]/30 text-[#FAFAF8] text-sm font-medium tracking-widest uppercase transition-all duration-300 hover:border-[#C9A96E] hover:text-[#C9A96E]"
            >
                <span class="flex h-8 w-8 items-center justify-center rounded-full border border-current">
                    <svg class="w-3 h-3 translate-x-0.5" viewBox="0 0 10 12" fill="currentColor">
                        <path d="M0 0l10 6-10 6V0z"/>
                    </svg>
                </span>
                Watch Film
            </a>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-[#FAFAF8]/40 animate-bounce">
        <span class="text-[10px] tracking-[0.2em] uppercase">Scroll</span>
        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M3 6l5 5 5-5"/>
        </svg>
    </div>
</section>

{{-- ============================================================
     2. MARQUEE STRIP
     ============================================================ --}}
<section class="bg-[#111111] py-4 overflow-hidden border-y border-[#202a40]/20" aria-label="Brand highlights">
    <div class="flex whitespace-nowrap">
        <div class="flex animate-[scroll-left_28s_linear_infinite] shrink-0">
            @for ($i = 0; $i < 4; $i++)
                <span class="text-[#202a40] text-xs font-medium tracking-[0.2em] uppercase px-2">
                    Free Shipping
                    <span class="text-[#C9A96E] mx-3">·</span>
                    Anti-Tarnish
                    <span class="text-[#C9A96E] mx-3">·</span>
                    7-Day Returns
                    <span class="text-[#C9A96E] mx-3">·</span>
                    BIS Hallmarked
                    <span class="text-[#C9A96E] mx-3">·</span>
                    Made in India
                    <span class="text-[#C9A96E] mx-3">·</span>
                </span>
            @endfor
        </div>
        <div class="flex animate-[scroll-left_28s_linear_infinite] shrink-0" aria-hidden="true">
            @for ($i = 0; $i < 4; $i++)
                <span class="text-[#202a40] text-xs font-medium tracking-[0.2em] uppercase px-2">
                    Free Shipping
                    <span class="text-[#C9A96E] mx-3">·</span>
                    Anti-Tarnish
                    <span class="text-[#C9A96E] mx-3">·</span>
                    7-Day Returns
                    <span class="text-[#C9A96E] mx-3">·</span>
                    BIS Hallmarked
                    <span class="text-[#C9A96E] mx-3">·</span>
                    Made in India
                    <span class="text-[#C9A96E] mx-3">·</span>
                </span>
            @endfor
        </div>
    </div>
</section>

{{-- ============================================================
     3. FEATURED CATEGORIES
     ============================================================ --}}
<section class="bg-[#FAFAF8] py-24 lg:py-32" aria-label="Featured categories">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-14">
            <p class="text-[#202a40] text-xs tracking-[0.3em] uppercase font-medium mb-3">Explore</p>
            <h2
                data-split="words"
                class="font-serif text-[#111111] text-4xl sm:text-5xl lg:text-6xl leading-tight tracking-tight mb-6"
            >
                Shop by Category
            </h2>
            <div class="mx-auto w-16 h-px bg-gradient-to-r from-transparent via-[#C9A96E] to-transparent"></div>
        </div>

        {{-- Bento grid --}}
        <div
            data-gsap="stagger-grid"
            class="grid grid-cols-2 lg:grid-cols-3 gap-4"
        >
            {{-- Card 1 — Large (Rings) --}}
            <a
                href="#"
                data-gsap="stagger-item"
                class="group relative col-span-2 lg:col-span-1 lg:row-span-2 overflow-hidden bg-[#222222] min-h-[320px] lg:min-h-[560px]"
            >
                <img
                    src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=800&q=80"
                    alt="Rings"
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6">
                    <p class="text-[#202a40] text-xs tracking-[0.25em] uppercase mb-1">Explore</p>
                    <h3 class="text-[#FAFAF8] font-serif text-3xl leading-tight">Rings</h3>
                    <span class="inline-flex items-center gap-2 mt-3 text-[#C9A96E] text-xs tracking-widest uppercase opacity-0 -translate-y-1 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0">
                        View All
                        <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M2 10L10 2M10 2H4M10 2v6"/>
                        </svg>
                    </span>
                </div>
            </a>

            {{-- Card 2 — Necklaces --}}
            <a
                href="#"
                data-gsap="stagger-item"
                class="group relative overflow-hidden bg-[#222222] min-h-[200px] lg:min-h-[270px]"
            >
                <img
                    src="https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=700&q=80"
                    alt="Necklaces"
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-5">
                    <h3 class="text-[#FAFAF8] font-serif text-2xl">Necklaces</h3>
                </div>
            </a>

            {{-- Card 3 — Earrings --}}
            <a
                href="#"
                data-gsap="stagger-item"
                class="group relative overflow-hidden bg-[#222222] min-h-[200px] lg:min-h-[270px]"
            >
                <img
                    src="https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=700&q=80"
                    alt="Earrings"
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-5">
                    <h3 class="text-[#FAFAF8] font-serif text-2xl">Earrings</h3>
                </div>
            </a>

            {{-- Card 4 — Bangles --}}
            <a
                href="#"
                data-gsap="stagger-item"
                class="group relative overflow-hidden bg-[#222222] min-h-[200px] lg:min-h-[270px]"
            >
                <img
                    src="https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=700&q=80"
                    alt="Bangles"
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-5">
                    <h3 class="text-[#FAFAF8] font-serif text-2xl">Bangles</h3>
                </div>
            </a>

            {{-- Card 5 — Sets --}}
            <a
                href="#"
                data-gsap="stagger-item"
                class="group relative overflow-hidden bg-[#222222] min-h-[200px] lg:min-h-[270px]"
            >
                <img
                    src="https://images.unsplash.com/photo-1573408301185-9519f94815b8?w=700&q=80"
                    alt="Sets"
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-5">
                    <h3 class="text-[#FAFAF8] font-serif text-2xl">Sets</h3>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- ============================================================
     4. FEATURED PRODUCTS SLIDER
     ============================================================ --}}
<section
    data-gsap="fade-up"
    class="bg-white py-24 lg:py-32"
    aria-label="Featured products"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Section header --}}
        <div class="flex items-end justify-between mb-12">
            <div>
                <p class="text-[#202a40] text-xs tracking-[0.3em] uppercase font-medium mb-3">Handpicked</p>
                <h2 class="font-serif text-[#111111] text-4xl sm:text-5xl leading-tight tracking-tight">
                    Featured Pieces
                </h2>
            </div>
            <a
                href="#"
                class="hidden sm:inline-flex items-center gap-2 text-[#111111] text-sm font-medium tracking-widest uppercase border-b border-[#111111] pb-0.5 hover:text-[#202a40] hover:border-[#202a40] transition-colors duration-300"
            >
                View All
                <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M2 10L10 2M10 2H4M10 2v6"/>
                </svg>
            </a>
        </div>

        {{-- Swiper wrapper --}}
        <div class="relative">
            <div class="swiper swiper-products overflow-hidden">
                <div class="swiper-wrapper">

                    {{-- Product 1 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=600&q=80"
                                    alt="Rose Gold Solitaire Ring"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                                <span class="absolute top-3 left-3 bg-[#202a40] text-white text-[10px] tracking-widest uppercase px-2 py-1">New</span>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Rose Gold</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">Solitaire Diamond Ring</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-[#111111]/40 text-[10px] ml-1">(128)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£8,499</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    {{-- Product 2 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=600&q=80"
                                    alt="Gold Layered Necklace"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Gold</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">Layered Chain Necklace</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-[#111111]/40 text-[10px] ml-1">(84)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£5,999</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    {{-- Product 3 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=600&q=80"
                                    alt="Rose Gold Drop Earrings"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                                <span class="absolute top-3 left-3 bg-[#C9A96E] text-white text-[10px] tracking-widest uppercase px-2 py-1">Bestseller</span>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Rose Gold</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">Floral Drop Earrings</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-[#111111]/40 text-[10px] ml-1">(211)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£3,299</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    {{-- Product 4 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=600&q=80"
                                    alt="Gold Bangles Set"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Gold</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">Classic Bangle Set of 4</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 4; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <svg class="w-3 h-3 text-[#111111]/20 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-[#111111]/40 text-[10px] ml-1">(67)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£12,999</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    {{-- Product 5 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1573408301185-9519f94815b8?w=600&q=80"
                                    alt="Bridal Jewellery Set"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                                <span class="absolute top-3 left-3 bg-[#202a40] text-white text-[10px] tracking-widest uppercase px-2 py-1">Bridal</span>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Rose Gold</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">Bridal Complete Set</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-[#111111]/40 text-[10px] ml-1">(42)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£24,999</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    {{-- Product 6 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=600&q=80"
                                    alt="Gold Pendant"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Gold</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">Om Pendant Necklace</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-[#111111]/40 text-[10px] ml-1">(156)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£2,499</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    {{-- Product 7 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1506630448388-4e683c67ddb0?w=600&q=80"
                                    alt="Stackable Rings"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Rose Gold</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">Stackable Band Rings Set of 3</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-[#111111]/40 text-[10px] ml-1">(93)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£6,799</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    {{-- Product 8 --}}
                    <div class="swiper-slide" data-product-card>
                        <div class="group cursor-pointer">
                            <div class="relative overflow-hidden bg-[#F5F3EF] aspect-[3/4] mb-4">
                                <img
                                    src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600&q=80"
                                    alt="Pearl Earrings"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                                <span class="absolute top-3 left-3 bg-[#222222] text-white text-[10px] tracking-widest uppercase px-2 py-1">Limited</span>
                            </div>
                            <p class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase mb-1">Gold + Pearl</p>
                            <h3 class="text-[#111111] font-medium text-sm leading-snug mb-1">South Sea Pearl Studs</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="text-[#111111]/40 text-[10px] ml-1">(29)</span>
                            </div>
                            <p class="text-[#111111] font-semibold text-sm mb-3">£11,499</p>
                            <button
                                @click="$store.cart.add(1); window.flyToCart?.($el)"
                                class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-colors duration-300"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>

                </div>
                {{-- end swiper-wrapper --}}
            </div>
            {{-- end swiper --}}

            {{-- Custom arrows --}}
            <button
                id="products-prev"
                class="absolute left-0 top-1/3 -translate-y-1/2 -translate-x-4 z-10 flex h-12 w-12 items-center justify-center bg-white border border-[#111111]/10 shadow-md hover:bg-[#111111] hover:text-white transition-colors duration-300 hidden lg:flex"
                aria-label="Previous products"
            >
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M10 3L5 8l5 5"/>
                </svg>
            </button>
            <button
                id="products-next"
                class="absolute right-0 top-1/3 -translate-y-1/2 translate-x-4 z-10 flex h-12 w-12 items-center justify-center bg-white border border-[#111111]/10 shadow-md hover:bg-[#111111] hover:text-white transition-colors duration-300 hidden lg:flex"
                aria-label="Next products"
            >
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M6 3l5 5-5 5"/>
                </svg>
            </button>
        </div>

        {{-- Mobile view all --}}
        <div class="mt-8 text-center sm:hidden">
            <a
                href="#"
                class="inline-flex items-center gap-2 text-[#111111] text-sm font-medium tracking-widest uppercase border-b border-[#111111] pb-0.5"
            >
                View All
                <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M2 10L10 2M10 2H4M10 2v6"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ============================================================
     5. PARALLAX FEATURE SECTION
     ============================================================ --}}
<section class="relative bg-[#0D0D0D] overflow-hidden py-0" aria-label="Our promise">
    <div class="max-w-7xl mx-auto grid lg:grid-cols-2 min-h-[640px]">

        {{-- Left: image with parallax --}}
        <div class="relative overflow-hidden min-h-[400px] lg:min-h-[680px]">
            <div data-parallax="-0.2" class="absolute inset-0 will-change-transform">
                <img
                    data-gsap="clip-reveal"
                    src="https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=900&q=80"
                    alt="Luxury jewelry lifestyle"
                    class="w-full h-full object-cover scale-110"
                    loading="lazy"
                >
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#0D0D0D]/40"></div>
        </div>

        {{-- Right: content --}}
        <div class="flex flex-col justify-center px-8 py-16 lg:px-16 lg:py-24">
            <p class="text-[#202a40] text-xs tracking-[0.3em] uppercase font-medium mb-4">Our Promise</p>
            <h2
                data-split="lines"
                class="font-serif text-[#FAFAF8] text-4xl sm:text-5xl leading-tight tracking-tight mb-6"
            >
                Crafted for<br>Generations to Come
            </h2>
            <p class="text-[#FAFAF8]/60 text-base leading-relaxed mb-10 max-w-md">
                Each piece at Trendymus undergoes rigorous quality checks. We combine traditional craftsmanship with modern metallurgy to create jewelry that becomes a family heirloom.
            </p>

            {{-- Feature points --}}
            <ul class="space-y-5 mb-10">
                <li class="flex items-start gap-4">
                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#202a40]/20 text-[#202a40]">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 7l3.5 3.5L12 3"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[#FAFAF8] font-medium text-sm">Anti-Tarnish Technology</p>
                        <p class="text-[#FAFAF8]/50 text-xs mt-0.5 leading-relaxed">Advanced coating ensures your jewelry stays radiant for years without fading or discoloration.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#C9A96E]/20 text-[#C9A96E]">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 7l3.5 3.5L12 3"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[#FAFAF8] font-medium text-sm">BIS Hallmarked &amp; Certified</p>
                        <p class="text-[#FAFAF8]/50 text-xs mt-0.5 leading-relaxed">Every piece certified by the Bureau of Indian Standards for purity and quality assurance.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#202a40]/20 text-[#202a40]">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 7l3.5 3.5L12 3"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[#FAFAF8] font-medium text-sm">Free Shipping Across India</p>
                        <p class="text-[#FAFAF8]/50 text-xs mt-0.5 leading-relaxed">Complimentary insured shipping on all orders with real-time tracking and secure packaging.</p>
                    </div>
                </li>
            </ul>

            <a
                href="#"
                class="self-start inline-flex items-center gap-3 px-8 py-4 bg-[#202a40] text-white text-xs font-medium tracking-widest uppercase hover:bg-[#C9A96E] transition-colors duration-300"
            >
                Discover Our Story
                <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M2 12L12 2M12 2H4M12 2v8"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ============================================================
     6. TESTIMONIALS SLIDER
     ============================================================ --}}
<section class="bg-[#FAFAF8] py-24 lg:py-32" aria-label="Customer testimonials">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-14">
            <p class="text-[#202a40] text-xs tracking-[0.3em] uppercase font-medium mb-3">Reviews</p>
            <h2 class="font-serif text-[#111111] text-4xl sm:text-5xl leading-tight tracking-tight mb-6">
                Loved by Thousands
            </h2>
            <div class="mx-auto w-16 h-px bg-gradient-to-r from-transparent via-[#C9A96E] to-transparent"></div>
        </div>

        {{-- Swiper --}}
        <div class="swiper swiper-testimonials overflow-hidden pb-12">
            <div class="swiper-wrapper">

                {{-- Testimonial 1 --}}
                <div class="swiper-slide">
                    <div class="bg-white p-8 lg:p-10 h-full flex flex-col">
                        <div class="flex items-center gap-1 mb-6">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <svg class="w-8 h-8 text-[#202a40]/20 mb-4 fill-current shrink-0" viewBox="0 0 32 32">
                            <path d="M10 8C6.1 8 3 11.1 3 15c0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S16.5 2 11 2h-1zm12 0c-3.9 0-7 3.1-7 7 0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S28.5 2 23 2h-1z" transform="translate(0,2)"/>
                        </svg>
                        <p class="text-[#222222]/80 text-base leading-relaxed flex-1 mb-8 italic">
                            "The rose gold ring I ordered for my anniversary was absolutely stunning. The craftsmanship is unmatched and it arrived in beautiful packaging. My wife was in tears — in the best way possible!"
                        </p>
                        <div class="flex items-center gap-4 mt-auto">
                            <div class="h-11 w-11 rounded-full bg-[#202a40] flex items-center justify-center text-white font-semibold text-sm shrink-0">
                                R
                            </div>
                            <div>
                                <p class="text-[#111111] font-semibold text-sm">Rohit Mehta</p>
                                <p class="text-[#111111]/40 text-xs">Mumbai, Maharashtra</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 2 --}}
                <div class="swiper-slide">
                    <div class="bg-white p-8 lg:p-10 h-full flex flex-col">
                        <div class="flex items-center gap-1 mb-6">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <svg class="w-8 h-8 text-[#202a40]/20 mb-4 fill-current shrink-0" viewBox="0 0 32 32">
                            <path d="M10 8C6.1 8 3 11.1 3 15c0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S16.5 2 11 2h-1zm12 0c-3.9 0-7 3.1-7 7 0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S28.5 2 23 2h-1z" transform="translate(0,2)"/>
                        </svg>
                        <p class="text-[#222222]/80 text-base leading-relaxed flex-1 mb-8 italic">
                            "I've been buying from Trendymus for two years and the quality is consistently exceptional. The anti-tarnish coating on my gold bangles is still perfect after daily wear. Truly investment-worthy pieces."
                        </p>
                        <div class="flex items-center gap-4 mt-auto">
                            <div class="h-11 w-11 rounded-full bg-[#C9A96E] flex items-center justify-center text-white font-semibold text-sm shrink-0">
                                P
                            </div>
                            <div>
                                <p class="text-[#111111] font-semibold text-sm">Priya Krishnamurthy</p>
                                <p class="text-[#111111]/40 text-xs">Bangalore, Karnataka</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 3 --}}
                <div class="swiper-slide">
                    <div class="bg-white p-8 lg:p-10 h-full flex flex-col">
                        <div class="flex items-center gap-1 mb-6">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <svg class="w-8 h-8 text-[#202a40]/20 mb-4 fill-current shrink-0" viewBox="0 0 32 32">
                            <path d="M10 8C6.1 8 3 11.1 3 15c0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S16.5 2 11 2h-1zm12 0c-3.9 0-7 3.1-7 7 0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S28.5 2 23 2h-1z" transform="translate(0,2)"/>
                        </svg>
                        <p class="text-[#222222]/80 text-base leading-relaxed flex-1 mb-8 italic">
                            "Ordered the bridal set for my wedding and I received so many compliments. The packaging was gorgeous, delivery was on time, and the 7-day return policy gave me confidence to order online. Absolutely recommend!"
                        </p>
                        <div class="flex items-center gap-4 mt-auto">
                            <div class="h-11 w-11 rounded-full bg-[#202a40] flex items-center justify-center text-white font-semibold text-sm shrink-0">
                                A
                            </div>
                            <div>
                                <p class="text-[#111111] font-semibold text-sm">Ananya Sharma</p>
                                <p class="text-[#111111]/40 text-xs">Delhi, NCR</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 4 --}}
                <div class="swiper-slide">
                    <div class="bg-white p-8 lg:p-10 h-full flex flex-col">
                        <div class="flex items-center gap-1 mb-6">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4 text-[#C9A96E] fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <svg class="w-8 h-8 text-[#202a40]/20 mb-4 fill-current shrink-0" viewBox="0 0 32 32">
                            <path d="M10 8C6.1 8 3 11.1 3 15c0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S16.5 2 11 2h-1zm12 0c-3.9 0-7 3.1-7 7 0 3.3 2.1 6.1 5 7.2V26l6-4.4c.3 0 .7.1 1 .1 5.5 0 10-4.5 10-10S28.5 2 23 2h-1z" transform="translate(0,2)"/>
                        </svg>
                        <p class="text-[#222222]/80 text-base leading-relaxed flex-1 mb-8 italic">
                            "The pearl earrings I bought are exactly as shown — even more beautiful in person. Customer support was incredibly responsive when I had a sizing question. Will definitely be a repeat customer."
                        </p>
                        <div class="flex items-center gap-4 mt-auto">
                            <div class="h-11 w-11 rounded-full bg-[#222222] flex items-center justify-center text-white font-semibold text-sm shrink-0">
                                N
                            </div>
                            <div>
                                <p class="text-[#111111] font-semibold text-sm">Neha Iyer</p>
                                <p class="text-[#111111]/40 text-xs">Chennai, Tamil Nadu</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- end swiper-wrapper --}}

            {{-- Dot pagination --}}
            <div class="swiper-pagination swiper-testimonials-pagination mt-8"></div>
        </div>
    </div>
</section>

{{-- ============================================================
     7. NEWSLETTER CTA
     ============================================================ --}}
<section
    class="relative overflow-hidden py-24 lg:py-32"
    style="background: linear-gradient(135deg, #202a40 0%, #4A2530 100%);"
    aria-label="Newsletter"
>
    {{-- Decorative circles --}}
    <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-white opacity-[0.07] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-16 h-[500px] w-[500px] rounded-full bg-white opacity-[0.05] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[700px] w-[700px] rounded-full bg-white opacity-[0.03] pointer-events-none"></div>

    <div class="relative z-10 max-w-2xl mx-auto px-6 lg:px-8 text-center">
        <p class="text-white/60 text-xs tracking-[0.35em] uppercase font-medium mb-4">Stay in the loop</p>
        <h2
            data-split="words"
            class="font-serif text-white text-4xl sm:text-5xl lg:text-6xl leading-tight tracking-tight mb-6"
        >
            Join the Trendymus Circle
        </h2>
        <p class="text-white/70 text-base sm:text-lg leading-relaxed mb-12 max-w-lg mx-auto">
            Get early access to new collections, exclusive member offers, and styling inspiration delivered to your inbox.
        </p>

        {{-- Email form --}}
        <form
            action="#"
            method="POST"
            class="flex flex-col sm:flex-row items-stretch gap-3 max-w-md mx-auto"
            @submit.prevent="handleNewsletterSubmit($event)"
        >
            @csrf
            <div class="relative flex-1" data-float-label>
                <input
                    id="newsletter-email"
                    type="email"
                    name="email"
                    required
                    autocomplete="email"
                    placeholder=" "
                    class="peer w-full bg-white/10 border border-white/30 text-white placeholder-transparent px-4 pt-5 pb-3 text-sm focus:outline-none focus:border-white/70 transition-colors duration-300 backdrop-blur-sm"
                >
                <label
                    for="newsletter-email"
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-white/60 text-sm pointer-events-none transition-all duration-300 peer-focus:top-3 peer-focus:text-xs peer-focus:translate-y-0 peer-[:not(:placeholder-shown)]:top-3 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:translate-y-0"
                >
                    Your email address
                </label>
            </div>
            <button
                type="submit"
                class="px-8 py-4 bg-white text-[#202a40] text-xs font-semibold tracking-widest uppercase hover:bg-[#FAFAF8] transition-colors duration-300 whitespace-nowrap"
            >
                Subscribe
            </button>
        </form>

        <p class="mt-5 text-white/40 text-xs tracking-wide">
            No spam, ever. Unsubscribe at any time. &nbsp;·&nbsp; Privacy protected.
        </p>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ---- Products Swiper (manual init with custom nav) ----
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper === 'undefined') return;

        // Products slider
        var productsSwiper = new Swiper('.swiper-products', {
            slidesPerView: 1.2,
            spaceBetween: 16,
            grabCursor: true,
            navigation: {
                prevEl: '#products-prev',
                nextEl: '#products-next',
            },
            breakpoints: {
                480: { slidesPerView: 2, spaceBetween: 20 },
                768: { slidesPerView: 3, spaceBetween: 24 },
                1024: { slidesPerView: 4, spaceBetween: 28 },
            },
        });

        // Testimonials slider
        var testimonialsSwiper = new Swiper('.swiper-testimonials', {
            slidesPerView: 1,
            spaceBetween: 24,
            grabCursor: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: {
                el: '.swiper-testimonials-pagination',
                clickable: true,
                renderBullet: function (index, className) {
                    return '<span class="' + className + ' !w-6 !h-0.5 !rounded-none !bg-[#202a40] !opacity-30 [&.swiper-pagination-bullet-active]:!opacity-100"></span>';
                },
            },
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 24 },
                1024: { slidesPerView: 3, spaceBetween: 32 },
            },
        });

        // ---- Simple parallax on scroll ----
        var parallaxEl = document.querySelector('[data-parallax]');
        if (parallaxEl) {
            var parallaxFactor = parseFloat(parallaxEl.getAttribute('data-parallax')) || -0.2;
            function updateParallax () {
                var rect = parallaxEl.closest('section').getBoundingClientRect();
                var center = rect.top + rect.height / 2 - window.innerHeight / 2;
                parallaxEl.style.transform = 'translateY(' + (center * parallaxFactor) + 'px)';
            }
            window.addEventListener('scroll', updateParallax, { passive: true });
            updateParallax();
        }

        // ---- Newsletter form ----
        window.handleNewsletterSubmit = function (event) {
            var form = event.target;
            var btn = form.querySelector('button[type="submit"]');
            var original = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Subscribing...';
            setTimeout(function () {
                btn.textContent = 'Subscribed!';
                form.reset();
                setTimeout(function () {
                    btn.disabled = false;
                    btn.textContent = original;
                }, 3000);
            }, 800);
        };
    });
})();
</script>

<style>
/* Marquee keyframe */
@keyframes scroll-left {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* Hero fade-in keyframe */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
