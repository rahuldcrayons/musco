<x-layouts.admin>
    <x-slot name="title">Delivery Partners</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Delivery Partners</h1>
            <p class="text-neutral-600">Manage delivery partners and assignments</p>
        </div>
        <a href="{{ route('admin.delivery-partners.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Partner
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Total</p>
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
                <p class="text-xs text-neutral-600">Active</p>
                <p class="text-2xl font-bold text-success-600">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Inactive</p>
                <p class="text-2xl font-bold text-neutral-600">{{ $stats['inactive'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-warning-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">On Delivery</p>
                <p class="text-2xl font-bold text-warning-600">{{ $stats['on_delivery'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'status', 'vehicle_type']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="text-sm font-medium text-neutral-700">Filters & Search</span>
                @if(request()->hasAny(['search', 'status', 'vehicle_type']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-5 h-5 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-collapse class="px-5 pb-4 border-t border-neutral-200">
            <form action="{{ route('admin.delivery-partners.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" placeholder="Name, email, phone...">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select w-full">
                            <option value="">All</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Vehicle Type</label>
                        <select name="vehicle_type" class="form-select w-full">
                            <option value="">All</option>
                            <option value="bike" @selected(request('vehicle_type') === 'bike')>Bike</option>
                            <option value="scooter" @selected(request('vehicle_type') === 'scooter')>Scooter</option>
                            <option value="van" @selected(request('vehicle_type') === 'van')>Van</option>
                            <option value="truck" @selected(request('vehicle_type') === 'truck')>Truck</option>
                            <option value="other" @selected(request('vehicle_type') === 'other')>Other</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                    @if(request()->hasAny(['search', 'status', 'vehicle_type']))
                        <a href="{{ route('admin.delivery-partners.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Partner</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Vehicle</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Verification</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Joined</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    @forelse($partners as $partner)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-sm font-bold text-primary-600">{{ strtoupper(substr($partner->user->first_name ?? '', 0, 1) . substr($partner->user->last_name ?? '', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-neutral-900">{{ $partner->user->full_name ?? 'N/A' }}</span>
                                        <p class="text-xs text-neutral-600">{{ $partner->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-medium text-neutral-700">{{ $partner->partner_id }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $partner->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="badge badge-info">{{ ucfirst($partner->vehicle_type) }}</span>
                                @if($partner->vehicle_number)
                                    <span class="text-xs text-neutral-600 ml-1">{{ $partner->vehicle_number }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $partner->is_active ? 'badge-success' : 'badge-error' }}">
                                    {{ $partner->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $vBadge = match($partner->verification_status) {
                                        'verified' => 'badge-success',
                                        'rejected' => 'badge-error',
                                        default => 'badge-warning',
                                    };
                                @endphp
                                <span class="badge {{ $vBadge }}">{{ ucfirst($partner->verification_status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $partner->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.delivery-partners.show', $partner) }}" class="text-neutral-600 hover:text-neutral-700 text-sm font-medium">View</a>
                                    <a href="{{ route('admin.delivery-partners.edit', $partner) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Edit</a>
                                    <form action="{{ route('admin.delivery-partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this delivery partner?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-600 hover:text-danger-700 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-neutral-600">
                                No delivery partners found.
                                <a href="{{ route('admin.delivery-partners.create') }}" class="text-primary-600 hover:text-primary-700 font-medium ml-1">Add one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($partners->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $partners->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
