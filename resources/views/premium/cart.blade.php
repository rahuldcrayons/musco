@extends('premium.layout')

@section('namespace', 'cart')

@section('title', 'Your Bag — Trendymus')

@push('meta')
    <meta name="description" content="Review your selected items and proceed to checkout at Trendymus.">
@endpush

@section('content')

<div
    class="min-h-screen bg-[#FAFAF8]"
    x-data="{
        promoCode: '',
        promoApplied: false,
        promoError: false,
        promoDiscount: 0,
        applyPromo() {
            if (this.promoCode.trim().toUpperCase() === 'TRENDYMUS10') {
                this.promoApplied = true;
                this.promoError = false;
                this.promoDiscount = Math.round(this.subtotal * 0.10);
            } else {
                this.promoApplied = false;
                this.promoError = true;
                this.promoDiscount = 0;
            }
        },
        removePromo() {
            this.promoApplied = false;
            this.promoError = false;
            this.promoDiscount = 0;
            this.promoCode = '';
        },
        items: [
            { id:1, name:'18K Rose Gold Solitaire Ring', variant:'Size M', price:24999, qty:1, img:'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=200' },
            { id:2, name:'Diamond Pendant Necklace', variant:'18 inch chain', price:18499, qty:2, img:'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=200' },
        ],
        get subtotal() { return this.items.reduce((s,i)=>s+(i.price*i.qty),0) },
        get shipping() { return this.subtotal >= 999 ? 0 : 149 },
        get total() { return this.subtotal + this.shipping - (this.promoApplied ? this.promoDiscount : 0) },
        removeItem(id) {
            const idx = this.items.findIndex(i=>i.id===id);
            if (idx === -1) return;
            const el = this.$refs['item_'+id];
            if (el && typeof gsap !== 'undefined') {
                gsap.to(el, {height: 0, opacity: 0, marginBottom: 0, paddingTop: 0, paddingBottom: 0, duration: 0.35, ease: 'power2.inOut', onComplete: () => { this.items = this.items.filter(i=>i.id!==id); }});
            } else {
                this.items = this.items.filter(i=>i.id!==id);
            }
        },
        updateQty(id, delta) { const item=this.items.find(i=>i.id===id); if(item) { item.qty = Math.max(1, item.qty+delta); if(this.promoApplied){ this.promoDiscount = Math.round(this.subtotal * 0.10); } } },
        get isEmpty() { return this.items.length === 0 },
        formatPrice(p) { return '£' + p.toFixed(2); }
    }"
