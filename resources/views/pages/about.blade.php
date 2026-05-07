<x-layouts.app>
    <x-slot name="title">About Us - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Learn about Trendymus &mdash; affordable luxury jewelry that is high-quality, skin-friendly, and beautifully designed for every occasion.">
        <link rel="canonical" href="{{ url('/about') }}">
        <meta property="og:title" content="About Us - {{ config('app.name') }}">
        <meta property="og:description" content="Trendymus &mdash; affordable luxury jewelry for everyone.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/about') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta property="og:image" content="{{ asset('images/og-default.png') }}">
        <meta name="twitter:image" content="{{ asset('images/og-default.png') }}">
    @endpush

    @push('styles')
    <style>
        .about-hero-img { object-position: center 30%; }
        @media (max-width: 640px) {
            .about-split-img { height: 260px; }
        }
    </style>
    @endpush

    {{-- ════════════════════════════════════════════════════════
         1. HERO BANNER
         ════════════════════════════════════════════════════════ --}}
    <section class="relative h-[300px] sm:h-[400px] lg:h-[480px] overflow-hidden">
        <img src="{{ asset('images/banner1.jpeg') }}"
             alt="About {{ config('app.name') }}"
             class="absolute inset-0 w-full h-full object-cover about-hero-img">
        <div class="absolute inset-0 bg-[#202a40]/50"></div>

        {{-- Overlay content --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
            <nav class="flex items-center gap-2 text-[11px] uppercase tracking-widest text-white/60 mb-4">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span>›</span>
                <span class="text-white/90">About Us</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white drop-shadow-sm"
                style="font-family:'Playfair Display',Georgia,serif;">About Us</h1>
            <div class="w-16 h-0.5 bg-white/40 mx-auto mt-5 mb-4"></div>
            <p class="text-sm text-white/75 max-w-sm leading-relaxed">Affordable luxury &mdash; crafted for confidence, designed to last.</p>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         2. OUR MISSION &mdash; image left, text right
         ════════════════════════════════════════════════════════ --}}
    <section class="py-16 sm:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-stretch max-w-6xl mx-auto shadow-sm overflow-hidden rounded-sm">

                {{-- Image side --}}
                <div class="relative overflow-hidden about-split-img h-72 sm:h-80 lg:h-auto min-h-[360px]">
                    <img src="{{ asset('images/img1.jpg') }}" alt="Our Mission"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#202a40]/60 via-[#202a40]/20 to-transparent"></div>
                    <div class="absolute bottom-6 left-6">
                        <span class="text-xs uppercase tracking-[0.2em] text-white/90 font-medium bg-[#202a40]/60 px-3 py-1 rounded-full backdrop-blur-sm">Our Mission</span>
                    </div>
                </div>

                {{-- Text side --}}
                <div class="bg-white p-10 sm:p-12 lg:p-16 flex flex-col justify-center">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-[#202a40] font-bold mb-3">Who We Are</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#1a1a1a] mb-5 leading-tight"
                        style="font-family:'Playfair Display',Georgia,serif;">
                        Quality & Confidence<br>For Every Budget
                    </h2>
                    <div class="w-10 h-0.5 bg-[#202a40] mb-6"></div>
                    <div class="space-y-4 text-[13px] text-[#555] leading-[1.8]">
                        <p>Our mission is to bring you high-quality, beautiful jewelry that fits your lifestyle and your budget. We hand-select every piece to ensure it meets our standards for shine, durability, and comfort. We believe that everyone deserves to feel elegant and confident, and we are dedicated to making that happen by offering stylish designs that don't break the bank.</p>
                        <p>We are also committed to providing a seamless and honest shopping experience. From our skin-friendly materials to our carefully handled shipping, our goal is to make sure you love what you wear as much as we loved picking it out for you. Your trust is our greatest reward, and we work hard every day to earn it through quality and care.</p>
                    </div>
                    <div class="mt-8 flex items-center gap-3">
                        <div class="flex gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#202a40]"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-[#202a40]/40"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-[#202a40]/20"></div>
                        </div>
                        <span class="text-[11px] text-[#999] uppercase tracking-wider">Since Day One</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         3. OUR VISION &mdash; text left, image right
         ════════════════════════════════════════════════════════ --}}
    <section class="py-16 sm:py-24 bg-[#f7f7f7]">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-stretch max-w-6xl mx-auto shadow-sm overflow-hidden rounded-sm">

                {{-- Text side --}}
                <div class="bg-[#202a40] p-10 sm:p-12 lg:p-16 flex flex-col justify-center order-2 lg:order-1">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-white/50 font-bold mb-3">Where We're Headed</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-5 leading-tight"
                        style="font-family:'Playfair Display',Georgia,serif;">
                        Style Without<br>Compromise
                    </h2>
                    <div class="w-10 h-0.5 bg-white/30 mb-6"></div>
                    <div class="space-y-4 text-[13px] text-white/75 leading-[1.8]">
                        <p>We envision a world where high-end style is accessible to everyone, regardless of their budget. Our goal is to break the myth that elegance must be expensive by providing stunning, jewelry-store-quality pieces that allow you to shine every day. We want to be the first name you think of when you want to treat yourself or a loved one to something truly special.</p>
                        <p>Looking ahead, we aim to build a community centered around confidence and self-expression. We are committed to constantly innovating our designs and improving our quality, ensuring that our brand remains a trusted symbol of modern luxury. Our ultimate dream is to help every customer feel like the best version of themselves, one sparkling piece at a time.</p>
                    </div>
                    <div class="mt-8">
                        <span class="text-lg text-white/80 italic" style="font-family:'Playfair Display',Georgia,serif;">Trendymus</span>
                    </div>
                </div>

                {{-- Image side --}}
                <div class="relative overflow-hidden about-split-img h-72 sm:h-80 lg:h-auto min-h-[360px] order-1 lg:order-2">
                    <img src="{{ asset('images/img2.jpg') }}" alt="Our Vision"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#202a40]/50"></div>
                    <div class="absolute bottom-6 right-6">
                        <span class="text-xs uppercase tracking-[0.2em] text-white/90 font-medium bg-[#202a40]/60 px-3 py-1 rounded-full backdrop-blur-sm">Our Vision</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         4. THE TRENDYMUS DIFFERENCE &mdash; 3 feature cards
         ════════════════════════════════════════════════════════ --}}
    <section class="py-16 sm:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-14">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-[#202a40] font-bold mb-3">What Sets Us Apart</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#1a1a1a]"
                        style="font-family:'Playfair Display',Georgia,serif;">The Trendymus Difference</h2>
                    <div class="w-12 h-0.5 bg-[#202a40] mx-auto mt-5"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 lg:gap-8">
                    {{-- Card 1 --}}
                    <div class="group relative bg-white border border-[#efefef] p-8 rounded-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="w-14 h-14 rounded-full bg-[#f2f4f8] group-hover:bg-[#202a40] flex items-center justify-center mb-6 transition-colors duration-300">
                            <svg class="w-6 h-6 text-[#202a40] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#1a1a1a] mb-3 uppercase tracking-wide">Handpicked Quality</h3>
                        <p class="text-[13px] text-[#666] leading-relaxed">We use premium alloys and high-grade stones that mimic the brilliance of real diamonds and gold.</p>
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#202a40] scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left rounded-b-sm"></div>
                    </div>

                    {{-- Card 2 &mdash; featured / center --}}
                    <div class="group relative bg-[#202a40] p-8 rounded-sm shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="w-14 h-14 rounded-full bg-white/15 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-white mb-3 uppercase tracking-wide">Skin-Friendly</h3>
                        <p class="text-[13px] text-white/70 leading-relaxed">All our pieces are 100% hypoallergenic and nickel-free.</p>
                        <div class="mt-6 inline-block text-[10px] uppercase tracking-widest text-white/50 border border-white/20 px-3 py-1 rounded-full">Our Promise</div>
                    </div>

                    {{-- Card 3 --}}
                    <div class="group relative bg-white border border-[#efefef] p-8 rounded-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="w-14 h-14 rounded-full bg-[#f2f4f8] group-hover:bg-[#202a40] flex items-center justify-center mb-6 transition-colors duration-300">
                            <svg class="w-6 h-6 text-[#202a40] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#1a1a1a] mb-3 uppercase tracking-wide">Timeless Design</h3>
                        <p class="text-[13px] text-[#666] leading-relaxed">We don't just follow trends; we create pieces you'll want to wear for years.</p>
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#202a40] scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left rounded-b-sm"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         5. OUR STORY &mdash; light bg with left accent card
         ════════════════════════════════════════════════════════ --}}
    <section class="py-16 sm:py-24 bg-[#f7f7f7]">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">

                <div class="text-center mb-12">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-[#202a40] font-bold mb-3">How It All Began</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#1a1a1a]"
                        style="font-family:'Playfair Display',Georgia,serif;">Our Story</h2>
                    <div class="w-12 h-0.5 bg-[#202a40] mx-auto mt-5"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 overflow-hidden rounded-sm shadow-sm">

                    {{-- Left accent panel --}}
                    <div class="bg-[#202a40] p-10 flex flex-col justify-between">
                        <div>
                            <svg class="w-10 h-10 text-white/20 mb-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.293-3.995 5.847h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.293-3.996 5.847h3.983v10h-9.983z"/>
                            </svg>
                            <p class="text-[13px] text-white/70 leading-[1.9] italic mb-8">
                                "Jewelry that looks, feels, and lasts like the real thing &mdash; without the investment-grade cost."
                            </p>
                        </div>
                        <div class="border-t border-white/15 pt-6">
                            <span class="text-xl text-white italic block mb-1" style="font-family:'Playfair Display',Georgia,serif;">Trendymus</span>
                            <span class="text-[10px] uppercase tracking-[0.2em] text-white/40">Affordable Luxury</span>
                        </div>
                    </div>

                    {{-- Right content panel --}}
                    <div class="lg:col-span-2 bg-white p-10 sm:p-12 flex flex-col justify-center">
                        <p class="text-[13px] text-[#555] leading-[1.9]">
                            We believe that elegance shouldn't come with a heavy price tag. <strong class="text-[#202a40]">Trendymus</strong> was born out of a simple observation: women were often forced to choose between overpriced fine jewelry or low-quality trinkets. We decided to bridge that gap by creating <span class="font-semibold text-[#1a1a1a]">"affordable luxury"</span> &mdash; jewelry that looks, feels, and lasts like the real thing, without the investment-grade cost.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#f2f4f8] flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#202a40]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-[12px] text-[#555]">Affordable Luxury</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#f2f4f8] flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#202a40]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-[12px] text-[#555]">Real Quality</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#f2f4f8] flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#202a40]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-[12px] text-[#555]">Built to Last</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         6. GALLERY STRIP &mdash; 4 images
         ════════════════════════════════════════════════════════ --}}
    <section class="grid grid-cols-2 sm:grid-cols-4">
        @foreach([
            ['label' => 'Necklaces', 'img' => 'img1.jpg'],
            ['label' => 'Earrings',  'img' => 'img2.jpg'],
            ['label' => 'Bracelets', 'img' => 'img3.jpg'],
            ['label' => 'Rings',     'img' => 'img4.jpg'],
        ] as $panel)
        <div class="relative overflow-hidden h-44 sm:h-56 lg:h-64 group bg-[#202a40]">
            <img src="{{ asset('images/' . $panel['img']) }}" alt="{{ $panel['label'] }}"
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-[#202a40]/30 group-hover:bg-[#202a40]/50 transition-colors duration-300"></div>
            <div class="absolute inset-0 flex items-end justify-center pb-5">
                <span class="text-xs font-semibold text-white uppercase tracking-widest drop-shadow">{{ $panel['label'] }}</span>
            </div>
        </div>
        @endforeach
    </section>

</x-layouts.app>
