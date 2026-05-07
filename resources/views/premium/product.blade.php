@extends('premium.layout')

@section('namespace', 'product')

@section('content')

{{-- ============================================================
     BREADCRUMB
     ============================================================ --}}
<nav
    aria-label="Breadcrumb"
    class="bg-[#FAFAF8] border-b border-[#111111]/8 px-6 lg:px-10 py-3"
>
    <ol class="max-w-[1440px] mx-auto flex items-center gap-2 text-xs text-[#111111]/40 flex-wrap">
        <li>
            <a href="#" class="hover:text-[#202a40] transition-colors duration-200">Home</a>
        </li>
        <li aria-hidden="true">
            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 2l4 4-4 4"/>
            </svg>
        </li>
        <li>
            <a href="#" class="hover:text-[#202a40] transition-colors duration-200">Rings</a>
        </li>
        <li aria-hidden="true">
            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 2l4 4-4 4"/>
            </svg>
        </li>
        <li>
            <span class="text-[#111111] font-medium">Rose Gold Diamond Ring</span>
        </li>
    </ol>
</nav>

{{-- ============================================================
     PRODUCT MAIN SECTION
     ============================================================ --}}
<section
    class="bg-[#FAFAF8] py-10 lg:py-16"
    x-data="{
        qty: 1,
        size: 'M',
        metal: 'Rose Gold',
        wishlisted: false,
        increaseQty() { if (this.qty < 10) this.qty++ },
        decreaseQty() { if (this.qty > 1) this.qty-- },
    }"
    aria-label="Product details"
