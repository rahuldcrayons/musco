<x-layouts.admin>
    <x-slot name="title">Collections</x-slot>

    {{-- Header: "Collections" title + "Add collection" button --}}
    <div class="flex items-center justify-between mb-4">
        <h1 style="font-size:20px;font-weight:600;color:#1a1a1a;line-height:28px">Collections</h1>
        <a href="{{ route('admin.categories.create') }}"
           style="background:#1a1a1a;color:#fff;font-size:13px;font-weight:500;padding:6px 12px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:4px"
           onmouseenter="this.style.background='#333'"
           onmouseleave="this.style.background='#1a1a1a'">
            Add collection
        </a>
    </div>

    {{-- Main card --}}
    <div style="background:#fff;border-radius:12px;box-shadow:0 1px 2px rgba(0,0,0,0.06),0 0 0 1px rgba(0,0,0,0.07)">

        {{-- Tab row + toolbar --}}
        <div x-data="{
            showSearch: {{ request('search') ? 'true' : 'false' }},
            showFilter: false,
            showSort: false,
            search: '{{ request('search') }}',
            submitSearch() { window.location = '{{ route('admin.categories.index') }}?search=' + encodeURIComponent(this.search); }
        }">
        <div class="flex items-center justify-between" style="padding:0 12px;border-bottom:1px solid #e1e1e1">
            <div class="flex items-center gap-0">
                {{-- "All" tab --}}
                <a href="{{ route('admin.categories.index') }}"
                   style="font-size:13px;font-weight:500;padding:10px 12px;text-decoration:none;display:inline-block;
                          {{ !request('status') && !request('search') ? 'color:#1a1a1a;border-bottom:2px solid #1a1a1a;' : 'color:#616161;border-bottom:2px solid transparent;' }}">
                    All
                </a>
                {{-- "+" = Add collection --}}
                <a href="{{ route('admin.categories.create') }}"
                   style="font-size:13px;color:#616161;padding:10px 8px;display:flex;align-items:center;text-decoration:none" title="Add collection">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <path d="M10 5v10M5 10h10" stroke="#616161" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </a>
            </div>

            {{-- Right toolbar: search, filter, sort --}}
            <div class="flex items-center gap-1" style="padding:6px 0;position:relative">
                {{-- Search toggle --}}
                <button @click="showSearch = !showSearch; if(showSearch) $nextTick(() => $refs.searchInput.focus())"
                        style="padding:6px;background:none;border:1px solid #ccc;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center"
                        :style="showSearch ? 'border-color:#1a1a1a;background:#f6f6f6' : ''" title="Search">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <path d="M8.5 3a5.5 5.5 0 014.383 8.823l3.896 3.9a.75.75 0 01-1.06 1.06l-3.9-3.896A5.5 5.5 0 118.5 3zm0 1.5a4 4 0 100 8 4 4 0 000-8z" fill="#616161"/>
                    </svg>
                </button>
                {{-- Filter dropdown --}}
                <div style="position:relative">
                    <button @click="showFilter = !showFilter; showSort = false"
                            style="padding:6px;background:none;border:1px solid #ccc;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center"
                            :style="showFilter || '{{ request('status') }}' ? 'border-color:#1a1a1a;background:#f6f6f6' : ''" title="Filter">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                            <path d="M2 5.5A.5.5 0 012.5 5h15a.5.5 0 010 1h-15a.5.5 0 01-.5-.5zm3 5a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm3 5a.5.5 0 01.5-.5h3a.5.5 0 010 1h-3a.5.5 0 01-.5-.5z" fill="#616161"/>
                        </svg>
                    </button>
                    <div x-show="showFilter" @click.outside="showFilter = false" x-transition
                         style="position:absolute;right:0;top:calc(100% + 4px);background:#fff;border:1px solid #e1e1e1;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);min-width:160px;z-index:50;padding:6px">
                        <p style="font-size:11px;font-weight:600;color:#8a8a8a;padding:4px 8px;text-transform:uppercase;letter-spacing:.05em">Status</p>
                        <a href="{{ route('admin.categories.index', array_merge(request()->except('status','page'), [])) }}"
                           style="display:block;padding:6px 8px;font-size:13px;text-decoration:none;border-radius:6px;{{ !request('status') ? 'background:#f1f1f1;font-weight:500;color:#1a1a1a' : 'color:#1a1a1a' }}">All</a>
                        <a href="{{ route('admin.categories.index', array_merge(request()->except('status','page'), ['status'=>'active'])) }}"
                           style="display:block;padding:6px 8px;font-size:13px;text-decoration:none;border-radius:6px;{{ request('status')==='active' ? 'background:#f1f1f1;font-weight:500;color:#1a1a1a' : 'color:#1a1a1a' }}">Active</a>
                        <a href="{{ route('admin.categories.index', array_merge(request()->except('status','page'), ['status'=>'draft'])) }}"
                           style="display:block;padding:6px 8px;font-size:13px;text-decoration:none;border-radius:6px;{{ request('status')==='draft' ? 'background:#f1f1f1;font-weight:500;color:#1a1a1a' : 'color:#1a1a1a' }}">Draft</a>
                    </div>
                </div>
                {{-- Sort dropdown --}}
                <div style="position:relative">
                    <button @click="showSort = !showSort; showFilter = false"
                            style="padding:6px;background:none;border:1px solid #ccc;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center"
                            :style="showSort || '{{ request('sort') }}' ? 'border-color:#1a1a1a;background:#f6f6f6' : ''" title="Sort">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                            <path d="M6 4.5a.75.75 0 01.75.75v8.69l1.72-1.72a.75.75 0 011.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 111.06-1.06l1.72 1.72V5.25A.75.75 0 016 4.5zm8 11a.75.75 0 01-.75-.75V6.06l-1.72 1.72a.75.75 0 01-1.06-1.06l3-3a.75.75 0 011.06 0l3 3a.75.75 0 01-1.06 1.06L14.75 6.06v8.69a.75.75 0 01-.75.75z" fill="#616161"/>
                        </svg>
                    </button>
                    <div x-show="showSort" @click.outside="showSort = false" x-transition
                         style="position:absolute;right:0;top:calc(100% + 4px);background:#fff;border:1px solid #e1e1e1;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);min-width:160px;z-index:50;padding:6px">
                        <p style="font-size:11px;font-weight:600;color:#8a8a8a;padding:4px 8px;text-transform:uppercase;letter-spacing:.05em">Sort by</p>
                        <a href="{{ route('admin.categories.index', array_merge(request()->except('sort','page'), [])) }}"
                           style="display:block;padding:6px 8px;font-size:13px;text-decoration:none;border-radius:6px;{{ !request('sort') ? 'background:#f1f1f1;font-weight:500;color:#1a1a1a' : 'color:#1a1a1a' }}">Default (position)</a>
                        <a href="{{ route('admin.categories.index', array_merge(request()->except('sort','page'), ['sort'=>'name_asc'])) }}"
                           style="display:block;padding:6px 8px;font-size:13px;text-decoration:none;border-radius:6px;{{ request('sort')==='name_asc' ? 'background:#f1f1f1;font-weight:500;color:#1a1a1a' : 'color:#1a1a1a' }}">Name A→Z</a>
                        <a href="{{ route('admin.categories.index', array_merge(request()->except('sort','page'), ['sort'=>'name_desc'])) }}"
                           style="display:block;padding:6px 8px;font-size:13px;text-decoration:none;border-radius:6px;{{ request('sort')==='name_desc' ? 'background:#f1f1f1;font-weight:500;color:#1a1a1a' : 'color:#1a1a1a' }}">Name Z→A</a>
                        <a href="{{ route('admin.categories.index', array_merge(request()->except('sort','page'), ['sort'=>'products_desc'])) }}"
                           style="display:block;padding:6px 8px;font-size:13px;text-decoration:none;border-radius:6px;{{ request('sort')==='products_desc' ? 'background:#f1f1f1;font-weight:500;color:#1a1a1a' : 'color:#1a1a1a' }}">Most products</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search bar (expandable) --}}
        <div x-show="showSearch" x-transition style="padding:8px 12px;border-bottom:1px solid #e1e1e1">
            <form @submit.prevent="submitSearch()" style="display:flex;align-items:center;gap:8px">
                <input x-ref="searchInput" x-model="search" type="text" placeholder="Search collections…"
                       style="flex:1;font-size:13px;padding:6px 10px;border:1px solid #ccc;border-radius:8px;outline:none"
                       @keydown.escape="showSearch = false; search = ''; window.location = '{{ route('admin.categories.index') }}'">
                <button type="submit" style="padding:6px 12px;background:#1a1a1a;color:#fff;font-size:13px;border:none;border-radius:8px;cursor:pointer">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index', request()->except('search','page')) }}"
                       style="padding:6px 10px;font-size:13px;color:#616161;text-decoration:none;border:1px solid #ccc;border-radius:8px">Clear</a>
                @endif
            </form>
        </div>
        </div>

        {{-- Table --}}
        @if($categories->total() > 0)
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="border-bottom:1px solid #e1e1e1">
                        <th style="width:28px;padding:10px 0 10px 12px;text-align:left">
                            <input type="checkbox" style="width:16px;height:16px;accent-color:#1a1a1a;cursor:pointer;border-radius:4px" />
                        </th>
                        <th style="padding:10px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Title</th>
                        <th style="padding:10px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Products</th>
                        <th style="padding:10px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Product conditions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr style="border-bottom:1px solid #e1e1e1;cursor:pointer"
                            onclick="window.location='{{ route('admin.categories.edit', $category) }}'"
                            onmouseenter="this.style.background='#f9f9f9'"
                            onmouseleave="this.style.background='#fff'">
                            <td style="width:28px;padding:10px 0 10px 12px" onclick="event.stopPropagation()">
                                <input type="checkbox" value="{{ $category->id }}" style="width:16px;height:16px;accent-color:#1a1a1a;cursor:pointer;border-radius:4px" />
                            </td>
                            <td style="padding:10px 12px">
                                <div class="flex items-center gap-3">
                                    @php $thumbSrc = $category->image_url ? asset('storage/' . $category->image_url) : ($category->fallback_image_url ?? null); @endphp
                                    @if($thumbSrc)
                                        <img src="{{ $thumbSrc }}" alt="{{ $category->name }}"
                                             style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid #e1e1e1;flex-shrink:0" />
                                    @else
                                        <div style="width:36px;height:36px;border-radius:8px;background:#f1f1f1;border:1px solid #e1e1e1;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                                <path d="M3.5 3A1.5 1.5 0 002 4.5v11A1.5 1.5 0 003.5 17h13a1.5 1.5 0 001.5-1.5v-11A1.5 1.5 0 0016.5 3h-13zm4.25 4a1.25 1.25 0 11-2.5 0 1.25 1.25 0 012.5 0zm-1.06 2.81L5 12.44V15.5h10v-2.44l-2.31-2.31a.75.75 0 00-1.06 0L9.31 13 7.75 11.44a1 1 0 00-1.06-.63z" fill="#8a8a8a"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <span style="font-size:13px;font-weight:500;color:#1a1a1a">{{ $category->name }}</span>
                                        @if($category->children && $category->children->count() > 0)
                                            <p style="font-size:12px;color:#616161;margin:1px 0 0 0">{{ $category->children->count() }} subcollections</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="padding:10px 12px;font-size:13px;color:#1a1a1a">
                                {{ $category->total_products_count ?? $category->products_count }}
                            </td>
                            <td style="padding:10px 12px;font-size:13px;color:#1a1a1a">
                                @if($category->parent)
                                    {{ $category->parent->name }}
                                @elseif($category->is_active)
                                    <span style="color:#616161">Manual</span>
                                @else
                                    <span style="color:#616161">Draft</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($categories->hasPages())
                <div class="flex items-center justify-center" style="padding:12px 16px;border-top:1px solid #e1e1e1">
                    <div class="flex items-center gap-2">
                        {{-- Previous --}}
                        @if($categories->onFirstPage())
                            <span style="padding:4px 8px;border:1px solid #e1e1e1;border-radius:8px;cursor:not-allowed;opacity:0.4;display:flex;align-items:center">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                                    <path d="M12 5l-5 5 5 5" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $categories->previousPageUrl() }}" style="padding:4px 8px;border:1px solid #ccc;border-radius:8px;display:flex;align-items:center;text-decoration:none" onmouseenter="this.style.background='#f6f6f6'" onmouseleave="this.style.background='#fff'">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                                    <path d="M12 5l-5 5 5 5" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @endif

                        <span style="font-size:13px;color:#1a1a1a">{{ $categories->currentPage() }} of {{ $categories->lastPage() }}</span>

                        {{-- Next --}}
                        @if($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}" style="padding:4px 8px;border:1px solid #ccc;border-radius:8px;display:flex;align-items:center;text-decoration:none" onmouseenter="this.style.background='#f6f6f6'" onmouseleave="this.style.background='#fff'">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                                    <path d="M8 5l5 5-5 5" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @else
                            <span style="padding:4px 8px;border:1px solid #e1e1e1;border-radius:8px;cursor:not-allowed;opacity:0.4;display:flex;align-items:center">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                                    <path d="M8 5l5 5-5 5" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif

        @else
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center" style="padding:60px 20px;text-align:center">
                <div style="width:48px;height:48px;border-radius:12px;background:#f1f1f1;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
                    <svg width="24" height="24" viewBox="0 0 20 20" fill="none">
                        <path d="M3.5 3A1.5 1.5 0 002 4.5v11A1.5 1.5 0 003.5 17h13a1.5 1.5 0 001.5-1.5v-11A1.5 1.5 0 0016.5 3h-13z" fill="#8a8a8a"/>
                    </svg>
                </div>
                <p style="font-size:14px;font-weight:500;color:#1a1a1a;margin:0 0 4px 0">No collections found</p>
                <p style="font-size:13px;color:#616161;margin:0 0 16px 0">Create your first collection to organize your products.</p>
                <a href="{{ route('admin.categories.create') }}"
                   style="background:#1a1a1a;color:#fff;font-size:13px;font-weight:500;padding:6px 12px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center"
                   onmouseenter="this.style.background='#333'"
                   onmouseleave="this.style.background='#1a1a1a'">
                    Add collection
                </a>
            </div>
        @endif
    </div>
</x-layouts.admin>
