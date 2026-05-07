<style>
@media (max-width: 639px) {
    .search-dropdown {
        position: fixed !important;
        top: 58px !important;
        left: 8px !important;
        right: 8px !important;
        width: auto !important;
        margin-top: 0 !important;
    }
}
</style>
@php
    $announcement = \App\Models\Setting::get('announcement_text', 'Free Shipping on Orders Above £30 | 100% Anti-Tarnish Guarantee | Easy 7-Day Returns');

    // Load jewellery subcategories as nav items — cached 10 min
    [$navCategories, $navTrending] = cache()->remember('nav.header.data', 600, function () {
        $jewelleryParent = \App\Models\Category::where('slug', 'jewellery')->first();
        if ($jewelleryParent) {
            $cats = \App\Models\Category::where('is_active', true)
                ->where('parent_id', $jewelleryParent->id)
                ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('position')->take(8)])
                ->orderBy('position')->take(20)->get();
        } else {
            $cats = \App\Models\Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('position')->take(8)])
                ->orderBy('position')->take(20)->get();
        }

        // Batch all category+children IDs into ONE query instead of N queries
        $allIds = $cats->flatMap(fn($nc) => collect([$nc->id])->merge($nc->children->pluck('id')))->unique()->values();
        $allProducts = \App\Models\Product::whereIn('category_id', $allIds)
            ->where('is_active', true)->with('primaryImage')
            ->orderByDesc('is_featured')->get();

        $trending = [];
        foreach ($cats as $nc) {
            $ids = collect([$nc->id])->merge($nc->children->pluck('id'));
            $trending[$nc->id] = $allProducts->whereIn('category_id', $ids->all())->take(4)->values();
        }

        return [$cats, $trending];
    });

@endphp

