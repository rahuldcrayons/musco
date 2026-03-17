<x-layouts.admin>
    <x-slot name="title">Analytics</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Analytics</h1>
                <p class="text-sm text-neutral-600 mt-0.5">Traffic and conversion insights</p>
            </div>
            <form action="{{ route('admin.reports.analytics') }}" method="GET">
                <select name="period" onchange="this.form.submit()" class="form-select text-sm py-1.5">
                    <option value="7"  @selected($period == 7)>Last 7 days</option>
                    <option value="30" @selected($period == 30)>Last 30 days</option>
                    <option value="90" @selected($period == 90)>Last 90 days</option>
                </select>
            </form>
        </div>
    </x-slot>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $summaryCards = [
                [
                    'label'  => 'Unique Visitors',
                    'value'  => number_format($funnel['visitors']),
                    'icon'   => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                    'color'  => 'bg-primary-50 text-primary-600',
                ],
                [
                    'label'  => 'Product Views',
                    'value'  => number_format($funnel['product_views']),
                    'icon'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'color'  => 'bg-info-50 text-info-600',
                ],
                [
                    'label'  => 'Add to Cart',
                    'value'  => number_format($funnel['add_to_cart']),
                    'icon'   => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
                    'color'  => 'bg-warning-50 text-warning-600',
                ],
                [
                    'label'  => 'Completed Orders',
                    'value'  => number_format($funnel['completed']),
                    'icon'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'color'  => 'bg-success-50 text-success-600',
                ],
            ];
        @endphp
        @foreach($summaryCards as $card)
            <div class="card p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl {{ $card['color'] }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-600 font-medium">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-neutral-900 mt-0.5 leading-none">{{ $card['value'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Conversion Rate Banner --}}
    @if($funnel['visitors'] > 0)
        @php
            $rate = round(($funnel['completed'] / $funnel['visitors']) * 100, 2);
            $cartRate = $funnel['visitors'] > 0 ? round(($funnel['add_to_cart'] / $funnel['visitors']) * 100, 1) : 0;
            $checkoutRate = $funnel['add_to_cart'] > 0 ? round(($funnel['checkout'] / $funnel['add_to_cart']) * 100, 1) : 0;
        @endphp
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="card p-4 border-l-4 border-l-primary-500">
                <p class="text-xs text-neutral-600">View → Cart Rate</p>
                <p class="text-xl font-bold text-neutral-900 mt-0.5">{{ $cartRate }}%</p>
                <p class="text-xs text-neutral-600 mt-1">{{ number_format($funnel['add_to_cart']) }} of {{ number_format($funnel['visitors']) }} visitors</p>
            </div>
            <div class="card p-4 border-l-4 border-l-warning-500">
                <p class="text-xs text-neutral-600">Cart → Order Rate</p>
                <p class="text-xl font-bold text-neutral-900 mt-0.5">{{ $checkoutRate }}%</p>
                <p class="text-xs text-neutral-600 mt-1">{{ number_format($funnel['checkout']) }} of {{ number_format($funnel['add_to_cart']) }} carts</p>
            </div>
            <div class="card p-4 border-l-4 border-l-success-500">
                <p class="text-xs text-neutral-600">Overall Conversion</p>
                <p class="text-xl font-bold text-success-600 mt-0.5">{{ $rate }}%</p>
                <p class="text-xs text-neutral-600 mt-1">{{ number_format($funnel['completed']) }} completed of {{ number_format($funnel['visitors']) }}</p>
            </div>
        </div>
    @endif

    {{-- Traffic Chart --}}
    <div class="card mb-6">
        <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-neutral-900 text-sm">Traffic Overview</h2>
                <p class="text-xs text-neutral-600 mt-0.5">Product views & unique visitors — last {{ $period }} days</p>
            </div>
            @if($trafficData->sum('pageviews') > 0)
                <div class="flex items-center gap-4 text-xs text-neutral-600">
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-sm" style="background:rgba(156,0,173,0.4)"></span>
                        Page Views
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-0.5" style="background:#06b6d4"></span>
                        Visitors
                    </span>
                </div>
            @endif
        </div>
        <div class="p-5">
            @if($trafficData->sum('pageviews') > 0)
                <div style="height:260px; position:relative;">
                    <canvas id="trafficChart"></canvas>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 text-neutral-300">
                    <svg class="w-14 h-14 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm font-medium text-neutral-600">No traffic data for this period</p>
                    <p class="text-xs text-neutral-300 mt-1">Product view tracking will appear here once customers start browsing</p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Conversion Funnel --}}
        <div class="card">
            <div class="px-5 py-3.5 border-b border-neutral-200">
                <h2 class="font-semibold text-neutral-900 text-sm">Conversion Funnel</h2>
                <p class="text-xs text-neutral-600 mt-0.5">Drop-off at each stage</p>
            </div>
            <div class="p-5 space-y-4">
                @php
                    $funnelSteps = [
                        ['label' => 'Unique Visitors',   'value' => $funnel['visitors'],      'color' => 'bg-primary-500'],
                        ['label' => 'Product Views',     'value' => $funnel['product_views'], 'color' => 'bg-primary-400'],
                        ['label' => 'Add to Cart',       'value' => $funnel['add_to_cart'],   'color' => 'bg-warning-500'],
                        ['label' => 'Orders Placed',     'value' => $funnel['checkout'],      'color' => 'bg-info-500'],
                        ['label' => 'Paid & Completed',  'value' => $funnel['completed'],     'color' => 'bg-success-500'],
                    ];
                    $maxFunnel = max($funnel['visitors'], $funnel['product_views'], 1);
                @endphp

                @foreach($funnelSteps as $index => $step)
                    @php
                        $width   = ($step['value'] / $maxFunnel) * 100;
                        $prev    = $index > 0 ? $funnelSteps[$index - 1]['value'] : $step['value'];
                        $dropoff = $prev > 0 ? round((1 - $step['value'] / $prev) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $step['color'] }} shrink-0"></span>
                                <span class="text-neutral-700 font-medium">{{ $step['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-neutral-900">{{ number_format($step['value']) }}</span>
                                @if($index > 0 && $dropoff > 0)
                                    <span class="text-xs font-medium text-error-500 bg-error-50 px-1.5 py-0.5 rounded-full">-{{ $dropoff }}%</span>
                                @elseif($index > 0 && $dropoff === 0)
                                    <span class="text-xs font-medium text-success-600 bg-success-50 px-1.5 py-0.5 rounded-full">0%</span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-neutral-100 rounded-full h-2.5 overflow-hidden">
                            <div class="{{ $step['color'] }} h-full rounded-full transition-all duration-700" style="width: {{ max($width, 1) }}%"></div>
                        </div>
                    </div>
                @endforeach

                @if($funnel['visitors'] == 0)
                    <p class="text-center text-sm text-neutral-600 py-4">No funnel data for this period</p>
                @endif
            </div>
        </div>

        {{-- Right column: Traffic Sources + Device Breakdown --}}
        <div class="space-y-6">
            {{-- Traffic Sources --}}
            <div class="card">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="font-semibold text-neutral-900 text-sm">Traffic Sources</h2>
                    <p class="text-xs text-neutral-600 mt-0.5">By referrer origin</p>
                </div>
                <div class="divide-y divide-neutral-50">
                    @php
                        $sourceIcons = [
                            'Organic Search' => ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'color' => 'bg-blue-50 text-blue-500'],
                            'Direct'         => ['icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'color' => 'bg-neutral-100 text-neutral-600'],
                            'Social Media'   => ['icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'color' => 'bg-pink-50 text-pink-500'],
                            'Email'          => ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'bg-orange-50 text-orange-500'],
                            'Referral'       => ['icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'color' => 'bg-purple-50 text-purple-500'],
                        ];
                        $hasSourceData = $sources->where('visitors', '>', 0)->count() > 0;
                    @endphp
                    @if($hasSourceData)
                        @foreach($sources->filter(fn($s) => $s['visitors'] > 0) as $source)
                            @php $si = $sourceIcons[$source['source']] ?? $sourceIcons['Referral']; @endphp
                            <div class="px-5 py-3.5 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $si['color'] }} flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $si['icon'] }}"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-sm font-medium text-neutral-700">{{ $source['source'] }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-neutral-900">{{ number_format($source['visitors']) }}</span>
                                            <span class="text-xs text-neutral-600 w-8 text-right">{{ $source['percentage'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="bg-neutral-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-primary-400 h-full rounded-full transition-all" style="width: {{ $source['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="px-5 py-8 text-center text-neutral-300">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945"/>
                            </svg>
                            <p class="text-sm text-neutral-600">No referrer data available</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Device Breakdown --}}
            <div class="card">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="font-semibold text-neutral-900 text-sm">Device Breakdown</h2>
                    <p class="text-xs text-neutral-600 mt-0.5">Orders by device type</p>
                </div>
                <div class="p-5">
                    @if($devices['mobile'] + $devices['desktop'] + $devices['tablet'] > 0)
                        {{-- Stacked bar --}}
                        <div class="flex h-3 rounded-full overflow-hidden mb-5 gap-0.5">
                            @if($devices['mobile'] > 0)
                                <div class="bg-primary-500 rounded-l-full" style="width: {{ $devices['mobile'] }}%" title="Mobile {{ $devices['mobile'] }}%"></div>
                            @endif
                            @if($devices['desktop'] > 0)
                                <div class="bg-info-400 {{ $devices['mobile'] == 0 ? 'rounded-l-full' : '' }} {{ $devices['tablet'] == 0 ? 'rounded-r-full' : '' }}" style="width: {{ $devices['desktop'] }}%" title="Desktop {{ $devices['desktop'] }}%"></div>
                            @endif
                            @if($devices['tablet'] > 0)
                                <div class="bg-warning-400 rounded-r-full" style="width: {{ $devices['tablet'] }}%" title="Tablet {{ $devices['tablet'] }}%"></div>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            @php
                                $deviceItems = [
                                    ['label' => 'Mobile',  'pct' => $devices['mobile'],  'color' => 'bg-primary-500', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                    ['label' => 'Desktop', 'pct' => $devices['desktop'], 'color' => 'bg-info-400',    'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                    ['label' => 'Tablet',  'pct' => $devices['tablet'],  'color' => 'bg-warning-400', 'icon' => 'M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                ];
                            @endphp
                            @foreach($deviceItems as $d)
                                <div class="bg-neutral-50 rounded-xl p-3 text-center">
                                    <div class="w-8 h-8 rounded-lg {{ str_replace('bg-', 'bg-opacity-20 bg-', $d['color']) }} mx-auto flex items-center justify-center mb-2">
                                        <svg class="w-4 h-4 {{ str_replace('bg-', 'text-', $d['color']) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $d['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-bold text-neutral-900">{{ $d['pct'] }}%</p>
                                    <div class="flex items-center justify-center gap-1 mt-0.5">
                                        <span class="w-2 h-2 rounded-full {{ $d['color'] }} shrink-0"></span>
                                        <p class="text-xs text-neutral-600">{{ $d['label'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-neutral-300">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-neutral-600">No device data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Info note --}}
    <div class="flex items-start gap-3 px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-xs text-neutral-600">
        <svg class="w-4 h-4 shrink-0 mt-0.5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Analytics data is collected from product views, cart activity, and order records. Traffic sources are derived from HTTP referrer headers. Device breakdown is based on user-agent strings from orders.
    </div>

    @if($trafficData->sum('pageviews') > 0)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const fontFamily = "'Manrope', 'Inter', sans-serif";
                const ctx = document.getElementById('trafficChart');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($trafficData->pluck('date')),
                        datasets: [
                            {
                                label: 'Page Views',
                                data: @json($trafficData->pluck('pageviews')),
                                backgroundColor: 'rgba(156, 0, 173, 0.18)',
                                hoverBackgroundColor: 'rgba(156, 0, 173, 0.35)',
                                borderColor: 'rgba(156, 0, 173, 0.6)',
                                borderWidth: 1,
                                borderRadius: 5,
                                borderSkipped: false,
                                yAxisID: 'y',
                                order: 2,
                            },
                            {
                                label: 'Unique Visitors',
                                data: @json($trafficData->pluck('visitors')),
                                type: 'line',
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.06)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#06b6d4',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: {{ $period <= 14 ? 4 : 0 }},
                                pointHoverRadius: 5,
                                yAxisID: 'y',
                                order: 1,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1a1a1a',
                                titleColor: '#fff',
                                bodyColor: '#ccc',
                                padding: 10,
                                cornerRadius: 8,
                                titleFont: { size: 11, family: fontFamily },
                                bodyFont: { size: 11, family: fontFamily },
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: {
                                    font: { size: 10, family: fontFamily },
                                    color: '#a0a0a0',
                                    maxRotation: 40,
                                    maxTicksLimit: {{ $period <= 14 ? $period : 15 }}
                                }
                            },
                            y: {
                                grid: { color: '#f3f3f3' },
                                border: { display: false, dash: [4, 4] },
                                ticks: {
                                    font: { size: 10, family: fontFamily },
                                    color: '#a0a0a0',
                                    maxTicksLimit: 6
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
        @endpush
    @endif
</x-layouts.admin>
