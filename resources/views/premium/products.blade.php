@extends('premium.layout')

@section('namespace', 'products')

@section('content')

{{-- ============================================================
     MOBILE FILTER SLIDE-OVER
     ============================================================ --}}
<div
    x-data="{ filterOpen: false }"
    @keydown.escape.window="filterOpen = false"
    class="relative"
>

{{-- Backdrop --}}
<div
    x-show="filterOpen"
    x-transition:enter="transition-opacity duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="filterOpen = false"
    class="fixed inset-0 bg-black/60 z-40 lg:hidden"
    aria-hidden="true"
></div>

{{-- Slide-over drawer --}}
<aside
    x-show="filterOpen"
    x-transition:enter="transition-transform duration-400 ease-out"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition-transform duration-300 ease-in"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 w-80 max-w-[90vw] bg-[#FAFAF8] z-50 overflow-y-auto shadow-2xl lg:hidden"
    aria-label="Mobile filters"
>
    <div class="flex items-center justify-between px-6 py-5 border-b border-[#111111]/10">
        <h2 class="font-serif text-[#111111] text-xl">Filters</h2>
        <button @click="filterOpen = false" class="p-1 text-[#111111]/50 hover:text-[#111111] transition-colors" aria-label="Close filters">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="px-6 py-4">
        {{-- Mobile filter content mirrors desktop sidebar --}}
        <div class="flex items-center justify-between mb-4">
            <span class="text-[#111111]/50 text-xs tracking-widest uppercase">Active filters</span>
            <a href="#" class="text-[#202a40] text-xs tracking-widest uppercase font-medium hover:text-[#C9A96E] transition-colors">Clear all</a>
        </div>

        @include('premium.partials.filter-accordion-content')
    </div>
</aside>

{{-- ============================================================
     PAGE WRAPPER: sidebar + content
     ============================================================ --}}
