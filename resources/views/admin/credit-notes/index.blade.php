<x-layouts.admin>
    <x-slot name="title">Credit Notes</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Credit Notes</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage customer store credit from refunds</p>
            </div>
        </div>
    </x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Total Notes</p>
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
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-info-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Total Issued</p>
                <p class="text-xl sm:text-2xl font-bold text-info-600">@price($stats['total_amount'])</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-warning-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Outstanding</p>
                <p class="text-xl sm:text-2xl font-bold text-warning-600">@price($stats['total_remaining'])</p>
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
            <form action="{{ route('admin.credit-notes.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Credit note #, customer name, email or order #..."
                               class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input w-full">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="partially_used" {{ request('status') === 'partially_used' ? 'selected' : '' }}>Partially Used</option>
                            <option value="fully_used" {{ request('status') === 'fully_used' ? 'selected' : '' }}>Fully Used</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
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
                                <a href="{{ route('admin.credit-notes.index') }}" class="btn btn-secondary shrink-0" title="Reset filters">
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

    <!-- Credit Notes Table -->
    <div class="card overflow-hidden">
        @if($creditNotes->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $creditNotes->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Credit Note</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Customer</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Order / Return</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Amount</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Remaining</th>
                        <th class="px-5 py-3 text-center text-xs font-medium text-neutral-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Expires</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($creditNotes as $note)
                        <tr class="hover:bg-neutral-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.credit-notes.show', $note) }}" class="font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                    {{ $note->credit_note_number }}
                                </a>
                                <p class="text-xs text-neutral-600 mt-0.5">{{ $note->created_at->format('M d, Y h:i A') }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 bg-linear-to-br from-primary-50 to-purple-50 rounded-full flex items-center justify-center ring-1 ring-neutral-200 shrink-0">
                                        <span class="text-xs font-bold text-primary-500">{{ strtoupper(substr($note->user->first_name ?? 'G', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900">{{ $note->user->full_name ?? 'N/A' }}</p>
                                        <p class="text-xs text-neutral-600">{{ $note->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($note->order)
                                    <a href="{{ route('admin.orders.show', $note->order) }}" class="text-sm text-primary-600 hover:text-primary-700">{{ $note->order->order_number }}</a>
                                @else
                                    <span class="text-sm text-neutral-600">-</span>
                                @endif
                                @if($note->return)
                                    <p class="text-xs text-neutral-600">Return: <a href="{{ route('admin.returns.show', $note->return) }}" class="hover:text-primary-600">{{ $note->return->return_number }}</a></p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right font-medium">@price($note->amount)</td>
                            <td class="px-5 py-3.5 text-right font-semibold {{ $note->remaining_amount > 0 ? 'text-success-600' : 'text-neutral-600' }}">
                                @price($note->remaining_amount)
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @php
                                    $statusClass = match($note->status) {
                                        'active' => 'badge-success',
                                        'partially_used' => 'badge-info',
                                        'fully_used' => 'badge-neutral',
                                        'expired' => 'badge-error',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $note->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm">
                                @if($note->expires_at)
                                    <span class="{{ $note->expires_at->isPast() ? 'text-error-500' : 'text-neutral-600' }}">
                                        {{ $note->expires_at->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-neutral-600">Never</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.credit-notes.show', $note) }}" class="btn-icon" title="View details">
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
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-neutral-900 mb-1">No credit notes found</h3>
                                    <p class="text-sm text-neutral-600">
                                        @if(request()->hasAny(['search', 'status']))
                                            Try adjusting your filters to find what you're looking for.
                                        @else
                                            Credit notes will appear here when refunds are processed.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($creditNotes->hasPages())
            <div class="px-5 py-3 border-t border-neutral-200">
                {{ $creditNotes->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