<header id="main-header" x-data="{ visible: true, lastScroll: 0 }"
       x-on:scroll.window="
           let y = window.scrollY;
           if (y < 60) { visible = true }
           else if (y < lastScroll) { visible = true }
           else if (y > lastScroll + 5) { visible = false }
           lastScroll = y;
       "
       class="bg-white fixed left-0 right-0 z-40"
       :style="'transition: top 0.3s ease; top: ' + (visible ? '0px' : '-80px')">

    {{-- Marquee Announcement Bar (commented out)
    @if($announcement)
    <div class="bg-[#222222] text-white overflow-hidden h-8 flex items-center">
        <div class="marquee-track">
            <div class="marquee-content">
                @for($i = 0; $i < 4; $i++)
                    @foreach(explode('|', $announcement) as $msg)
                        <span class="marquee-item">{{ trim($msg) }}</span>
                        <span class="marquee-separator">✦</span>
                    @endforeach
                @endfor
            </div>
        </div>
    </div>
    @endif
    --}}

    <!-- Main Header Row -->
    <div class="border-b border-neutral-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-14 lg:h-16">

                <!-- Left: Mobile menu -->
                <div class="flex items-center lg:hidden shrink-0 mr-1">
                    <button @click="$dispatch('toggle-mobile-nav')" class="p-1.5 -ml-1.5 text-neutral-700 hover:text-[#202a40]" aria-label="Open menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Center: Logo (hidden on mobile) -->
                <a href="{{ route('home') }}" class="hidden sm:flex items-center shrink-0 lg:mr-8">
                    @php $siteLogo = \App\Models\Setting::get('site_logo', ''); @endphp
                    <span style="font-size:1.45rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;"><span style="color:#1e2a40;">Trendy</span><span style="color:#5a6e8a;">mus</span></span>
                </a>

                <!-- Desktop Navigation with Mega Menu -->
                <nav class="hidden lg:flex items-center gap-0 shrink-0">
                    @foreach($navCategories->take(9) as $navCat)
                        @php
                            $shortName = match(true) {
                                str_contains($navCat->name, 'Necklaces & Pendants') => 'Necklaces',
                                str_contains($navCat->name, 'Bangles & Bracelets')  => 'Bangles+',
                                str_contains($navCat->name, 'Nose Pins')            => 'Nose Pins',
                                str_contains($navCat->name, 'Pendant Sets')         => 'Pendants',
                                str_contains($navCat->name, 'Accessories')          => 'Accessories',
                                default => $navCat->name,
                            };
                            $trendingItems = $navTrending[$navCat->id] ?? collect();
                            $children      = $navCat->children ?? collect();
                            $col1          = $children->slice(0, 5);
                            $col2          = $children->slice(5, 5);
                            $currentSlug   = request()->route('category')?->slug ?? '';
                            $childSlugs    = $navCat->children->pluck('slug')->toArray();
                            $isActive      = $currentSlug === $navCat->slug || in_array($currentSlug, $childSlugs);
                        @endphp

                        <div class="relative shrink-0"
                             x-data="{ open: false, t: null, active: {{ $isActive ? 'true' : 'false' }} }"
                             @mouseenter="clearTimeout(t); open = true"
                             @mouseleave="t = setTimeout(() => open = false, 200)">

                            {{-- Nav trigger link --}}
                            <a href="{{ route('categories.show', $navCat) }}"
                               class="relative flex items-center gap-0.5 px-1.5 py-5 text-[14px] font-medium whitespace-nowrap transition-colors"
                               :class="(open || active) ? 'text-[#202a40]' : 'text-[#555]'">
                                {{ $shortName }}
                                <svg class="w-2 h-2 opacity-40 shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                                {{-- Bottom active/hover bar --}}
                                <span class="absolute bottom-0 left-0 right-0 h-[2px] bg-[#202a40] rounded-t transition-all duration-150 opacity-0 scale-x-0"
                                      :class="(open || active) ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0'"></span>
                            </a>

                            {{-- Mega panel teleported to <body> — escapes header stacking context entirely --}}
                            <template x-teleport="body">
                                {{-- Backdrop: starts BELOW the header so it never covers nav triggers.
                                     pointer-events:none so it doesn't intercept mouse — only visual dim. --}}
                                <div x-show="open"
                                     style="display:none;position:fixed;left:0;right:0;bottom:0;z-index:9997;background:rgba(0,0,0,0.2);pointer-events:none;"
                                     :style="'top:' + (document.getElementById('main-header')?.offsetHeight ?? 64) + 'px'"></div>

                                {{-- Panel --}}
                                <div x-show="open"
                                     style="display:none;position:fixed;left:0;right:0;z-index:9999;background:#fff;border-top:2px solid #202a40;box-shadow:0 8px 40px rgba(0,0,0,0.12);"
                                     :style="'top:' + (document.getElementById('main-header')?.offsetHeight ?? 64) + 'px'"
                                     @mouseenter="clearTimeout(t)"
                                     @mouseleave="t = setTimeout(() => open = false, 200)">

                                    <div class="container mx-auto px-4">
                                        <div class="flex" style="min-height:240px;">

                                            {{-- Left: subcategory columns --}}
                                            <div class="flex flex-1 divide-x divide-neutral-100 py-6">

                                                @if($col1->isNotEmpty())
                                                <div class="pr-6 flex-1">
                                                    <p class="text-[11px] font-bold text-[#202a40] uppercase tracking-widest mb-3">{{ $shortName }}</p>
                                                    <ul class="space-y-0.5">
                                                        @foreach($col1 as $child)
                                                        <li>
                                                            <a href="{{ route('categories.show', $child) }}"
                                                               class="flex items-center gap-2.5 py-1.5 text-[13px] text-neutral-600 hover:text-[#202a40] transition-colors group">
                                                                {{-- Left bar indicator on sub-item hover --}}
                                                                <span class="w-[3px] h-4 rounded-full bg-[#202a40] opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></span>
                                                                {{ $child->name }}
                                                            </a>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif

                                                @if($col2->isNotEmpty())
                                                <div class="px-6 flex-1">
                                                    <p class="text-[11px] font-bold text-neutral-300 uppercase tracking-widest mb-3">&nbsp;</p>
                                                    <ul class="space-y-0.5">
                                                        @foreach($col2 as $child)
                                                        <li>
                                                            <a href="{{ route('categories.show', $child) }}"
                                                               class="flex items-center gap-2.5 py-1.5 text-[13px] text-neutral-600 hover:text-[#202a40] transition-colors group">
                                                                <span class="w-[3px] h-4 rounded-full bg-[#202a40] opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></span>
                                                                {{ $child->name }}
                                                            </a>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif

                                                {{-- Trending --}}
                                                <div class="px-6 flex-1">
                                                    <p class="text-[11px] font-bold text-[#202a40] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                                        <span>🔥</span> Trending
                                                    </p>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        @forelse($trendingItems->take(4) as $tp)
                                                        <a href="{{ route('product.show', $tp) }}" class="group">
                                                            <div class="aspect-square rounded-lg overflow-hidden bg-neutral-50 border border-neutral-100 group-hover:border-[#202a40]/30 transition-colors mb-1">
                                                                <img src="{{ $tp->primary_image_url }}" alt="{{ $tp->name }}"
                                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                                     onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                                                            </div>
                                                            <p class="text-[11px] text-neutral-600 group-hover:text-[#202a40] transition-colors line-clamp-1">{{ Str::limit($tp->name, 22) }}</p>
                                                            <p class="text-[12px] font-bold text-[#202a40]">£{{ number_format($tp->price, 2) }}</p>
                                                        </a>
                                                        @empty
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Right: CTA sidebar --}}
                                            <div class="w-36 shrink-0 bg-gradient-to-b from-[#f5f7fa] to-[#eef1f5] flex flex-col items-center justify-center py-6 border-l border-neutral-100 gap-3">
                                                <p class="text-xs font-bold text-[#202a40] uppercase tracking-wider text-center">{{ $shortName }}</p>
                                                <a href="{{ route('categories.show', $navCat) }}"
                                                   class="px-4 py-2 bg-[#202a40] text-white text-[11px] font-bold rounded-full uppercase tracking-wider hover:bg-[#2d3a55] transition-colors text-center">
                                                    View All
                                                </a>
                                                <a href="{{ route('products.index') }}?category={{ $navCat->slug }}&on_sale=1"
                                                   class="px-4 py-1.5 border border-[#202a40] text-[#202a40] text-[11px] font-semibold rounded-full hover:bg-neutral-100 transition-colors text-center">
                                                    On Sale
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    @endforeach

                    <a href="{{ route('deals') }}" class="nav-link shrink-0 px-1.5 py-5 text-[14px] font-semibold text-[#202a40] hover:text-[#2d3a55] transition-colors whitespace-nowrap">
                        Sale
                    </a>
                </nav>

                <!-- Right: Search + Icons -->
                <div class="flex items-center gap-2 flex-1 lg:flex-none justify-end min-w-0 ml-3">

                    <!-- Inline Search Bar -->
                    <div class="relative min-w-0 lg:w-[180px] xl:w-[220px]"
                         x-data="searchBar()"
                         @click.outside="showResults = false">
                        <form action="{{ route('search') }}" method="GET" class="relative flex items-center"
                              @submit.prevent="if(query.trim()) window.location.href='{{ route('search') }}?q='+encodeURIComponent(query.trim())">
                            <svg class="absolute left-3 w-4 h-4 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="q" x-ref="searchInput" x-model="query"
                                   @input.debounce.150ms="fetchSuggestions()"
                                   @focus="showResults = true; stopTypewriter()"
                                   @blur="if(!query) startTypewriter()"
                                   @keydown.escape="showResults = false; $refs.searchInput.blur()"
                                   :placeholder="currentPlaceholder"
                                   class="w-full pl-9 pr-10 py-2 text-sm bg-neutral-50 border border-neutral-200 rounded-full placeholder-neutral-400 focus:bg-white focus:border-[#202a40] transition-all"
                                   style="outline:none !important; box-shadow:none !important; font-size:14px;"
                                   autocomplete="off">
                            <button type="button" @click="if(query.trim()) window.location.href='{{ route('search') }}?q='+encodeURIComponent(query.trim())" class="absolute right-1 w-7 h-7 flex items-center justify-center bg-[#202a40] text-white rounded-full transition-colors shadow-sm" aria-label="Search">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </form>

                        <!-- Live Search Dropdown -->
                        <div x-show="showResults && query.length >= 1" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="search-dropdown absolute top-full mt-4 mr-1 bg-white border border-neutral-200 rounded-2xl shadow-2xl z-[9999] overflow-hidden"
                             style="width:380px; right:-1px; left:auto;">

                            <!-- Loading -->
                            <div x-show="loading" class="flex items-center gap-2 px-4 py-3 text-sm text-neutral-400">
                                <svg class="w-4 h-4 animate-spin text-[#202a40]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                Searching...
                            </div>

                            <!-- Results -->
                            <template x-if="!loading && results.length > 0">
                                <div>
                                    <!-- Products -->
                                    <template x-if="results.filter(r => r.type === 'product').length > 0">
                                        <div>
                                            <div class="px-4 pt-3 pb-1">
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400">Products</span>
                                            </div>
                                            <template x-for="result in results.filter(r => r.type === 'product').slice(0,6)" :key="result.id">
                                                <a :href="result.url" class="flex items-center gap-3 px-4 py-2 hover:bg-[#FDF2F3] transition-colors group">
                                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-neutral-100 shrink-0 border border-neutral-100">
                                                        <img :src="result.image" :alt="result.name" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                                                    </div>
                                                    <div class="flex-1 min-w-0 overflow-hidden">
                                                        <p class="text-[13px] text-neutral-800 group-hover:text-[#202a40] transition-colors leading-tight" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:270px;" x-text="result.name"></p>
                                                        <p class="text-[11px] text-neutral-400 mt-0.5" x-text="result.category"></p>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>


                                </div>
                            </template>

                            <!-- No results -->
                            <div x-show="!loading && results.length === 0 && query.length >= 1" class="px-4 py-5 text-center">
                                <p class="text-sm text-neutral-500">No results for "<span x-text="query" class="font-medium text-neutral-700"></span>"</p>
                            </div>
                        </div>
                    </div>


                    <!-- Wishlist -->
                    <a href="{{ route('wishlist') }}" class="relative shrink-0 w-8 h-8 flex items-center justify-center text-neutral-600 hover:text-[#202a40] transition-colors ml-1" aria-label="Wishlist">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span x-cloak x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count"
                              class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#202a40] text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
                    </a>

                    <!-- User account -->
                    @guest
                        <button @click="$store.authModal.open('login', '/account')"
                                class="shrink-0 w-8 h-8 flex items-center justify-center text-neutral-600 hover:text-[#202a40] hover:bg-neutral-50 rounded-full transition-colors" aria-label="Login">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </button>
                    @else
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click.stop="open = !open"
                                    class="flex items-center gap-1 p-1 rounded-full hover:bg-neutral-50 active:bg-neutral-100 transition-colors focus:outline-none shrink-0"
                                    aria-label="My Account" aria-haspopup="true" :aria-expanded="open">
                                @if(Auth::user()->avatar_url ?? null)
                                    <img src="{{ Storage::disk('public')->url(Auth::user()->avatar_url) }}" alt="{{ Auth::user()->first_name }}" class="w-7 h-7 rounded-full object-cover">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-[#202a40] flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <svg class="w-3 h-3 text-neutral-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open"
                                 style="display:none;z-index:99999; top:100%; right:-1px; min-width:160px; width:auto;"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute mt-2 bg-white rounded-2xl shadow-2xl border border-neutral-100 overflow-hidden origin-top-right py-1 whitespace-nowrap">

                                <!-- Profile -->
                                <a href="{{ route('account.profile') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-neutral-700 hover:bg-[#FDF2F3] hover:text-[#202a40] transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile
                                </a>

                                <div class="border-t border-neutral-100 mt-1"></div>

                                <!-- Logout -->
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest

                    <!-- Cart -->
                    <a id="cart-icon" href="{{ route('cart.index') }}" class="relative shrink-0 w-8 h-8 flex items-center justify-center text-neutral-700 hover:text-[#202a40] transition-colors" aria-label="Cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span x-cloak x-show="$store.cart.itemCount > 0" x-text="$store.cart.itemCount"
                              class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#202a40] text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>


</header>

<!-- Spacer for fixed header -->
<div id="header-spacer"
     class="{{ $announcement ? 'h-[88px] lg:h-[96px]' : 'h-14 lg:h-16' }}"
     aria-hidden="true"></div>
<script>
    (function () {
        function syncSpacer() {
            var hdr = document.getElementById('main-header');
            var spc = document.getElementById('header-spacer');
            if (hdr && spc) spc.style.height = hdr.offsetHeight + 'px';
        }
        syncSpacer();
        window.addEventListener('resize', syncSpacer);
    })();
</script>
