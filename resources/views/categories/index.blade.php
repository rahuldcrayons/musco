<x-layouts.app>
    <x-slot name="title">All Categories - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Browse all jewellery categories at {{ config('app.name') }}. Find rings, necklaces, earrings, bracelets, pendants and more.">
        <link rel="canonical" href="{{ url('/categories') }}">
        <meta property="og:title" content="All Categories - {{ config('app.name') }}">
        <meta property="og:description" content="Browse all jewellery categories at {{ config('app.name') }}. Find rings, necklaces, earrings, bracelets and more.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/categories') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="All Categories - {{ config('app.name') }}">
        <meta name="twitter:description" content="Browse all jewellery categories at {{ config('app.name') }}.">
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-neutral-100">
        <div class="container mx-auto px-4 py-2.5">
            <x-breadcrumb :items="[['label' => 'Categories', 'url' => null]]" />
        </div>
    </div>

    <!-- Header -->
    <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #202a40 0%, #15383c 100%); height: 180px;">
        <div class="absolute inset-0 w-full h-full" style="background: linear-gradient(135deg, #202a40 0%, #222222 50%, #0f2a2e 100%); opacity: 0.6;"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/50 via-black/20 to-transparent"></div>
        <div class="relative container mx-auto px-4 h-full flex flex-col justify-center">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Shop by Category</h1>
            <p class="text-sm" style="color: rgba(255,255,255,0.85);">Browse our wide range of jewellery & accessories</p>
            <p class="text-xs mt-2" style="color: rgba(255,255,255,0.7);">{{ $categories->count() }} categories</p>
        </div>
    </div>

    @php
        $gradients = [
            'from-rose-400 to-pink-600',
            'from-amber-400 to-orange-500',
            'from-teal-400 to-emerald-600',
            'from-violet-400 to-purple-600',
            'from-sky-400 to-blue-600',
            'from-fuchsia-400 to-pink-600',
            'from-lime-400 to-green-600',
            'from-cyan-400 to-teal-600',
            'from-indigo-400 to-violet-600',
            'from-red-400 to-rose-600',
        ];
    @endphp

    <div class="container mx-auto px-4 py-6 md:py-8">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
            @foreach($categories as $index => $category)
                @php
                    $catImage = null;
                    if ($category->image_url) {
                        $catImage = asset('storage/' . $category->image_url);
                    } else {
                        $firstProduct = $category->products->first();
                        if ($firstProduct) {
                            $catImage = $firstProduct->primary_image_url;
                        } else {
                            foreach ($category->children as $child) {
                                $childProduct = $child->products()->where('is_active', true)->with('primaryImage')->first();
                                if ($childProduct) {
                                    $catImage = $childProduct->primary_image_url;
                                    break;
                                }
                            }
                        }
                    }
                    $gradient = $gradients[$index % count($gradients)];
                    $initial = mb_strtoupper(mb_substr($category->name, 0, 1));
                @endphp

                <a href="{{ route('categories.show', $category) }}"
                   class="group bg-white rounded-xl border border-neutral-100 overflow-hidden hover:shadow-xl hover:border-neutral-200 hover:-translate-y-0.5 transition-all duration-200">

                    <!-- Image area -->
                    <div class="aspect-[4/3] overflow-hidden relative">
                        @if($catImage)
                            <img src="{{ $catImage }}"
                                 alt="{{ $category->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-gradient-to-br {{ $gradient }} items-center justify-center absolute inset-0" style="display:none;">
                                <span class="text-white font-bold text-5xl opacity-80 select-none">{{ $initial }}</span>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br {{ $gradient }} flex items-center justify-center">
                                <span class="text-white font-bold text-5xl opacity-80 select-none">{{ $initial }}</span>
                            </div>
                        @endif

                        <!-- Product count badge -->
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-0.5 bg-black/60 backdrop-blur-sm text-white text-[10px] font-semibold rounded-full">
                                {{ $category->total_products_count }} items
                            </span>
                        </div>
                    </div>

                    <!-- Card body -->
                    <div class="p-3">
                        <h3 class="font-semibold text-sm text-neutral-900 group-hover:text-[#506282] transition-colors leading-tight">
                            {{ $category->name }}
                        </h3>

                        @if($category->children->count())
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($category->children->take(3) as $child)
                                    <span class="text-[10px] text-neutral-500 bg-neutral-50 border border-neutral-100 rounded-full px-2 py-0.5 leading-tight">
                                        {{ $child->name }}
                                    </span>
                                @endforeach
                                @if($category->children->count() > 3)
                                    <span class="text-[10px] text-[#202a40] bg-[#202a40]/5 border border-[#202a40]/15 rounded-full px-2 py-0.5 leading-tight">
                                        +{{ $category->children->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="mt-1 text-[11px] text-neutral-400">{{ $category->total_products_count }} products</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
