{{--
    Trendymus Premium Navbar
    Usage: @include('premium._navbar')
    Behaviour:
      - Transparent by default (for hero dark pages).
      - Adds .navbar--scrolled (bg-white/95 + backdrop-blur + shadow) on scroll via JS.
      - Mobile menu: full-screen overlay sliding from right.
--}}

<nav
    id="premium-navbar"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    x-data="{
        mobileOpen: false,
        scrolled: false,
        catOpen: null,
        init() {
            const onScroll = () => {
                this.scrolled = window.scrollY > 40;
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }
    }"
    :class="scrolled ? 'navbar--scrolled bg-white/95 backdrop-blur-md shadow-sm' : 'bg-transparent'"
    @keydown.escape.window="mobileOpen = false; catOpen = null"
>

    {{-- ── Announcement bar ── --}}
    <div
        class="overflow-hidden transition-all duration-300"
        :class="scrolled ? 'h-0 opacity-0' : 'h-8 opacity-100'"
    >
        <div class="bg-[#111111] text-white text-[11px] font-medium tracking-wider flex items-center justify-center h-8 px-4 gap-4">
            <span class="hidden sm:inline">✦ Free shipping on orders above £19</span>
            <span class="sm:hidden">Free shipping above £19</span>
            <span class="w-px h-3 bg-white/20 hidden sm:block"></span>
            <span class="hidden sm:inline">Easy 7-Day Returns</span>
            <span class="w-px h-3 bg-white/20 hidden md:block"></span>
            <span class="hidden md:inline">BIS Hallmarked Jewellery</span>
        </div>
    </div>

    {{-- ── Main bar ── --}}
    <div class="px-4 sm:px-6 lg:px-10 xl:px-16">
        <div class="flex items-center justify-between h-16 lg:h-20 max-w-[1440px] mx-auto">

            {{-- ──── Logo ──── --}}
            <a
                href="/"
                class="flex items-center shrink-0 z-10"
                aria-label="Trendymus Home"
            >
                <span
                    class="font-serif italic text-2xl sm:text-3xl leading-none transition-colors duration-300"
                    :class="scrolled ? 'text-[#202a40]' : 'text-[#202a40]'"
                    style="font-family:'Playfair Display',serif; letter-spacing:-0.01em;"
                >Trendymus</span>
            </a>

            {{-- ──── Desktop Nav ──── --}}
            <div
                class="hidden lg:flex items-center gap-0 flex-1 justify-center"
                @mouseleave="catOpen = null"
            >

                {{-- Collections dropdown --}}
                <div class="relative" @mouseenter="catOpen = 'collections'">
                    <button
                        class="nav-link group relative px-4 xl:px-5 py-6 text-[13px] font-medium transition-colors duration-200 flex items-center gap-1"
                        :class="scrolled ? 'text-[#111111]/75 hover:text-[#202a40]' : 'text-white/85 hover:text-white'"
                        :aria-expanded="catOpen === 'collections'"
                    >
                        Collections
                        <svg class="w-3 h-3 transition-transform duration-200" :class="catOpen === 'collections' ? 'rotate-180' : ''" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4l4 4 4-4"/></svg>
                        {{-- Hover underline --}}
                        <span class="absolute bottom-4 left-4 right-4 h-px bg-[#202a40] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out" :class="catOpen === 'collections' ? 'scale-x-100' : ''"></span>
                    </button>

                    {{-- Mega dropdown --}}
                    <div
                        x-show="catOpen === 'collections'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        @mouseenter="catOpen = 'collections'"
                        class="absolute top-full left-1/2 -translate-x-1/2 mt-0 w-[580px] bg-white rounded-2xl shadow-2xl border border-[#111111]/6 p-6 z-50"
                    >
                        <div class="grid grid-cols-3 gap-x-8 gap-y-1 mb-5">
                            @foreach([
                                'Rings', 'Necklaces', 'Earrings',
                                'Bracelets', 'Bangles', 'Nose Pins',
                                'Pendants', 'Anklets', 'Mangalsutra',
                                'Men\'s Jewellery', 'Kids Jewellery', 'Wedding Sets',
                            ] as $cat)
                            <a
                                href="/categories/{{ strtolower(str_replace(['\'', ' '], ['', '-'], $cat)) }}"
                                class="group flex items-center gap-2 py-2 px-2.5 rounded-lg hover:bg-[#FDF2F3] transition-colors text-sm text-[#111111]/65 hover:text-[#202a40] font-medium"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-[#202a40]/25 group-hover:bg-[#202a40] transition-colors shrink-0"></span>
                                {{ $cat }}
                            </a>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-[#111111]/6">
                            <a href="/collections/new-arrivals" class="flex items-center gap-3 p-3 bg-gradient-to-br from-[#FDF2F3] to-[#F9E0E3] rounded-xl group hover:shadow-md transition-all">
                                <div class="w-9 h-9 rounded-full bg-[#202a40]/15 flex items-center justify-center shrink-0 group-hover:bg-[#202a40]/25 transition-colors">
                                    <svg class="w-4 h-4 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1v14M1 8h14"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#111111]">New Arrivals</p>
                                    <p class="text-[10px] text-[#111111]/40">Latest pieces</p>
                                </div>
                            </a>
                            <a href="/collections/sale" class="flex items-center gap-3 p-3 bg-gradient-to-br from-[#FFF9F0] to-[#FDF0D5] rounded-xl group hover:shadow-md transition-all">
                                <div class="w-9 h-9 rounded-full bg-[#C9A96E]/15 flex items-center justify-center shrink-0 group-hover:bg-[#C9A96E]/25 transition-colors">
                                    <svg class="w-4 h-4 text-[#C9A96E]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 2l12 12M5 3h-3v3M11 13h3v-3"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#111111]">Sale</p>
                                    <p class="text-[10px] text-[#111111]/40">Up to 40% off</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Individual nav links --}}
                @foreach([
                    ['New Arrivals', '/collections/new-arrivals'],
                    ['Gold', '/collections/gold'],
                    ['Diamond', '/collections/diamond'],
                    ['Silver', '/collections/silver'],
                    ['Gifting', '/collections/gift-sets'],
                ] as [$label, $href])
                <a
                    href="{{ $href }}"
                    class="nav-link group relative px-4 xl:px-5 py-6 text-[13px] font-medium transition-colors duration-200"
                    :class="scrolled ? 'text-[#111111]/75 hover:text-[#202a40]' : 'text-white/85 hover:text-white'"
                >
                    {{ $label }}
                    <span class="absolute bottom-4 left-4 right-4 h-px bg-[#202a40] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out"></span>
                </a>
                @endforeach

                <a
                    href="/sale"
                    class="nav-link group relative px-4 xl:px-5 py-6 text-[13px] font-semibold transition-colors duration-200 text-[#202a40]"
                >
                    Sale
                    <span class="absolute bottom-4 left-4 right-4 h-px bg-[#202a40] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out"></span>
                </a>

            </div>{{-- end desktop nav --}}

            {{-- ──── Right Icons ──── --}}
            <div class="flex items-center gap-1 sm:gap-1.5 z-10">

                {{-- Search --}}
                <a
                    href="/search"
                    class="w-9 h-9 flex items-center justify-center rounded-full transition-colors duration-200"
                    :class="scrolled ? 'text-[#111111]/60 hover:text-[#202a40] hover:bg-[#202a40]/8' : 'text-white/75 hover:text-white hover:bg-white/10'"
                    aria-label="Search"
                >
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.5 17.5l-4.5-4.5m1-4.5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </a>

                {{-- Wishlist --}}
                <a
                    href="/wishlist"
                    class="relative w-9 h-9 hidden sm:flex items-center justify-center rounded-full transition-colors duration-200"
                    :class="scrolled ? 'text-[#111111]/60 hover:text-[#202a40] hover:bg-[#202a40]/8' : 'text-white/75 hover:text-white hover:bg-white/10'"
                    aria-label="Wishlist"
                >
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                    </svg>
                    <span
                        id="wishlist-count"
                        class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#202a40] text-white text-[9px] font-bold rounded-full items-center justify-center hidden"
                    >0</span>
                </a>

                {{-- Cart --}}
                <a
                    id="cart-icon"
                    href="/cart"
                    class="relative w-9 h-9 flex items-center justify-center rounded-full transition-colors duration-200"
                    :class="scrolled ? 'text-[#111111]/60 hover:text-[#202a40] hover:bg-[#202a40]/8' : 'text-white/75 hover:text-white hover:bg-white/10'"
                    aria-label="Shopping cart"
                >
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 2L3 6v14a2 2 0 002 2h10a2 2 0 002-2V6l-3-4zM3 6h14M13 10a3 3 0 11-6 0"/>
                    </svg>
                    <span
                        id="cart-count"
                        class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#202a40] text-white text-[9px] font-bold rounded-full items-center justify-center hidden"
                    >0</span>
                </a>

                {{-- User (desktop) --}}
                <div
                    class="relative hidden lg:block"
                    x-data="{ userOpen: false }"
                    @click.outside="userOpen = false"
                >
                    <button
                        @click="userOpen = !userOpen"
                        class="w-9 h-9 flex items-center justify-center rounded-full transition-colors duration-200"
                        :class="scrolled ? 'text-[#111111]/60 hover:text-[#202a40] hover:bg-[#202a40]/8' : 'text-white/75 hover:text-white hover:bg-white/10'"
                        aria-label="Account"
                        :aria-expanded="userOpen"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </button>
                    <div
                        x-show="userOpen"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-full mt-2 w-52 bg-white border border-[#111111]/8 rounded-2xl shadow-xl overflow-hidden z-50"
                    >
                        @guest
                        <div class="p-4 border-b border-[#111111]/6">
                            <p class="text-xs text-[#111111]/50 mb-3">Sign in for a personalised experience</p>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="/login" class="flex items-center justify-center px-3 py-2 bg-[#202a40] text-white text-xs font-semibold rounded-lg hover:bg-[#a05c68] transition-colors">Login</a>
                                <a href="/register" class="flex items-center justify-center px-3 py-2 border border-[#202a40] text-[#202a40] text-xs font-semibold rounded-lg hover:bg-[#202a40]/5 transition-colors">Register</a>
                            </div>
                        </div>
                        @else
                        <div class="px-4 py-3.5 border-b border-[#111111]/6">
                            <p class="text-sm font-semibold text-[#111111]">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                            <p class="text-xs text-[#111111]/40 mt-0.5 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        @endguest
                        <div class="py-1.5">
                            <a href="/account" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#111111]/65 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5" r="3"/><path d="M2 15a6 6 0 0112 0"/></svg>
                                My Account
                            </a>
                            <a href="/account/orders" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#111111]/65 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="12" height="12" rx="1.5"/><path d="M5 6h6M5 9h4"/></svg>
                                My Orders
                            </a>
                            <a href="/wishlist" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#111111]/65 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 13.5S1.5 9.5 1.5 5.5a3.5 3.5 0 017 0 3.5 3.5 0 017 0c0 4-6.5 8-6.5 8z"/></svg>
                                Wishlist
                            </a>
                        </div>
                        @auth
                        <div class="border-t border-[#111111]/6 py-1.5">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-[#111111]/40 hover:text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 3H3a1 1 0 00-1 1v8a1 1 0 001 1h3M10 11l3-3-3-3M13 8H6"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                        @endauth
                    </div>
                </div>

                {{-- Mobile hamburger --}}
                <button
                    @click="mobileOpen = true"
                    class="lg:hidden w-9 h-9 flex items-center justify-center rounded-full transition-colors duration-200 ml-1"
                    :class="scrolled ? 'text-[#111111]/60 hover:text-[#202a40] hover:bg-[#202a40]/8' : 'text-white/80 hover:text-white hover:bg-white/10'"
                    aria-label="Open menu"
                >
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" d="M3 5h14M3 10h14M3 15h14"/>
                    </svg>
                </button>

            </div>{{-- end right icons --}}

        </div>
    </div>

    {{-- ═══════════════════════════════════
         MOBILE MENU — Full-screen overlay
    ════════════════════════════════════ --}}
    {{-- Backdrop --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-250"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileOpen = false"
        class="fixed inset-0 bg-[#111111]/60 z-40 lg:hidden"
        aria-hidden="true"
    ></div>

    {{-- Slide-from-right panel --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition-transform duration-350 ease-out"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-280 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 bottom-0 w-[85vw] max-w-sm bg-white z-50 flex flex-col shadow-2xl lg:hidden overflow-y-auto"
        @click.stop
    >
        {{-- Panel header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-[#111111]/8">
            <a href="/" class="font-serif italic text-2xl text-[#202a40]" style="font-family:'Playfair Display',serif;">Trendymus</a>
            <button
                @click="mobileOpen = false"
                class="w-9 h-9 rounded-full flex items-center justify-center text-[#111111]/40 hover:text-[#111111] hover:bg-[#111111]/5 transition-colors"
                aria-label="Close menu"
            >
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" d="M5 5l10 10M15 5L5 15"/>
                </svg>
            </button>
        </div>

        {{-- Search bar --}}
        <div class="px-5 pt-5 pb-3">
            <a
                href="/search"
                class="flex items-center gap-3 w-full px-4 py-3 bg-[#F3EEE9] rounded-xl text-sm text-[#111111]/40 hover:bg-[#EBE0D5] transition-colors"
            >
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 14l-3.5-3.5M9 2a7 7 0 100 10A7 7 0 009 2z"/></svg>
                Search jewellery...
            </a>
        </div>

        {{-- Navigation links --}}
        <nav class="flex-1 px-3 py-2 space-y-0.5">

            {{-- Collections accordion --}}
            <div x-data="{ open: false }">
                <button
                    @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-3.5 rounded-xl text-[#111111] font-semibold text-sm hover:bg-[#F3EEE9] transition-colors"
                >
                    Collections
                    <svg class="w-4 h-4 text-[#111111]/35 transition-transform duration-200" :class="open ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
                </button>
                <div x-show="open" x-collapse class="pl-4">
                    <div class="grid grid-cols-2 gap-0.5 py-2">
                        @foreach(['Rings', 'Necklaces', 'Earrings', 'Bracelets', 'Bangles', 'Nose Pins', 'Pendants', 'Anklets', 'Mangalsutra', 'Men\'s', 'Kids', 'Wedding Sets'] as $cat)
                        <a
                            href="/categories/{{ strtolower(str_replace(['\'', ' '], ['', '-'], $cat)) }}"
                            class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm text-[#111111]/65 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-[#202a40]/25 shrink-0"></span>
                            {{ $cat }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @foreach([
                ['New Arrivals', '/collections/new-arrivals', 'M8 1v14M1 8h14'],
                ['Gold Jewellery', '/collections/gold', 'M8 1l1.5 3h3l-2.5 2 1 3L8 7.5 5 9l1-3L3.5 4h3z'],
                ['Diamond', '/collections/diamond', 'M2 6l6-4 6 4-6 8-6-8z'],
                ['Silver', '/collections/silver', 'M4 2h8l2 4-6 8-6-8 2-4z'],
                ['Gifting', '/collections/gift-sets', 'M4 10V7M8 10V4M12 10V7M2 10h12M2 13h12M4 4a2 2 0 012-2h4a2 2 0 012 2'],
            ] as [$label, $href, $icon])
            <a
                href="{{ $href }}"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-[#111111] font-semibold text-sm hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors group"
            >
                <span class="w-7 h-7 rounded-lg bg-[#F3EEE9] flex items-center justify-center shrink-0 group-hover:bg-[#202a40]/10 transition-colors">
                    <svg class="w-3.5 h-3.5 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="{{ $icon }}"/></svg>
                </span>
                {{ $label }}
            </a>
            @endforeach

            <a
                href="/sale"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-[#202a40] font-bold text-sm hover:bg-[#FDF2F3] transition-colors group"
            >
                <span class="w-7 h-7 rounded-lg bg-[#202a40]/10 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 2l12 12M5 3H2v3M11 13h3v-3"/></svg>
                </span>
                Sale — Up to 40% off
            </a>

        </nav>

        {{-- Divider --}}
        <div class="mx-5 border-t border-[#111111]/6"></div>

        {{-- Footer links --}}
        <div class="px-3 py-4 space-y-0.5">
            <a href="/account" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-[#111111]/60 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5" r="3"/><path d="M2 15a6 6 0 0112 0"/></svg>
                My Account
            </a>
            <a href="/account/orders" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-[#111111]/60 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="12" height="12" rx="1.5"/><path d="M5 6h6M5 9h4"/></svg>
                My Orders
            </a>
            <a href="/wishlist" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-[#111111]/60 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 13.5S1.5 9.5 1.5 5.5a3.5 3.5 0 017 0 3.5 3.5 0 017 0c0 4-6.5 8-6.5 8z"/></svg>
                Wishlist
            </a>
            <a href="/contact" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-[#111111]/60 hover:text-[#202a40] hover:bg-[#FDF2F3] transition-colors">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 3h12a1 1 0 011 1v7a1 1 0 01-1 1H2a1 1 0 01-1-1V4a1 1 0 011-1zM1 4l7 5 7-5"/></svg>
                Contact Us
            </a>
        </div>

        {{-- CTA --}}
        <div class="px-5 pb-7 pt-3">
            <a
                href="/collections/new-arrivals"
                class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-[#202a40] to-[#C9A96E] text-white text-sm font-bold rounded-xl shadow-md"
            >
                Explore New Arrivals
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
            </a>
        </div>

    </div>{{-- end mobile panel --}}

</nav>

{{-- Spacer so content doesn't hide under fixed navbar --}}
<div id="premium-navbar-spacer" class="h-16 lg:h-20" aria-hidden="true"></div>

<script>
    (function () {
        // Sync spacer height with actual navbar height
        function syncNavbarSpacer() {
            var nav = document.getElementById('premium-navbar');
            var spacer = document.getElementById('premium-navbar-spacer');
            if (nav && spacer) {
                spacer.style.height = nav.offsetHeight + 'px';
            }
        }
        // Update cart/wishlist counts from store if available
        function syncBadges() {
            var cartCount = document.getElementById('cart-count');
            var wishlistCount = document.getElementById('wishlist-count');
            // Check Alpine store
            if (window.Alpine && window.Alpine.store) {
                try {
                    var cartN = Alpine.store('cart')?.itemCount || 0;
                    var wishN = Alpine.store('wishlist')?.count || 0;
                    if (cartCount) {
                        cartCount.textContent = cartN;
                        cartCount.style.display = cartN > 0 ? 'flex' : 'none';
                    }
                    if (wishlistCount) {
                        wishlistCount.textContent = wishN;
                        wishlistCount.style.display = wishN > 0 ? 'flex' : 'none';
                    }
                } catch (e) {}
            }
        }
        window.addEventListener('resize', syncNavbarSpacer);
        document.addEventListener('DOMContentLoaded', function () {
            syncNavbarSpacer();
            syncBadges();
        });
        document.addEventListener('alpine:initialized', syncBadges);
        window.addEventListener('cart-updated', syncBadges);
        window.addEventListener('wishlist-updated', syncBadges);
    })();
</script>
