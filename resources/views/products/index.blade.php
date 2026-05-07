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
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaCat ?? ($metaBrand ?? 'All Products') }} - {{ config('app.name') }}">
        <meta name="twitter:description" content="{{ $metaDesc }}">
        @if(request()->anyFilled(['category', 'brand', 'min_price', 'max_price', 'rating', 'in_stock', 'on_sale', 'sort']))
        <meta name="robots" content="noindex, follow">
        @endif
    @endpush

    @push('styles')
    <style>
        /* Custom page scrollbar for products */
        html { scrollbar-width: thin; scrollbar-color: #202a40 #f7f7f7; }
        html::-webkit-scrollbar { width: 8px; }
        html::-webkit-scrollbar-track { background: #f7f7f7; }
        html::-webkit-scrollbar-thumb { background: #b0c4c7; border-radius: 4px; border: 2px solid #f7f7f7; }
        html::-webkit-scrollbar-thumb:hover { background: #202a40; }
    </style>
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-neutral-100">
        <div class="container mx-auto px-4 py-2.5">
            <x-breadcrumb :items="[['label' => 'Products', 'url' => null]]" />
        </div>
    </div>

    <!-- Header -->
    <div class="bg-[#202a40]">
        <div class="container mx-auto px-4 py-7 md:py-10">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2" style="font-family:'Playfair Display',Georgia,serif;">All Products</h1>
            <p class="text-white/80 text-sm">Browse our curated collection of fine jewelry</p>
            <div class="flex items-center gap-2 mt-3">
                <span class="inline-flex items-center gap-1.5 bg-white/10 text-white/90 text-xs font-medium px-3 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    {{ $products->total() }} products
                </span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-2 sm:px-4 py-6 sm:py-8">
        {{-- Active filter chips — desktop only --}}
        @if(request()->hasAny(['category', 'brand', 'min_price', 'max_price', 'rating', 'in_stock', 'on_sale']))
            <div class="hidden lg:flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs font-medium text-neutral-500 uppercase tracking-wide">Active Filters:</span>
                @if(request('category'))
                    @php $catName = $categories->firstWhere('slug', request('category'))?->name ?? request('category'); @endphp
                    <a href="{{ request()->fullUrlWithoutQuery('category') }}"
                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30 hover:bg-[#506282]/10 transition-colors">
                        {{ $catName }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if(request('brand'))
                    @foreach((array) request('brand') as $brandSlug)
                        @php $brandName = $brands->firstWhere('slug', $brandSlug)?->name ?? $brandSlug; @endphp
                        <a href="{{ request()->fullUrlWithoutQuery('brand') }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30 hover:bg-[#506282]/10 transition-colors">
                            {{ $brandName }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endforeach
                @endif
                @if(request('min_price') || request('max_price'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30">
                        @price(request('min_price', 0)) – @price(request('max_price', '...'))
                    </span>
                @endif
                @if(request('rating'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30">{{ request('rating') }}+ Stars</span>
                @endif
                @if(request('in_stock'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30">In Stock</span>
                @endif
                @if(request('on_sale'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30">On Sale</span>
                @endif
                <a href="{{ route('products.index') }}" class="text-xs text-neutral-500 hover:text-[#506282] underline ml-1">Clear all</a>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-3 lg:gap-8" x-data="{ mobileOpen: false }">

            {{-- Mobile toolbar: [Filters btn | Sort dropdown] — lg:hidden --}}
            <div class="lg:hidden flex flex-col gap-1.5 mb-2">

                {{-- Row 1: Filters + Sort --}}
                <div class="flex items-stretch gap-0 border border-neutral-200 rounded-xl overflow-hidden bg-white shadow-sm">
                    {{-- Filters button --}}
                    <button @click="mobileOpen = true; document.body.style.overflow='hidden'; document.documentElement.style.overflow='hidden';"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-[#202a40] border-r border-neutral-200 shrink-0 bg-white active:bg-neutral-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters
                        @if(request()->hasAny(['category', 'brand', 'min_price', 'max_price', 'rating', 'in_stock', 'on_sale']))
                            <span class="w-5 h-5 bg-[#202a40] text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                {{ count(array_filter([request('category'), request('brand'), request('min_price'), request('max_price'), request('rating'), request('in_stock'), request('on_sale')])) }}
                            </span>
                        @endif
                    </button>

                    {{-- Sort dropdown --}}
                    <div x-data="{
                            open: false,
                            selected: '{{ request('sort', 'newest') }}',
                            options: [
                                {value:'newest',      label:'Newest'},
                                {value:'price_asc',   label:'Price: Low → High'},
                                {value:'price_desc',  label:'Price: High → Low'},
                                {value:'rating',      label:'Best Rating'},
                                {value:'bestselling', label:'Bestselling'},
                            ],
                            get label(){ return this.options.find(o=>o.value===this.selected)?.label??'Newest'; },
                            choose(val){ this.selected=val; this.open=false; window.location.href='{{ route('products.index') }}?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)),sort:val}); }
                         }" @click.outside="open=false" class="relative flex-1">
                        <button @click="open=!open" type="button"
                                class="w-full flex items-center justify-between gap-2 text-sm font-semibold py-2 pl-3 pr-3 bg-white text-neutral-700 active:bg-neutral-50">
                            <span x-text="label" class="truncate"></span>
                            <svg class="w-4 h-4 text-neutral-400 shrink-0 transition-transform duration-200" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                             class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-neutral-200 rounded-xl shadow-xl z-[9999] overflow-hidden">
                            <template x-for="opt in options" :key="opt.value">
                                <button @click="choose(opt.value)" type="button"
                                        class="w-full text-left px-4 py-3 text-sm transition-colors"
                                        :class="selected===opt.value ? 'font-semibold text-[#202a40] bg-[#202a40]/5' : 'text-neutral-700 hover:bg-neutral-50'"
                                        x-text="opt.label"></button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Count + Search --}}
                <div class="flex items-center gap-2" x-data="prodFilter()" x-init="init()" @keydown.window.escape="reset()">
                    @if($products->total() > 0)
                    <p class="text-xs font-medium text-neutral-600 shrink-0">
                        <span class="font-bold text-[#202a40]" x-text="visible !== null ? visible : {{ $products->total() }}"></span>
                        <span class="text-neutral-400"> found</span>
                    </p>
                    @endif
                    <div class="flex-1 relative">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="q" @input="filter()"
                               placeholder="Search products…"
                               style="outline:none !important; box-shadow:none !important;"
                               class="w-full pl-8 pr-7 py-1.5 text-xs bg-white border border-neutral-200 rounded-xl text-neutral-700 placeholder-neutral-400 focus:border-[#202a40] transition-all">
                        <button x-show="q" x-cloak @click="reset()" type="button"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-neutral-300 hover:bg-neutral-400 flex items-center justify-center transition-colors">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile filter overlay — teleported to body (outside aside so hidden aside doesn't block Alpine) -->
            <template x-teleport="body">
                <div x-show="mobileOpen" x-cloak
                     style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;touch-action:none;">
                    {{-- Backdrop --}}
                    <div x-show="mobileOpen"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @click="mobileOpen = false; document.body.style.overflow=''; document.documentElement.style.overflow='';"
                         style="position:absolute;inset:0;background:rgba(0,0,0,0.5);"></div>
                    {{-- Drawer --}}
                    <div x-show="mobileOpen"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                         style="position:absolute;top:0;left:0;bottom:0;width:300px;max-width:85vw;height:100vh;background:#fff;box-shadow:4px 0 24px rgba(0,0,0,0.15);display:flex;flex-direction:column;overflow:hidden;">
                        {{-- Header --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #f0f0f0;flex-shrink:0;">
                            <span style="font-size:15px;font-weight:600;color:#1a1a1a;">Filters</span>
                            <button @click="mobileOpen = false; document.body.style.overflow=''; document.documentElement.style.overflow='';"
                                    style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:none;background:#f5f5f5;cursor:pointer;color:#555;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        {{-- Scrollable content --}}
                        <div style="flex:1;overflow-y:auto;padding:16px;overscroll-behavior:contain;">
                            @include('products.partials.filters')
                        </div>
                    </div>
                </div>
            </template>

            <!-- Filters Sidebar — desktop only -->
            <aside class="hidden lg:block lg:w-60 shrink-0">
                @include('products.partials.filters')
            </aside>

            <!-- Products Grid -->
            <div class="flex-1 min-w-0">
                <!-- Sort Bar — desktop only (mobile has its own toolbar above) -->
                <div class="hidden lg:flex flex-wrap items-center gap-3 mb-6 pb-4 border-b border-neutral-200"
                     x-data="prodFilter()" x-init="init()" @keydown.window.escape="reset()">

                    {{-- Count --}}
                    @if($products->total() > 0)
                    <p class="text-sm font-medium text-neutral-700 shrink-0">
                        <span class="font-bold text-[#202a40]" x-text="visible !== null ? visible : {{ $products->total() }}"></span>
                        <span class="text-neutral-500" x-text="visible !== null ? ' results' : ' products found'"></span>
                    </p>
                    @endif

                    {{-- Live search --}}
                    <div class="flex-1 min-w-[180px] max-w-[320px] relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="q" @input="filter()"
                               placeholder="Search products…"
                               style="outline:none !important; box-shadow:none !important;"
                               class="w-full pl-9 pr-8 py-2 text-sm bg-white border border-neutral-200 rounded-lg text-neutral-700 placeholder-neutral-400 focus:border-[#202a40] focus:ring-1 focus:ring-[#202a40]/20 transition-all duration-200">
                        <button x-show="q" x-cloak @click="reset()" type="button"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-neutral-300 hover:bg-neutral-400 flex items-center justify-center transition-colors">
                            <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Sort (desktop) --}}
                    <div class="hidden lg:flex items-center gap-2 ml-auto shrink-0">
                        <span class="text-xs text-neutral-500">Sort:</span>
                        <div x-data="{
                                open: false,
                                selected: '{{ request('sort', 'newest') }}',
                                options: [
                                    {value:'newest',      label:'Newest'},
                                    {value:'price_asc',   label:'Price: Low → High'},
                                    {value:'price_desc',  label:'Price: High → Low'},
                                    {value:'rating',      label:'Best Rating'},
                                    {value:'bestselling', label:'Bestselling'},
                                ],
                                get label(){ return this.options.find(o=>o.value===this.selected)?.label??'Newest'; },
                                choose(val){ this.selected=val; this.open=false; window.location.href='{{ route('products.index') }}?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)),sort:val}); }
                             }" @click.outside="open=false" class="relative">
                            <button @click="open=!open" type="button"
                                    class="flex items-center gap-2 text-sm py-1.5 pl-3 pr-3 border border-neutral-200 rounded-lg bg-white text-neutral-700 hover:border-neutral-300 transition-colors min-w-[160px] justify-between">
                                <span x-text="label"></span>
                                <svg class="w-4 h-4 text-neutral-400 transition-transform duration-200" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                                 class="absolute right-0 top-full mt-1.5 w-52 bg-white border border-neutral-200 rounded-xl shadow-xl z-[9999] overflow-hidden">
                                <template x-for="opt in options" :key="opt.value">
                                    <button @click="choose(opt.value)" type="button"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-colors"
                                            :class="selected===opt.value ? 'font-semibold text-[#202a40] bg-[#202a40]/5' : 'text-neutral-700 hover:bg-neutral-50'"
                                            x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .pc-card-name mark{background:#fef08a;color:#78350f;border-radius:2px;font-weight:700;padding:0 1px;}
                    [data-product-card]{transition:opacity .15s ease,transform .15s ease;}
                    [data-product-card].pf-hide{opacity:0;pointer-events:none;transform:scale(.97);position:absolute;visibility:hidden;}
                </style>
                <script>
                function prodFilter(){
                    return {
                        q:'', visible:null,
                        init(){},
                        filter(){
                            const t=this.q.trim().toLowerCase();
                            const grid=document.getElementById('prod-grid');
                            if(!grid)return;
                            let n=0;
                            grid.querySelectorAll('[data-product-card]').forEach(card=>{
                                const name=card.dataset.name||'';
                                const el=card.querySelector('.pc-card-name');
                                const orig=el?el.dataset.originalName:name;
                                if(!t){
                                    card.classList.remove('pf-hide');
                                    if(el)el.innerHTML=orig;
                                    n++;
                                } else if(name.includes(t)){
                                    card.classList.remove('pf-hide');
                                    if(el&&orig){
                                        const esc=t.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
                                        el.innerHTML=orig.replace(new RegExp(`(${esc})`,'gi'),'<mark>$1</mark>');
                                    }
                                    n++;
                                } else {
                                    card.classList.add('pf-hide');
                                    if(el)el.innerHTML=orig;
                                }
                            });
                            this.visible=t?n:null;
                        },
                        reset(){
                            this.q='';this.visible=null;
                            const grid=document.getElementById('prod-grid');
                            if(!grid)return;
                            grid.querySelectorAll('[data-product-card]').forEach(c=>{
                                c.classList.remove('pf-hide');
                                const n=c.querySelector('.pc-card-name');
                                if(n)n.innerHTML=n.dataset.originalName;
                            });
                        }
                    };
                }
                </script>

                @if($products->count())
                    <div id="prod-grid">
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2 sm:gap-5 md:gap-6">
                            @foreach($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        {{-- Pagination — right-aligned, Prev / 1 2 3 / Next --}}
                        @if($products->lastPage() > 1)
                        <div class="flex justify-end items-center gap-1.5 mt-8 pt-6 border-t border-neutral-100">

                            {{-- Prev --}}
                            @if($products->onFirstPage())
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-neutral-300 border border-neutral-200 rounded cursor-not-allowed select-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Prev
                                </span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-[#202a40] border border-[#202a40]/30 rounded hover:bg-[#202a40] hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Prev
                                </a>
                            @endif

                            {{-- Page numbers with smart ellipsis --}}
                            @php
                                $current = $products->currentPage();
                                $last    = $products->lastPage();
                                $start   = max(1, $current - 2);
                                $end     = min($last, $current + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ $products->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 text-xs font-medium text-[#202a40] border border-neutral-200 rounded hover:bg-[#202a40] hover:text-white hover:border-[#202a40] transition-colors">1</a>
                                @if($start > 2)
                                    <span class="text-neutral-400 text-xs px-1">…</span>
                                @endif
                            @endif

                            @for($p = $start; $p <= $end; $p++)
                                @if($p === $current)
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-[#202a40] border border-[#202a40] rounded">{{ $p }}</span>
                                @else
                                    <a href="{{ $products->url($p) }}" class="inline-flex items-center justify-center w-8 h-8 text-xs font-medium text-[#202a40] border border-neutral-200 rounded hover:bg-[#202a40] hover:text-white hover:border-[#202a40] transition-colors">{{ $p }}</a>
                                @endif
                            @endfor

                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="text-neutral-400 text-xs px-1">…</span>
                                @endif
                                <a href="{{ $products->url($last) }}" class="inline-flex items-center justify-center w-8 h-8 text-xs font-medium text-[#202a40] border border-neutral-200 rounded hover:bg-[#202a40] hover:text-white hover:border-[#202a40] transition-colors">{{ $last }}</a>
                            @endif

                            {{-- Next --}}
                            @if($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-[#202a40] border border-[#202a40]/30 rounded hover:bg-[#202a40] hover:text-white transition-colors">
                                    Next
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-neutral-300 border border-neutral-200 rounded cursor-not-allowed select-none">
                                    Next
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            @endif

                        </div>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-20 h-20 rounded-full bg-[#202a40]/8 flex items-center justify-center mb-5">
                            <svg class="w-10 h-10 text-[#202a40]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-neutral-800 mb-2">No products found</h3>
                        <p class="text-sm text-neutral-500 mb-6 max-w-xs">Try adjusting your filters or browse our full collection.</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#202a40] text-white text-sm font-semibold rounded-lg hover:bg-[#2d3a55] transition-colors">
                            Browse All Products
                        </a>
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

    {{-- Auto-scroll to product grid after pagination reload --}}
    @if(request('page'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const grid = document.getElementById('prod-grid');
            if (grid) {
                setTimeout(() => grid.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
            }
        });
    </script>
    @endif

</x-layouts.app>
