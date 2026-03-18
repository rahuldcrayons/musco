<x-layouts.app>
    <x-slot name="title">Checkout - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Secure checkout at {{ config('app.name') }}. Fast payment with Razorpay or Partial Pay.">
        <meta name="robots" content="noindex, nofollow">
        <meta property="og:title" content="Checkout - {{ config('app.name') }}">
        <meta property="og:description" content="Complete your order securely at {{ config('app.name') }}.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('checkout.index') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Checkout - {{ config('app.name') }}">

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

    <div class="bg-[#F7F8FA] min-h-screen">
        <div class="container mx-auto px-3 py-3">
            <x-breadcrumb :items="[['label' => 'Cart', 'url' => route('cart.index')], ['label' => 'Checkout', 'url' => null]]" />
        </div>

        <div class="container mx-auto px-3 pb-8">
            {{-- Header with back + user info + logout --}}
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="text-xs text-[#007185] hover:text-[#C7511F] font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Cart
                    </a>
                    <h1 class="text-base font-bold text-[#0F1111]">Checkout</h1>
                </div>
                @auth
                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#565959]">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-[#CC0C39] hover:underline font-medium">Logout</button>
                    </form>
                </div>
                @endauth
            </div>

            @php
                $methodOrder = ['razorpay' => 'razorpay_enabled', 'cod' => 'cod_enabled'];
                $firstMethod = 'cod';
                foreach ($methodOrder as $method => $key) {
                    if (($paymentSettings[$key] ?? '1') === '1') { $firstMethod = $method; break; }
                }
            @endphp

            <form action="{{ route('checkout.process') }}" method="POST"
                  x-data="checkoutForm('{{ $firstMethod }}')"
                  @submit.prevent="handleSubmit($event)">
                @csrf

                <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                    {{-- ═══ LEFT COLUMN ═══ --}}
                    <div class="flex-1 min-w-0 space-y-3">

                        {{-- ── Section 1: Contact + Shipping (merged for guests, just shipping for auth) ── --}}
                        <div class="bg-white rounded border border-[#E3E6E6]">
                            <div class="flex items-center gap-2 px-3 py-2.5 border-b border-[#E3E6E6] bg-[#F7F8FA]">
                                <div class="w-5 h-5 rounded-full bg-[#205258] text-white text-[10px] font-bold flex items-center justify-center">1</div>
                                <h2 class="text-xs font-bold text-[#0F1111] uppercase tracking-wide">
                                    @if($isGuest) Contact & Shipping @else Shipping Address @endif
                                </h2>
                            </div>

                            <div class="p-3">
                                @if($isGuest)
                                {{-- Guest: contact + address in one compact block --}}
                                <p class="text-[11px] text-[#565959] mb-2">Have an account? <a href="{{ route('login') }}" class="text-[#007185] hover:text-[#C7511F] font-medium">Log in</a> for faster checkout.</p>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-2">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Phone *</label>
                                        <input type="tel" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" required autocomplete="tel" autofocus
                                               class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2" style="outline:none" onfocus="this.style.border='1px solid #007185'" onblur="this.style.border='1px solid #E3E6E6'" placeholder="+91 98765 43210">
                                        @error('guest_phone') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Name *</label>
                                        <input type="text" name="guest_name" value="{{ old('guest_name') }}" required autocomplete="name"
                                               class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2" style="outline:none" onfocus="this.style.border='1px solid #007185'" onblur="this.style.border='1px solid #E3E6E6'" placeholder="Full name">
                                        @error('guest_name') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Email *</label>
                                        <input type="email" name="guest_email" value="{{ old('guest_email') }}" required autocomplete="email"
                                               class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2" style="outline:none" onfocus="this.style.border='1px solid #007185'" onblur="this.style.border='1px solid #E3E6E6'" placeholder="email@example.com">
                                        @error('guest_email') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="border-t border-dashed border-[#E3E6E6] my-2"></div>

                                {{-- Shipping fields with PIN autocomplete --}}
                                <div class="space-y-2" x-data="pinLookup()">
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">PIN Code *</label>
                                            <input type="text" name="shipping_postal_code" x-model="pin" @input="fetchPinData()" value="{{ old('shipping_postal_code') }}" required maxlength="6" autocomplete="postal-code"
                                                   class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="400001">
                                            <p x-show="pinError" x-text="pinError" class="text-[10px] text-[#CC0C39] mt-0.5" x-cloak></p>
                                            @error('shipping_postal_code') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">City *</label>
                                            <input type="text" name="shipping_city" x-model="city" value="{{ old('shipping_city') }}" required autocomplete="address-level2"
                                                   class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="City">
                                            @error('shipping_city') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">State *</label>
                                            <input type="text" name="shipping_state" x-model="state" value="{{ old('shipping_state') }}" required autocomplete="address-level1"
                                                   class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="State">
                                            @error('shipping_state') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Recipient Name *</label>
                                            <input type="text" name="shipping_name" value="{{ old('shipping_name') }}" required autocomplete="shipping name"
                                                   class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="Recipient name">
                                            @error('shipping_name') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Phone *</label>
                                            <input type="tel" name="shipping_phone" value="{{ old('shipping_phone') }}" required autocomplete="tel"
                                                   class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="+91 98765 43210">
                                            @error('shipping_phone') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Address Line 2</label>
                                            <input type="text" name="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}" autocomplete="address-line2"
                                                   class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="Area, Landmark (optional)">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Address *</label>
                                        <input type="text" name="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}" required autocomplete="address-line1"
                                               class="w-full text-sm border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="House no., Building, Street">
                                        @error('shipping_address_line_1') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <input type="hidden" name="same_billing_address" value="1">
                                @else
                                {{-- Authenticated: saved addresses compact --}}
                                @if($addresses->count())
                                    <div class="space-y-2">
                                        @foreach($addresses as $address)
                                            <label class="flex items-start gap-2.5 p-2.5 border rounded cursor-pointer transition-colors
                                                {{ $address->id === $defaultAddress?->id ? 'border-[#205258] bg-[#205258]/5' : 'border-[#E3E6E6] hover:border-[#007185]' }}">
                                                <input type="radio" name="shipping_address_id" value="{{ $address->id }}"
                                                       {{ $address->id === $defaultAddress?->id ? 'checked' : '' }}
                                                       class="mt-0.5 text-[#205258] focus:ring-[#205258]">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs font-semibold text-[#0F1111]">{{ $address->name }}</span>
                                                        @if($address->is_default)
                                                            <span class="text-[9px] font-medium text-[#205258] bg-[#205258]/10 px-1 py-px rounded">Default</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-[#565959] leading-relaxed">
                                                        {{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }},
                                                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                                        &middot; {{ $address->phone }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    <button type="button" @click="showAddressForm = !showAddressForm" class="inline-flex items-center gap-1 mt-2 text-[11px] font-medium text-[#007185] hover:text-[#C7511F]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span x-text="showAddressForm ? 'Cancel' : 'Add New Address'"></span>
                                    </button>
                                @else
                                    <div class="text-center py-4" x-show="!showAddressForm">
                                        <p class="text-xs text-[#565959] mb-2">No saved addresses found.</p>
                                        <button type="button" @click="showAddressForm = true" class="inline-flex items-center gap-1 text-xs font-semibold text-white bg-[#205258] hover:bg-[#205258]/90 px-3 py-1.5 rounded transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Add Address
                                        </button>
                                    </div>
                                @endif

                                {{-- Inline Add Address Form with PIN autocomplete --}}
                                <div x-show="showAddressForm" x-collapse x-cloak class="mt-2 p-3 bg-[#F7F8FA] rounded border border-[#E3E6E6] space-y-2" x-data="pinLookup()">
                                    <h3 class="text-xs font-bold text-[#0F1111]">New Address</h3>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Full Name *</label>
                                            <input type="text" id="new_addr_name" class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="Full name">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Phone *</label>
                                            <input type="tel" id="new_addr_phone" class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="+91 98765 43210">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Address *</label>
                                        <input type="text" id="new_addr_line1" class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="House no., Building, Street">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">Area / Landmark</label>
                                        <input type="text" id="new_addr_line2" class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="Optional">
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">PIN Code *</label>
                                            <input type="text" id="new_addr_pincode" x-model="pin" @input="fetchPinData()" maxlength="6"
                                                   class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="400001">
                                            <p x-show="pinError" x-text="pinError" class="text-[10px] text-[#CC0C39] mt-0.5" x-cloak></p>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">City *</label>
                                            <input type="text" id="new_addr_city" x-model="city"
                                                   class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="City">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#565959] mb-0.5">State *</label>
                                            <input type="text" id="new_addr_state" x-model="state"
                                                   class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none" placeholder="State">
                                        </div>
                                    </div>
                                    <div id="new_addr_error" class="hidden text-[10px] text-[#CC0C39]"></div>
                                    <div class="flex gap-2 pt-1">
                                        <button type="button" :disabled="savingAddress"
                                                @click="
                                                    let name = document.getElementById('new_addr_name').value.trim();
                                                    let phone = document.getElementById('new_addr_phone').value.trim();
                                                    let line1 = document.getElementById('new_addr_line1').value.trim();
                                                    let line2 = document.getElementById('new_addr_line2').value.trim();
                                                    let pincode = pin;
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
                                                class="px-3 py-1.5 text-xs font-semibold text-white bg-[#205258] hover:bg-[#205258]/90 rounded transition-colors disabled:opacity-50">
                                            <span x-show="!savingAddress">Save</span>
                                            <span x-show="savingAddress" class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                Saving...
                                            </span>
                                        </button>
                                        <button type="button" @click="showAddressForm = false" class="px-3 py-1.5 text-xs font-medium text-[#565959] border border-[#E3E6E6] rounded hover:bg-[#F7F8FA] transition-colors">Cancel</button>
                                    </div>
                                </div>

                                @error('shipping_address_id')
                                    <p class="mt-1 text-[10px] text-[#CC0C39]">{{ $message }}</p>
                                @enderror
                                @endif
                            </div>
                        </div>

                        {{-- ── Section 2: Billing (auth only, collapsed by default) ── --}}
                        @if(!$isGuest)
                        <div class="bg-white rounded border border-[#E3E6E6]">
                            <div class="px-3 py-2.5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="same_billing_address" value="1" x-model="sameBilling"
                                           class="rounded border-[#E3E6E6] text-[#205258] focus:ring-[#205258] w-3.5 h-3.5">
                                    <span class="text-xs text-[#0F1111] font-medium">Billing same as shipping</span>
                                </label>

                                <div x-show="!sameBilling" x-collapse class="mt-2 pt-2 border-t border-[#E3E6E6]">
                                    @if($addresses->count())
                                        <div class="space-y-1.5">
                                            @foreach($addresses as $address)
                                                <label class="flex items-start gap-2 p-2 border border-[#E3E6E6] rounded cursor-pointer hover:border-[#007185] transition-colors">
                                                    <input type="radio" name="billing_address_id" value="{{ $address->id }}"
                                                           class="mt-0.5 text-[#205258] focus:ring-[#205258]">
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-[11px] font-semibold text-[#0F1111]">{{ $address->name }}</span>
                                                        <p class="text-[10px] text-[#565959]">{{ $address->address_line_1 }}, {{ $address->city }}, {{ $address->state }}</p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ── Section 3: Payment Method (Razorpay + COD only) ── --}}
                        <div class="bg-white rounded border border-[#E3E6E6]">
                            <div class="flex items-center gap-2 px-3 py-2.5 border-b border-[#E3E6E6] bg-[#F7F8FA]">
                                <div class="w-5 h-5 rounded-full bg-[#205258] text-white text-[10px] font-bold flex items-center justify-center">{{ $isGuest ? '2' : '2' }}</div>
                                <h2 class="text-xs font-bold text-[#0F1111] uppercase tracking-wide">Payment</h2>
                            </div>
                            <div class="p-3 space-y-2">
                                {{-- Razorpay --}}
                                @if(($paymentSettings['razorpay_enabled'] ?? '1') === '1')
                                <div @click="paymentMethod = 'razorpay'"
                                     :class="paymentMethod === 'razorpay' ? 'border-[#205258] bg-[#205258]/5 ring-1 ring-[#205258]/20' : 'border-[#E3E6E6] hover:border-[#007185]'"
                                     class="border rounded cursor-pointer transition-all">
                                    <div class="flex items-center gap-2.5 p-2.5">
                                        <input type="radio" name="payment_method" value="razorpay" x-model="paymentMethod"
                                               class="text-[#205258] focus:ring-[#205258]">
                                        <div class="flex items-center gap-2 flex-1">
                                            {{-- Razorpay Logo --}}
                                            <img src="{{ asset('images/razorpay.png') }}" alt="Razorpay" class="h-5 w-auto shrink-0">
                                            <div>
                                                <p class="text-[10px] text-[#565959]">Cards, UPI, Net Banking & more</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Partial Pay (₹100 advance + rest COD) --}}
                                @if(($paymentSettings['cod_enabled'] ?? '1') === '1')
                                <div @click="paymentMethod = 'cod'"
                                     :class="paymentMethod === 'cod' ? 'border-[#205258] bg-[#205258]/5 ring-1 ring-[#205258]/20' : 'border-[#E3E6E6] hover:border-[#007185]'"
                                     class="border rounded cursor-pointer transition-all">
                                    <div class="flex items-center gap-2.5 p-2.5">
                                        <input type="radio" name="payment_method" value="cod" x-model="paymentMethod"
                                               class="text-[#205258] focus:ring-[#205258]">
                                        <div class="flex items-center gap-2 flex-1">
                                            <div class="w-7 h-7 rounded flex items-center justify-center shrink-0"
                                                 :class="paymentMethod === 'cod' ? 'bg-[#F8931D]/15 text-[#F8931D]' : 'bg-[#F7F8FA] text-[#565959]'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="text-xs font-medium text-[#0F1111]">Partial Pay</span>
                                                <p class="text-[10px] text-[#565959]">Pay ₹100 now, rest on delivery</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="paymentMethod === 'cod'" x-collapse>
                                        <div class="px-2.5 pb-2.5 pt-0">
                                            <div class="flex items-center gap-1.5 p-2 bg-[#205258]/5 border border-[#205258]/15 rounded text-[10px] text-[#0F1111]">
                                                <svg class="w-3.5 h-3.5 text-[#205258] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                <span>Pay <strong class="text-[#205258]">@price(100)</strong> advance via Razorpay to confirm. <strong>@price($cart->total - 100 > 0 ? $cart->total - 100 : 0)</strong> on delivery.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @error('payment_method')
                                    <p class="text-[10px] text-[#CC0C39]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Weekend Prepaid Bonus --}}
                        @if($isWeekend)
                        <div class="bg-emerald-50 border border-emerald-200 rounded p-2.5 flex items-center gap-2" x-show="paymentMethod !== 'cod'">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-[11px] font-bold text-emerald-800">Weekend: Extra 5% Off on Online Payment!</p>
                            </div>
                        </div>
                        @endif

                        {{-- Order Notes (compact) --}}
                        <div class="bg-white rounded border border-[#E3E6E6]">
                            <div class="p-3">
                                <label class="block text-[10px] font-semibold text-[#565959] mb-1">Order Notes (optional)</label>
                                <textarea name="notes" rows="2" class="w-full text-xs border border-[#E3E6E6] rounded px-2.5 py-2 focus:border-[#007185] focus:outline-none resize-none"
                                          placeholder="Special delivery instructions...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ RIGHT COLUMN - Order Summary ═══ --}}
                    <div class="lg:w-80 shrink-0 self-stretch">
                        <div class="bg-white rounded border border-[#E3E6E6] lg:sticky lg:top-20">

                            {{-- Coupons Carousel (horizontal scroll) --}}
                            @if($availableCoupons->count())
                            <div class="p-3 border-b border-[#E3E6E6]" x-data="{ applying: false }">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <svg class="w-3.5 h-3.5 text-[#FFA41C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span class="text-[10px] font-bold text-[#0F1111] uppercase tracking-wider">Coupons</span>
                                </div>
                                <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-thin">
                                    @foreach($availableCoupons as $coupon)
                                        <div class="flex-shrink-0 w-44 border border-dashed {{ $cart->coupon_id === $coupon->id ? 'border-[#205258] bg-[#205258]/5' : 'border-[#E3E6E6]' }} rounded p-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[10px] font-bold text-[#205258] bg-[#205258]/10 px-1.5 py-0.5 rounded font-mono">{{ $coupon->code }}</span>
                                                @if($cart->coupon_id === $coupon->id)
                                                    <span class="text-[9px] font-medium text-green-700 bg-green-50 px-1 py-px rounded">Applied</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-[#565959] leading-snug mb-1.5 line-clamp-2">{{ $coupon->name }}</p>
                                            @if($cart->coupon_id !== $coupon->id)
                                                <button type="button" :disabled="applying"
                                                        @click="applying = true; fetch('{{ route('cart.apply-coupon') }}', {
                                                            method: 'POST',
                                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                                            body: JSON.stringify({ code: '{{ $coupon->code }}' })
                                                        }).then(r => r.json()).then(d => { applying = false; location.reload(); }).catch(() => { applying = false; })"
                                                        class="w-full text-[10px] font-semibold text-[#007185] hover:text-white border border-[#007185] hover:bg-[#007185] rounded py-1 transition-colors">
                                                    Apply
                                                </button>
                                            @else
                                                <button type="button"
                                                        @click="fetch('{{ route('cart.remove-coupon') }}', {
                                                            method: 'POST',
                                                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                                        }).then(() => location.reload())"
                                                        class="w-full text-[10px] font-medium text-[#CC0C39] border border-[#CC0C39]/30 rounded py-1 hover:bg-[#CC0C39]/5 transition-colors">
                                                    Remove
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Applied Coupon Badge --}}
                            @if($cart->coupon)
                                <div class="px-3 py-2 border-b border-[#E3E6E6] bg-green-50/50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded font-mono">{{ $cart->coupon->code }}</span>
                                            @if($cart->coupon->auto_apply)
                                                <span class="text-[9px] text-[#205258] bg-[#205258]/10 px-1 py-px rounded font-medium">Auto</span>
                                            @endif
                                            <span class="text-[10px] text-green-600 font-medium">
                                                @if($cart->coupon->type === 'percentage')
                                                    {{ intval($cart->coupon->value) }}% off
                                                @elseif($cart->coupon->type === 'fixed')
                                                    @price($cart->coupon->value) off
                                                @elseif($cart->coupon->type === 'buy_x_get_y')
                                                    Buy {{ $cart->coupon->conditions['buy_qty'] ?? 0 }} Get {{ $cart->coupon->conditions['get_qty'] ?? 0 }}{{ $cart->coupon->value >= 100 ? ' Free' : '' }}
                                                @endif
                                            </span>
                                        </div>
                                        <span class="text-xs font-bold text-green-700">-@price($cart->discount)</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Order Items --}}
                            <div class="p-3 border-b border-[#E3E6E6]">
                                <h3 class="text-[10px] font-bold text-[#565959] uppercase tracking-wider mb-2">
                                    {{ $cart->items->sum('quantity') }} {{ $cart->items->sum('quantity') === 1 ? 'Item' : 'Items' }}
                                </h3>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach($cart->items as $item)
                                        <div class="flex gap-2">
                                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}"
                                                 class="w-10 h-10 rounded border border-[#E3E6E6] bg-white object-contain shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] font-medium text-[#0F1111] line-clamp-1">{{ $item->product->name }}</p>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[10px] text-[#565959]">Qty: {{ $item->quantity }}</span>
                                                    <span class="text-[11px] font-semibold text-[#0F1111]">@price($item->price * $item->quantity)</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Price Details --}}
                            <div class="p-3">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-[#565959]">Subtotal</span>
                                        <span class="text-[#0F1111] font-medium">@price($cart->subtotal)</span>
                                    </div>

                                    @if($cart->discount > 0)
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-[#565959]">Discount</span>
                                            <span class="text-green-600 font-medium">-@price($cart->discount)</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-[#565959]">Shipping</span>
                                        <span class="text-green-600 font-semibold">FREE</span>
                                    </div>

                                    @if($cart->tax > 0)
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-[#565959]">Tax</span>
                                            <span class="text-[#0F1111] font-medium">@price($cart->tax)</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="border-t border-dashed border-[#E3E6E6] my-2"></div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-[#0F1111]">Total</span>
                                    <span class="text-sm font-bold text-[#CC0C39]">@price($cart->total)</span>
                                </div>

                                @if($cart->discount > 0)
                                    <p class="text-[10px] font-medium text-green-700 text-center mt-1.5 bg-green-50 rounded py-1">
                                        You save @price($cart->discount) on this order
                                    </p>
                                @endif
                            </div>

                            {{-- Place Order Button --}}
                            <div class="p-3 pt-0">
                                <button type="submit" :disabled="processing"
                                        class="block w-full py-2.5 bg-[#F8931D] hover:bg-[#E8850F] text-white text-xs font-bold text-center rounded transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wide">
                                    <span x-show="!processing">
                                        <template x-if="paymentMethod === 'razorpay'">
                                            <span>Pay Now &middot; @price($cart->total)</span>
                                        </template>
                                        <template x-if="paymentMethod === 'cod'">
                                            <span>Pay ₹100 & Place Order</span>
                                        </template>
                                    </span>
                                    <span x-show="processing" x-cloak class="flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Processing...
                                    </span>
                                </button>
                                <p x-show="error" x-text="error" class="text-[10px] text-[#CC0C39] text-center mt-1.5" x-cloak></p>
                                @if(!$isGuest && $addresses->isEmpty())
                                    <p class="text-[10px] text-[#CC0C39] text-center mt-1.5">Please add an address to place your order.</p>
                                @endif
                            </div>

                            {{-- Trust Badges --}}
                            <div class="px-3 pb-3">
                                <div class="flex items-center justify-center gap-3 pt-2 border-t border-[#E3E6E6]">
                                    <div class="flex items-center gap-1 text-[#565959]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span class="text-[9px] font-medium">Secure</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[#565959]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span class="text-[9px] font-medium">Genuine</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[#565959]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span class="text-[9px] font-medium">Easy Returns</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Terms --}}
                            <div class="px-3 pb-2.5">
                                <p class="text-[9px] text-[#565959] text-center leading-relaxed">
                                    By placing your order, you agree to our
                                    <a href="{{ route('terms') }}" class="text-[#007185] hover:text-[#C7511F]">Terms</a> &
                                    <a href="{{ route('privacy') }}" class="text-[#007185] hover:text-[#C7511F]">Privacy Policy</a>.
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
            // PIN code autocomplete using India Post API
            function pinLookup() {
                return {
                    pin: '',
                    city: '',
                    state: '',
                    pinError: '',
                    pinTimeout: null,

                    fetchPinData() {
                        this.pinError = '';
                        clearTimeout(this.pinTimeout);
                        if (this.pin.length !== 6) return;

                        this.pinTimeout = setTimeout(() => {
                            fetch('https://api.postalpincode.in/pincode/' + this.pin)
                                .then(r => r.json())
                                .then(data => {
                                    if (data[0] && data[0].Status === 'Success' && data[0].PostOffice && data[0].PostOffice.length) {
                                        const po = data[0].PostOffice[0];
                                        this.city = po.District || po.Division || '';
                                        this.state = po.State || '';
                                    } else {
                                        this.pinError = 'Invalid PIN code';
                                    }
                                })
                                .catch(() => {
                                    // Silently fail - user can fill manually
                                });
                        }, 300);
                    }
                };
            }

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
                        // Both payment methods go through Razorpay
                        // COD = partial pay (₹100 advance via Razorpay, rest on delivery)
                        // Razorpay = full payment
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

        {{-- GA4 begin_checkout --}}
        @if(config('services.ga4.measurement_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @php
                    $ga4CheckoutItems = $cart->items->map(function ($item) {
                        return [
                            'item_id' => $item->product->sku ?? (string) $item->product_id,
                            'item_name' => $item->product->name,
                            'price' => (float) $item->price,
                            'quantity' => $item->quantity,
                        ];
                    });
                @endphp
                var checkoutItems = {!! json_encode($ga4CheckoutItems, JSON_UNESCAPED_UNICODE) !!};
                gtag('event', 'begin_checkout', {
                    currency: 'INR',
                    value: {{ (float) $cart->total }},
                    items: checkoutItems
                });
            });
        </script>
        @endif
    </x-slot>
</x-layouts.app>
