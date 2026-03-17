<x-layouts.admin>
    <x-slot name="title">Homepage Manager</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Homepage Manager</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage your storefront homepage sections, banners, and content</p>
            </div>
        </div>
    </x-slot>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.homepage.site-settings') }}" class="card card-hover p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-neutral-900">Site Settings</h3>
                <p class="text-sm text-neutral-600">Logo, Brand, Social</p>
            </div>
        </a>

        <a href="{{ route('admin.homepage.hero-banners') }}" class="card card-hover p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-neutral-900">Hero Banners</h3>
                <p class="text-sm text-neutral-600">{{ $banners->count() }} banners</p>
            </div>
        </a>

        <a href="{{ route('admin.homepage.sections') }}" class="card card-hover p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-neutral-900">Sections</h3>
                <p class="text-sm text-neutral-600">{{ $sections->where('is_active', true)->count() }} active</p>
            </div>
        </a>

        <a href="{{ route('admin.homepage.testimonials') }}" class="card card-hover p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-neutral-900">Testimonials</h3>
                <p class="text-sm text-neutral-600">{{ $testimonials->count() }} reviews</p>
            </div>
        </a>
    </div>

    <!-- Current Homepage Preview -->
    <div class="card p-6">
        <h2 class="text-lg font-semibold text-neutral-900 mb-4">Section Order</h2>
        <div class="space-y-2">
            @foreach($sections->sortBy('position') as $section)
                <div class="flex items-center justify-between p-3 bg-neutral-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 bg-white rounded flex items-center justify-center text-sm font-medium text-neutral-600 border">
                            {{ $section->position + 1 }}
                        </span>
                        <div>
                            <span class="font-medium text-neutral-900">{{ $section->title }}</span>
                            <span class="text-xs text-neutral-600 ml-2">{{ $section->type }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge {{ $section->is_active ? 'badge-success' : 'badge-neutral' }}">
                            {{ $section->is_active ? 'Active' : 'Hidden' }}
                        </span>
                        <a href="{{ route('admin.homepage.sections.edit', $section) }}" class="btn btn-sm btn-secondary">Edit</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
