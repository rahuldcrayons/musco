<x-layouts.admin>
    <x-slot name="title">Blog Posts</x-slot>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Blog Posts</h1>
            <p class="text-neutral-600">Manage your blog content</p>
        </div>
        <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Post
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Total Posts</p>
                <p class="text-2xl font-bold text-neutral-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-success-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Published</p>
                <p class="text-2xl font-bold text-success-600">{{ $stats['published'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-warning-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Drafts</p>
                <p class="text-2xl font-bold text-warning-600">{{ $stats['drafts'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'status', 'category']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="text-sm font-medium text-neutral-700">Filters & Search</span>
                @if(request()->hasAny(['search', 'status', 'category']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-5 h-5 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-collapse class="px-5 pb-4 border-t border-neutral-200">
            <form action="{{ route('admin.blog-posts.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-input w-full" placeholder="Search posts by title...">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select w-full">
                            <option value="">All Statuses</option>
                            <option value="published" @selected(request('status') === 'published')>Published</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        </select>
                    </div>
                    @if($categories->count())
                        <div>
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select w-full">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                    @if(request()->hasAny(['search', 'status', 'category']))
                        <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        @if($posts->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $posts->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Post</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase hidden md:table-cell">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase hidden sm:table-cell">Published</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase hidden lg:table-cell">Views</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    @forelse($posts as $post)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                             class="w-14 h-10 rounded-lg object-cover shrink-0 hidden sm:block border border-neutral-100">
                                    @else
                                        <div class="w-14 h-10 rounded-lg bg-neutral-100 hidden sm:flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-neutral-900 truncate max-w-xs text-sm">{{ $post->title }}</p>
                                        <p class="text-xs text-neutral-600 font-mono mt-0.5">/blog/{{ $post->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if($post->category)
                                    <span class="badge badge-info">{{ $post->category }}</span>
                                @else
                                    <span class="text-neutral-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($post->is_published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600 hidden sm:table-cell">
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <span class="text-sm text-neutral-600">{{ number_format($post->view_count) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if($post->is_published)
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                           class="text-neutral-600 hover:text-neutral-700 text-sm font-medium">View</a>
                                    @endif
                                    <a href="{{ route('admin.blog-posts.edit', $post) }}"
                                       class="text-primary-600 hover:text-primary-700 text-sm font-medium">Edit</a>
                                    <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST"
                                          onsubmit="return confirm('Delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-600 hover:text-danger-700 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-neutral-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-neutral-700 mb-1">No blog posts found</p>
                                    <p class="text-sm text-neutral-600 mb-3">
                                        @if(request()->hasAny(['search', 'status', 'category']))
                                            No posts match your current filters.
                                        @else
                                            You haven't created any blog posts yet.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'status', 'category']))
                                        <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
                                    @else
                                        <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary btn-sm">Create First Post</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
