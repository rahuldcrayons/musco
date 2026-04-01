<x-layouts.app>
    <x-slot name="title">FAQ - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Frequently asked questions about {{ config('app.name') }}. Find answers about shipping, returns, sizing, orders, and more.">
        <link rel="canonical" href="{{ url('/faq') }}">
        <meta property="og:title" content="FAQ - {{ config('app.name') }}">
        <meta property="og:description" content="Frequently asked questions about {{ config('app.name') }}. Find answers about shipping, returns, sizing, orders, and more.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/faq') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="FAQ - {{ config('app.name') }}">
        <meta name="twitter:description" content="Find answers about shipping, returns, sizing, orders, and more at {{ config('app.name') }}.">

        @if($faqs->isNotEmpty())
            @php
                $faqSchema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => $faqs->flatten()->map(fn($faq) => [
                        '@type' => 'Question',
                        'name' => $faq->question,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq->answer,
                        ],
                    ])->values()->toArray(),
                ];
            @endphp
            <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-neutral-50 border-b border-neutral-100">
        <div class="container mx-auto px-4 py-3">
            <x-breadcrumb :items="[['label' => 'FAQ', 'url' => null]]" />
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 sm:py-12">
        <div class="max-w-3xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-8 sm:mb-10">
                <div class="w-14 h-14 mx-auto rounded-full bg-[#B76E79]/5 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-[#B76E79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 mb-1.5">Frequently Asked Questions</h1>
                <p class="text-[13px] text-neutral-600">Find answers to common questions about shopping with us.</p>
            </div>

            @if($faqs->isNotEmpty())
                <!-- FAQ Accordion -->
                <div x-data="{ open: null }" class="space-y-3">
                    @php $counter = 0; @endphp
                    @foreach($faqs as $category => $items)
                        <p class="text-xs font-semibold text-[#B76E79] uppercase tracking-wider {{ $loop->first ? 'pt-2' : 'pt-4' }} pb-1">{{ $category }}</p>

                        @foreach($items as $faq)
                            @php $counter++; @endphp
                            <div class="bg-white border border-neutral-100 rounded-xl overflow-hidden">
                                <button @click="open = open === {{ $counter }} ? null : {{ $counter }}"
                                        class="w-full px-5 py-3.5 flex items-center justify-between text-left gap-3 hover:bg-neutral-50/50 transition-colors">
                                    <span class="text-sm font-medium text-neutral-900">{{ $faq->question }}</span>
                                    <svg class="w-4 h-4 text-neutral-600 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open === {{ $counter }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open === {{ $counter }}" x-collapse>
                                    <div class="px-5 pb-4 text-[13px] text-neutral-600 leading-relaxed border-t border-neutral-50">
                                        <div class="pt-3">{!! nl2br(e($faq->answer)) !!}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-neutral-100 rounded-xl p-8 text-center">
                    <p class="text-sm text-neutral-600">No FAQs available yet. Please check back later.</p>
                </div>
            @endif

            <!-- Still Need Help -->
            <div class="mt-10 bg-white border border-neutral-100 rounded-xl p-6 sm:p-8 text-center">
                <div class="w-11 h-11 mx-auto rounded-full bg-[#B76E79]/5 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-[#B76E79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-bold text-neutral-900 mb-1">Still have questions?</h3>
                <p class="text-[13px] text-neutral-600 mb-4">Can't find what you're looking for? We're here to help.</p>
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#B76E79] via-[#B76E79] to-[#222222] hover:from-[#222222] hover:via-[#222222] hover:to-[#D47200] text-white text-sm font-semibold rounded-xl shadow-lg shadow-[#B76E79]/25 hover:shadow-[#B76E79]/40 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
