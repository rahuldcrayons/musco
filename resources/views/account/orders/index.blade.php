<x-layouts.app>
    <x-slot name="title">My Orders</x-slot>

    @include('account.partials.sidebar')
<div class="flex items-center justify-between mb-5">
                        <div>
                            <h1 class="text-xl font-bold text-neutral-900">My Orders</h1>
                            <p class="text-sm text-neutral-600 mt-0.5">{{ $orders->total() }} {{ Str::plural('order', $orders->total()) }}</p>
                        </div>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
                           style="background:#202a40;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            Continue Shopping
                        </a>
                    </div>

                    {{-- Status Filter Tabs --}}
                    @php
                        $statuses = [
                            '' => 'All',
                            'confirmed' => 'Confirmed',
                            'processing' => 'Processing',
                            'packed' => 'Packed',
                            'shipped' => 'Shipped',
                            'out_for_delivery' => 'Out for Delivery',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ];
                        $currentStatus = request('status', '');
                    @endphp
                    <div class="flex items-center gap-1.5 mb-5 overflow-x-auto pb-1 -mx-1 px-1">
                        @foreach($statuses as $value => $label)
                            <a href="{{ route('account.orders.index', $value ? ['status' => $value] : []) }}"
                               class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors
                                      {{ $currentStatus === $value ? 'bg-[#202a40] text-white' : 'bg-white border border-neutral-200 text-neutral-600 hover:border-neutral-300 hover:text-neutral-800' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Orders List --}}
                    @forelse($orders as $order)
                        @php
                            $statusColors = [
                                'pending' => 'bg-[#506282]/10 text-[#506282] border-[#506282]/30',
                                'confirmed' => 'bg-[#202a40]/5 text-[#222222] border-[#202a40]/30',
                                'processing' => 'bg-[#202a40]/5 text-[#222222] border-[#202a40]/30',
                                'packed' => 'bg-[#202a40]/10 text-[#222222] border-[#202a40]/30',
                                'shipped' => 'bg-[#202a40]/15 text-[#15383c] border-[#202a40]/40',
                                'out_for_delivery' => 'bg-[#202a40]/5 text-[#222222] border-[#202a40]/30',
                                'delivered' => 'bg-[#202a40]/10 text-[#202a40] border-[#202a40]/30',
                                'completed' => 'bg-[#202a40]/10 text-[#202a40] border-[#202a40]/30',
                                'cancelled' => 'bg-[#CC0C39]/10 text-[#CC0C39] border-[#CC0C39]/30',
                                'returned' => 'bg-neutral-100 text-neutral-600 border-neutral-200',
                            ];
                            $color = $statusColors[$order->status] ?? 'bg-neutral-50 text-neutral-600 border-neutral-200';
                        @endphp
                        <div class="bg-white rounded-xl border border-neutral-200 mb-3 overflow-hidden hover:shadow-sm transition-shadow">
                            {{-- Header --}}
                            <div class="px-4 py-3 flex flex-wrap items-center justify-between gap-3 border-b border-neutral-100">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('account.orders.show', $order) }}" class="text-sm font-bold text-neutral-900 hover:text-[#506282] transition-colors">
                                        {{ $order->order_number }}
                                    </a>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $color }}">
                                        {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-neutral-600">
                                    <span>{{ $order->created_at->format('M d, Y') }}</span>
                                    <span class="text-sm font-bold text-neutral-900">@price($order->total)</span>
                                </div>
                            </div>

                            {{-- Items --}}
                            <div class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    {{-- Product thumbnails --}}
                                    <div class="flex -space-x-2">
                                        @foreach($order->items->take(4) as $item)
                                            <div class="w-11 h-11 rounded-lg border-2 border-white overflow-hidden bg-neutral-100 shrink-0">
                                                @if($item->product && $item->product->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <div class="w-11 h-11 rounded-lg border-2 border-white bg-neutral-100 shrink-0 flex items-center justify-center">
                                                <span class="text-[10px] font-bold text-neutral-600">+{{ $order->items->count() - 4 }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Item names --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-neutral-700 line-clamp-1">
                                            {{ $order->items->take(2)->pluck('product_name')->join(', ') }}{{ $order->items->count() > 2 ? ' & ' . ($order->items->count() - 2) . ' more' : '' }}
                                        </p>
                                        <p class="text-xs text-neutral-600 mt-0.5">{{ $order->items->sum('quantity') }} {{ Str::plural('item', $order->items->sum('quantity')) }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="px-4 py-2.5 bg-neutral-50 border-t border-neutral-100 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('account.orders.show', $order) }}" class="text-xs font-semibold text-[#202a40] hover:text-[#222222] inline-flex items-center gap-1">
                                        View Details
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    @if(!in_array($order->status, ['cancelled', 'returned']))
                                        <a href="{{ route('account.orders.track', $order) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-700 inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                            Track
                                        </a>
                                    @endif
                                </div>
                                @if(in_array($order->status, ['confirmed', 'processing']))
                                    <form action="{{ route('account.orders.cancel', $order) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-[#CC0C39] hover:text-[#CC0C39]/80 transition-colors">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border border-neutral-200 p-12 text-center">
                            <div class="w-16 h-16 mx-auto bg-neutral-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-neutral-900 mb-1">No orders yet</h3>
                            <p class="text-sm text-neutral-600 mb-5">Start shopping to see your orders here.</p>
                            <a href="{{ url('/account/products') }}" class="inline-flex items-center gap-2 bg-[#202a40] text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors">
                                Browse Products
                            </a>
                        </div>
                    @endforelse

                    @if($orders->hasPages())
                    <div class="mt-5 flex items-center justify-between gap-3">
                        {{-- Prev --}}
                        @if($orders->onFirstPage())
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-neutral-300 bg-white border border-neutral-200 cursor-not-allowed select-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Prev
                            </span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-neutral-700 bg-white border border-neutral-200 hover:border-[#202a40] hover:text-[#202a40] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Prev
                            </a>
                        @endif

                        {{-- Page indicator --}}
                        <span class="text-xs text-neutral-500">
                            Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                            &nbsp;·&nbsp; {{ $orders->total() }} {{ Str::plural('order', $orders->total()) }}
                        </span>

                        {{-- Next --}}
                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-neutral-700 bg-white border border-neutral-200 hover:border-[#202a40] hover:text-[#202a40] transition-colors">
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-neutral-300 bg-white border border-neutral-200 cursor-not-allowed select-none">
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                    @endif
                @include('account.partials.sidebar-end')
</x-layouts.app>
