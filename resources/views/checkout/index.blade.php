<x-layouts.app>
    <x-slot name="title">Checkout - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Secure checkout at {{ config('app.name') }}. Fast payment with Razorpay, UPI, or Cash on Delivery.">
        <meta name="robots" content="noindex, nofollow">
        <meta property="og:title" content="Checkout - {{ config('app.name') }}">
        <meta property="og:description" content="Complete your order securely at {{ config('app.name') }}.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('checkout.index') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Checkout - {{ config('app.name') }}">

        {{-- JSON-LD CheckoutPage + BreadcrumbList --}}
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebPage",
            "name": "Checkout",
            "description": "Secure checkout at {{ config('app.name') }}",
            "url": "{{ route('checkout.index') }}",
            "breadcrumb": {
                "@@type": "BreadcrumbList",
                "itemListElement": [
                    { "@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ url('/') }}" },
                    { "@@type": "ListItem", "position": 2, "name": "Cart", "item": "{{ route('cart.index') }}" },
                    { "@@type": "ListItem", "position": 3, "name": "Checkout" }
                ]
            },
            "potentialAction": {
                "@@type": "OrderAction",
                "target": "{{ route('checkout.process') }}"
            }
        }
        </script>
    @endpush

    {{-- Facebook Pixel: InitiateCheckout --}}
    @if(!empty($fbEventId) && config('services.facebook.pixel_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof fbq !== 'undefined') {
                fbq('track', 'InitiateCheckout', {
                    content_ids: @json($cart->items->pluck('product_id')->map(fn ($id) => (string) $id)->toArray()),
                    content_type: 'product',
                    value: {{ (float) ($cart->subtotal - $cart->discount) }},
                    currency: 'INR',
                    num_items: {{ $cart->items->sum('quantity') }}
                }, {eventID: '{{ $fbEventId }}'});
            }
        });
    </script>
    @endif

    <div class="bg-neutral-50 min-h-screen">
        <div class="container mx-auto px-4 py-4">
            <x-breadcrumb :items="[['label' => 'Cart', 'url' => route('cart.index')], ['label' => 'Checkout', 'url' => null]]" />
        </div>

        <div class="container mx-auto px-4 pb-10">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-lg font-bold text-neutral-900">Checkout</h1>
                <a href="{{ route('cart.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    Back to Cart
                </a>
            </div>

            @php
                $methodOrder = ['razorpay' => 'razorpay_enabled', 'upi' => 'upi_enabled', 'cod' => 'cod_enabled'];
                $firstMethod = 'cod';
                foreach ($methodOrder as $method => $key) {
                    if (($paymentSettings[$key] ?? '1') === '1') { $firstMethod = $method; break; }
                }
            @endphp
            <form action="{{ route('checkout.process') }}" method="POST"
                  x-data="checkoutForm('{{ $firstMethod }}')"
                  @submit.prevent="handleSubmit($event)">
                @csrf

                <div class="flex flex-col lg:flex-row lg:items-start gap-5">
                    <!-- Left Column -->
                    <div class="flex-1 min-w-0 space-y-4">

                        {{-- Guest Contact Info --}}
                        @if($isGuest)
                        <div class="bg-white rounded-lg border border-neutral-100">
                            <div class="flex items-center gap-2.5 px-4 py-3 border-b border-neutral-100">
                                <div class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center">1</div>
                                <h2 class="text-sm font-semibold text-neutral-900">Contact Information</h2>
                            </div>
                            <div class="p-4 space-y-3">
                                <p class="text-xs text-neutral-500">Already have an account? <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">Log in</a> for a faster checkout.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-medium text-neutral-600 mb-1">Full Name *</label>
                                        <input type="text" name="guest_name" value="{{ old('guest_name') }}" required
                                               class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="Your full name">
                                        @error('guest_name') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium text-neutral-600 mb-1">Email Address *</label>
                                        <input type="email" name="guest_email" value="{{ old('guest_email') }}" required
                                               class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="email@example.com">
                                        @error('guest_email') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="sm:w-1/2">
                                    <label class="block text-[11px] font-medium text-neutral-600 mb-1">Phone *</label>
                                    <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" required
                                           class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="+91 98765 43210">
                                    @error('guest_phone') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Shipping Address -->
                        <div class="bg-white rounded-lg border border-neutral-100">
                            <div class="flex items-center gap-2.5 px-4 py-3 border-b border-neutral-100">
                                <div class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center">{{ $isGuest ? '2' : '1' }}</div>
                                <h2 class="text-sm font-semibold text-neutral-900">Shipping Address</h2>
                            </div>
                            <div class="p-4">
                                @if($isGuest)
                                    {{-- Guest inline address form (always visible, fields submitted with form) --}}
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">Full Name *</label>
                                                <input type="text" name="shipping_name" value="{{ old('shipping_name') }}" required
                                                       class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="Recipient's name">
                                                @error('shipping_name') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">Phone *</label>
                                                <input type="tel" name="shipping_phone" value="{{ old('shipping_phone') }}" required
                                                       class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="+91 98765 43210">
                                                @error('shipping_phone') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-neutral-600 mb-1">Address Line 1 *</label>
                                            <input type="text" name="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}" required
                                                   class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="House no., Building, Street">
                                            @error('shipping_address_line_1') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-neutral-600 mb-1">Address Line 2</label>
                                            <input type="text" name="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}"
                                                   class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="Area, Landmark (optional)">
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">City *</label>
                                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required
                                                       class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="City">
                                                @error('shipping_city') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">State *</label>
                                                <input type="text" name="shipping_state" value="{{ old('shipping_state') }}" required
                                                       class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="State">
                                                @error('shipping_state') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">PIN Code *</label>
                                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required
                                                       class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="400001" maxlength="6">
                                                @error('shipping_postal_code') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Authenticated user: saved addresses --}}
                                    @if($addresses->count())
                                        <div class="space-y-2.5">
                                            @foreach($addresses as $address)
                                                <label class="flex items-start gap-3 p-3 border border-neutral-200 rounded-lg cursor-pointer hover:border-primary-300 has-checked:border-primary-500 has-checked:bg-primary-50/50 transition-colors">
                                                    <input type="radio" name="shipping_address_id" value="{{ $address->id }}"
                                                           {{ $address->id === $defaultAddress?->id ? 'checked' : '' }}
                                                           class="mt-0.5 text-primary-500 focus:ring-primary-500">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[13px] font-semibold text-neutral-900">{{ $address->name }}</span>
                                                            @if($address->is_default)
                                                                <span class="text-[10px] font-medium text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded">Default</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-neutral-600 mt-0.5">{{ $address->phone }}</p>
                                                        <p class="text-xs text-neutral-600 leading-relaxed">
                                                            {{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }},
                                                            {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                                        </p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>

                                        <button type="button" @click="showAddressForm = !showAddressForm" class="inline-flex items-center gap-1.5 mt-3 text-xs font-medium text-primary-600 hover:text-primary-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span x-text="showAddressForm ? 'Cancel' : 'Add New Address'"></span>
                                        </button>
                                    @else
                                        <div class="text-center py-6" x-show="!showAddressForm">
                                            <svg class="w-10 h-10 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <p class="text-sm text-neutral-600 mb-3">No saved addresses found.</p>
                                            <button type="button" @click="showAddressForm = true" class="inline-flex items-center gap-1.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 px-4 py-2 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Add Address
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Inline Add Address Form (auth users only) -->
                                    <div x-show="showAddressForm" x-collapse x-cloak class="mt-3 p-4 bg-neutral-50 rounded-lg border border-neutral-200 space-y-3">
                                        <h3 class="text-sm font-semibold text-neutral-800">New Address</h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">Full Name *</label>
                                                <input type="text" id="new_addr_name" required class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="Full name">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">Phone *</label>
                                                <input type="tel" id="new_addr_phone" required class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="+91 98765 43210">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-neutral-600 mb-1">Address Line 1 *</label>
                                            <input type="text" id="new_addr_line1" required class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="House no., Building, Street">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-neutral-600 mb-1">Address Line 2</label>
                                            <input type="text" id="new_addr_line2" class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="Area, Landmark (optional)">
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">City *</label>
                                                <input type="text" id="new_addr_city" required class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="City">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">State *</label>
                                                <input type="text" id="new_addr_state" required class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="State">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">PIN Code *</label>
                                                <input type="text" id="new_addr_pincode" required class="w-full text-sm border border-neutral-200 rounded-lg px-3 py-2.5 focus:border-primary-400 focus:ring focus:ring-primary-100" placeholder="400001" maxlength="6">
                                            </div>
                                        </div>
                                        <div id="new_addr_error" class="hidden text-xs text-error-600"></div>
                                        <div class="flex gap-2 pt-1">
                                            <button type="button"
                                                    :disabled="savingAddress"
                                                    @click="
                                                        let name = document.getElementById('new_addr_name').value.trim();
                                                        let phone = document.getElementById('new_addr_phone').value.trim();
                                                        let line1 = document.getElementById('new_addr_line1').value.trim();
                                                        let line2 = document.getElementById('new_addr_line2').value.trim();
                                                        let city = document.getElementById('new_addr_city').value.trim();
                                                        let state = document.getElementById('new_addr_state').value.trim();
                                                        let pincode = document.getElementById('new_addr_pincode').value.trim();
                                                        let errEl = document.getElementById('new_addr_error');
                                                        if (!name || !phone || !line1 || !city || !state || !pincode) {
                                                            errEl.textContent = 'Please fill all required fields.';
                                                            errEl.classList.remove('hidden');
                                                            return;
                                                        }
                                                        errEl.classList.add('hidden');
                                                        savingAddress = true;
                                                        fetch('{{ route('account.addresses.store') }}', {
                                                            method: 'POST',
                                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                                            body: JSON.stringify({ name, phone, address_line_1: line1, address_line_2: line2, city, state, postal_code: pincode })
                                                        }).then(r => r.json().then(d => ({ok: r.ok, data: d}))).then(({ok, data}) => {
                                                            savingAddress = false;
                                                            if (ok) { location.reload(); }
                                                            else {
                                                                let msg = data.message || Object.values(data.errors || {}).flat().join(', ') || 'Failed to save address';
                                                                errEl.textContent = msg;
                                                                errEl.classList.remove('hidden');
                                                            }
                                                        }).catch(() => { savingAddress = false; errEl.textContent = 'Something went wrong.'; errEl.classList.remove('hidden'); });
                                                    "
                                                    class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors disabled:opacity-50">
                                                <span x-show="!savingAddress">Save Address</span>
                                                <span x-show="savingAddress" class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                    Saving...
                                                </span>
                                            </button>
                                            <button type="button" @click="showAddressForm = false" class="px-4 py-2 text-sm font-medium text-neutral-600 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @error('shipping_address_id')
                                    <p class="mt-2 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Billing Address (auth users only) -->
                        @if(!$isGuest)
                        <div class="bg-white rounded-lg border border-neutral-100">
                            <div class="flex items-center gap-2.5 px-4 py-3 border-b border-neutral-100">
                                <div class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center">2</div>
                                <h2 class="text-sm font-semibold text-neutral-900">Billing Address</h2>
                            </div>
                            <div class="p-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="same_billing_address" value="1" x-model="sameBilling"
                                           class="rounded border-neutral-300 text-primary-500 focus:ring-primary-500">
                                    <span class="text-[13px] text-neutral-700">Same as shipping address</span>
                                </label>

                                <div x-show="!sameBilling" x-collapse class="mt-3">
                                    @if($addresses->count())
                                        <div class="space-y-2.5">
                                            @foreach($addresses as $address)
                                                <label class="flex items-start gap-3 p-3 border border-neutral-200 rounded-lg cursor-pointer hover:border-primary-300 has-checked:border-primary-500 has-checked:bg-primary-50/50 transition-colors">
                                                    <input type="radio" name="billing_address_id" value="{{ $address->id }}"
                                                           class="mt-0.5 text-primary-500 focus:ring-primary-500">
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-[13px] font-semibold text-neutral-900">{{ $address->name }}</span>
                                                        <p class="text-xs text-neutral-600">
                                                            {{ $address->address_line_1 }}, {{ $address->city }}, {{ $address->state }}
                                                        </p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                            <input type="hidden" name="same_billing_address" value="1">
                        @endif

                        <!-- Payment Method -->
                        <div class="bg-white rounded-lg border border-neutral-100">
                            <div class="flex items-center gap-2.5 px-4 py-3 border-b border-neutral-100">
                                <div class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center">{{ $isGuest ? '3' : '3' }}</div>
                                <h2 class="text-sm font-semibold text-neutral-900">Payment Method</h2>
                            </div>
                            <div class="p-4 space-y-2.5">
                                {{-- Razorpay --}}
                                @if(($paymentSettings['razorpay_enabled'] ?? '1') === '1')
                                <div @click="paymentMethod = 'razorpay'"
                                     :class="paymentMethod === 'razorpay' ? 'border-primary-400 bg-primary-50/50 ring-1 ring-primary-200' : 'border-neutral-200 hover:border-neutral-300'"
                                     class="border rounded-lg cursor-pointer transition-all overflow-hidden">
                                    <div class="flex items-center gap-3 p-3">
                                        <input type="radio" name="payment_method" value="razorpay" x-model="paymentMethod"
                                               class="text-primary-500 focus:ring-primary-500">
                                        <div class="flex items-center gap-2.5 flex-1">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 text-white text-xs font-bold"
                                                 style="background:#072654;">R₹</div>
                                            <div>
                                                <span class="text-[13px] font-medium text-neutral-800">Razorpay</span>
                                                <p class="text-[11px] text-neutral-600">Cards, UPI, Net Banking & more</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="paymentMethod === 'razorpay'" x-collapse>
                                        <div class="px-3.5 pb-3.5 pt-0">
                                            <div class="flex items-center gap-2 p-2.5 bg-primary-50 border border-primary-100 rounded-lg">
                                                <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <p class="text-[11px] text-primary-700">Pay securely using Cards, UPI, Net Banking & more. Payment opens right here on this page.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- UPI --}}
                                @if(($paymentSettings['upi_enabled'] ?? '1') === '1')
                                <div @click="paymentMethod = 'upi'"
                                     :class="paymentMethod === 'upi' ? 'border-primary-400 bg-primary-50/50 ring-1 ring-primary-200' : 'border-neutral-200 hover:border-neutral-300'"
                                     class="border rounded-lg cursor-pointer transition-all overflow-hidden">
                                    <div class="flex items-center gap-3 p-3">
                                        <input type="radio" name="payment_method" value="upi" x-model="paymentMethod"
                                               class="text-primary-500 focus:ring-primary-500">
                                        <div class="flex items-center gap-2.5 flex-1">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                                 :class="paymentMethod === 'upi' ? 'bg-primary-100 text-primary-600' : 'bg-neutral-100 text-neutral-600'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="text-[13px] font-medium text-neutral-800">UPI</span>
                                                <p class="text-[11px] text-neutral-600">Google Pay, PhonePe, Paytm, BHIM</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="paymentMethod === 'upi'" x-collapse>
                                        <div class="px-3.5 pb-3.5 pt-0 space-y-3" @click.stop>
                                            <div>
                                                <label class="block text-[11px] font-medium text-neutral-600 mb-1">UPI ID</label>
                                                <input type="text" name="upi_id" placeholder="yourname@upi"
                                                       class="w-full rounded-lg border border-neutral-200 text-sm px-3 py-2.5 focus:border-primary-300 focus:ring focus:ring-primary-100 focus:ring-opacity-50 bg-white">
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] text-neutral-600">Supported:</span>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-medium text-neutral-600 bg-neutral-100 px-1.5 py-0.5 rounded">GPay</span>
                                                    <span class="text-[10px] font-medium text-neutral-600 bg-neutral-100 px-1.5 py-0.5 rounded">PhonePe</span>
                                                    <span class="text-[10px] font-medium text-neutral-600 bg-neutral-100 px-1.5 py-0.5 rounded">Paytm</span>
                                                    <span class="text-[10px] font-medium text-neutral-600 bg-neutral-100 px-1.5 py-0.5 rounded">BHIM</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Cash on Delivery --}}
                                @if(($paymentSettings['cod_enabled'] ?? '1') === '1')
                                <div @click="paymentMethod = 'cod'"
                                     :class="paymentMethod === 'cod' ? 'border-primary-400 bg-primary-50/50 ring-1 ring-primary-200' : 'border-neutral-200 hover:border-neutral-300'"
                                     class="border rounded-lg cursor-pointer transition-all overflow-hidden">
                                    <div class="flex items-center gap-3 p-3">
                                        <input type="radio" name="payment_method" value="cod" x-model="paymentMethod"
                                               class="text-primary-500 focus:ring-primary-500">
                                        <div class="flex items-center gap-2.5 flex-1">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                                 :class="paymentMethod === 'cod' ? 'bg-primary-100 text-primary-600' : 'bg-neutral-100 text-neutral-600'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="text-[13px] font-medium text-neutral-800">Cash on Delivery</span>
                                                <p class="text-[11px] text-neutral-600">Pay when your order arrives</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="paymentMethod === 'cod'" x-collapse>
                                        <div class="px-3.5 pb-3.5 pt-0 space-y-2">
                                            <div class="flex items-center gap-2 p-2.5 bg-amber-50 border border-amber-100 rounded-lg">
                                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <div class="text-[11px] text-amber-700">
                                                    <p class="font-semibold">Partial Payment Required</p>
                                                    <p>Pay <strong>@price(100)</strong> now to confirm your order. Remaining amount on delivery.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @error('payment_method')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Weekend Prepaid Bonus --}}
                        @if($isWeekend)
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-lg p-3" x-show="paymentMethod !== 'cod'">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-xs font-bold text-emerald-800">Weekend Special: Extra 5% Off!</p>
                                    <p class="text-[11px] text-emerald-600">Pay online (Razorpay/UPI) and get an additional 5% discount. Not available on COD.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Available Coupons --}}
                        @if($availableCoupons->count())
                        <div class="bg-white rounded-lg border border-neutral-100">
                            <div class="flex items-center gap-2.5 px-4 py-3 border-b border-neutral-100">
                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <h2 class="text-sm font-semibold text-neutral-900">Available Coupons</h2>
                            </div>
                            <div class="p-4 space-y-2" x-data="{ applying: false }">
                                @foreach($availableCoupons as $coupon)
                                    <div class="flex items-center justify-between p-2.5 border border-dashed {{ $cart->coupon_id === $coupon->id ? 'border-primary-400 bg-primary-50/50' : 'border-neutral-200' }} rounded-lg">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-primary-700 bg-primary-50 border border-primary-200 px-2 py-0.5 rounded font-mono">{{ $coupon->code }}</span>
                                                @if($cart->coupon_id === $coupon->id)
                                                    <span class="text-[10px] font-medium text-success-600 bg-success-50 px-1.5 py-0.5 rounded">Applied</span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-neutral-600 mt-0.5">
                                                {{ $coupon->name }}
                                                @if($coupon->min_order_amount > 0)
                                                    &middot; Min order @price($coupon->min_order_amount)
                                                @endif
                                                @if($coupon->max_discount > 0)
                                                    &middot; Max @price($coupon->max_discount) off
                                                @endif
                                            </p>
                                        </div>
                                        @if($cart->coupon_id !== $coupon->id)
                                            <button type="button" :disabled="applying"
                                                    @click="applying = true; fetch('{{ route('cart.apply-coupon') }}', {
                                                        method: 'POST',
                                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                                        body: JSON.stringify({ code: '{{ $coupon->code }}' })
                                                    }).then(r => r.json()).then(d => { applying = false; location.reload(); }).catch(() => { applying = false; })"
                                                    class="text-xs font-semibold text-primary-600 hover:text-primary-700 px-3 py-1.5 border border-primary-200 rounded-lg hover:bg-primary-50 transition-colors">
                                                Apply
                                            </button>
                                        @else
                                            <button type="button"
                                                    @click="fetch('{{ route('cart.remove-coupon') }}', {
                                                        method: 'POST',
                                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                                    }).then(() => location.reload())"
                                                    class="text-xs font-medium text-red-600 hover:text-red-700 px-3 py-1.5 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Order Notes -->
                        <div class="bg-white rounded-lg border border-neutral-100">
                            <div class="flex items-center gap-2.5 px-4 py-3 border-b border-neutral-100">
                                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <h2 class="text-sm font-semibold text-neutral-900">Order Notes <span class="font-normal text-neutral-600">(Optional)</span></h2>
                            </div>
                            <div class="p-4">
                                <textarea name="notes" rows="2" class="form-input w-full text-[13px]"
                                          placeholder="Special instructions for delivery or your order...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="lg:w-85 shrink-0 self-stretch">
                        <div class="bg-white rounded-lg border border-neutral-100 lg:sticky lg:top-20 flex flex-col">
                            <!-- Coupon Display -->
                            @if($cart->coupon)
                                <div class="p-4 border-b border-neutral-100">
                                    <div class="flex items-center gap-2 mb-2.5">
                                        <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        <span class="text-sm font-semibold text-neutral-800">Coupon Applied</span>
                                    </div>
                                    <div class="flex items-center justify-between px-3 py-2 bg-success-50 border border-dashed border-success-300 rounded-md">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-success-700 bg-success-100 px-2 py-0.5 rounded">{{ $cart->coupon->code }}</span>
                                                @if($cart->coupon->auto_apply)
                                                    <span class="text-[10px] text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded font-medium">Auto</span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-success-600 font-medium">
                                                @if($cart->coupon->type === 'buy_x_get_y')
                                                    Buy {{ $cart->coupon->conditions['buy_qty'] ?? 0 }} Get {{ $cart->coupon->conditions['get_qty'] ?? 0 }}{{ $cart->coupon->value >= 100 ? ' Free' : ' at ' . intval($cart->coupon->value) . '% off' }}
                                                @elseif($cart->coupon->type === 'percentage')
                                                    {{ intval($cart->coupon->value) }}% off applied
                                                @elseif($cart->coupon->type === 'fixed')
                                                    @price($cart->coupon->value) off applied
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-xs font-semibold text-success-700">-@price($cart->discount)</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Items -->
                            <div class="p-4 border-b border-neutral-100">
                                <h3 class="text-[11px] font-bold text-neutral-600 uppercase tracking-wider mb-3">Order Items ({{ $cart->items->sum('quantity') }} {{ $cart->items->sum('quantity') === 1 ? 'item' : 'items' }})</h3>

                                <div class="space-y-3 max-h-52 overflow-y-auto">
                                    @foreach($cart->items as $item)
                                        <div class="flex gap-2.5">
                                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}"
                                                 class="w-12 h-12 rounded border border-neutral-100 bg-neutral-50 object-contain shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-medium text-neutral-800 line-clamp-1">{{ $item->product->name }}</p>
                                                <div class="flex items-center justify-between mt-0.5">
                                                    <span class="text-[11px] text-neutral-600">Qty: {{ $item->quantity }}</span>
                                                    <span class="text-[13px] font-semibold text-neutral-900">@price($item->price * $item->quantity)</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Details -->
                            <div class="p-4">
                                <h3 class="text-[11px] font-bold text-neutral-600 uppercase tracking-wider mb-3">Price Details</h3>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-[13px]">
                                        <span class="text-neutral-600">Subtotal</span>
                                        <span class="text-neutral-800 font-medium">@price($cart->subtotal)</span>
                                    </div>

                                    @if($cart->discount > 0)
                                        <div class="flex items-center justify-between text-[13px]">
                                            <span class="text-neutral-600">
                                                @if($cart->coupon)
                                                    @if($cart->coupon->type === 'buy_x_get_y')
                                                        Buy {{ $cart->coupon->conditions['buy_qty'] ?? 0 }} Get {{ $cart->coupon->conditions['get_qty'] ?? 0 }}{{ $cart->coupon->value >= 100 ? ' Free' : '' }}
                                                    @elseif($cart->coupon->type === 'percentage')
                                                        Coupon ({{ $cart->coupon->code }} - {{ intval($cart->coupon->value) }}%)
                                                    @else
                                                        Coupon ({{ $cart->coupon->code }})
                                                    @endif
                                                @else
                                                    Discount
                                                @endif
                                            </span>
                                            <span class="text-success-600 font-medium">-@price($cart->discount)</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between text-[13px]">
                                        <span class="text-neutral-600">Shipping</span>
                                        <span class="text-success-600 font-semibold">FREE</span>
                                    </div>

                                    @if($cart->tax > 0)
                                        <div class="flex items-center justify-between text-[13px]">
                                            <span class="text-neutral-600">Tax</span>
                                            <span class="text-neutral-800 font-medium">@price($cart->tax)</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="border-t border-dashed border-neutral-200 my-3"></div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-neutral-900">Total Amount</span>
                                    <span class="text-sm font-bold text-neutral-900">@price($cart->total)</span>
                                </div>

                                @if($cart->discount > 0)
                                    <div class="mt-3 px-3 py-2 bg-success-50 border border-success-100 rounded-md">
                                        <p class="text-xs font-semibold text-success-700 text-center">
                                            You save @price($cart->discount) on this order
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Place Order Button -->
                            <div class="p-4 pt-0">
                                <button type="submit" :disabled="processing"
                                        class="block w-full py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold text-center rounded-lg transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!processing">
                                        <template x-if="paymentMethod === 'razorpay' || paymentMethod === 'upi'">
                                            <span>PAY NOW</span>
                                        </template>
                                        <template x-if="paymentMethod === 'cod'">
                                            <span>PLACE ORDER</span>
                                        </template>
                                    </span>
                                    <span x-show="processing" x-cloak class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Processing...
                                    </span>
                                </button>
                                <p x-show="error" x-text="error" class="text-[11px] text-error-500 text-center mt-2" x-cloak></p>
                                @if(!$isGuest && $addresses->isEmpty())
                                    <p class="text-[11px] text-error-500 text-center mt-2">Please add an address to place your order.</p>
                                @endif
                            </div>

                            <!-- Trust Badges -->
                            <div class="px-4 pb-4">
                                <div class="flex items-center justify-center gap-4 pt-3 border-t border-neutral-100">
                                    <div class="flex items-center gap-1.5 text-neutral-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span class="text-[10px] font-medium">Secure</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-neutral-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span class="text-[10px] font-medium">100% Genuine</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-neutral-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span class="text-[10px] font-medium">Easy Returns</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="px-4 pb-4">
                                <p class="text-[10px] text-neutral-600 text-center leading-relaxed">
                                    By placing your order, you agree to our
                                    <a href="{{ route('terms') }}" class="text-primary-500 hover:text-primary-600">Terms</a>
                                    and
                                    <a href="{{ route('privacy') }}" class="text-primary-500 hover:text-primary-600">Privacy Policy</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            function checkoutForm(firstMethod) {
                return {
                    sameBilling: true,
                    paymentMethod: firstMethod,
                    showAddressForm: false,
                    savingAddress: false,
                    processing: false,
                    error: '',

                    handleSubmit(e) {
                        this.error = '';

                        // For COD, do regular form submit
                        if (this.paymentMethod === 'cod') {
                            this.processing = true;
                            e.target.submit();
                            return;
                        }

                        // For Razorpay/UPI, do inline payment
                        this.initiateRazorpay(e.target);
                    },

                    async initiateRazorpay(form) {
                        this.processing = true;
                        const formData = new FormData(form);
                        const data = Object.fromEntries(formData.entries());

                        try {
                            const response = await fetch('{{ route("checkout.razorpay.create") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(data),
                            });

                            const result = await response.json();

                            if (!response.ok) {
                                this.error = result.error || result.message || 'Something went wrong. Please try again.';
                                this.processing = false;
                                return;
                            }

                            // Open Razorpay inline checkout
                            this.openRazorpayCheckout(result);
                        } catch (err) {
                            this.error = 'Network error. Please check your connection and try again.';
                            this.processing = false;
                        }
                    },

                    openRazorpayCheckout(orderData) {
                        const self = this;

                        const options = {
                            key: orderData.key,
                            amount: orderData.amount,
                            currency: orderData.currency,
                            name: orderData.name,
                            description: orderData.description,
                            order_id: orderData.order_id,
                            prefill: orderData.prefill,
                            theme: {
                                color: '#205258',
                                backdrop_color: 'rgba(0, 0, 0, 0.5)',
                            },
                            modal: {
                                ondismiss: function() {
                                    self.processing = false;
                                    self.error = 'Payment was cancelled. You can try again.';
                                },
                                confirm_close: true,
                                escape: false,
                            },
                            handler: function(response) {
                                self.verifyPayment(response);
                            },
                        };

                        const rzp = new Razorpay(options);
                        rzp.on('payment.failed', function(response) {
                            self.processing = false;
                            self.error = response.error.description || 'Payment failed. Please try again.';
                        });
                        rzp.open();
                    },

                    async verifyPayment(paymentResponse) {
                        try {
                            const response = await fetch('{{ route("checkout.razorpay.verify") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    razorpay_order_id: paymentResponse.razorpay_order_id,
                                    razorpay_payment_id: paymentResponse.razorpay_payment_id,
                                    razorpay_signature: paymentResponse.razorpay_signature,
                                }),
                            });

                            const result = await response.json();

                            if (result.success && result.redirect) {
                                window.location.href = result.redirect;
                            } else {
                                this.error = result.error || 'Payment verification failed. Please contact support.';
                                this.processing = false;
                            }
                        } catch (err) {
                            this.error = 'Verification failed. If amount was deducted, it will be refunded. Please contact support.';
                            this.processing = false;
                        }
                    },
                };
            }
        </script>
    </x-slot>
</x-layouts.app>
