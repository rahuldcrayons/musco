{{--
    Trendymus Premium Footer
    Usage: @include('premium._footer')
    Dark footer — bg-[#0D0D0D] text-white
--}}

<footer role="contentinfo" class="bg-[#0D0D0D] text-white" style="border-top: 1px solid rgba(255,255,255,0.07);">

    {{-- ═══════════════════════════════════
         TOP SECTION: Logo + Tagline + Socials
    ════════════════════════════════════ --}}
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 pt-14 pb-10 border-b border-white/6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            {{-- Logo + Tagline --}}
            <div class="flex flex-col gap-3">
                <a href="/" class="inline-block" aria-label="Trendymus">
                    <span
                        class="font-serif italic text-3xl text-[#202a40]"
                        style="font-family:'Playfair Display',serif; letter-spacing:-0.01em;"
                    >Trendymus</span>
                </a>
                <p class="text-sm text-white/35 max-w-xs leading-relaxed">
                    Timeless jewellery crafted with precision. Hallmarked gold, diamonds &amp; silver for every occasion.
                </p>
            </div>

            {{-- Social icons --}}
            <div class="flex items-center gap-3">
                <span class="text-xs text-white/25 uppercase tracking-widest font-semibold mr-1 hidden sm:block">Follow Us</span>

                {{-- Instagram --}}
                <a
                    href="{{ \App\Models\Setting::get('social_instagram', 'https://instagram.com/trendymus') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group w-10 h-10 rounded-full border border-white/12 flex items-center justify-center text-white/40 hover:text-[#202a40] hover:border-[#202a40]/40 hover:bg-[#202a40]/8 transition-all duration-300"
                    aria-label="Instagram"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>

                {{-- Facebook --}}
                <a
                    href="{{ \App\Models\Setting::get('social_facebook', '#') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group w-10 h-10 rounded-full border border-white/12 flex items-center justify-center text-white/40 hover:text-[#202a40] hover:border-[#202a40]/40 hover:bg-[#202a40]/8 transition-all duration-300"
                    aria-label="Facebook"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>


                {{-- TikTok --}}
                <a
                    href="{{ \App\Models\Setting::get('social_tiktok', '#') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group w-10 h-10 rounded-full border border-white/12 flex items-center justify-center text-white/40 hover:text-[#202a40] hover:border-[#202a40]/40 hover:bg-[#202a40]/8 transition-all duration-300"
                    aria-label="TikTok"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                    </svg>
                </a>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════
         MAIN 4-COLUMN LINK GRID
    ════════════════════════════════════ --}}
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-10">

            {{-- Column 1: Company --}}
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-[0.15em] text-white/30 mb-5">Company</h4>
                <ul class="space-y-3.5">
                    @foreach([
                        ['About Us', '/about'],
                        ['Careers', '/careers'],
                        ['Press & Media', '/press'],
                        ['Blog', '/blog'],
                        ['Affiliate Program', '/affiliates'],
                        ['Store Locator', '/stores'],
                    ] as [$label, $href])
                    <li>
                        <a
                            href="{{ $href }}"
                            class="text-sm text-white/45 hover:text-[#C9A96E] transition-colors duration-200 font-medium"
                        >{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 2: Shop --}}
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-[0.15em] text-white/30 mb-5">Shop</h4>
                <ul class="space-y-3.5">
                    @foreach([
                        ['All Jewellery', '/products'],
                        ['New Arrivals', '/collections/new-arrivals'],
                        ['Best Sellers', '/collections/best-sellers'],
                        ['Sale', '/sale'],
                        ['Gift Cards', '/gift-cards'],
                        ['Customise', '/customise'],
                    ] as [$label, $href])
                    <li>
                        <a
                            href="{{ $href }}"
                            class="text-sm text-white/45 hover:text-[#C9A96E] transition-colors duration-200 font-medium"
                        >{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 3: Support --}}
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-[0.15em] text-white/30 mb-5">Support</h4>
                <ul class="space-y-3.5">
                    @foreach([
                        ['FAQ', '/faq'],
                        ['Shipping Info', '/shipping'],
                        ['Returns & Refunds', '/returns'],
                        ['Contact Us', '/contact'],
                        ['Track Order', '/track-order'],
                        ['Size Guide', '/size-guide'],
                    ] as [$label, $href])
                    <li>
                        <a
                            href="{{ $href }}"
                            class="text-sm text-white/45 hover:text-[#C9A96E] transition-colors duration-200 font-medium"
                        >{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>

                {{-- WhatsApp CTA --}}
                <a
                    href="https://wa.me/919999999999"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 mt-5 px-3.5 py-2 bg-[#25D366]/12 border border-[#25D366]/25 text-[#25D366] text-xs font-semibold rounded-full hover:bg-[#25D366]/20 transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat with us
                </a>
            </div>

            {{-- Column 4: Legal --}}
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-[0.15em] text-white/30 mb-5">Legal</h4>
                <ul class="space-y-3.5">
                    @foreach([
                        ['Privacy Policy', '/privacy'],
                        ['Terms of Service', '/terms'],
                        ['Cookie Policy', '/cookie-policy'],
                        ['GDPR Compliance', '/gdpr'],
                        ['Disclaimer', '/disclaimer'],
                    ] as [$label, $href])
                    <li>
                        <a
                            href="{{ $href }}"
                            class="text-sm text-white/45 hover:text-[#C9A96E] transition-colors duration-200 font-medium"
                        >{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>

                {{-- Certifications --}}
                <div class="mt-6 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-white/6 border border-white/10 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-[#C9A96E]" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.25"><path d="M7 1l1.5 3h3l-2.5 2 1 3L7 7.5 4 9l1-3L2.5 4h3z"/></svg>
                        </div>
                        <span class="text-[11px] text-white/30 font-medium">BIS Hallmarked</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-white/6 border border-white/10 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-[#202a40]" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.25"><path d="M7 1l5 2.5v4c0 3-5 5.5-5 5.5S2 10.5 2 7.5v-4L7 1z"/></svg>
                        </div>
                        <span class="text-[11px] text-white/30 font-medium">SSL Secured</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════
         NEWSLETTER SIGNUP
    ════════════════════════════════════ --}}
    <div
        class="border-y border-white/6"
        x-data="{ email: '', submitted: false, error: false, submit() { if (!this.email.includes('@')) { this.error = true; return; } this.submitted = true; this.error = false; } }"
    >
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 py-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 lg:gap-12">

                <div class="shrink-0">
                    <h3 class="font-serif text-lg text-white" style="font-family:'Playfair Display',serif;">Stay in the loop</h3>
                    <p class="text-sm text-white/35 mt-1">New arrivals, exclusive deals & style inspiration.</p>
                </div>

                <div class="flex-1 max-w-md w-full">
                    <div x-show="!submitted" class="flex gap-2">
                        <div class="flex-1 relative">
                            <input
                                type="email"
                                x-model="email"
                                @keydown.enter="submit()"
                                placeholder="Enter your email address"
                                class="w-full px-4 py-3 bg-white/6 border rounded-xl text-sm text-white placeholder-white/25 focus:outline-none transition-all"
                                :class="error ? 'border-red-500/50 focus:border-red-400' : 'border-white/12 focus:border-[#202a40]/60 focus:bg-white/10'"
                            >
                            <p x-show="error" class="absolute -bottom-5 left-0 text-[11px] text-red-400">Please enter a valid email address.</p>
                        </div>
                        <button
                            @click="submit()"
                            class="px-5 py-3 bg-[#202a40] text-white text-sm font-semibold rounded-xl hover:bg-[#a05c68] transition-colors shrink-0 whitespace-nowrap"
                        >
                            Subscribe
                        </button>
                    </div>
                    <div x-show="submitted" class="flex items-center gap-3 py-3">
                        <div class="w-7 h-7 rounded-full bg-[#202a40]/20 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-[#202a40]" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 7l3.5 3.5L12 3"/></svg>
                        </div>
                        <p class="text-sm text-white/60">You're in! Welcome to the Trendymus family. ✨</p>
                    </div>
                    <p class="text-[11px] text-white/20 mt-2.5">No spam. Unsubscribe anytime. View our <a href="/privacy" class="underline hover:text-white/40">Privacy Policy</a>.</p>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════
         BOTTOM BAR
    ════════════════════════════════════ --}}
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 py-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-5">

            {{-- Copyright + Made in India --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-5 text-center sm:text-left">
                <p class="text-xs text-white/25">
                    &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'Trendymus') }}. All rights reserved.
                </p>
            <p class="text-xs text-neutral-400 mt-2">Registered in England and Wales. All prices include VAT where applicable.</p>
                <span class="hidden sm:block w-px h-3 bg-white/10"></span>
                <div class="flex items-center gap-1.5 text-xs text-white/20">
                    <svg class="w-3.5 h-3.5 text-[#FF9933]" viewBox="0 0 14 14" fill="currentColor"><circle cx="7" cy="7" r="7"/></svg>
                    Made with love in the UK 🇬🇧
                </div>
            </div>

            {{-- Payment icons --}}
            <div class="flex items-center gap-2.5 flex-wrap justify-center md:justify-end">
                <span class="text-[10px] text-white/20 uppercase tracking-widest font-semibold hidden sm:block mr-1">We accept</span>

                {{-- Visa --}}
                <div class="h-7 px-2 bg-white/6 border border-white/10 rounded-md flex items-center justify-center">
                    <svg class="h-4 w-auto" viewBox="0 0 48 20" fill="none">
                        <path d="M18.5 18.5H15l2-12h3.5L18.5 18.5zm10-12c-.5-.2-1.4-.4-2.4-.4-2.7 0-4.5 1.4-4.5 3.4 0 1.5 1.4 2.3 2.4 2.8 1 .5 1.4.8 1.4 1.3 0 .7-.8 1-1.6 1-1.1 0-1.6-.2-2.5-.5l-.3-.2-.4 2.2c.6.3 1.8.5 3 .5 2.8 0 4.7-1.4 4.7-3.5 0-1.2-.7-2.1-2.3-2.8-.9-.5-1.5-.8-1.5-1.3 0-.4.5-.9 1.5-.9.9 0 1.5.2 2 .4l.2.1.3-2.1zM33 6.5h-2.1c-.7 0-1.1.2-1.4.8L25.8 18.5h2.8l.6-1.5h3.5l.3 1.5H35L33 6.5zm-3.4 7 1.1-3 .3-.8.2.7.6 3.1h-2.2zM14 6.5l-2.5 7.2-.3-1.3c-.5-1.6-2-3.4-3.7-4.3l2.4 9h2.9l4.3-10.5H14z" fill="white" opacity="0.6"/>
                        <path d="M10 6.5H5.8l-.1.3c3.4.9 5.7 3 6.6 5.5L11 7.5c-.1-.7-.6-.9-1.3-1z" fill="#F9A533" opacity="0.8"/>
                    </svg>
                </div>

                {{-- Mastercard --}}
                <div class="h-7 px-2 bg-white/6 border border-white/10 rounded-md flex items-center justify-center">
                    <svg class="h-5 w-auto" viewBox="0 0 32 20" fill="none">
                        <circle cx="12" cy="10" r="7" fill="#EB001B" opacity="0.85"/>
                        <circle cx="20" cy="10" r="7" fill="#F79E1B" opacity="0.85"/>
                        <path d="M16 4.7a7 7 0 010 10.6A7 7 0 0116 4.7z" fill="#FF5F00" opacity="0.9"/>
                    </svg>
                </div>

                {{-- RazorPay --}}
                <div class="h-7 px-2.5 bg-white/6 border border-white/10 rounded-md flex items-center justify-center">
                    <span class="text-[10px] font-bold text-white/50 tracking-wide">Stripe</span>
                </div>

                {{-- Secure --}}
                <div class="h-7 px-2.5 bg-white/6 border border-white/10 rounded-md flex items-center justify-center">
                    <span class="text-[10px] font-bold text-white/50 tracking-wider">Secure</span>
                </div>

                {{-- COD --}}
                <div class="h-7 px-2.5 bg-white/6 border border-white/10 rounded-md flex items-center justify-center">
                    <span class="text-[10px] font-bold text-white/50 tracking-wider">COD</span>
                </div>

                {{-- Net Banking --}}
                <div class="h-7 px-2.5 bg-white/6 border border-white/10 rounded-md flex items-center justify-center">
                    <span class="text-[10px] font-bold text-white/50 tracking-tight">Net Banking</span>
                </div>

            </div>

        </div>

        {{-- Developer credit --}}
        <div class="mt-4 text-center md:text-right">
            <p class="text-[10px] text-white/12">
                Designed &amp; developed by <a href="https://dcrayons.com" target="_blank" rel="noopener noreferrer" class="hover:text-white/25 transition-colors">Dcrayons</a>
            </p>
        </div>

    </div>

</footer>
