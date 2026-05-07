<x-layouts.app>
    <x-slot name="title">Careers - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Join the {{ config('app.name') }} team. Explore career opportunities in e-commerce and retail.">
        <link rel="canonical" href="{{ url('/careers') }}">
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-neutral-50 border-b border-neutral-100">
        <div class="container mx-auto px-4 py-3">
            <x-breadcrumb :items="[['label' => 'Careers', 'url' => null]]" />
        </div>
    </div>

    <section class="py-10 sm:py-14">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">

                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 mb-3">{{ \App\Models\Setting::get('careers_heading', 'Careers at ' . config('app.name')) }}</h1>
                    <p class="text-sm text-neutral-600 max-w-lg mx-auto">
                        @if($page?->content)
                            {!! $page->content !!}
                        @else
                            {{ \App\Models\Setting::get('careers_description', 'Join our team and help us bring the best products to customers across India.') }}
                        @endif
                    </p>
                </div>

                @if($positions->isNotEmpty())
                    <!-- Open Positions -->
                    <div class="space-y-4 text-left mb-10">
                        @foreach($positions as $position)
                            <div class="bg-white rounded-xl border border-neutral-100 p-5 sm:p-6 hover:border-[#506282]/30 hover:shadow-sm transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div>
                                        <h3 class="text-[15px] font-semibold text-neutral-900">{{ $position->title }}</h3>
                                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                            @if($position->department)
                                                <span class="inline-flex items-center gap-1 text-xs text-neutral-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    {{ $position->department }}
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center gap-1 text-xs text-neutral-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $position->location }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full bg-[#202a40]/10 text-[#202a40]">
                                                {{ $position->type }}
                                            </span>
                                        </div>
                                        @if($position->description)
                                            <p class="text-[13px] text-neutral-600 mt-2 leading-relaxed">{{ $position->description }}</p>
                                        @endif
                                    </div>
                                    <a href="mailto:{{ \App\Models\Setting::get('careers_email', 'careers@trendymus.com') }}?subject=Application: {{ $position->title }}"
                                       class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-[#202a40] border border-[#202a40]/30 rounded-lg hover:bg-[#506282]/5 transition-colors">
                                        Apply
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Openings Card -->
                    <div class="bg-white rounded-xl border border-neutral-100 p-8 sm:p-12">
                        <div class="w-16 h-16 mx-auto rounded-full bg-[#202a40]/10 flex items-center justify-center mb-5">
                            <svg class="w-8 h-8 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-neutral-900 mb-2">{{ \App\Models\Setting::get('careers_no_openings_title', 'No Open Positions Right Now') }}</h2>
                        <p class="text-sm text-neutral-600 mb-6 max-w-md mx-auto">{{ \App\Models\Setting::get('careers_no_openings_desc', "We don't have any active openings at the moment, but we're always looking for talented people. Send us your resume and we'll keep it on file.") }}</p>

                        <div class="inline-flex items-center gap-2 bg-neutral-50 rounded-lg px-5 py-3 border border-neutral-100">
                            <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ \App\Models\Setting::get('careers_email', 'careers@trendymus.com') }}" class="text-sm font-medium text-[#202a40] hover:underline">{{ \App\Models\Setting::get('careers_email', 'careers@trendymus.com') }}</a>
                        </div>
                    </div>
                @endif

                <!-- Why Join Us -->
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6 text-left">
                    <div class="bg-white rounded-xl border border-neutral-100 p-5">
                        <div class="w-9 h-9 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-3">
                            <svg class="w-4.5 h-4.5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-1">{{ \App\Models\Setting::get('careers_perk_1_title', 'Fast-Growing Startup') }}</h3>
                        <p class="text-xs text-neutral-600 leading-relaxed">{{ \App\Models\Setting::get('careers_perk_1_desc', 'Be part of a rapidly growing e-commerce brand with big ambitions.') }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-neutral-100 p-5">
                        <div class="w-9 h-9 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-3">
                            <svg class="w-4.5 h-4.5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-1">{{ \App\Models\Setting::get('careers_perk_2_title', 'Great Team') }}</h3>
                        <p class="text-xs text-neutral-600 leading-relaxed">{{ \App\Models\Setting::get('careers_perk_2_desc', 'Work alongside passionate people who love what they do.') }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-neutral-100 p-5">
                        <div class="w-9 h-9 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-3">
                            <svg class="w-4.5 h-4.5 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-1">{{ \App\Models\Setting::get('careers_perk_3_title', 'Meaningful Work') }}</h3>
                        <p class="text-xs text-neutral-600 leading-relaxed">{{ \App\Models\Setting::get('careers_perk_3_desc', 'Help bring quality, affordable products to customers across India.') }}</p>
                    </div>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
