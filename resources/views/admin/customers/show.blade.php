<x-layouts.admin>
    <x-slot name="title">{{ $customer->full_name }}</x-slot>

    <div class="flex items-center gap-2 text-sm text-neutral-600 mb-6">
        <a href="{{ route('admin.customers.index') }}" class="hover:text-primary-600">Customers</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-neutral-900">{{ $customer->full_name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Info Card -->
            <div class="card p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-primary-600">{{ substr($customer->first_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-neutral-900">{{ $customer->full_name }}</h1>
                            <p class="text-neutral-600">{{ $customer->email }}</p>
                            @if($customer->phone)
                                <p class="text-neutral-600">{{ $customer->phone }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($customer->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-neutral">Inactive</span>
                        @endif
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-secondary btn-sm">Edit</a>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-lg">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-neutral-900">{{ $stats['total_orders'] }}</p>
                        <p class="text-sm text-neutral-600">Total Orders</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-neutral-900">@price($stats['total_spent'])</p>
                        <p class="text-sm text-neutral-600">Total Spent</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-neutral-900">@price($stats['avg_order_value'])</p>
                        <p class="text-sm text-neutral-600">Avg. Order Value</p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="p-4 border-b border-neutral-200 flex items-center justify-between">
                    <h2 class="font-semibold text-neutral-900">Recent Orders</h2>
                    <a href="{{ route('admin.customers.orders', $customer) }}" class="text-sm text-primary-600 hover:text-primary-700">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Order</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-neutral-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-primary-600 hover:text-primary-700">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-600">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge {{ $order->status === 'completed' ? 'badge-success' : ($order->status === 'pending' ? 'badge-warning' : 'badge-info') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-right">@price($order->total)</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-neutral-600">
                                        No orders yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Details -->
            <div class="card p-6">
                <h2 class="font-semibold text-neutral-900 mb-4">Account Details</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-neutral-600">Member Since</dt>
                        <dd class="font-medium">{{ $customer->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-600">Last Login</dt>
                        <dd class="font-medium">{{ $customer->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-600">Email Verified</dt>
                        <dd>
                            @if($customer->email_verified_at)
                                <span class="text-success-600">Yes</span>
                            @else
                                <span class="text-warning-600">No</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Addresses -->
            <div class="card p-6">
                <h2 class="font-semibold text-neutral-900 mb-4">Saved Addresses</h2>
                @if($customer->addresses && $customer->addresses->count())
                    <div class="space-y-4">
                        @foreach($customer->addresses as $address)
                            <div class="p-3 bg-neutral-50 rounded-lg text-sm">
                                @if($address->is_default)
                                    <span class="badge badge-primary mb-2">Default</span>
                                @endif
                                <p class="font-medium text-neutral-900">{{ $address->name }}</p>
                                <p class="text-neutral-600">{{ $address->address_line1 }}</p>
                                @if($address->address_line2)
                                    <p class="text-neutral-600">{{ $address->address_line2 }}</p>
                                @endif
                                <p class="text-neutral-600">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                <p class="text-neutral-600">{{ $address->country }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-neutral-600 text-sm">No saved addresses</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="card p-6">
                <h2 class="font-semibold text-neutral-900 mb-4">Actions</h2>
                <div class="space-y-2">
                    <form action="{{ route('admin.customers.toggle-status', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if($customer->is_active)
                            <button type="submit" class="btn btn-secondary w-full text-warning-600">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Deactivate Account
                            </button>
                        @else
                            <button type="submit" class="btn btn-secondary w-full text-success-600">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Activate Account
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
