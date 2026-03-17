<x-layouts.admin>
    <x-slot name="title">Flash Sales</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Flash Sales</h1>
            <p class="text-neutral-600">Manage limited-time sale events</p>
        </div>
        <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Flash Sale
        </a>
    </div>

    {{-- Stats --}}
    @php
        $now           = now();
        $totalSales    = \App\Models\FlashSale::count();
        $liveSales     = \App\Models\FlashSale::where('starts_at', '<=', $now)->where('ends_at', '>=', $now)->count();
        $upcomingSales = \App\Models\FlashSale::where('starts_at', '>', $now)->count();
        $endedSales    = \App\Models\FlashSale::where('ends_at', '<', $now)->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Total</p>
                <p class="text-2xl font-bold text-neutral-900">{{ $totalSales }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-success-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Live Now</p>
                <p class="text-2xl font-bold text-success-600">{{ $liveSales }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-info-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Upcoming</p>
                <p class="text-2xl font-bold text-info-600">{{ $upcomingSales }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Ended</p>
                <p class="text-2xl font-bold text-neutral-600">{{ $endedSales }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'status']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="text-sm font-medium text-neutral-700">Filters & Search</span>
                @if(request()->hasAny(['search', 'status']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-5 h-5 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-collapse class="px-5 pb-4 border-t border-neutral-200">
            <form action="{{ route('admin.flash-sales.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-input w-full" placeholder="Search by name...">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select w-full">
                            <option value="">All Statuses</option>
                            <option value="live" @selected(request('status') === 'live')>Live</option>
                            <option value="upcoming" @selected(request('status') === 'upcoming')>Upcoming</option>
                            <option value="ended" @selected(request('status') === 'ended')>Ended</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        @if($flashSales->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $flashSales->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Products</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Schedule</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($flashSales as $sale)
                        <tr class="hover:bg-neutral-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.flash-sales.edit', $sale) }}"
                                   class="font-medium text-neutral-900 hover:text-primary-600 transition-colors">{{ $sale->name }}</a>
                                @if($sale->description)
                                    <p class="text-sm text-neutral-600 truncate max-w-62.5">{{ $sale->description }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="text-sm font-medium text-neutral-700">{{ $sale->products_count }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-neutral-600">
                                <p>{{ $sale->starts_at->format('M d, Y H:i') }}</p>
                                <p class="text-neutral-600">to {{ $sale->ends_at->format('M d, Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($sale->isActive())
                                    <span class="badge badge-success">Live</span>
                                @elseif($sale->isUpcoming())
                                    <span class="badge badge-info">Upcoming</span>
                                @elseif($sale->hasEnded())
                                    <span class="badge badge-error">Ended</span>
                                @else
                                    <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.flash-sales.edit', $sale) }}" class="btn-icon" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.flash-sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Delete this flash sale?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-danger-500 hover:text-danger-700 hover:bg-danger-50" title="Delete">
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
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-neutral-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-neutral-700 mb-1">No flash sales found</p>
                                    <p class="text-sm text-neutral-600 mb-3">
                                        @if(request()->hasAny(['search', 'status']))
                                            No sales match your current filters.
                                        @else
                                            You haven't created any flash sales yet.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'status']))
                                        <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
                                    @else
                                        <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary btn-sm">Create First Sale</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($flashSales->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $flashSales->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
