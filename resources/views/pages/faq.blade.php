<x-layouts.app>
    <x-slot name="title">FAQ &mdash; {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Frequently asked questions about {{ config('app.name') }}.">
        <link rel="canonical" href="{{ url('/faq') }}">
        @if($faqs->isNotEmpty())
            @php
                $faqSchema = [
                    '@context'   => 'https://schema.org',
                    '@type'      => 'FAQPage',
                    'mainEntity' => $faqs->flatten()->map(fn($f) => [
                        '@type'          => 'Question',
                        'name'           => $f->question,
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->answer],
                    ])->values()->toArray(),
                ];
            @endphp
            <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
        <meta property="og:image" content="{{ asset('images/og-default.png') }}">
        <meta name="twitter:image" content="{{ asset('images/og-default.png') }}">
    @endpush

    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .faq-answer-text { color: #333333 !important; font-size: 13.5px; line-height: 1.8; }
    </style>

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-neutral-100">
        <div class="container mx-auto px-4 py-3">
            <x-breadcrumb :items="[['label' => 'FAQ', 'url' => null]]" />
        </div>
    </div>

    @php
        $categories = $faqs->keys()->filter()->values();
        $faqData    = $faqs->map(fn($items, $cat) =>
            $items->map(fn($f) => [
                'id'       => $f->id,
                'category' => $cat ?: 'All',
                'question' => $f->question,
                'answer'   => $f->answer,
            ])->values()
        )->flatten(1)->values();
    @endphp

    <div class="bg-[#f4f5f7] min-h-screen"
         x-data="{
             activeTab: 'All',
             openId: null,
             search: '',
             faqs: {{ Js::from($faqData) }},
             tabs: {{ Js::from($categories->prepend('All')->values()) }},
             get filtered() {
                 let list = this.faqs;
                 if (this.activeTab !== 'All') {
                     list = list.filter(f => f.category === this.activeTab);
                 }
                 const q = this.search.trim().toLowerCase();
                 if (q) list = list.filter(f =>
                     f.question.toLowerCase().includes(q) || f.answer.toLowerCase().includes(q)
                 );
                 return list;
             },
             get leftCol()  { return this.filtered.filter((_, i) => i % 2 === 0); },
             get rightCol() { return this.filtered.filter((_, i) => i % 2 !== 0); },
             toggle(id) { this.openId = (this.openId === id) ? null : id; }
         }">

        {{-- ═══ HERO ═══════════════════════════════════════════════ --}}
        <div class="bg-[#202a40] px-4 pt-14 pb-16 text-center">
            <span class="inline-block text-[10px] font-bold uppercase tracking-[0.3em] text-white/40 mb-3">Help Center</span>
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3"
                style="font-family:'Playfair Display',Georgia,serif;">
                Frequently Asked Questions
            </h1>
            <p class="text-[13px] max-w-sm mx-auto mb-10 leading-relaxed" style="color: rgba(255,255,255,0.55);">
                Find quick answers about orders, shipping, payments, returns, and more.
            </p>

            {{-- Search --}}
            <div class="relative max-w-xl mx-auto">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400 pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-3.5-3.5"/>
                </svg>
                <input type="search"
                       x-model="search"
                       @input="openId = null"
                       placeholder="Search questions…"
                       aria-label="Search FAQ"
                       class="w-full pl-12 pr-10 py-4 rounded-xl bg-white text-[14px] text-neutral-800
                              placeholder-neutral-400 shadow-lg outline-none focus:ring-2 focus:ring-white/30 transition" />
                <button x-show="search.trim()" x-cloak
                        @click="search = ''; openId = null"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center
                               rounded-full bg-neutral-100 hover:bg-neutral-200 transition-colors">
                    <svg class="w-3 h-3 text-neutral-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ═══ TABS ════════════════════════════════════════════════ --}}
        @if($categories->count() > 0)
        <div class="bg-white border-b border-neutral-100 sticky top-0 z-20 shadow-sm">
            <div class="container mx-auto px-4">
                <div class="flex items-center overflow-x-auto scrollbar-hide"
                     role="tablist" aria-label="FAQ Categories">
                    <template x-for="tab in tabs" :key="tab">
                        <button type="button"
                                role="tab"
                                :aria-selected="activeTab === tab ? 'true' : 'false'"
                                @click="activeTab = tab; openId = null; search = ''"
                                class="shrink-0 px-5 py-3.5 text-[13px] font-semibold whitespace-nowrap
                                       border-b-2 transition-all duration-150 focus:outline-none"
                                :class="activeTab === tab
                                    ? 'border-[#202a40] text-[#202a40]'
                                    : 'border-transparent text-neutral-500 hover:text-neutral-800'"
                                x-text="tab">
                        </button>
                    </template>
                </div>
            </div>
        </div>
        @endif

        {{-- ═══ CONTENT ═════════════════════════════════════════════ --}}
        <div class="container mx-auto px-4 py-10 pb-16">
            <div class="max-w-6xl mx-auto">

                {{-- Result count --}}
                <p x-show="search.trim() !== ''" x-cloak
                   class="text-center text-[13px] text-neutral-500 mb-6">
                    Showing <strong class="text-[#202a40]" x-text="filtered.length"></strong>&nbsp;<span x-text="filtered.length === 1 ? 'result' : 'results'"></span>
                    for "<em x-text="search"></em>"
                </p>

                {{-- Empty state --}}
                <div x-show="filtered.length === 0" x-cloak class="text-center py-24">
                    <svg class="w-12 h-12 text-neutral-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/>
                    </svg>
                    <p class="text-[15px] font-semibold text-neutral-700 mb-1">No results found</p>
                    <p class="text-[13px] text-neutral-400">Try a different keyword or choose another category.</p>
                </div>

                {{-- ═══ 2-COLUMN GRID &mdash; each column its own DOM subtree ═══ --}}
                <div x-show="filtered.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                    {{-- LEFT COLUMN --}}
                    <div class="space-y-3">
                        <template x-for="faq in leftCol" :key="faq.id">
                            <div class="bg-white rounded-xl border transition-colors duration-200"
                                 :class="openId === faq.id
                                     ? 'border-[#202a40]/30 shadow-md'
                                     : 'border-neutral-200 shadow-sm hover:border-[#202a40]/20 hover:shadow-md'">

                                {{-- Question --}}
                                <button type="button"
                                        @click="toggle(faq.id)"
                                        :aria-expanded="openId === faq.id ? 'true' : 'false'"
                                        class="w-full min-h-[58px] flex items-center justify-between gap-4
                                               px-5 py-4 text-left focus:outline-none group rounded-xl">
                                    <span class="flex-1 text-[14px] font-semibold leading-snug transition-colors"
                                          :class="openId === faq.id ? 'text-[#202a40]' : 'text-[#1a1a1a] group-hover:text-[#202a40]'"
                                          x-text="faq.question"></span>

                                    {{-- + icon --}}
                                    <svg x-show="openId !== faq.id"
                                         class="shrink-0 w-7 h-7 p-1.5 rounded-lg border-2 border-neutral-300
                                                group-hover:border-[#202a40]/50 transition-colors text-neutral-500 group-hover:text-[#202a40]"
                                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                    </svg>
                                    {{-- - icon --}}
                                    <svg x-show="openId === faq.id" style="display:none"
                                         class="shrink-0 w-7 h-7 p-1.5 rounded-lg bg-[#202a40] border-2 border-[#202a40] text-white"
                                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                    </svg>
                                </button>

                                {{-- Answer --}}
                                <div x-show="openId === faq.id"
                                     style="display:none"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0">
                                    <div class="px-5 pb-5 pt-3 border-t border-neutral-100 bg-neutral-50 rounded-b-xl">
                                        <p class="faq-answer-text" x-text="faq.answer"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-3">
                        <template x-for="faq in rightCol" :key="faq.id">
                            <div class="bg-white rounded-xl border transition-colors duration-200"
                                 :class="openId === faq.id
                                     ? 'border-[#202a40]/30 shadow-md'
                                     : 'border-neutral-200 shadow-sm hover:border-[#202a40]/20 hover:shadow-md'">

                                {{-- Question --}}
                                <button type="button"
                                        @click="toggle(faq.id)"
                                        :aria-expanded="openId === faq.id ? 'true' : 'false'"
                                        class="w-full min-h-[58px] flex items-center justify-between gap-4
                                               px-5 py-4 text-left focus:outline-none group rounded-xl">
                                    <span class="flex-1 text-[14px] font-semibold leading-snug transition-colors"
                                          :class="openId === faq.id ? 'text-[#202a40]' : 'text-[#1a1a1a] group-hover:text-[#202a40]'"
                                          x-text="faq.question"></span>

                                    {{-- + icon --}}
                                    <svg x-show="openId !== faq.id"
                                         class="shrink-0 w-7 h-7 p-1.5 rounded-lg border-2 border-neutral-300
                                                group-hover:border-[#202a40]/50 transition-colors text-neutral-500 group-hover:text-[#202a40]"
                                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                    </svg>
                                    {{-- - icon --}}
                                    <svg x-show="openId === faq.id" style="display:none"
                                         class="shrink-0 w-7 h-7 p-1.5 rounded-lg bg-[#202a40] border-2 border-[#202a40] text-white"
                                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                    </svg>
                                </button>

                                {{-- Answer --}}
                                <div x-show="openId === faq.id"
                                     style="display:none"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0">
                                    <div class="px-5 pb-5 pt-3 border-t border-neutral-100 bg-neutral-50 rounded-b-xl">
                                        <p class="faq-answer-text" x-text="faq.answer"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>

        {{-- ═══ CTA ════════════════════════════════════════════════ --}}
        <div class="container mx-auto px-4 pb-16">
            <div class="max-w-6xl mx-auto bg-[#202a40] rounded-2xl p-8 sm:p-10
                        flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-[17px] font-bold text-white mb-1">Still have questions?</h3>
                    <p class="text-[13px] leading-relaxed" style="color:rgba(255,255,255,0.55)">
                        Can't find the answer? Our support team is ready to help.
                    </p>
                </div>
                <a href="{{ route('contact') }}"
                   class="shrink-0 inline-flex items-center gap-2 px-7 py-3 bg-white text-[#202a40]
                          text-[13.5px] font-bold rounded-xl hover:bg-neutral-100 transition-colors shadow-sm">
                    Contact Support
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>
</x-layouts.app>
