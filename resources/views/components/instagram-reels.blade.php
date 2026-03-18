{{-- Shoppable Instagram Reels Carousel — Lazy loaded via AJAX for page speed --}}
<section class="ig-shop-section py-10 lg:py-14" style="background: linear-gradient(180deg, #fff 0%, #f8f6f3 100%);"
         x-data="igReels()" x-intersect.once="loadReels()">
    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#833ab4] via-[#fd1d1d] to-[#fcb045] flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                </div>
                <div>
                    <h2 class="section-title" style="margin:0;font-size:22px;">Shop Our Instagram</h2>
                    <p class="text-xs text-[#565959] mt-0.5">Watch, explore, and shop instantly</p>
                </div>
            </div>
            <a x-show="reelsUrl" :href="reelsUrl" class="view-all-link" style="display:inline-flex;align-items:center;gap:6px;">
                View All
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Loading skeleton --}}
        <div x-show="loading" class="ig-shop-carousel">
            <div class="ig-shop-track">
                <template x-for="i in 6" :key="i">
                    <div class="ig-shop-item">
                        <div class="ig-shop-thumb animate-pulse" style="background:#e5e5e5;"></div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Reels Carousel --}}
        <div x-show="!loading && reels.length > 0" x-cloak class="ig-shop-carousel" x-data="{ scrollEl: null }" x-init="scrollEl = $refs.track">
            <div class="ig-shop-track" x-ref="track">
                <template x-for="reel in reels" :key="reel.shortcode">
                    <div class="ig-shop-item" x-data="igReelItem(reel)">
                        <a :href="'/reels/' + reel.shortcode" class="ig-shop-thumb group" rel="noopener">
                            {{-- Video autoplay for VIDEO/REELS --}}
                            <template x-if="reel.media_url && (reel.media_type === 'VIDEO' || reel.media_type === 'REELS')">
                                <video x-ref="video"
                                       :src="reel.media_url"
                                       :poster="reel.thumbnail_url"
                                       muted
                                       loop
                                       playsinline
                                       preload="metadata"
                                       @click.prevent="$el.closest('a').click()"
                                       class="ig-shop-video">
                                </video>
                            </template>
                            {{-- Fallback: thumbnail image for non-video or missing media_url --}}
                            <template x-if="!(reel.media_url && (reel.media_type === 'VIDEO' || reel.media_type === 'REELS'))">
                                <div>
                                    <img x-show="reel.thumbnail_url" :src="reel.thumbnail_url" :alt="reel.caption ? reel.caption.substring(0, 60) : 'Instagram Reel'" loading="lazy">
                                    <template x-if="!reel.thumbnail_url">
                                        <div class="ig-shop-placeholder">
                                            <svg class="ig-shop-placeholder-icon" width="36" height="36" viewBox="0 0 24 24" fill="rgba(255,255,255,0.8)"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <div class="ig-shop-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                                Reel
                            </div>
                        </a>
                        <p x-show="reel.caption" class="ig-shop-caption" x-text="reel.caption ? reel.caption.substring(0, 45) : ''"></p>
                    </div>
                </template>
            </div>

            {{-- Scroll arrows --}}
            <button @click="scrollEl.scrollBy({ left: -340, behavior: 'smooth' })"
                    class="ig-shop-arrow ig-shop-arrow--prev" aria-label="Scroll left">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="scrollEl.scrollBy({ left: 340, behavior: 'smooth' })"
                    class="ig-shop-arrow ig-shop-arrow--next" aria-label="Scroll right">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        {{-- Instagram follow strip --}}
        <div x-show="igHandle" x-cloak class="text-center mt-6">
            <a :href="'https://instagram.com/' + igHandle.replace('@', '')" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 text-sm text-[#565959] hover:text-[#205258] transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                Follow <span x-text="igHandle"></span> on Instagram
            </a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('igReels', () => ({
            reels: [],
            igHandle: '',
            reelsUrl: '',
            loading: true,
            loadReels() {
                fetch('/api/reels')
                    .then(r => r.json())
                    .then(data => {
                        this.reels = data.reels || [];
                        this.igHandle = data.handle || '';
                        this.reelsUrl = data.reels_url || '/reels';
                        this.loading = false;
                    })
                    .catch(() => { this.loading = false; });
            }
        }));

        Alpine.data('igReelItem', (reel) => ({
            observer: null,
            init() {
                if (!(reel.media_url && (reel.media_type === 'VIDEO' || reel.media_type === 'REELS'))) return;

                this.$nextTick(() => {
                    const video = this.$el.querySelector('video');
                    if (!video) return;

                    this.observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                video.play().catch(() => {});
                            } else {
                                video.pause();
                            }
                        });
                    }, { threshold: 0.5 });

                    this.observer.observe(video);
                });
            },
            destroy() {
                if (this.observer) this.observer.disconnect();
            }
        }));
    });
</script>

<style>
    .ig-shop-carousel { position: relative; }
    .ig-shop-track {
        display: flex; gap: 16px; overflow-x: auto; scroll-snap-type: x mandatory;
        scrollbar-width: none; -ms-overflow-style: none; padding: 4px 0 8px;
    }
    .ig-shop-track::-webkit-scrollbar { display: none; }
    .ig-shop-item { flex: 0 0 200px; scroll-snap-align: start; }
    .ig-shop-thumb {
        position: relative; display: block; border-radius: 16px; overflow: hidden;
        aspect-ratio: 9/16; background: #f0f0f0; text-decoration: none;
    }
    .ig-shop-thumb img, .ig-shop-video { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
    .ig-shop-thumb:hover img, .ig-shop-thumb:hover .ig-shop-video { transform: scale(1.05); }
    .ig-shop-placeholder { width: 100%; height: 100%; background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); display: flex; align-items: center; justify-content: center; }
    .ig-shop-placeholder-icon { opacity: 0.7; }
    .ig-shop-video { pointer-events: none; }
    .ig-shop-badge {
        position: absolute; top: 10px; right: 10px;
        display: flex; align-items: center; gap: 4px;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
        padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; color: #fff;
    }
    .ig-shop-caption {
        margin: 8px 0 0; font-size: 12px; color: #565959; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ig-shop-arrow {
        position: absolute; top: 40%; transform: translateY(-50%);
        width: 40px; height: 40px; border-radius: 50%;
        background: #fff; border: 1px solid #e5e5e5; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; z-index: 2; transition: all 0.2s; color: #333;
    }
    .ig-shop-arrow:hover { background: #205258; color: #fff; border-color: #205258; }
    .ig-shop-arrow--prev { left: -16px; }
    .ig-shop-arrow--next { right: -16px; }
    @media (max-width: 767px) {
        .ig-shop-item { flex: 0 0 160px; }
        .ig-shop-arrow { display: none; }
        .ig-shop-play { width: 40px; height: 40px; }
    }
    @media (max-width: 480px) { .ig-shop-item { flex: 0 0 140px; } }
</style>
