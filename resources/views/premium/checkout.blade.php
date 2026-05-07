@extends('premium.layout')

@section('namespace', 'checkout')

@section('title', 'Checkout — Trendymus')

@push('meta')
    <meta name="description" content="Complete your purchase securely at Trendymus.">
    <meta name="robots" content="noindex,nofollow">
@endpush

@section('content')

<div
    class="min-h-screen bg-[#FAFAF8]"
    x-data="{
        step: 1,
        maxStep: 4,
        ordered: false,
        orderNumber: '',

        /* Contact */
        email: '',
        phone: '',

        /* Shipping */
        fullName: '',
        address1: '',
        address2: '',
        city: '',
        state: '',
        pincode: '',
        delivery: 'standard',

        /* Payment */
        paymentMethod: 'paypal',
        cardNumber: '',
        cardExpiry: '',
        cardCvv: '',
        cardName: '',
        paypalEmail: '',
        paypalOption: '',

        /* Helpers */
        get shippingCost() { return this.delivery === 'express' ? 149 : 0; },
        get subtotal() { return 61997; },
        get total() { return this.subtotal + this.shippingCost; },
        formatPrice(p) { return '£' + Number(p).toFixed(2); },

        goTo(s) {
            const prev = this.step;
            this.step = s;
            this.$nextTick(() => {
                if (typeof gsap === 'undefined') return;
                const panels = document.querySelectorAll('[data-gsap=\"step-panel\"]');
                panels.forEach(panel => {
                    if (panel.style.display !== 'none') {
                        gsap.fromTo(panel,
                            { x: s > prev ? 30 : -30, opacity: 0 },
                            { x: 0, opacity: 1, duration: 0.35, ease: 'power2.out' }
                        );
                    }
                });
            });
        },

        nextStep() {
            if (this.step === 1 && !this.email.trim()) {
                alert('Please enter a valid email address.');
                return;
            }
            if (this.step < this.maxStep) this.goTo(this.step + 1);
        },

        prevStep() {
            if (this.step > 1) this.goTo(this.step - 1);
        },

        placeOrder() {
            this.orderNumber = 'MCO-' + Math.floor(100000 + Math.random() * 900000);
            this.ordered = true;
            this.$nextTick(() => {
                if (typeof gsap !== 'undefined') {
                    gsap.fromTo('#order-success-overlay',
                        { opacity: 0, scale: 0.95 },
                        { opacity: 1, scale: 1, duration: 0.5, ease: 'back.out(1.7)' }
                    );
                    gsap.fromTo('#success-checkmark',
                        { scale: 0, opacity: 0 },
                        { scale: 1, opacity: 1, duration: 0.6, delay: 0.2, ease: 'back.out(2)' }
                    );
                }
            });
        },

        orderSummaryItems: [
            { name: '18K Rose Gold Solitaire Ring', variant: 'Size M', price: 24999, qty: 1, img: 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=80' },
            { name: 'Diamond Pendant Necklace', variant: '18 inch chain', price: 18499, qty: 2, img: 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=80' },
        ]
    }"
>

    {{-- ═══════════════════════════════════
         STICKY PROGRESS BAR
    ════════════════════════════════════ --}}
    <div class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-[#111111]/8 shadow-sm">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between py-4">

                {{-- Logo --}}
                <a href="/" class="font-serif text-xl italic text-[#202a40] shrink-0 mr-8 hidden sm:block" style="font-family:'Playfair Display',serif;">Trendymus</a>

                {{-- Steps --}}
                <div class="flex items-center gap-0 flex-1 max-w-lg mx-auto">

                    @foreach([['Contact', 1], ['Shipping', 2], ['Payment', 3], ['Review', 4]] as [$label, $num])
                    <div class="flex items-center flex-1 @if($num === 4) flex-none @endif">

                        {{-- Step circle --}}
                        <button
                            @click="if ({{ $num }} < step || {{ $num }} <= step) goTo({{ $num }})"
                            class="flex flex-col items-center gap-1 group relative"
                            :disabled="{{ $num }} > step"
                        >
                            <div
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-300 relative z-10"
                                :class="{
                                    'bg-[#202a40] border-[#202a40] text-white shadow-md': step === {{ $num }},
                                    'bg-[#202a40] border-[#202a40] text-white': step > {{ $num }},
                                    'bg-white border-[#111111]/20 text-[#111111]/35': step < {{ $num }}
                                }"
                            >
                                <span x-show="step <= {{ $num }}">{{ $num }}</span>
                                <svg x-show="step > {{ $num }}" class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8l3.5 3.5L13 4"/></svg>
                            </div>
                            <span
                                class="text-[10px] sm:text-xs font-medium transition-colors duration-200 hidden sm:block absolute -bottom-5 whitespace-nowrap"
                                :class="step >= {{ $num }} ? 'text-[#202a40]' : 'text-[#111111]/30'"
                            >{{ $label }}</span>
                        </button>

                        {{-- Connector line (not after last step) --}}
                        @if($num < 4)
                        <div class="flex-1 h-0.5 mx-2 rounded-full overflow-hidden bg-[#111111]/10">
                            <div
                                class="h-full bg-[#202a40] transition-all duration-500 ease-out"
                                :style="'width:' + (step > {{ $num }} ? '100%' : '0%')"
                            ></div>
                        </div>
                        @endif

                    </div>
                    @endforeach

                </div>

                <div class="w-16 hidden sm:block shrink-0"></div>{{-- spacer --}}
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════ --}}
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-10 py-8 lg:py-12 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12">

            {{-- ── LEFT: Form Steps ── --}}
            <div class="lg:col-span-7">

                {{-- ────────────────────────────────
                     STEP 1: CONTACT
                ──────────────────────────────── --}}
                <div x-show="step===1" x-cloak data-gsap="step-panel">
                    <div class="bg-white border border-[#111111]/8 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-6 sm:px-8 pt-7 pb-5 border-b border-[#111111]/6">
                            <h2 class="font-serif text-xl sm:text-2xl text-[#111111]" style="font-family:'Playfair Display',serif;">Contact Information</h2>
                            <p class="text-sm text-[#111111]/45 mt-1">We'll only contact you about your order.</p>
                        </div>
                        <div class="px-6 sm:px-8 py-7 space-y-5">

                            {{-- Email --}}
                            <div class="relative" data-float-label>
                                <input
                                    type="email"
                                    id="email"
                                    x-model="email"
                                    placeholder=" "
                                    class="peer w-full px-4 pt-5 pb-2.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all"
                                    autocomplete="email"
                                >
                                <label for="email" class="absolute left-4 top-3.5 text-xs font-medium text-[#111111]/40 transition-all duration-200 pointer-events-none peer-placeholder-shown:text-sm peer-placeholder-shown:top-4 peer-focus:text-xs peer-focus:top-3.5 peer-focus:text-[#202a40]">
                                    Email Address <span class="text-red-400">*</span>
                                </label>
                            </div>

                            {{-- Phone --}}
                            <div class="relative" data-float-label>
                                <div class="flex">
                                    <div class="flex items-center px-3.5 border border-r-0 border-[#111111]/15 rounded-l-xl bg-[#F3EEE9] text-sm text-[#111111]/60 font-medium shrink-0">
                                        🇬🇧 +44
                                    </div>
                                    <input
                                        type="tel"
                                        id="phone"
                                        x-model="phone"
                                        placeholder="Mobile number"
                                        maxlength="11"
                                        class="flex-1 px-4 py-3.5 text-sm border border-[#111111]/15 rounded-r-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all"
                                        autocomplete="tel"
                                    >
                                </div>
                            </div>

                            {{-- Login prompt --}}
                            <div class="flex items-center gap-3 py-4 px-4 bg-[#F3EEE9]/60 rounded-xl border border-[#202a40]/10">
                                <svg class="w-5 h-5 text-[#202a40]/60 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="10" cy="7" r="3"/><path d="M3 18a7 7 0 0114 0"/>
                                </svg>
                                <p class="text-sm text-[#111111]/60">
                                    Already have an account?
                                    <a href="/login" class="text-[#202a40] font-semibold hover:underline ml-1">Log in</a>
                                    <span class="text-[#111111]/35 mx-1.5">·</span>
                                    <a href="/register" class="text-[#C9A96E] font-medium hover:underline">Create account</a>
                                </p>
                            </div>

                            <button
                                @click="nextStep()"
                                class="group w-full flex items-center justify-center gap-2 py-4 bg-[#202a40] text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                            >
                                Continue to Shipping
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                            </button>

                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────
                     STEP 2: SHIPPING
                ──────────────────────────────── --}}
                <div x-show="step===2" x-cloak data-gsap="step-panel">
                    <div class="bg-white border border-[#111111]/8 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-6 sm:px-8 pt-7 pb-5 border-b border-[#111111]/6">
                            <h2 class="font-serif text-xl sm:text-2xl text-[#111111]" style="font-family:'Playfair Display',serif;">Shipping Address</h2>
                        </div>
                        <div class="px-6 sm:px-8 py-7 space-y-4">

                            {{-- Full Name --}}
                            <div class="relative">
                                <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-1.5">Full Name <span class="text-red-400">*</span></label>
                                <input
                                    type="text"
                                    x-model="fullName"
                                    placeholder="As on government ID"
                                    class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all placeholder-[#111111]/25"
                                    autocomplete="name"
                                >
                            </div>

                            {{-- Address Line 1 --}}
                            <div>
                                <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-1.5">Address Line 1 <span class="text-red-400">*</span></label>
                                <input
                                    type="text"
                                    x-model="address1"
                                    placeholder="House / Flat no., Street name"
                                    class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all placeholder-[#111111]/25"
                                    autocomplete="address-line1"
                                >
                            </div>

                            {{-- Address Line 2 --}}
                            <div>
                                <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-1.5">Address Line 2 <span class="text-[#111111]/30 font-normal">(optional)</span></label>
                                <input
                                    type="text"
                                    x-model="address2"
                                    placeholder="Apartment, building, landmark"
                                    class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all placeholder-[#111111]/25"
                                    autocomplete="address-line2"
                                >
                            </div>

                            {{-- City + State --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-1.5">City <span class="text-red-400">*</span></label>
                                    <input
                                        type="text"
                                        x-model="city"
                                        placeholder="Mumbai"
                                        class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all placeholder-[#111111]/25"
                                        autocomplete="address-level2"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-1.5">State <span class="text-red-400">*</span></label>
                                    <div x-data="{
                                        open: false,
                                        states: ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu & Kashmir','Ladakh']
                                    }" class="relative">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center justify-between px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all"
                                                :class="$root.state ? 'text-[#111111]' : 'text-[#111111]/40'">
                                            <span x-text="$root.state || 'Select county'"></span>
                                            <svg class="w-4 h-4 text-[#111111]/40 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" x-cloak
                                             class="absolute left-0 right-0 z-50 mt-1 bg-white border border-[#111111]/10 rounded-xl shadow-xl overflow-y-auto"
                                             style="max-height:240px">
                                            <template x-for="st in states" :key="st">
                                                <button type="button" @click="$root.state = st; open = false"
                                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors"
                                                        :class="$root.state === st ? 'bg-[#202a40]/5 text-[#202a40] font-semibold' : 'text-[#111111]/80 hover:bg-[#111111]/5'">
                                                    <span x-text="st"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Postcode --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-1.5"></label>
                                    <input
                                        type="text"
                                        x-model="pincode"
                                        placeholder="SW1A 1AA"
                                        maxlength="8"
                                        class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all placeholder-[#111111]/25"
                                        autocomplete="postal-code"
                                    >
                                </div>
                                <div class="flex items-end pb-0.5">
                                    <p class="text-[11px] text-[#111111]/35 leading-relaxed">
                                        optional
                                    </p>
                                </div>
                            </div>

                            {{-- Delivery method --}}
                            <div class="pt-2">
                                <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-3">Delivery Method</label>
                                <div class="space-y-3">

                                    {{-- Standard --}}
                                    <label
                                        class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                                        :class="delivery === 'standard' ? 'border-[#202a40] bg-[#202a40]/5' : 'border-[#111111]/10 hover:border-[#202a40]/40'"
                                    >
                                        <input type="radio" name="delivery" value="standard" x-model="delivery" class="sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                                            :class="delivery === 'standard' ? 'border-[#202a40]' : 'border-[#111111]/20'"
                                        >
                                            <div class="w-2.5 h-2.5 rounded-full bg-[#202a40] transition-transform duration-200" :class="delivery === 'standard' ? 'scale-100' : 'scale-0'"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold text-[#111111]">Standard Delivery</span>
                                                <span class="text-sm font-bold text-emerald-600">Free</span>
                                            </div>
                                            <p class="text-xs text-[#111111]/45 mt-0.5">Estimated 5–7 business days</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-full bg-[#F3EEE9] flex items-center justify-center">
                                            <svg class="w-4 h-4 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="5" width="14" height="8" rx="1"/><path d="M1 8h14M5 5V3a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                        </div>
                                    </label>

                                    {{-- Express --}}
                                    <label
                                        class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                                        x-show="false" :class="delivery === 'express' ? 'border-[#C9A96E] bg-[#C9A96E]/5' : 'border-[#111111]/10 hover:border-[#C9A96E]/40'"
                                    >
                                        <input type="radio" name="delivery" value="express" x-model="delivery" class="sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                                            :class="delivery === 'express' ? 'border-[#C9A96E]' : 'border-[#111111]/20'"
                                        >
                                            <div class="w-2.5 h-2.5 rounded-full bg-[#C9A96E] transition-transform duration-200" :class="delivery === 'express' ? 'scale-100' : 'scale-0'"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold text-[#111111]">Express Delivery</span>
                                                <span class="text-sm font-bold text-[#111111]">£1.50</span>
                                            </div>
                                            <p class="text-xs text-[#111111]/45 mt-0.5">Estimated 2–3 business days</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-full bg-[#FDF5E6] flex items-center justify-center">
                                            <svg class="w-4 h-4 text-[#C9A96E]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 10l2-5h8l2 5"/><path d="M1 10h14M3 13a1 1 0 100-2 1 1 0 000 2zM13 13a1 1 0 100-2 1 1 0 000 2z"/></svg>
                                        </div>
                                    </label>

                                </div>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button
                                    @click="prevStep()"
                                    class="px-6 py-4 border border-[#111111]/15 text-[#111111]/60 text-sm font-medium rounded-xl hover:bg-[#111111]/5 transition-colors"
                                >
                                    ← Back
                                </button>
                                <button
                                    @click="nextStep()"
                                    class="group flex-1 flex items-center justify-center gap-2 py-4 bg-[#202a40] text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg"
                                >
                                    Continue to Payment
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────
                     STEP 3: PAYMENT
                ──────────────────────────────── --}}
                <div x-show="step===3" x-cloak data-gsap="step-panel">
                    <div class="bg-white border border-[#111111]/8 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-6 sm:px-8 pt-7 pb-5 border-b border-[#111111]/6">
                            <h2 class="font-serif text-xl sm:text-2xl text-[#111111]" style="font-family:'Playfair Display',serif;">Payment Method</h2>
                            <div class="flex items-center gap-1.5 mt-1.5">
                                <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="2" y="3" width="12" height="10" rx="1.5"/><path d="M2 7h12"/></svg>
                                <p class="text-xs text-[#111111]/45">256-bit SSL encryption. Your payment info is secure.</p>
                            </div>
                        </div>
                        <div class="px-6 sm:px-8 py-7 space-y-4">

                            {{-- Payment method selector --}}
                            <div class="grid grid-cols-2 gap-3">
                                @foreach([['card', 'Credit / Debit Card', 'M2 3h12a1 1 0 011 1v9a1 1 0 01-1 1H2a1 1 0 01-1-1V4a1 1 0 011-1zM1 7h14'], ['paypal', 'PayPal', 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2']] as [$method, $label, $icon])
                                <label
                                    class="flex flex-col items-center gap-2 p-3.5 border-2 rounded-xl cursor-pointer transition-all duration-200 text-center"
                                    :class="paymentMethod === '{{ $method }}' ? 'border-[#202a40] bg-[#202a40]/5' : 'border-[#111111]/10 hover:border-[#202a40]/30'"
                                >
                                    <input type="radio" name="paymentMethod" value="{{ $method }}" x-model="paymentMethod" class="sr-only">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors" :class="paymentMethod === '{{ $method }}' ? 'bg-[#202a40] text-white' : 'bg-[#111111]/6 text-[#111111]/50'">
                                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="{{ $icon }}"/></svg>
                                    </div>
                                    <span class="text-xs font-semibold leading-tight" :class="paymentMethod === '{{ $method }}' ? 'text-[#202a40]' : 'text-[#111111]/60'">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>

                            {{-- Card form --}}
                            <div x-show="paymentMethod === 'card'" class="space-y-4 pt-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold text-[#111111]/50 uppercase tracking-wider">Card Details</span>
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-auto" viewBox="0 0 48 32" fill="none"><rect width="48" height="32" rx="4" fill="#fff" stroke="#e5e7eb"/><path d="M19.5 21h-2.7l1.7-10.5h2.7L19.5 21Zm11.2-10.2c-.5-.2-1.4-.4-2.4-.4-2.7 0-4.5 1.4-4.5 3.4 0 1.5 1.4 2.3 2.4 2.8 1 .5 1.4.8 1.4 1.3 0 .7-.8 1-1.6 1-1.1 0-1.6-.2-2.5-.5l-.3-.2-.4 2.2c.6.3 1.8.5 3 .5 2.8 0 4.7-1.4 4.7-3.5 0-1.2-.7-2.1-2.3-2.8-.9-.5-1.5-.8-1.5-1.3 0-.4.5-.9 1.5-.9.9 0 1.5.2 2 .4l.2.1.3-2.1ZM35 10.5h-2.1c-.7 0-1.1.2-1.4.8L27.8 21h2.8l.6-1.5h3.5l.3 1.5H37L35 10.5Zm-3.4 7 1.1-3 .3-.8.2.7.6 3.1h-2.2ZM16 10.5l-2.5 7.2-.3-1.3c-.5-1.6-2-3.4-3.7-4.3l2.4 9h2.9l4.3-10.5H16Z" fill="#1A1F71"/><path d="M12 10.5H7.8l-.1.3c3.4.9 5.7 3 6.6 5.5l-1-4.8c-.1-.7-.6-.9-1.3-1Z" fill="#F9A533"/></svg>
                                        <svg class="h-5 w-auto" viewBox="0 0 48 32" fill="none"><rect width="48" height="32" rx="4" fill="#fff" stroke="#e5e7eb"/><circle cx="20" cy="16" r="9" fill="#EB001B"/><circle cx="28" cy="16" r="9" fill="#F79E1B"/><path d="M24 9.3a9 9 0 011.3 6.7A9 9 0 0124 22.7a9 9 0 01-1.3-6.7A9 9 0 0124 9.3z" fill="#FF5F00"/></svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="cardNumber"
                                        placeholder="1234  5678  9012  3456"
                                        maxlength="19"
                                        @input="cardNumber = cardNumber.replace(/[^\d]/g,'').replace(/(\d{4})/g,'$1 ').trim().substring(0,19)"
                                        class="w-full pl-4 pr-12 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all font-mono tracking-widest placeholder-[#111111]/20"
                                        autocomplete="cc-number"
                                    >
                                    <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#111111]/25" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="16" height="12" rx="1.5"/><path d="M2 9h16"/></svg>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <input
                                            type="text"
                                            x-model="cardExpiry"
                                            placeholder="MM / YY"
                                            maxlength="7"
                                            @input="let v = cardExpiry.replace(/[^\d]/g,''); if(v.length >= 3) v = v.substring(0,2)+' / '+v.substring(2,4); cardExpiry = v;"
                                            class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all font-mono tracking-wider placeholder-[#111111]/20"
                                            autocomplete="cc-exp"
                                        >
                                    </div>
                                    <div class="relative">
                                        <input
                                            type="password"
                                            x-model="cardCvv"
                                            placeholder="CVV"
                                            maxlength="4"
                                            class="w-full pl-4 pr-10 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all font-mono tracking-widest placeholder-[#111111]/20"
                                            autocomplete="cc-csc"
                                        >
                                        <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#111111]/25" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="12" height="8" rx="1"/><path d="M5 6V5a3 3 0 016 0v1"/></svg>
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    x-model="cardName"
                                    placeholder="Name on card"
                                    class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all uppercase placeholder-[#111111]/20"
                                    autocomplete="cc-name"
                                >
                            </div>

                            {{-- PayPal handled by redirect --}}
                            <div x-show="paymentMethod === 'paypal'" class="space-y-4 pt-2">
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="paypalEmail"
                                        placeholder="yourname@upi"
                                        class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all placeholder-[#111111]/25"
                                    >
                                </div>
                                <div>
                                    <p class="text-xs text-[#111111]/45 mb-3 font-medium">Or pay using popular apps</p>
                                    <div class="flex flex-wrap gap-2.5">
                                        @foreach([['gpay', 'G Pay', '#4285F4'], ['phonepe', 'PhonePe', '#5F259F'], ['paytm', 'Paytm', '#00BAF2'], ['amazonpay', 'Amazon Pay', '#FF9900']] as [$app, $label, $color])
                                        <button
                                            @click="paypalOption = '{{ $app }}'"
                                            class="flex items-center gap-2 px-4 py-2 rounded-full border-2 text-sm font-semibold transition-all duration-200"
                                            :class="paypalOption === '{{ $app }}' ? 'border-[#202a40] bg-[#202a40]/8 text-[#202a40]' : 'border-[#111111]/10 text-[#111111]/60 hover:border-[#202a40]/40'"
                                        >
                                            <span class="w-2 h-2 rounded-full" style="background: {{ $color }};"></span>
                                            {{ $label }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Net Banking --}}
                            <div x-show="paymentMethod === 'netbanking'" class="space-y-3 pt-2">
                                <label class="block text-xs font-semibold text-[#111111]/50 uppercase tracking-wider mb-2">Select Bank</label>
                                <select class="w-full px-4 py-3.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all text-[#111111]">
                                    <option value="" disabled selected>Choose your bank</option>
                                    @foreach(['SBI — State Bank of India', 'HDFC Bank', 'ICICI Bank', 'Axis Bank', 'Kotak Mahindra Bank', 'Punjab National Bank', 'Bank of Baroda', 'Canara Bank', 'Union Bank of India', 'Yes Bank'] as $bank)
                                    <option>{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- COD --}}
                            <div x-show="false" class="pt-2">
                                <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-amber-800">Secure payment via Stripe or PayPal</p>
                                        <p class="text-xs text-amber-700 mt-0.5">An additional handling charge of £40 applies for COD orders.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button
                                    @click="prevStep()"
                                    class="px-6 py-4 border border-[#111111]/15 text-[#111111]/60 text-sm font-medium rounded-xl hover:bg-[#111111]/5 transition-colors"
                                >
                                    ← Back
                                </button>
                                <button
                                    @click="nextStep()"
                                    class="group flex-1 flex items-center justify-center gap-2 py-4 bg-[#202a40] text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg"
                                >
                                    Review Order
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────
                     STEP 4: REVIEW
                ──────────────────────────────── --}}
                <div x-show="step===4" x-cloak data-gsap="step-panel">
                    <div class="bg-white border border-[#111111]/8 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-6 sm:px-8 pt-7 pb-5 border-b border-[#111111]/6">
                            <h2 class="font-serif text-xl sm:text-2xl text-[#111111]" style="font-family:'Playfair Display',serif;">Review Your Order</h2>
                            <p class="text-sm text-[#111111]/45 mt-1">Please verify all details before placing your order.</p>
                        </div>
                        <div class="divide-y divide-[#111111]/6">

                            {{-- Contact info review --}}
                            <div
                                class="px-6 sm:px-8 py-5"
                                x-data="{ open: true }"
                            >
                                <button @click="open = !open" class="flex items-center justify-between w-full">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5" r="3"/><path d="M2 14a6 6 0 0112 0"/></svg>
                                        <span class="text-sm font-semibold text-[#111111]">Contact Information</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click.stop="goTo(1)" class="text-xs text-[#202a40] hover:underline font-medium">Edit</button>
                                        <svg class="w-4 h-4 text-[#111111]/30 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
                                    </div>
                                </button>
                                <div x-show="open" x-collapse class="mt-3 space-y-1.5">
                                    <p class="text-sm text-[#111111]/70"><span class="font-medium text-[#111111]">Email:</span> <span x-text="email || 'Not provided'"></span></p>
                                    <p class="text-sm text-[#111111]/70"><span class="font-medium text-[#111111]">Phone:</span> +44 <span x-text="phone || 'Not provided'"></span></p>
                                </div>
                            </div>

                            {{-- Shipping address review --}}
                            <div
                                class="px-6 sm:px-8 py-5"
                                x-data="{ open: true }"
                            >
                                <button @click="open = !open" class="flex items-center justify-between w-full">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1a5 5 0 015 5c0 4-5 9-5 9S3 10 3 6a5 5 0 015-5z"/><circle cx="8" cy="6" r="1.5"/></svg>
                                        <span class="text-sm font-semibold text-[#111111]">Shipping Address</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click.stop="goTo(2)" class="text-xs text-[#202a40] hover:underline font-medium">Edit</button>
                                        <svg class="w-4 h-4 text-[#111111]/30 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
                                    </div>
                                </button>
                                <div x-show="open" x-collapse class="mt-3">
                                    <p class="text-sm text-[#111111]/70 leading-relaxed">
                                        <span class="font-semibold text-[#111111]" x-text="fullName || 'Name not set'"></span><br>
                                        <span x-text="address1 || '—'"></span>
                                        <span x-show="address2">, <span x-text="address2"></span></span><br>
                                        <span x-text="city || '—'"></span>, <span x-text="state || '—'"></span> — <span x-text="pincode || '—'"></span>
                                    </p>
                                    <div class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium" :class="delivery === 'express' ? 'text-[#C9A96E]' : 'text-emerald-600'">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 9l2-4h8l2 4"/><path d="M1 9h12M3 12a1 1 0 100-2 1 1 0 000 2zM11 12a1 1 0 100-2 1 1 0 000 2z"/></svg>
                                        <span x-text="delivery === 'express' ? 'Express (£1.50) — 2–3 days' : 'Standard (Free) — 5–7 days'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Payment review --}}
                            <div
                                class="px-6 sm:px-8 py-5"
                                x-data="{ open: true }"
                            >
                                <button @click="open = !open" class="flex items-center justify-between w-full">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="14" height="10" rx="1.5"/><path d="M1 7h14"/></svg>
                                        <span class="text-sm font-semibold text-[#111111]">Payment Method</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click.stop="goTo(3)" class="text-xs text-[#202a40] hover:underline font-medium">Edit</button>
                                        <svg class="w-4 h-4 text-[#111111]/30 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
                                    </div>
                                </button>
                                <div x-show="open" x-collapse class="mt-3">
                                    <p class="text-sm text-[#111111]/70">
                                        <span x-show="paymentMethod === 'card'">Credit / Debit Card ending in <strong x-text="cardNumber ? cardNumber.replace(/\s/g,'').slice(-4) : '****'"></strong></span>
                                        <span x-show="paymentMethod === 'paypal'">PayPal — <span x-text="paypalEmail || (paypalOption ? paypalOption.charAt(0).toUpperCase() + paypalOption.slice(1) : 'PayPal payment')"></span></span>
                                        <span x-show="paymentMethod === 'netbanking'">Net Banking</span>
                                        
                                    </p>
                                </div>
                            </div>

                            {{-- Price breakdown --}}
                            <div class="px-6 sm:px-8 py-5">
                                <h3 class="text-sm font-semibold text-[#111111] mb-4">Price Breakdown</h3>
                                <div class="space-y-2.5">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#111111]/55">Subtotal (3 items)</span>
                                        <span class="font-medium text-[#111111]" x-text="formatPrice(subtotal)"></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#111111]/55">Shipping</span>
                                        <span class="font-medium" :class="shippingCost === 0 ? 'text-emerald-600' : 'text-[#111111]'" x-text="shippingCost === 0 ? 'Free' : formatPrice(shippingCost)"></span>
                                    </div>
                                    <div class="flex justify-between text-sm" x-show="false">
                                        <span class="text-[#111111]/55">COD Charges</span>
                                        <span class="font-medium text-[#111111]">£40</span>
                                    </div>
                                    <div class="border-t border-[#111111]/8 pt-2.5 flex justify-between">
                                        <span class="text-base font-bold text-[#111111]">Total</span>
                                        <span class="text-xl font-bold text-[#111111]" x-text="formatPrice(total)"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="px-6 sm:px-8 py-6 bg-[#FAFAF8] flex gap-3">
                            <button
                                @click="prevStep()"
                                class="px-6 py-4 border border-[#111111]/15 text-[#111111]/60 text-sm font-medium rounded-xl hover:bg-white transition-colors"
                            >
                                ← Back
                            </button>
                            <button
                                @click="placeOrder()"
                                class="group flex-1 flex items-center justify-center gap-2 py-4 bg-gradient-to-r from-[#202a40] to-[#C9A96E] text-white text-sm font-bold rounded-xl hover:from-[#2d3a55] hover:to-[#b8924a] transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                            >
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="12" height="10" rx="1"/><path d="M2 7h12"/></svg>
                                Place Order · <span x-text="formatPrice(total)"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>{{-- end left --}}

            {{-- ── RIGHT: Order Summary ── --}}
            <div class="lg:col-span-5 order-first lg:order-last">
                <div class="sticky top-24">
                    <div class="bg-white border border-[#111111]/8 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-5 pt-5 pb-4 border-b border-[#111111]/6">
                            <h3 class="text-sm font-semibold text-[#111111]">Order Summary</h3>
                        </div>

                        {{-- Items --}}
                        <div class="px-5 py-4 space-y-4">
                            <template x-for="item in orderSummaryItems" :key="item.name">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-[#F3EEE9] shrink-0">
                                        <img :src="item.img" :alt="item.name" class="w-full h-full object-cover">
                                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-[#111111] text-white text-[10px] font-bold rounded-full flex items-center justify-center" x-text="item.qty"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-[#111111] line-clamp-2 leading-snug" x-text="item.name"></p>
                                        <p class="text-[11px] text-[#111111]/40 mt-0.5" x-text="item.variant"></p>
                                    </div>
                                    <p class="text-sm font-semibold text-[#111111] shrink-0" x-text="formatPrice(item.price * item.qty)"></p>
                                </div>
                            </template>
                        </div>

                        {{-- Totals --}}
                        <div class="px-5 pb-5 border-t border-[#111111]/6 pt-4 space-y-2.5">
                            <div class="flex justify-between text-sm">
                                <span class="text-[#111111]/55">Subtotal</span>
                                <span class="font-medium text-[#111111]" x-text="formatPrice(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-[#111111]/55">Shipping</span>
                                <span class="font-medium" :class="shippingCost === 0 ? 'text-emerald-600' : 'text-[#111111]'" x-text="shippingCost === 0 ? 'Free' : formatPrice(shippingCost)"></span>
                            </div>
                            <div class="border-t border-[#111111]/8 pt-2.5 flex justify-between">
                                <span class="text-sm font-bold text-[#111111]">Total</span>
                                <span class="text-base font-bold text-[#111111]" x-text="formatPrice(total)"></span>
                            </div>
                            <p class="text-[11px] text-[#111111]/35"></p>
                        </div>

                        {{-- Trust --}}
                        <div class="px-5 pb-5 flex items-center justify-center gap-4">
                            <div class="flex items-center gap-1.5 text-[11px] text-[#111111]/40">
                                <svg class="w-3.5 h-3.5 text-emerald-500" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M7 1l5 2.5v4c0 3-5 5.5-5 5.5S2 10.5 2 7.5v-4L7 1z"/></svg>
                                Secure
                            </div>
                            <div class="w-px h-3 bg-[#111111]/10"></div>
                            <div class="flex items-center gap-1.5 text-[11px] text-[#111111]/40">
                                <svg class="w-3.5 h-3.5 text-[#202a40]" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2H2v9l5 2 5-2V2z"/></svg>
                                Easy Returns
                            </div>
                            <div class="w-px h-3 bg-[#111111]/10"></div>
                            <div class="flex items-center gap-1.5 text-[11px] text-[#111111]/40">
                                <svg class="w-3.5 h-3.5 text-[#C9A96E]" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M7 1l1.5 3h3l-2.5 2 1 3L7 7.5 4 9l1-3L2.5 4h3z"/></svg>
                                Certified
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════
         ORDER SUCCESS OVERLAY
    ════════════════════════════════════ --}}
    <div
        id="order-success-overlay"
        x-show="ordered"
        x-cloak
        class="fixed inset-0 bg-[#FAFAF8]/95 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    >
        <div class="bg-white border border-[#111111]/8 rounded-3xl shadow-2xl max-w-md w-full px-8 py-12 text-center">

            {{-- Checkmark --}}
            <div id="success-checkmark" class="w-20 h-20 rounded-full bg-gradient-to-br from-[#202a40] to-[#C9A96E] flex items-center justify-center mx-auto mb-6 shadow-lg">
                <svg class="w-10 h-10 text-white" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 20l8 8L32 12"/>
                </svg>
            </div>

            <h2 class="font-serif text-2xl sm:text-3xl text-[#111111] mb-2" style="font-family:'Playfair Display',serif;">Order Placed!</h2>
            <p class="text-[#111111]/50 text-sm mb-5 leading-relaxed">
                Thank you for your order. We've sent a confirmation to <strong x-text="email"></strong>.
            </p>

            <div class="bg-[#F3EEE9] rounded-2xl px-6 py-4 mb-6">
                <p class="text-[11px] text-[#111111]/40 uppercase tracking-widest font-semibold mb-1">Order Number</p>
                <p class="text-xl font-bold text-[#202a40] tracking-wider font-mono" x-text="orderNumber"></p>
            </div>

            <p class="text-xs text-[#111111]/35 mb-7 leading-relaxed">
                Estimated delivery: <strong class="text-[#111111]/60" x-text="delivery === 'express' ? '2–3 business days' : '5–7 business days'"></strong>
            </p>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="/account/orders" class="flex-1 flex items-center justify-center gap-2 px-5 py-3 border border-[#202a40] text-[#202a40] text-sm font-semibold rounded-xl hover:bg-[#202a40] hover:text-white transition-all duration-200">
                    Track Order
                </a>
                <a href="/" class="flex-1 flex items-center justify-center gap-2 px-5 py-3 bg-[#111111] text-white text-sm font-semibold rounded-xl hover:bg-[#333] transition-colors">
                    Continue Shopping
                </a>
            </div>

        </div>
    </div>

</div>{{-- end page wrapper --}}

@endsection
