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

        <?php
        $checkoutSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Checkout',
            'description' => 'Secure checkout at ' . config('app.name'),
            'url' => route('checkout.index'),
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Cart', 'item' => route('cart.index')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => 'Checkout'],
                ],
            ],
            'potentialAction' => [
                '@type' => 'OrderAction',
                'target' => route('checkout.process'),
            ],
        ];
        ?>
        <script type="application/ld+json">{!! json_encode($checkoutSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        {{-- Phone input styled inline - India only --}}
        <style>
            @media (min-width: 1024px) {
                #checkout-grid { display: flex !important; flex-direction: row !important; align-items: flex-start !important; gap: 16px !important; }
                #checkout-left { flex: 1 !important; min-width: 0 !important; }
                #checkout-right { width: 340px !important; flex-shrink: 0 !important; position: sticky !important; top: 16px !important; }
            }
            .iti { width: 100%; }
            .iti__tel-input { width: 100% !important; }
        </style>
    @endpush

    {{-- Facebook Pixel: InitiateCheckout --}}
    @if(!empty($fbEventId) && config('services.facebook.pixel_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof fbq !== 'undefined') {
                fbq('track', 'InitiateCheckout', {
                    content_ids: {!! json_encode($cart->items->pluck('product_id')->map('strval')->values()->toArray()) !!},
                    content_type: 'product',
                    value: {{ (float) ($cart->subtotal - $cart->discount) }},
                    currency: 'GBP',
                    num_items: {{ $cart->items->sum('quantity') }}
                }, {eventID: '{{ $fbEventId }}'});
            }
        });
    </script>
    @endif

    <div class="bg-[#f7f7f7] min-h-screen">
        <div class="container mx-auto px-3 py-3">
            <x-breadcrumb :items="[['label' => 'Cart', 'url' => route('cart.index')], ['label' => 'Checkout', 'url' => null]]" />
        </div>

        <div class="container mx-auto px-3 pb-8">
            {{-- Header with back + user info + logout --}}
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="text-xs text-[#B76E79] hover:text-[#c29958] font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Cart
                    </a>
                    <h1 class="text-base font-bold text-[#222222]">Checkout</h1>
                </div>
                @auth
                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#555555]">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-[#CC0C39] hover:underline font-medium">Logout</button>
                    </form>
                </div>
                @endauth
            </div>

            {{-- Express Checkout Banner (for returning users with saved preferences) --}}
            @if(!empty($oneClickReady) && $defaultAddress)
            <div class="bg-gradient-to-r from-[#B76E79] to-[#222222] rounded-lg p-3 mb-3 text-white" x-data="{ expressLoading: false }">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold">Express Checkout</p>
                        <p class="text-[10px] opacity-80 mt-0.5">Ship to {{ $defaultAddress->name }} — {{ $defaultAddress->city }}, {{ $defaultAddress->postal_code }}</p>
                    </div>
                    <form action="{{ route('checkout.process') }}" method="POST" @submit="expressLoading = true">
                        @csrf
                        <input type="hidden" name="shipping_address_id" value="{{ $defaultAddress->id }}">
                        <input type="hidden" name="same_billing_address" value="1">
                        <input type="hidden" name="payment_method" value="{{ $checkoutPreference->default_payment_method ?? 'cod' }}">
                        <input type="hidden" name="express_checkout" value="1">
                        <button type="submit" :disabled="expressLoading"
                                class="px-4 py-2 bg-[#B76E79] hover:bg-[#222222] text-white text-xs font-bold rounded-lg transition-colors whitespace-nowrap disabled:opacity-50">
                            <span x-show="!expressLoading">Place Order</span>
                            <span x-show="expressLoading">Processing...</span>
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @php
                $methodOrder = ['paypal' => 'paypal_enabled', 'stripe' => 'stripe_enabled'];
                $firstMethod = 'stripe';
                foreach ($methodOrder as $method => $key) {
                    if (($paymentSettings[$key] ?? '0') === '1') { $firstMethod = $method; break; }
                }
            @endphp

            <form action="{{ route('checkout.process') }}" method="POST"
                  x-data="checkoutForm('{{ $firstMethod }}')"
                  @submit.prevent="handleSubmit($event)">
                @csrf

                <div id="checkout-grid" class="space-y-4">
                    {{-- ═══ LEFT COLUMN ═══ --}}
                    <div id="checkout-left" class="space-y-3">

                        {{-- ── Section 1: Contact + Shipping (merged for guests, just shipping for auth) ── --}}
                        <div class="bg-white rounded border border-[#efefef]">
                            <div class="flex items-center gap-2 px-3 py-2.5 border-b border-[#efefef] bg-[#f7f7f7]">
                                <div class="w-5 h-5 rounded-full bg-[#B76E79] text-white text-[10px] font-bold flex items-center justify-center">1</div>
                                <h2 class="text-xs font-bold text-[#222222] uppercase tracking-wide">
                                    @if($isGuest) Contact & Shipping @else Shipping Address @endif
                                </h2>
                            </div>

                            <div class="p-3">
                                @if($isGuest)
                                {{-- Guest: contact + address in one compact block --}}
                                <p class="text-[11px] text-[#555555] mb-2">Have an account? <a href="{{ route('login') }}" class="text-[#B76E79] hover:text-[#c29958] font-medium">Log in</a> for faster checkout.</p>

                                <div class="space-y-2 mb-2">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Phone *</label>
                                        <input type="tel" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" required autocomplete="tel" autofocus
                                               class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="+44 7700 900000"
                                               @input="captureAbandoned(false)" @blur="captureAbandoned(true)">
                                        @error('guest_phone') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Name *</label>
                                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" required autocomplete="name"
                                                   class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="Full name"
                                                   @input="captureAbandoned(false)" @blur="captureAbandoned(true)">
                                            @error('guest_name') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Email *</label>
                                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" required autocomplete="email"
                                                   class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="email@example.com"
                                                   @input="captureAbandoned(false)" @blur="captureAbandoned(true)">
                                            @error('guest_email') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-dashed border-[#efefef] my-2"></div>

                                {{-- Shipping fields --}}
                                <div class="space-y-2">
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Recipient Name *</label>
                                            <input type="text" name="shipping_name" value="{{ old('shipping_name') }}" required autocomplete="shipping name"
                                                   class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="Recipient name">
                                            @error('shipping_name') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">City *</label>
                                            <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required autocomplete="address-level2"
                                                   class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="City">
                                            @error('shipping_city') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Post Code *</label>
                                            <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required maxlength="8" autocomplete="postal-code"
                                                   class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="SW1A 1AA">
                                            @error('shipping_postal_code') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Country *</label>
                                            <select name="shipping_country" required autocomplete="country"
                                                    class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none">
                                                <option value="GB" {{ old('shipping_country', 'GB') === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                            </select>
                                            @error('shipping_country') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Address *</label>
                                        <input type="text" name="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}" required autocomplete="address-line1"
                                               class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="House no., Building, Street">
                                        @error('shipping_address_line_1') <p class="text-[10px] text-[#CC0C39] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Address Line 2</label>
                                        <input type="text" name="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}" autocomplete="address-line2"
                                               class="w-full text-sm border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="Flat, Suite, Area (optional)">
                                    </div>
                                </div>
                                <input type="hidden" name="same_billing_address" value="1">
                                @else
                                {{-- Authenticated: saved addresses compact --}}
                                @if($addresses->count())
                                    <div class="space-y-2">
                                        @foreach($addresses as $address)
                                            <label class="flex items-start gap-2.5 p-2.5 border rounded cursor-pointer transition-colors
                                                {{ $address->id === $defaultAddress?->id ? 'border-[#B76E79] bg-[#B76E79]/5' : 'border-[#efefef] hover:border-[#B76E79]' }}">
                                                <input type="radio" name="shipping_address_id" value="{{ $address->id }}"
                                                       {{ $address->id === $defaultAddress?->id ? 'checked' : '' }}
                                                       class="mt-0.5 text-[#B76E79] focus:ring-[#B76E79]">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs font-semibold text-[#222222]">{{ $address->name }}</span>
                                                        @if($address->is_default)
                                                            <span class="text-[9px] font-medium text-[#B76E79] bg-[#B76E79]/10 px-1 py-px rounded">Default</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-[#555555] leading-relaxed">
                                                        {{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }},
                                                        {{ $address->city }}, {{ $address->postal_code }}
                                                        &middot; {{ $address->phone }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    <button type="button" @click="showAddressForm = !showAddressForm" class="inline-flex items-center gap-1 mt-2 text-[11px] font-medium text-[#B76E79] hover:text-[#c29958]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span x-text="showAddressForm ? 'Cancel' : 'Add New Address'"></span>
                                    </button>
                                @else
                                    <div class="text-center py-4" x-show="!showAddressForm">
                                        <p class="text-xs text-[#555555] mb-2">No saved addresses found.</p>
                                        <button type="button" @click="showAddressForm = true" class="inline-flex items-center gap-1 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#c29958]/90 px-3 py-1.5 rounded transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Add Address
                                        </button>
                                    </div>
                                @endif

                                {{-- Inline Add Address Form --}}
                                <div x-show="showAddressForm" x-collapse x-cloak class="mt-2 p-3 bg-[#f7f7f7] rounded border border-[#efefef] space-y-2">
                                    <h3 class="text-xs font-bold text-[#222222]">New Address</h3>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Full Name *</label>
                                            <input type="text" id="new_addr_name" class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="Full name">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Phone *</label>
                                            <input type="tel" id="new_addr_phone" class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="+44 7700 900000">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Address *</label>
                                        <input type="text" id="new_addr_line1" class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="House no., Building, Street">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Area / Landmark</label>
                                        <input type="text" id="new_addr_line2" class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="Optional">
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">City *</label>
                                            <input type="text" id="new_addr_city"
                                                   class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="City">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Post Code *</label>
                                            <input type="text" id="new_addr_postcode" maxlength="8"
                                                   class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none" placeholder="SW1A 1AA">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#555555] mb-0.5">Country *</label>
                                            <select id="new_addr_country" class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none">
                                                <option value="GB" selected>United Kingdom</option>
                                            </select>
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
                                                    let city = document.getElementById('new_addr_city').value.trim();
                                                    let postcode = document.getElementById('new_addr_postcode').value.trim();
                                                    let country = document.getElementById('new_addr_country').value;
                                                    let errEl = document.getElementById('new_addr_error');
                                                    if (!name || !phone || !line1 || !city || !postcode) {
                                                        errEl.textContent = 'Please fill all required fields.';
                                                        errEl.classList.remove('hidden');
                                                        return;
                                                    }
                                                    errEl.classList.add('hidden');
                                                    savingAddress = true;
                                                    fetch('{{ route('account.addresses.store') }}', {
                                                        method: 'POST',
                                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                                        body: JSON.stringify({ name, phone, address_line_1: line1, address_line_2: line2, city, postal_code: postcode, country })
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
                                                class="px-3 py-1.5 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#c29958]/90 rounded transition-colors disabled:opacity-50">
                                            <span x-show="!savingAddress">Save</span>
                                            <span x-show="savingAddress" class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                Saving...
                                            </span>
                                        </button>
                                        <button type="button" @click="showAddressForm = false" class="px-3 py-1.5 text-xs font-medium text-[#555555] border border-[#efefef] rounded hover:bg-[#f7f7f7] transition-colors">Cancel</button>
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
                        <div class="bg-white rounded border border-[#efefef]">
                            <div class="px-3 py-2.5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="same_billing_address" value="1" x-model="sameBilling"
                                           class="rounded border-[#efefef] text-[#B76E79] focus:ring-[#B76E79] w-3.5 h-3.5">
                                    <span class="text-xs text-[#222222] font-medium">Billing same as shipping</span>
                                </label>

                                <div x-show="!sameBilling" x-collapse class="mt-2 pt-2 border-t border-[#efefef]">
                                    @if($addresses->count())
                                        <div class="space-y-1.5">
                                            @foreach($addresses as $address)
                                                <label class="flex items-start gap-2 p-2 border border-[#efefef] rounded cursor-pointer hover:border-[#B76E79] transition-colors">
                                                    <input type="radio" name="billing_address_id" value="{{ $address->id }}"
                                                           class="mt-0.5 text-[#B76E79] focus:ring-[#B76E79]">
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-[11px] font-semibold text-[#222222]">{{ $address->name }}</span>
                                                        <p class="text-[10px] text-[#555555]">{{ $address->address_line_1 }}, {{ $address->city }}, {{ $address->postal_code }}</p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ── Section 3: Payment Method ── --}}
                        <div class="bg-white rounded border border-[#efefef]">
                            <div class="flex items-center gap-2 px-3 py-2.5 border-b border-[#efefef] bg-[#f7f7f7]">
                                <div class="w-5 h-5 rounded-full bg-[#B76E79] text-white text-[10px] font-bold flex items-center justify-center">2</div>
                                <h2 class="text-xs font-bold text-[#222222] uppercase tracking-wide">Payment</h2>
                            </div>
                            <div class="p-3 space-y-2">
                                {{-- PayPal --}}
                                @if(($paymentSettings['paypal_enabled'] ?? '0') === '1')
                                <div @click="paymentMethod = 'paypal'"
                                     :class="paymentMethod === 'paypal' ? 'border-[#202a40] bg-[#202a40]/5 ring-1 ring-[#202a40]/20' : 'border-[#efefef] hover:border-[#202a40]'"
                                     class="border rounded cursor-pointer transition-all">
                                    <div class="flex items-center gap-2.5 p-2.5">
                                        <input type="radio" name="payment_method" value="paypal" x-model="paymentMethod"
                                               class="text-[#202a40] focus:ring-[#202a40]">
                                        <div class="flex items-center gap-2 flex-1">
                                            <div class="w-7 h-7 rounded flex items-center justify-center shrink-0 bg-[#003087]/10">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944 3.72a.773.773 0 01.763-.658h6.263c2.075 0 3.753.468 4.796 1.524.973.986 1.337 2.406 1.064 4.169-.018.11-.04.224-.063.34-.687 3.537-3.05 5.34-6.68 5.34H9.043a.775.775 0 00-.764.659l-.816 5.162-.387 2.445a.412.412 0 01-.407.346z" fill="#003087"/>
                                                    <path d="M20.603 8.108c-.028.165-.06.333-.095.507-.744 3.828-3.29 5.143-6.547 5.143h-1.656a.803.803 0 00-.793.68l-.848 5.373-.24 1.524a.423.423 0 00.418.49h2.934a.705.705 0 00.695-.594l.029-.148.55-3.49.035-.192a.705.705 0 01.696-.594h.438c2.836 0 5.055-1.152 5.704-4.486.271-1.392.13-2.555-.585-3.372a2.8 2.8 0 00-.735-.54z" fill="#0070E0"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="text-xs font-medium text-[#222222]">PayPal</span>
                                                <p class="text-[10px] text-[#555555]">Pay securely with your PayPal account</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Stripe (Card Payment) --}}
                                @if(($paymentSettings['stripe_enabled'] ?? '0') === '1')
                                <div @click="paymentMethod = 'stripe'"
                                     :class="paymentMethod === 'stripe' ? 'border-[#202a40] bg-[#202a40]/5 ring-1 ring-[#202a40]/20' : 'border-[#efefef] hover:border-[#202a40]'"
                                     class="border rounded cursor-pointer transition-all">
                                    <div class="flex items-center gap-2.5 p-2.5">
                                        <input type="radio" name="payment_method" value="stripe" x-model="paymentMethod"
                                               class="text-[#202a40] focus:ring-[#202a40]">
                                        <div class="flex items-center gap-2 flex-1">
                                            <div class="w-7 h-7 rounded flex items-center justify-center shrink-0 bg-[#635BFF]/10">
                                                <span class="text-[#635BFF] font-bold text-sm">S</span>
                                            </div>
                                            <div>
                                                <span class="text-xs font-medium text-[#222222]">Card Payment</span>
                                                <p class="text-[10px] text-[#555555]">Pay securely with credit or debit card</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <img src="{{ asset('images/visa.svg') }}" alt="Visa" class="h-5 w-auto" onerror="this.style.display='none'">
                                            <img src="{{ asset('images/mastercard.svg') }}" alt="Mastercard" class="h-5 w-auto" onerror="this.style.display='none'">
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @error('payment_method')
                                    <p class="text-[10px] text-[#CC0C39]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Navratri Offer Banner --}}
                        @if(isset($navratriActive) && $navratriActive)
                        <div class="rounded p-2.5 flex items-center gap-2" style="background: linear-gradient(135deg, #FFF3E0, #FFE0B2); border: 1px solid #FFB74D;">
                            <span class="text-lg">🎉</span>
                            <div>
                                <p class="text-[11px] font-bold" style="color: #E65100;">Navratri Special: Extra 5% Off Applied Automatically!</p>
                            </div>
                        </div>
                        @endif

                        {{-- Order Notes (compact) --}}
                        <div class="bg-white rounded border border-[#efefef]">
                            <div class="p-3">
                                <label class="block text-[10px] font-semibold text-[#555555] mb-1">Order Notes (optional)</label>
                                <textarea name="notes" rows="2" class="w-full text-xs border border-[#efefef] rounded px-2.5 py-2 focus:border-[#B76E79] focus:outline-none resize-none"
                                          placeholder="Special delivery instructions...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ RIGHT COLUMN - Order Summary ═══ --}}
                    <div id="checkout-right">
                        {{-- Free Shipping Nudge --}}
                        @php
                            $freeShipThreshold = (float) \App\Models\Setting::get('free_shipping_threshold', 399);
                            $cartSubtotal = (float) $cart->subtotal;
                            $shippingRemaining = max(0, $freeShipThreshold - $cartSubtotal);
                        @endphp
                        @if($shippingRemaining > 0)
                            <div class="bg-[#B76E79]/10 border border-[#B76E79]/20 rounded-md px-3.5 py-2.5 mb-3">
                                <p class="text-xs text-[#B76E79] font-semibold mb-1.5">Add @price($shippingRemaining) more for FREE shipping!</p>
                                <div class="bg-[#B76E79]/15 rounded h-1.5 overflow-hidden">
                                    <div class="bg-[#B76E79] h-full rounded" style="width:{{ min(100, ($cartSubtotal / $freeShipThreshold) * 100) }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="bg-[#B76E79]/5 rounded-md px-3.5 py-2 mb-3">
                                <p class="text-xs text-[#B76E79] font-semibold">&#10003; You qualify for FREE shipping!</p>
                            </div>
                        @endif

                        <div class="bg-white rounded border border-[#efefef] lg:sticky lg:top-20">

                            {{-- Coupons Carousel (horizontal scroll) --}}
                            @if($availableCoupons->count())
                            <div class="p-3 border-b border-[#efefef]" x-data="{ applying: false }">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <svg class="w-3.5 h-3.5 text-[#c29958]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span class="text-[10px] font-bold text-[#222222] uppercase tracking-wider">Coupons</span>
                                </div>
                                <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-thin">
                                    @foreach($availableCoupons as $coupon)
                                        <div class="flex-shrink-0 w-44 border border-dashed {{ $cart->coupon_id === $coupon->id ? 'border-[#B76E79] bg-[#B76E79]/5' : 'border-[#efefef]' }} rounded p-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[10px] font-bold text-[#B76E79] bg-[#B76E79]/10 px-1.5 py-0.5 rounded font-mono">{{ $coupon->code }}</span>
                                                @if($cart->coupon_id === $coupon->id)
                                                    <span class="text-[9px] font-medium text-[#B76E79] bg-[#B76E79]/5 px-1 py-px rounded">Applied</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-[#555555] leading-snug mb-1.5 line-clamp-2">{{ $coupon->name }}</p>
                                            @if($cart->coupon_id !== $coupon->id)
                                                <button type="button" :disabled="applying"
                                                        @click="
                                                            applying = true;
                                                            fetch('{{ route('cart.apply-coupon') }}', {
                                                                method: 'POST',
                                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                                                body: JSON.stringify({ code: '{{ $coupon->code }}' })
                                                            }).then(r => {
                                                                if (r.ok) { location.reload(); }
                                                                else { return r.json().then(d => { alert(d.error || 'Could not apply coupon'); applying = false; }); }
                                                            }).catch(() => { alert('Something went wrong'); applying = false; })
                                                        "
                                                        class="w-full text-[10px] font-semibold text-[#B76E79] hover:text-white border border-[#B76E79] hover:bg-[#B76E79] rounded py-1 transition-colors">
                                                    Apply
                                                </button>
                                            @else
                                                <button type="button"
                                                        @click="fetch('{{ route('cart.remove-coupon') }}', {
                                                            method: 'DELETE',
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
                                <div class="px-3 py-2 border-b border-[#efefef] bg-[#B76E79]/5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold text-[#B76E79] bg-[#B76E79]/10 px-1.5 py-0.5 rounded font-mono">{{ $cart->coupon->code }}</span>
                                            @if($cart->coupon->auto_apply)
                                                <span class="text-[9px] text-[#B76E79] bg-[#B76E79]/10 px-1 py-px rounded font-medium">Auto</span>
                                            @endif
                                            <span class="text-[10px] text-[#c29958] font-medium">
                                                @if($cart->coupon->type === 'percentage')
                                                    {{ intval($cart->coupon->value) }}% off
                                                @elseif($cart->coupon->type === 'fixed')
                                                    @price($cart->coupon->value) off
                                                @elseif($cart->coupon->type === 'buy_x_get_y')
                                                    Buy {{ $cart->coupon->conditions['buy_qty'] ?? 0 }} Get {{ $cart->coupon->conditions['get_qty'] ?? 0 }}{{ $cart->coupon->value >= 100 ? ' Free' : '' }}
                                                @endif
                                            </span>
                                        </div>
                                        <span class="text-xs font-bold text-[#c29958]">-@price($cart->discount)</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Order Items --}}
                            <div class="p-3 border-b border-[#efefef]">
                                <h3 class="text-[10px] font-bold text-[#555555] uppercase tracking-wider mb-2">
                                    {{ $cart->items->sum('quantity') }} {{ $cart->items->sum('quantity') === 1 ? 'Item' : 'Items' }}
                                </h3>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach($cart->items as $item)
                                        <div class="flex gap-2">
                                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}"
                                                 class="w-10 h-10 rounded border border-[#efefef] bg-white object-contain shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[11px] font-medium text-[#222222] line-clamp-1">{{ $item->product->name }}</p>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[10px] text-[#555555] flex items-center gap-1">
                                                        <button type="button" class="w-5 h-5 rounded bg-neutral-100 hover:bg-neutral-200 text-xs flex items-center justify-center border" onclick="fetch('/cart/{{ $item->id }}',{method:'PUT',headers:{'Content-Type':'application/json','X-XSRF-TOKEN':decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]||''),'Accept':'application/json'},body:JSON.stringify({quantity:{{ max(1,$item->quantity-1) }}})}).then(()=>location.reload())">-</button>
                                                        <span class="font-semibold text-[#222222] px-1">{{ $item->quantity }}</span>
                                                        <button type="button" class="w-5 h-5 rounded bg-neutral-100 hover:bg-neutral-200 text-xs flex items-center justify-center border" onclick="fetch('/cart/{{ $item->id }}',{method:'PUT',headers:{'Content-Type':'application/json','X-XSRF-TOKEN':decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]||''),'Accept':'application/json'},body:JSON.stringify({quantity:{{ $item->quantity+1 }}})}).then(()=>location.reload())">+</button>
                                                        <button type="button" class="ml-1 text-[9px] text-[#CC0C39] hover:underline" onclick="if(confirm('Remove this item?'))fetch('/cart/{{ $item->id }}',{method:'DELETE',headers:{'X-XSRF-TOKEN':decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]||''),'Accept':'application/json'}}).then(()=>location.reload())">Remove</button>
                                                    </span>
                                                    <span class="text-[11px] font-semibold text-[#222222]">@price($item->price * $item->quantity)</span>
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
                                        <span class="text-[#555555]">Subtotal</span>
                                        <span class="text-[#222222] font-medium">@price($cart->subtotal)</span>
                                    </div>

                                    @if($cart->discount > 0)
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-[#555555]">Coupon Discount</span>
                                            <span class="text-[#c29958] font-medium">-@price($cart->discount)</span>
                                        </div>
                                    @endif

                                    @if(isset($navratriActive) && $navratriActive)
                                        @php $navratriSaving = round(($cart->subtotal - $cart->discount) * 0.05, 2); @endphp
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-[#CC0C39] font-semibold">Navratri 5% Extra Off</span>
                                            <span class="text-[#CC0C39] font-semibold">-@price($navratriSaving)</span>
                                        </div>
                                    @endif

                                    @php
                                        $afterCoupon = $cart->subtotal - $cart->discount;
                                        $shipFee = $afterCoupon >= $freeShipThreshold ? 0 : 50;
                                    @endphp
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-[#555555]">Shipping</span>
                                        @if($shipFee > 0)
                                            <span class="text-[#222222] font-medium">@price($shipFee)</span>
                                        @else
                                            <span class="text-[#B76E79] font-semibold">FREE</span>
                                        @endif
                                    </div>
                                    @if($shipFee > 0)
                                        <p class="text-[9px] text-[#555555]">Free shipping on orders above @price($freeShipThreshold)</p>
                                    @endif

                                    {{-- Loyalty Points Redemption --}}
                                    @if(!empty($loyaltyPoints) && $loyaltyPoints > 0)
                                        <div class="flex items-center justify-between text-[11px] pt-1 border-t border-dashed border-[#efefef]"
                                             x-data="{ usePoints: false, pointsToUse: {{ min($loyaltyPoints, (int) ceil(($cart->subtotal - $cart->discount) / 0.25)) }} }">
                                            <div class="flex items-center gap-1.5">
                                                <input type="checkbox" name="use_loyalty_points" value="1" x-model="usePoints"
                                                       class="w-3 h-3 rounded border-neutral-300 text-[#c29958] focus:ring-[#c29958]">
                                                <span class="text-[#c29958] font-medium">Use {{ number_format($loyaltyPoints) }} points</span>
                                                <span class="text-[9px] text-[#555555]">(worth @price($loyaltyValue))</span>
                                            </div>
                                            <template x-if="usePoints">
                                                <span class="text-[#c29958] font-semibold">-@price($loyaltyValue)</span>
                                            </template>
                                            <input type="hidden" name="loyalty_points_used" :value="usePoints ? {{ $loyaltyPoints }} : 0">
                                        </div>
                                    @endif

                                    @if($cart->tax > 0)
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-[#555555]">GST (incl.)</span>
                                            <span class="text-[#555555] font-medium text-[10px]">incl. @price($cart->tax)</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="border-t border-dashed border-[#efefef] my-2"></div>

                                @php
                                    $displayTotal = $cart->total + $shipFee;
                                    $totalSavings = $cart->discount;
                                    if (isset($navratriActive) && $navratriActive) {
                                        $navSave = round(($cart->subtotal - $cart->discount) * 0.05, 2);
                                        $displayTotal = max(0, $displayTotal - $navSave);
                                        $totalSavings += $navSave;
                                    }
                                    $codMinOrder = 199;
                                    $showCod = $displayTotal >= $codMinOrder;
                                @endphp

                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-[#222222]">Total</span>
                                    <span class="text-sm font-bold text-[#CC0C39]">@price($displayTotal)</span>
                                </div>
                                <p class="text-[9px] text-[#555555] text-center mt-0.5">Inclusive of all taxes</p>

                                @if($totalSavings > 0)
                                    <p class="text-[10px] font-medium text-[#c29958] text-center mt-1.5 bg-[#c29958]/10 rounded py-1">
                                        You save @price($totalSavings) on this order
                                    </p>
                                @endif

                                @if(isset($navratriActive) && $navratriActive)
                                    <div class="mt-2 p-2 rounded-lg text-center" style="background: linear-gradient(135deg, #FF6B35, #FF9F1C);">
                                        <p class="text-[10px] font-bold text-white">Navratri Special - Extra 5% Off Applied!</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Place Order Button --}}
                            <div class="p-3 pt-0">
                                <button type="submit" :disabled="processing"
                                        class="block w-full py-2.5 bg-[#B76E79] hover:bg-[#a25d67] text-white text-xs font-bold text-center rounded transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wide">
                                    <span x-show="!processing"
                                          x-text="paymentMethod === 'paypal' ? 'Pay with PayPal · £{{ number_format($displayTotal, 2) }}' : 'Pay with Card · £{{ number_format($displayTotal, 2) }}'">
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
                                <div class="flex items-center justify-center gap-3 pt-2 border-t border-[#efefef]">
                                    <div class="flex items-center gap-1 text-[#555555]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span class="text-[9px] font-medium">Secure</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[#555555]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span class="text-[9px] font-medium">Genuine</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[#555555]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span class="text-[9px] font-medium">Easy Returns</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Terms --}}
                            <div class="px-3 pb-2.5">
                                <p class="text-[9px] text-[#555555] text-center leading-relaxed">
                                    By placing your order, you agree to our
                                    <a href="{{ route('terms') }}" class="text-[#B76E79] hover:text-[#c29958]">Terms</a> &
                                    <a href="{{ route('privacy') }}" class="text-[#B76E79] hover:text-[#c29958]">Privacy Policy</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-slot name="scripts">
        {{-- Checkout JS --}}
        <script>
            // Address helper (UK)
            function pinLookup() {
                return {
                    pin: '',
                    city: '',
                    state: '',
                    pinError: '',
                    pinServiceable: null,
                    pinTimeout: null,
                    fetchPinData() {}
                };
            }

            // Capture guest contact info for abandoned checkout recovery
            // Fires on both @input (debounced 2s) and @blur (immediate) for early capture
            let abandonedCaptureTimeout = null;
            let lastCapturedData = '';
            function captureAbandoned(immediate) {
                clearTimeout(abandonedCaptureTimeout);
                const delay = immediate ? 0 : 2000;
                abandonedCaptureTimeout = setTimeout(() => {
                    const phone = (document.querySelector('[name="guest_phone"]')?.value || '').replace(/\D/g, '');
                    const email = document.querySelector('[name="guest_email"]')?.value || '';
                    const name = document.querySelector('[name="guest_name"]')?.value || '';

                    // Need at least a phone (10+ digits) or email to capture
                    if (phone.length < 10 && !email.includes('@')) return;

                    // Don't re-send identical data
                    const dataKey = phone + '|' + email + '|' + name;
                    if (dataKey === lastCapturedData) return;
                    lastCapturedData = dataKey;

                    fetch('{{ route("checkout.abandoned.capture") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ phone, email, name }),
                    }).catch(() => {});
                }, delay);
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
                        if (this.paymentMethod === 'paypal') {
                            this.initiatePayPal(e.target);
                        } else if (this.paymentMethod === 'stripe') {
                            this.initiateStripe(e.target);
                        }
                    },

                    async initiatePayPal(form) {
                        this.processing = true;
                        const formData = new FormData(form);
                        const data = Object.fromEntries(formData.entries());
                        data.payment_method = 'paypal';

                        try {
                            const response = await fetch('{{ route("checkout.paypal.create") }}', {
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

                            if (result.approval_url) {
                                window.location.href = result.approval_url;
                            } else {
                                this.error = 'Could not initiate PayPal payment.';
                                this.processing = false;
                            }
                        } catch (err) {
                            this.error = 'Network error. Please check your connection and try again.';
                            this.processing = false;
                        }
                    },

                    async initiateStripe(form) {
                        this.processing = true;
                        const formData = new FormData(form);
                        const data = Object.fromEntries(formData.entries());
                        data.payment_method = 'stripe';

                        try {
                            const response = await fetch('{{ route("checkout.stripe.create") }}', {
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

                            if (result.checkout_url) {
                                window.location.href = result.checkout_url;
                            } else {
                                this.error = 'Could not initiate card payment.';
                                this.processing = false;
                            }
                        } catch (err) {
                            this.error = 'Network error. Please check your connection and try again.';
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
                    currency: 'GBP',
                    value: {{ (float) $cart->total }},
                    items: checkoutItems
                });
            });
        </script>
        @endif
    </x-slot>
</x-layouts.app>
