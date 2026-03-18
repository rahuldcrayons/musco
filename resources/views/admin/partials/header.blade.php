<header class="h-14 flex items-center justify-between px-4 gap-4" style="background:#1a1a1a">
    <!-- Left: Mobile menu -->
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 text-neutral-400 hover:text-white" aria-label="Toggle menu">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <!-- Center: Search bar (Shopify style) -->
    <div class="flex-1 max-w-xl mx-auto" x-data="adminSearch()" @keydown.ctrl.k.window.prevent="openSearch()">
        <button @click="openSearch()"
                class="w-full flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-neutral-400 transition-colors" style="background:#303030">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="flex-1 text-left">Search</span>
            <span class="hidden sm:flex items-center gap-0.5 text-xs text-neutral-400">
                <kbd class="px-1 py-0.5 rounded text-xs" style="background:#444;color:#999">Ctrl</kbd>
                <kbd class="px-1 py-0.5 rounded text-xs" style="background:#444;color:#999">K</kbd>
            </span>
        </button>

        <!-- Search Modal (Shopify-style centered overlay) -->
        <div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-start justify-center" style="padding-top:80px" @keydown.escape.window="open = false">
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" @click="open = false" class="absolute inset-0" style="background:rgba(0,0,0,.4)"></div>
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative w-full bg-white overflow-hidden" style="max-width:680px;border-radius:14px;box-shadow:0 25px 50px -12px rgba(0,0,0,.25)">
                <!-- Search input -->
                <div class="flex items-center gap-3 px-5 py-3.5" style="border-bottom:1px solid #e5e5e5">
                    <svg class="w-5 h-5 text-neutral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-ref="searchInput" x-model="query" @input.debounce.300ms="search()" placeholder="Search" class="flex-1 text-base border-0 outline-none bg-transparent text-neutral-900 placeholder-neutral-400" style="font-size:16px">
                    <button @click="open = false" class="p-1 text-neutral-400 hover:text-neutral-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Category tabs -->
                <div class="flex items-center gap-1 px-5 py-2" style="border-bottom:1px solid #f0f0f0">
                    <template x-for="tab in tabs" :key="tab.key">
                        <button @click="section = tab.key; search()"
                                class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
                                :style="section === tab.key ? 'background:#e8e8e8;color:#1a1a1a' : 'color:#666'"
                                x-text="tab.label"></button>
                    </template>
                </div>

                <!-- Results -->
                <div class="px-5 py-4" style="min-height:120px;max-height:400px;overflow-y:auto">
                    <template x-if="!query && !loading">
                        <div class="flex flex-col items-center py-6 text-center">
                            <svg class="w-10 h-10 text-neutral-300 mb-3" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <p class="text-sm text-neutral-500">Find anything in {{ config('app.name') }}</p>
                        </div>
                    </template>
                    <template x-if="loading">
                        <div class="flex items-center justify-center py-8">
                            <svg class="animate-spin h-5 w-5 text-neutral-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </div>
                    </template>
                    <template x-if="query && !loading && results.length === 0">
                        <div class="py-6 text-center">
                            <p class="text-sm text-neutral-500">No results for "<span x-text="query" class="font-medium text-neutral-700"></span>"</p>
                        </div>
                    </template>
                    <template x-if="results.length > 0">
                        <div class="space-y-1">
                            <template x-for="item in results" :key="item.url">
                                <a :href="item.url" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-neutral-50 transition-colors">
                                    <template x-if="item.image">
                                        <img :src="item.image" class="w-8 h-8 rounded object-cover shrink-0" style="border:1px solid #e5e5e5">
                                    </template>
                                    <template x-if="!item.image">
                                        <div class="w-8 h-8 rounded flex items-center justify-center shrink-0" style="background:#f0f0f0">
                                            <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                    </template>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-neutral-900 truncate" x-text="item.title"></p>
                                        <p class="text-xs text-neutral-400" x-text="item.subtitle"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Notifications + User -->
    <div class="flex items-center gap-1">
        <!-- Notifications -->
        @php
            $adminUser = auth('admin')->user();
            $unreadNotifications = \App\Models\Notification::where('user_id', $adminUser->id)->unread()->latest()->limit(5)->get();
            $unreadCount = \App\Models\Notification::where('user_id', $adminUser->id)->unread()->count();
        @endphp
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 text-neutral-400 hover:text-white rounded-lg" aria-label="Notifications">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                @if($unreadCount > 0)
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full" style="background:#e74c3c"></span>
                @endif
            </button>
            <div x-cloak x-show="open" x-transition @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white border border-neutral-200 rounded-xl shadow-xl z-50">
                <div class="px-4 py-3 flex items-center justify-between" style="border-bottom:1px solid #eee">
                    <h3 class="font-semibold text-neutral-900 text-sm">Notifications</h3>
                    @if($unreadCount > 0)
                    <span class="text-xs font-medium" style="color:#2c6ecb">{{ $unreadCount }} new</span>
                    @endif
                </div>
                <div class="max-h-80 overflow-y-auto">
                    @forelse($unreadNotifications as $notification)
                    <a href="{{ route('admin.notifications.read', $notification) }}" class="block px-4 py-3 hover:bg-neutral-50" style="border-bottom:1px solid #f5f5f5">
                        <p class="text-sm font-medium text-neutral-900">{{ $notification->title }}</p>
                        <p class="text-xs text-neutral-500 mt-0.5 truncate">{{ $notification->content }}</p>
                        <p class="text-xs text-neutral-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                    @empty
                    <div class="p-4 text-center text-sm text-neutral-400">No new notifications</div>
                    @endforelse
                </div>
                <div class="px-4 py-2.5" style="border-top:1px solid #eee">
                    <a href="{{ route('admin.notifications') }}" class="text-xs font-medium" style="color:#2c6ecb">View all</a>
                </div>
            </div>
        </div>

        <!-- User menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-1.5" aria-label="User menu">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold text-white" style="background:#36a854">
                    {{ strtoupper(substr(auth('admin')->user()->full_name ?? auth('admin')->user()->email ?? 'A', 0, 1)) }}
                </div>
                <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-cloak x-show="open" x-transition @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-neutral-200 rounded-xl shadow-xl z-50">
                <div class="px-4 py-3" style="border-bottom:1px solid #eee">
                    <div class="text-sm font-medium text-neutral-900">{{ auth('admin')->user()->full_name ?? 'Admin' }}</div>
                    <div class="text-xs text-neutral-500">{{ auth('admin')->user()->email }}</div>
                </div>
                <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Profile</a>
                <a href="{{ url('/') }}" target="_blank" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">View Store</a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50" style="border-top:1px solid #eee">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
