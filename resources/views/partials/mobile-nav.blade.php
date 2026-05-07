<!-- Mobile Navigation Drawer -->
<div x-data="{ open: false }"
     @toggle-mobile-nav.window="open = !open"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="lg:hidden fixed inset-0 z-50"
     role="dialog"
     aria-modal="true"
     aria-label="Navigation menu">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

    <!-- Drawer -->
    <div x-show="open"
         x-transition:enter="transition-transform ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 w-[82vw] max-w-[320px] flex flex-col overflow-hidden"
         style="background:#fff; box-shadow: 4px 0 40px rgba(0,0,0,0.18);">

        <!-- ── HEADER with gradient ── -->
        <div style="background: linear-gradient(135deg, #202a40 0%, #8B4A55 100%); padding: 0;" class="shrink-0">
            <div class="flex items-center justify-between px-4 pt-4 pb-3">
                <a href="{{ route('home') }}" @click="open=false">
                    <span style="font-size:1.45rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;"><span style="color:#c8d6e5;">Trendy</span><span style="color:#7a9ab8;">mus</span></span>
                </a>
                <button @click="open = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-white/80 hover:text-white hover:bg-white/20 transition-colors"
                        aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- User info inside header -->
            @guest
                <div class="px-4 pb-4 flex gap-2">
                    <a href="{{ route('login') }}" @click="open=false"
                       class="flex-1 py-2 text-center text-[13px] font-semibold text-[#202a40] bg-white rounded-lg hover:bg-neutral-50 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" @click="open=false"
                       class="flex-1 py-2 text-center text-[13px] font-medium text-white border border-white/40 rounded-lg hover:bg-white/10 transition-colors">
                        Register
                    </a>
                </div>
            @else
                <div class="px-4 pb-4 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/20 border-2 border-white/40 flex items-center justify-center shrink-0 overflow-hidden">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="text-base font-bold text-white">{{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-[14px] font-semibold text-white truncate">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                        <div class="text-[11px] text-white/70 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            @endguest
        </div>

        <!-- ── SCROLLABLE NAV ── -->
        <nav class="flex-1 overflow-y-auto" style="scrollbar-width:none;">

            <!-- Quick Links -->
            <div class="px-3 pt-3 pb-1">
                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest px-1 mb-1">Quick Links</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('home') }}" @click="open=false"
                       class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('home') ? 'bg-[#202a40]/10 text-[#202a40]' : 'bg-neutral-50 text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40]' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Home
                    </a>
                    <a href="{{ route('new-arrivals') }}" @click="open=false"
                       class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] font-medium bg-neutral-50 text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        New Arrivals
                    </a>
                    <a href="{{ route('bestsellers') }}" @click="open=false"
                       class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] font-medium bg-neutral-50 text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Bestsellers
                    </a>
                    <a href="{{ route('deals') }}" @click="open=false"
                       class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] font-semibold bg-red-50 text-red-600 hover:bg-red-100 transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Sale 🔥
                    </a>
                </div>
            </div>

            <!-- Categories -->
            <div class="px-3 pt-4 pb-1">
                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest px-1 mb-2">Shop by Category</p>
                @foreach($navCategories ?? [] as $cat)
                    @if($cat->children->count())
                        <div x-data="{ expanded: false }" class="mb-1">
                            <button @click="expanded = !expanded"
                                    class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[13px] font-medium text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-all">
                                <span>{{ $cat->name }}</span>
                                <svg class="w-4 h-4 text-neutral-400 shrink-0 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="expanded" x-collapse class="pl-3 mt-0.5">
                                <a href="{{ route('categories.show', $cat) }}" @click="open=false"
                                   class="block px-3 py-2 text-[12px] font-semibold text-[#202a40] rounded-lg hover:bg-[#202a40]/5">
                                    View All {{ $cat->name }} →
                                </a>
                                @foreach($cat->children as $child)
                                    <a href="{{ route('categories.show', $child) }}" @click="open=false"
                                       class="block px-3 py-2 text-[12px] text-neutral-500 rounded-lg hover:text-[#202a40] hover:bg-[#202a40]/5 transition-colors">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route('categories.show', $cat) }}" @click="open=false"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13px] font-medium text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-all mb-1">
                            {{ $cat->name }}
                            <svg class="w-3.5 h-3.5 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                @endforeach
                <a href="{{ route('categories.index') }}" @click="open=false"
                   class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] font-semibold text-[#202a40] hover:bg-[#202a40]/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    All Categories
                </a>
            </div>

            <!-- Account -->
            @auth
            <div class="px-3 pt-4 pb-1">
                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest px-1 mb-2">My Account</p>
                <div class="rounded-2xl overflow-hidden border border-neutral-100">
                    <a href="{{ route('account.dashboard') }}" @click="open=false"
                       class="flex items-center gap-3 px-4 py-3 text-[13px] text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-colors border-b border-neutral-50">
                        <svg class="w-4.5 h-4.5 shrink-0 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('account.orders.index') }}" @click="open=false"
                       class="flex items-center gap-3 px-4 py-3 text-[13px] text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-colors border-b border-neutral-50">
                        <svg class="w-4.5 h-4.5 shrink-0 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        My Orders
                    </a>
                    <a href="{{ route('wishlist') }}" @click="open=false"
                       class="flex items-center gap-3 px-4 py-3 text-[13px] text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-colors border-b border-neutral-50">
                        <svg class="w-4.5 h-4.5 shrink-0 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Wishlist
                    </a>
                    <a href="{{ route('account.profile') }}" @click="open=false"
                       class="flex items-center gap-3 px-4 py-3 text-[13px] text-neutral-700 hover:bg-[#202a40]/5 hover:text-[#202a40] transition-colors">
                        <svg class="w-4.5 h-4.5 shrink-0 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile & Settings
                    </a>
                </div>
            </div>
            @endauth

            <!-- Bottom padding -->
            <div class="h-4"></div>
        </nav>

        <!-- ── FOOTER: Logout or social ── -->
        <div class="shrink-0 border-t border-neutral-100 bg-neutral-50 px-3 py-3">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-[13px] font-medium text-red-500 hover:bg-red-50 transition-colors">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sign Out
                    </button>
                </form>
            @else
                <p class="text-[11px] text-neutral-400 text-center">Premium Anti-Tarnish Jewellery ✦ Trendymus</p>
            @endauth
        </div>

    </div>
</div>
