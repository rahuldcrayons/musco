<x-layouts.app>
    <x-slot name="title">Order Confirmed - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="robots" content="noindex, nofollow">
        @php
            $orderSchema = [
                '@context' => 'https://schema.org',
                '@type'    => 'Order',
                'orderNumber' => $order->order_number,
                'orderDate'   => $order->created_at->toIso8601String(),
                'orderStatus' => 'https://schema.org/OrderProcessing',
                'priceCurrency' => 'GBP',
                'price' => number_format($order->total, 2, '.', ''),
                'acceptedOffer' => $order->items->map(fn($item) => [
                    '@type' => 'Offer',
                    'itemOffered' => ['@type' => 'Product', 'name' => $item->product_name],
                    'price' => number_format($item->price, 2, '.', ''),
                    'priceCurrency' => 'GBP',
                    'eligibleQuantity' => ['@type' => 'QuantitativeValue', 'value' => $item->quantity],
                ])->toArray(),
                'seller' => ['@type' => 'Organization', 'name' => config('app.name')],
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($orderSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <style>
        @keyframes checkPop {
            0%   { transform: scale(0); opacity: 0; }
            60%  { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-check  { animation: checkPop .5s cubic-bezier(.34,1.56,.64,1) .1s both; opacity:1; }
        .anim-fade-1 { animation: fadeUp .4s ease .2s both; }
        .anim-fade-2 { animation: fadeUp .4s ease .35s both; }
        .anim-fade-3 { animation: fadeUp .4s ease .5s both; }
    </style>

    <div class="min-h-screen bg-[#F7F4F2]">
        <div class="container mx-auto px-4 py-8 lg:py-10">

            {{-- 2-column layout --}}
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                {{-- ── LEFT COL — Success header + items ── --}}
                <div class="lg:col-span-7 space-y-4">

                    {{-- Success hero --}}
                    <div class="anim-fade-1 bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
                        <div class="h-1.5 w-full" style="background: linear-gradient(90deg, #202a40 0%, #506282 100%);"></div>
                        <div class="px-6 pt-7 pb-6">
                            <div class="flex items-center gap-4">
                                <div class="anim-check shrink-0 w-14 h-14 rounded-full flex items-center justify-center" style="background-color:#22c55e;">
                                    <svg class="w-7 h-7" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold text-neutral-900">Order Confirmed!</h1>
                                    <p class="text-[13px] text-neutral-500 mt-0.5">Thank you for shopping with {{ config('app.name') }}.</p>
                                    @if(auth()->check())
                                    <p class="text-[11px] text-neutral-400 mt-1">
                                        Confirmation sent to <span class="font-medium text-neutral-500">{{ auth()->user()->email }}</span>
                                    </p>
                                    @elseif($order->guest_email)
                                    <p class="text-[11px] text-neutral-400 mt-1">
                                        Confirmation will be sent to <span class="font-medium text-neutral-500">{{ $order->guest_email }}</span>
                                    </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Order number + date --}}
                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div class="bg-[#F7F4F2] rounded-xl px-4 py-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Order Number</p>
                                    <p class="text-[15px] font-bold text-neutral-800 mt-1">{{ $order->order_number }}</p>
                                </div>
                                <div class="bg-[#F7F4F2] rounded-xl px-4 py-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Placed On</p>
                                    <p class="text-[13px] font-semibold text-neutral-700 mt-1">{{ $order->created_at->format('d M Y') }}</p>
                                    <p class="text-[11px] text-neutral-400">{{ $order->created_at->format('h:i A') }}</p>
                                </div>
                            </div>

                            @if($order->discount > 0)
                            <div class="mt-3 inline-flex items-center gap-1.5 text-[12px] font-semibold px-3 py-1.5 rounded-full" style="background:rgba(183,110,121,.08);color:#202a40;">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                You saved @price($order->discount) on this order!
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Items ordered --}}
                    <div class="anim-fade-2 bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-neutral-100">
                            <h2 class="text-[13px] font-bold text-neutral-800">Items Ordered</h2>
                            <span class="text-[11px] text-neutral-400 bg-neutral-100 px-2 py-0.5 rounded-full">
                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                            </span>
                        </div>

                        <div class="divide-y divide-neutral-50">
                            @foreach($order->items as $item)
                            <div class="flex gap-3 p-4">
                                @if($item->product && $item->product->primary_image_url)
                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name }}"
                                         class="w-14 h-14 object-cover rounded-lg border border-neutral-100 shrink-0 bg-neutral-50">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-neutral-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-neutral-900 leading-snug line-clamp-2">{{ $item->product_name }}</p>
                                    @if($item->variant_name)
                                        <p class="text-[11px] text-neutral-400 mt-0.5">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-[11px] text-neutral-400 mt-0.5">Qty: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-[13px] font-bold text-neutral-900">@price($item->total)</p>
                                    @if($item->quantity > 1)
                                        <p class="text-[11px] text-neutral-400 mt-0.5">@price($item->price) each</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- ── RIGHT COL — Summary + shipping + payment + actions ── --}}
                <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-6">

                    {{-- Price summary --}}
                    <div class="anim-fade-2 bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-neutral-100">
                            <h2 class="text-[13px] font-bold text-neutral-800">Order Summary</h2>
                        </div>
                        <div class="px-5 py-4 space-y-2.5">
                            <div class="flex justify-between text-[12px] text-neutral-500">
                                <span>Subtotal</span>
                                <span>@price($order->subtotal)</span>
                            </div>
                            @if($order->discount > 0)
                            <div class="flex justify-between text-[12px]">
                                <span class="text-[#202a40]">Discount</span>
                                <span class="text-[#202a40] font-medium">−@price($order->discount)</span>
                            </div>
                            @endif
                            @if($order->tax > 0)
                            <div class="flex justify-between text-[12px] text-neutral-500">
                                <span>Tax (GST incl.)</span>
                                <span>@price($order->tax)</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-[12px] text-neutral-500">
                                <span>Shipping</span>
                                <span class="{{ $order->shipping_cost > 0 ? '' : 'text-[#202a40] font-medium' }}">
                                    {{ $order->shipping_cost > 0 ? '£'.number_format($order->shipping_cost, 2) : 'Free' }}
                                </span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-dashed border-neutral-200">
                                <span class="text-[14px] font-bold text-neutral-900">Total Paid</span>
                                <span class="text-[14px] font-bold text-neutral-900">@price($order->total)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping & Payment --}}
                    <div class="anim-fade-3 bg-white rounded-2xl border border-neutral-100 shadow-sm divide-y divide-neutral-100 overflow-hidden">

                        {{-- Shipping address --}}
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2.5">
                                <div class="w-6 h-6 rounded-full bg-[#202a40]/10 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <h3 class="text-[12px] font-bold text-neutral-700">Deliver To</h3>
                            </div>
                            @php $shipping = $order->shipping_address_snapshot; @endphp
                            @if($shipping)
                            <div class="text-[12px] text-neutral-500 leading-relaxed space-y-0.5">
                                <p class="font-semibold text-neutral-800">{{ $shipping['name'] ?? '' }}</p>
                                @if(!empty($shipping['phone']))<p>{{ $shipping['phone'] }}</p>@endif
                                <p>{{ $shipping['address_line_1'] ?? '' }}@if(!empty($shipping['address_line_2'])), {{ $shipping['address_line_2'] }}@endif</p>
                                <p>{{ $shipping['city'] ?? '' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['postal_code'] ?? '' }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Payment --}}
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2.5">
                                <div class="w-6 h-6 rounded-full bg-[#202a40]/10 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h3 class="text-[12px] font-bold text-neutral-700">Payment</h3>
                            </div>
                            @php $paymentMethod = $order->metadata['payment_method'] ?? 'cod'; @endphp
                            <div class="flex items-center justify-between">
                                <p class="text-[12px] font-semibold text-neutral-800">
                                    @switch($paymentMethod)
                                        @case('cod') Pending Payment @break
                                        @case('card') Credit / Debit Card @break
                                        @case('paypal') PayPal @break
                                        @case('paypal') PayPal @break
                                        @default {{ ucfirst($paymentMethod) }}
                                    @endswitch
                                </p>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full
                                    {{ $order->payment_status === 'paid' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="anim-fade-3 space-y-2.5">
                        @if(auth()->check())
                        <a href="{{ route('account.orders.show', $order) }}"
                           class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-[13px] font-bold text-white"
                           style="background:#202a40;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Track Order
                        </a>
                        @endif
                        <a href="{{ route('products.index') }}"
                           class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-[13px] font-semibold border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            Continue Shopping
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- GA4 + FB Purchase tracking --}}
    @if(config('services.ga4.measurement_id') || config('services.facebook.pixel_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php
            $orderItemsArr = $order->items->map(function($item) {
                return [
                    'item_id'   => $item->sku ?? (string) $item->product_id,
                    'item_name' => $item->product_name,
                    'price'     => (float) $item->price,
                    'quantity'  => $item->quantity,
                ];
            })->values()->toArray();
            ?>
            var orderItems = {!! json_encode($orderItemsArr) !!};

            @if(config('services.ga4.measurement_id'))
            gtag('event', 'purchase', {
                transaction_id: '{{ $order->order_number }}',
                value: {{ (float) $order->total }},
                tax: {{ (float) $order->tax }},
                shipping: {{ (float) $order->shipping_cost }},
                currency: 'GBP',
                items: orderItems
            });
            @endif

            @if(config('services.facebook.pixel_id'))
            fbq('track', 'Purchase', {
                content_ids: {!! json_encode($order->items->pluck('product_id')->map('strval')->values()->toArray()) !!},
                content_type: 'product',
                value: {{ (float) $order->total }},
                currency: 'GBP',
                num_items: {{ $order->items->sum('quantity') }},
                order_id: '{{ $order->order_number }}'
            }@if(!empty($fbPurchaseEventId)), {eventID: '{{ $fbPurchaseEventId }}'}@endif);
            @endif
        });
    </script>
    @endif
</x-layouts.app>
