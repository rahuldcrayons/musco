<x-layouts.app>
    <x-slot name="title">Reels & Videos - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Watch the latest reels and videos from {{ config('app.name') }}. See our products in action!">
        <meta property="og:title" content="Reels & Videos - {{ config('app.name') }}">
        <meta property="og:description" content="Watch the latest product videos and reels from {{ config('app.name') }}.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('reels.index') }}">
        <link rel="canonical" href="{{ route('reels.index') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @php
            $fallbackThumb = asset('images/trendymus-banner.png');
            $reelsSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Reels & Videos - ' . config('app.name'),
                'url' => route('reels.index'),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($reels),
                    'itemListElement' => collect($reels)->map(function ($r, $i) use ($fallbackThumb) {
                        $isVideo = in_array($r['media_type'] ?? 'VIDEO', ['VIDEO', 'REELS']);
                        $caption = $r['caption'] ?: config('app.name') . ' - Product Video';
                        $thumb = $r['thumbnail_url'] ?: $fallbackThumb;
                        $date = !empty($r['timestamp']) ? \Carbon\Carbon::parse($r['timestamp'])->toIso8601String() : now()->toIso8601String();
                        $item = [
                            '@type' => $isVideo ? 'VideoObject' : 'ImageObject',
                            'name' => Str::limit($caption, 100),
                            'thumbnailUrl' => $thumb,
                            'uploadDate' => $date,
                            'url' => route('reels.show', $r['shortcode']),
                        ];
                        if ($isVideo && !empty($r['media_url'])) {
                            $item['contentUrl'] = $r['media_url'];
                        }
                        return ['@type' => 'ListItem', 'position' => $i + 1, 'item' => $item];
                    })->toArray(),
                ],
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($reelsSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    @push('styles')
    <style>
        /* ── Page wrapper ────────────────────────────────────────── */
        .reels-page {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0d1117 0%, #161b27 40%, #1a1f2e 70%, #0d1117 100%);
            min-height: 100vh;
            padding-bottom: 60px;
        }

        /* ── Page header ─────────────────────────────────────────── */
        .reels-hero {
            position: relative;
            padding: 56px 24px 48px;
            text-align: center;
            overflow: hidden;
        }
        .reels-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(80,98,130,0.28) 0%, transparent 70%);
            pointer-events: none;
        }
        .reels-hero h1 {
            font-size: clamp(1.6rem, 4vw, 2.4rem);
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 10px;
        }
        .reels-hero p {
            color: rgba(255,255,255,0.55);
            font-size: 0.95rem;
            max-width: 380px;
            margin: 0 auto;
        }
        .reels-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            border-radius: 100px;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,0.75);
            margin-bottom: 18px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* ── Grid wrapper ────────────────────────────────────────── */
        .reels-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }
        @media (max-width: 640px)  { .reels-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 641px) and (max-width: 1024px) { .reels-grid { grid-template-columns: repeat(3, 1fr); } }

        /* ── Reel card ───────────────────────────────────────────── */
        .reel-card {
            position: relative;
            aspect-ratio: 9 / 16;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4), 0 2px 8px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.3s ease;
            text-decoration: none;
            display: block;
        }
        .reel-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 24px 60px rgba(0,0,0,0.55), 0 4px 16px rgba(80,98,130,0.25);
        }

        /* Media (img / video) */
        .reel-card__media {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .reel-card:hover .reel-card__media {
            transform: scale(1.06);
        }

        /* Gradient overlay at bottom */
        .reel-card__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                transparent 35%,
                rgba(0,0,0,0.15) 55%,
                rgba(0,0,0,0.75) 80%,
                rgba(0,0,0,0.92) 100%
            );
        }

        /* Play button */
        .reel-card__play {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .reel-card:hover .reel-card__play { opacity: 1; }
        .reel-card__play-btn {
            width: 54px;
            height: 54px;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1.5px solid rgba(255,255,255,0.35);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .reel-card:hover .reel-card__play-btn {
            transform: scale(1.1);
            background: rgba(255,255,255,0.28);
        }

        /* Type badge (top-right) */
        .reel-card__badge {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 100px;
            padding: 4px 9px;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* Bottom info */
        .reel-card__info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 18px 16px 16px;
        }
        .reel-card__title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .reel-card__username {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.72rem;
            font-weight: 500;
            color: rgba(255,255,255,0.6);
        }
        .reel-card__username svg { flex-shrink: 0; }

        /* Shop CTA on hover */
        .reel-card__cta {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px 14px;
            transform: translateY(100%);
            opacity: 0;
            transition: transform 0.28s ease, opacity 0.28s ease;
        }
        .reel-card:hover .reel-card__cta {
            transform: translateY(0);
            opacity: 1;
        }
        .reel-card__cta-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 9px 14px;
        }
        .reel-card__cta-btn span {
            font-size: 0.76rem;
            font-weight: 700;
            color: #202a40;
        }

        /* ── Steal Deals / Bestsellers interstitial ──────────────── */
        .deals-section {
            max-width: 1280px;
            margin: 48px auto;
            padding: 0 20px;
        }
        .deals-section__header {
            text-align: center;
            margin-bottom: 28px;
        }
        .deals-section__header h2 {
            font-size: clamp(1.3rem, 3vw, 1.9rem);
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }
        .deals-section__header p {
            color: rgba(255,255,255,0.45);
            font-size: 0.88rem;
        }
        .deals-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 600px) { .deals-grid { grid-template-columns: 1fr; } }

        .deal-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            padding: 32px 28px;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .deal-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .deal-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            pointer-events: none;
        }
        .deal-card--steal::before {
            background: radial-gradient(ellipse 100% 80% at 0% 0%, rgba(255,100,50,0.12) 0%, transparent 60%);
        }
        .deal-card--best::before {
            background: radial-gradient(ellipse 100% 80% at 100% 0%, rgba(245,166,35,0.12) 0%, transparent 60%);
        }
        .deal-card__icon {
            font-size: 2.4rem;
            margin-bottom: 14px;
            display: block;
        }
        .deal-card__label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
        }
        .deal-card--steal .deal-card__label { color: #ff6e42; }
        .deal-card--best  .deal-card__label { color: #f5a623; }
        .deal-card__title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 10px;
            line-height: 1.2;
        }
        .deal-card__desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.55;
            margin-bottom: 24px;
        }
        .deal-card__btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 100px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        .deal-card__btn:hover { transform: scale(1.04); opacity: 0.9; }
        .deal-card--steal .deal-card__btn {
            background: linear-gradient(135deg, #ff6e42, #ff3d00);
            color: #fff;
            box-shadow: 0 4px 20px rgba(255,80,30,0.4);
        }
        .deal-card--best .deal-card__btn {
            background: linear-gradient(135deg, #f5a623, #e08e00);
            color: #fff;
            box-shadow: 0 4px 20px rgba(245,166,35,0.4);
        }
        .deal-card__deco {
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 8rem;
            opacity: 0.06;
            pointer-events: none;
            line-height: 1;
        }

        /* ── Section divider label ───────────────────────────────── */
        .reels-section-label {
            max-width: 1280px;
            margin: 0 auto 20px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .reels-section-label span {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.35);
            white-space: nowrap;
        }
        .reels-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        /* ── Empty state ─────────────────────────────────────────── */
        .reels-empty {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255,255,255,0.35);
            font-size: 0.95rem;
        }
    </style>
    @endpush

    <div class="reels-page">

        {{-- ── Hero header ──────────────────────────────────────── --}}
        <div class="reels-hero">
            <div class="reels-hero-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                Live on Instagram
            </div>
            <h1>Reels &amp; Videos</h1>
            <p>Watch our latest drops, styling inspiration, and behind-the-scenes content</p>
        </div>

        @if(count($reels))
            @php
                $half = (int) ceil(count($reels) / 2);
                $firstHalf  = array_slice($reels, 0, $half);
                $secondHalf = array_slice($reels, $half);
                $appName = config('app.name');
            @endphp

            {{-- ── First half of reels ──────────────────────────── --}}
            <div class="reels-section-label"><span>Latest Reels</span></div>
            <div class="reels-grid">
                @foreach($firstHalf as $reel)
                    @php
                        $isVideo  = in_array($reel['media_type'] ?? 'VIDEO', ['VIDEO', 'REELS']);
                        $caption  = Str::limit($reel['caption'] ?? '', 80);
                        $thumb    = $reel['thumbnail_url'] ?? $reel['media_url'] ?? asset('images/no-product-image.svg');
                        $mediaUrl = $reel['media_url'] ?? null;
                        $username = '@' . strtolower(str_replace(' ', '', $appName));
                    @endphp
                    <a href="{{ route('reels.show', $reel['shortcode']) }}"
                       class="reel-card"
                       data-reel
                       aria-label="{{ $caption ?: 'Watch reel' }}">

                        {{-- Media --}}
                        @if($isVideo && $mediaUrl)
                            <video class="reel-card__media"
                                   src="{{ $mediaUrl }}"
                                   poster="{{ $thumb }}"
                                   muted loop playsinline
                                   loading="lazy"
                                   preload="none">
                            </video>
                        @else
                            <img class="reel-card__media"
                                 src="{{ $thumb }}"
                                 alt="{{ $caption ?: 'Reel' }}"
                                 loading="lazy">
                        @endif

                        {{-- Gradient overlay --}}
                        <div class="reel-card__overlay"></div>

                        {{-- Play icon (centre, on hover) --}}
                        <div class="reel-card__play">
                            <div class="reel-card__play-btn">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>

                        {{-- Top-right badge --}}
                        <div class="reel-card__badge">
                            @if($isVideo)
                                <svg width="9" height="9" fill="white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                                Reel
                            @else
                                <svg width="9" height="9" fill="white" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                Post
                            @endif
                        </div>

                        {{-- Bottom info --}}
                        <div class="reel-card__info">
                            @if($caption)
                                <p class="reel-card__title">{{ $caption }}</p>
                            @endif
                            <div class="reel-card__username">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="rgba(255,255,255,0.5)"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                {{ $username }}
                            </div>
                        </div>

                        {{-- Hover CTA --}}
                        <div class="reel-card__cta">
                            <div class="reel-card__cta-btn">
                                <span>{{ $isVideo ? 'Watch &amp; Shop' : 'View &amp; Shop' }}</span>
                                <svg width="14" height="14" fill="none" stroke="#202a40" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- ── Steal Deals / Bestsellers interstitial ──────── --}}
            <div class="deals-section">
                <div class="deals-section__header">
                    <h2>Exclusive Picks</h2>
                    <p>Handpicked deals and bestsellers — only while stocks last</p>
                </div>
                <div class="deals-grid">
                    {{-- Steal Deals card --}}
                    <div class="deal-card deal-card--steal">
                        <span class="deal-card__icon">🔥</span>
                        <div class="deal-card__label">Limited Time</div>
                        <h3 class="deal-card__title">Steal Deals</h3>
                        <p class="deal-card__desc">Massive discounts on trending pieces. Grab them before they sell out — prices this low won't last long.</p>
                        <a href="{{ route('home') }}?filter=deals" class="deal-card__btn">
                            Shop Deals
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <div class="deal-card__deco">🔥</div>
                    </div>

                    {{-- Bestsellers card --}}
                    <div class="deal-card deal-card--best">
                        <span class="deal-card__icon">⭐</span>
                        <div class="deal-card__label">Fan Favourites</div>
                        <h3 class="deal-card__title">Bestsellers</h3>
                        <p class="deal-card__desc">The products our customers love most. Tried, tested, and rated highly — discover what everyone's talking about.</p>
                        <a href="{{ route('home') }}?filter=bestsellers" class="deal-card__btn">
                            Explore Now
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <div class="deal-card__deco">⭐</div>
                    </div>
                </div>
            </div>

            {{-- ── Second half of reels ─────────────────────────── --}}
            @if(count($secondHalf))
                <div class="reels-section-label"><span>More to Watch</span></div>
                <div class="reels-grid">
                    @foreach($secondHalf as $reel)
                        @php
                            $isVideo  = in_array($reel['media_type'] ?? 'VIDEO', ['VIDEO', 'REELS']);
                            $caption  = Str::limit($reel['caption'] ?? '', 80);
                            $thumb    = $reel['thumbnail_url'] ?? $reel['media_url'] ?? asset('images/no-product-image.svg');
                            $mediaUrl = $reel['media_url'] ?? null;
                            $username = '@' . strtolower(str_replace(' ', '', $appName));
                        @endphp
                        <a href="{{ route('reels.show', $reel['shortcode']) }}"
                           class="reel-card"
                           data-reel
                           aria-label="{{ $caption ?: 'Watch reel' }}">

                            @if($isVideo && $mediaUrl)
                                <video class="reel-card__media"
                                       src="{{ $mediaUrl }}"
                                       poster="{{ $thumb }}"
                                       muted loop playsinline
                                       loading="lazy"
                                       preload="none">
                                </video>
                            @else
                                <img class="reel-card__media"
                                     src="{{ $thumb }}"
                                     alt="{{ $caption ?: 'Reel' }}"
                                     loading="lazy">
                            @endif

                            <div class="reel-card__overlay"></div>

                            <div class="reel-card__play">
                                <div class="reel-card__play-btn">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>

                            <div class="reel-card__badge">
                                @if($isVideo)
                                    <svg width="9" height="9" fill="white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                                    Reel
                                @else
                                    <svg width="9" height="9" fill="white" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                    Post
                                @endif
                            </div>

                            <div class="reel-card__info">
                                @if($caption)
                                    <p class="reel-card__title">{{ $caption }}</p>
                                @endif
                                <div class="reel-card__username">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="rgba(255,255,255,0.5)"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    {{ $username }}
                                </div>
                            </div>

                            <div class="reel-card__cta">
                                <div class="reel-card__cta-btn">
                                    <span>{{ $isVideo ? 'Watch &amp; Shop' : 'View &amp; Shop' }}</span>
                                    <svg width="14" height="14" fill="none" stroke="#202a40" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

        @else
            <div class="reels-empty">
                <p>No reels available at the moment. Check back soon!</p>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    /* Autoplay video on hover, pause on leave; IntersectionObserver for viewport autoplay */
    (function () {
        /* Hover play/pause for video cards */
        document.querySelectorAll('.reel-card').forEach(card => {
            const video = card.querySelector('video');
            if (!video) return;

            card.addEventListener('mouseenter', () => {
                video.play().catch(() => {});
            });
            card.addEventListener('mouseleave', () => {
                video.pause();
                video.currentTime = 0;
            });
        });

        /* IntersectionObserver: autoplay videos visible in viewport (mobile) */
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const video = entry.target.querySelector('video');
                    if (!video) return;
                    if (entry.isIntersecting) {
                        video.play().catch(() => {});
                    } else {
                        video.pause();
                        video.currentTime = 0;
                    }
                });
            }, { threshold: 0.6 });

            document.querySelectorAll('.reel-card[data-reel]').forEach(card => {
                observer.observe(card);
            });
        }
    })();
    </script>
    @endpush

</x-layouts.app>
