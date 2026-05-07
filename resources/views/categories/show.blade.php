<x-layouts.app>
    <x-slot name="title">{{ $category->name }} - {{ config('app.name') }}</x-slot>

    @push('meta')
        @php
            $catDesc = $category->meta_description ?? $category->description ?? "Shop {$category->name} at " . config('app.name') . ". Browse {$products->total()} products with great prices.";
        @endphp
        <meta name="description" content="{{ Str::limit(strip_tags($catDesc), 160) }}">
        <link rel="canonical" href="{{ route('categories.show', $category) }}">
        <meta property="og:title" content="{{ $category->name }} - {{ config('app.name') }}">
        <meta property="og:description" content="{{ Str::limit(strip_tags($catDesc), 160) }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('categories.show', $category) }}">
        @if($category->image_url)
        <meta property="og:image" content="{{ asset('storage/' . $category->image_url) }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $category->name }} - {{ config('app.name') }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($catDesc), 160) }}">
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-neutral-100">
        <div class="container mx-auto px-4 py-2.5">
            <x-breadcrumb :items="$breadcrumbs" />
        </div>
    </div>

    <!-- Category Header -->
    @php
        $headerBg = $category->image_url
            ? 'background-image: url(' . asset('storage/' . $category->image_url) . '); background-size: cover; background-position: center;'
            : 'background: linear-gradient(135deg, #202a40 0%, #151e30 40%, #3D2226 100%);';
    @endphp
    <div class="relative overflow-hidden" style="{{ $headerBg }} height: 224px;">
        @if($category->image_url)
            <div class="absolute inset-0 bg-gradient-to-r from-black/65 via-black/35 to-black/10"></div>
        @else
            <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(183,110,121,0.15) 0%, rgba(61,34,38,0.55) 100%);"></div>
        @endif
        <div class="relative container mx-auto px-4 h-full flex flex-col justify-center">
            <p class="text-xs font-medium uppercase tracking-widest mb-1.5" style="color: #C9A96E;">{{ $category->parent?->name ?? 'Collection' }}</p>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-sm max-w-lg line-clamp-2 mb-3" style="color: rgba(255,255,255,0.85);">{{ $category->description }}</p>
            @else
                <div class="mb-3"></div>
            @endif
            <div class="flex items-center gap-3">
                <a href="#products"
                   class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold transition-all"
                   style="background: #202a40; color: #fff;">
                    Shop Now
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
                <span class="text-xs" style="color: rgba(255,255,255,0.7);">{{ $products->total() }} products</span>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        :root { --sticky-subcat-top: 64px; }
        .sticky-subcat {
            position: sticky;
            top: var(--sticky-subcat-top, 64px);
            z-index: 35;
            background: rgba(255,255,255,0.97);
            border-bottom: 1px solid #e5e7eb;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .sticky-subcat.is-stuck {
            box-shadow: 0 12px 28px rgba(0,0,0,0.12);
            transform: translateY(-1px);
        }
    </style>
    @endpush

    <!-- Subcategories -->
    @if($subcategories->count())
        <div class="bg-white border-b border-neutral-200 sticky-subcat" data-subcat-bar>
            <div class="container mx-auto px-4">
                <div x-data x-init="$el.scrollLeft = 0" class="flex gap-1 overflow-x-auto py-2.5 scrollbar-hide">
                    <a href="{{ route('categories.show', $category) }}"
                       class="shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ !request('subcategory') ? 'bg-[#202a40] text-white shadow-sm' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                        All
                    </a>
                    @foreach($subcategories as $sub)
                        <a href="{{ route('categories.show', $sub) }}"
                           class="shrink-0 px-4 py-1.5 rounded-full text-sm font-medium bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors">
                            {{ $sub->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div id="products" class="container mx-auto px-2 sm:px-4 py-4 sm:py-6">
        <!-- Active Filters -->
        @if(request()->hasAny(['subcategory', 'min_price', 'max_price', 'in_stock', 'on_sale']))
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <span class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Active Filters:</span>
                @if(request('subcategory'))
                    @foreach((array) request('subcategory') as $subSlug)
                        @php $subName = $filterSubcategories->firstWhere('slug', $subSlug)?->name ?? $subSlug; @endphp
                        <a href="{{ request()->fullUrlWithoutQuery('subcategory') }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30 hover:bg-[#506282]/10 transition-colors">
                            {{ $subName }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endforeach
                @endif
                @if(request('min_price') || request('max_price'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30">
                        @price(request('min_price', 0)) - @price(request('max_price', '...'))
                    </span>
                @endif
                @if(request('in_stock'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30">In Stock</span>
                @endif
                @if(request('on_sale'))
                    <span class="inline-flex items-center px-2.5 py-1 bg-[#202a40]/5 text-[#222222] text-xs font-medium rounded-full border border-[#202a40]/30">On Sale</span>
                @endif
                <a href="{{ route('categories.show', $category) }}" class="text-xs text-neutral-600 hover:text-[#506282] underline ml-1">Clear all</a>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-2 lg:gap-6" x-data="{ mobileOpen: false }">

            {{-- Mobile: Filters + Sort toolbar (top row) --}}
            <div class="lg:hidden flex items-center gap-2 mb-1">
                {{-- Filters button --}}
                <button @click="mobileOpen = true; document.body.style.overflow = 'hidden'; document.documentElement.style.overflow = 'hidden';"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-neutral-200 rounded-lg text-sm font-semibold text-neutral-700 hover:border-[#202a40] transition-colors shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters
                    @if(request()->hasAny(['brand', 'min_price', 'max_price', 'in_stock', 'on_sale']))
                        <span class="w-4 h-4 bg-[#202a40] text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                            {{ count(array_filter([request('brand'), request('min_price'), request('max_price'), request('in_stock'), request('on_sale')])) }}
                        </span>
                    @endif
                </button>

                {{-- Sort dropdown (mobile, same row as Filters) --}}
                <div x-data="{
                        open: false,
                        selected: '{{ request('sort', 'newest') }}',
                        options: [
                            {value:'newest',     label:'Newest'},
                            {value:'price_asc',  label:'Price: Low → High'},
                            {value:'price_desc', label:'Price: High → Low'},
                            {value:'rating',     label:'Best Rating'},
                            {value:'bestselling',label:'Bestselling'},
                        ],
                        get label(){ return this.options.find(o=>o.value===this.selected)?.label??'Newest'; },
                        choose(val){ this.selected=val; this.open=false; window.location.href='{{ route('categories.show', $category) }}?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)),sort:val}); }
                     }" @click.outside="open=false" class="relative flex-1">
                    <button @click="open=!open" type="button"
                            class="w-full flex items-center justify-between gap-2 text-sm py-1.5 pl-3 pr-3 border border-neutral-200 rounded-lg bg-white text-neutral-700 shadow-sm">
                        <span x-text="label" class="truncate"></span>
                        <svg class="w-4 h-4 text-neutral-400 shrink-0 transition-transform duration-200" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"  x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-neutral-200 rounded-xl shadow-xl z-[9999] overflow-hidden">
                        <template x-for="opt in options" :key="opt.value">
                            <button @click="choose(opt.value)" type="button"
                                    class="w-full text-left px-4 py-2.5 text-sm transition-colors"
                                    :class="selected===opt.value ? 'font-semibold text-[#202a40] bg-[#202a40]/5' : 'text-neutral-700 hover:bg-neutral-50'"
                                    x-text="opt.label"></button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Mobile filter overlay — teleported to body so nothing interferes -->
            <template x-teleport="body">
                <div x-show="mobileOpen" x-cloak
                     style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;overflow:hidden;touch-action:none;">
                    {{-- Backdrop --}}
                    <div x-show="mobileOpen"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @click="mobileOpen = false; document.body.style.overflow = ''; document.documentElement.style.overflow = '';"
                         style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);"></div>
                    {{-- Drawer panel --}}
                    <div x-show="mobileOpen"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                         style="position:absolute;top:0;left:0;height:100%;width:280px;max-width:85vw;background:#fff;box-shadow:4px 0 32px rgba(0,0,0,0.15);display:flex;flex-direction:column;touch-action:pan-y;">
                        {{-- Header --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #efefef;flex-shrink:0;background:#fff;">
                            <span style="font-weight:700;font-size:15px;color:#111827;">Filters</span>
                            <button @click="mobileOpen = false; document.body.style.overflow = ''; document.documentElement.style.overflow = '';"
                                    style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:none;background:transparent;cursor:pointer;color:#6b7280;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        {{-- Scrollable filters --}}
                        <div style="flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:16px;">
                            @include('categories.partials.filters')
                        </div>
                    </div>
                </div>
            </template>

            <!-- Desktop Filters Sidebar (sticky, fixed height) -->
            <aside class="hidden lg:block lg:w-60 shrink-0">
                <div class="sticky top-[80px] max-h-[calc(100vh-100px)] overflow-y-auto rounded-xl border border-neutral-100 bg-white shadow-sm p-4 scrollbar-hide">
                    @include('categories.partials.filters')
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1 min-w-0">
                <!-- Sort Bar (desktop + mobile search row) -->
                <div class="flex flex-wrap items-center gap-2 mb-2 pb-2 lg:mb-5 lg:pb-4 border-b border-neutral-100"
                     x-data="catFilter()" x-init="init()" @keydown.window.escape="reset()">

                    {{-- Count --}}
                    <p class="text-xs text-neutral-600 shrink-0">
                        <span class="font-semibold text-neutral-900" x-text="visible !== null ? visible : {{ $products->total() }}"></span>
                        <span class="text-neutral-500" x-text="visible !== null ? ' results' : ' products found'"></span>
                    </p>

                    {{-- Live search --}}
                    <div class="flex-1 min-w-[100px]">
                        <div class="relative">
                            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-neutral-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" x-model="q" @input="filter()"
                                   placeholder="Search {{ $category->name }}…"
                                   style="outline:none !important; box-shadow:none !important;"
                                   class="w-full pl-8 pr-7 py-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl text-neutral-700 placeholder-neutral-400 focus:border-[#202a40] focus:bg-white transition-all">
                            <button x-show="q" x-cloak @click="reset()" type="button"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-neutral-300 hover:bg-neutral-400 flex items-center justify-center transition-colors">
                                <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Sort (desktop only) --}}
                    <div class="hidden lg:flex items-center gap-2 ml-auto shrink-0">
                        <span class="text-xs text-neutral-500">Sort:</span>
                        <div x-data="{
                                open: false,
                                selected: '{{ request('sort', 'newest') }}',
                                options: [
                                    {value:'newest',     label:'Newest'},
                                    {value:'price_asc',  label:'Price: Low → High'},
                                    {value:'price_desc', label:'Price: High → Low'},
                                    {value:'rating',     label:'Best Rating'},
                                    {value:'bestselling',label:'Bestselling'},
                                ],
                                get label(){ return this.options.find(o=>o.value===this.selected)?.label??'Newest'; },
                                choose(val){ this.selected=val; this.open=false; window.location.href='{{ route('categories.show', $category) }}?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)),sort:val}); }
                             }" @click.outside="open=false" class="relative">
                            <button @click="open=!open" type="button"
                                    class="flex items-center gap-2 text-sm py-1.5 pl-3 pr-3 border border-neutral-200 rounded-lg bg-white text-neutral-700 hover:border-neutral-300 transition-colors min-w-[160px] justify-between">
                                <span x-text="label"></span>
                                <svg class="w-4 h-4 text-neutral-400 transition-transform duration-200" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"  x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
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
                    [data-product-card].cf-hide{opacity:0;pointer-events:none;transform:scale(.97);position:absolute;visibility:hidden;}
                </style>
                <script>
                function catFilter(){
                    return {
                        q:'', visible:null,
                        init(){},
                        filter(){
                            const t=this.q.trim().toLowerCase();
                            const grid=document.getElementById('cat-product-grid');
                            if(!grid)return;
                            const cards=grid.querySelectorAll('[data-product-card]');
                            let n=0;
                            cards.forEach(card=>{
                                const name=(card.dataset.name||'');
                                const el=card.querySelector('.pc-card-name');
                                const orig=el?el.dataset.originalName:name;
                                if(!t){
                                    card.classList.remove('cf-hide');
                                    if(el)el.innerHTML=orig;
                                    n++;
                                } else if(name.includes(t)){
                                    card.classList.remove('cf-hide');
                                    if(el&&orig){
                                        const esc=t.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
                                        el.innerHTML=orig.replace(new RegExp(`(${esc})`,'gi'),'<mark>$1</mark>');
                                    }
                                    n++;
                                } else {
                                    card.classList.add('cf-hide');
                                    if(el)el.innerHTML=orig;
                                }
                            });
                            this.visible=t?n:null;
                        },
                        reset(){
                            this.q='';this.visible=null;
                            const grid=document.getElementById('cat-product-grid');
                            if(!grid)return;
                            grid.querySelectorAll('[data-product-card]').forEach(c=>{
                                c.classList.remove('cf-hide');
                                const n=c.querySelector('.pc-card-name');
                                if(n)n.innerHTML=n.dataset.originalName;
                            });
                        }
                    };
                }
                </script>


                @if($products->count())
                    <div id="cat-product-grid">
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2 sm:gap-4 md:gap-5">
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

                            {{-- Page numbers --}}
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
                    <div class="text-center py-20">
                        <div class="w-20 h-20 mx-auto mb-4 bg-neutral-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-neutral-900 mb-1">No products found</h3>
                        <p class="text-sm text-neutral-600 mb-5">Try adjusting your filters or browse all products.</p>
                        <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#202a40] text-white text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear All Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-trust-badges />
    <x-faq-section />

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bar = document.querySelector('[data-subcat-bar]');
            if (!bar) return;

            const root = document.documentElement;
            const header = document.getElementById('main-header');

            const updateOffset = () => {
                const headerBottom = header ? Math.max(header.getBoundingClientRect().bottom, 0) : 0;
                const offset = header ? Math.max(headerBottom, 8) : 64;
                root.style.setProperty('--sticky-subcat-top', offset + 'px');
            };

            const updateState = () => {
                const offset = parseFloat(getComputedStyle(root).getPropertyValue('--sticky-subcat-top')) || 64;
                const stuck = bar.getBoundingClientRect().top <= offset + 0.5;
                bar.classList.toggle('is-stuck', stuck);
            };

            updateOffset();
            updateState();

            window.addEventListener('resize', () => { updateOffset(); updateState(); });
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(() => {
                    updateOffset();
                    updateState();
                    ticking = false;
                });
            }, { passive: true });
        });
    </script>
    @endpush

    {{-- GA4 view_item_list --}}
    @if(config('services.ga4.measurement_id') && $products->count())
    @php
        $ga4Items = $products->getCollection()->values()->map(function ($p, $i) use ($category) {
            return [
                'item_id' => $p->sku ?? (string) $p->id,
                'item_name' => $p->name,
                'item_category' => $category->name,
                'item_brand' => $p->brand?->name ?? '',
                'price' => (float) $p->price,
                'index' => $i,
            ];
        });
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            gtag('event', 'view_item_list', {
                item_list_id: 'category_{{ $category->id }}',
                item_list_name: {!! json_encode($category->name, JSON_UNESCAPED_UNICODE) !!},
                items: {!! json_encode($ga4Items, JSON_UNESCAPED_UNICODE) !!}
            });
        });
    </script>
    @endif
</x-layouts.app>
