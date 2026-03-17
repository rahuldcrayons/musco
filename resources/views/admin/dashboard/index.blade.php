<x-layouts.admin>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Date Range Filter -->
    <div class="card p-4 mb-6" x-data>
        <form method="GET" action="{{ route('admin.dashboard') }}" x-ref="filterForm" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-35">
                <label class="block text-xs font-medium text-neutral-600 mb-1">Start Date</label>
                <input type="date" name="start_date" x-ref="startDate" value="{{ request('start_date') }}"
                       class="form-input w-full text-sm">
            </div>
            <div class="flex-1 min-w-35">
                <label class="block text-xs font-medium text-neutral-600 mb-1">End Date</label>
                <input type="date" name="end_date" x-ref="endDate" value="{{ request('end_date') }}"
                       class="form-input w-full text-sm">
            </div>
            <button type="submit" class="btn btn-primary h-9 px-4 text-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            @if($hasDateFilter)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary h-9 px-4 text-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
            <!-- Quick Presets -->
            <div class="flex items-center gap-1.5 ml-auto">
                <button type="button" @click="$refs.startDate.value='{{ today()->format('Y-m-d') }}'; $refs.endDate.value='{{ today()->format('Y-m-d') }}'; $refs.filterForm.submit()"
                        class="px-2.5 py-1.5 text-xs font-medium rounded-md {{ request('start_date') == today()->format('Y-m-d') && request('end_date') == today()->format('Y-m-d') ? 'bg-primary-100 text-primary-700' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">
                    Today
                </button>
                <button type="button" @click="$refs.startDate.value='{{ now()->subDays(6)->format('Y-m-d') }}'; $refs.endDate.value='{{ today()->format('Y-m-d') }}'; $refs.filterForm.submit()"
                        class="px-2.5 py-1.5 text-xs font-medium rounded-md {{ request('start_date') == now()->subDays(6)->format('Y-m-d') && request('end_date') == today()->format('Y-m-d') ? 'bg-primary-100 text-primary-700' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">
                    7 Days
                </button>
                <button type="button" @click="$refs.startDate.value='{{ now()->subDays(29)->format('Y-m-d') }}'; $refs.endDate.value='{{ today()->format('Y-m-d') }}'; $refs.filterForm.submit()"
                        class="px-2.5 py-1.5 text-xs font-medium rounded-md {{ request('start_date') == now()->subDays(29)->format('Y-m-d') && request('end_date') == today()->format('Y-m-d') ? 'bg-primary-100 text-primary-700' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">
                    30 Days
                </button>
                <button type="button" @click="$refs.startDate.value='{{ now()->startOfMonth()->format('Y-m-d') }}'; $refs.endDate.value='{{ today()->format('Y-m-d') }}'; $refs.filterForm.submit()"
                        class="px-2.5 py-1.5 text-xs font-medium rounded-md {{ request('start_date') == now()->startOfMonth()->format('Y-m-d') && request('end_date') == today()->format('Y-m-d') ? 'bg-primary-100 text-primary-700' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">
                    This Month
                </button>
                <button type="button" @click="$refs.startDate.value='{{ now()->startOfYear()->format('Y-m-d') }}'; $refs.endDate.value='{{ today()->format('Y-m-d') }}'; $refs.filterForm.submit()"
                        class="px-2.5 py-1.5 text-xs font-medium rounded-md {{ request('start_date') == now()->startOfYear()->format('Y-m-d') && request('end_date') == today()->format('Y-m-d') ? 'bg-primary-100 text-primary-700' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">
                    This Year
                </button>
            </div>
        </form>

        @if($hasDateFilter)
            <div class="mt-3 pt-3 border-t border-neutral-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-neutral-600">
                    Showing data from <span class="font-semibold text-neutral-900">{{ $startDate->format('M d, Y') }}</span>
                    to <span class="font-semibold text-neutral-900">{{ $endDate->format('M d, Y') }}</span>
                </span>
            </div>
        @endif
    </div>

    <!-- Stats Grid - Row 1 -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
        <!-- Orders -->
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">{{ $hasDateFilter ? 'Orders' : "Today's Orders" }}</p>
                    <p class="text-xl font-bold text-neutral-900">{{ number_format($topOrders) }}</p>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">{{ $hasDateFilter ? 'Revenue' : "Today's Revenue" }}</p>
                    <p class="text-xl font-bold text-neutral-900">@price($topRevenue)</p>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Customers</p>
                    <p class="text-xl font-bold text-neutral-900">{{ number_format($totalCustomers) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid - Row 2 -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <!-- Pending Orders -->
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-warning-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Confirmed Orders</p>
                    <p class="text-xl font-bold text-neutral-900">{{ number_format($pendingOrders) }}</p>
                </div>
            </div>
        </div>

        <!-- Returns -->
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-error-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Returns</p>
                    <p class="text-xl font-bold text-neutral-900">{{ number_format($totalReturns) }}</p>
                    @if($pendingReturns > 0)
                        <p class="text-[11px] text-warning-600">{{ $pendingReturns }} pending</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products & Sellers -->
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Products / Sellers</p>
                    <p class="text-xl font-bold text-neutral-900">{{ number_format($totalProducts) }} <span class="text-sm font-normal text-neutral-600">/</span> <span class="text-base">{{ number_format($totalSellers) }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 card">
            <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-semibold text-neutral-900 text-sm">Revenue Overview</h2>
                <span class="text-xs text-neutral-600">{{ $hasDateFilter ? $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y') : 'Last 7 Days' }}</span>
            </div>
            <div class="p-4">
                <canvas id="revenueChart" height="240"></canvas>
            </div>
        </div>

        <!-- Order Status Donut -->
        <div class="card">
            <div class="px-5 py-3.5 border-b border-neutral-200">
                <h2 class="font-semibold text-neutral-900 text-sm">Order Status</h2>
            </div>
            <div class="p-4 flex items-center justify-center">
                <canvas id="orderStatusChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue + Performance Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Monthly Revenue Bar Chart -->
        <div class="lg:col-span-2 card">
            <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-semibold text-neutral-900 text-sm">Monthly Revenue</h2>
                <span class="text-xs text-neutral-600">{{ $hasDateFilter ? $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y') : 'Last 6 Months' }}</span>
            </div>
            <div class="p-4">
                <canvas id="monthlyRevenueChart" height="200"></canvas>
            </div>
        </div>

        <!-- Performance Metrics (Combined Circle Progress) -->
        <div class="card">
            <div class="px-5 py-3.5 border-b border-neutral-200">
                <h2 class="font-semibold text-neutral-900 text-sm">Performance</h2>
            </div>
            <div class="p-4 space-y-5">
                <!-- Completion Rate -->
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 shrink-0">
                        <svg class="w-16 h-16 -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#10b981" stroke-width="10"
                                    stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $completionRate / 100) }}"
                                    stroke-linecap="round" class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-bold text-neutral-900">{{ $completionRate }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-neutral-900">Completion Rate</p>
                        <p class="text-xs text-neutral-600">{{ number_format($completedOrders) }} of {{ number_format($totalOrders) }} delivered</p>
                    </div>
                </div>

                <!-- Cancellation Rate -->
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 shrink-0">
                        <svg class="w-16 h-16 -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#ef4444" stroke-width="10"
                                    stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $cancellationRate / 100) }}"
                                    stroke-linecap="round" class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-bold text-neutral-900">{{ $cancellationRate }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-neutral-900">Cancellation Rate</p>
                        <p class="text-xs text-neutral-600">{{ number_format($cancelledOrders) }} of {{ number_format($totalOrders) }} cancelled</p>
                    </div>
                </div>

                <!-- Active Products -->
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 shrink-0">
                        <svg class="w-16 h-16 -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#6366f1" stroke-width="10"
                                    stroke-dasharray="{{ 2 * 3.14159 * 52 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $productActiveRate / 100) }}"
                                    stroke-linecap="round" class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-bold text-neutral-900">{{ $productActiveRate }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-neutral-900">Active Products</p>
                        <p class="text-xs text-neutral-600">{{ number_format($activeProducts) }} of {{ number_format($totalProducts) }} active</p>
                    </div>
                </div>

                <!-- Quick Overview Numbers -->
                <div class="pt-4 border-t border-neutral-100 space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600">Total Revenue</span>
                        <span class="font-semibold text-neutral-900">@price($totalRevenue)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600">Total Orders</span>
                        <span class="font-semibold text-neutral-900">{{ number_format($totalOrders) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600">Total Sellers</span>
                        <span class="font-semibold text-neutral-900">{{ number_format($totalSellers) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders + Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 card">
            <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-semibold text-neutral-900 text-sm">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-neutral-600 uppercase">Order</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-neutral-600 uppercase">Customer</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-neutral-600 uppercase">Status</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-neutral-600 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-neutral-50/50">
                                <td class="px-4 py-2.5">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                        {{ $order->order_number }}
                                    </a>
                                    <p class="text-[11px] text-neutral-600">{{ $order->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-neutral-700">
                                    {{ $order->user->full_name ?? 'Guest' }}
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="badge {{ $order->status === 'delivered' || $order->status === 'completed' ? 'badge-success' : ($order->status === 'pending' ? 'badge-warning' : ($order->status === 'cancelled' ? 'badge-error' : 'badge-info')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-right font-medium">
                                    @price($order->total)
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-neutral-600 text-sm">
                                    No orders yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="card">
            <div class="px-5 py-3.5 border-b border-neutral-200">
                <h2 class="font-semibold text-neutral-900 text-sm">Top Selling Products</h2>
            </div>
            <div class="divide-y divide-neutral-100">
                @forelse($topProducts as $index => $product)
                    <div class="px-4 py-3 flex items-center gap-3">
                        <span class="text-xs font-bold text-neutral-300 w-4 text-center shrink-0">{{ $index + 1 }}</span>
                        <div class="w-9 h-9 bg-neutral-100 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                            @if($product->primary_image_url)
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-neutral-900 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-neutral-600">{{ $product->total_sold ?? 0 }} sold</p>
                        </div>
                        <span class="text-sm font-medium text-neutral-700">@price($product->price)</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-neutral-600 text-sm">
                        No products yet
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fontFamily = "'Poppins', 'Inter', sans-serif";

            // Revenue Overview - Line Chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($chartRevenue),
                        borderColor: '#9c00ad',
                        backgroundColor: 'rgba(156, 0, 173, 0.06)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#9c00ad',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Orders',
                        data: @json($chartOrders),
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.04)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#06b6d4',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 11, family: fontFamily } }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, family: fontFamily }, color: '#9e9e9e' } },
                        y: {
                            position: 'left',
                            grid: { color: '#f5f5f5' },
                            ticks: { font: { size: 11, family: fontFamily }, color: '#9e9e9e', callback: v => '₹' + v.toLocaleString() }
                        },
                        y1: {
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { font: { size: 11, family: fontFamily }, color: '#9e9e9e', stepSize: 1 }
                        }
                    }
                }
            });

            // Order Status Donut Chart
            const statusData = @json($orderStatusCounts);
            const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' '));
            const statusValues = Object.values(statusData);
            const statusColors = {
                pending: '#f59e0b', confirmed: '#3b82f6', processing: '#8b5cf6',
                packed: '#6366f1', shipped: '#06b6d4', out_for_delivery: '#14b8a6',
                delivered: '#10b981', completed: '#059669', cancelled: '#ef4444', returned: '#f97316'
            };
            const bgColors = Object.keys(statusData).map(s => statusColors[s] || '#bdbdbd');

            new Chart(document.getElementById('orderStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: bgColors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, pointStyle: 'circle', padding: 10, font: { size: 11, family: fontFamily } }
                        }
                    }
                }
            });

            // Monthly Revenue Bar Chart
            new Chart(document.getElementById('monthlyRevenueChart'), {
                type: 'bar',
                data: {
                    labels: @json($monthLabels),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($monthData),
                        backgroundColor: 'rgba(156, 0, 173, 0.15)',
                        hoverBackgroundColor: 'rgba(156, 0, 173, 0.3)',
                        borderColor: '#9c00ad',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, family: fontFamily }, color: '#9e9e9e' } },
                        y: {
                            grid: { color: '#f5f5f5' },
                            ticks: { font: { size: 11, family: fontFamily }, color: '#9e9e9e', callback: v => '₹' + v.toLocaleString() }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.admin>
