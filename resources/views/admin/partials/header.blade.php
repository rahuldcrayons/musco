<header class="h-16 bg-white border-b border-neutral-200 flex items-center justify-between px-6">
    <!-- Left side -->
    <div class="flex items-center gap-4">
        <!-- Mobile menu toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 text-neutral-600 hover:text-neutral-900" aria-label="Toggle menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Page title -->
        <h1 class="text-lg font-semibold text-neutral-900">{{ $title ?? 'Dashboard' }}</h1>
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-4">
        <!-- Quick search -->
        <div class="hidden md:block relative" x-data="{ open: false, query: '', section: 'products' }">
            <button @click="open = !open; $nextTick(() => $refs.searchInput.focus())" class="p-2 text-neutral-600 hover:text-neutral-900" aria-label="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
            <div x-cloak x-show="open" x-transition @click.outside="open = false"
                 class="absolute right-0 mt-2 w-80 bg-white border border-neutral-200 rounded-lg shadow-lg z-50 p-4">
                <form @submit.prevent="if(query.trim()) { window.location.href = '{{ url('admin') }}/' + section + '?search=' + encodeURIComponent(query); }">
                    <input type="text" x-ref="searchInput" x-model="query" placeholder="Search..." class="form-input w-full mb-2" autofocus>
                    <div class="flex items-center gap-2 mb-3">
                        <label class="text-xs text-neutral-600">Search in:</label>
                        <select x-model="section" class="form-select text-xs py-1 px-2">
                            <option value="products">Products</option>
                            <option value="orders">Orders</option>
                            <option value="customers">Customers</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-full text-sm">Search</button>
                </form>
            </div>
        </div>

        <!-- Notifications -->
        @php
            $adminUser = auth('admin')->user();
            $unreadNotifications = \App\Models\Notification::where('user_id', $adminUser->id)->unread()->latest()->limit(5)->get();
            $unreadCount = \App\Models\Notification::where('user_id', $adminUser->id)->unread()->count();
        @endphp
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 text-neutral-600 hover:text-neutral-900" aria-label="Notifications">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @if($unreadCount > 0)
                    <span class="absolute top-1 right-1 w-2 h-2 bg-error-500 rounded-full"></span>
                @endif
            </button>

            <div x-cloak x-show="open" x-transition @click.away="open = false"
                 class="absolute right-0 mt-2 w-80 bg-white border border-neutral-200 rounded-lg shadow-lg z-50">
                <div class="px-4 py-3 border-b border-neutral-200 flex items-center justify-between">
                    <h3 class="font-semibold text-neutral-900">Notifications</h3>
                    @if($unreadCount > 0)
                        <span class="text-xs text-primary-600 font-medium">{{ $unreadCount }} new</span>
                    @endif
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @forelse($unreadNotifications as $notification)
                        <a href="{{ route('admin.notifications.read', $notification) }}"
                           class="block px-4 py-3 hover:bg-neutral-50 border-b border-neutral-100 last:border-0">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $notification->type === 'new_enquiry' ? 'bg-primary-100' : ($notification->type === 'new_ticket' ? 'bg-purple-100' : 'bg-neutral-100') }}">
                                    @if($notification->type === 'new_enquiry')
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    @elseif($notification->type === 'new_ticket')
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-900">{{ $notification->title }}</p>
                                    <p class="text-xs text-neutral-600 mt-0.5 truncate">{{ $notification->content }}</p>
                                    <p class="text-[10px] text-neutral-600 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-sm text-neutral-600">
                            No new notifications
                        </div>
                    @endforelse
                </div>
                <div class="px-4 py-3 border-t border-neutral-200">
                    <a href="{{ route('admin.notifications') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        View all notifications
                    </a>
                </div>
            </div>
        </div>

        <!-- User menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2" aria-label="User menu">
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-primary-600">
                        {{ substr(auth('admin')->user()->full_name ?? 'A', 0, 1) }}
                    </span>
                </div>
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-cloak x-show="open" x-transition @click.away="open = false"
                 class="absolute right-0 mt-2 w-48 bg-white border border-neutral-200 rounded-lg shadow-lg z-50">
                <div class="px-4 py-3 border-b border-neutral-100">
                    <div class="text-sm font-medium text-neutral-900">{{ auth('admin')->user()->full_name }}</div>
                    <div class="text-xs text-neutral-600">{{ auth('admin')->user()->email }}</div>
                </div>
                <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                    Profile Settings
                </a>
                <a href="{{ route('admin.stores.index') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                    View Store
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
