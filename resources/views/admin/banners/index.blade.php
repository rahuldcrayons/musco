<x-layouts.admin>
    <x-slot name="title">Banners</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Banners</h1>
            <p class="text-neutral-600">Manage promotional banners</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Banner
        </a>
    </div>
    <div class="card overflow-hidden">
        @if($banners->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $banners->links('vendor.pagination.info-bar') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Banner</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Position</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-neutral-600 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Schedule</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($banner->image_url)
                                        <img src="{{ asset('storage/' . $banner->image_url) }}" alt="{{ $banner->name }}"
                                             class="w-20 h-12 object-cover rounded border border-neutral-200">
                                    @else
                                        <div class="w-20 h-12 bg-neutral-100 rounded border border-neutral-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-neutral-900">{{ $banner->name }}</p>
                                        @if($banner->link)
                                            <p class="text-sm text-neutral-600 truncate max-w-[200px]">{{ $banner->link }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge badge-info">{{ $banner->position }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-medium">{{ $banner->priority }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-600">
                                @if($banner->starts_at || $banner->ends_at)
                                    @if($banner->starts_at)
                                        <p>{{ $banner->starts_at->format('M d, Y H:i') }}</p>
                                    @endif
                                    @if($banner->ends_at)
                                        <p class="text-neutral-600">to {{ $banner->ends_at->format('M d, Y H:i') }}</p>
                                    @endif
                                @else
                                    <span class="text-neutral-600">Always</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($banner->isActive())
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Edit</a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner?')">
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
                                No banners found.
                                <a href="{{ route('admin.banners.create') }}" class="text-primary-600 hover:text-primary-700 font-medium ml-1">Create one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
