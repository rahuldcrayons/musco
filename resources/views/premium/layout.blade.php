<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Trendymus') }}</title>

    <!-- SEO -->
    @stack('meta')
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <!-- Performance -->
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=playfair-display:400i,400,500,600,700|dm-sans:300,400,500,600&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/css/premium.css', 'resources/js/app.js', 'resources/js/premium.js'])

    <!-- Reduced motion -->
    <style>
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        :focus-visible { outline: 2px solid #202a40; outline-offset: 2px; }
    </style>

    @stack('head')
</head>
<body
    class="bg-[#FAFAF8] text-[#111111] font-sans antialiased overflow-x-hidden"
    data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
    x-data
>
    {{-- Skip to content --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[999999] focus:bg-[#202a40] focus:text-white focus:px-4 focus:py-2 focus:rounded-lg">
        Skip to main content
    </a>

    {{-- Barba.js page wrapper --}}
    <div data-barba="wrapper">

        {{-- Navbar (outside barba container so it persists across transitions) --}}
        @include('premium._navbar')

        {{-- Page content --}}
        <main id="main-content" data-barba="container" data-barba-namespace="@yield('namespace', 'default')">
            @yield('content')
        </main>

        {{-- Footer (outside barba container) --}}
        @include('premium._footer')

    </div>

    {{-- Page Transition Curtain --}}
    <div id="page-curtain"
         style="position:fixed;inset:0;background:linear-gradient(135deg,#202a40 0%,#151e30 100%);z-index:999999;transform:scaleY(0);transform-origin:top center;pointer-events:none;will-change:transform;"
         aria-hidden="true">
    </div>

    {{-- Toast notifications (Alpine, from app.js store) --}}
    <div x-data
         class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"
         aria-live="polite">
        <template x-for="item in $store.toast.items" :key="item.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium max-w-xs"
                 :class="{
                     'bg-white text-[#111] border border-[#efefef]': item.type === 'info',
                     'bg-[#202a40] text-white': item.type === 'success',
                     'bg-red-500 text-white': item.type === 'error',
                     'bg-amber-500 text-white': item.type === 'warning',
                 }">
                <span x-text="item.message"></span>
                <button @click="$store.toast.remove(item.id)" class="ml-auto opacity-70 hover:opacity-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    {{-- WhatsApp button --}}
    <x-whatsapp-button />

    @stack('scripts')
</body>
</html>
