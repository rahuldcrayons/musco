@extends('premium.layout')

@section('namespace', 'dashboard')

@push('head')
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
    <style>
        /* Override premium layout body background inside dash-shell */
        body { background-color: var(--bg-base) !important; }

        /* Row hover accent border (applied via JS) */
        .dash-table tbody tr.row-hover-accent td:first-child {
            box-shadow: inset 3px 0 0 var(--accent-primary);
        }

        /* SVG chart bars */
        .chart-bar { transition: opacity 0.2s ease; }
        .chart-bar:hover { opacity: 0.8; }

        /* Progress bar fill */
        .progress-fill {
            height: 100%;
            border-radius: var(--radius-pill);
            background: var(--accent-gradient);
            transition: width 1s var(--ease-out-expo);
        }

        /* Activity feed connector */
        .activity-item:not(:last-child) .activity-line {
            position: absolute;
            left: 15px;
            top: 32px;
            bottom: -8px;
            width: 1px;
            background: var(--border-subtle);
        }

        /* Quick action grid buttons */
        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 0.5rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-default);
            background: var(--bg-elevated);
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--duration-base) var(--ease-out-expo);
            text-align: center;
        }
        .quick-action-btn:hover {
            border-color: var(--accent-border);
            background: var(--accent-muted);
            color: var(--accent-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .quick-action-btn svg { width: 22px; height: 22px; flex-shrink: 0; }

        /* Sparkline */
        .sparkline path { fill: none; stroke: var(--accent-primary); stroke-width: 1.5; }
        .sparkline polygon { fill: var(--accent-muted); }

        /* Period tab */
        .period-tab { padding: 0.3rem 0.75rem; border-radius: var(--radius-pill); font-size: 0.78rem; font-weight: 600; cursor: pointer; color: var(--text-tertiary); transition: all 0.2s; }
        .period-tab.active, .period-tab:hover { background: var(--accent-muted); color: var(--accent-primary); }

        /* Filter chip */
        .filter-chip { padding: 0.3rem 0.875rem; border-radius: var(--radius-pill); font-size: 0.78rem; font-weight: 600; cursor: pointer; border: 1px solid var(--border-default); color: var(--text-secondary); background: transparent; transition: all 0.2s; }
        .filter-chip.active, .filter-chip:hover { border-color: var(--accent-border); background: var(--accent-muted); color: var(--accent-primary); }

        /* Sidebar collapse on mobile */
        @media (max-width: 1023px) {
            .dash-sidebar { transform: translateX(-100%); }
            .dash-sidebar.open { transform: translateX(0); }
            .dash-main { margin-left: 0; }
            .dash-navbar { left: 0; }
        }

        /* Hide premium layout navbar + footer inside dashboard */
        #premium-navbar, #premium-navbar-spacer, .premium-footer { display: none !important; }
    </style>
@endpush

@section('content')

<div
    id="dashboard-root"
    x-data="{
        theme: localStorage.getItem('dash-theme') || 'dark',
        sidebarOpen: window.innerWidth >= 1024,
        notifOpen: false,
        userMenuOpen: false,
        activeNav: 'dashboard',
        activeFilter: 'all',
        activePeriod: '7d',
        init() {
            this.\$el.dataset.theme = this.theme === 'light' ? 'light' : '';
            window.addEventListener('dash-theme-changed', (e) => {
                this.theme = e.detail.theme;
                this.\$el.dataset.theme = this.theme === 'light' ? 'light' : '';
            });
            window.addEventListener('resize', () => {
                this.sidebarOpen = window.innerWidth >= 1024;
            });
        }
    }"
    x-init="init()"
    :data-theme="theme === 'light' ? 'light' : ''"
    class="dash-shell min-h-screen"
>

    {{-- ═══════════════════════════════════════════════════════════
         SIDEBAR
    ════════════════════════════════════════════════════════════ --}}
    <aside
        class="dash-sidebar"
        :class="sidebarOpen ? '' : '-translate-x-full lg:translate-x-0'"
        aria-label="Sidebar navigation"
    >
        {{-- Logo --}}
        <a href="/admin" class="sidebar-logo" aria-label="Trendymus Admin">
            <div class="sidebar-logo-icon">
                <svg viewBox="0 0 20 20" fill="none" width="18" height="18">
                    <path d="M3 17V7l7-4 7 4v10" stroke="#080910" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M7 17v-5h6v5" stroke="#080910" stroke-width="1.6" stroke-linejoin="round"/>
                    <circle cx="10" cy="9" r="1.5" fill="#080910"/>
                </svg>
            </div>
            <span>Trendymus <span style="color:var(--accent-primary)">Admin</span></span>
        </a>

        {{-- Navigation --}}
        <nav class="sidebar-nav" aria-label="Main navigation">

            {{-- MAIN --}}
            <span class="sidebar-section-label">Main</span>

            <a href="#" class="sidebar-nav-item active" aria-current="page"
               @click.prevent="activeNav = 'dashboard'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="1" y="1" width="7" height="7" rx="1.5"/>
                    <rect x="10" y="1" width="7" height="7" rx="1.5"/>
                    <rect x="1" y="10" width="7" height="7" rx="1.5"/>
                    <rect x="10" y="10" width="7" height="7" rx="1.5"/>
                </svg>
                Dashboard
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'analytics'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <polyline points="1,13 5,8 9,10 13,4 17,6"/>
                    <path d="M1 17h16"/>
                </svg>
                Analytics
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'products'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 11.5a2.5 2.5 0 01-2.5 2.5h-9A2.5 2.5 0 012 11.5V7l7-4 7 4v4.5z"/>
                    <path d="M9 3v10.5M2 7h14"/>
                </svg>
                Products
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'orders'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M6 2L2 7v9a1 1 0 001 1h12a1 1 0 001-1V7l-4-5H6z"/>
                    <path d="M2 7h14M9 7v9"/>
                </svg>
                Orders
                <span class="sidebar-badge">12</span>
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'customers'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="7" cy="5.5" r="2.5"/>
                    <path d="M1 16a6 6 0 0112 0"/>
                    <circle cx="14" cy="6" r="2"/>
                    <path d="M17 16a3 3 0 00-5-2.24"/>
                </svg>
                Customers
            </a>

            {{-- STORE --}}
            <span class="sidebar-section-label">Store</span>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'inventory'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="1" y="1" width="16" height="4" rx="1"/>
                    <rect x="1" y="7" width="16" height="4" rx="1"/>
                    <rect x="1" y="13" width="16" height="4" rx="1"/>
                </svg>
                Inventory
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'categories'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M2 4a1 1 0 011-1h3.5l1.5 2H15a1 1 0 011 1v7a1 1 0 01-1 1H3a1 1 0 01-1-1V4z"/>
                </svg>
                Categories
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'reviews'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 1.5l2 4 4.5.65-3.25 3.17.77 4.48L9 11.75l-4.02 2.05.77-4.48L2.5 6.15 7 5.5z"/>
                </svg>
                Reviews
                <span class="sidebar-badge">3</span>
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'coupons'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M2 6a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V6z"/>
                    <circle cx="5.5" cy="9" r="1"/>
                    <circle cx="12.5" cy="9" r="1"/>
                    <path d="M8 9h2"/>
                </svg>
                Coupons
            </a>

            {{-- SYSTEM --}}
            <span class="sidebar-section-label">System</span>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'settings'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="9" cy="9" r="2.5"/>
                    <path d="M9 1v2M9 15v2M1 9h2M15 9h2M3.22 3.22l1.41 1.41M13.37 13.37l1.41 1.41M3.22 14.78l1.41-1.41M13.37 4.63l1.41-1.41"/>
                </svg>
                Settings
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'integrations'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M7 3h4v4l2 2-2 2v4H7v-4L5 9l2-2V3z"/>
                    <path d="M3 7l2 2-2 2M15 7l-2 2 2 2"/>
                </svg>
                Integrations
            </a>

            <a href="#" class="sidebar-nav-item" @click.prevent="activeNav = 'team'">
                <svg class="sidebar-nav-icon" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="6" cy="5" r="2.5"/>
                    <circle cx="12" cy="5" r="2.5"/>
                    <path d="M1 16a5 5 0 0110 0"/>
                    <path d="M11.5 13.5a5 5 0 016 0"/>
                </svg>
                Team
            </a>

        </nav>

        {{-- Sidebar Footer --}}
        <div class="sidebar-footer">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="relative shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                         style="background:var(--accent-gradient);color:var(--text-on-accent);">
                        R
                    </div>
                    <span class="status-dot online absolute -bottom-0.5 -right-0.5"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color:var(--text-primary)">Rahul Yadav</p>
                    <p class="text-xs truncate" style="color:var(--text-tertiary)">Super Admin</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit"
                            class="navbar-action-btn"
                            title="Logout"
                            aria-label="Logout">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" width="15" height="15">
                            <path d="M6 3H3a1 1 0 00-1 1v8a1 1 0 001 1h3M10 11l3-3-3-3M13 8H6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════════════
         MAIN COLUMN
    ════════════════════════════════════════════════════════════ --}}
    <div class="dash-main">

        {{-- ─── DASHBOARD NAVBAR ─────────────────────────────── --}}
        <header class="dash-navbar" role="banner">

            {{-- Mobile sidebar toggle --}}
            <button class="navbar-action-btn lg:hidden shrink-0"
                    @click="sidebarOpen = !sidebarOpen"
                    aria-label="Toggle sidebar">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
                    <path d="M2 4h12M2 8h12M2 12h12" stroke-linecap="round"/>
                </svg>
            </button>

            {{-- Page title + breadcrumb --}}
            <div class="shrink-0 hidden sm:block">
                <h1 class="text-sm font-bold" style="color:var(--text-primary);line-height:1.2">Dashboard</h1>
                <nav class="flex items-center gap-1.5 mt-0.5" aria-label="Breadcrumb">
                    <a href="/admin" class="text-xs" style="color:var(--text-tertiary);text-decoration:none">Admin</a>
                    <svg viewBox="0 0 8 8" fill="none" width="8" height="8" style="color:var(--text-disabled)">
                        <path d="M2 1l3 3-3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                    <span class="text-xs font-medium" style="color:var(--accent-primary)">Dashboard</span>
                </nav>
            </div>

            {{-- Search --}}
            <div class="navbar-search">
                <svg class="navbar-search-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M13 13l-3-3M2 7.5a5.5 5.5 0 1011 0 5.5 5.5 0 00-11 0z" stroke-linecap="round"/>
                </svg>
                <input
                    type="search"
                    placeholder="Search..."
                    aria-label="Search dashboard"
                    @keydown.meta.k.prevent="$el.focus()"
                    @keydown.ctrl.k.prevent="$el.focus()"
                />
                <kbd class="absolute right-3 top-1/2 -translate-y-1/2 hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-medium"
                     style="background:var(--bg-overlay);color:var(--text-tertiary);border:1px solid var(--border-default)">
                    ⌘K
                </kbd>
            </div>

            {{-- Right actions --}}
            <div class="navbar-actions">

                {{-- Notification bell --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button class="navbar-action-btn"
                            @click="open = !open"
                            aria-label="Notifications"
                            :aria-expanded="open">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
                            <path d="M8 1a5 5 0 015 5v3l1.5 2.5H1.5L3 9V6a5 5 0 015-5zM6 13a2 2 0 004 0" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-[9px] font-bold flex items-center justify-center"
                              style="background:var(--accent-primary);color:var(--text-on-accent)">4</span>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-72 rounded-xl shadow-xl z-50 overflow-hidden"
                         style="background:var(--bg-elevated);border:1px solid var(--border-default)">
                        <div class="px-4 py-3 flex items-center justify-between"
                             style="border-bottom:1px solid var(--border-subtle)">
                            <span class="text-sm font-bold" style="color:var(--text-primary)">Notifications</span>
                            <button class="text-xs font-medium" style="color:var(--accent-primary)">Mark all read</button>
                        </div>
                        @foreach([
                            ['New order #ORD-1852 received', '2m ago', 'success'],
                            ['Low stock: Pearl Necklace (3 left)', '18m ago', 'warning'],
                            ['Review pending approval', '1h ago', 'info'],
                            ['Payout processed: £12,400', '3h ago', 'success'],
                        ] as [$msg, $time, $type])
                        <div class="flex items-start gap-3 px-4 py-3 hover:opacity-80 transition-opacity cursor-pointer"
                             style="border-bottom:1px solid var(--border-subtle)">
                            <span class="status-dot mt-1.5 shrink-0 {{ $type === 'success' ? 'online' : ($type === 'warning' ? 'away' : 'busy') }}"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium leading-snug" style="color:var(--text-primary)">{{ $msg }}</p>
                                <p class="text-xs mt-0.5" style="color:var(--text-tertiary)">{{ $time }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Theme toggle --}}
                <button class="navbar-action-btn"
                        data-theme-toggle
                        aria-label="Toggle theme"
                        title="Toggle light/dark theme">
                    {{-- Sun icon (shown in dark mode) --}}
                    <svg class="icon-sun" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" width="15" height="15">
                        <circle cx="8" cy="8" r="3"/>
                        <path d="M8 1v1.5M8 13.5V15M1 8h1.5M13.5 8H15M3.05 3.05l1.06 1.06M11.89 11.89l1.06 1.06M3.05 12.95l1.06-1.06M11.89 4.11l1.06-1.06" stroke-linecap="round"/>
                    </svg>
                    {{-- Moon icon (shown in light mode) --}}
                    <svg class="icon-moon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" width="15" height="15" style="display:none">
                        <path d="M13.5 10A6 6 0 016 2.5a6 6 0 100 11 6 6 0 007.5-3.5z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                {{-- User avatar dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1"
                            aria-label="User menu"
                            :aria-expanded="open">
                        <div class="w-[34px] h-[34px] rounded-full flex items-center justify-center text-xs font-bold navbar-avatar"
                             style="background:var(--accent-gradient);color:var(--text-on-accent);cursor:pointer">
                            R
                        </div>
                        <svg viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" width="10" height="10"
                             style="color:var(--text-tertiary)"
                             :class="open ? 'rotate-180' : ''"
                             class="transition-transform duration-200 hidden sm:block">
                            <path d="M2 3.5l3 3 3-3" stroke-linecap="round"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-52 rounded-xl shadow-xl z-50 overflow-hidden"
                         style="background:var(--bg-elevated);border:1px solid var(--border-default)">
                        <div class="px-4 py-3.5" style="border-bottom:1px solid var(--border-subtle)">
                            <p class="text-sm font-semibold" style="color:var(--text-primary)">Rahul Yadav</p>
                            <p class="text-xs mt-0.5" style="color:var(--text-tertiary)">rahul@trendymus.in</p>
                        </div>
                        <div class="py-1.5">
                            <a href="/admin/profile" class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
                               style="color:var(--text-secondary);text-decoration:none"
                               onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-overlay)'"
                               onmouseout="this.style.color='var(--text-secondary)';this.style.background='transparent'">
                                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><circle cx="7" cy="4.5" r="2.5"/><path d="M1.5 13a5.5 5.5 0 0111 0"/></svg>
                                My Profile
                            </a>
                            <a href="/admin/settings" class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
                               style="color:var(--text-secondary);text-decoration:none"
                               onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-overlay)'"
                               onmouseout="this.style.color='var(--text-secondary)';this.style.background='transparent'">
                                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><circle cx="7" cy="7" r="2"/><path d="M7 1v1.5M7 11.5V13M1 7h1.5M11.5 7H13"/></svg>
                                Settings
                            </a>
                        </div>
                        <div style="border-top:1px solid var(--border-subtle)" class="py-1.5">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm transition-colors"
                                        style="color:var(--error);background:transparent;border:none;cursor:pointer"
                                        onmouseover="this.style.background='var(--error-muted)'"
                                        onmouseout="this.style.background='transparent'">
                                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><path d="M5 3H3a1 1 0 00-1 1v6a1 1 0 001 1h2M9 10l3-3-3-3M12 7H5" stroke-linecap="round"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        {{-- ─── MAIN CONTENT ─────────────────────────────────── --}}
        <main id="main-content" class="dash-content">

            {{-- ════════════════════════════════════════════════
                 1. STATS ROW
            ═════════════════════════════════════════════════ --}}
            <section aria-labelledby="stats-heading" class="mb-6">
                <h2 id="stats-heading" class="sr-only">Key metrics</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Total Revenue --}}
                    <div class="card-stat">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="card-stat-label">Total Revenue</p>
                                <p class="card-stat-number dash-metric" data-count="482100" data-format="£">£4,82,100</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:var(--accent-muted)">
                                <svg viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" style="color:var(--accent-primary)">
                                    <path d="M9 1v16M5 5.5h5.5a2.5 2.5 0 010 5H5M5 10.5h6a2.5 2.5 0 010 5H5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="card-stat-trend up">
                                <svg viewBox="0 0 10 10" fill="none" width="10" height="10" stroke="currentColor" stroke-width="2"><path d="M2 7l3-4 3 4" stroke-linecap="round"/></svg>
                                +12.4%
                            </span>
                            <svg class="sparkline" viewBox="0 0 60 24" width="60" height="24" aria-hidden="true">
                                <polygon points="0,24 0,18 10,15 20,16 30,10 40,12 50,6 60,4 60,24" style="fill:var(--accent-muted)"/>
                                <polyline points="0,18 10,15 20,16 30,10 40,12 50,6 60,4" style="fill:none;stroke:var(--accent-primary);stroke-width:1.5"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Orders --}}
                    <div class="card-stat">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="card-stat-label">Total Orders</p>
                                <p class="card-stat-number dash-metric" data-count="1847" data-format="">1,847</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:var(--info-muted)">
                                <svg viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" style="color:var(--info)">
                                    <path d="M6 2L2 7v9a1 1 0 001 1h12a1 1 0 001-1V7l-4-5H6z" stroke-linejoin="round"/>
                                    <path d="M2 7h14"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="card-stat-trend up">
                                <svg viewBox="0 0 10 10" fill="none" width="10" height="10" stroke="currentColor" stroke-width="2"><path d="M2 7l3-4 3 4" stroke-linecap="round"/></svg>
                                +8.1%
                            </span>
                            <svg class="sparkline" viewBox="0 0 60 24" width="60" height="24" aria-hidden="true">
                                <polygon points="0,24 0,20 10,17 20,14 30,16 40,11 50,9 60,7 60,24" style="fill:var(--info-muted)"/>
                                <polyline points="0,20 10,17 20,14 30,16 40,11 50,9 60,7" style="fill:none;stroke:var(--info);stroke-width:1.5"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Avg Order --}}
                    <div class="card-stat">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="card-stat-label">Avg. Order Value</p>
                                <p class="card-stat-number dash-metric" data-count="261" data-format="£">£261</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:var(--warning-muted)">
                                <svg viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" style="color:var(--warning)">
                                    <path d="M9 1l2 5h5l-4 3 1.5 5L9 11.5 4.5 14 6 9 2 6h5z" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="card-stat-trend down">
                                <svg viewBox="0 0 10 10" fill="none" width="10" height="10" stroke="currentColor" stroke-width="2"><path d="M2 3l3 4 3-4" stroke-linecap="round"/></svg>
                                -2.1%
                            </span>
                            <svg class="sparkline" viewBox="0 0 60 24" width="60" height="24" aria-hidden="true">
                                <polygon points="0,24 0,8 10,10 20,9 30,12 40,14 50,15 60,17 60,24" style="fill:var(--warning-muted)"/>
                                <polyline points="0,8 10,10 20,9 30,12 40,14 50,15 60,17" style="fill:none;stroke:var(--warning);stroke-width:1.5"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Active Users --}}
                    <div class="card-stat">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="card-stat-label">Active Users</p>
                                <p class="card-stat-number dash-metric" data-count="3204" data-format="">3,204</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:var(--success-muted)">
                                <svg viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" style="color:var(--success)">
                                    <circle cx="6" cy="5" r="2.5"/>
                                    <circle cx="12" cy="5" r="2.5"/>
                                    <path d="M1 16a5 5 0 0110 0M11.5 13.5a5 5 0 016 0"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="card-stat-trend up">
                                <svg viewBox="0 0 10 10" fill="none" width="10" height="10" stroke="currentColor" stroke-width="2"><path d="M2 7l3-4 3 4" stroke-linecap="round"/></svg>
                                +23.6%
                            </span>
                            <svg class="sparkline" viewBox="0 0 60 24" width="60" height="24" aria-hidden="true">
                                <polygon points="0,24 0,22 10,19 20,16 30,13 40,10 50,6 60,3 60,24" style="fill:var(--success-muted)"/>
                                <polyline points="0,22 10,19 20,16 30,13 40,10 50,6 60,3" style="fill:none;stroke:var(--success);stroke-width:1.5"/>
                            </svg>
                        </div>
                    </div>

                </div>
            </section>

            {{-- ════════════════════════════════════════════════
                 2. REVENUE CHART + TOP PRODUCTS
            ═════════════════════════════════════════════════ --}}
            <section aria-label="Revenue and top products" class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

                {{-- Revenue Chart (2 cols) --}}
                <div class="card lg:col-span-2" style="padding:0">
                    <div class="card-header">
                        <div>
                            <h2 class="text-sm font-bold" style="color:var(--text-primary)">Revenue Overview</h2>
                            <p class="text-xs mt-0.5" style="color:var(--text-tertiary)">Daily revenue for selected period</p>
                        </div>
                        <div class="flex items-center gap-1" x-data="{ period: '7d' }">
                            @foreach(['7d' => '7D', '1m' => '1M', '3m' => '3M', '1y' => '1Y'] as $val => $label)
                            <button class="period-tab" :class="period === '{{ $val }}' ? 'active' : ''"
                                    @click="period = '{{ $val }}'">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- SVG Bar Chart (last 7 days) --}}
                        <div class="w-full" style="height:180px">
                            <svg viewBox="0 0 480 160" preserveAspectRatio="none" width="100%" height="100%" aria-label="Revenue bar chart last 7 days">
                                {{-- Y-axis grid lines --}}
                                @foreach([0, 40, 80, 120] as $y)
                                <line x1="0" y1="{{ $y }}" x2="480" y2="{{ $y }}" stroke="var(--border-subtle)" stroke-width="1"/>
                                @endforeach

                                {{-- Bars: Mon-Sun data (heights scaled to 160) --}}
                                @php
                                $days    = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                                $heights = [72, 95, 68, 112, 88, 140, 104];
                                $barW    = 44;
                                $gap     = 24;
                                $startX  = 20;
                                @endphp

                                @foreach($days as $i => $day)
                                @php $x = $startX + $i * ($barW + $gap); $h = $heights[$i]; $y = 160 - $h; @endphp
                                <g class="chart-bar">
                                    {{-- Bar background --}}
                                    <rect x="{{ $x }}" y="0" width="{{ $barW }}" height="160" rx="4" fill="var(--bg-elevated)" opacity="0.5"/>
                                    {{-- Bar fill (gradient) --}}
                                    <defs>
                                        <linearGradient id="bar-grad-{{ $i }}" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="var(--accent-primary)" stop-opacity="0.9"/>
                                            <stop offset="100%" stop-color="var(--accent-dim)" stop-opacity="0.6"/>
                                        </linearGradient>
                                    </defs>
                                    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $h }}" rx="4"
                                          fill="url(#bar-grad-{{ $i }})"/>
                                    {{-- Day label --}}
                                    <text x="{{ $x + $barW / 2 }}" y="155" text-anchor="middle"
                                          font-size="9" fill="var(--text-tertiary)" font-family="inherit">{{ $day }}</text>
                                </g>
                                @endforeach

                                {{-- Trend line --}}
                                <polyline
                                    points="@foreach($days as $i => $day){{ $startX + $i * ($barW + $gap) + $barW / 2 }},{{ 160 - $heights[$i] - 2 }} @endforeach"
                                    fill="none" stroke="var(--accent-primary)" stroke-width="1.5"
                                    stroke-dasharray="3 2" opacity="0.5"/>
                            </svg>
                        </div>
                        {{-- Summary row --}}
                        <div class="flex items-center gap-6 mt-4 pt-4" style="border-top:1px solid var(--border-subtle)">
                            <div>
                                <p class="text-xs" style="color:var(--text-tertiary)">This Week</p>
                                <p class="text-base font-bold mt-0.5" style="color:var(--text-primary)">£68,240</p>
                            </div>
                            <div>
                                <p class="text-xs" style="color:var(--text-tertiary)">Last Week</p>
                                <p class="text-base font-bold mt-0.5" style="color:var(--text-secondary)">£60,180</p>
                            </div>
                            <div class="ml-auto">
                                <span class="badge badge-success">+13.4% vs last week</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Products (1 col) --}}
                <div class="card" style="padding:0">
                    <div class="card-header">
                        <h2 class="text-sm font-bold" style="color:var(--text-primary)">Top Products</h2>
                        <a href="/admin/products" class="text-xs font-medium" style="color:var(--accent-primary);text-decoration:none">View all</a>
                    </div>
                    <div class="card-body" style="padding-top:1rem;padding-bottom:1rem">
                        @php
                        $topProducts = [
                            ['Gold Hoop Earrings',    'Earrings',   '£28,400', 92],
                            ['Pearl Drop Necklace',   'Necklaces',  '£22,100', 71],
                            ['Diamond Nose Pin',      'Nose Pins',  '£19,500', 63],
                            ['Silver Anklet Set',     'Anklets',    '£14,800', 48],
                            ['Rose Gold Bracelet',    'Bracelets',  '£11,200', 36],
                        ];
                        @endphp
                        <div class="flex flex-col gap-4">
                            @foreach($topProducts as $idx => $prod)
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold w-5 shrink-0 text-right"
                                      style="color:{{ $idx === 0 ? 'var(--accent-primary)' : 'var(--text-disabled)' }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <p class="text-xs font-semibold truncate" style="color:var(--text-primary)">{{ $prod[0] }}</p>
                                        <p class="text-xs font-bold shrink-0" style="color:var(--text-secondary)">{{ $prod[2] }}</p>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full" style="background:var(--bg-elevated)">
                                        <div class="progress-fill" style="width:{{ $prod[3] }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </section>

            {{-- ════════════════════════════════════════════════
                 3. RECENT ORDERS TABLE
            ═════════════════════════════════════════════════ --}}
            <section aria-labelledby="orders-heading" class="mb-6">
                <div class="dash-table-wrap">
                    <div class="card-header">
                        <div>
                            <h2 id="orders-heading" class="text-sm font-bold" style="color:var(--text-primary)">Recent Orders</h2>
                            <p class="text-xs mt-0.5" style="color:var(--text-tertiary)">Last 6 transactions</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="flex items-center gap-1" x-data="{ f: 'all' }">
                                @foreach(['all' => 'All', 'pending' => 'Pending', 'shipped' => 'Shipped', 'delivered' => 'Delivered'] as $val => $label)
                                <button class="filter-chip" :class="f === '{{ $val }}' ? 'active' : ''"
                                        @click="f = '{{ $val }}'">{{ $label }}</button>
                                @endforeach
                            </div>
                            <a href="/admin/orders" class="btn btn-sm btn-secondary ml-2">View All</a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="dash-table" aria-label="Recent orders">
                            <thead>
                                <tr>
                                    <th data-sort="id">Order ID</th>
                                    <th>Customer</th>
                                    <th data-sort="product">Product</th>
                                    <th data-sort="date">Date</th>
                                    <th data-sort="amount">Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $orders = [
                                    ['ORD-1852', 'Priya Sharma',    'PS', 'Gold Hoop Earrings', 'Apr 4, 2026', '£4,200', 'delivered',  'success'],
                                    ['ORD-1851', 'Ankit Mehta',     'AM', 'Pearl Drop Necklace','Apr 4, 2026', '£6,800', 'shipped',    'info'],
                                    ['ORD-1850', 'Sunita Patel',    'SP', 'Diamond Nose Pin',   'Apr 3, 2026', '£3,500', 'processing', 'warning'],
                                    ['ORD-1849', 'Ravi Kumar',      'RK', 'Rose Gold Bracelet', 'Apr 3, 2026', '£2,900', 'pending',    'warning'],
                                    ['ORD-1848', 'Deepa Nair',      'DN', 'Silver Anklet Set',  'Apr 2, 2026', '£1,800', 'delivered',  'success'],
                                    ['ORD-1847', 'Vikram Singh',    'VS', 'Mangalsutra Set',    'Apr 2, 2026', '£12,400','cancelled',  'error'],
                                ];
                                @endphp
                                @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <span class="text-xs font-mono font-semibold" style="color:var(--accent-primary)">{{ $order[0] }}</span>
                                    </td>
                                    <td>
                                        <div class="td-avatar">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                                 style="background:var(--accent-muted);color:var(--accent-primary)">
                                                {{ $order[2] }}
                                            </div>
                                            <span class="text-sm font-medium" style="color:var(--text-primary)">{{ $order[1] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm" style="color:var(--text-secondary)">{{ $order[3] }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm" style="color:var(--text-tertiary)">{{ $order[4] }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm font-semibold" style="color:var(--text-primary)">{{ $order[5] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $order[7] }}">{{ ucfirst($order[6]) }}</span>
                                    </td>
                                    <td>
                                        <a href="/admin/orders/{{ strtolower(str_replace('-','', $order[0])) }}"
                                           class="navbar-action-btn inline-flex"
                                           title="View order {{ $order[0] }}"
                                           aria-label="View order {{ $order[0] }}">
                                            <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14">
                                                <path d="M1 7s2-4 6-4 6 4 6 4-2 4-6 4-6-4-6-4z"/>
                                                <circle cx="7" cy="7" r="1.5"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- ════════════════════════════════════════════════
                 4. QUICK ACTIONS + ACTIVITY FEED
            ═════════════════════════════════════════════════ --}}
            <section aria-label="Quick actions and activity" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

                {{-- Quick Actions --}}
                <div class="card" style="padding:0">
                    <div class="card-header">
                        <h2 class="text-sm font-bold" style="color:var(--text-primary)">Quick Actions</h2>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-3 gap-3">

                            <button class="quick-action-btn btn" onclick="window.location='/admin/products/create'">
                                <svg viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="2" y="2" width="8" height="8" rx="1.5"/>
                                    <rect x="12" y="2" width="8" height="8" rx="1.5"/>
                                    <rect x="2" y="12" width="8" height="8" rx="1.5"/>
                                    <path d="M15 15h4M17 13v4" stroke-linecap="round"/>
                                </svg>
                                Add Product
                            </button>

                            <button class="quick-action-btn btn" onclick="window.location='/admin/coupons/create'">
                                <svg viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M3 7a1 1 0 011-1h14a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V7z"/>
                                    <circle cx="7" cy="11" r="1.5"/>
                                    <circle cx="15" cy="11" r="1.5"/>
                                    <path d="M10 11h2" stroke-linecap="round"/>
                                </svg>
                                Create Coupon
                            </button>

                            <button class="quick-action-btn btn" onclick="window.location='/admin/emails/compose'">
                                <svg viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="2" y="4" width="18" height="14" rx="2"/>
                                    <path d="M2 7l9 6 9-6" stroke-linecap="round"/>
                                </svg>
                                Send Email
                            </button>

                            <button class="quick-action-btn btn" onclick="window.location='/admin/reports'">
                                <svg viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4 18V8M9 18V4M14 18v-6M19 18v-10" stroke-linecap="round"/>
                                </svg>
                                Generate Report
                            </button>

                            <button class="quick-action-btn btn" onclick="window.location='/admin/export'">
                                <svg viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4 14v4h14v-4M11 3v10M7 9l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Export Data
                            </button>

                            <button class="quick-action-btn btn" onclick="window.location='/admin/analytics'">
                                <svg viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <polyline points="3,16 8,9 12,12 17,5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="17" cy="5" r="2"/>
                                </svg>
                                View Analytics
                            </button>

                        </div>
                    </div>
                </div>

                {{-- Activity Feed --}}
                <div class="card" style="padding:0">
                    <div class="card-header">
                        <h2 class="text-sm font-bold" style="color:var(--text-primary)">Recent Activity</h2>
                        <button class="btn btn-sm btn-ghost">See all</button>
                    </div>
                    <div class="card-body" style="padding-top:1rem;padding-bottom:1rem">
                        @php
                        $activities = [
                            ['New order placed by Priya Sharma — £4,200',       '2 min ago',  'success', 'M6 2L2 7v9a1 1 0 001 1h12a1 1 0 001-1V7l-4-5H6zM2 7h14'],
                            ['Product "Gold Hoop" updated by admin',              '18 min ago', 'info',    'M2 3h14a1 1 0 011 1v10a1 1 0 01-1 1H2a1 1 0 01-1-1V4a1 1 0 011-1zM7 7h4M7 10h2'],
                            ['Review approved: ★★★★★ Pearl Necklace',            '1h ago',     'accent',  'M8 1.5l2 4 4.5.65-3.25 3.17.77 4.48L8 11.75l-4.02 2.05.77-4.48L1.5 6.15 6 5.5z'],
                            ['Coupon SAVE20 used × 7 times today',               '2h ago',     'warning', 'M2 6a1 1 0 011-1h10a1 1 0 011 1v4a1 1 0 01-1 1H3a1 1 0 01-1-1V6zM4 8h.5M9.5 8h.5'],
                            ['Stock alert: Silver Anklet below 5 units',         '3h ago',     'error',   'M8 3v1M8 12v1M3 8h1M12 8h1M4.93 4.93l.7.7M10.37 10.37l.7.7M4.93 11.07l.7-.7M10.37 5.63l.7-.7'],
                            ['Payout of £12,400 processed to bank account',     '5h ago',     'success', 'M9 1v16M5 5.5h5.5a2.5 2.5 0 010 5H5M5 10.5h6a2.5 2.5 0 010 5H5'],
                        ];
                        @endphp
                        <div class="flex flex-col gap-0">
                            @foreach($activities as $idx => $act)
                            <div class="activity-item relative flex items-start gap-3 py-2.5 {{ $idx < count($activities) - 1 ? 'border-b' : '' }}"
                                 style="{{ $idx < count($activities) - 1 ? 'border-color:var(--border-subtle)' : '' }}">
                                {{-- Icon --}}
                                <div class="w-[30px] h-[30px] rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                                     style="background:var(--{{ $act[2] === 'accent' ? 'accent' : $act[2] }}-muted)">
                                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.3"
                                         width="13" height="13"
                                         style="color:var(--{{ $act[2] === 'accent' ? 'accent-primary' : $act[2] }})">
                                        <path d="{{ $act[3] }}" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs leading-snug" style="color:var(--text-secondary)">{{ $act[0] }}</p>
                                    <p class="text-xs mt-0.5 font-medium" style="color:var(--text-tertiary)">{{ $act[1] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </section>

            {{-- ════════════════════════════════════════════════
                 5. FORM DEMO — Add New Product
            ═════════════════════════════════════════════════ --}}
            <section aria-labelledby="form-heading" class="mb-8">
                <div class="card" style="padding:0">
                    <div class="card-header">
                        <div>
                            <h2 id="form-heading" class="text-sm font-bold" style="color:var(--text-primary)">Add New Product</h2>
                            <p class="text-xs mt-0.5" style="color:var(--text-tertiary)">Fill in the details below to add a new product to the catalogue</p>
                        </div>
                        <span class="badge badge-accent">Draft</span>
                    </div>
                    <div class="card-body">
                        <form
                            x-data="{
                                name: '', sku: '', price: '', stock: '', category: '', description: '',
                                errors: {},
                                validate() {
                                    this.errors = {};
                                    if (!this.name)  this.errors.name = 'Product name is required';
                                    if (!this.price) this.errors.price = 'Price is required';
                                    if (!this.stock) this.errors.stock = 'Stock quantity is required';
                                    return Object.keys(this.errors).length === 0;
                                },
                                submit() {
                                    if (this.validate()) {
                                        window.\$store?.toast?.show('Product saved as draft!', 'success');
                                    }
                                }
                            }"
                            @submit.prevent="submit()"
                            novalidate
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                                {{-- Product Name --}}
                                <div class="form-group">
                                    <label class="form-label" for="prod-name">Product Name</label>
                                    <input
                                        id="prod-name"
                                        type="text"
                                        class="form-input"
                                        :class="errors.name ? 'error' : ''"
                                        x-model="name"
                                        placeholder="e.g. Gold Hoop Earrings"
                                        autocomplete="off"
                                    />
                                    <span class="form-error-msg" x-show="errors.name" x-text="errors.name"></span>
                                </div>

                                {{-- SKU --}}
                                <div class="form-group">
                                    <label class="form-label" for="prod-sku">SKU / Product Code</label>
                                    <input
                                        id="prod-sku"
                                        type="text"
                                        class="form-input"
                                        x-model="sku"
                                        placeholder="e.g. GLD-HOOP-001"
                                        autocomplete="off"
                                    />
                                    <span class="form-hint">Leave blank to auto-generate</span>
                                </div>

                                {{-- Price --}}
                                <div class="form-group">
                                    <label class="form-label" for="prod-price">Price (£)</label>
                                    <input
                                        id="prod-price"
                                        type="number"
                                        class="form-input"
                                        :class="errors.price ? 'error' : ''"
                                        x-model="price"
                                        placeholder="0.00"
                                        min="0"
                                        step="0.01"
                                    />
                                    <span class="form-error-msg" x-show="errors.price" x-text="errors.price"></span>
                                </div>

                                {{-- Stock --}}
                                <div class="form-group">
                                    <label class="form-label" for="prod-stock">Stock Quantity</label>
                                    <input
                                        id="prod-stock"
                                        type="number"
                                        class="form-input"
                                        :class="errors.stock ? 'error' : ''"
                                        x-model="stock"
                                        placeholder="0"
                                        min="0"
                                    />
                                    <span class="form-error-msg" x-show="errors.stock" x-text="errors.stock"></span>
                                </div>

                                {{-- Category --}}
                                <div class="form-group">
                                    <label class="form-label" for="prod-category">Category</label>
                                    <select id="prod-category" class="form-select" x-model="category">
                                        <option value="">— Select category —</option>
                                        @foreach(['Rings','Necklaces','Earrings','Bracelets','Bangles','Nose Pins','Pendants','Anklets','Mangalsutra','Men\'s Jewellery'] as $cat)
                                        <option value="{{ strtolower(str_replace(['\'', ' '], ['', '-'], $cat)) }}">{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Description (full width) --}}
                                <div class="form-group md:col-span-2">
                                    <label class="form-label" for="prod-desc">Description</label>
                                    <textarea
                                        id="prod-desc"
                                        class="form-textarea"
                                        x-model="description"
                                        rows="4"
                                        placeholder="Describe the product — material, occasion, care instructions..."
                                    ></textarea>
                                    <span class="form-hint" x-text="`${description.length} / 500 characters`"></span>
                                </div>

                            </div>

                            {{-- Form Actions --}}
                            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid var(--border-subtle);margin-top:0.5rem;padding-top:1.25rem">
                                <button type="submit" class="btn btn-primary">
                                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.75" width="14" height="14">
                                        <path d="M2 7h10M7 2l5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Save Product
                                </button>
                                <button type="button" class="btn btn-ghost"
                                        @click="name = ''; sku = ''; price = ''; stock = ''; category = ''; description = ''; errors = {}">
                                    Cancel
                                </button>
                                <span class="ml-auto text-xs" style="color:var(--text-tertiary)">
                                    All fields marked required will be validated on submit
                                </span>
                            </div>

                        </form>
                    </div>
                </div>
            </section>

        </main>
    </div>

    {{-- Mobile sidebar backdrop --}}
    <div
        x-show="sidebarOpen && window.innerWidth < 1024"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/60 lg:hidden"
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    ></div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.initDashboard?.();
    });
</script>
@endpush
