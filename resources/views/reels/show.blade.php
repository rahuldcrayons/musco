<x-layouts.app>
    <x-slot name="title">{{ Str::limit($reel['caption'], 60) ?: 'Watch Reel' }} - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="{{ Str::limit($reel['caption'], 160) }}">
        <meta property="og:title" content="{{ Str::limit($reel['caption'], 60) ?: 'Watch Reel' }} - {{ config('app.name') }}">
        <meta property="og:type" content="video.other">
        <meta property="og:url" content="{{ route('reels.show', $reel['shortcode']) }}">
        @if($reel['thumbnail_url'])
            <meta property="og:image" content="{{ $reel['thumbnail_url'] }}">
        @endif
        <link rel="canonical" href="{{ route('reels.show', $reel['shortcode']) }}">
        @php
            $isVideo = in_array($reel['media_type'] ?? 'VIDEO', ['VIDEO', 'REELS']);
            $reelThumb = $reel['thumbnail_url'] ?: asset('images/trendymus-banner.png');
            $reelDate  = !empty($reel['timestamp']) ? \Carbon\Carbon::parse($reel['timestamp'])->toIso8601String() : now()->toIso8601String();
            $schema = ['@context'=>'https://schema.org','@type'=>$isVideo?'VideoObject':'ImageObject','name'=>Str::limit($reel['caption']?:config('app.name').' - Video',100),'thumbnailUrl'=>$reelThumb,'uploadDate'=>$reelDate,'url'=>route('reels.show',$reel['shortcode'])];
            if($isVideo && !empty($reel['media_url'])) $schema['contentUrl'] = $reel['media_url'];
        @endphp
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <div class="bg-[#f8f6f3] min-h-screen pb-16">
        <div class="container mx-auto px-4 py-6 max-w-5xl">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-xs text-[#888] mb-6">
                <a href="{{ route('home') }}" class="hover:text-[#202a40] transition-colors">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('reels.index') }}" class="hover:text-[#202a40] transition-colors">Reels &amp; Videos</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#444] line-clamp-1">{{ Str::limit($reel['caption'], 35) ?: $reel['shortcode'] }}</span>
            </nav>

            {{-- Main grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

                {{-- Left: media preview --}}
                <div class="lg:sticky lg:top-24">
                    @php
                        $mediaThumb = $reel['thumbnail_url'] ?? $reel['media_url'] ?? asset('images/no-product-image.svg');
                        $mediaVideo = ($isVideo && !empty($reel['media_url'])) ? $reel['media_url'] : null;
                    @endphp

                    <div class="relative rounded-2xl overflow-hidden bg-[#111] shadow-sm border border-[#e8e8e8]" style="aspect-ratio:9/16; max-width:360px; margin:0 auto;">

                        {{-- Video or image --}}
                        @if($mediaVideo)
                            <video src="{{ $mediaVideo }}"
                                   poster="{{ $mediaThumb }}"
                                   class="absolute inset-0 w-full h-full object-cover"
                                   controls muted loop playsinline
                                   id="reel-video">
                            </video>
                        @else
                            <img src="{{ $mediaThumb }}"
                                 alt="{{ Str::limit($reel['caption'] ?? '', 80) }}"
                                 class="absolute inset-0 w-full h-full object-cover">

                            {{-- Play overlay linking to Instagram --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/30">
                                <a href="{{ $reel['permalink'] }}" target="_blank" rel="noopener"
                                   class="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-transform mb-3">
                                    <svg class="w-7 h-7 text-[#202a40] ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </a>
                                <span class="text-white text-xs font-semibold bg-black/40 px-3 py-1 rounded-full">Watch on Instagram</span>
                            </div>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center gap-3 mt-4" style="max-width:360px; margin:16px auto 0;">
                        <a href="{{ $reel['permalink'] }}" target="_blank" rel="noopener"
                           class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-white border border-[#e0e0e0] rounded-full text-xs font-semibold text-[#444] hover:border-[#202a40] hover:text-[#202a40] transition-colors shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            Instagram
                        </a>
                        <button onclick="navigator.share?navigator.share({title:'{{ e(Str::limit($reel['caption'],50)) }}',url:window.location.href}):navigator.clipboard.writeText(window.location.href).then(()=>alert('Link copied!'))"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-white border border-[#e0e0e0] rounded-full text-xs font-semibold text-[#444] hover:border-[#202a40] hover:text-[#202a40] transition-colors shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Share
                        </button>
                    </div>
                </div>

                {{-- Right: info --}}
                <div class="flex flex-col gap-5">

                    {{-- Caption --}}
                    @if($reel['caption'] || !empty($reel['timestamp']))
                    <div class="bg-white rounded-2xl border border-[#efefef] shadow-sm p-5">
                        @if(!empty($reel['timestamp']))
                            <p class="text-[11px] text-[#aaa] uppercase tracking-wider mb-3">
                                {{ \Carbon\Carbon::parse($reel['timestamp'])->format('d M Y · h:i A') }}
                            </p>
                        @endif
                        @if($reel['caption'])
                            <p class="text-sm text-[#444] leading-relaxed whitespace-pre-line">{{ $reel['caption'] }}</p>
                        @endif
                    </div>
                    @endif

                    {{-- Shop CTA --}}
                    <div class="bg-[#202a40] rounded-2xl p-5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-white mb-1">Love what you see?</p>
                            <p class="text-xs text-white/60">Browse our full collection.</p>
                        </div>
                        <a href="{{ route('products.index') }}"
                           class="shrink-0 flex items-center gap-2 bg-white text-[#202a40] text-xs font-bold px-4 py-2 rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Shop Now
                        </a>
                    </div>

                    {{-- More reels --}}
                    @if(count($moreReels))
                    <div class="bg-white rounded-2xl border border-[#efefef] shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-[#222]">More from Instagram</h3>
                            <a href="{{ route('reels.index') }}"
                               class="flex items-center gap-1 text-xs font-semibold text-[#202a40] hover:underline">
                                View All
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(array_slice($moreReels, 0, 3) as $more)
                                @php
                                    $moreIsVideo = in_array($more['media_type'] ?? 'VIDEO', ['VIDEO', 'REELS']);
                                    $moreThumb   = $more['thumbnail_url'] ?? $more['media_url'] ?? asset('images/no-product-image.svg');
                                @endphp
                                <a href="{{ route('reels.show', $more['shortcode']) }}"
                                   class="group relative block rounded-xl overflow-hidden bg-[#f0ede9] border border-[#efefef]"
                                   style="aspect-ratio:9/16;">
                                    <img src="{{ $moreThumb }}"
                                         alt="{{ Str::limit($more['caption'] ?? '', 40) }}"
                                         loading="lazy"
                                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/60"></div>
                                    @if($moreIsVideo)
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-[#202a40] ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </div>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script async src="//www.instagram.com/embed.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.instgrm) window.instgrm.Embeds.process();
        });
    </script>
    @endpush

</x-layouts.app>
