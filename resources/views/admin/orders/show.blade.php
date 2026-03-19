<x-layouts.admin>
    <x-slot name="title">Order {{ $order->order_number }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm text-neutral-600 mb-1">
                    <a href="{{ route('admin.orders.index') }}" class="hover:text-primary-600 transition-colors">Orders</a>
                    <svg class="w-3.5 h-3.5 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-neutral-900">{{ $order->order_number }}</span>
                </div>
                <h1 class="text-2xl font-bold text-neutral-900">Order {{ $order->order_number }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-secondary" target="_blank">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Invoice
                </a>
                <a href="{{ route('admin.orders.packing-slip', $order) }}" class="btn btn-secondary" target="_blank">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Packing Slip
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Order Tracking Timeline --}}
    @if(!in_array($order->status, ['pending', 'cancelled', 'returned']))
        <div class="card mb-6">
            <div class="px-5 py-4 border-b border-neutral-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="font-semibold text-neutral-900">Order Tracking</h2>
                @if($latestShipment && $latestShipment->tracking_number)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-neutral-600">Tracking ID:</span>
                        <span class="font-mono font-semibold text-primary-600">{{ $latestShipment->tracking_number }}</span>
                        @if($latestShipment->carrier)
                            <span class="badge badge-info">{{ $latestShipment->carrier }}</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="p-6">
                <div class="relative">
                    <div class="flex items-start justify-between">
                        @foreach($trackingSteps as $index => $step)
                            <div class="flex-1 {{ $index < count($trackingSteps) - 1 ? 'relative' : '' }}">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 relative transition-all
                                        {{ $step['completed'] ? 'bg-success-500 text-white' : ($step['current'] ? 'bg-primary-500 text-white ring-4 ring-primary-100' : 'bg-neutral-200 text-neutral-600') }}">
                                        @if($step['completed'] && !$step['current'])
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($step['icon'] === 'clipboard-check')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        @elseif($step['icon'] === 'cube')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        @elseif($step['icon'] === 'truck')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                            </svg>
                                        @elseif($step['icon'] === 'map-pin')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        @elseif($step['icon'] === 'check-circle')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-xs font-semibold text-center {{ $step['completed'] || $step['current'] ? 'text-neutral-900' : 'text-neutral-600' }}">
                                        {{ $step['label'] }}
                                    </p>
                                    @if($step['timestamp'])
                                        <p class="text-xs text-neutral-600 text-center mt-0.5">
                                            {{ $step['timestamp']->format('M d, h:i A') }}
                                        </p>
                                    @endif
                                </div>
                                @if($index < count($trackingSteps) - 1)
                                    <div class="absolute top-5 left-1/2 w-full h-0.5 {{ $trackingSteps[$index + 1]['completed'] || $trackingSteps[$index + 1]['current'] ? 'bg-success-500' : 'bg-neutral-200' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-200">
                    <h2 class="font-semibold text-neutral-900">Order Items</h2>
                </div>
                <div class="divide-y divide-neutral-100">
                    @foreach($order->items as $item)
                        <div class="px-5 py-4 flex gap-4">
                            <div class="w-16 h-16 rounded-lg bg-neutral-50 ring-1 ring-neutral-200 overflow-hidden shrink-0">
                                @if($item->product->primary_image_url ?? null)
                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-neutral-900">{{ $item->product_name }}</h3>
                                @if($item->variant_name)
                                    <p class="text-sm text-neutral-600 mt-0.5">{{ $item->variant_name }}</p>
                                @endif
                                <p class="text-xs text-neutral-600 font-mono mt-1">SKU: {{ $item->sku }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm text-neutral-600">{{ $order->currency }} {{ number_format($item->price, 2) }} &times; {{ $item->quantity }}</p>
                                <p class="text-base font-bold text-neutral-900 mt-1">{{ $order->currency }} {{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-5 py-4 bg-neutral-50/80 border-t border-neutral-200 space-y-2">
                    <div class="flex justify-between text-sm text-neutral-600">
                        <span>Subtotal</span>
                        <span>{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-sm text-success-600">
                            <span>Discount</span>
                            <span>-{{ $order->currency }} {{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm text-neutral-600">
                        <span>Shipping</span>
                        <span>{{ $order->currency }} {{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-neutral-600">
                        <span>Tax</span>
                        <span>{{ $order->currency }} {{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-neutral-900 pt-2 border-t border-neutral-200">
                        <span>Total</span>
                        <span>{{ $order->currency }} {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-200">
                    <h2 class="font-semibold text-neutral-900">Shipping Address</h2>
                </div>
                <div class="p-5">
                    @php $shipping = $order->shipping_address_snapshot; @endphp
                    @if($shipping)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-neutral-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4.5 h-4.5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="text-sm text-neutral-600 space-y-0.5">
                                <p class="font-semibold text-neutral-900">{{ $shipping['name'] ?? ($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '') }}</p>
                                @if(!empty($shipping['phone'])) <p>{{ $shipping['phone'] }}</p> @endif
                                @if(!empty($shipping['address'])) <p>{{ $shipping['address'] }}</p> @endif
                                @if(!empty($shipping['address_line_1'])) <p>{{ $shipping['address_line_1'] }}</p> @endif
                                <p>{{ $shipping['city'] ?? '' }}{{ !empty($shipping['state']) ? ', ' . $shipping['state'] : '' }} {{ $shipping['postal_code'] ?? $shipping['zip'] ?? '' }}</p>
                            </div>
                        </div>
                    @elseif($order->user)
                        <p class="text-sm text-neutral-600">{{ $order->user->full_name }}</p>
                        <p class="text-sm text-neutral-600">{{ $order->user->email }}</p>
                    @endif
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-neutral-200">
                        <h2 class="font-semibold text-neutral-900">Order Notes</h2>
                    </div>
                    <div class="p-5">
                        <p class="text-sm text-neutral-600">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Update Order Status -->
            <div class="card overflow-hidden" x-data="{ status: '{{ $order->status }}' }">
                <div class="px-5 py-4 border-b border-neutral-200 flex items-center justify-between">
                    <h2 class="font-semibold text-neutral-900">Update Status</h2>
                    @php
                        $currentClass = match($order->status) {
                            'delivered', 'completed' => 'badge-success',
                            'confirmed' => 'badge-warning',
                            'processing', 'packed' => 'badge-info',
                            'shipped', 'out_for_delivery' => 'badge-primary',
                            'cancelled', 'returned' => 'badge-error',
                            default => 'badge-neutral',
                        };
                    @endphp
                    <span class="badge {{ $currentClass }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="form-label">New Status</label>
                            <select name="status" x-model="status" class="form-select w-full">
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="packed">Packed</option>
                                <option value="shipped">Shipped</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="returned">Returned</option>
                            </select>
                        </div>

                        {{-- Carrier & Tracking --}}
                        <div x-show="status === 'shipped'" x-transition x-cloak class="space-y-3">
                            <div>
                                <label class="form-label">Carrier</label>
                                <select name="carrier" class="form-select w-full">
                                    <option value="">Select carrier</option>
                                    <option value="BlueDart">BlueDart</option>
                                    <option value="Delhivery">Delhivery</option>
                                    <option value="DTDC">DTDC</option>
                                    <option value="Ecom Express">Ecom Express</option>
                                    <option value="India Post">India Post</option>
                                    <option value="FedEx">FedEx</option>
                                    <option value="DHL">DHL</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-input w-full" placeholder="Enter tracking number">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Note (optional)</label>
                            <textarea name="comment" rows="2" class="form-textarea w-full" placeholder="Add a note..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Delhivery Shipping -->
            @if(in_array($order->status, ['confirmed', 'packed', 'shipped', 'out_for_delivery']))
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-neutral-200">
                        <h2 class="font-semibold text-neutral-900">Delhivery Shipping</h2>
                    </div>
                    <div class="p-5">
                        @if($order->tracking_number && $order->carrier === 'Delhivery')
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-neutral-900">Shipment Booked</p>
                                    <p class="text-xs text-neutral-600">AWB: <span class="font-mono font-medium text-primary-600">{{ $order->tracking_number }}</span></p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.delivery.label', $order) }}" class="btn btn-outline text-xs flex-1">Download Label</a>
                                <button onclick="fetch('{{ route('admin.delivery.track', $order) }}').then(r=>r.json()).then(d=>alert(d.success ? 'Status: ' + d.status + '\nLocation: ' + d.status_location : d.message))"
                                        class="btn btn-outline text-xs flex-1">Track</button>
                            </div>
                            <form action="{{ route('admin.delivery.cancel', $order) }}" method="POST" class="mt-2" onsubmit="return confirm('Cancel Delhivery shipment?')">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 hover:underline">Cancel Shipment</button>
                            </form>
                        @elseif(!$order->tracking_number)
                            <form action="{{ route('admin.delivery.book', $order) }}" method="POST" onsubmit="return confirm('Book Delhivery shipment for this order?')">
                                @csrf
                                <button type="submit" class="btn btn-primary w-full">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25"/></svg>
                                    Book via Delhivery
                                </button>
                            </form>
                            <p class="text-[10px] text-neutral-500 mt-2 text-center">One-click shipment booking with auto-tracking</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Assign Delivery Partner -->
            @if(in_array($order->status, ['packed', 'shipped', 'out_for_delivery']))
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-neutral-200 flex items-center justify-between">
                        <h2 class="font-semibold text-neutral-900">Delivery Partner</h2>
                        @if($order->deliveryPartner)
                            <span class="badge badge-success">Assigned</span>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($order->deliveryPartner)
                            <div class="flex items-center gap-3 mb-4 p-3 bg-neutral-50 rounded-lg">
                                <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-sm font-bold text-primary-600">{{ strtoupper(substr($order->deliveryPartner->user->first_name, 0, 1) . substr($order->deliveryPartner->user->last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-neutral-900">{{ $order->deliveryPartner->user->full_name }}</p>
                                    <p class="text-xs text-neutral-600">{{ $order->deliveryPartner->partner_id }} &middot; {{ $order->deliveryPartner->phone }}</p>
                                </div>
                            </div>
                        @endif
                        <form action="{{ route('admin.orders.assign-partner', $order) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="form-label">{{ $order->deliveryPartner ? 'Change Partner' : 'Select Partner' }}</label>
                                <select name="delivery_partner_id" class="form-select w-full">
                                    <option value="">-- None --</option>
                                    @foreach($activePartners as $partner)
                                        <option value="{{ $partner->id }}" @selected($order->delivery_partner_id == $partner->id)>
                                            {{ $partner->user->full_name }} ({{ $partner->partner_id }}) - {{ ucfirst($partner->vehicle_type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-full">
                                {{ $order->delivery_partner_id ? 'Update Partner' : 'Assign Partner' }}
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($order->deliveryPartner)
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-neutral-200">
                        <h2 class="font-semibold text-neutral-900">Delivery Partner</h2>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-sm font-bold text-primary-600">{{ strtoupper(substr($order->deliveryPartner->user->first_name, 0, 1) . substr($order->deliveryPartner->user->last_name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">{{ $order->deliveryPartner->user->full_name }}</p>
                                <p class="text-xs text-neutral-600">{{ $order->deliveryPartner->partner_id }} &middot; {{ $order->deliveryPartner->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Customer Info -->
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-200">
                    <h2 class="font-semibold text-neutral-900">Customer</h2>
                </div>
                <div class="p-5">
                    @if($order->user)
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-linear-to-br from-primary-50 to-purple-50 rounded-full flex items-center justify-center ring-1 ring-neutral-200">
                                <span class="text-sm font-bold text-primary-500">{{ strtoupper(substr($order->user->first_name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-neutral-900">{{ $order->user->full_name }}</p>
                                <p class="text-sm text-neutral-600">{{ $order->user->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.customers.show', $order->user) }}" class="btn btn-secondary w-full text-center">
                            View Customer
                        </a>
                    @else
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-neutral-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="text-sm text-neutral-600">Guest checkout</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Info -->
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-200">
                    <h2 class="font-semibold text-neutral-900">Order Info</h2>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-600">Order Date</span>
                        <span class="font-medium text-neutral-700">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-600">Payment Status</span>
                        @php
                            $payClass = match($order->payment_status) {
                                'paid' => 'badge-success',
                                'pending' => 'badge-warning',
                                'failed' => 'badge-error',
                                'refunded' => 'badge-neutral',
                                default => 'badge-neutral',
                            };
                        @endphp
                        <span class="badge {{ $payClass }}">{{ ucfirst($order->payment_status) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-600">Payment Collected</span>
                        @if($order->payment_collected)
                            <span class="badge badge-success">Yes</span>
                        @else
                            <span class="badge badge-warning">No</span>
                        @endif
                    </div>
                    @if($order->payment_collected_at)
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Collected At</span>
                            <span class="font-medium text-neutral-700">{{ $order->payment_collected_at->format('M d, Y h:i A') }}</span>
                        </div>
                    @endif
                    @if($latestShipment)
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Carrier</span>
                            <span class="font-medium text-neutral-700">{{ $latestShipment->carrier }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Tracking #</span>
                            <span class="font-mono font-medium text-primary-600">{{ $latestShipment->tracking_number }}</span>
                        </div>
                    @endif
                    @if($order->shipped_at)
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Shipped</span>
                            <span class="font-medium text-neutral-700">{{ $order->shipped_at->format('M d, Y') }}</span>
                        </div>
                    @endif
                    @if($order->delivered_at)
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-600">Delivered</span>
                            <span class="font-medium text-neutral-700">{{ $order->delivered_at->format('M d, Y') }}</span>
                        </div>
                    @endif
                </div>
                {{-- Expected Delivery Date --}}
                <div class="px-5 py-4 border-t border-neutral-200" x-data="{ editing: false }">
                    <div x-show="!editing" class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-600">Expected Delivery</p>
                            @if($order->expected_delivery_date)
                                <p class="text-sm font-semibold text-success-700 mt-0.5">
                                    {{ $order->expected_delivery_date->format('D, M d, Y') }}
                                    @if($order->expected_delivery_date->isToday())
                                        <span class="text-xs font-normal text-success-500">(Today)</span>
                                    @elseif($order->expected_delivery_date->isTomorrow())
                                        <span class="text-xs font-normal text-success-500">(Tomorrow)</span>
                                    @endif
                                </p>
                            @else
                                <p class="text-sm text-neutral-600 mt-0.5">Not set</p>
                            @endif
                        </div>
                        @if(!in_array($order->status, ['delivered', 'cancelled', 'returned']))
                            <button @click="editing = true" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                {{ $order->expected_delivery_date ? 'Change' : 'Set Date' }}
                            </button>
                        @endif
                    </div>
                    @if(!in_array($order->status, ['delivered', 'cancelled', 'returned']))
                        <form x-show="editing" x-cloak action="{{ route('admin.orders.expected-delivery', $order) }}" method="POST" class="space-y-2 mt-2">
                            @csrf
                            @method('PUT')
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" name="expected_delivery_date"
                                   value="{{ $order->expected_delivery_date?->format('Y-m-d') }}"
                                   min="{{ today()->format('Y-m-d') }}"
                                   class="form-input w-full">
                            <div class="flex gap-2 pt-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-1 text-xs">Save</button>
                                <button type="button" @click="editing = false" class="btn btn-secondary btn-sm text-xs">Cancel</button>
                            </div>
                            @if($order->expected_delivery_date)
                                <button type="submit" name="expected_delivery_date" value="" class="w-full text-xs text-error-500 hover:text-error-600 text-center py-1">
                                    Clear date
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            <!-- Status History -->
            @if($order->statusHistory->count())
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-neutral-200">
                        <h2 class="font-semibold text-neutral-900">Activity Log</h2>
                    </div>
                    <div class="p-5">
                        <div class="relative">
                            <div class="absolute left-3 top-0 bottom-0 w-px bg-neutral-200"></div>
                            <div class="space-y-4">
                                @foreach($order->statusHistory->sortByDesc('created_at') as $history)
                                    <div class="flex gap-3 text-sm relative">
                                        @php
                                            $dotColor = match(true) {
                                                $history->status === 'delivered' => 'bg-success-100 text-success-600',
                                                in_array($history->status, ['cancelled', 'returned']) => 'bg-error-100 text-error-600',
                                                default => 'bg-primary-100 text-primary-600',
                                            };
                                            $innerDot = match(true) {
                                                $history->status === 'delivered' => 'bg-success-500',
                                                in_array($history->status, ['cancelled', 'returned']) => 'bg-error-500',
                                                default => 'bg-primary-500',
                                            };
                                        @endphp
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center z-10 {{ $dotColor }}">
                                            <div class="w-2 h-2 rounded-full {{ $innerDot }}"></div>
                                        </div>
                                        <div class="flex-1 pb-1">
                                            <p class="font-medium text-neutral-900">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                            @if($history->comment)
                                                <p class="text-neutral-600">{{ $history->comment }}</p>
                                            @endif
                                            <p class="text-xs text-neutral-600 mt-0.5">{{ $history->created_at->format('M d, Y \a\t h:i A') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