function adminSearch() {
    return {
        open: false,
        query: '',
        section: 'products',
        loading: false,
        results: [],
        tabs: [
            { key: 'products', label: 'Products' },
            { key: 'orders', label: 'Orders' },
            { key: 'customers', label: 'Customers' },
        ],
        openSearch() {
            this.open = true;
            this.$nextTick(() => this.$refs.searchInput.focus());
        },
        async search() {
            if (!this.query.trim()) { this.results = []; return; }
            this.loading = true;
            try {
                const res = await fetch(`/admin/search/${this.section}?search=${encodeURIComponent(this.query)}`);
                const data = await res.json();
                this.results = (data.data || data || []).slice(0, 8).map(item => {
                    if (this.section === 'products') {
                        return { title: item.name, subtitle: '₹' + item.price, image: item.primary_image_url || null, url: `/admin/products/${item.id}/edit` };
                    } else if (this.section === 'orders') {
                        return { title: item.order_number, subtitle: '₹' + item.total, image: null, url: `/admin/orders/${item.id}` };
                    } else {
                        return { title: (item.first_name || '') + ' ' + (item.last_name || ''), subtitle: item.email, image: null, url: `/admin/customers/${item.id}` };
                    }
                });
            } catch(e) { this.results = []; }
            this.loading = false;
        }
    };
}
</script>
