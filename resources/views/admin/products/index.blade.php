<x-layouts.admin>
    <x-slot name="title">Products</x-slot>

    {{-- Header: Products title + action buttons --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 style="font-size:20px;font-weight:600;color:#1a1a1a;line-height:1.2">Products</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.export', request()->query()) }}"
                   style="background:#fff;border:1px solid #8a8a8a;color:#323232;font-size:13px;font-weight:500;padding:6px 12px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center"
                   onmouseover="this.style.background='#f7f7f7'" onmouseout="this.style.background='#fff'">
                    Export
                </a>
                <button @click="$dispatch('open-import-modal')"
                        style="background:#fff;border:1px solid #8a8a8a;color:#323232;font-size:13px;font-weight:500;padding:6px 12px;border-radius:8px;cursor:pointer"
                        onmouseover="this.style.background='#f7f7f7'" onmouseout="this.style.background='#fff'">
                    Import
                </button>
                <a href="{{ route('admin.products.create') }}"
                   style="background:#1a1a1a;border:1px solid #1a1a1a;color:#fff;font-size:13px;font-weight:500;padding:6px 12px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center"
                   onmouseover="this.style.background='#333'" onmouseout="this.style.background='#1a1a1a'">
                    Add product
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Stats Bar --}}
    <x-slot name="statsBar">
        @include('admin.partials.stats-bar', ['stats' => [
            ['label' => 'Total products', 'value' => number_format($stats['total'] ?? 0), 'sparkline' => '2,12 10,10 18,8 26,10 34,6 42,8 50,5 58,3', 'color' => '#5c6ac4'],
            ['label' => 'Active', 'value' => number_format($stats['active'] ?? 0), 'sparkline' => '2,14 10,12 18,10 26,8 34,10 42,6 50,8 58,4', 'color' => '#50b83c'],
            ['label' => 'Out of stock', 'value' => number_format($stats['out_of_stock'] ?? 0), 'sparkline' => '2,10 10,10 18,10 26,10 34,10 42,10 50,10 58,10', 'color' => '#de3618'],
            ['label' => 'Inventory value', 'value' => '£' . number_format($stats['inventory_value'] ?? 0), 'sparkline' => '2,16 10,14 18,12 26,10 34,8 42,6 50,4 58,2', 'color' => '#47c1bf'],
        ]])
    </x-slot>

    @php
        $currentStatus = request('status', '');
        $pageIds = $products->pluck('id')->toArray();
    @endphp

    {{-- Single card containing tabs + filters + table + pagination --}}
    <div style="background:#fff;border-radius:12px;box-shadow:0 1px 2px rgba(0,0,0,0.06),0 1px 3px rgba(0,0,0,0.1);overflow:hidden"
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

        {{-- Tab row --}}
        <div class="flex items-center justify-between" style="border-bottom:1px solid #e1e1e1;padding:0 12px">
            <div class="flex items-center gap-0">
                @php
                    $tabs = [
                        '' => ['label' => 'All', 'count' => $stats['total']],
                        'active' => ['label' => 'Active', 'count' => $stats['active']],
                        'inactive' => ['label' => 'Draft', 'count' => $stats['inactive']],
                        'archived' => ['label' => 'Archived', 'count' => null],
                    ];
                @endphp
                @foreach($tabs as $statusKey => $tab)
                    <a href="{{ route('admin.products.index', array_merge(request()->except('status', 'page'), $statusKey ? ['status' => $statusKey] : [])) }}"
                       style="padding:10px 12px;font-size:13px;font-weight:500;text-decoration:none;position:relative;{{ $currentStatus === $statusKey ? 'color:#1a1a1a;' : 'color:#616161;' }}"
                       onmouseover="this.style.color='#1a1a1a'" onmouseout="this.style.color='{{ $currentStatus === $statusKey ? '#1a1a1a' : '#616161' }}'">
                        {{ $tab['label'] }}
                        @if($currentStatus === $statusKey)
                            <span style="position:absolute;bottom:-1px;left:12px;right:12px;height:2px;background:#1a1a1a;border-radius:1px"></span>
                        @endif
                    </a>
                @endforeach
                {{-- Plus tab --}}
                <button style="padding:10px 8px;color:#616161;background:none;border:none;cursor:pointer;font-size:16px;line-height:1" title="Create view">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                </button>
            </div>
            {{-- Right icons: search, filter, sort --}}
            <div class="flex items-center gap-1">
                {{-- Search icon --}}
                <button style="padding:6px;color:#616161;background:none;border:none;cursor:pointer;border-radius:6px" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='none'" title="Search">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                </button>
                {{-- Filter icon --}}
                <button style="padding:6px;color:#616161;background:none;border:none;cursor:pointer;border-radius:6px" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='none'" title="Filter">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
                </button>
                {{-- Sort icon --}}
                <button style="padding:6px;color:#616161;background:none;border:none;cursor:pointer;border-radius:6px" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='none'" title="Sort">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/></svg>
                </button>
            </div>
        </div>

        {{-- Search / filter row --}}
        <div class="flex items-center gap-2" style="padding:12px;border-bottom:1px solid #e1e1e1">
            <form action="{{ route('admin.products.index') }}" method="GET" class="flex items-center gap-2 flex-1">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex-1 relative">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="#8a8a8a" style="position:absolute;left:10px;top:50%;transform:translateY(-50%)"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Filter products"
                           style="width:100%;padding:7px 10px 7px 32px;font-size:13px;border:1px solid #c9c9c9;border-radius:8px;outline:none;color:#1a1a1a;background:#fff"
                           onfocus="this.style.borderColor='#005bd3';this.style.boxShadow='0 0 0 1px #005bd3'" onblur="this.style.borderColor='#c9c9c9';this.style.boxShadow='none'">
                </div>
                <select name="category" onchange="this.form.submit()"
                        style="padding:7px 28px 7px 10px;font-size:13px;border:1px solid #c9c9c9;border-radius:8px;outline:none;color:#1a1a1a;background:#fff;cursor:pointer;appearance:auto">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <select name="seller" onchange="this.form.submit()"
                        style="padding:7px 28px 7px 10px;font-size:13px;border:1px solid #c9c9c9;border-radius:8px;outline:none;color:#1a1a1a;background:#fff;cursor:pointer;appearance:auto">
                    <option value="">All sellers</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}" {{ request('seller') == $seller->id ? 'selected' : '' }}>
                            {{ $seller->store_name }}
                        </option>
                    @endforeach
                </select>
                @if(request()->hasAny(['search', 'category', 'seller', 'stock']))
                    <a href="{{ route('admin.products.index', request('status') ? ['status' => request('status')] : []) }}"
                       style="padding:7px 12px;font-size:13px;color:#323232;text-decoration:none;white-space:nowrap">
                        Clear filters
                    </a>
                @endif
            </form>
        </div>

        {{-- Bulk Actions Bar --}}
        <div x-show="selected.length > 0" x-cloak
             style="padding:8px 12px;background:#f3f3f3;border-bottom:1px solid #e1e1e1" class="flex flex-wrap items-center justify-between gap-3">
            <span style="font-size:13px;font-weight:500;color:#323232">
                <span x-text="selected.length"></span> selected
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
                    <select name="action" x-ref="bulkAction"
                            style="padding:5px 8px;font-size:12px;border:1px solid #c9c9c9;border-radius:6px;outline:none;background:#fff">
                        <option value="">Bulk action</option>
                        <option value="activate">Set as active</option>
                        <option value="deactivate">Set as draft</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit"
                            style="padding:5px 10px;font-size:12px;font-weight:500;background:#1a1a1a;color:#fff;border:none;border-radius:6px;cursor:pointer">
                        Apply
                    </button>
                </div>
            </form>
        </div>

        {{-- Product table --}}
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="border-bottom:1px solid #e1e1e1">
                        <th style="padding:8px 8px 8px 12px;width:36px;text-align:center">
                            <input type="checkbox"
                                   style="width:16px;height:16px;cursor:pointer;accent-color:#1a1a1a"
                                   @change="toggleAll($event.target.checked)"
                                   :checked="allChecked">
                        </th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Product</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Status</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Inventory</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Category</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr style="border-bottom:1px solid #f1f1f1;cursor:pointer"
                            onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='#fff'"
                            onclick="if(!event.target.closest('input[type=checkbox]')) window.location='{{ route('admin.products.edit', $product) }}'">
                            {{-- Checkbox --}}
                            <td style="padding:8px 8px 8px 12px;width:36px;text-align:center" onclick="event.stopPropagation()">
                                <input type="checkbox"
                                       style="width:16px;height:16px;cursor:pointer;accent-color:#1a1a1a"
                                       :checked="selected.includes({{ $product->id }})"
                                       @change="toggle({{ $product->id }})">
                            </td>
                            {{-- Product (image + name) --}}
                            <td style="padding:8px 12px">
                                <div class="flex items-center gap-3">
                                    @if($product->primary_image_url)
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                             style="width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid #e1e1e1;flex-shrink:0">
                                    @else
                                        <div style="width:40px;height:40px;border-radius:8px;background:#f1f1f1;border:1px solid #e1e1e1;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="#b5b5b5"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                                        </div>
                                    @endif
                                    <span style="font-size:13px;font-weight:500;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;display:block">
                                        {{ $product->name }}
                                    </span>
                                </div>
                            </td>
                            {{-- Status --}}
                            <td style="padding:8px 12px">
                                @if($product->is_active)
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:12px;font-weight:500;background:#c8f9d4;color:#1a7f37">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#1a7f37;display:inline-block"></span>
                                        Active
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:12px;font-weight:500;background:#e4e4e4;color:#616161">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#8a8a8a;display:inline-block"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            {{-- Inventory --}}
                            <td style="padding:8px 12px;font-size:13px;color:#323232;white-space:nowrap">
                                @php
                                    $variantCount = $product->variants_count ?? ($product->relationLoaded('variants') ? $product->variants->count() : 0);
                                @endphp
                                @if($product->stock_quantity <= 0)
                                    <span style="color:#b92020">0 in stock{{ $variantCount > 0 ? " for {$variantCount} variants" : '' }}</span>
                                @else
                                    {{ $product->stock_quantity }} in stock{{ $variantCount > 0 ? " for {$variantCount} variants" : '' }}
                                @endif
                            </td>
                            {{-- Category --}}
                            <td style="padding:8px 12px;font-size:13px;color:#323232">
                                {{ $product->category->name ?? '--' }}
                            </td>
                            {{-- Type --}}
                            <td style="padding:8px 12px;font-size:13px;color:#323232">
                                @if($product->is_featured)
                                    Featured
                                @else
                                    Standard
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:48px 12px;text-align:center">
                                <div class="flex flex-col items-center">
                                    <svg width="40" height="40" viewBox="0 0 20 20" fill="#c9c9c9" style="margin-bottom:12px"><path fill-rule="evenodd" d="M10 2L3 7v6l7 5 7-5V7l-7-5zM5 7.5L10 4.5l5 3-5 3-5-3zm0 1.8v4.2l5 3.5v-4.2L5 9.3zm10 4.2v-4.2l-5 3.5v4.2l5-3.5z" clip-rule="evenodd"/></svg>
                                    <p style="font-size:14px;font-weight:500;color:#1a1a1a;margin-bottom:4px">No products found</p>
                                    <p style="font-size:13px;color:#616161;margin-bottom:16px">Try changing the filters or add a new product.</p>
                                    <a href="{{ route('admin.products.create') }}"
                                       style="background:#1a1a1a;color:#fff;font-size:13px;font-weight:500;padding:7px 14px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center">
                                        Add product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->total() > 0)
            <div class="flex items-center justify-center gap-4" style="padding:12px;border-top:1px solid #e1e1e1">
                <div class="flex items-center gap-2">
                    @if($products->onFirstPage())
                        <span style="padding:6px;color:#c9c9c9;cursor:not-allowed">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" style="padding:6px;color:#616161;text-decoration:none;border-radius:6px;display:inline-flex" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='transparent'">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    @endif

                    <span style="font-size:13px;color:#323232">
                        {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }}
                    </span>

                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" style="padding:6px;color:#616161;text-decoration:none;border-radius:6px;display:inline-flex" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='transparent'">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    @else
                        <span style="padding:6px;color:#c9c9c9;cursor:not-allowed">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Import Modal --}}
    <div x-data="{ open: false }"
         @open-import-modal.window="open = true"
         x-show="open" x-cloak
         style="position:fixed;inset:0;z-index:50;overflow-y:auto">
        <div class="flex items-center justify-center" style="min-height:100vh;padding:16px">
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 style="position:fixed;inset:0;background:rgba(0,0,0,0.5)" @click="open = false"></div>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 style="position:relative;background:#fff;border-radius:14px;box-shadow:0 25px 50px rgba(0,0,0,0.25);width:100%;max-width:500px;overflow:hidden">

                <div style="padding:16px 20px;border-bottom:1px solid #e1e1e1;display:flex;align-items:center;justify-content:between">
                    <div class="flex items-center justify-between flex-1">
                        <h2 style="font-size:16px;font-weight:600;color:#1a1a1a">Import products</h2>
                        <button @click="open = false" style="padding:4px;background:none;border:none;cursor:pointer;color:#616161;border-radius:6px" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='none'">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" style="padding:20px">
                    @csrf
                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:13px;font-weight:500;color:#1a1a1a;margin-bottom:4px">CSV File <span style="color:#b92020">*</span></label>
                        <input type="file" name="csv_file" accept=".csv,.txt" required
                               style="width:100%;padding:7px 10px;font-size:13px;border:1px solid #c9c9c9;border-radius:8px;background:#fff">
                        <p style="font-size:12px;color:#616161;margin-top:4px">Max 10MB. Must contain a header row with column names.</p>
                    </div>

                    <div style="padding:12px;background:#f7f7f7;border-radius:8px;margin-bottom:16px">
                        <p style="font-size:12px;font-weight:600;color:#1a1a1a;margin-bottom:6px">Required columns</p>
                        <div class="flex flex-wrap gap-1" style="margin-bottom:10px">
                            <span style="padding:2px 6px;background:#e3f1ff;color:#005bd3;border-radius:4px;font-size:11px;font-weight:500">name</span>
                            <span style="padding:2px 6px;background:#e3f1ff;color:#005bd3;border-radius:4px;font-size:11px;font-weight:500">sku</span>
                            <span style="padding:2px 6px;background:#e3f1ff;color:#005bd3;border-radius:4px;font-size:11px;font-weight:500">price</span>
                        </div>
                        <p style="font-size:12px;font-weight:600;color:#1a1a1a;margin-bottom:6px">Optional columns</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach(['slug','category','seller','sale_price','cost_price','stock_quantity','description','short_description','is_active','is_featured','image_url','meta_title','meta_description'] as $col)
                                <span style="padding:2px 6px;background:#e4e4e4;color:#616161;border-radius:4px;font-size:11px">{{ $col }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="open = false"
                                style="padding:7px 14px;font-size:13px;font-weight:500;background:#fff;border:1px solid #8a8a8a;color:#323232;border-radius:8px;cursor:pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                style="padding:7px 14px;font-size:13px;font-weight:500;background:#1a1a1a;border:1px solid #1a1a1a;color:#fff;border-radius:8px;cursor:pointer">
                            Import products
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
