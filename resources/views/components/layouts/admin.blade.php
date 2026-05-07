<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $styles ?? '' }}
    @stack('styles')
    <style>
        #nprogress-bar{position:fixed;top:0;left:0;width:0;height:2px;background:linear-gradient(90deg,#202a40,#506282);z-index:99999;transition:width 0.2s ease,opacity 0.4s ease;pointer-events:none;opacity:0}
        #nprogress-bar.active{opacity:1}
    </style>
</head>
<div id="nprogress-bar"></div>
<body class="antialiased layout-admin" x-data="{ sidebarOpen: false }" style="background:#f1f1f1;font-family:'Inter',system-ui,-apple-system,sans-serif">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Content area -->
        <div class="flex flex-col flex-1 overflow-hidden" style="background:#f1f1f1">
            <!-- Admin Header -->
            @include('admin.partials.header')

            <!-- Main content -->
            <main class="flex-1 overflow-y-auto" style="scrollbar-width:thin;scrollbar-color:#ccc transparent;background:#f1f1f1;padding:16px 20px">
                @isset($header)
                    <div class="mb-4">{{ $header }}</div>
                @endisset

                @isset($statsBar)
                    {{ $statsBar }}
                @endisset

                {{ $slot }}
            </main>
        </div>
    </div>

    {{ $scripts ?? '' }}
    @stack('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 4000,
            extendedTimeOut: 2000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
        };

        @if(session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if(session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if(session('warning'))
            toastr.warning(@json(session('warning')));
        @endif

        @if(session('info'))
            toastr.info(@json(session('info')));
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error(@json($error));
            @endforeach
        @endif
    </script>
    <script>
    (function(){
        var bar = document.getElementById('nprogress-bar');
        var timer, width = 0, raf;
        function start() {
            clearTimeout(timer); cancelAnimationFrame(raf);
            width = 0; bar.style.width = '0%'; bar.classList.add('active'); grow();
        }
        function grow() {
            var t = width < 50 ? width+8 : width < 80 ? width+3 : width < 95 ? width+0.5 : width;
            if (t > 95) t = 95; width = t; bar.style.width = width+'%';
            if (width < 95) raf = requestAnimationFrame(function(){ timer = setTimeout(grow, 100); });
        }
        function done() {
            cancelAnimationFrame(raf); clearTimeout(timer); width = 100; bar.style.width = '100%';
            setTimeout(function(){ bar.classList.remove('active'); bar.style.width = '0%'; }, 400);
        }
        document.addEventListener('click', function(e) {
            var a = e.target.closest('a[href]');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || a.target === '_blank') return;
            try { var url = new URL(href, location.href); if (url.origin !== location.origin) return; if (url.pathname === location.pathname && url.search === location.search) return; } catch(e) { return; }
            start();
        });
        document.addEventListener('submit', function(e) { if (e.target.method && e.target.method.toLowerCase() !== 'get') start(); });
        window.addEventListener('pageshow', done);
    })();
    </script>
</body>
</html>
