<x-layouts.admin>
    <x-slot name="title">Fraud Review Queue</x-slot>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Fraud Review Queue</h1>
            <p class="text-neutral-600">Monitor and review flagged orders for potential fraud</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-neutral-600">Total</p>
                    <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['total'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-warning-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-neutral-600">Flagged</p>
                    <p class="text-2xl font-bold text-warning-600">{{ number_format($stats['flagged'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-error-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-neutral-600">Blocked</p>
                    <p class="text-2xl font-bold text-error-600">{{ number_format($stats['blocked'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="card px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-neutral-600">Unreviewed</p>
                    <p class="text-2xl font-bold text-info-600">{{ number_format($stats['unreviewed'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'action', 'reviewed']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters & Search
                @if(request()->hasAny(['search', 'action', 'reviewed']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-4 h-4 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-cloak x-transition class="px-5 pb-4 border-t border-neutral-100">
            <form action="{{ route('admin.fraud.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label">Action</label>
                        <select name="action" class="form-input w-full">
                            <option value="">All Actions</option>
                            <option value="flagged" {{ request('action') === 'flagged' ? 'selected' : '' }}>Flagged</option>
                            <option value="blocked" {{ request('action') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                            <option value="allowed" {{ request('action') === 'allowed' ? 'selected' : '' }}>Allowed</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Reviewed</label>
                        <select name="reviewed" class="form-input w-full">
                            <option value="">All</option>
                            <option value="yes" {{ request('reviewed') === 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ request('reviewed') === 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Order #, customer name or email..."
                               class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label">&nbsp;</label>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary shrink-0" title="Apply filters">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                            @if(request()->hasAny(['search', 'action', 'reviewed']))
                                <a href="{{ route('admin.fraud.index') }}" class="btn btn-secondary shrink-0" title="Reset filters">
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

    <!-- Fraud Logs Table -->
    <div class="card overflow-hidden">
        @if($fraudLogs->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                <p class="text-sm text-neutral-600">
                    Showing <span class="font-medium text-neutral-900">{{ $fraudLogs->firstItem() }}</span>–<span class="font-medium text-neutral-900">{{ $fraudLogs->lastItem() }}</span> of <span class="font-medium text-neutral-900">{{ $fraudLogs->total() }}</span> records
                </p>
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wide">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wide">Customer</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wide">Risk Score</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wide">Fraud Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wide">Action</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-neutral-600 uppercase tracking-wide">Reviewed</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wide"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($fraudLogs as $log)
                        <tr class="hover:bg-neutral-50/60 transition-colors">
                            <td class="px-4 py-3 text-sm text-neutral-600">
                                {{ $log->created_at->format('M d, Y') }}
                                <p class="text-xs text-neutral-600">{{ $log->created_at->format('h:i A') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->order)
                                    <a href="{{ route('admin.orders.show', $log->order_id) }}" class="font-medium text-primary-600 hover:text-primary-700 text-sm">
                                        {{ $log->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-sm text-neutral-600">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-neutral-200 rounded-full flex items-center justify-center shrink-0">
                                            <span class="text-xs font-bold text-neutral-600">{{ strtoupper(substr($log->user->first_name ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-neutral-900">{{ $log->user->full_name ?? $log->user->first_name . ' ' . $log->user->last_name }}</p>
                                            <p class="text-xs text-neutral-600">{{ $log->user->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-neutral-600">Guest</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $score = $log->risk_score ?? 0;
                                    if ($score >= 75) {
                                        $scoreClass = 'bg-error-100 text-error-700';
                                    } elseif ($score >= 50) {
                                        $scoreClass = 'bg-warning-100 text-warning-700';
                                    } elseif ($score >= 25) {
                                        $scoreClass = 'bg-info-100 text-info-700';
                                    } else {
                                        $scoreClass = 'bg-success-100 text-success-700';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $scoreClass }}">
                                    {{ $score }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-neutral-700">{{ ucfirst(str_replace('_', ' ', $log->type ?? '-')) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $actionClass = match($log->action ?? '') {
                                        'flagged' => 'badge-warning',
                                        'blocked' => 'badge-error',
                                        'allowed' => 'badge-success',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <span class="badge {{ $actionClass }}">
                                    {{ ucfirst($log->action ?? 'Pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($log->reviewed_at)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-success-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Yes
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-neutral-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        No
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.fraud.show', $log) }}" class="btn-icon" title="View details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-neutral-900 mb-1">No fraud logs found</h3>
                                    <p class="text-sm text-neutral-600">
                                        @if(request()->hasAny(['search', 'action', 'reviewed']))
                                            Try adjusting your filters to find what you're looking for.
                                        @else
                                            Fraud detection logs will appear here when orders are screened.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fraudLogs->hasPages())
            <div class="px-5 py-3 border-t border-neutral-200">
                {{ $fraudLogs->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