<div class="min-h-screen bg-[#FAFAF8]">
    <div class="max-w-[1440px] mx-auto flex">

        {{-- ========================================================
             DESKTOP SIDEBAR
             ======================================================== --}}
        <aside class="hidden lg:flex lg:w-72 shrink-0 flex-col border-r border-[#111111]/10 bg-[#FAFAF8]">
            <div class="sticky top-24 h-[calc(100vh-6rem)] overflow-y-auto px-8 py-8 scrollbar-thin scrollbar-thumb-[#C9A96E]/30">

                {{-- Heading --}}
                <div class="flex items-center justify-between mb-8">
                    <h2 class="font-serif text-[#111111] text-2xl tracking-tight">Filters</h2>
                    <a
                        href="#"
                        class="text-[#202a40] text-[10px] tracking-[0.2em] uppercase font-medium hover:text-[#C9A96E] transition-colors duration-200 border-b border-[#202a40]/40 hover:border-[#C9A96E] pb-px"
                    >
                        Clear all
                    </a>
                </div>

                {{-- Divider --}}
                <div class="w-full h-px bg-gradient-to-r from-[#C9A96E]/40 via-[#202a40]/20 to-transparent mb-8"></div>

                {{-- ── 1. CATEGORIES ── --}}
                <div
                    x-data="{ open: true }"
                    class="mb-6 border-b border-[#111111]/8 pb-6"
                >
                    <button
                        @click="open = !open"
                        class="flex w-full items-center justify-between text-left mb-4 group"
                        :aria-expanded="open"
                    >
                        <span class="text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold">Categories</span>
                        <svg
                            class="w-4 h-4 text-[#111111]/40 transition-transform duration-300 group-hover:text-[#202a40]"
                            :class="open ? 'rotate-180' : ''"
                            viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"
                        >
                            <path d="M4 6l4 4 4-4"/>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="space-y-3">
                        @php
                            $categories = [
                                ['label' => 'Rings',      'count' => 142],
                                ['label' => 'Necklaces',  'count' => 98],
                                ['label' => 'Earrings',   'count' => 176],
                                ['label' => 'Bangles',    'count' => 64],
                                ['label' => 'Sets',       'count' => 37],
                                ['label' => 'Bracelets',  'count' => 81],
                            ];
                        @endphp
                        @foreach ($categories as $cat)
                            <label class="flex items-center gap-3 cursor-pointer group/check">
                                <input
                                    type="checkbox"
                                    class="w-4 h-4 rounded-none border border-[#111111]/30 checked:bg-[#202a40] checked:border-[#202a40] focus:ring-[#202a40] focus:ring-1 focus:ring-offset-0 cursor-pointer accent-[#202a40]"
                                >
                                <span class="flex-1 text-[#111111]/70 text-sm group-hover/check:text-[#111111] transition-colors duration-200">{{ $cat['label'] }}</span>
                                <span class="text-[#111111]/30 text-xs">({{ $cat['count'] }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── 2. PRICE RANGE ── --}}
                <div
                    x-data="{ open: true, minVal: 0, maxVal: 50000 }"
                    class="mb-6 border-b border-[#111111]/8 pb-6"
                >
                    <button
                        @click="open = !open"
                        class="flex w-full items-center justify-between text-left mb-4 group"
                        :aria-expanded="open"
                    >
                        <span class="text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold">Price Range</span>
                        <svg
                            class="w-4 h-4 text-[#111111]/40 transition-transform duration-300 group-hover:text-[#202a40]"
                            :class="open ? 'rotate-180' : ''"
                            viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"
                        >
                            <path d="M4 6l4 4 4-4"/>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[#111111]/50 text-xs">£<span x-text="minVal.toLocaleString('en-GB')">0</span></span>
                            <span class="text-[#202a40] text-xs font-medium">£<span x-text="maxVal.toLocaleString('en-GB')">50,000</span></span>
                        </div>
                        <div class="relative mb-4">
                            <input
                                type="range"
                                x-model="maxVal"
                                min="0"
                                max="50000"
                                step="500"
                                class="w-full h-1 appearance-none bg-[#111111]/10 rounded-full cursor-pointer
                                       [&::-webkit-slider-thumb]:appearance-none
                                       [&::-webkit-slider-thumb]:w-4
                                       [&::-webkit-slider-thumb]:h-4
                                       [&::-webkit-slider-thumb]:rounded-full
                                       [&::-webkit-slider-thumb]:bg-[#202a40]
                                       [&::-webkit-slider-thumb]:border-2
                                       [&::-webkit-slider-thumb]:border-white
                                       [&::-webkit-slider-thumb]:shadow-md
                                       [&::-webkit-slider-thumb]:cursor-pointer
                                       [&::-moz-range-thumb]:w-4
                                       [&::-moz-range-thumb]:h-4
                                       [&::-moz-range-thumb]:rounded-full
                                       [&::-moz-range-thumb]:bg-[#202a40]
                                       [&::-moz-range-thumb]:border-2
                                       [&::-moz-range-thumb]:border-white
                                       [&::-moz-range-thumb]:cursor-pointer"
                            >
                        </div>
                        <button class="w-full py-2 border border-[#202a40] text-[#202a40] text-[10px] tracking-[0.2em] uppercase hover:bg-[#202a40] hover:text-white transition-all duration-300">
                            Apply
                        </button>
                    </div>
                </div>

                {{-- ── 3. METAL ── --}}
                <div
                    x-data="{ open: true }"
                    class="mb-6 border-b border-[#111111]/8 pb-6"
                >
                    <button
                        @click="open = !open"
                        class="flex w-full items-center justify-between text-left mb-4 group"
                        :aria-expanded="open"
                    >
                        <span class="text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold">Metal</span>
                        <svg
                            class="w-4 h-4 text-[#111111]/40 transition-transform duration-300 group-hover:text-[#202a40]"
                            :class="open ? 'rotate-180' : ''"
                            viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"
                        >
                            <path d="M4 6l4 4 4-4"/>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="space-y-3">
                        @foreach (['Gold', 'Silver', 'Rose Gold', 'Platinum'] as $metal)
                            <label class="flex items-center gap-3 cursor-pointer group/check">
                                <input
                                    type="checkbox"
                                    class="w-4 h-4 rounded-none border border-[#111111]/30 focus:ring-[#202a40] focus:ring-1 focus:ring-offset-0 cursor-pointer accent-[#202a40]"
                                >
                                <span class="flex-1 text-[#111111]/70 text-sm group-hover/check:text-[#111111] transition-colors duration-200">{{ $metal }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── 4. STONE ── --}}
                <div
                    x-data="{ open: false }"
                    class="mb-6 border-b border-[#111111]/8 pb-6"
                >
                    <button
                        @click="open = !open"
                        class="flex w-full items-center justify-between text-left mb-4 group"
                        :aria-expanded="open"
                    >
                        <span class="text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold">Stone</span>
                        <svg
                            class="w-4 h-4 text-[#111111]/40 transition-transform duration-300 group-hover:text-[#202a40]"
                            :class="open ? 'rotate-180' : ''"
                            viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"
                        >
                            <path d="M4 6l4 4 4-4"/>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="space-y-3">
                        @foreach (['Diamond', 'Emerald', 'Ruby', 'Sapphire', 'Pearl'] as $stone)
                            <label class="flex items-center gap-3 cursor-pointer group/check">
                                <input
                                    type="checkbox"
                                    class="w-4 h-4 rounded-none border border-[#111111]/30 focus:ring-[#202a40] focus:ring-1 focus:ring-offset-0 cursor-pointer accent-[#202a40]"
                                >
                                <span class="flex-1 text-[#111111]/70 text-sm group-hover/check:text-[#111111] transition-colors duration-200">{{ $stone }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── 5. RATING ── --}}
                <div
                    x-data="{ open: false }"
                    class="mb-6"
                >
                    <button
                        @click="open = !open"
                        class="flex w-full items-center justify-between text-left mb-4 group"
                        :aria-expanded="open"
                    >
                        <span class="text-[#111111] text-xs tracking-[0.2em] uppercase font-semibold">Rating</span>
                        <svg
                            class="w-4 h-4 text-[#111111]/40 transition-transform duration-300 group-hover:text-[#202a40]"
                            :class="open ? 'rotate-180' : ''"
                            viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"
                        >
                            <path d="M4 6l4 4 4-4"/>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="space-y-3">
                        @php
                            $ratings = [
                                ['label' => '5★', 'sub' => 'Only', 'count' => 23],
                                ['label' => '4★+', 'sub' => '& above', 'count' => 87],
                                ['label' => '3★+', 'sub' => '& above', 'count' => 120],
                            ];
                        @endphp
                        @foreach ($ratings as $r)
                            <label class="flex items-center gap-3 cursor-pointer group/check">
                                <input
                                    type="radio"
                                    name="rating"
                                    class="w-4 h-4 border border-[#111111]/30 focus:ring-[#202a40] focus:ring-1 focus:ring-offset-0 cursor-pointer accent-[#202a40]"
                                >
                                <span class="flex items-center gap-1.5 flex-1">
                                    <span class="text-[#C9A96E] text-sm font-medium">{{ $r['label'] }}</span>
                                    <span class="text-[#111111]/50 text-xs">{{ $r['sub'] }}</span>
                                </span>
                                <span class="text-[#111111]/30 text-xs">({{ $r['count'] }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </aside>

        {{-- ========================================================
             MAIN CONTENT
             ======================================================== --}}
        <main class="flex-1 min-w-0 px-4 sm:px-6 lg:px-10 py-8">

            {{-- ── TOP BAR ── --}}
            <div
                x-data="{ sort: 'Best Match', grid: 4 }"
                class="flex flex-col sm:flex-row sm:items-center gap-4 justify-between mb-8"
            >
                {{-- Left: mobile filter + results count --}}
                <div class="flex items-center gap-4">
                    {{-- Mobile filter pill --}}
                    <button
                        @click="filterOpen = true"
                        class="lg:hidden inline-flex items-center gap-2 px-4 py-2 border border-[#111111]/20 text-[#111111] text-xs tracking-[0.15em] uppercase font-medium hover:border-[#202a40] hover:text-[#202a40] transition-all duration-300"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 5h14M6 10h8M9 15h2"/>
                        </svg>
                        Filters
                    </button>
                    <p class="text-[#111111]/50 text-sm">
                        Showing <span class="text-[#111111] font-semibold">48</span> products
                    </p>
                </div>

                {{-- Right: sort + grid toggle --}}
                <div class="flex items-center gap-4">

                    {{-- Sort dropdown --}}
                    <div x-data="{ sortOpen: false }" class="relative">
                        <button
                            @click="sortOpen = !sortOpen"
                            @click.outside="sortOpen = false"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-[#111111]/20 text-[#111111] text-xs tracking-[0.1em] uppercase font-medium hover:border-[#202a40] transition-all duration-300"
                        >
                            <span x-text="sort">Best Match</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="sortOpen ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 6l4 4 4-4"/>
                            </svg>
                        </button>
                        <div
                            x-show="sortOpen"
                            x-transition:enter="transition duration-150 ease-out"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition duration-100 ease-in"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 top-full mt-1 w-48 bg-white border border-[#111111]/10 shadow-xl z-20"
                        >
                            @foreach (['Best Match', 'Price Low–High', 'Price High–Low', 'Newest', 'Best Rating'] as $option)
                                <button
                                    @click="sort = '{{ $option }}'; sortOpen = false"
                                    class="block w-full text-left px-4 py-2.5 text-sm text-[#111111]/70 hover:text-[#111111] hover:bg-[#FAFAF8] transition-colors duration-150"
                                    :class="sort === '{{ $option }}' ? 'text-[#202a40] font-medium bg-[#FAFAF8]' : ''"
                                >
                                    {{ $option }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grid toggle (hidden on mobile) --}}
                    <div class="hidden sm:flex items-center gap-1 border border-[#111111]/15 p-1">
                        {{-- 2-col --}}
                        <button
                            @click="grid = 2"
                            :class="grid === 2 ? 'bg-[#111111] text-white' : 'text-[#111111]/40 hover:text-[#111111]'"
                            class="p-1.5 transition-all duration-200"
                            aria-label="2 column grid"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                                <rect x="1" y="1" width="6" height="6" rx="0.5"/>
                                <rect x="9" y="1" width="6" height="6" rx="0.5"/>
                                <rect x="1" y="9" width="6" height="6" rx="0.5"/>
                                <rect x="9" y="9" width="6" height="6" rx="0.5"/>
                            </svg>
                        </button>
                        {{-- 4-col --}}
                        <button
                            @click="grid = 4"
                            :class="grid === 4 ? 'bg-[#111111] text-white' : 'text-[#111111]/40 hover:text-[#111111]'"
                            class="p-1.5 transition-all duration-200"
                            aria-label="4 column grid"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                                <rect x="1"   y="1" width="3" height="3" rx="0.3"/>
                                <rect x="5.5" y="1" width="3" height="3" rx="0.3"/>
                                <rect x="10"  y="1" width="3" height="3" rx="0.3"/>
                                <rect x="14.5" y="1" width="3" height="3" rx="0.3"/>
                                <rect x="1"   y="6.5" width="3" height="3" rx="0.3"/>
                                <rect x="5.5" y="6.5" width="3" height="3" rx="0.3"/>
                                <rect x="10"  y="6.5" width="3" height="3" rx="0.3"/>
                                <rect x="14.5" y="6.5" width="3" height="3" rx="0.3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── PRODUCT GRID ── --}}
            <div
                x-data="{ loaded: false, grid: 4 }"
                x-init="setTimeout(() => loaded = true, 1500)"
                class="relative"
            >

                {{-- SKELETON STATE --}}
                <div
                    x-show="!loaded"
                    data-gsap="stagger-grid"
                    class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                    aria-label="Loading products"
                    aria-busy="true"
                >
                    @for ($i = 0; $i < 8; $i++)
                        <div data-aos="fade-up" data-aos-delay="{{ $i * 60 }}" class="animate-pulse">
                            <div class="aspect-square bg-[#111111]/8 mb-3"></div>
                            <div class="h-3 bg-[#111111]/8 rounded mb-2 w-1/3"></div>
                            <div class="h-4 bg-[#111111]/8 rounded mb-2 w-3/4"></div>
                            <div class="h-3 bg-[#111111]/8 rounded mb-3 w-1/2"></div>
                            <div class="h-9 bg-[#111111]/8 rounded"></div>
                        </div>
                    @endfor
                </div>

                {{-- REAL PRODUCT GRID --}}
                @php
                    $products = [
                        [
                            'id'      => 1,
                            'image'   => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=600&q=80',
                            'name'    => 'Rose Gold Solitaire Ring',
                            'brand'   => 'Trendymus Gold',
                            'price'   => '£8,499',
                            'mrp'     => '£11,999',
                            'off'     => '29%',
                            'rating'  => 5,
                            'reviews' => 128,
                            'badge'   => 'New',
                            'badgeColor' => 'bg-[#202a40]',
                        ],
                        [
                            'id'      => 2,
                            'image'   => 'https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=600&q=80',
                            'name'    => 'Layered Chain Necklace',
                            'brand'   => 'Trendymus Gold',
                            'price'   => '£5,999',
                            'mrp'     => '£7,999',
                            'off'     => '25%',
                            'rating'  => 5,
                            'reviews' => 84,
                            'badge'   => null,
                            'badgeColor' => '',
                        ],
                        [
                            'id'      => 3,
                            'image'   => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=600&q=80',
                            'name'    => 'Floral Drop Earrings',
                            'brand'   => 'Trendymus Rose',
                            'price'   => '£3,299',
                            'mrp'     => '£4,499',
                            'off'     => '27%',
                            'rating'  => 5,
                            'reviews' => 211,
                            'badge'   => 'Bestseller',
                            'badgeColor' => 'bg-[#C9A96E]',
                        ],
                        [
                            'id'      => 4,
                            'image'   => 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=600&q=80',
                            'name'    => 'Classic Bangle Set of 4',
                            'brand'   => 'Trendymus Gold',
                            'price'   => '£12,999',
                            'mrp'     => '£17,500',
                            'off'     => '26%',
                            'rating'  => 4,
                            'reviews' => 67,
                            'badge'   => null,
                            'badgeColor' => '',
                        ],
                        [
                            'id'      => 5,
                            'image'   => 'https://images.unsplash.com/photo-1573408301185-9519f94815b8?w=600&q=80',
                            'name'    => 'Diamond Tennis Bracelet',
                            'brand'   => 'Trendymus Diamond',
                            'price'   => '£24,999',
                            'mrp'     => '£34,999',
                            'off'     => '29%',
                            'rating'  => 5,
                            'reviews' => 53,
                            'badge'   => 'New',
                            'badgeColor' => 'bg-[#202a40]',
                        ],
                        [
                            'id'      => 6,
                            'image'   => 'https://images.unsplash.com/photo-1602751584552-8ba73aad10e1?w=600&q=80',
                            'name'    => 'Pearl Drop Pendant',
                            'brand'   => 'Trendymus Pearl',
                            'price'   => '£6,799',
                            'mrp'     => '£9,500',
                            'off'     => '28%',
                            'rating'  => 4,
                            'reviews' => 39,
                            'badge'   => null,
                            'badgeColor' => '',
                        ],
                        [
                            'id'      => 7,
                            'image'   => 'https://images.unsplash.com/photo-1506630448388-4e683c67ddb0?w=600&q=80',
                            'name'    => 'Emerald Halo Ring',
                            'brand'   => 'Trendymus Gems',
                            'price'   => '£18,499',
                            'mrp'     => '£26,000',
                            'off'     => '29%',
                            'rating'  => 5,
                            'reviews' => 42,
                            'badge'   => 'Limited',
                            'badgeColor' => 'bg-[#111111]',
                        ],
                        [
                            'id'      => 8,
                            'image'   => 'https://images.unsplash.com/photo-1610694955371-d4a3e0ce4b52?w=600&q=80',
                            'name'    => 'Gold Stacking Ring Set',
                            'brand'   => 'Trendymus Gold',
                            'price'   => '£4,299',
                            'mrp'     => '£5,999',
                            'off'     => '28%',
                            'rating'  => 5,
                            'reviews' => 156,
                            'badge'   => null,
                            'badgeColor' => '',
                        ],
                        [
                            'id'      => 9,
                            'image'   => 'https://images.unsplash.com/photo-1512163143273-bde0e3cc7407?w=600&q=80',
                            'name'    => 'Sapphire Statement Necklace',
                            'brand'   => 'Trendymus Gems',
                            'price'   => '£31,999',
                            'mrp'     => '£44,999',
                            'off'     => '29%',
                            'rating'  => 5,
                            'reviews' => 28,
                            'badge'   => 'Exclusive',
                            'badgeColor' => 'bg-[#202a40]',
                        ],
                        [
                            'id'      => 10,
                            'image'   => 'https://images.unsplash.com/photo-1583292650898-7d22cd27ca6f?w=600&q=80',
                            'name'    => 'Ruby Cluster Earrings',
                            'brand'   => 'Trendymus Ruby',
                            'price'   => '£9,999',
                            'mrp'     => '£13,999',
                            'off'     => '29%',
                            'rating'  => 4,
                            'reviews' => 61,
                            'badge'   => null,
                            'badgeColor' => '',
                        ],
                        [
                            'id'      => 11,
                            'image'   => 'https://images.unsplash.com/photo-1553361371-9b22f78e8b1d?w=600&q=80',
                            'name'    => 'Rose Gold Chain Bracelet',
                            'brand'   => 'Trendymus Rose',
                            'price'   => '£7,499',
                            'mrp'     => '£10,499',
                            'off'     => '29%',
                            'rating'  => 5,
                            'reviews' => 93,
                            'badge'   => 'Trending',
                            'badgeColor' => 'bg-[#C9A96E]',
                        ],
                        [
                            'id'      => 12,
                            'image'   => 'https://images.unsplash.com/photo-1598560917807-1bae44bd2be8?w=600&q=80',
                            'name'    => 'Platinum Diamond Band',
                            'brand'   => 'Trendymus Platinum',
                            'price'   => '£42,999',
                            'mrp'     => '£59,999',
                            'off'     => '28%',
                            'rating'  => 5,
                            'reviews' => 19,
                            'badge'   => 'New',
                            'badgeColor' => 'bg-[#202a40]',
                        ],
                    ];
                @endphp

                <div
                    x-show="loaded"
                    x-transition:enter="transition-opacity duration-500"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    data-gsap="stagger-grid"
                    class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                    :class="grid === 2 ? 'xl:grid-cols-2' : 'xl:grid-cols-4'"
                >
                    @foreach ($products as $index => $product)
                        <div
                            data-gsap="stagger-item"
                            data-product-card
                            data-aos="fade-up"
                            data-aos-delay="{{ ($index % 4) * 80 }}"
                            x-data="{ wishlisted: false }"
                            class="group relative flex flex-col bg-white"
                        >
                            {{-- Image container --}}
                            <div class="relative overflow-hidden aspect-square bg-[#F5F3EF]">
                                <img
                                    src="{{ $product['image'] }}"
                                    alt="{{ $product['name'] }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    loading="lazy"
                                >

                                {{-- Discount badge --}}
                                @if ($product['badge'])
                                    <span class="{{ $product['badgeColor'] }} text-white text-[9px] tracking-widest uppercase px-2 py-1 absolute top-3 left-3 z-10 font-medium">
                                        {{ $product['badge'] }}
                                    </span>
                                @endif

                                {{-- Off badge --}}
                                <span class="absolute top-3 right-3 bg-[#111111] text-[#FAFAF8] text-[9px] tracking-widest uppercase px-2 py-1 z-10 font-medium">
                                    {{ $product['off'] }} off
                                </span>

                                {{-- Wishlist heart --}}
                                <button
                                    @click="wishlisted = !wishlisted"
                                    class="absolute top-3 right-3 z-20 p-1.5 bg-white/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white"
                                    :class="{ 'top-10': true }"
                                    style="top: calc(1.75rem + 28px);"
                                    aria-label="Add to wishlist"
                                >
                                    <svg
                                        class="w-4 h-4 transition-colors duration-200"
                                        :class="wishlisted ? 'text-[#202a40] fill-[#202a40]' : 'text-[#111111]/60'"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    >
                                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                                    </svg>
                                </button>

                                {{-- Quick view --}}
                                <div class="absolute bottom-0 inset-x-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-10">
                                    <button class="w-full py-3 bg-[#111111]/90 backdrop-blur-sm text-[#FAFAF8] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#202a40] transition-colors duration-300">
                                        Quick View
                                    </button>
                                </div>
                            </div>

                            {{-- Product info --}}
                            <div class="flex flex-col flex-1 p-3 pt-4">
                                <p class="text-[#202a40] text-[9px] tracking-[0.2em] uppercase mb-1 font-medium">{{ $product['brand'] }}</p>
                                <h3 class="text-[#111111] text-sm font-medium leading-snug mb-2 line-clamp-2">{{ $product['name'] }}</h3>

                                {{-- Stars --}}
                                <div class="flex items-center gap-1 mb-2">
                                    @for ($s = 0; $s < 5; $s++)
                                        <svg
                                            class="w-3 h-3 fill-current {{ $s < $product['rating'] ? 'text-[#C9A96E]' : 'text-[#111111]/15' }}"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-[#111111]/35 text-[9px] ml-0.5">({{ $product['reviews'] }})</span>
                                </div>

                                {{-- Price --}}
                                <div class="flex items-baseline gap-2 mb-3 mt-auto">
                                    <span class="text-[#111111] font-semibold text-sm">{{ $product['price'] }}</span>
                                    <span class="text-[#111111]/35 text-xs line-through">{{ $product['mrp'] }}</span>
                                </div>

                                {{-- Add to cart --}}
                                <button
                                    @click="$store.cart.add(1); window.flyToCart?.($el)"
                                    class="w-full py-2.5 border border-[#111111] text-[#111111] text-[10px] tracking-[0.2em] uppercase font-medium hover:bg-[#111111] hover:text-white transition-all duration-300 mt-auto"
                                >
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── PAGINATION ── --}}
            <nav
                aria-label="Pagination"
                data-gsap="fade-up"
                class="flex items-center justify-center gap-1 mt-16 pb-12"
            >
                {{-- Prev --}}
                <a
                    href="#"
                    class="inline-flex items-center justify-center w-9 h-9 border border-[#111111]/15 text-[#111111]/50 hover:border-[#202a40] hover:text-[#202a40] transition-all duration-200"
                    aria-label="Previous page"
                >
                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M10 12L6 8l4-4"/>
                    </svg>
                </a>

                {{-- Page 1 (active) --}}
                <a
                    href="#"
                    aria-current="page"
                    class="inline-flex items-center justify-center w-9 h-9 bg-[#111111] text-[#FAFAF8] text-sm font-medium"
                >1</a>

                @foreach ([2, 3, 4, 5] as $page)
                    <a
                        href="#"
                        class="inline-flex items-center justify-center w-9 h-9 border border-[#111111]/15 text-[#111111] text-sm hover:border-[#202a40] hover:text-[#202a40] transition-all duration-200"
                    >{{ $page }}</a>
                @endforeach

                {{-- Ellipsis --}}
                <span class="inline-flex items-center justify-center w-9 h-9 text-[#111111]/30 text-sm select-none">…</span>

                <a
                    href="#"
                    class="inline-flex items-center justify-center w-9 h-9 border border-[#111111]/15 text-[#111111] text-sm hover:border-[#202a40] hover:text-[#202a40] transition-all duration-200"
                >10</a>

                {{-- Next --}}
                <a
                    href="#"
                    class="inline-flex items-center justify-center w-9 h-9 border border-[#111111]/15 text-[#111111]/50 hover:border-[#202a40] hover:text-[#202a40] transition-all duration-200"
                    aria-label="Next page"
                >
                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 4l4 4-4 4"/>
                    </svg>
                </a>
            </nav>

        </main>
    </div>
</div>

</div>{{-- /x-data filterOpen --}}

@endsection