>

    {{-- ── Page Header ── --}}
    <div class="border-b border-[#111111]/8 bg-white">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-10 py-4 sm:py-5">
            <nav class="flex items-center gap-2 text-xs text-[#111111]/40 mb-3">
                <a href="/" class="hover:text-[#202a40] transition-colors duration-200">Home</a>
                <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 2l4 4-4 4"/></svg>
                <span class="text-[#111111]/60">Your Bag</span>
            </nav>
            <h1 class="font-serif text-2xl sm:text-3xl text-[#111111]" style="font-family:'Playfair Display',serif;">
                Your Bag
                <span class="text-[#202a40] text-xl sm:text-2xl ml-1" x-show="!isEmpty">
                    (<span x-text="items.length"></span> <span x-text="items.length === 1 ? 'item' : 'items'"></span>)
                </span>
            </h1>
        </div>
    </div>

    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-10 py-8 lg:py-12">

        {{-- ═══════════════════════════════════
             EMPTY STATE
        ════════════════════════════════════ --}}
        <div
            x-show="isEmpty"
            x-transition:enter="transition ease-out duration-400"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col items-center justify-center py-24 text-center"
        >
            {{-- Lottie container --}}
            <div
                data-lottie
                data-lottie-path="/animations/empty-cart.json"
                class="w-64 h-64 mx-auto"
                aria-hidden="true"
            >
                {{-- Fallback SVG cart icon shown until/if Lottie loads --}}
                <svg class="w-32 h-32 mx-auto mt-8 text-[#202a40]/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    <circle cx="9" cy="21" r="1" fill="currentColor" stroke="none"/>
                    <circle cx="15" cy="21" r="1" fill="currentColor" stroke="none"/>
                </svg>
            </div>

            <h2 class="font-serif text-2xl sm:text-3xl text-[#111111] mt-4 mb-3" style="font-family:'Playfair Display',serif;">
                Your bag is empty
            </h2>
            <p class="text-[#111111]/50 text-sm sm:text-base max-w-xs mb-8 leading-relaxed">
                Looks like you haven't added any pieces yet. Explore our collections to find something beautiful.
            </p>
            <a
                href="/products"
                class="inline-flex items-center gap-2 px-8 py-3.5 bg-[#202a40] text-white text-sm font-semibold rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5"
            >
                Start Shopping
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
            </a>
        </div>

        {{-- ═══════════════════════════════════
             CART CONTENT (non-empty)
        ════════════════════════════════════ --}}
        <div
            x-show="!isEmpty"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12"
        >

            {{-- ── LEFT: Cart Items ── --}}
            <div class="lg:col-span-8">

                {{-- Items heading --}}
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold text-[#111111]">
                        Your Bag
                        (<span x-text="items.length"></span>&nbsp;<span x-text="items.length === 1 ? 'item' : 'items'"></span>)
                    </h2>
                    <span class="text-xs text-[#111111]/40 uppercase tracking-wider font-medium">Price</span>
                </div>

                {{-- Free shipping progress --}}
                <div class="bg-white border border-[#111111]/8 rounded-2xl px-5 py-4 mb-5">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-medium text-[#111111]/70">
                            <span x-show="subtotal < 999" class="text-[#202a40] font-semibold">
                                <span x-text="formatPrice(999 - subtotal)"></span> more
                            </span>
                            <span x-show="subtotal >= 999" class="text-emerald-600 font-semibold">🎉 You've got</span>
                            <span x-show="subtotal < 999"> for free shipping</span>
                            <span x-show="subtotal >= 999"> free shipping!</span>
                        </span>
                        <span class="text-[10px] text-[#111111]/40 uppercase tracking-widest font-semibold">Free above £19</span>
                    </div>
                    <div class="h-1.5 bg-[#111111]/8 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-gradient-to-r from-[#202a40] to-[#C9A96E] rounded-full transition-all duration-700 ease-out"
                            :style="'width:' + Math.min(100, Math.round((subtotal/999)*100)) + '%'"
                        ></div>
                    </div>
                </div>

                {{-- Item list --}}
                <div class="space-y-0">
                    <template x-for="item in items" :key="item.id">
                        <div
                            :ref="'item_'+item.id"
                            class="bg-white border border-[#111111]/8 rounded-2xl mb-3 overflow-hidden"
                            style="transition: opacity 0.3s"
                        >
                            <div class="flex items-start gap-4 p-4 sm:p-5">

                                {{-- Product image --}}
                                <div class="w-[72px] h-[72px] sm:w-20 sm:h-20 rounded-xl overflow-hidden shrink-0 bg-[#F3EEE9]">
                                    <img
                                        :src="item.img"
                                        :alt="item.name"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    >
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <h3 class="text-sm sm:text-[15px] font-semibold text-[#111111] leading-snug truncate" x-text="item.name"></h3>
                                            <p class="text-xs text-[#111111]/45 mt-0.5" x-text="item.variant"></p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-sm sm:text-base font-semibold text-[#111111]" x-text="formatPrice(item.price * item.qty)"></p>
                                            <p class="text-[11px] text-[#111111]/40 mt-0.5" x-text="formatPrice(item.price) + ' each'" x-show="item.qty > 1"></p>
                                        </div>
                                    </div>

                                    {{-- Qty + remove --}}
                                    <div class="flex items-center justify-between mt-4">
                                        {{-- Quantity stepper --}}
                                        <div class="flex items-center border border-[#111111]/12 rounded-full overflow-hidden">
                                            <button
                                                @click="updateQty(item.id, -1)"
                                                class="w-8 h-8 flex items-center justify-center text-[#111111]/50 hover:text-[#111111] hover:bg-[#111111]/5 transition-colors"
                                                :disabled="item.qty <= 1"
                                                :class="item.qty <= 1 ? 'opacity-30 cursor-not-allowed' : ''"
                                                aria-label="Decrease quantity"
                                            >
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7h10"/></svg>
                                            </button>
                                            <span class="w-9 text-center text-sm font-medium text-[#111111]" x-text="item.qty"></span>
                                            <button
                                                @click="updateQty(item.id, 1)"
                                                class="w-8 h-8 flex items-center justify-center text-[#111111]/50 hover:text-[#111111] hover:bg-[#111111]/5 transition-colors"
                                                aria-label="Increase quantity"
                                            >
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 2v10M2 7h10"/></svg>
                                            </button>
                                        </div>

                                        {{-- Remove --}}
                                        <button
                                            @click="
                                                if (typeof gsap !== 'undefined') {
                                                    const el = $refs['item_'+item.id];
                                                    gsap.to(el, {height: el.offsetHeight + 'px'});
                                                    gsap.to(el, {height: 0, opacity: 0, marginBottom: 0, paddingTop: 0, paddingBottom: 0, duration: 0.35, ease: 'power2.inOut', onComplete: () => removeItem(item.id)});
                                                } else { removeItem(item.id); }
                                            "
                                            class="flex items-center gap-1.5 text-xs text-[#111111]/35 hover:text-red-500 transition-colors duration-200"
                                            aria-label="Remove item"
                                        >
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.75">
                                                <path d="M2 2l10 10M12 2L2 12"/>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Continue shopping --}}
                <div class="mt-4">
                    <a href="/products" class="inline-flex items-center gap-1.5 text-sm text-[#202a40] font-medium hover:gap-2.5 transition-all duration-200">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M13 8H3M7 4l-4 4 4 4"/></svg>
                        Continue Shopping
                    </a>
                </div>

            </div>{{-- end left --}}

            {{-- ── RIGHT: Order Summary ── --}}
            <div class="lg:col-span-4">
                <div class="sticky top-6">

                    {{-- Summary card --}}
                    <div class="bg-white border border-[#111111]/8 rounded-2xl overflow-hidden shadow-sm">

                        {{-- Header --}}
                        <div class="px-5 pt-5 pb-4 border-b border-[#111111]/6">
                            <h2 class="text-base font-semibold text-[#111111]">Order Summary</h2>
                        </div>

                        <div class="px-5 py-5 space-y-3.5">

                            {{-- Subtotal --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-[#111111]/60">Subtotal (<span x-text="items.reduce((s,i)=>s+i.qty,0)"></span> items)</span>
                                <span class="text-sm font-medium text-[#111111]" x-text="formatPrice(subtotal)"></span>
                            </div>

                            {{-- Shipping --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-[#111111]/60">Shipping</span>
                                <span
                                    class="text-sm font-medium"
                                    :class="shipping === 0 ? 'text-emerald-600' : 'text-[#111111]'"
                                    x-text="shipping === 0 ? 'Free' : formatPrice(shipping)"
                                ></span>
                            </div>

                            {{-- Promo discount --}}
                            <div class="flex items-center justify-between" x-show="promoApplied">
                                <span class="text-sm text-emerald-600 font-medium">Promo (TRENDYMUS10)</span>
                                <span class="text-sm font-semibold text-emerald-600" x-text="'− ' + formatPrice(promoDiscount)"></span>
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-[#111111]/8 pt-3.5 mt-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-base font-semibold text-[#111111]">Total</span>
                                    <span class="text-xl font-bold text-[#111111]" x-text="formatPrice(total)"></span>
                                </div>
                                <p class="text-[11px] text-[#111111]/35 mt-1">Inclusive of all taxes (GST incl.)</p>
                            </div>

                        </div>

                        {{-- Promo code --}}
                        <div
                            class="px-5 pb-5"
                            x-data="{ promoOpen: false }"
                        >
                            <button
                                @click="promoOpen = !promoOpen"
                                class="flex items-center gap-2 text-sm text-[#202a40] font-medium w-full mb-3"
                            >
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="5" width="14" height="7" rx="1.5"/><path d="M5 5V4a3 3 0 016 0v1"/></svg>
                                <span x-text="promoOpen ? 'Hide promo code' : 'Have a promo code?'"></span>
                                <svg class="w-3.5 h-3.5 ml-auto transition-transform duration-200" :class="promoOpen ? 'rotate-180' : ''" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4l4 4 4-4"/></svg>
                            </button>
                            <div x-show="promoOpen" x-collapse class="space-y-2">
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        x-model="promoCode"
                                        @keydown.enter="applyPromo()"
                                        placeholder="Enter promo code"
                                        class="flex-1 px-3.5 py-2.5 text-sm border border-[#111111]/15 rounded-xl bg-[#FAFAF8] focus:outline-none focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all placeholder-[#111111]/30 uppercase tracking-wider"
                                        :class="promoError ? 'border-red-400' : (promoApplied ? 'border-emerald-400 bg-emerald-50' : '')"
                                    >
                                    <button
                                        @click="applyPromo()"
                                        x-show="!promoApplied"
                                        class="px-4 py-2.5 bg-[#111111] text-white text-sm font-semibold rounded-xl hover:bg-[#333] transition-colors shrink-0"
                                    >Apply</button>
                                    <button
                                        @click="removePromo()"
                                        x-show="promoApplied"
                                        class="px-4 py-2.5 bg-red-50 text-red-500 text-sm font-semibold rounded-xl hover:bg-red-100 transition-colors shrink-0"
                                    >Remove</button>
                                </div>
                                <p x-show="promoError" class="text-xs text-red-500">Invalid promo code. Try <strong>TRENDYMUS10</strong>.</p>
                                <p x-show="promoApplied" class="text-xs text-emerald-600 font-medium">✓ 10% discount applied!</p>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="px-5 pb-5">
                            <a
                                href="/checkout"
                                class="group flex items-center justify-center gap-2 w-full py-4 bg-[#202a40] text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-xl hover:-translate-y-0.5"
                            >
                                Proceed to Checkout
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                            </a>
                        </div>

                        {{-- Trust badges --}}
                        <div class="px-5 pb-5 border-t border-[#111111]/6 pt-4">
                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <div class="w-8 h-8 rounded-full bg-[#202a40]/8 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#202a40]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M8 1l1.5 3.5L13 5l-2.5 2.5.6 3.5L8 9.5l-3.1 1.5.6-3.5L3 5l3.5-.5z"/>
                                        </svg>
                                    </div>
                                    <span class="text-[10px] text-[#111111]/50 leading-tight font-medium">Secure Payment</span>
                                </div>
                                <div class="flex flex-col items-center gap-1.5">
                                    <div class="w-8 h-8 rounded-full bg-[#C9A96E]/12 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#C9A96E]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M14 2H2v9l6 3 6-3V2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-[10px] text-[#111111]/50 leading-tight font-medium">Easy Returns</span>
                                </div>
                                <div class="flex flex-col items-center gap-1.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/8 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M13 4l-7 7-3-3"/>
                                        </svg>
                                    </div>
                                    <span class="text-[10px] text-[#111111]/50 leading-tight font-medium">BIS Certified</span>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end summary card --}}

                </div>{{-- end sticky --}}
            </div>{{-- end right --}}

        </div>{{-- end grid --}}

    </div>{{-- end container --}}

    {{-- ═══════════════════════════════════
         RECENTLY VIEWED
    ════════════════════════════════════ --}}
    <div class="border-t border-[#111111]/6 bg-white mt-8">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-10 py-10">
            <h2 class="font-serif text-xl text-[#111111] mb-6" style="font-family:'Playfair Display',serif;">
                Recently Viewed
            </h2>
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory -mx-1 px-1">

                @php
                $recentlyViewed = [
                    ['name' => '22K Gold Jhumka Earrings', 'price' => '£12,499', 'img' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=300', 'badge' => 'Bestseller'],
                    ['name' => 'Pearl Drop Earrings', 'price' => '£4,299', 'img' => 'https://images.unsplash.com/photo-1573408301185-9519f94816b5?w=300', 'badge' => null],
                    ['name' => 'Diamond Tennis Bracelet', 'price' => '£32,999', 'img' => 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=300', 'badge' => 'New'],
                    ['name' => 'Rose Gold Mangalsutra', 'price' => '£18,750', 'img' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=300', 'badge' => null],
                ];
                @endphp

                @foreach($recentlyViewed as $rv)
                <a
                    href="#"
                    class="snap-start shrink-0 w-48 sm:w-52 group"
                >
                    <div class="relative rounded-xl overflow-hidden bg-[#F3EEE9] aspect-square mb-3">
                        <img
                            src="{{ $rv['img'] }}"
                            alt="{{ $rv['name'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            loading="lazy"
                        >
                        @if($rv['badge'])
                        <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-white bg-[#202a40]">
                            {{ $rv['badge'] }}
                        </span>
                        @endif
                        <button class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white/80 backdrop-blur-sm flex items-center justify-center text-[#111111]/40 hover:text-[#202a40] hover:bg-white transition-all" aria-label="Wishlist">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M7 11.5s-5-3.2-5-6.5a3 3 0 016 0 3 3 0 016 0c0 3.3-5 6.5-5 6.5z"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-xs font-semibold text-[#111111] line-clamp-2 leading-snug group-hover:text-[#202a40] transition-colors">{{ $rv['name'] }}</h3>
                    <p class="text-sm font-bold text-[#111111] mt-1">{{ $rv['price'] }}</p>
                </a>
                @endforeach

            </div>
        </div>
    </div>

</div>{{-- end page wrapper --}}

@endsection

@push('scripts')
<script>
    // Lottie loader with fallback
    document.addEventListener('DOMContentLoaded', function () {
        const lottieContainers = document.querySelectorAll('[data-lottie]');
        lottieContainers.forEach(function (container) {
            const path = container.dataset.lottiePath;
            if (!path || typeof lottie === 'undefined') return;
            // Hide the fallback SVG first
            const fallback = container.querySelector('svg');
            fetch(path)
                .then(function (r) {
                    if (!r.ok) throw new Error('Lottie file not found');
                    return r.json();
                })
                .then(function () {
                    if (fallback) fallback.style.display = 'none';
                    lottie.loadAnimation({
                        container: container,
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        path: path,
                    });
                })
                .catch(function () {
                    // Fallback SVG stays visible
                    if (fallback) fallback.style.display = '';
                });
        });
    });
</script>
@endpush
