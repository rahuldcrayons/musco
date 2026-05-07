<x-layouts.admin>
    <x-slot name="title">Delivery Dashboard</x-slot>

    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-semibold text-gray-900">Delivery Dashboard</h1>
        <div class="flex gap-2">
            <form action="{{ route('admin.delivery.pickup') }}" method="POST" class="inline" x-data>
                @csrf
                <input type="hidden" name="package_count" value="{{ $stats['pending'] }}">
                <button type="submit" class="btn btn-outline text-sm">Request Pickup</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#202a40]/5 border border-[#202a40]/20 text-[#202a40] text-sm rounded-lg flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1">
                    <p class="font-semibold text-red-700">{{ session('error') }}</p>
                    @if(str_contains(session('error', ''), 'not found') || str_contains(session('error', ''), 'Pickup location'))
                        <div class="mt-2 flex items-center gap-2">
                            <a href="{{ route('admin.settings.general') }}" class="text-xs font-semibold px-3 py-1.5 bg-[#202a40] text-white rounded-lg hover:bg-[#2d3a55] transition-colors">Fix in Settings</a>
                            <button type="button" onclick="fetchWarehouses()" class="text-xs font-semibold px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors">View Warehouses</button>
                        </div>
                        <div id="warehouse-list" class="mt-2 hidden text-xs bg-white border border-red-200 rounded-lg p-3"></div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
    function fetchWarehouses() {
        const el = document.getElementById('warehouse-list');
        el.classList.remove('hidden');
        el.innerHTML = '<span class="text-gray-500">Fetching from Delhivery…</span>';
        fetch('{{ route('admin.delivery.warehouses') }}')
            .then(r => r.json())
            .then(data => {
                if (!data.warehouses || data.warehouses.length === 0) {
                    el.innerHTML = '<span class="text-red-600">No warehouses found. Check your Delhivery API token in settings.</span>';
                    return;
                }
                const match = data.match
                    ? `<p class="text-green-700 font-semibold mb-1">✓ "${data.configured}" matches a registered warehouse.</p>`
                    : `<p class="text-red-700 font-semibold mb-1">✗ "${data.configured}" does NOT match any warehouse. Use one of the names below:</p>`;
                const rows = data.warehouses.map(w =>
                    `<div class="flex items-center justify-between py-1 border-b border-gray-100 last:border-0">
                        <span class="font-mono font-bold text-gray-900">${w.name}</span>
                        <span class="text-gray-500">${w.city} ${w.postcode}</span>
                     </div>`
                ).join('');
                el.innerHTML = match + rows;
            })
            .catch(() => { el.innerHTML = '<span class="text-red-600">Failed to fetch warehouses.</span>'; });
    }
    </script>
    @endpush

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-[#506282]">{{ $stats['pending'] }}</div>
            <div class="text-xs text-gray-600">Pending Booking</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-[#202a40]">{{ $stats['shipped'] }}</div>
            <div class="text-xs text-gray-600">In Transit</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-neutral-700">{{ $stats['out_for_delivery'] }}</div>
            <div class="text-xs text-gray-600">Out for Delivery</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-[#202a40]">{{ $stats['delivered'] }}</div>
            <div class="text-xs text-gray-600">Delivered (7d)</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-[#CC0C39]">{{ $stats['rto'] }}</div>
            <div class="text-xs text-gray-600">RTO (30d)</div>
        </div>
    </div>

    {{-- Analytics (30-day) --}}
    @if(isset($analytics))
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">Total Shipped (30d)</div>
            <div class="text-xl font-bold text-gray-900">{{ $analytics['total_shipped'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">Delivery Rate</div>
            <div class="text-xl font-bold {{ $analytics['delivery_rate'] >= 90 ? 'text-[#202a40]' : ($analytics['delivery_rate'] >= 75 ? 'text-[#506282]' : 'text-[#CC0C39]') }}">{{ $analytics['delivery_rate'] }}%</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">RTO Rate</div>
            <div class="text-xl font-bold {{ $analytics['rto_rate'] <= 5 ? 'text-[#202a40]' : ($analytics['rto_rate'] <= 15 ? 'text-[#506282]' : 'text-[#CC0C39]') }}">{{ $analytics['rto_rate'] }}%</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">Avg Delivery Days</div>
            <div class="text-xl font-bold text-gray-900">{{ round($analytics['avg_delivery_days'], 1) }}</div>
        </div>
    </div>
    @endif
    {{-- Pending Orders — Ready to Ship --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Ready to Ship ({{ $stats['pending'] }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Order</th>
                        <th class="px-4 py-2 text-left">Customer</th>
                        <th class="px-4 py-2 text-left">Items</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Payment</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-primary-600 hover:underline">{{ $order->order_number }}</a>
                        </td>
                        <td class="px-4 py-2 text-gray-700">{{ $order->customer_name ?? $order->user?->full_name ?? 'Guest' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $order->items->count() }} item(s)</td>
                        <td class="px-4 py-2 font-medium">@price($order->total)</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full {{ $order->payment_method === 'cod' ? 'bg-[#506282]/10 text-[#506282]' : 'bg-[#202a40]/10 text-[#202a40]' }}">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-500 text-xs">{{ $order->created_at->format('M d, h:i A') }}</td>
                        <td class="px-4 py-2 text-right">
                            <form action="{{ route('admin.delivery.book', $order) }}" method="POST" class="inline" onsubmit="return confirm('Book delivery for {{ $order->order_number }}?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#202a40] text-white text-xs font-medium rounded-lg hover:bg-[#202a40]/90">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25"/></svg>
                                    Book Delivery
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No orders pending shipment</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Shipped Orders — Track --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">In Transit ({{ $stats['shipped'] + $stats['out_for_delivery'] }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Order</th>
                        <th class="px-4 py-2 text-left">AWB</th>
                        <th class="px-4 py-2 text-left">Customer</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Shipped</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shippedOrders as $order)
                    <tr class="hover:bg-gray-50" x-data="{ tracking: null, showTracking: false }">
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-primary-600 hover:underline">{{ $order->order_number }}</a>
                        </td>
                        <td class="px-4 py-2 font-mono text-xs text-gray-700">{{ $order->tracking_number }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $order->customer_name ?? $order->user?->full_name ?? 'Guest' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full {{ $order->status === 'shipped' ? 'bg-[#202a40]/10 text-[#202a40]' : 'bg-neutral-100 text-neutral-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-500 text-xs">{{ $order->shipped_at?->format('M d') ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="fetch('{{ route('admin.delivery.track', $order) }}').then(r=>r.json()).then(d=>{tracking=d;showTracking=true})"
                                        class="text-xs text-[#202a40] hover:underline">Track</button>
                                <a href="{{ route('admin.delivery.label', $order) }}" class="text-xs text-gray-600 hover:underline">Label</a>
                                <form action="{{ route('admin.delivery.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this shipment?')">
                                    @csrf
                                    <button type="submit" class="text-xs text-[#CC0C39] hover:underline">Cancel</button>
                                </form>
                            </div>
                            {{-- Tracking details --}}
                            <div x-show="showTracking" x-cloak class="mt-2 text-left p-3 bg-gray-50 rounded-lg text-xs">
                                <template x-if="tracking?.success">
                                    <div>
                                        <p class="font-medium" x-text="'Status: ' + tracking.status"></p>
                                        <p class="text-gray-500" x-text="'Location: ' + tracking.status_location"></p>
                                        <p x-show="tracking.expected_date" class="text-gray-500" x-text="'Expected: ' + tracking.expected_date"></p>
                                        <template x-if="tracking.scans?.length">
                                            <div class="mt-2 space-y-1 max-h-40 overflow-y-auto">
                                                <template x-for="scan in tracking.scans" :key="scan.datetime">
                                                    <div class="flex gap-2">
                                                        <span class="text-gray-400 whitespace-nowrap" x-text="scan.datetime"></span>
                                                        <span x-text="scan.status + ' - ' + scan.location"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="tracking && !tracking.success">
                                    <p class="text-[#CC0C39]" x-text="tracking.message"></p>
                                </template>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No shipments in transit</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    function pincodeChecker() {
        return {
            pincode: '', weight: 500, paymentType: 'Pre-paid', loading: false, result: null,
            async check() {
                if (this.pincode.length !== 6) return;
                this.loading = true;
                try {
                    const res = await fetch(`{{ route('admin.delivery.check-pincode') }}?pincode=${this.pincode}&weight=${this.weight}&payment_type=${this.paymentType}`);
                    this.result = await res.json();
                } catch(e) { this.result = { serviceable: false, message: 'Error' }; }
                this.loading = false;
            }
        };
    }
    </script>
    @endpush
</x-layouts.admin>
