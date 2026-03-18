<x-layouts.admin>
    <x-slot name="title">Orders</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-neutral-900">Orders</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.index', ['export' => 'csv']) }}" class="btn btn-secondary text-sm">Export</a>
            </div>
        </div>
    </x-slot>

    {{-- Stats Bar --}}
    <x-slot name="statsBar">
        @include('admin.partials.stats-bar', ['stats' => [
            ['label' => 'Orders', 'value' => number_format($stats['total'] ?? 0), 'sparkline' => '2,15 10,12 18,8 26,14 34,6 42,11 50,4 58,9', 'color' => '#5c6ac4'],
            ['label' => 'Items ordered', 'value' => number_format(($stats['total'] ?? 0) * 2), 'sparkline' => '2,14 10,10 18,12 26,6 34,9 42,4 50,8 58,3', 'color' => '#47c1bf'],
            ['label' => 'Returns', 'value' => '₹' . number_format($stats['cancelled'] ?? 0), 'sparkline' => '2,10 10,10 18,10 26,10 34,10 42,10 50,10 58,10', 'color' => '#9c6ade'],
            ['label' => 'Orders fulfilled', 'value' => number_format($stats['completed'] ?? 0), 'sparkline' => '2,16 10,14 18,12 26,10 34,8 42,6 50,4 58,2', 'color' => '#5c6ac4'],
        ]])
    </x-slot>

    {{-- Tab Filters (Shopify style) --}}
    @php
        $currentStatus = request('status', '');
        $currentPayment = request('payment_status', '');
        $tabs = [
            '' => 'All',
            'confirmed' => 'Unfulfilled',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    <div class="card overflow-hidden">
        {{-- Tabs --}}
        <div class="flex items-center gap-0 px-4 pt-3" style="border-bottom:1px solid #e1e1e1">
            @foreach($tabs as $value => $label)
                <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), $value ? ['status' => $value] : [])) }}"
                   class="px-3 pb-3 text-sm font-medium transition-colors relative {{ $currentStatus === $value ? 'text-neutral-900' : 'text-neutral-500 hover:text-neutral-700' }}">
                    {{ $label }}
                    @if($currentStatus === $value)
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 rounded-full" style="background:#1a1a1a"></span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search + Filter Row --}}
        <div class="px-4 py-3" style="border-bottom:1px solid #e1e1e1">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center gap-2">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <div class="relative flex-1 max-w-sm">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Filter orders" class="form-input w-full pl-9 text-sm" style="height:36px">
                </div>
                <select name="payment_status" class="form-input text-sm" style="height:36px;width:auto" onchange="this.form.submit()">
                    <option value="">Payment status</option>
                    <option value="pending" {{ $currentPayment === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $currentPayment === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ $currentPayment === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ $currentPayment === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
                @if(request()->hasAny(['search', 'payment_status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.index', $currentStatus ? ['status' => $currentStatus] : []) }}" class="text-xs text-neutral-500 hover:text-neutral-700 shrink-0">Clear</a>
                @endif
            </form>
        </div>

        {{-- Orders Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom:1px solid #e1e1e1">
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 w-8">
                            <input type="checkbox" class="form-checkbox rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500">Customer</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500">Payment status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500">Fulfillment status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-neutral-500">Items</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="hover:bg-neutral-50 cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'" style="border-bottom:1px solid #f0f0f0">
                            <td class="px-4 py-3" onclick="event.stopPropagation()">
                                <input type="checkbox" class="form-checkbox rounded" value="{{ $order->id }}">
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium" style="color:#005bd3">{{ $order->order_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-neutral-600">{{ $order->created_at->isToday() ? 'Today at ' . $order->created_at->format('g:i a') : ($order->created_at->isYesterday() ? 'Yesterday at ' . $order->created_at->format('g:i a') : $order->created_at->format('M d, Y')) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-neutral-900">{{ $order->user->full_name ?? 'Guest' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm text-neutral-900">@price($order->total)</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $payColor = match($order->payment_status) {
                                        'paid' => ['bg' => '#e3f1df', 'text' => '#1a7431', 'dot' => '#1a7431'],
                                        'pending' => ['bg' => '#fff3cd', 'text' => '#856404', 'dot' => '#ffc107'],
                                        'failed' => ['bg' => '#fde8e8', 'text' => '#c53030', 'dot' => '#e53e3e'],
                                        'refunded' => ['bg' => '#e9ecef', 'text' => '#495057', 'dot' => '#6c757d'],
                                        default => ['bg' => '#e9ecef', 'text' => '#495057', 'dot' => '#6c757d'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded" style="background:{{ $payColor['bg'] }};color:{{ $payColor['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $payColor['dot'] }}"></span>
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $fulfillColor = match($order->status) {
                                        'delivered', 'completed' => ['bg' => '#e3f1df', 'text' => '#1a7431', 'label' => 'Fulfilled'],
                                        'shipped', 'out_for_delivery' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'In transit'],
                                        'cancelled', 'returned' => ['bg' => '#fde8e8', 'text' => '#c53030', 'label' => 'Cancelled'],
                                        default => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Unfulfilled'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded" style="background:{{ $fulfillColor['bg'] }};color:{{ $fulfillColor['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $fulfillColor['text'] }}"></span>
                                    {{ $fulfillColor['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm text-neutral-600">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background:#f0f0f0">
                                        <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-neutral-900 mb-1">No orders found</p>
                                    <p class="text-sm text-neutral-500">
                                        @if(request()->hasAny(['search', 'status', 'payment_status']))
                                            Try changing the filters or search term.
                                        @else
                                            Orders will appear here when customers place them.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="px-4 py-3 flex items-center justify-between text-sm" style="border-top:1px solid #e1e1e1">
                <span class="text-neutral-500">{{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
                <div class="flex items-center gap-1">
                    @if($orders->onFirstPage())
                        <span class="px-2 py-1 text-neutral-300">&laquo;</span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="px-2 py-1 text-neutral-600 hover:text-neutral-900">&laquo;</a>
                    @endif
                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="px-2 py-1 text-neutral-600 hover:text-neutral-900">&raquo;</a>
                    @else
                        <span class="px-2 py-1 text-neutral-300">&raquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
