<x-layouts.admin>
    <x-slot name="title">Staff</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Staff</h1>
            <p class="text-neutral-600">Manage staff members and their roles</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Staff
        </a>
    </div>
    <div class="card overflow-hidden">
        @if($staff->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $staff->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Joined</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    @forelse($staff as $member)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-sm font-bold text-primary-600">{{ strtoupper(substr($member->user->first_name ?? '', 0, 1) . substr($member->user->last_name ?? '', 0, 1)) }}</span>
                                    </div>
                                    <span class="font-medium text-neutral-900">{{ $member->user->full_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $member->user->email ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $member->role ?? 'staff')) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $member->is_active ? 'badge-success' : 'badge-error' }}">
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600">{{ $member->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.staff.edit', $member) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Edit</a>
                                    <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff member?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-600 hover:text-danger-700 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-neutral-600">
                                No staff members found.
                                <a href="{{ route('admin.staff.create') }}" class="text-primary-600 hover:text-primary-700 font-medium ml-1">Add one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staff->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $staff->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
