@props(['limit' => 10])

<div x-data="recentlyViewed()"
     x-init="load()"
     x-show="products.length > 0"
     x-cloak
     class="mt-6 mb-2">

    {{-- Header with nav arrows --}}
    <div class="flex items-center justify-between mb-4 px-1">
        <h2 class="text-xl font-bold text-neutral-900">Recently Viewed</h2>
        <div class="hidden sm:flex items-center gap-2">
            <button onclick="rvScroll(-1)" class="pc-nav-btn" aria-label="Previous">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button onclick="rvScroll(1)" class="pc-nav-btn" aria-label="Next">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>

    <div id="rv-track" class="flex gap-3 overflow-x-auto pb-2" style="scrollbar-width:none; -webkit-overflow-scrolling:touch; scroll-snap-type:x mandatory;">
        <template x-for="product in products" :key="product.id">

            <a :href="'/products/' + product.slug"
               class="group rv-card rv-card-item flex flex-col flex-shrink-0 cursor-pointer"
               style="scroll-snap-align:start; text-decoration:none;">

                {{-- Image zone --}}
                <div class="relative overflow-hidden flex-none rv-img" style="border-radius:12px 12px 0 0; background:#f7f4f1;">
                    <img :src="product.image || '/images/no-product-image.svg'"
                         :alt="product.name"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">

                    {{-- Discount badge --}}
                    <span x-show="product.mrp > product.price"
                          x-text="'-' + Math.round((1 - product.price/product.mrp)*100) + '% OFF'"
                          class="absolute top-2 left-2 text-white text-[9px] font-bold px-1.5 py-0.5 rounded"
                          style="background:#202a40; letter-spacing:0.03em; z-index:1;"></span>

                    {{-- Top-right action buttons --}}
                    <div class="absolute top-2 right-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity" style="z-index:1;">
                        {{-- Wishlist --}}
                        <button @click.prevent="$store.wishlist?.toggle(product.id)"
                                class="w-6 h-6 rounded-full flex items-center justify-center"
                                style="background:rgba(255,255,255,0.9); backdrop-filter:blur(6px);">
                            <svg class="w-3 h-3 text-[#aaa]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                        {{-- Quick view --}}
                        <button @click.prevent="$dispatch('open-preview', { id: product.id, image: product.image, name: product.name, url: '/products/' + product.slug, price: product.price, mrp: product.mrp, rating: product.rating, review_count: product.review_count, sales_count: product.sales_count || 0, stock: product.stock_quantity || 0, desc: '' })"
                                class="w-6 h-6 rounded-full flex items-center justify-center text-[#888]"
                                style="background:rgba(255,255,255,0.9); backdrop-filter:blur(6px);">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex flex-col px-2 pt-2.5 pb-1 gap-0.5" style="flex:1;">

                    {{-- Name --}}
                    <h3 x-text="product.name"
                        class="text-[11px] font-medium text-[#1c1c1c] line-clamp-2 leading-snug"
                        style="min-height:2.2em;"></h3>

                    {{-- Stars (if rating exists) --}}
                    <div x-show="product.rating > 0" class="flex items-center gap-0.5">
                        <div class="flex gap-px">
                            <template x-for="s in 5" :key="s">
                                <svg class="w-2 h-2" :fill="s <= Math.round(product.rating) ? '#f5a623' : '#e0e0e0'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </template>
                        </div>
                        <span x-text="'(' + product.review_count + ')'" class="text-[9px] text-[#999]"></span>
                    </div>

                    {{-- Price --}}
                    <div class="flex flex-col gap-0" style="margin-top:auto;">
                        <div class="flex items-baseline gap-1 flex-wrap">
                            <span x-text="window.__currencySymbol + Number(product.price).toFixed(2)"
                                  class="text-[12px] font-bold text-[#1c1c1c]"></span>
                            <span x-show="product.mrp > product.price"
                                  x-text="window.__currencySymbol + Number(product.mrp).toFixed(2)"
                                  class="text-[9px] text-[#bbb] line-through"></span>
                        </div>
                        <span x-show="product.mrp > product.price"
                              x-text="'Save £' + Number(product.mrp - product.price).toFixed(2) + ' (' + Math.round((1 - product.price/product.mrp)*100) + '%)'"
                              class="text-[9px] font-semibold" style="color:#2e9e5b;"></span>
                        {{-- Free delivery --}}
                        <span x-show="product.price >= 30"
                              class="flex items-center gap-0.5 text-[9px] font-medium" style="color:#007185;">
                            <svg class="w-2 h-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 10a2 2 0 002 2h8a2 2 0 002-2L19 8"/></svg>
                            Free Delivery
                        </span>
                    </div>
                </div>

                {{-- Add to Cart --}}
                <div style="padding:6px 8px 8px;">
                    <button @click.prevent="$store.cart?.add(product.id)"
                            style="padding:8px 0; background:#202a40; color:#fff; width:100%; display:flex; align-items:center; justify-content:center; gap:5px; font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; border:none; cursor:pointer; transition:background 0.2s; border-radius:4px;"
                            onmouseover="this.style.background='#506282'" onmouseout="this.style.background='#202a40'">
                        <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        Add to Cart
                    </button>
                </div>

            </a>
        </template>
    </div>
</div>

<style>
.rv-card {
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(14px) saturate(1.4);
    -webkit-backdrop-filter: blur(14px) saturate(1.4);
    border: 1px solid rgba(255,255,255,0.7);
    border-radius: 12px;
    box-shadow: none;
    overflow: hidden;
    transition: none;
}
.rv-card:hover {
    transform: none;
    box-shadow: none;
}
/* 4 cards visible on desktop */
.rv-card-item { width: calc(25% - 10px); }
.rv-img       { width: 100%; aspect-ratio: 1/1; }
/* 2 cards on mobile */
@media (max-width: 640px) {
    .rv-card-item { width: calc(50% - 6px); }
}
</style>

<script>
function rvScroll(dir) {
    var track = document.getElementById('rv-track');
    if (!track) return;
    var card = track.firstElementChild;
    var cardW = card ? card.offsetWidth + 12 : 200;
    var visible = Math.max(1, Math.round(track.offsetWidth / cardW));
    track.scrollBy({ left: dir * cardW * visible, behavior: 'smooth' });
}
function recentlyViewed() {
    return {
        products: [],

        async load() {
            try {
                const res = await fetch('/recommendations/recently-viewed?limit={{ $limit }}');
                const data = await res.json();
                this.products = data.data || [];
            } catch (e) {}
        }
    }
}
</script>
