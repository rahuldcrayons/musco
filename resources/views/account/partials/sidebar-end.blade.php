    </div>{{-- end main content --}}
</div>{{-- end sidebar flex wrapper --}}

{{-- Mobile: Bottom tab bar --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-neutral-100 px-1 py-1 flex justify-around">
    @php
        $mLinks = [
            ['route' => 'account.dashboard', 'match' => 'account.dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'label' => 'Home'],
            ['route' => 'account.orders.index', 'match' => 'account.orders*', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'label' => 'Orders'],
            ['route' => 'wishlist', 'match' => 'wishlist*', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'label' => 'Wishlist'],
            ['route' => 'account.profile', 'match' => 'account.profile', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profile'],
        ];
    @endphp
    @foreach($mLinks as $ml)
        <a href="{{ route($ml['route']) }}" @class([
            'flex items-center justify-center px-3 py-2 rounded-lg text-[11px] font-semibold transition-colors',
            'text-[#202a40] bg-[#202a40]/8' => request()->routeIs($ml['match']),
            'text-neutral-500 hover:text-[#202a40]' => !request()->routeIs($ml['match']),
        ])>
            {{ $ml['label'] }}
        </a>
    @endforeach
</div>
