@php
    $links = [
        ['route' => 'account.dashboard', 'match' => 'account.dashboard', 'label' => 'Dashboard', 'd' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'account.orders.index', 'match' => 'account.orders*', 'label' => 'Orders', 'd' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['route' => 'wishlist', 'match' => 'wishlist*', 'label' => 'Wishlist', 'd' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
        ['route' => 'account.addresses.index', 'match' => 'account.addresses*', 'label' => 'Addresses', 'd' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
        ['route' => 'account.returns.index', 'match' => 'account.returns*', 'label' => 'Returns', 'd' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
        ['route' => 'account.tickets.index', 'match' => 'account.tickets*', 'label' => 'Tickets', 'd' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z'],
        ['route' => 'account.profile', 'match' => 'account.profile', 'label' => 'Settings', 'd' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];
@endphp

{{-- Read localStorage before Alpine loads to prevent flash --}}
<script>
(function(){
    var ex = localStorage.getItem('acSb') !== 'false';
    document.documentElement.style.setProperty('--sb-w', ex ? '200px' : '56px');
})();
</script>
<style>
    #ac-sidebar { width: var(--sb-w, 200px); }
    #ac-sidebar.sb-ready { transition: width 0.3s ease-in-out; }
</style>

<div x-data="{ ex: localStorage.getItem('acSb') !== 'false' }"
     x-init="$nextTick(() => { document.getElementById('ac-sidebar').classList.add('sb-ready'); $watch('ex', v => { localStorage.setItem('acSb', v); document.documentElement.style.setProperty('--sb-w', v ? '200px' : '56px'); }); })"
     class="lg:flex min-h-[calc(100vh-70px)]">

    {{-- Sidebar --}}
    <aside id="ac-sidebar" class="hidden lg:flex flex-col shrink-0 bg-white border-r border-neutral-100 sticky top-[70px] h-[calc(100vh-70px)] overflow-y-auto overflow-x-hidden"
           :style="ex ? 'width:200px' : 'width:56px'">

        {{-- Toggle --}}
        <button @click="ex = !ex" class="flex items-center justify-center h-10 shrink-0 text-neutral-400 hover:text-[#202a40] transition-colors border-b border-neutral-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        </button>

        {{-- Links --}}
        <nav class="flex-1 py-2 px-1.5 space-y-0.5">
            @foreach($links as $lk)
                <a href="{{ route($lk['route']) }}"
                   class="flex items-center h-9 rounded-md transition-colors duration-150 group relative overflow-hidden"
                   :class="ex ? 'px-3 gap-2.5' : 'justify-center'"
                   @class([
                       'bg-[#202a40]/10 text-[#202a40]' => request()->routeIs($lk['match']),
                       'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-700' => !request()->routeIs($lk['match']),
                   ])>
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $lk['d'] }}"/></svg>
                    <span x-show="ex" x-cloak class="text-xs font-medium whitespace-nowrap">{{ $lk['label'] }}</span>
                    <span x-show="!ex" x-cloak class="absolute left-full ml-3 px-2 py-1 bg-neutral-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">{{ $lk['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Logout --}}
        <div class="px-1.5 py-2 border-t border-neutral-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center w-full h-9 rounded-md text-neutral-400 hover:bg-red-50 hover:text-red-500 transition-colors group relative overflow-hidden"
                        :class="ex ? 'px-3 gap-2.5' : 'justify-center'">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span x-show="ex" x-cloak class="text-xs font-medium">Logout</span>
                    <span x-show="!ex" x-cloak class="absolute left-full ml-3 px-2 py-1 bg-neutral-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content: fills all remaining space --}}
    <div class="flex-1 min-w-0 pb-16 lg:pb-0 px-4 lg:px-6 py-6">
