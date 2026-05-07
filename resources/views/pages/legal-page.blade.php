<x-layouts.app>
    <x-slot name="title">{{ $page->seo_data['meta_title'] ?? $page->title }} - {{ config('app.name') }}</x-slot>

    @push('meta')
        @if(!empty($page->seo_data['meta_description']))
            <meta name="description" content="{{ $page->seo_data['meta_description'] }}">
        @endif
        <link rel="canonical" href="{{ url()->current() }}">
    @endpush

    <div class="bg-neutral-50 border-b border-neutral-100">
        <div class="container mx-auto px-4 py-3">
            <x-breadcrumb :items="[['label' => $page->title, 'url' => null]]" />
        </div>
    </div>

    @php
        $iconBg = match($page->slug) {
            'privacy-policy'   => 'bg-[#202a40]/5',
            'terms-of-service' => 'bg-neutral-100',
            'cookie-policy'    => 'bg-[#506282]/10',
            'gdpr'             => 'bg-primary-50',
            'returns-policy'   => 'bg-warning-50',
            'shipping-policy'  => 'bg-[#202a40]/5',
            'size-guide'       => 'bg-[#202a40]/5',
            'help-center'      => 'bg-[#202a40]/5',
            'contact-us'       => 'bg-[#202a40]/5',
            default            => 'bg-neutral-100',
        };
        $iconColor = match($page->slug) {
            'privacy-policy'   => 'text-[#202a40]',
            'terms-of-service' => 'text-neutral-600',
            'cookie-policy'    => 'text-[#506282]',
            'gdpr'             => 'text-primary-600',
            'returns-policy'   => 'text-warning-600',
            'shipping-policy'  => 'text-[#202a40]',
            'size-guide'       => 'text-[#202a40]',
            'help-center'      => 'text-[#202a40]',
            'contact-us'       => 'text-[#202a40]',
            default            => 'text-neutral-600',
        };

        // Split content into separate sections at every <h2> boundary
        $sections = [];
        if ($page->content) {
            $rawParts = preg_split('/(?=<h2[\s>])/i', $page->content);
            foreach ($rawParts as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $sections[] = $part;
                }
            }
        }
    @endphp

    <div class="container mx-auto px-4 py-8 sm:py-12">
        <div class="max-w-3xl mx-auto">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-14 h-14 mx-auto rounded-full {{ $iconBg }} flex items-center justify-center mb-4">
                    @if($page->slug === 'privacy-policy')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    @elseif($page->slug === 'cookie-policy')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @elseif($page->slug === 'gdpr')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @elseif($page->slug === 'returns-policy')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    @elseif($page->slug === 'size-guide')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    @elseif($page->slug === 'help-center')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    @elseif($page->slug === 'contact-us')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    @elseif($page->slug === 'shipping-policy')
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                    @else
                        <svg class="w-7 h-7 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    @endif
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-neutral-900">{{ $page->title }}</h1>
                <p class="text-[13px] text-neutral-600 mt-2">
                    Last updated: {{ ($page->updated_at ?? $page->published_at ?? now())->format('F Y') }}
                    &middot; Please read this document carefully.
                </p>
            </div>

            {{-- Section Cards --}}
            @if($sections)
                @foreach($sections as $section)
                    <div class="bg-white border border-neutral-100 rounded-xl p-5 sm:p-6 mb-4
                                [&_h2]:text-[15px] [&_h2]:font-bold [&_h2]:text-neutral-900 [&_h2]:mb-3
                                [&_h3]:text-[13px] [&_h3]:font-semibold [&_h3]:text-neutral-900 [&_h3]:mb-2 [&_h3]:mt-3
                                [&_p]:text-[13px] [&_p]:text-neutral-600 [&_p]:leading-relaxed [&_p]:mb-2
                                [&_ul]:mt-2 [&_ul]:space-y-1.5 [&_ul]:list-disc [&_ul]:pl-5
                                [&_ol]:mt-2 [&_ol]:space-y-1.5 [&_ol]:list-decimal [&_ol]:pl-5
                                [&_li]:text-[13px] [&_li]:text-neutral-600 [&_li]:leading-relaxed [&_li]:marker:text-neutral-600
                                [&_a]:text-primary-600 [&_a]:underline [&_a]:underline-offset-2
                                [&_table]:w-full [&_table]:mt-2 [&_table]:text-[13px] [&_table]:border-collapse
                                [&_th]:text-left [&_th]:py-2 [&_th]:px-3 [&_th]:bg-neutral-50 [&_th]:text-neutral-700 [&_th]:font-semibold [&_th]:text-[12px] [&_th]:border [&_th]:border-neutral-200
                                [&_td]:py-2 [&_td]:px-3 [&_td]:text-neutral-600 [&_td]:border [&_td]:border-neutral-100
                                [&_strong]:font-semibold [&_strong]:text-neutral-800
                                [&_blockquote]:border-l-4 [&_blockquote]:border-neutral-200 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-neutral-600">
                        {{-- Render each section's raw HTML --}}
                        {!! $section !!}
                    </div>
                @endforeach
            @else
                <div class="bg-white border border-neutral-100 rounded-xl p-5 sm:p-6 mb-4">
                    <p class="text-[13px] text-neutral-600 italic text-center">Content coming soon.</p>
                </div>
            @endif

            {{-- Footer / Related links --}}
            <div class="bg-white border border-neutral-100 rounded-xl p-5 sm:p-6 text-center">
                <h2 class="text-[15px] font-bold text-neutral-900 mb-2">Have Questions?</h2>
                <p class="text-[13px] text-neutral-600 mb-4">Our support team is happy to help with any queries.</p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-[#202a40] border border-[#202a40] rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Contact Us
                    </a>
                    @if($page->slug !== 'privacy-policy')
                        <a href="{{ route('privacy') }}" class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-neutral-700 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors">
                            Privacy Policy
                        </a>
                    @endif
                    @if($page->slug !== 'terms-of-service')
                        <a href="{{ route('terms') }}" class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-neutral-700 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors">
                            Terms of Service
                        </a>
                    @endif
                    @if($page->slug !== 'cookie-policy')
                        <a href="{{ route('cookie-policy') }}" class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-neutral-700 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors">
                            Cookie Policy
                        </a>
                    @endif
                    @if($page->slug !== 'gdpr')
                        <a href="{{ route('gdpr') }}" class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-neutral-700 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors">
                            GDPR
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
