<x-layouts.admin>
<x-slot name="title">Abandoned Checkouts</x-slot>
<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Abandoned Checkouts</h1>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.abandoned-checkouts.cleanup') }}" onsubmit="return confirm('Delete all entries without contact info?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 border border-red-200 rounded hover:bg-red-50 transition">
                    Clean Up (No Contact)
                </button>
            </form>
            <span class="text-sm text-gray-500">{{ $abandoned->total() }} total</span>
        </div>
    </div>

    {{-- Stats cards --}}
    @php
        $totalAbandoned = \App\Models\AbandonedCheckout::count();
        $recovered = \App\Models\AbandonedCheckout::where('recovered', true)->count();
        $withContact = \App\Models\AbandonedCheckout::where(function($q) { $q->whereNotNull('email')->orWhereNotNull('phone'); })->count();
        $totalValue = \App\Models\AbandonedCheckout::where('recovered', false)->sum('cart_total');
        $recoveryRate = $totalAbandoned > 0 ? round(($recovered / $totalAbandoned) * 100, 1) : 0;
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Abandoned</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalAbandoned }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Recovered</p>
            <p class="text-2xl font-bold text-green-600">{{ $recovered }} <span class="text-sm text-gray-400">({{ $recoveryRate }}%)</span></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">With Contact Info</p>
            <p class="text-2xl font-bold text-blue-600">{{ $withContact }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Lost Revenue</p>
            <p class="text-2xl font-bold text-red-600">{{ config('app.currency_symbol', '₹') }}{{ number_format($totalValue, 0) }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-800 text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">ID</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contact</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Cart Total</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Items</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Step</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
                @forelse($abandoned as $item)
                <tbody class="divide-y divide-gray-100" x-data="{ showItems: false }">
                <tr class="hover:bg-gray-50 {{ !$item->email && !$item->phone ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->id }}</td>
                    <td class="px-4 py-3">
                        @if($item->name || $item->email || $item->phone)
                            @if($item->name)<p class="font-medium text-gray-900">{{ $item->name }}</p>@endif
                            @if($item->email)<p class="text-xs text-gray-600">{{ $item->email }}</p>@endif
                            @if($item->phone)<p class="text-xs text-gray-600">{{ $item->phone }}</p>@endif
                        @else
                            <span class="text-xs text-gray-400 italic">No contact info</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ config('app.currency_symbol', '₹') }}{{ number_format($item->cart_total, 0) }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($item->cart_snapshot && count($item->cart_snapshot) > 0)
                        <button @click="showItems = !showItems" class="text-blue-600 hover:text-blue-800 underline cursor-pointer">
                            {{ $item->items_count ?? count($item->cart_snapshot) }} item{{ ($item->items_count ?? count($item->cart_snapshot)) > 1 ? 's' : '' }}
                        </button>
                        @else
                        {{ $item->items_count ?? 0 }}
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $stepColors = [
                                'checkout' => 'bg-amber-100 text-amber-800',
                                'contact_captured' => 'bg-blue-100 text-blue-800',
                                'payment' => 'bg-purple-100 text-purple-800',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded text-xs {{ $stepColors[$item->step] ?? 'bg-gray-100 text-gray-800' }}">{{ str_replace('_', ' ', ucfirst($item->step)) }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->recovered && $item->order_id)
                            <a href="{{ route('admin.orders.show', $item->order_id) }}" class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 font-medium text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Order #{{ $item->order_id }}
                            </a>
                            @if($item->recovered_at)
                            <p class="text-[10px] text-gray-400">{{ $item->recovered_at->diffForHumans() }}</p>
                            @endif
                        @elseif($item->recovered)
                            <span class="text-green-600 font-medium text-xs">Recovered</span>
                        @else
                            <span class="text-red-500 text-xs">Abandoned</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $item->created_at->format('d M Y') }}<br>
                        <span class="text-gray-400">{{ $item->created_at->format('h:i A') }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            @if(!$item->recovered && ($item->email || $item->phone))
                                <form method="POST" action="{{ route('admin.abandoned-checkouts.remind', $item->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Send Reminder" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" {{ $item->notified_at ? 'onclick=return\ confirm(\'Already\ notified.\ Send\ again?\')' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.abandoned-checkouts.delete', $item->id) }}" class="inline" onsubmit="return confirm('Delete this entry?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @if($item->notified_at)
                        <p class="text-[9px] text-gray-400 mt-0.5">Sent {{ $item->notified_at->format('d M H:i') }}</p>
                        @endif
                    </td>
                </tr>
                @if($item->cart_snapshot && count($item->cart_snapshot) > 0)
                <tr x-show="showItems" x-cloak>
                    <td colspan="8" class="px-8 py-3 bg-gray-50 border-t border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 mb-1.5">Cart Items:</p>
                        <div class="text-xs text-gray-600 space-y-1">
                            @foreach($item->cart_snapshot as $cartItem)
                            <div class="flex justify-between max-w-md">
                                <span>{{ $cartItem['product_name'] ?? 'Product' }} x{{ $cartItem['quantity'] ?? 1 }}</span>
                                <span class="font-medium">{{ config('app.currency_symbol', '₹') }}{{ number_format(($cartItem['price'] ?? 0) * ($cartItem['quantity'] ?? 1), 0) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                </tbody>
                @empty
                <tbody><tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">No abandoned checkouts yet</td></tr></tbody>
                @endforelse
        </table>
    </div>
    @if($abandoned->hasPages())
    <div class="mt-4">{{ $abandoned->links() }}</div>
    @endif
</div>
</x-layouts.admin>
