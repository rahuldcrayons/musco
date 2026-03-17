<x-layouts.app>
    <x-slot name="title">Sitemap - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Complete sitemap of {{ config('app.name') }}. Browse all pages, products, categories, and brands.">
        <link rel="canonical" href="{{ url('/sitemap') }}">
    @endpush

    <div class="bg-[#F7F8FA] min-h-screen">
        <div class="container mx-auto px-4 py-4">
            <x-breadcrumb :items="[['label' => 'Sitemap', 'url' => null]]" />
        </div>

        <div class="container mx-auto px-4 pb-12">
            <h1 class="text-2xl font-bold text-[#0F1111] mb-6">Sitemap</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Main Pages --}}
                <div class="bg-white rounded border border-[#E3E6E6] p-5">
                    <h2 class="text-sm font-bold text-[#205258] uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Main Pages
                    </h2>
                    <ul class="space-y-1.5 text-sm">
                        <li><a href="{{ url('/') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">All Products</a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Categories</a></li>
                        <li><a href="{{ route('brands.index') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Brands</a></li>
                        <li><a href="{{ route('deals') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Deals</a></li>
                        <li><a href="{{ route('new-arrivals') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">New Arrivals</a></li>
                        <li><a href="{{ route('bestsellers') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Bestsellers</a></li>
                        <li><a href="{{ route('search') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Search</a></li>
                    </ul>
                </div>

                {{-- Categories --}}
                <div class="bg-white rounded border border-[#E3E6E6] p-5">
                    <h2 class="text-sm font-bold text-[#205258] uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Categories
                    </h2>
                    <ul class="space-y-1.5 text-sm">
                        @foreach($categories as $category)
                            <li><a href="{{ route('categories.show', $category->slug) }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Brands --}}
                <div class="bg-white rounded border border-[#E3E6E6] p-5">
                    <h2 class="text-sm font-bold text-[#205258] uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Brands
                    </h2>
                    <ul class="space-y-1.5 text-sm">
                        @foreach($brands as $brand)
                            <li><a href="{{ route('brands.show', $brand->slug) }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">{{ $brand->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Customer Service --}}
                <div class="bg-white rounded border border-[#E3E6E6] p-5">
                    <h2 class="text-sm font-bold text-[#205258] uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Customer Service
                    </h2>
                    <ul class="space-y-1.5 text-sm">
                        <li><a href="{{ route('help') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Help Center</a></li>
                        <li><a href="{{ route('track-order') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Track Order</a></li>
                        <li><a href="{{ route('returns') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Returns & Refunds</a></li>
                        <li><a href="{{ route('shipping') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Shipping Information</a></li>
                        <li><a href="{{ route('faq') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">FAQs</a></li>
                        <li><a href="{{ route('contact') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Information --}}
                <div class="bg-white rounded border border-[#E3E6E6] p-5">
                    <h2 class="text-sm font-bold text-[#205258] uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Information
                    </h2>
                    <ul class="space-y-1.5 text-sm">
                        <li><a href="{{ route('about') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">About Us</a></li>
                        <li><a href="{{ route('blog') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Blog</a></li>
                        <li><a href="{{ route('careers') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Careers</a></li>
                        <li><a href="{{ route('size-guide') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Size Guide</a></li>
                        <li><a href="{{ route('wholesale') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Wholesale</a></li>
                        <li><a href="{{ route('seller.register') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Sell on Jikra</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div class="bg-white rounded border border-[#E3E6E6] p-5">
                    <h2 class="text-sm font-bold text-[#205258] uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Legal
                    </h2>
                    <ul class="space-y-1.5 text-sm">
                        <li><a href="{{ route('privacy') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Terms of Service</a></li>
                        <li><a href="{{ route('cookie-policy') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Cookie Policy</a></li>
                        <li><a href="{{ route('gdpr') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">GDPR Compliance</a></li>
                    </ul>
                </div>
            </div>

            {{-- XML Sitemaps for Search Engines --}}
            <div class="mt-8 bg-white rounded border border-[#E3E6E6] p-5">
                <h2 class="text-sm font-bold text-[#205258] uppercase tracking-wider mb-3">XML Sitemaps (for Search Engines)</h2>
                <div class="flex flex-wrap gap-3 text-sm">
                    <a href="{{ url('/sitemap.xml') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Sitemap Index</a>
                    <span class="text-[#E3E6E6]">|</span>
                    <a href="{{ url('/sitemap-pages.xml') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Pages</a>
                    <span class="text-[#E3E6E6]">|</span>
                    <a href="{{ url('/sitemap-products.xml') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Products</a>
                    <span class="text-[#E3E6E6]">|</span>
                    <a href="{{ url('/sitemap-categories.xml') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Categories</a>
                    <span class="text-[#E3E6E6]">|</span>
                    <a href="{{ url('/sitemap-brands.xml') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Brands</a>
                    <span class="text-[#E3E6E6]">|</span>
                    <a href="{{ url('/sitemap-blog.xml') }}" class="text-[#007185] hover:text-[#C7511F] hover:underline">Blog</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
