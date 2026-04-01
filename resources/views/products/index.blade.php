<x-layouts.app>
    <x-slot name="title">{{ request('category') ? ($categories->firstWhere('slug', request('category'))?->name ?? 'Products') : 'All Products' }} - {{ config('app.name') }}</x-slot>

    @push('meta')
        @php
            $metaCat = request('category') ? ($categories->firstWhere('slug', request('category'))?->name ?? null) : null;
            $metaBrand = request('brand') ? ($brands->firstWhere('slug', request('brand'))?->name ?? null) : null;
            $metaDesc = $metaCat
                ? "Shop {$metaCat} at " . config('app.name') . ". Browse {$products->total()} products with great prices and free shipping."
                : ($metaBrand
                    ? "Shop {$metaBrand} products at " . config('app.name') . ". Discover {$products->total()} products with great deals."
                    : "Shop certified gold, diamond & silver jewellery at " . config('app.name') . ". Browse {$products->total()} products with great deals.");
        @endphp
        <meta name="description" content="{{ $metaDesc }}">
        <link rel="canonical" href="{{ url('/products') }}">
        <meta property="og:title" content="{{ $metaCat ?? ($metaBrand ?? 'All Products') }} - {{ config('app.name') }}">
        <meta property="og:description" content="{{ $metaDesc }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/products') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $metaCat ?? ($metaBrand ?? 'All Products') }} - {{ config('app.name') }}">
        <meta name="twitter:description" content="{{ $metaDesc }}">
        @if(request()->anyFilled(['category', 'brand', 'min_price', 'max_price', 'rating', 'in_stock', 'on_sale', 'sort']))
        <meta name="robots" content="noindex, follow">
        @endif
    @endpush

    @push('styles')
    <style>
        /* Custom page scrollbar for products */
        html { scrollbar-width: thin; scrollbar-color: #B76E79 #f7f7f7; }
        html::-webkit-scrollbar { width: 8px; }
        html::-webkit-scrollbar-track { background: #f7f7f7; }
        html::-webkit-scrollbar-thumb { background: #b0c4c7; border-radius: 4px; border: 2px solid #f7f7f7; }
        html::-webkit-scrollbar-thumb:hover { background: #B76E79; }
    </style>
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-neutral-100">
        <div class="container mx-auto px-4 py-2.5">
            <x-breadcrumb :items="[['label' => 'Products', 'url' => null]]" />
        </div>
    </div>

    <!-- Header -->
    <div class="bg-[#B76E79]">
        <div class="container mx-auto px-4 py-6 md:py-8">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1" style="font-family:'Playfair Display',Georgia,serif;">All Products</h1>
            <p class="text-white/90 text-sm">Browse our curated collection of fine jewelry</p>
            <p class="text-white/70 text-xs mt-2">{{ $products->total() ?: 12 }} products</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Active Filters -->
        @if(request()->hasAny(['category', 'brand', 'min_price', 'max_price', 'rating', 'in_stock', 'on_sale']))
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <span class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Active Filters:</span>
                @if(request('category'))
                    @php $catName = $categories->firstWhere('slug', request('category'))?->name ?? request('category'); @endphp
                    <a href="{{ request()->fullUrlWithoutQuery('category') }}"
                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#B76E79]/5 text-[#222222] text-xs font-medium rounded-full border border-[#B76E79]/30 hover:bg-[#c29958]/10 transition-colors">
                        {{ $catName }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if(request('brand'))
                    @foreach((array) request('brand') as $brandSlug)
                        @php $brandName = $brands->firstWhere('slug', $brandSlug)?->name ?? $brandSlug; @endphp
                        <a href="{{ request()->fullUrlWithoutQuery('brand') }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#B76E79]/5 text-[#222222] text-xs font-medium rounded-full border border-[#B76E79]/30 hover:bg-[#c29958]/10 transition-colors">
                            {{ $brandName }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endforeach
                @endif
                @if(request('min_price') || request('max_price'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#B76E79]/5 text-[#222222] text-xs font-medium rounded-full border border-[#B76E79]/30">
                        @price(request('min_price', 0)) - @price(request('max_price', '...'))
                    </span>
                @endif
                @if(request('rating'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#B76E79]/5 text-[#222222] text-xs font-medium rounded-full border border-[#B76E79]/30">
                        {{ request('rating') }}+ Stars
                    </span>
                @endif
                @if(request('in_stock'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#B76E79]/5 text-[#222222] text-xs font-medium rounded-full border border-[#B76E79]/30">In Stock</span>
                @endif
                @if(request('on_sale'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#B76E79]/5 text-[#222222] text-xs font-medium rounded-full border border-[#B76E79]/30">On Sale</span>
                @endif
                <a href="{{ route('products.index') }}" class="text-xs text-neutral-600 hover:text-[#c29958] underline ml-1">Clear all</a>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Filters Sidebar -->
            <aside class="lg:w-60 shrink-0" x-data="{ mobileOpen: false }">
                <!-- Mobile filter toggle -->
                <button @click="mobileOpen = true"
                        class="lg:hidden w-full flex items-center justify-center gap-2 px-3 py-1.5 bg-white rounded-lg text-sm font-medium text-neutral-700 transition-colors mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters
                    @if(request()->hasAny(['category', 'brand', 'min_price', 'max_price', 'rating', 'in_stock', 'on_sale']))
                        <span class="w-5 h-5 bg-[#B76E79] text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ count(array_filter([request('category'), request('brand'), request('min_price'), request('max_price'), request('rating'), request('in_stock'), request('on_sale')])) }}
                        </span>
                    @endif
                </button>

                <!-- Mobile filter overlay -->
                <div x-show="mobileOpen" x-cloak class="lg:hidden fixed inset-0 z-50">
                    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @click="mobileOpen = false" class="absolute inset-0 bg-black/40"></div>
                    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                         class="absolute inset-y-0 left-0 w-80 max-w-[85vw] bg-white shadow-xl flex flex-col">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-neutral-100">
                            <h3 class="font-semibold text-neutral-900">Filters</h3>
                            <button @click="mobileOpen = false" class="p-1 text-neutral-600 hover:text-neutral-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4">
                            @include('products.partials.filters')
                        </div>
                    </div>
                </div>

                <!-- Desktop filters -->
                <div class="hidden lg:block">
                    @include('products.partials.filters')
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1 min-w-0">
                <!-- Sort Bar -->
                <div class="flex items-center justify-between mb-5 pb-4 border-b border-neutral-100">
                    <p class="text-sm text-neutral-600">
                        <span class="font-semibold text-neutral-900">{{ $products->total() ?: 12 }}</span> products found
                    </p>

                    <div class="flex items-center gap-2">
                        <label class="text-xs text-neutral-600 hidden sm:inline">Sort by:</label>
                        <select onchange="window.location.href = '{{ route('products.index') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value})"
                                class="text-sm py-1.5 pl-3 pr-8 border border-neutral-200 rounded-lg bg-white text-neutral-700 focus:outline-none focus:border-[#B76E79] cursor-pointer">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Best Rating</option>
                            <option value="bestselling" {{ request('sort') === 'bestselling' ? 'selected' : '' }}>Bestselling</option>
                        </select>
                    </div>
                </div>

                @if($products->count())
                    <div x-data="{
                        page: {{ $products->currentPage() }},
                        loading: false,
                        hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
                        loadMore() {
                            if (this.loading || !this.hasMore) return;
                            this.loading = true;
                            this.page++;
                            const url = new URL(window.location.href);
                            url.searchParams.set('page', this.page);
                            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                .then(r => r.json())
                                .then(data => {
                                    this.$refs.grid.insertAdjacentHTML('beforeend', data.html);
                                    this.hasMore = data.hasMore;
                                    this.loading = false;
                                })
                                .catch(() => { this.loading = false; });
                        }
                    }" x-init="
                        new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting) loadMore();
                        }, { rootMargin: '200px' }).observe($refs.sentinel)
                    ">
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4" x-ref="grid">
                            @foreach($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        <!-- Sentinel for infinite scroll -->
                        <div x-ref="sentinel" class="h-4"></div>

                        <!-- Loading spinner -->
                        <div x-show="loading" x-cloak class="flex justify-center py-8">
                            <svg class="animate-spin h-6 w-6 text-[#B76E79]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                    </div>
                @else
                    {{-- Dummy products for visual preview --}}
                    @php
                        $dummyProducts = [
                            ['name' => 'Pearl Drop Necklace', 'price' => '1,299', 'mrp' => '2,499', 'discount' => 48, 'rating' => 4.5, 'reviews' => 128, 'img' => asset('images/dummy/product-1.jpg'), 'cat' => 'Necklaces'],
                            ['name' => 'Gold Hoop Earrings', 'price' => '899', 'mrp' => '1,799', 'discount' => 50, 'rating' => 4.3, 'reviews' => 86, 'img' => asset('images/dummy/product-2.jpg'), 'cat' => 'Earrings'],
                            ['name' => 'Diamond Tennis Bracelet', 'price' => '2,499', 'mrp' => '4,999', 'discount' => 50, 'rating' => 4.7, 'reviews' => 234, 'img' => asset('images/dummy/product-3.jpg'), 'cat' => 'Bracelets'],
                            ['name' => 'Emerald Statement Ring', 'price' => '1,599', 'mrp' => '2,999', 'discount' => 47, 'rating' => 4.4, 'reviews' => 156, 'img' => asset('images/dummy/product-4.jpg'), 'cat' => 'Rings'],
                            ['name' => 'Layered Chain Necklace', 'price' => '1,099', 'mrp' => '1,999', 'discount' => 45, 'rating' => 4.6, 'reviews' => 198, 'img' => asset('images/dummy/cat-necklace.jpg'), 'cat' => 'Necklaces'],
                            ['name' => 'Crystal Stud Earrings', 'price' => '599', 'mrp' => '1,199', 'discount' => 50, 'rating' => 4.2, 'reviews' => 312, 'img' => asset('images/dummy/cat-earrings.jpg'), 'cat' => 'Earrings'],
                            ['name' => 'Rose Gold Bangle', 'price' => '1,899', 'mrp' => '3,499', 'discount' => 46, 'rating' => 4.8, 'reviews' => 89, 'img' => asset('images/dummy/cat-bracelet.jpg'), 'cat' => 'Bracelets'],
                            ['name' => 'Minimalist Silver Ring', 'price' => '499', 'mrp' => '999', 'discount' => 50, 'rating' => 4.1, 'reviews' => 267, 'img' => asset('images/dummy/cat-ring.jpg'), 'cat' => 'Rings'],
                            ['name' => 'Vintage Pearl Choker', 'price' => '1,799', 'mrp' => '3,299', 'discount' => 45, 'rating' => 4.5, 'reviews' => 143, 'img' => asset('images/dummy/product-1.jpg'), 'cat' => 'Necklaces'],
                            ['name' => 'Kundan Drop Earrings', 'price' => '1,399', 'mrp' => '2,799', 'discount' => 50, 'rating' => 4.6, 'reviews' => 175, 'img' => asset('images/dummy/product-2.jpg'), 'cat' => 'Earrings'],
                            ['name' => 'Charm Link Bracelet', 'price' => '999', 'mrp' => '1,899', 'discount' => 47, 'rating' => 4.3, 'reviews' => 204, 'img' => asset('images/dummy/product-3.jpg'), 'cat' => 'Bracelets'],
                            ['name' => 'Solitaire Diamond Ring', 'price' => '3,499', 'mrp' => '6,999', 'discount' => 50, 'rating' => 4.9, 'reviews' => 67, 'img' => asset('images/dummy/product-4.jpg'), 'cat' => 'Rings'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                        @foreach($dummyProducts as $dp)
                            <div class="group card-product flex flex-col rounded-lg overflow-hidden">
                                {{-- Image Section --}}
                                <div class="relative aspect-square overflow-hidden bg-[#FAF7F5]">
                                    <a href="#">
                                        <img src="{{ $dp['img'] }}" alt="{{ $dp['name'] }}"
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                             loading="lazy">
                                    </a>
                                    {{-- Discount badge --}}
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-[#1346af] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ $dp['discount'] }}%</span>
                                    </div>
                                    {{-- Hover actions --}}
                                    <div class="absolute top-2 right-2 flex flex-col gap-1.5 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity duration-200">
                                        <button class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full shadow-sm flex items-center justify-center text-[#747474] hover:text-[#ef4444] hover:bg-white transition-colors" aria-label="Add to wishlist">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        </button>
                                        <button class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full shadow-sm flex items-center justify-center text-[#747474] hover:text-[#2b2b2b] hover:bg-white transition-colors" aria-label="Quick view">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                {{-- Content Section --}}
                                <div class="p-3 flex flex-col flex-1">
                                    <h3 class="text-[13px] text-[#2b2b2b] mb-1.5 leading-snug min-h-9">
                                        <a href="#" class="line-clamp-2 hover:text-[#c29958] transition-colors">{{ $dp['name'] }}</a>
                                    </h3>
                                    <div class="flex items-center gap-1 mb-1.5">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= floor($dp['rating']) ? 'text-[#C9A96E]' : 'text-[#E0E0E0]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <span class="text-[11px] text-[#747474]">({{ $dp['reviews'] }})</span>
                                    </div>
                                    <div class="mb-1.5">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-[16px] font-semibold text-[#2b2b2b]">₹{{ $dp['price'] }}</span>
                                            <span class="text-[12px] text-[#747474] line-through">₹{{ $dp['mrp'] }}</span>
                                        </div>
                                        <span class="text-[11px] text-[#B76E79] font-medium">({{ $dp['discount'] }}% off)</span>
                                    </div>
                                    <div class="mt-auto pt-2">
                                        <button class="w-full py-2 text-xs font-semibold text-white bg-[#B76E79] hover:bg-[#222222] rounded-full transition-all shadow-sm hover:shadow-md tracking-wide uppercase" style="letter-spacing: 0.05em;">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-trust-badges />
    <x-faq-section />

    {{-- GA4 view_item_list --}}
    @if(config('services.ga4.measurement_id') && $products->count())
    @php
        $ga4Items = $products->getCollection()->values()->map(function ($p, $i) {
            return [
                'item_id' => $p->sku ?? (string) $p->id,
                'item_name' => $p->name,
                'item_category' => $p->category?->name ?? '',
                'item_brand' => $p->brand?->name ?? '',
                'price' => (float) $p->price,
                'index' => $i,
            ];
        });
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            gtag('event', 'view_item_list', {
                item_list_id: 'all_products',
                item_list_name: 'All Products',
                items: {!! json_encode($ga4Items, JSON_UNESCAPED_UNICODE) !!}
            });
        });
    </script>
    @endif
</x-layouts.app>
