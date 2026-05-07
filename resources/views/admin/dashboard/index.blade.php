<x-layouts.admin>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <h1 class="text-xl font-semibold text-neutral-900">Home</h1>
    </x-slot>

    {{-- Date Filter Pills --}}
    <div class="flex items-center gap-2 mb-5" x-data>
        <form method="GET" action="{{ route('admin.dashboard') }}" x-ref="filterForm" class="flex items-center gap-2">
            <input type="hidden" name="start_date" x-ref="startDate" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" x-ref="endDate" value="{{ request('end_date') }}">
            @php
                $isToday = request('start_date') == today()->format('Y-m-d') && request('end_date') == today()->format('Y-m-d');
                $is7d = request('start_date') == now()->subDays(6)->format('Y-m-d') && request('end_date') == today()->format('Y-m-d');
                $is30d = request('start_date') == now()->subDays(29)->format('Y-m-d') && request('end_date') == today()->format('Y-m-d');
                $isMonth = request('start_date') == now()->startOfMonth()->format('Y-m-d') && request('end_date') == today()->format('Y-m-d');
                $isYear = request('start_date') == now()->startOfYear()->format('Y-m-d') && request('end_date') == today()->format('Y-m-d');
                $noFilter = !$hasDateFilter;
                $pill = 'px-3 py-1.5 text-sm font-medium rounded-lg transition-colors cursor-pointer';
                $pillActive = 'color:#fff';
                $pillNormal = 'border:1px solid #c9cccf;color:#303030;background:#fff';
            @endphp
            <button type="button" @click="$refs.startDate.value='{{ today()->format('Y-m-d') }}';$refs.endDate.value='{{ today()->format('Y-m-d') }}';$refs.filterForm.submit()"
                    class="{{ $pill }}" style="{{ $isToday ? 'background:#1a1a1a;'.$pillActive : $pillNormal }}">Today</button>
            <button type="button" @click="$refs.startDate.value='{{ now()->subDays(6)->format('Y-m-d') }}';$refs.endDate.value='{{ today()->format('Y-m-d') }}';$refs.filterForm.submit()"
                    class="{{ $pill }}" style="{{ $is7d ? 'background:#1a1a1a;'.$pillActive : $pillNormal }}">7 Days</button>
            <button type="button" @click="$refs.startDate.value='{{ now()->subDays(29)->format('Y-m-d') }}';$refs.endDate.value='{{ today()->format('Y-m-d') }}';$refs.filterForm.submit()"
                    class="{{ $pill }}" style="{{ $is30d || $noFilter ? 'background:#1a1a1a;'.$pillActive : $pillNormal }}">30 Days</button>
            <button type="button" @click="$refs.startDate.value='{{ now()->startOfMonth()->format('Y-m-d') }}';$refs.endDate.value='{{ today()->format('Y-m-d') }}';$refs.filterForm.submit()"
                    class="{{ $pill }}" style="{{ $isMonth ? 'background:#1a1a1a;'.$pillActive : $pillNormal }}">This Month</button>
            <button type="button" @click="$refs.startDate.value='{{ now()->startOfYear()->format('Y-m-d') }}';$refs.endDate.value='{{ today()->format('Y-m-d') }}';$refs.filterForm.submit()"
                    class="{{ $pill }}" style="{{ $isYear ? 'background:#1a1a1a;'.$pillActive : $pillNormal }}">This Year</button>
        </form>
    </div>

    {{-- Stats Bar --}}
    <div class="bg-white overflow-hidden mb-5" style="border-radius:12px;border:1px solid #e3e3e3">
        <div class="flex">
            <div class="flex-1 px-5 py-4">
                <p class="text-xs font-medium mb-1" style="color:#6d7175">Orders</p>
                <p class="text-2xl font-semibold" style="color:#1a1a1a">{{ number_format($topOrders) }}</p>
            </div>
            <div class="flex-1 px-5 py-4" style="border-left:1px solid #e3e3e3">
                <p class="text-xs font-medium mb-1" style="color:#6d7175">Revenue</p>
                <p class="text-2xl font-semibold" style="color:#1a1a1a">@price($topRevenue)</p>
            </div>
            <div class="flex-1 px-5 py-4" style="border-left:1px solid #e3e3e3">
                <p class="text-xs font-medium mb-1" style="color:#6d7175">Customers</p>
                <p class="text-2xl font-semibold" style="color:#1a1a1a">{{ number_format($totalCustomers) }}</p>
            </div>
            <div class="flex-1 px-5 py-4" style="border-left:1px solid #e3e3e3">
                <p class="text-xs font-medium mb-1" style="color:#6d7175">Confirmed</p>
                <p class="text-2xl font-semibold" style="color:#1a1a1a">{{ number_format($pendingOrders) }}</p>
            </div>
            <div class="flex-1 px-5 py-4" style="border-left:1px solid #e3e3e3">
                <p class="text-xs font-medium mb-1" style="color:#6d7175">Returns</p>
                <p class="text-2xl font-semibold" style="color:#1a1a1a">{{ number_format($totalReturns) }}</p>
            </div>
            <div class="flex-1 px-5 py-4" style="border-left:1px solid #e3e3e3">
                <p class="text-xs font-medium mb-1" style="color:#6d7175">Products</p>
                <p class="text-2xl font-semibold" style="color:#1a1a1a">{{ number_format($totalProducts) }}</p>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="lg:col-span-2 bg-white" style="border-radius:12px;border:1px solid #e3e3e3">
            <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid #e3e3e3">
                <p class="text-sm font-semibold" style="color:#1a1a1a">Revenue Overview</p>
                <p class="text-xs" style="color:#6d7175">{{ $hasDateFilter ? $startDate->format('M d') . ' - ' . $endDate->format('M d, Y') : 'Last 7 Days' }}</p>
            </div>
            <div class="p-4"><canvas id="revenueChart" height="240"></canvas></div>
        </div>
        <div class="bg-white" style="border-radius:12px;border:1px solid #e3e3e3">
            <div class="px-5 py-4" style="border-bottom:1px solid #e3e3e3">
                <p class="text-sm font-semibold" style="color:#1a1a1a">Order Status</p>
            </div>
            <div class="p-4 flex items-center justify-center"><canvas id="orderStatusChart" height="220"></canvas></div>
        </div>
    </div>

    {{-- Monthly Revenue + Performance --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="lg:col-span-2 bg-white" style="border-radius:12px;border:1px solid #e3e3e3">
            <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid #e3e3e3">
                <p class="text-sm font-semibold" style="color:#1a1a1a">Monthly Revenue</p>
            </div>
            <div class="p-4"><canvas id="monthlyRevenueChart" height="200"></canvas></div>
        </div>
        <div class="bg-white" style="border-radius:12px;border:1px solid #e3e3e3">
            <div class="px-5 py-4" style="border-bottom:1px solid #e3e3e3">
                <p class="text-sm font-semibold" style="color:#1a1a1a">Performance</p>
            </div>
            <div class="p-5 space-y-5">
                @foreach([
                    ['label' => 'Completion Rate', 'value' => $completionRate, 'detail' => number_format($completedOrders).' of '.number_format($totalOrders).' delivered', 'color' => '#10b981'],
                    ['label' => 'Cancellation Rate', 'value' => $cancellationRate, 'detail' => number_format($cancelledOrders).' of '.number_format($totalOrders).' cancelled', 'color' => '#ef4444'],
                    ['label' => 'Active Products', 'value' => $productActiveRate, 'detail' => number_format($activeProducts).' of '.number_format($totalProducts).' active', 'color' => '#6366f1'],
                ] as $metric)
                <div class="flex items-center gap-4">
                    <div class="relative w-14 h-14 shrink-0">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $metric['color'] }}" stroke-width="8" stroke-dasharray="{{ 2*3.14159*52 }}" stroke-dashoffset="{{ 2*3.14159*52*(1-$metric['value']/100) }}" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center"><span class="text-xs font-bold" style="color:#1a1a1a">{{ $metric['value'] }}%</span></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color:#1a1a1a">{{ $metric['label'] }}</p>
                        <p class="text-xs" style="color:#6d7175">{{ $metric['detail'] }}</p>
                    </div>
                </div>
                @endforeach
                <div class="pt-4 space-y-2" style="border-top:1px solid #e3e3e3">
                    <div class="flex justify-between text-sm"><span style="color:#6d7175">Total Revenue</span><span class="font-semibold" style="color:#1a1a1a">@price($totalRevenue)</span></div>
                    <div class="flex justify-between text-sm"><span style="color:#6d7175">Total Orders</span><span class="font-semibold" style="color:#1a1a1a">{{ number_format($totalOrders) }}</span></div>
                    <div class="flex justify-between text-sm"><span style="color:#6d7175">Total Sellers</span><span class="font-semibold" style="color:#1a1a1a">{{ number_format($totalSellers) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders + Top Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white overflow-hidden" style="border-radius:12px;border:1px solid #e3e3e3">
            <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid #e3e3e3">
                <p class="text-sm font-semibold" style="color:#1a1a1a">Recent Orders</p>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium" style="color:#2c6ecb">View all</a>
            </div>
            <table class="w-full">
                <thead><tr style="border-bottom:1px solid #e3e3e3">
                    <th class="px-4 py-3 text-left text-xs font-medium" style="color:#6d7175">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-medium" style="color:#6d7175">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium" style="color:#6d7175">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium" style="color:#6d7175">Total</th>
                </tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-neutral-50 cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'" style="border-bottom:1px solid #f0f0f0">
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium" style="color:#2c6ecb">{{ $order->order_number }}</span>
                            <p class="text-xs" style="color:#6d7175">{{ $order->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm" style="color:#1a1a1a">{{ $order->user->full_name ?? 'Guest' }}</td>
                        <td class="px-4 py-3">
                            @php $sc = match($order->status) { 'delivered','completed' => ['#e3f1df','#1a7431'], 'cancelled' => ['#fde8e8','#c53030'], default => ['#fef3c7','#92400e'] }; @endphp
                            <span class="text-xs font-medium px-2 py-0.5 rounded" style="background:{{ $sc[0] }};color:{{ $sc[1] }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium" style="color:#1a1a1a">@price($order->total)</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-sm" style="color:#6d7175">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white overflow-hidden" style="border-radius:12px;border:1px solid #e3e3e3">
            <div class="px-5 py-4" style="border-bottom:1px solid #e3e3e3">
                <p class="text-sm font-semibold" style="color:#1a1a1a">Top Products</p>
            </div>
            @forelse($topProducts as $i => $product)
            <div class="px-4 py-3 flex items-center gap-3" style="{{ !$loop->last ? 'border-bottom:1px solid #f0f0f0' : '' }}">
                <span class="text-xs font-medium w-4 text-center shrink-0" style="color:#999">{{ $i+1 }}</span>
                <div class="w-8 h-8 rounded overflow-hidden shrink-0" style="background:#f4f4f4;border:1px solid #e3e3e3">
                    @if($product->primary_image_url)
                        <img src="{{ $product->primary_image_url }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" style="color:#1a1a1a">{{ $product->name }}</p>
                    <p class="text-xs" style="color:#6d7175">{{ $product->total_sold ?? 0 }} sold</p>
                </div>
                <span class="text-sm font-medium" style="color:#1a1a1a">@price($product->price)</span>
            </div>
            @empty
            <div class="p-4 text-center text-sm" style="color:#6d7175">No products yet</div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fontFamily = "'Inter', sans-serif";

            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Revenue', data: @json($chartRevenue),
                        borderColor: '#5c6ac4', backgroundColor: 'rgba(92,106,196,.06)',
                        borderWidth: 2, fill: true, tension: 0.4,
                        pointBackgroundColor: '#5c6ac4', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 3, pointHoverRadius: 5
                    }, {
                        label: 'Orders', data: @json($chartOrders),
                        borderColor: '#47c1bf', backgroundColor: 'rgba(71,193,191,.04)',
                        borderWidth: 2, fill: true, tension: 0.4,
                        pointBackgroundColor: '#47c1bf', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 2, pointHoverRadius: 4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 11, family: fontFamily } } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, family: fontFamily }, color: '#999' } },
                        y: { position: 'left', grid: { color: '#f5f5f5' }, ticks: { font: { size: 11, family: fontFamily }, color: '#999', callback: v => '£'+v.toLocaleString() } },
                        y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { font: { size: 11, family: fontFamily }, color: '#999', stepSize: 1 } }
                    }
                }
            });

            const statusData = @json($orderStatusCounts);
            new Chart(document.getElementById('orderStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase()+s.slice(1).replace('_',' ')),
                    datasets: [{ data: Object.values(statusData), backgroundColor: Object.keys(statusData).map(s => ({pending:'#f59e0b',confirmed:'#3b82f6',processing:'#8b5cf6',packed:'#6366f1',shipped:'#06b6d4',out_for_delivery:'#14b8a6',delivered:'#10b981',completed:'#059669',cancelled:'#ef4444',returned:'#f97316'})[s]||'#bbb'), borderWidth: 2, borderColor: '#fff', hoverOffset: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', padding: 10, font: { size: 11, family: fontFamily } } } } }
            });

            new Chart(document.getElementById('monthlyRevenueChart'), {
                type: 'bar',
                data: {
                    labels: @json($monthLabels),
                    datasets: [{ label: 'Revenue', data: @json($monthData), backgroundColor: 'rgba(92,106,196,.15)', hoverBackgroundColor: 'rgba(92,106,196,.3)', borderColor: '#5c6ac4', borderWidth: 1, borderRadius: 6, borderSkipped: false }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { font: { size: 11, family: fontFamily }, color: '#999' } }, y: { grid: { color: '#f5f5f5' }, ticks: { font: { size: 11, family: fontFamily }, color: '#999', callback: v => '£'+v.toLocaleString() } } } }
            });
        });
    </script>
    @endpush
</x-layouts.admin>
