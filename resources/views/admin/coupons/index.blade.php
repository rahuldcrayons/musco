<x-layouts.admin>
    <x-slot name="title">Discounts</x-slot>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-semibold text-gray-900">Discounts</h1>
        <a href="{{ route('admin.coupons.create') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
            Create discount
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200"
         x-data="{
             tab: '{{ request('status', 'all') }}',
             search: '{{ request('search', '') }}',
             selected: [],
             allIds: {{ $coupons->pluck('id') }},
             toggleAll(checked) { this.selected = checked ? [...this.allIds] : []; },
             isAllSelected() { return this.allIds.length > 0 && this.selected.length === this.allIds.length; }
         }">

        {{-- Tabs --}}
        <div class="flex items-center gap-0 px-4" style="border-bottom:1px solid #e1e1e1">
            @php
                $tabs = [
                    'all' => 'All',
                    'active' => 'Active',
                    'expired' => 'Expired',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('admin.coupons.index', array_merge(request()->except('status', 'page'), $key === 'all' ? [] : ['status' => $key])) }}"
                   class="relative px-4 py-3 text-sm font-medium transition-colors
                          {{ request('status', 'all') === $key ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                    @if(request('status', 'all') === $key)
                        <span class="absolute bottom-0 left-4 right-4 h-0.5 bg-gray-900 rounded-full"></span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <div class="px-4 py-3" style="border-bottom:1px solid #e1e1e1">
            <form action="{{ route('admin.coupons.index') }}" method="GET" class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search discounts"
                           class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="border-bottom:1px solid #e1e1e1">
                        <th class="pl-4 pr-0 py-3 w-10">
                            <input type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                                   @change="toggleAll($event.target.checked)"
                                   :checked="isAllSelected()">
                        </th>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Value</th>
                        <th class="px-4 py-3">Usage</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Expires</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50 cursor-pointer group" style="border-bottom:1px solid #e1e1e1"
                            onclick="if(!event.target.closest('input[type=checkbox]')) window.location='{{ route('admin.coupons.edit', $coupon) }}'">
                            <td class="pl-4 pr-0 py-3 w-10" onclick="event.stopPropagation()">
                                <input type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-500"
                                       value="{{ $coupon->id }}"
                                       :checked="selected.includes({{ $coupon->id }})"
                                       @change="selected.includes({{ $coupon->id }}) ? selected.splice(selected.indexOf({{ $coupon->id }}), 1) : selected.push({{ $coupon->id }})">
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900">{{ $coupon->code }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ ucfirst(str_replace('_', ' ', $coupon->type)) }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 font-medium">
                                @if($coupon->type === 'percentage')
                                    {{ $coupon->value }}%
                                @elseif($coupon->type === 'free_shipping')
                                    Free shipping
                                @else
                                    @price($coupon->value)
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $coupon->times_used ?? 0 }}{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($coupon->isValid())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @elseif($coupon->expires_at?->isPast())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($coupon->expires_at)
                                    {{ $coupon->expires_at->format('M d, Y') }}
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-gray-500 text-sm">No discounts found</p>
                                <a href="{{ route('admin.coupons.create') }}"
                                   class="inline-flex items-center mt-3 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                                    Create discount
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($coupons->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #e1e1e1">
                <div class="text-sm text-gray-600">
                    Showing {{ $coupons->firstItem() }}-{{ $coupons->lastItem() }} of {{ $coupons->total() }}
                </div>
                <div class="flex items-center gap-1">
                    @if($coupons->onFirstPage())
                        <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded-lg cursor-not-allowed">&lsaquo;</span>
                    @else
                        <a href="{{ $coupons->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">&lsaquo;</a>
                    @endif
                    @if($coupons->hasMorePages())
                        <a href="{{ $coupons->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">&rsaquo;</a>
                    @else
                        <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded-lg cursor-not-allowed">&rsaquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