>
    <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">

            {{-- ========================================================
                 LEFT — IMAGE GALLERY (col-span-7)
                 ======================================================== --}}
            <div class="lg:col-span-7" data-gallery>

                {{-- Main image --}}
                <div class="relative overflow-hidden bg-[#F5F3EF] aspect-square mb-4 group cursor-zoom-in">

                    {{-- "New" badge --}}
                    <span class="absolute top-4 left-4 z-10 bg-[#202a40] text-white text-[10px] tracking-[0.2em] uppercase px-3 py-1.5 font-medium">
                        New
                    </span>

                    {{-- Share button --}}
                    <button
                        class="absolute top-4 right-4 z-10 flex items-center justify-center w-9 h-9 bg-white/80 backdrop-blur-sm hover:bg-white transition-all duration-300 group/share"
                        aria-label="Share product"
                    >
                        <svg class="w-4 h-4 text-[#111111] group-hover/share:text-[#202a40] transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8M16 6l-4-4-4 4M12 2v13"/>
                        </svg>
                    </button>

                    {{-- Main image element --}}
                    <img
                        data-gallery-main
                        src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=1000&q=85"
                        alt="18K Rose Gold Diamond Solitaire Ring — main view"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    >
                </div>

                {{-- Thumbnail row --}}
                <div class="grid grid-cols-4 gap-3">
                    @php
                        $thumbs = [
                            [
                                'src'  => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&q=80',
                                'alt'  => 'Front view',
                                'active' => true,
                            ],
                            [
                                'src'  => 'https://images.unsplash.com/photo-1598560917505-59a3ad559071?w=400&q=80',
                                'alt'  => 'Side profile',
                                'active' => false,
                            ],
                            [
                                'src'  => 'https://images.unsplash.com/photo-1573408301185-9519f94815b8?w=400&q=80',
                                'alt'  => 'Detail shot',
                                'active' => false,
                            ],
                            [
                                'src'  => 'https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=400&q=80',
                                'alt'  => 'On model',
                                'active' => false,
                            ],
                        ];
                    @endphp
                    @foreach ($thumbs as $thumb)
                        <button
                            data-thumb="{{ $thumb['src'] }}"
                            aria-label="View: {{ $thumb['alt'] }}"
                            class="relative overflow-hidden aspect-square bg-[#F5F3EF] transition-all duration-300 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-[#202a40] focus:ring-offset-1 {{ $thumb['active'] ? 'ring-2 ring-[#202a40]' : 'opacity-60 hover:opacity-80' }}"
                        >
                            <img
                                src="{{ $thumb['src'] }}"
                                alt="{{ $thumb['alt'] }}"
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                                loading="lazy"
                            >
                        </button>
                    @endforeach
                </div>

            </div>

            {{-- ========================================================
                 RIGHT — PRODUCT INFO (col-span-5)
                 ======================================================== --}}
            <div class="lg:col-span-5 flex flex-col">

                {{-- Brand --}}
                <p class="text-[#202a40] text-[11px] tracking-[0.3em] uppercase font-semibold small-caps mb-3">Trendymus Jewelry</p>

                {{-- Product name --}}
                <h1
                    data-gsap="fade-up"
                    class="font-serif text-[#111111] text-3xl sm:text-4xl leading-tight tracking-tight mb-5"
                >
                    18K Rose Gold Diamond Solitaire Ring
                </h1>

                {{-- Rating row --}}
                <div class="flex items-center gap-3 mb-6 pb-6 border-b border-[#111111]/10">
                    <div class="flex items-center gap-0.5">
                        @for ($s = 0; $s < 5; $s++)
                            <svg class="w-4 h-4 text-[#C9A96E] fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-[#111111] text-sm font-medium">4.9</span>
                    <a href="#reviews" class="text-[#111111]/45 text-sm hover:text-[#202a40] transition-colors duration-200">(124 reviews)</a>
                    <span class="text-[#111111]/15">|</span>
                    <span class="text-[#111111]/45 text-sm">BIS Hallmarked</span>
                </div>

                {{-- Price section --}}
                <div class="mb-6" data-gsap="fade-up">
                    <div class="flex items-baseline gap-3 mb-1">
                        <span class="text-[#111111] font-bold text-4xl tracking-tight">£24,999</span>
                        <span class="text-[#111111]/35 text-lg line-through">£34,999</span>
                        <span class="inline-flex items-center px-2.5 py-1 bg-[#202a40]/12 text-[#202a40] text-xs font-semibold tracking-wide">
                            28% off
                        </span>
                    </div>
                    <p class="text-[#111111]/40 text-xs tracking-wide mt-1">
                        <svg class="inline w-3.5 h-3.5 mr-1 text-[#C9A96E]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Inclusive of all taxes &amp; duties
                    </p>
                </div>

                {{-- Size selector --}}
                <div class="mb-6" data-gsap="fade-up">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold">Ring Size: <span class="text-[#202a40]" x-text="size"></span></span>
                        <a href="#" class="text-[#111111]/40 text-xs underline underline-offset-2 hover:text-[#202a40] transition-colors duration-200">Size Guide</a>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @foreach (['S', 'M', 'L', 'XL'] as $s)
                            <button
                                @click="size = '{{ $s }}'"
                                :class="size === '{{ $s }}'
                                    ? 'bg-[#111111] text-[#FAFAF8] border-[#111111]'
                                    : 'bg-transparent text-[#111111] border-[#111111]/25 hover:border-[#111111]'"
                                class="w-12 h-12 border text-sm font-medium tracking-wide transition-all duration-200"
                            >
                                {{ $s }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Metal selector --}}
                <div class="mb-6" data-gsap="fade-up">
                    <span class="block text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold mb-3">Metal: <span class="text-[#202a40]" x-text="metal"></span></span>
                    <div class="flex items-center gap-3">
                        @php
                            $metals = [
                                ['label' => 'Yellow Gold', 'color' => '#C9A96E', 'border' => '#C9A96E'],
                                ['label' => 'Rose Gold',   'color' => '#202a40', 'border' => '#202a40'],
                                ['label' => 'White Gold',  'color' => '#E8E8E8', 'border' => '#aaa'],
                            ];
                        @endphp
                        @foreach ($metals as $m)
                            <button
                                @click="metal = '{{ $m['label'] }}'"
                                :class="metal === '{{ $m['label'] }}' ? 'ring-2 ring-offset-2 ring-[{{ $m['border'] }}]' : 'ring-1 ring-[{{ $m['border'] }}]/30 hover:ring-[{{ $m['border'] }}]'"
                                class="flex items-center gap-2 pl-1 pr-3 py-1.5 transition-all duration-200"
                                :title="'{{ $m['label'] }}'"
                            >
                                <span
                                    class="w-5 h-5 rounded-full border border-white/50 shrink-0"
                                    style="background: {{ $m['color'] }};"
                                ></span>
                                <span class="text-xs text-[#111111] font-medium">{{ $m['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Quantity --}}
                <div class="mb-8" data-gsap="fade-up">
                    <span class="block text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold mb-3">Quantity</span>
                    <div class="inline-flex items-center border border-[#111111]/20">
                        <button
                            @click="decreaseQty"
                            class="w-11 h-11 flex items-center justify-center text-[#111111]/60 hover:text-[#111111] hover:bg-[#F5F3EF] transition-all duration-200"
                            aria-label="Decrease quantity"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M3 8h10"/>
                            </svg>
                        </button>
                        <span
                            x-text="qty"
                            class="w-12 h-11 flex items-center justify-center text-[#111111] font-semibold text-sm border-x border-[#111111]/15"
                            aria-live="polite"
                        >1</span>
                        <button
                            @click="increaseQty"
                            class="w-11 h-11 flex items-center justify-center text-[#111111]/60 hover:text-[#111111] hover:bg-[#F5F3EF] transition-all duration-200"
                            aria-label="Increase quantity"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M8 3v10M3 8h10"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- CTA buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-6" data-gsap="fade-up">
                    <button
                        @click="$store.cart.add(qty); window.flyToCart?.($el)"
                        data-magnetic
                        class="group relative flex-1 flex items-center justify-center gap-2 px-8 py-4 bg-[#202a40] text-white text-sm font-medium tracking-[0.2em] uppercase overflow-hidden transition-all duration-500 hover:shadow-[0_0_40px_rgba(183,110,121,0.35)]"
                    >
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/>
                            </svg>
                            Add to Cart
                        </span>
                        <span class="absolute inset-0 translate-y-full bg-[#C9A96E] transition-transform duration-500 group-hover:translate-y-0"></span>
                    </button>

                    <button
                        class="flex-1 flex items-center justify-center gap-2 px-8 py-4 border-2 border-[#111111] text-[#111111] text-sm font-medium tracking-[0.2em] uppercase hover:bg-[#111111] hover:text-white transition-all duration-300"
                    >
                        Buy Now
                    </button>
                </div>

                {{-- Delivery info --}}
                <div class="flex items-start gap-3 p-4 bg-white border border-[#111111]/8 mb-6" data-gsap="fade-up">
                    <svg class="w-5 h-5 text-[#C9A96E] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8zM5.5 19a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM18.5 19a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                    </svg>
                    <div>
                        <p class="text-[#111111] text-sm font-medium">Free delivery by <span class="text-[#202a40]">Mon, Dec 15</span></p>
                        <p class="text-[#111111]/45 text-xs mt-0.5">Order within 6 hours. Free shipping on orders above £19.</p>
                    </div>
                </div>

                {{-- Offer chips --}}
                <div class="flex flex-wrap gap-2 mb-6" data-gsap="fade-up">
                    @php
                        $offers = [
                            [
                                'icon' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
                                'text' => '10% bank discount on HDFC cards',
                            ],
                            [
                                'icon' => '<path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>',
                                'text' => 'EMI from £2,083/month',
                            ],
                            [
                                'icon' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
                                'text' => 'Exchange old jewelry',
                            ],
                        ];
                    @endphp
                    @foreach ($offers as $offer)
                        <div class="inline-flex items-center gap-1.5 px-3 py-2 bg-[#C9A96E]/10 border border-[#C9A96E]/30 text-[#111111] text-xs">
                            <svg class="w-3.5 h-3.5 text-[#C9A96E] shrink-0 fill-current" viewBox="0 0 24 24">{!! $offer['icon'] !!}</svg>
                            <span>{{ $offer['text'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Wishlist + Share row --}}
                <div class="flex items-center gap-6 pt-6 border-t border-[#111111]/8">
                    <button
                        @click="wishlisted = !wishlisted"
                        class="flex items-center gap-2 text-sm transition-colors duration-200"
                        :class="wishlisted ? 'text-[#202a40]' : 'text-[#111111]/50 hover:text-[#202a40]'"
                    >
                        <svg
                            class="w-5 h-5 transition-all duration-200"
                            :class="wishlisted ? 'fill-[#202a40]' : 'fill-none'"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                        >
                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                        </svg>
                        <span x-text="wishlisted ? 'Wishlisted' : 'Wishlist'">Wishlist</span>
                    </button>

                    <button class="flex items-center gap-2 text-sm text-[#111111]/50 hover:text-[#202a40] transition-colors duration-200">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                        Share
                    </button>

                    <div class="flex items-center gap-1.5 ml-auto">
                        <svg class="w-4 h-4 text-[#C9A96E]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                        </svg>
                        <span class="text-[#111111]/40 text-xs">BIS Hallmarked &amp; Certified</span>
                    </div>
                </div>

            </div>
            {{-- /product info --}}

        </div>
    </div>
</section>

{{-- ============================================================
     PRODUCT DETAILS TABS
     ============================================================ --}}
<section
    id="reviews"
    class="bg-white py-16"
    x-data="{ tab: 'description' }"
    aria-label="Product details tabs"
>
    <div class="max-w-[1440px] mx-auto px-6 lg:px-10">

        {{-- Tab nav --}}
        <div class="flex border-b border-[#111111]/10 mb-10 gap-0 overflow-x-auto" role="tablist">
            @php
                $tabs = [
                    ['key' => 'description',    'label' => 'Description'],
                    ['key' => 'specifications', 'label' => 'Specifications'],
                    ['key' => 'reviews',        'label' => 'Reviews (124)'],
                    ['key' => 'care',           'label' => 'Care Guide'],
                ];
            @endphp
            @foreach ($tabs as $t)
                <button
                    @click="tab = '{{ $t['key'] }}'"
                    role="tab"
                    :aria-selected="tab === '{{ $t['key'] }}'"
                    class="relative shrink-0 px-6 py-4 text-sm font-medium tracking-[0.1em] uppercase transition-all duration-200"
                    :class="tab === '{{ $t['key'] }}'
                        ? 'text-[#111111]'
                        : 'text-[#111111]/40 hover:text-[#111111]/70'"
                >
                    {{ $t['label'] }}
                    <span
                        class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#202a40] transition-transform duration-300 origin-left"
                        :class="tab === '{{ $t['key'] }}' ? 'scale-x-100' : 'scale-x-0'"
                    ></span>
                </button>
            @endforeach
        </div>

        {{-- ── DESCRIPTION ── --}}
        <div x-show="tab === 'description'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="max-w-3xl">
                <p class="text-[#111111]/70 text-base leading-[1.9] mb-5">
                    The Trendymus 18K Rose Gold Diamond Solitaire Ring is a timeless expression of elegance and devotion. Crafted in luxurious 18-karat rose gold, this ring features a hand-selected round brilliant diamond centre stone, set in a classic four-prong setting that allows maximum light to dance through the stone from every angle.
                </p>
                <p class="text-[#111111]/70 text-base leading-[1.9] mb-5">
                    The warm blush tone of rose gold lends this ring an inherently romantic quality, making it a perfect choice for engagements, anniversaries, or as a cherished gift. Each piece is individually finished by our master jewellers at our atelier, ensuring no two rings are exactly alike — yours is uniquely yours.
                </p>
                <p class="text-[#111111]/70 text-base leading-[1.9]">
                    All Trendymus fine jewelry comes with a certificate of authenticity, BIS hallmark, and a complimentary velvet-lined gift box. Every purchase supports fair-trade artisan communities across Rajasthan and Gujarat.
                </p>
            </div>
        </div>

        {{-- ── SPECIFICATIONS ── --}}
        <div x-show="tab === 'specifications'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="max-w-2xl">
                <table class="w-full text-sm">
                    <tbody>
                        @php
                            $specs = [
                                ['label' => 'Material',     'value' => '18K Rose Gold (750 Hallmarked)'],
                                ['label' => 'Stone',        'value' => 'Round Brilliant Diamond, GIA Certified'],
                                ['label' => 'Carat Weight', 'value' => '0.50 ct (centre) + 0.12 ct (side stones)'],
                                ['label' => 'Metal Purity', 'value' => '75% gold, 25% alloy'],
                                ['label' => 'Ring Weight',  'value' => '3.2 grams'],
                                ['label' => 'Dimensions',   'value' => 'Band width 2mm, Setting height 6mm'],
                                ['label' => 'Certificate',  'value' => 'GIA + BIS Hallmark'],
                                ['label' => 'Finish',       'value' => 'High polish with rhodium accent'],
                            ];
                        @endphp
                        @foreach ($specs as $i => $spec)
                            <tr class="{{ $i % 2 === 0 ? 'bg-[#FAFAF8]' : 'bg-white' }}">
                                <td class="px-4 py-3 text-[#111111]/50 font-medium text-xs tracking-[0.1em] uppercase w-40 lg:w-52">{{ $spec['label'] }}</td>
                                <td class="px-4 py-3 text-[#111111] text-sm">{{ $spec['value'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── REVIEWS ── --}}
        <div x-show="tab === 'reviews'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

            {{-- Summary bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-8 mb-10 pb-10 border-b border-[#111111]/8">
                <div class="text-center sm:text-left">
                    <div class="font-serif text-[#111111] text-7xl font-bold leading-none mb-2">4.9</div>
                    <div class="flex items-center justify-center sm:justify-start gap-0.5 mb-2">
                        @for ($s = 0; $s < 5; $s++)
                            <svg class="w-5 h-5 text-[#C9A96E] fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-[#111111]/45 text-sm">124 verified reviews</p>
                </div>
                <div class="flex-1 space-y-2">
                    @php
                        $bars = [5 => 89, 4 => 24, 3 => 8, 2 => 2, 1 => 1];
                    @endphp
                    @foreach ($bars as $star => $count)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-[#111111]/50 w-6 text-right">{{ $star }}★</span>
                            <div class="flex-1 h-1.5 bg-[#111111]/8 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-[#C9A96E] rounded-full"
                                    style="width: {{ round($count / 124 * 100) }}%"
                                ></div>
                            </div>
                            <span class="text-xs text-[#111111]/35 w-6">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Review cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @php
                    $reviews = [
                        [
                            'name'   => 'Priya Sharma',
                            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&q=80',
                            'rating' => 5,
                            'date'   => 'November 28, 2025',
                            'title'  => 'Absolutely stunning piece',
                            'text'   => 'I gifted this to my wife on our anniversary and she was speechless. The rose gold colour is even more beautiful in person. The diamond catches the light beautifully. Packaging was luxurious too.',
                        ],
                        [
                            'name'   => 'Ananya Krishnan',
                            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&q=80',
                            'rating' => 5,
                            'date'   => 'December 1, 2025',
                            'title'  => 'Worth every penny',
                            'text'   => 'Got this for my engagement proposal. Ordered a custom size and delivery was right on time. The BIS hallmark certificate adds so much confidence. My fiancée said yes, obviously!',
                        ],
                        [
                            'name'   => 'Roshni Patel',
                            'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&q=80',
                            'rating' => 5,
                            'date'   => 'December 8, 2025',
                            'title'  => 'Exquisite craftsmanship',
                            'text'   => 'This is my third purchase from Trendymus and the quality never disappoints. The stone setting is immaculate — zero visible inclusions. Fits perfectly. Will keep coming back.',
                        ],
                    ];
                @endphp
                @foreach ($reviews as $review)
                    <div
                        data-gsap="fade-up"
                        class="bg-[#FAFAF8] p-6 flex flex-col gap-4"
                    >
                        {{-- Reviewer info --}}
                        <div class="flex items-center gap-3">
                            <img
                                src="{{ $review['avatar'] }}"
                                alt="{{ $review['name'] }}"
                                class="w-10 h-10 rounded-full object-cover"
                                loading="lazy"
                            >
                            <div>
                                <p class="text-[#111111] text-sm font-semibold">{{ $review['name'] }}</p>
                                <p class="text-[#111111]/35 text-xs">{{ $review['date'] }}</p>
                            </div>
                            <div class="ml-auto flex items-center gap-0.5">
                                @for ($s = 0; $s < $review['rating']; $s++)
                                    <svg class="w-3.5 h-3.5 text-[#C9A96E] fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>

                        {{-- Review body --}}
                        <div>
                            <p class="text-[#111111] text-sm font-semibold mb-2">{{ $review['title'] }}</p>
                            <p class="text-[#111111]/65 text-sm leading-relaxed">{{ $review['text'] }}</p>
                        </div>

                        {{-- Verified badge --}}
                        <div class="flex items-center gap-1.5 mt-auto pt-3 border-t border-[#111111]/8">
                            <svg class="w-3.5 h-3.5 text-[#C9A96E] fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-[#111111]/35 text-[11px] tracking-wide">Verified Purchase</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Write a review --}}
            <div class="flex justify-center">
                <button class="inline-flex items-center gap-2 px-8 py-4 border border-[#202a40] text-[#202a40] text-sm font-medium tracking-[0.2em] uppercase hover:bg-[#202a40] hover:text-white transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Write a Review
                </button>
            </div>

        </div>

        {{-- ── CARE GUIDE ── --}}
        <div x-show="tab === 'care'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="max-w-2xl">
                <h3 class="font-serif text-[#111111] text-2xl mb-6">Caring for Your Jewelry</h3>
                <ul class="space-y-4">
                    @php
                        $tips = [
                            'Store each piece separately in the provided velvet pouch to prevent scratches and tangling.',
                            'Remove jewelry before swimming, bathing, or exercising — chlorine and sweat accelerate tarnishing.',
                            'Apply perfumes, lotions, and hair products before putting on your jewelry, not after.',
                            'Clean with a soft lint-free cloth after each wear to remove skin oils and residue.',
                            'For a deeper clean, use lukewarm water with a drop of mild dish soap and a soft-bristle toothbrush. Rinse thoroughly and pat dry.',
                            'Have diamonds and gemstones professionally inspected and cleaned every 12 months.',
                            'Rose gold develops a natural patina over time — embrace it or ask us about professional re-polishing.',
                        ];
                    @endphp
                    @foreach ($tips as $i => $tip)
                        <li class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-[#202a40]/12 text-[#202a40] text-xs font-semibold flex items-center justify-center mt-0.5">
                                {{ $i + 1 }}
                            </span>
                            <p class="text-[#111111]/70 text-sm leading-relaxed">{{ $tip }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================
     RELATED PRODUCTS
     ============================================================ --}}
<section class="bg-[#0D0D0D] py-20 lg:py-28" aria-label="Related products">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-10">

        {{-- Section heading --}}
        <div class="flex items-end justify-between mb-12">
            <div>
                <p class="text-[#202a40] text-[11px] tracking-[0.3em] uppercase font-medium mb-3">Curated for you</p>
                <h2
                    data-gsap="fade-up"
                    class="font-serif text-[#FAFAF8] text-4xl sm:text-5xl leading-tight tracking-tight"
                >
                    You May Also Like
                </h2>
            </div>
            <a
                href="#"
                class="hidden sm:inline-flex items-center gap-2 text-[#FAFAF8]/50 text-xs tracking-[0.2em] uppercase font-medium border-b border-[#FAFAF8]/20 pb-0.5 hover:text-[#C9A96E] hover:border-[#C9A96E] transition-all duration-300"
            >
                View All
                <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M2 10L10 2M10 2H4M10 2v6"/>
                </svg>
            </a>
        </div>

        {{-- Swiper --}}
        <div class="relative">
            <div class="swiper swiper-products overflow-hidden">
                <div class="swiper-wrapper">

                    @php
                        $related = [
                            [
                                'image'   => 'https://images.unsplash.com/photo-1602751584552-8ba73aad10e1?w=500&q=80',
                                'name'    => 'Pearl Halo Engagement Ring',
                                'metal'   => 'Rose Gold',
                                'price'   => '£18,999',
                                'mrp'     => '£25,999',
                                'rating'  => 5,
                                'reviews' => 47,
                                'badge'   => 'New',
                            ],
                            [
                                'image'   => 'https://images.unsplash.com/photo-1612453051258-4e8b47c0f6c1?w=500&q=80',
                                'name'    => 'Sapphire Three-Stone Ring',
                                'metal'   => 'White Gold',
                                'price'   => '£29,999',
                                'mrp'     => '£41,000',
                                'rating'  => 5,
                                'reviews' => 31,
                                'badge'   => null,
                            ],
                            [
                                'image'   => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=500&q=80',
                                'name'    => 'Twisted Band Diamond Ring',
                                'metal'   => 'Rose Gold',
                                'price'   => '£15,499',
                                'mrp'     => '£21,000',
                                'rating'  => 4,
                                'reviews' => 88,
                                'badge'   => 'Bestseller',
                            ],
                            [
                                'image'   => 'https://images.unsplash.com/photo-1573408301185-9519f94815b8?w=500&q=80',
                                'name'    => 'Emerald Eternity Band',
                                'metal'   => 'Yellow Gold',
                                'price'   => '£22,499',
                                'mrp'     => '£31,000',
                                'rating'  => 5,
                                'reviews' => 19,
                                'badge'   => null,
                            ],
                            [
                                'image'   => 'https://images.unsplash.com/photo-1610694955371-d4a3e0ce4b52?w=500&q=80',
                                'name'    => 'Gold Milgrain Stacking Ring',
                                'metal'   => 'Yellow Gold',
                                'price'   => '£7,999',
                                'mrp'     => '£11,000',
                                'rating'  => 5,
                                'reviews' => 112,
                                'badge'   => 'Popular',
                            ],
                            [
                                'image'   => 'https://images.unsplash.com/photo-1598560917807-1bae44bd2be8?w=500&q=80',
                                'name'    => 'Platinum Solitaire Band',
                                'metal'   => 'Platinum',
                                'price'   => '£38,999',
                                'mrp'     => '£54,000',
                                'rating'  => 5,
                                'reviews' => 26,
                                'badge'   => 'Exclusive',
                            ],
                        ];
                    @endphp

                    @foreach ($related as $rel)
                        <div class="swiper-slide" data-product-card>
                            <div class="group cursor-pointer">
                                <div class="relative overflow-hidden bg-[#1A1A1A] aspect-[3/4] mb-4">
                                    <img
                                        src="{{ $rel['image'] }}"
                                        alt="{{ $rel['name'] }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                        loading="lazy"
                                    >
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/15 transition-colors duration-300"></div>
                                    @if ($rel['badge'])
                                        <span class="absolute top-3 left-3 bg-[#202a40] text-white text-[9px] tracking-widest uppercase px-2 py-1 font-medium">
                                            {{ $rel['badge'] }}
                                        </span>
                                    @endif
                                    <div class="absolute bottom-0 inset-x-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out">
                                        <button class="w-full py-3 bg-[#111111]/90 text-[#FAFAF8] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#202a40] transition-colors duration-300">
                                            Quick View
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[#202a40] text-[9px] tracking-[0.2em] uppercase mb-1 font-medium">{{ $rel['metal'] }}</p>
                                <h3 class="text-[#FAFAF8] font-medium text-sm leading-snug mb-2">{{ $rel['name'] }}</h3>
                                <div class="flex items-center gap-1 mb-2">
                                    @for ($s = 0; $s < $rel['rating']; $s++)
                                        <svg class="w-3 h-3 text-[#C9A96E] fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-[#FAFAF8]/30 text-[10px] ml-1">({{ $rel['reviews'] }})</span>
                                </div>
                                <div class="flex items-baseline gap-2 mb-3">
                                    <span class="text-[#FAFAF8] font-semibold text-sm">{{ $rel['price'] }}</span>
                                    <span class="text-[#FAFAF8]/30 text-xs line-through">{{ $rel['mrp'] }}</span>
                                </div>
                                <button
                                    @click="$store.cart.add(1); window.flyToCart?.($el)"
                                    class="w-full py-2.5 border border-[#FAFAF8]/20 text-[#FAFAF8] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#202a40] hover:border-[#202a40] transition-all duration-300"
                                >
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- Swiper navigation --}}
                <div class="swiper-button-prev !text-[#FAFAF8] !w-10 !h-10 !-left-2 after:!text-base"></div>
                <div class="swiper-button-next !text-[#FAFAF8] !w-10 !h-10 !-right-2 after:!text-base"></div>
                <div class="swiper-pagination !bottom-[-2rem] [&_.swiper-pagination-bullet]:bg-[#FAFAF8]/30 [&_.swiper-pagination-bullet-active]:bg-[#202a40]"></div>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================
     STICKY BOTTOM BAR — MOBILE ONLY
     ============================================================ --}}
<div
    class="fixed bottom-0 inset-x-0 z-40 lg:hidden bg-white border-t border-[#111111]/10 px-4 py-3 safe-area-inset-bottom"
    aria-label="Mobile buy bar"
    x-data
>
    <div class="flex items-center gap-3">
        <div class="flex-1 min-w-0">
            <p class="text-[#111111]/45 text-xs line-through truncate">£34,999</p>
            <p class="text-[#111111] font-bold text-lg leading-tight">£24,999 <span class="text-[#202a40] text-xs font-semibold">28% off</span></p>
        </div>
        <button
            @click="$store.cart.add(1); window.flyToCart?.($el)"
            class="flex-shrink-0 px-7 py-3.5 bg-[#202a40] text-white text-sm font-medium tracking-[0.15em] uppercase hover:bg-[#C9A96E] transition-colors duration-300"
        >
            Add to Cart
        </button>
    </div>
</div>

{{-- spacer so sticky bar doesn't cover content on mobile --}}
<div class="h-20 lg:hidden"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Gallery: init first thumb as active ──────────────────────────
    const gallery = document.querySelector('[data-gallery]');
    if (gallery) {
        const firstThumb = gallery.querySelector('[data-thumb]');
        if (firstThumb) {
            firstThumb.classList.add('ring-2', 'ring-[#202a40]');
            firstThumb.classList.remove('opacity-60');
        }
    }

    // ── Related products Swiper (dark bg section) ────────────────────
    // The global initSwipers() called by premium.js DOMContentLoaded
    // handles .swiper-products automatically. If Barba re-enters this
    // page, initPageAnimations(container) handles re-init.

    // ── Fly-to-cart for sticky bottom bar ────────────────────────────
    // window.flyToCart is already exposed by premium.js.
    // The sticky bar's Add to Cart delegates via Alpine @click which
    // calls window.flyToCart?.(el) — the element passed must sit inside
    // or near a [data-product-card]. For the sticky bar we manually
    // find the first product card on the page to provide the image src.
    const stickyBtn = document.querySelector('[aria-label="Mobile buy bar"] button');
    if (stickyBtn) {
        stickyBtn.addEventListener('click', function () {
            const mainImg = document.querySelector('[data-gallery-main]');
            const cartIcon = document.querySelector('#cart-icon');
            const cartCount = document.querySelector('#cart-count');
            if (!mainImg || !cartIcon || typeof gsap === 'undefined') return;

            const imgRect = mainImg.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();

            const clone = document.createElement('div');
            clone.style.cssText = [
                'position:fixed',
                'top:' + imgRect.top + 'px',
                'left:' + imgRect.left + 'px',
                'width:80px',
                'height:80px',
                'background-image:url(' + mainImg.src + ')',
                'background-size:cover',
                'background-position:center',
                'border-radius:4px',
                'pointer-events:none',
                'z-index:9999',
            ].join(';');
            document.body.appendChild(clone);

            gsap.to(clone, {
                x: cartRect.left - imgRect.left + cartRect.width / 2 - 40,
                y: cartRect.top - imgRect.top + cartRect.height / 2 - 40,
                scale: 0.1,
                opacity: 0,
                duration: 0.75,
                ease: 'power3.in',
                onComplete: function () {
                    clone.remove();
                    gsap.fromTo(cartIcon, { scale: 1 }, { scale: 1.4, duration: 0.25, ease: 'elastic.out(1.5,0.4)', yoyo: true, repeat: 1 });
                    if (cartCount) {
                        cartCount.textContent = (parseInt(cartCount.textContent) || 0) + 1;
                        gsap.fromTo(cartCount, { scale: 1.5 }, { scale: 1, duration: 0.35, ease: 'elastic.out(1.2,0.5)' });
                    }
                },
            });
        });
    }
});
</script>
@endpush

@endsection
