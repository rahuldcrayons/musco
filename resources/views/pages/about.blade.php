<x-layouts.app>
    <x-slot name="title">About Us - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Learn about {{ config('app.name') }} - your trusted destination for certified gold, diamond & silver jewellery. Exquisite craftsmanship, hallmarked collections.">
        <link rel="canonical" href="{{ url('/about') }}">
        <meta property="og:title" content="About Us - {{ config('app.name') }}">
        <meta property="og:description" content="Learn about {{ config('app.name') }} - your trusted destination for certified gold, diamond & silver jewellery.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/about') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="About Us - {{ config('app.name') }}">
        <meta name="twitter:description" content="Learn about {{ config('app.name') }} - your trusted destination for certified gold, diamond & silver jewellery.">
    @endpush

    <!-- ============================================
         1. BREADCRUMB HERO (Corano style — light gray)
         ============================================ -->
    <section class="bg-[#f2f2f2] py-12 sm:py-16 border-b border-[#efefef]">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-[#222222] mb-3" style="font-family:'Playfair Display',Georgia,serif;">About Us</h1>
            <div class="flex items-center justify-center gap-2 text-sm">
                <a href="{{ url('/') }}" class="text-[#777777] hover:text-[#c29958] transition-colors">Home</a>
                <span class="text-[#c29958]">></span>
                <span class="text-[#c29958]">About Us</span>
            </div>
        </div>
    </section>

    <!-- ============================================
         2. LARGE IMAGE (Corano — centered image block)
         ============================================ -->
    <section class="py-14 sm:py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                @php $aboutImage = \App\Models\Setting::get('about_image', ''); @endphp
                @if($aboutImage)
                    <img src="{{ asset('storage/' . $aboutImage) }}" alt="About {{ config('app.name') }}" class="w-full max-h-[400px] object-cover" loading="lazy">
                @else
                    <img src="{{ asset('images/about-us-banner1.jpg') }}" alt="About {{ config('app.name') }}" class="w-full max-h-[400px] object-cover" loading="lazy">
                @endif
            </div>
        </div>
    </section>

    <!-- ============================================
         3. STORY TEXT — Centered (Corano style)
         ============================================ -->
    <section class="pb-14 sm:pb-20">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#222222] mb-6 leading-snug" style="font-family:'Playfair Display',Georgia,serif;">
                    {{ \App\Models\Setting::get('about_heading', 'We Are A Jewellery Brand Focused On Delivering Timeless Elegance And Quality Craftsmanship.') }}
                </h2>
                <div class="text-sm text-[#777777] leading-relaxed space-y-4">
                    @if($page?->content)
                        {!! $page->content !!}
                    @else
                        <p>{{ \App\Models\Setting::get('about_paragraph_1', 'Founded with a passion for fine craftsmanship and timeless design, ' . config('app.name') . ' started with a simple mission — to bring exquisite, certified jewellery to every doorstep across India. We believe that beautiful jewellery should be accessible to everyone.') }}</p>
                        <p>{{ \App\Models\Setting::get('about_paragraph_2', 'Today, we\'ve grown into a trusted destination for thousands of customers. We partner directly with master artisans and certified manufacturers to ensure every piece meets the highest standards of purity and craftsmanship. From everyday gold essentials to stunning diamond collections.') }}</p>
                    @endif
                </div>
                <!-- Signature / Brand mark -->
                <div class="mt-8">
                    <span class="text-2xl text-[#B76E79] italic" style="font-family:'Playfair Display',Georgia,serif;">{{ config('app.name') }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         4. THREE SERVICES (Corano — line icons, 3 cols with dividers)
         ============================================ -->
    <section class="py-14 sm:py-20 border-y border-[#efefef]">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-0 max-w-5xl mx-auto">
                <!-- Service 1 -->
                <div class="text-center px-6 sm:px-8 py-6 sm:border-r border-[#efefef]">
                    <div class="mb-5">
                        <svg class="w-12 h-12 mx-auto text-[#222222]" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#222222] mb-3 uppercase tracking-wider">{{ \App\Models\Setting::get('about_usp_1_title', 'Creative Design') }}</h3>
                    <p class="text-xs text-[#777777] leading-relaxed">{{ \App\Models\Setting::get('about_usp_1_desc', 'Every piece is designed with passion and precision, blending traditional artistry with modern elegance.') }}</p>
                </div>
                <!-- Service 2 -->
                <div class="text-center px-6 sm:px-8 py-6 sm:border-r border-[#efefef] border-t sm:border-t-0">
                    <div class="mb-5">
                        <svg class="w-12 h-12 mx-auto text-[#222222]" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#222222] mb-3 uppercase tracking-wider">{{ \App\Models\Setting::get('about_usp_2_title', '100% Quality Guarantee') }}</h3>
                    <p class="text-xs text-[#777777] leading-relaxed">{{ \App\Models\Setting::get('about_usp_2_desc', 'All our jewellery is BIS hallmarked and certified. We stand behind the quality of every piece we sell.') }}</p>
                </div>
                <!-- Service 3 -->
                <div class="text-center px-6 sm:px-8 py-6 border-t sm:border-t-0">
                    <div class="mb-5">
                        <svg class="w-12 h-12 mx-auto text-[#222222]" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#222222] mb-3 uppercase tracking-wider">{{ \App\Models\Setting::get('about_usp_3_title', 'Online Support 24/7') }}</h3>
                    <p class="text-xs text-[#777777] leading-relaxed">{{ \App\Models\Setting::get('about_usp_3_desc', 'Our support team is always ready to help. Contact us anytime via WhatsApp, email, or phone.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         5. THREE CARDS — What We Do / Mission / History (Corano style)
         ============================================ -->
    <section class="py-14 sm:py-20 bg-[#f7f7f7]">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-5xl mx-auto">
                @php
                    $defaultCardImages = [
                        'images/dummy/cat-necklace.jpg',
                        'images/dummy/cat-ring.jpg',
                        'images/dummy/cat-bracelet.jpg',
                    ];
                    $aboutCards = [
                        [
                            'title' => \App\Models\Setting::get('about_card_1_title', 'What Do We Do?'),
                            'desc' => \App\Models\Setting::get('about_card_1_desc', 'We curate and deliver exquisite jewellery that combines traditional Indian craftsmanship with contemporary design. Every piece is handpicked for quality and beauty.'),
                            'img' => \App\Models\Setting::get('about_card_1_image', ''),
                        ],
                        [
                            'title' => \App\Models\Setting::get('about_card_2_title', 'Our Mission'),
                            'desc' => \App\Models\Setting::get('about_card_2_desc', 'To make fine jewellery accessible to every woman in India. We believe in transparent pricing, certified quality, and creating pieces that tell your unique story.'),
                            'img' => \App\Models\Setting::get('about_card_2_image', ''),
                        ],
                        [
                            'title' => \App\Models\Setting::get('about_card_3_title', 'History Of Us'),
                            'desc' => \App\Models\Setting::get('about_card_3_desc', 'Starting as a small family business, we have grown into a trusted online jewellery destination serving thousands of happy customers across India with hallmarked collections.'),
                            'img' => \App\Models\Setting::get('about_card_3_image', ''),
                        ],
                    ];
                @endphp
                @foreach($aboutCards as $card)
                    <div class="bg-white hover:shadow-md transition-shadow duration-300">
                        <!-- Card Image -->
                        <div class="overflow-hidden">
                            @if($card['img'])
                                <img src="{{ asset('storage/' . $card['img']) }}" alt="{{ $card['title'] }}" class="w-full h-48 sm:h-52 object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <img src="{{ asset($defaultCardImages[$loop->index]) }}" alt="{{ $card['title'] }}" class="w-full h-48 sm:h-52 object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                            @endif
                        </div>
                        <!-- Card Content -->
                        <div class="p-6">
                            <h3 class="text-base font-bold text-[#222222] mb-3">{{ $card['title'] }}</h3>
                            <p class="text-xs text-[#777777] leading-relaxed">{{ $card['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ============================================
         6. WHY CHOOSE US (accordion) + TESTIMONIALS (Corano 2-col)
         ============================================ -->
    <section class="py-14 sm:py-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 max-w-5xl mx-auto">

                <!-- LEFT: Why Choose Us — Accordion -->
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#222222] mb-2" style="font-family:'Playfair Display',Georgia,serif;">Why You Choose Us?</h2>
                    <p class="text-xs text-[#777777] mb-6 leading-relaxed">{{ \App\Models\Setting::get('about_why_subtitle', 'We believe in quality, transparency, and building lasting relationships with our customers.') }}</p>

                    <div x-data="{ open: 1 }" class="space-y-0">
                        @php
                            $faqs = [
                                ['title' => \App\Models\Setting::get('about_faq_1_title', 'Fast Free Delivery'), 'content' => \App\Models\Setting::get('about_faq_1_content', 'We offer free shipping on all orders above ₹499. Your jewellery is carefully packaged and delivered to your doorstep within 3-7 business days across India.')],
                                ['title' => \App\Models\Setting::get('about_faq_2_title', 'BIS Hallmarked & Certified'), 'content' => \App\Models\Setting::get('about_faq_2_content', 'Every piece of gold jewellery we sell is BIS hallmarked for purity. We provide authenticity certificates with all our products for your peace of mind.')],
                                ['title' => \App\Models\Setting::get('about_faq_3_title', '100% Anti-Tarnish Quality'), 'content' => \App\Models\Setting::get('about_faq_3_content', 'Our anti-tarnish collections are crafted with premium coatings to maintain their shine and beauty for years. Quality that stands the test of time.')],
                                ['title' => \App\Models\Setting::get('about_faq_4_title', '7 Days Easy Returns'), 'content' => \App\Models\Setting::get('about_faq_4_content', 'Not satisfied with your purchase? Simply return it within 7 days for a full refund. No questions asked, hassle-free process.')],
                            ];
                        @endphp
                        @foreach($faqs as $i => $faq)
                            <div class="{{ $loop->first ? 'border-t' : '' }} border-b border-[#efefef]">
                                <button @click="open = open === {{ $i + 1 }} ? 0 : {{ $i + 1 }}"
                                        class="w-full flex items-center justify-between px-5 py-3.5 text-left transition-colors duration-300"
                                        :class="open === {{ $i + 1 }} ? 'bg-[#B76E79] text-white' : 'bg-white text-[#222222] hover:text-[#B76E79]'">
                                    <span class="text-sm font-semibold">{{ $faq['title'] }}</span>
                                    <span class="text-lg font-light shrink-0 w-5 text-center" x-text="open === {{ $i + 1 }} ? '−' : '+'"></span>
                                </button>
                                <div x-show="open === {{ $i + 1 }}" x-collapse x-cloak class="px-5 py-4 bg-white">
                                    <p class="text-xs text-[#777777] leading-relaxed">{{ $faq['content'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- RIGHT: What Clients Say — Testimonial Carousel -->
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#222222] mb-6" style="font-family:'Playfair Display',Georgia,serif;">What Clients Say</h2>

                    @php
                        $testimonialItems = $testimonials->isNotEmpty()
                            ? $testimonials->map(fn($t) => ['name' => $t->name, 'content' => $t->content, 'title' => $t->title ?? 'Verified Buyer', 'avatar' => $t->avatar_url, 'initial' => strtoupper(substr($t->name, 0, 1))])
                            : collect([
                                ['name' => 'Priya Sharma', 'content' => 'Absolutely stunning jewellery! The craftsmanship is exquisite and the quality exceeded my expectations. Will definitely shop again.', 'title' => 'Verified Buyer', 'avatar' => null, 'initial' => 'P'],
                                ['name' => 'Anita Verma', 'content' => 'I ordered a gold necklace set for my wedding and it was perfect. Beautiful packaging, fast delivery, and the hallmark certificate gave me full confidence.', 'title' => 'Verified Buyer', 'avatar' => null, 'initial' => 'A'],
                                ['name' => 'Meera Patel', 'content' => 'The best online jewellery shopping experience I have had. The designs are unique, prices are transparent, and customer support is always helpful.', 'title' => 'Verified Buyer', 'avatar' => null, 'initial' => 'M'],
                            ]);
                    @endphp
                    <div x-data="{ current: 0, total: {{ $testimonialItems->count() }} }" x-init="setInterval(() => current = (current + 1) % total, 5000)">
                        <div class="relative" style="min-height: 320px;">
                            @foreach($testimonialItems as $ti)
                                <div x-show="current === {{ $loop->index }}"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-x-0 top-0 text-center">
                                    <!-- Avatar -->
                                    <div class="mb-4">
                                        @if($ti['avatar'])
                                            <img src="{{ Storage::url($ti['avatar']) }}" alt="{{ $ti['name'] }}" class="w-20 h-20 rounded-full object-cover mx-auto shadow-md border-3 border-white">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-[#f2f2f2] flex items-center justify-center mx-auto shadow-md border-3 border-white">
                                                <span class="text-2xl font-bold text-[#B76E79]">{{ $ti['initial'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Quote icon (closing quotes like Corano) -->
                                    <div class="text-[#c29958] text-3xl mb-3" style="font-family: Georgia, serif; line-height: 1;">&rdquo;</div>
                                    <!-- Content -->
                                    <p class="text-sm text-[#555555] leading-relaxed mb-5 max-w-md mx-auto">{{ $ti['content'] }}</p>
                                    <!-- Name -->
                                    <p class="text-sm font-semibold text-[#c29958] uppercase tracking-wider">{{ $ti['name'] }}</p>
                                    <p class="text-xs text-[#777777] mt-1">{{ $ti['title'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <!-- Dots (outside the relative container) -->
                        @if($testimonialItems->count() > 1)
                            <div class="flex items-center justify-center gap-3 pt-4">
                                @foreach($testimonialItems as $ti)
                                    <button @click="current = {{ $loop->index }}"
                                            class="w-3 h-3 rounded-full transition-colors duration-300"
                                            :class="current === {{ $loop->index }} ? 'bg-[#333]' : 'bg-[#ccc]'"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
