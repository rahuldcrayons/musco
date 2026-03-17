<x-layouts.admin>
    <x-slot name="title">Sales Report</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Sales Report</h1>
            <p class="text-sm text-neutral-600">Overview of your sales performance</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="flex items-center gap-2">
                <select name="period" onchange="this.form.submit()"
                        class="form-input text-sm py-1.5">
                    <option value="7" @selected($period == 7)>Last 7 days</option>
                    <option value="30" @selected($period == 30)>Last 30 days</option>
                    <option value="90" @selected($period == 90)>Last 90 days</option>
                    <option value="365" @selected($period == 365)>Last year</option>
                </select>
            </form>
            <a href="{{ route('admin.reports.export', ['type' => 'sales', 'period' => $period]) }}" class="btn btn-secondary text-sm h-9">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Total Revenue</p>
                    <p class="text-xl font-bold text-neutral-900">@price($stats['total_revenue'])</p>
                    @if($stats['revenue_change'] != 0)
                        <p class="text-xs {{ $stats['revenue_change'] > 0 ? 'text-success-600' : 'text-error-600' }}">
                            {{ $stats['revenue_change'] > 0 ? '+' : '' }}{{ number_format($stats['revenue_change'], 1) }}% vs prev
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Total Orders</p>
                    <p class="text-xl font-bold text-neutral-900">{{ number_format($stats['total_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-warning-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Average Order</p>
                    <p class="text-xl font-bold text-neutral-900">@price($stats['average_order'])</p>
                </div>
            </div>
        </div>

        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-neutral-600">Items Sold</p>
                    <p class="text-xl font-bold text-neutral-900">{{ number_format($stats['items_sold']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 card">
            <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-semibold text-neutral-900 text-sm">Daily Sales</h2>
                <span class="text-xs text-neutral-600">Last {{ $period }} days</span>
            </div>
            <div class="p-4">
                @if($salesData->count() > 0)
                    <canvas id="salesChart" height="260"></canvas>
                @else
                    <div class="flex flex-col items-center justify-center py-16 text-neutral-600">
                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-sm">No sales data for this period</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="card">
            <div class="px-5 py-3.5 border-b border-neutral-200">
                <h2 class="font-semibold text-neutral-900 text-sm">Sales by Category</h2>
            </div>
            <div class="p-4">
                @if($salesByCategory->count() > 0)
                    <canvas id="categoryChart" height="260"></canvas>
                @else
                    <div class="flex items-center justify-center py-12 text-neutral-600 text-sm">
                        No category data available
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="card">
        <div class="px-5 py-3.5 border-b border-neutral-200">
            <h2 class="font-semibold text-neutral-900 text-sm">Top Selling Products</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-neutral-600 uppercase">Rank</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-neutral-600 uppercase">Product</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-neutral-600 uppercase">Price</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium text-neutral-600 uppercase">Units Sold</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($topProducts as $index => $product)
                        <tr class="hover:bg-neutral-50/50">
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                    {{ $index < 3 ? 'bg-primary-100 text-primary-600' : 'bg-neutral-100 text-neutral-600' }}
                                    text-xs font-bold">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-neutral-100 rounded-lg shrink-0 overflow-hidden">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-neutral-900 text-sm truncate">{{ $product->name }}</p>
                                        <p class="text-xs text-neutral-600">{{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-sm text-neutral-600">@price($product->price)</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="text-sm font-semibold text-neutral-900">{{ number_format($product->sold ?? 0) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-neutral-600 text-sm">No sales data for this period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($salesData->count() > 0 || $salesByCategory->count() > 0)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fontFamily = "'Poppins', 'Inter', sans-serif";

                @if($salesData->count() > 0)
                // Sales Chart - Bar + Line combo
                new Chart(document.getElementById('salesChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($salesData->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
                        datasets: [{
                            label: 'Revenue',
                            data: @json($salesData->pluck('revenue')->map(fn($r) => round($r, 2))),
                            backgroundColor: 'rgba(156, 0, 173, 0.15)',
                            hoverBackgroundColor: 'rgba(156, 0, 173, 0.3)',
                            borderColor: '#9c00ad',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                            yAxisID: 'y'
                        }, {
                            label: 'Orders',
                            data: @json($salesData->pluck('orders')),
                            type: 'line',
                            borderColor: '#06b6d4',
                            backgroundColor: 'rgba(6, 182, 212, 0.05)',
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
                            x: { grid: { display: false }, ticks: { font: { size: 10, family: fontFamily }, color: '#9e9e9e', maxRotation: 45 } },
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
                @endif

                @if($salesByCategory->count() > 0)
                // Category Chart - Doughnut
                const categoryColors = ['#9c00ad', '#d946ef', '#f472b6', '#fb923c', '#facc15', '#4ade80', '#22d3ee', '#818cf8', '#a78bfa', '#94a3b8'];
                new Chart(document.getElementById('categoryChart'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($salesByCategory->pluck('name')),
                        datasets: [{
                            data: @json($salesByCategory->pluck('revenue')->map(fn($r) => round($r, 2))),
                            backgroundColor: categoryColors.slice(0, {{ $salesByCategory->count() }}),
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, pointStyle: 'circle', padding: 10, font: { size: 11, family: fontFamily } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.label + ': ₹' + ctx.parsed.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
                @endif
            });
        </script>
        @endpush
    @endif
</x-layouts.admin>
