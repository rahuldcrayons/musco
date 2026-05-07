<x-layouts.app>
    <x-slot name="title">My Account - {{ config('app.name') }}</x-slot>


    @include('account.partials.sidebar')

                <!-- Loyalty Points + Order Stats -->
                <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">

                    {{-- Reward Points --}}
                    <div class="col-span-2 bg-gradient-to-br from-[#506282]/15 to-[#506282]/5 border border-[#506282]/25 rounded-2xl p-4 flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-[#506282]/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#506282]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-[#506282] leading-none">{{ number_format($user->loyalty_points_balance ?? 0) }}</div>
                            <div class="text-[11px] text-[#506282]/80 font-medium mt-0.5">Reward Points</div>
                            @if(($user->loyalty_points_balance ?? 0) > 0)
                                <div class="text-[10px] text-[#506282]/70 mt-1">Worth @price(($user->loyalty_points_balance ?? 0) * 0.25)</div>
                            @endif
                        </div>
                    </div>

                    {{-- Total Orders --}}
                    <div class="bg-white border border-neutral-100 rounded-2xl p-4 flex flex-col items-center gap-2 hover:border-[#202a40]/30 hover:shadow-sm transition-all">
                        <div class="w-10 h-10 rounded-full bg-[#202a40]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-neutral-900 leading-none">{{ $orderStats['total'] }}</div>
                            <div class="text-[11px] text-neutral-500 mt-0.5">Total Orders</div>
                        </div>
                    </div>

                    {{-- Confirmed --}}
                    <div class="bg-white border border-neutral-100 rounded-2xl p-4 flex flex-col items-center gap-2 hover:border-amber-300/50 hover:shadow-sm transition-all">
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600 leading-none">{{ $orderStats['confirmed'] }}</div>
                            <div class="text-[11px] text-neutral-500 mt-0.5">Confirmed</div>
                        </div>
                    </div>

                    {{-- Processing --}}
                    <div class="bg-white border border-neutral-100 rounded-2xl p-4 flex flex-col items-center gap-2 hover:border-blue-300/50 hover:shadow-sm transition-all">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 leading-none">{{ $orderStats['processing'] }}</div>
                            <div class="text-[11px] text-neutral-500 mt-0.5">Processing</div>
                        </div>
                    </div>

                    {{-- Completed --}}
                    <div class="bg-white border border-neutral-100 rounded-2xl p-4 flex flex-col items-center gap-2 hover:border-emerald-300/50 hover:shadow-sm transition-all">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600 leading-none">{{ $orderStats['completed'] }}</div>
                            <div class="text-[11px] text-neutral-500 mt-0.5">Completed</div>
                        </div>
                    </div>

                </div>

                <!-- Wallet Balance -->
                @if($creditBalance > 0)
                    <div class="bg-white border border-success-200 rounded-xl p-5 mb-6 flex items-center gap-4">
                        <div class="w-12 h-12 bg-success-50 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-neutral-600">Store Credit Balance</p>
                            <p class="text-xl font-bold text-success-600">@price($creditBalance)</p>
                        </div>
                        <p class="ml-auto text-xs text-neutral-600">Available for your next order</p>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
                    <a href="{{ route('account.orders.index') }}" class="bg-white border border-neutral-100 rounded-xl p-4 text-center hover:border-[#202a40]/30 hover:shadow-sm transition-all group">
                        <svg class="w-7 h-7 mx-auto text-[#202a40] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="text-[13px] font-medium text-neutral-700">My Orders</span>
                    </a>
                    <a href="{{ route('wishlist') }}" class="bg-white border border-neutral-100 rounded-xl p-4 text-center hover:border-[#202a40]/30 hover:shadow-sm transition-all group">
                        <svg class="w-7 h-7 mx-auto text-[#202a40] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="text-[13px] font-medium text-neutral-700">Wishlist ({{ $wishlistCount }})</span>
                    </a>
                    <a href="{{ route('account.addresses.index') }}" class="bg-white border border-neutral-100 rounded-xl p-4 text-center hover:border-[#202a40]/30 hover:shadow-sm transition-all group">
                        <svg class="w-7 h-7 mx-auto text-[#202a40] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-[13px] font-medium text-neutral-700">Addresses</span>
                    </a>
                    <a href="{{ route('account.profile') }}" class="bg-white border border-neutral-100 rounded-xl p-4 text-center hover:border-[#202a40]/30 hover:shadow-sm transition-all group">
                        <svg class="w-7 h-7 mx-auto text-[#202a40] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-[13px] font-medium text-neutral-700">Settings</span>
                    </a>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white border border-neutral-100 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
                        <h2 class="text-[15px] font-semibold text-neutral-900">Recent Orders</h2>
                        <a href="{{ route('account.orders.index') }}" class="text-[13px] text-[#202a40] hover:text-[#2d3a55] font-medium">View All</a>
                    </div>

                    @if($recentOrders->count())
                        <div class="divide-y divide-neutral-100">
                            @foreach($recentOrders as $order)
                                <div class="px-5 py-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-11 h-11 bg-neutral-50 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                                            @if($order->items->first()?->product?->primary_image_url)
                                                <img src="{{ $order->items->first()->product->primary_image_url }}" alt="{{ $order->items->first()->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-5 h-5 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('account.orders.show', $order) }}" class="text-[13px] font-semibold text-neutral-900 hover:text-[#202a40]">
                                                Order #{{ $order->order_number }}
                                            </a>
                                            <p class="text-xs text-neutral-600 mt-0.5">
                                                {{ $order->created_at->format('M d, Y') }} &middot; {{ $order->items_count }} items
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="text-[13px] font-semibold text-neutral-900">@price($order->total)</div>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                                            {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-600' :
                                               ($order->status === 'pending'   ? 'bg-amber-50 text-amber-600' :
                                               ($order->status === 'cancelled' ? 'bg-red-50 text-red-500' :
                                                'bg-[#202a40]/10 text-[#202a40]')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Prev / Next pagination --}}
                        <div class="px-4 py-3 border-t border-neutral-100 flex items-center justify-between gap-2">
                            {{-- Prev --}}
                            @if($recentOrders->onFirstPage())
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium text-neutral-300 bg-neutral-50 border border-neutral-100 cursor-not-allowed select-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Prev
                                </span>
                            @else
                                <a href="{{ $recentOrders->previousPageUrl() }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white transition-colors"
                                   style="background:#202a40;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Prev
                                </a>
                            @endif

                            <span class="text-[11px] text-neutral-400">
                                {{ $recentOrders->currentPage() }} / {{ $recentOrders->lastPage() }}
                            </span>

                            {{-- Next --}}
                            @if($recentOrders->hasMorePages())
                                <a href="{{ $recentOrders->nextPageUrl() }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white transition-colors"
                                   style="background:#202a40;">
                                    Next
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium text-neutral-300 bg-neutral-50 border border-neutral-100 cursor-not-allowed select-none">
                                    Next
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-neutral-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="text-sm text-neutral-600 mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-white bg-[#202a40] hover:bg-[#2d3a55] rounded-lg transition-colors">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
    @include('account.partials.sidebar-end')
</x-layouts.app>
