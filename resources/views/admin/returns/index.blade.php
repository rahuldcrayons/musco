<x-layouts.admin>
    <x-slot name="title">Returns</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Returns</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage return and refund requests</p>
            </div>
        </div>
    </x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Total</p>
                <p class="text-xl sm:text-2xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-warning-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Requested</p>
                <p class="text-xl sm:text-2xl font-bold text-warning-600">{{ number_format($stats['requested']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-info-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Approved</p>
                <p class="text-xl sm:text-2xl font-bold text-info-600">{{ number_format($stats['approved']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Received</p>
                <p class="text-xl sm:text-2xl font-bold text-purple-600">{{ number_format($stats['received']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-success-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Completed</p>
                <p class="text-xl sm:text-2xl font-bold text-success-600">{{ number_format($stats['completed']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-error-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Rejected</p>
                <p class="text-xl sm:text-2xl font-bold text-error-600">{{ number_format($stats['rejected']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'status']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters
                @if(request()->hasAny(['search', 'status']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-4 h-4 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-cloak x-transition class="px-5 pb-4 border-t border-neutral-100">
            <form action="{{ route('admin.returns.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Return #, order # or customer..."
                               class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input w-full">
                            <option value="">All Status</option>
                            <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>Requested</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="pickup_scheduled" {{ request('status') === 'pickup_scheduled' ? 'selected' : '' }}>Pickup Scheduled</option>
                            <option value="picked_up" {{ request('status') === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                            <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                            <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Refund Processed</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">&nbsp;</label>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary shrink-0" title="Apply filters">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary shrink-0" title="Reset filters">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Returns Table -->
    <div class="card overflow-hidden">
        @if($returns->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $returns->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Return</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Customer</th>
                        <th class="px-5 py-3 text-center text-xs font-medium text-neutral-600 uppercase tracking-wider">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Reason</th>
                        <th class="px-5 py-3 text-center text-xs font-medium text-neutral-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Refund</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($returns as $return)
                        <tr class="hover:bg-neutral-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.returns.show', $return) }}" class="font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                    {{ $return->return_number }}
                                </a>
                                <p class="text-xs text-neutral-600 mt-0.5">
                                    Order: <a href="{{ route('admin.orders.show', $return->order_id) }}" class="text-neutral-600 hover:text-primary-600">{{ $return->order->order_number ?? 'N/A' }}</a>
                                </p>
                                <p class="text-xs text-neutral-600 mt-0.5">{{ $return->created_at->format('M d, Y h:i A') }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 bg-linear-to-br from-primary-50 to-purple-50 rounded-full flex items-center justify-center ring-1 ring-neutral-200 shrink-0">
                                        <span class="text-xs font-bold text-primary-500">{{ strtoupper(substr($return->order->user->first_name ?? 'G', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900">{{ $return->order->user->full_name ?? 'N/A' }}</p>
                                        <p class="text-xs text-neutral-600">{{ $return->order->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @php
                                    $typeClass = match($return->type ?? 'return') {
                                        'return' => 'bg-info-50 text-info-700',
                                        'refund' => 'bg-warning-50 text-warning-700',
                                        'exchange' => 'bg-purple-50 text-purple-700',
                                        default => 'bg-neutral-100 text-neutral-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $typeClass }}">
                                    {{ ucfirst($return->type ?? 'Return') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-sm text-neutral-600 max-w-48 truncate">{{ $return->reason ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @php
                                    $statusClass = match($return->status) {
                                        'requested' => 'badge-warning',
                                        'approved', 'pickup_scheduled', 'picked_up' => 'badge-info',
                                        'received', 'processed' => 'badge-primary',
                                        'rejected' => 'badge-error',
                                        'completed' => 'badge-success',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $return->status === 'processed' ? 'Refund Processed' : ucfirst(str_replace('_', ' ', $return->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($return->refund_amount)
                                    <span class="font-semibold text-success-600">@price($return->refund_amount)</span>
                                @else
                                    <span class="text-sm text-neutral-600">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.returns.show', $return) }}" class="btn-icon" title="View return">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-neutral-900 mb-1">No returns found</h3>
                                    <p class="text-sm text-neutral-600">
                                        @if(request()->hasAny(['search', 'status']))
                                            Try adjusting your filters to find what you're looking for.
                                        @else
                                            Return requests will appear here when customers submit them.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="px-5 py-3 border-t border-neutral-200">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
