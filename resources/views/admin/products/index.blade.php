<x-layouts.admin>
    <x-slot name="title">Products</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Products</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage all products in your marketplace</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.products.export', request()->query()) }}" class="btn btn-secondary btn-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </a>
                <button @click="$dispatch('open-import-modal')" class="btn btn-secondary btn-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import
                </button>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Product
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Total</p>
                <p class="text-xl sm:text-2xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-success-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Active</p>
                <p class="text-xl sm:text-2xl font-bold text-success-600">{{ number_format($stats['active']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-neutral-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Inactive</p>
                <p class="text-xl sm:text-2xl font-bold text-neutral-600">{{ number_format($stats['inactive']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-error-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Out of Stock</p>
                <p class="text-xl sm:text-2xl font-bold text-error-600">{{ number_format($stats['out_of_stock']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'status', 'category', 'seller', 'stock']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters & Search
                @if(request()->hasAny(['search', 'status', 'category', 'seller', 'stock']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-4 h-4 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-cloak x-transition class="px-5 pb-4 border-t border-neutral-100">
            <form action="{{ route('admin.products.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <div class="lg:col-span-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Name or SKU..."
                               class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input w-full">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <select name="category" class="form-input w-full">
                            <option value="">All</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Seller</label>
                        <select name="seller" class="form-input w-full">
                            <option value="">All</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ request('seller') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->store_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Stock</label>
                        <select name="stock" class="form-input w-full">
                            <option value="">All</option>
                            <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                            <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low (&le; 10)</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'status', 'category', 'seller', 'stock']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    @php $pageIds = $products->pluck('id')->toArray(); @endphp
    <div class="card"
         x-data="{
             selected: [],
             toggleAll(checked) {
                 this.selected = checked ? {{ json_encode($pageIds) }} : [];
             },
             toggle(id) {
                 const idx = this.selected.indexOf(id);
                 idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
             },
             get allChecked() {
                 return this.selected.length === {{ count($pageIds) }} && {{ count($pageIds) }} > 0;
             }
         }">

        {{-- Bulk Actions Bar --}}
        <div x-show="selected.length > 0" x-cloak
             class="px-5 py-3 bg-primary-50 border-b border-primary-200 flex flex-wrap items-center justify-between gap-3">
            <span class="text-sm font-medium text-primary-700">
                <span x-text="selected.length"></span> product(s) selected
            </span>
            <form method="POST" action="{{ route('admin.products.bulk-action') }}"
                  x-ref="bulkForm"
                  @submit.prevent="
                      const action = $refs.bulkAction.value;
                      if (!action) { alert('Please select an action'); return; }
                      const labels = { activate: 'activate', deactivate: 'deactivate', approve: 'approve', delete: 'permanently delete' };
                      if (!confirm('Are you sure you want to ' + labels[action] + ' ' + selected.length + ' product(s)?')) return;
                      $refs.bulkIds.value = JSON.stringify(selected);
                      $el.submit();
                  ">
                @csrf
                <input type="hidden" name="ids" x-ref="bulkIds" value="">
                <div class="flex items-center gap-2">
                    <select name="action" x-ref="bulkAction" class="form-input text-sm py-1.5 pr-8">
                        <option value="">-- Bulk Action --</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="approve">Approve (Draft → Approved)</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </form>
        </div>

        @if($products->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $products->links('vendor.pagination.info-bar') }}
            </div>
        @endif

        {{-- Desktop / Tablet Table (md and up) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="bg-neutral-50/80 border-b border-neutral-200">
                        <th class="pl-5 pr-1 py-3 w-10">
                            <input type="checkbox" class="form-checkbox w-4 h-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                                   @change="toggleAll($event.target.checked)"
                                   :checked="allChecked">
                        </th>
                        <th class="pl-2 pr-3 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider w-[35%]">Product</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Category</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Seller</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Price</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Stock</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                        <th class="pl-3 pr-5 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-neutral-50/70 transition-colors group" :class="selected.includes({{ $product->id }}) && 'bg-primary-50/40'">
                            {{-- Checkbox --}}
                            <td class="pl-5 pr-1 py-3">
                                <input type="checkbox" class="form-checkbox w-4 h-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                                       :checked="selected.includes({{ $product->id }})"
                                       @change="toggle({{ $product->id }})">
                            </td>
                            {{-- Product --}}
                            <td class="pl-2 pr-3 py-3">
                                <div class="flex items-center gap-3">
                                    @if($product->primary_image_url)
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                             class="w-10 h-10 rounded-lg object-cover ring-1 ring-neutral-200 shrink-0">
                                    @else
                                        <div class="w-10 h-10 bg-linear-to-br from-primary-50 to-purple-50 rounded-lg flex items-center justify-center ring-1 ring-neutral-200 shrink-0">
                                            <svg class="w-5 h-5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="text-sm font-medium text-neutral-900 hover:text-primary-600 transition-colors truncate block">
                                            {{ $product->name }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs text-neutral-600 font-mono">{{ $product->sku }}</span>
                                            @if($product->is_featured)
                                                <span class="inline-flex items-center gap-0.5 text-[10px] text-amber-600 bg-amber-50 px-1.5 py-px rounded-full font-semibold leading-relaxed">
                                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-3 py-3">
                                @if($product->category)
                                    <span class="text-sm text-neutral-600">{{ $product->category->name }}</span>
                                @else
                                    <span class="text-xs text-neutral-600">--</span>
                                @endif
                            </td>

                            {{-- Seller --}}
                            <td class="px-3 py-3">
                                @if($product->seller)
                                    <span class="text-sm text-neutral-600">{{ $product->seller->store_name }}</span>
                                @else
                                    <span class="text-xs text-neutral-600">--</span>
                                @endif
                            </td>

                            {{-- Price --}}
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                @if($product->sale_price)
                                    <div class="text-sm font-semibold text-primary-600">@price($product->sale_price)</div>
                                    <div class="text-xs text-neutral-600 line-through">@price($product->price)</div>
                                @else
                                    <div class="text-sm font-semibold text-neutral-800">@price($product->price)</div>
                                @endif
                            </td>

                            {{-- Stock --}}
                            <td class="px-3 py-3 text-center">
                                @if($product->stock_quantity <= 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-error-50 text-error-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-error-500"></span>
                                        Out
                                    </span>
                                @elseif($product->stock_quantity <= 10)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-warning-50 text-warning-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-warning-500"></span>
                                        {{ $product->stock_quantity }}
                                    </span>
                                @else
                                    <span class="text-sm text-neutral-600">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-3 text-center">
                                @if($product->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-neutral">Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="pl-3 pr-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 rounded-md hover:bg-primary-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('product.show', $product) }}" target="_blank"
                                       class="btn-icon" title="View on site">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-icon" title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($product->is_active)
                                                <svg class="w-4 h-4 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Delete &quot;{{ $product->name }}&quot;? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-error-400 hover:text-error-600 hover:bg-error-50" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-neutral-900 mb-1">No products found</h3>
                                    <p class="text-sm text-neutral-600 mb-5">Get started by adding your first product.</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View (below md) --}}
        <div class="md:hidden divide-y divide-neutral-100">
            @forelse($products as $product)
                <div class="p-4 hover:bg-neutral-50/50 transition-colors" :class="selected.includes({{ $product->id }}) && 'bg-primary-50/40'">
                    <div class="flex gap-3">
                        {{-- Checkbox --}}
                        <div class="flex items-start pt-1 shrink-0">
                            <input type="checkbox" class="form-checkbox w-4 h-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                                   :checked="selected.includes({{ $product->id }})"
                                   @change="toggle({{ $product->id }})">
                        </div>
                        {{-- Image --}}
                        @if($product->primary_image_url)
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                 class="w-14 h-14 rounded-lg object-cover ring-1 ring-neutral-200 shrink-0">
                        @else
                            <div class="w-14 h-14 bg-linear-to-br from-primary-50 to-purple-50 rounded-lg flex items-center justify-center ring-1 ring-neutral-200 shrink-0">
                                <svg class="w-6 h-6 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-sm font-medium text-neutral-900 hover:text-primary-600 transition-colors line-clamp-2">
                                        {{ $product->name }}
                                    </a>
                                    <p class="text-xs text-neutral-600 font-mono mt-0.5">{{ $product->sku }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if($product->sale_price)
                                        <div class="text-sm font-bold text-primary-600">@price($product->sale_price)</div>
                                        <div class="text-[10px] text-neutral-600 line-through">@price($product->price)</div>
                                    @else
                                        <div class="text-sm font-bold text-neutral-800">@price($product->price)</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Tags --}}
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                @if($product->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-neutral">Inactive</span>
                                @endif

                                @if($product->stock_quantity <= 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-50 text-error-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-error-500"></span>
                                        Out of Stock
                                    </span>
                                @elseif($product->stock_quantity <= 10)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-warning-50 text-warning-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-warning-500"></span>
                                        {{ $product->stock_quantity }} left
                                    </span>
                                @else
                                    <span class="text-[10px] text-neutral-600 font-medium px-2 py-0.5 bg-neutral-100 rounded-full">
                                        {{ $product->stock_quantity }} in stock
                                    </span>
                                @endif

                                @if($product->is_featured)
                                    <span class="inline-flex items-center gap-0.5 text-[10px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full font-semibold">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        Featured
                                    </span>
                                @endif

                                @if($product->category)
                                    <span class="text-[10px] text-neutral-600 font-medium px-2 py-0.5 bg-neutral-50 border border-neutral-200 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1.5 mt-2.5 pt-2 border-t border-neutral-100">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 rounded-md hover:bg-primary-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <a href="{{ route('product.show', $product) }}" target="_blank"
                                   class="btn-icon" title="View on site">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn-icon" title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                        @if($product->is_active)
                                            <svg class="w-3.5 h-3.5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('Delete &quot;{{ $product->name }}&quot;? This cannot be undone.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-error-400 hover:text-error-600 hover:bg-error-50" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-neutral-900 mb-1">No products found</h3>
                        <p class="text-sm text-neutral-600 mb-5">Get started by adding your first product.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Product
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="px-4 sm:px-5 py-3 border-t border-neutral-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Import Modal -->
    <div x-data="{ open: false }"
         @open-import-modal.window="open = true"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50" @click="open = false"></div>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-0 overflow-hidden">

                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <h2 class="font-semibold text-neutral-900">Import Products</h2>
                        </div>
                        <button @click="open = false" class="btn-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="form-label form-label-required">CSV File</label>
                        <input type="file" name="csv_file" accept=".csv,.txt" required
                               class="form-input w-full">
                        <p class="form-help">Max 10MB. Must contain header row with column names.</p>
                    </div>

                    <div class="card p-4 bg-neutral-50/50">
                        <h3 class="text-sm font-semibold text-neutral-900 mb-2">Required Columns</h3>
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <span class="px-2 py-0.5 bg-primary-50 text-primary-700 rounded text-xs font-medium">name</span>
                            <span class="px-2 py-0.5 bg-primary-50 text-primary-700 rounded text-xs font-medium">sku</span>
                            <span class="px-2 py-0.5 bg-primary-50 text-primary-700 rounded text-xs font-medium">price</span>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-2">Optional Columns</h3>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">slug</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">category</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">seller</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">sale_price</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">cost_price</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">stock_quantity</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">description</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">short_description</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">is_active</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">is_featured</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">image_url</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">meta_title</span>
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-xs">meta_description</span>
                        </div>
                        <p class="text-xs text-neutral-600 mt-3">
                            <strong>category</strong> and <strong>seller</strong> should match existing names exactly.
                            <strong>image_url</strong> accepts a public URL to auto-download.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="open = false" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Import Products
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
