<x-layouts.app>
    <x-slot name="title">Offers & Deals - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Exclusive offers and deals at {{ config('app.name') }}. Shop now for the best discounts and cashback offers!">
        <link rel="canonical" href="{{ url('/offers') }}">
        <meta property="og:image" content="{{ asset('images/og-default.png') }}">
        <meta name="twitter:image" content="{{ asset('images/og-default.png') }}">
    @endpush

    <div class="bg-white min-h-screen">
        <!-- Hero Banner -->
        <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #202a40 0%, #2d3a55 50%, #222222 100%);">
            <div class="absolute inset-0 opacity-10">
                <svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none"><circle cx="20" cy="30" r="15" fill="white"/><circle cx="80" cy="70" r="20" fill="white"/><circle cx="50" cy="10" r="10" fill="white"/></svg>
            </div>
            <div class="container mx-auto px-4 py-12 lg:py-16 text-center relative z-10">
                <h1 class="text-3xl lg:text-5xl font-extrabold text-white mb-3">Offers & Deals</h1>
                <p class="text-white/90 text-sm lg:text-base max-w-lg mx-auto">Grab the best deals on {{ config('app.name') }} products. Limited time offers you don't want to miss!</p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-10 lg:py-14">
            <div class="grid gap-8 lg:gap-10 max-w-4xl mx-auto">

                <!-- OFFER 1: Navratri 5% Extra Off -->
                <div class="rounded-2xl overflow-hidden shadow-lg border border-[#efefef]">
                    <div class="p-1" style="background: #202a40;">
                        <div class="bg-white rounded-xl p-6 lg:p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                                <div class="flex-shrink-0 w-20 h-20 lg:w-24 lg:h-24 rounded-2xl flex items-center justify-center text-4xl lg:text-5xl" style="background: #f2f2f2;">
                                    🎉
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-white" style="background: #202a40;">Live Now</span>
                                        <span class="px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#f2f2f2] text-[#222222]">Auto Applied</span>
                                    </div>
                                    <h2 class="text-xl lg:text-2xl font-bold text-[#222222] mb-2">Navratri Special - Extra 5% Off</h2>
                                    <p class="text-sm text-[#555555] mb-3">Celebrate Navratri with {{ config('app.name') }}! Get <strong>flat 5% extra discount</strong> on your entire order, applied automatically at checkout after all other discounts. No coupon needed!</p>
                                    <ul class="text-xs text-[#555555] space-y-1 mb-4">
                                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#202a40] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Applied on top of coupon discounts</li>
                                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#202a40] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Valid on all products, all payment methods</li>
                                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#202a40] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Limited period offer</li>
                                    </ul>
                                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-bold text-white transition-all hover:scale-105" style="background: #202a40;">
                                        Shop Now
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OFFER 2: Video Review Cashback -->
                <div class="rounded-2xl overflow-hidden shadow-lg border border-[#efefef]">
                    <div class="p-1" style="background: #202a40;">
                        <div class="bg-white rounded-xl p-6 lg:p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                                <div class="flex-shrink-0 w-20 h-20 lg:w-24 lg:h-24 rounded-2xl flex items-center justify-center text-4xl lg:text-5xl" style="background: #f2f2f2;">
                                    🎥
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-white bg-[#202a40]">Cashback</span>
                                        <span class="px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#f2f2f2] text-[#506282]">£1 Reward</span>
                                    </div>
                                    @php $cashbackAmount = \App\Models\Setting::get('video_cashback_amount', '100'); @endphp
                                    <h2 class="text-xl lg:text-2xl font-bold text-[#222222] mb-2">Share a Video, Get £{{ $cashbackAmount }} Cashback!</h2>
                                    <p class="text-sm text-[#555555] mb-3">Already have a {{ config('app.name') }} product? Make a short video review showing how you use it and send it to us. We'll give you <strong>£{{ $cashbackAmount }} cashback</strong> directly to your account!</p>

                                    <div class="bg-[#f8f6f3] rounded-xl p-4 mb-4">
                                        <h3 class="text-sm font-bold text-[#222222] mb-3">How it works:</h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div class="text-center">
                                                <div class="w-10 h-10 rounded-full bg-[#202a40] text-white flex items-center justify-center mx-auto mb-2 text-sm font-bold">1</div>
                                                <p class="text-xs text-[#555555]">Record a <strong>30-60 sec video</strong> of your {{ config('app.name') }} product in use</p>
                                            </div>
                                            <div class="text-center">
                                                <div class="w-10 h-10 rounded-full bg-[#202a40] text-white flex items-center justify-center mx-auto mb-2 text-sm font-bold">2</div>
                                                <p class="text-xs text-[#555555]">Send the video to us on <strong>WhatsApp</strong> or <strong>Instagram DM</strong></p>
                                            </div>
                                            <div class="text-center">
                                                <div class="w-10 h-10 rounded-full bg-[#202a40] text-white flex items-center justify-center mx-auto mb-2 text-sm font-bold">3</div>
                                                <p class="text-xs text-[#555555]">Get <strong>£1 cashback</strong> as store credit within 48 hours</p>
                                            </div>
                                        </div>
                                    </div>

                                    <ul class="text-xs text-[#555555] space-y-1 mb-4">
                                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#202a40] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> One cashback per product per customer</li>
                                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#202a40] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Video must show the actual {{ config('app.name') }} product</li>
                                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#202a40] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> We may feature your video on our Instagram (with credit!)</li>
                                    </ul>

                                    @php
                                        $_waNum     = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('contact_whatsapp', ''));
                                        $_waText    = urlencode('Hi ' . config('app.name') . '! I want to share a video review of my product for the £' . $cashbackAmount . ' cashback offer.');
                                        $_igLink    = \App\Models\Setting::get('social_instagram', 'https://www.instagram.com/');
                                    @endphp
                                    <div class="flex flex-wrap gap-3">
                                        @if($_waNum)
                                        <a href="https://wa.me/{{ $_waNum }}?text={{ $_waText }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold text-white bg-[#25D366] hover:bg-[#20BD5A] transition-all hover:scale-105">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            Send on WhatsApp
                                        </a>
                                        @endif
                                        @if($_igLink)
                                        <a href="{{ $_igLink }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold text-white transition-all hover:scale-105" style="background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913a6.6 6.6 0 001.384 2.126A6.6 6.6 0 004.14 23.37c.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558a6.6 6.6 0 002.126-1.384 6.6 6.6 0 001.384-2.126c.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913a6.6 6.6 0 00-1.384-2.126A6.6 6.6 0 0019.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm7.846-10.405a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/></svg>
                                            DM on Instagram
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OFFER 3: Free Shipping -->
                <div class="rounded-2xl overflow-hidden shadow-lg border border-[#efefef]">
                    <div class="p-1 bg-[#202a40]">
                        <div class="bg-white rounded-xl p-6 lg:p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                                <div class="flex-shrink-0 w-20 h-20 lg:w-24 lg:h-24 rounded-2xl flex items-center justify-center text-4xl lg:text-5xl bg-[#f2f2f2]">
                                    🚚
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-white bg-[#202a40]">Always On</span>
                                    </div>
                                    @php $freeShipThreshold = \App\Models\Setting::get('free_shipping_threshold', 499); @endphp
                                    <h2 class="text-xl lg:text-2xl font-bold text-[#222222] mb-2">Free Shipping on Orders Above £{{ $freeShipThreshold }}</h2>
                                    <p class="text-sm text-[#555555] mb-3">No hidden shipping charges! All orders above £{{ $freeShipThreshold }} qualify for free shipping.</p>
                                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-bold text-white bg-[#202a40] hover:bg-[#506282] transition-all hover:scale-105">
                                        Start Shopping
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OFFER 4: 7-Day Easy Returns -->
                <div class="rounded-2xl overflow-hidden shadow-lg border border-[#efefef]">
                    <div class="p-1 bg-[#202a40]">
                        <div class="bg-white rounded-xl p-6 lg:p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                                <div class="flex-shrink-0 w-20 h-20 lg:w-24 lg:h-24 rounded-2xl flex items-center justify-center text-4xl lg:text-5xl bg-[#f2f2f2]">
                                    🔄
                                </div>
                                <div class="flex-1">
                                    <h2 class="text-xl lg:text-2xl font-bold text-[#222222] mb-2">7-Day Easy Returns</h2>
                                    <p class="text-sm text-[#555555] mb-3">Not satisfied? Return any product within 7 days of delivery. No questions asked. We'll process your refund within 3-5 business days.</p>
                                    <a href="{{ url('/pages/return-policy') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#202a40] hover:text-[#506282] transition-colors">
                                        View Return Policy
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
