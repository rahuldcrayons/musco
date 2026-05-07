<footer class="mt-auto" style="background: #202a40;">
    <!-- Rose gold top accent line -->
    <div style="height:3px; background: linear-gradient(90deg, #506282 0%, #202a40 50%, #506282 100%);"></div>

    <!-- Main footer -->
    <div class="py-12 lg:py-16">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-0 lg:gap-8 items-start divide-y divide-white/10 lg:divide-y-0">

                <!-- About -->
                <div class="lg:col-span-1 pb-4 lg:pb-0">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-5">
                        @php
                            $footerLogo = \App\Models\Setting::get('site_logo', '');
                            $footerAbout = \App\Models\Setting::get('footer_about', '');
                        @endphp
                        @if($footerLogo)
                            <img src="{{ asset('storage/' . $footerLogo) }}" alt="{{ config('app.name') }}" class="w-auto object-contain" style="width:180px;">
                        @else
                            <span style="font-size:1.6rem; font-weight:700; letter-spacing:-0.02em; line-height:1;">
                                <span style="color:#c8d6e5;">Trendy</span><span style="color:#7a9ab8;">mus</span>
                            </span>
                        @endif
                    </a>
                    @php $cleanAbout = trim($footerAbout); @endphp
                    @if($cleanAbout && $cleanAbout !== 'Voluptatem hic aliqu')
                    <p class="text-sm mb-6 leading-relaxed max-w-sm" style="color: rgba(255,255,255,0.75);">
                        {{ $cleanAbout }}
                    </p>
                    @endif

                    <!-- Social Icons -->
                    <div class="flex gap-3">
                        @php
                            $socialFacebook  = \App\Models\Setting::get('social_facebook', 'https://www.facebook.com/');
                            $socialInstagram = \App\Models\Setting::get('social_instagram', 'https://www.instagram.com/');
                            $socialTiktok    = \App\Models\Setting::get('social_tiktok', 'https://www.tiktok.com/@trendymus_');
                        @endphp
                        @if($socialFacebook)
                            <a href="{{ $socialFacebook }}" class="w-9 h-9 rounded-full flex items-center justify-center transition-all bg-[#1877F2] hover:opacity-80" aria-label="Facebook" target="_blank">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                        @if($socialInstagram)
                            <a href="{{ $socialInstagram }}" class="w-9 h-9 rounded-full flex items-center justify-center transition-all hover:opacity-80" style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%)" aria-label="Instagram" target="_blank">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            </a>
                        @endif
                        @if($socialTiktok)
                            <a href="{{ $socialTiktok }}" class="w-9 h-9 rounded-full flex items-center justify-center transition-all bg-[#010101] hover:opacity-80" aria-label="TikTok" target="_blank">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                            </a>
                        @endif
                    </div>

                    <!-- Contact Info -->
                    <div class="mt-5 space-y-2">
                        @php
                            $contactPhone    = \App\Models\Setting::get('contact_phone', '+44 7459914080');
                            $contactWhatsapp = \App\Models\Setting::get('contact_whatsapp', '447459914080');
                        @endphp
                        <a href="https://wa.me/{{ $contactWhatsapp }}" target="_blank"
                           class="flex items-center gap-2 text-sm transition-colors"
                           style="color: rgba(255,255,255,0.7);"
                           onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            {{ $contactPhone }}
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div x-data="{ open: false }" class="py-3 lg:py-0">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between lg:cursor-default lg:pointer-events-none py-1"
                            type="button">
                        <h4 class="text-sm font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.9);">Quick Links</h4>
                        <svg class="w-4 h-4 lg:hidden transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <ul x-show="open || window.innerWidth >= 1024"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="space-y-3 text-sm mt-3">
                        <li><a href="{{ route('about') }}"            class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">About Us</a></li>
                        <li><a href="{{ route('contact') }}"          class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Contact Us</a></li>
                        <li><a href="{{ route('faq') }}"              class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">FAQs</a></li>
                        <li><a href="{{ route('blog') }}"             class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Blog</a></li>
                        <li><a href="{{ route('categories.index') }}" class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">All Categories</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div x-data="{ open: false }" class="py-3 lg:py-0">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between lg:cursor-default lg:pointer-events-none py-1"
                            type="button">
                        <h4 class="text-sm font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.9);">Customer Service</h4>
                        <svg class="w-4 h-4 lg:hidden transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <ul x-show="open || window.innerWidth >= 1024"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="space-y-3 text-sm mt-3">
                        <li><a href="{{ route('help') }}"        class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Help Center</a></li>
                        <li><a href="{{ route('track-order') }}" class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Track Order</a></li>
                        <li><a href="{{ route('returns') }}"     class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Returns & Refunds</a></li>
                        <li><a href="{{ route('shipping') }}"    class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Shipping Info</a></li>
                    </ul>
                </div>

                <!-- Policies -->
                <div x-data="{ open: false }" class="py-3 lg:py-0">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between lg:cursor-default lg:pointer-events-none py-1"
                            type="button">
                        <h4 class="text-sm font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.9);">Policies</h4>
                        <svg class="w-4 h-4 lg:hidden transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <ul x-show="open || window.innerWidth >= 1024"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="space-y-3 text-sm mt-3">
                        <li><a href="{{ route('privacy') }}"      class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}"         class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Terms of Service</a></li>
                        <li><a href="{{ route('cookie-policy') }}" class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">Cookie Policy</a></li>
                        <li><a href="{{ route('gdpr') }}"          class="transition-colors" style="color:rgba(255,255,255,0.75);" onmouseover="this.style.color='#a8bfd4'" onmouseout="this.style.color='rgba(255,255,255,0.75)'">GDPR Compliance</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- Bottom bar -->
    <div class="py-5 pb-safe-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
        <div class="container mx-auto px-4 text-center">
            <p class="text-xs" style="color: rgba(255,255,255,0.45);">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'Trendymus') }}. All rights reserved.
            </p>
            <p class="text-[11px] mt-1" style="color: rgba(255,255,255,0.3);">Registered in England and Wales. All prices include VAT where applicable.</p>
        </div>
    </div>
</footer>
