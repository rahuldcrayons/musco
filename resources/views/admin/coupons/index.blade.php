<x-layouts.admin>
    <x-slot name="title">Coupons</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Coupons</h1>
            <p class="text-sm text-neutral-600 mt-1">Manage discount coupons and promotions</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Coupon
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-success-50 border border-success-200 text-success-700 text-sm rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Total Coupons</p>
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
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-error-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Expired</p>
                <p class="text-xl sm:text-2xl font-bold text-error-600">{{ number_format($stats['expired']) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-info-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Auto Apply</p>
                <p class="text-xl sm:text-2xl font-bold text-info-600">{{ number_format($stats['auto_apply']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'type', 'status']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters & Search
                @if(request()->hasAny(['search', 'type', 'status']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-4 h-4 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-cloak x-transition class="px-5 pb-4 border-t border-neutral-100">
            <form action="{{ route('admin.coupons.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Coupon code or name..."
                               class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label">Type</label>
                        <select name="type" class="form-input w-full">
                            <option value="">All Types</option>
                            <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="free_shipping" {{ request('type') === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                            <option value="buy_x_get_y" {{ request('type') === 'buy_x_get_y' ? 'selected' : '' }}>Buy X Get Y</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input w-full">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Coupons Table -->
    <div class="card overflow-hidden">
        @if($coupons->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $coupons->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Value</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Used</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Validity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-3">
                                <span class="font-mono font-semibold text-sm text-neutral-900 bg-neutral-100 px-2 py-1 rounded">{{ $coupon->code }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-700">{{ $coupon->name }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $typeBadges = [
                                        'percentage' => 'badge-info',
                                        'fixed' => 'badge-success',
                                        'free_shipping' => 'badge-warning',
                                        'buy_x_get_y' => 'badge-primary',
                                    ];
                                @endphp
                                <span class="badge {{ $typeBadges[$coupon->type] ?? 'badge-info' }}">
                                    {{ ucfirst(str_replace('_', ' ', $coupon->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-medium">
                                @if($coupon->type === 'percentage')
                                    {{ $coupon->value }}%
                                @elseif($coupon->type === 'free_shipping')
                                    <span class="text-success-600">Free</span>
                                @else
                                    @price($coupon->value)
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-neutral-600">
                                <span class="font-medium">{{ $coupon->times_used ?? 0 }}</span>{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">
                                @if($coupon->starts_at && $coupon->expires_at)
                                    <div>
                                        <span class="text-neutral-700">{{ $coupon->starts_at->format('M d') }}</span>
                                        <span class="text-neutral-600 mx-0.5">-</span>
                                        <span class="text-neutral-700">{{ $coupon->expires_at->format('M d, Y') }}</span>
                                    </div>
                                @elseif($coupon->expires_at)
                                    Until {{ $coupon->expires_at->format('M d, Y') }}
                                @else
                                    <span class="text-neutral-600">No expiry</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    @if($coupon->isValid())
                                        <span class="badge badge-success">Active</span>
                                    @elseif($coupon->expires_at?->isPast())
                                        <span class="badge badge-error">Expired</span>
                                    @else
                                        <span class="badge badge-neutral">Inactive</span>
                                    @endif
                                    @if($coupon->auto_apply)
                                        <span class="badge badge-primary">Auto</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-icon" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Delete this coupon?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-error-500 hover:text-error-700" title="Delete">
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
                            <td colspan="8" class="px-4 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-neutral-900 mb-2">No coupons found</h3>
                                <p class="text-neutral-600 mb-4">Create your first coupon to get started.</p>
                                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add Coupon
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
