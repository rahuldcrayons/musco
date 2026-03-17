<x-layouts.admin>
    <x-slot name="title">Newsletter Subscribers</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Newsletter Subscribers</h1>
            <p class="text-neutral-600">Manage Jikra email subscribers</p>
        </div>
        <a href="{{ route('admin.newsletter.export') }}{{ request()->hasAny(['status']) ? '?' . request()->getQueryString() : '' }}"
           class="btn btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-success-50 border border-success-200 rounded-xl text-sm text-success-700">
            <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card p-4 sm:p-5 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Total Subscribers</p>
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
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-neutral-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-neutral-600">Unsubscribed</p>
                <p class="text-xl sm:text-2xl font-bold text-neutral-600">{{ number_format($stats['inactive']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" x-data="{ open: {{ request()->hasAny(['search', 'status', 'source']) ? 'true' : 'false' }} }">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters & Search
                @if(request()->hasAny(['search', 'status', 'source']))
                    <span class="badge badge-primary">Active</span>
                @endif
            </div>
            <svg class="w-4 h-4 text-neutral-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div x-show="open" x-collapse class="px-5 pb-4 border-t border-neutral-200">
            <form action="{{ route('admin.newsletter.index') }}" method="GET" class="pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Email or name..."
                               class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input w-full">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Unsubscribed</option>
                        </select>
                    </div>
                    @if($stats['sources']->isNotEmpty())
                        <div>
                            <label class="form-label">Source</label>
                            <select name="source" class="form-input w-full">
                                <option value="">All Sources</option>
                                @foreach($stats['sources'] as $src)
                                    <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>
                                        {{ ucfirst($src) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'status', 'source']))
                        <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden"
         x-data="{
             selected: [],
             toggleAll(checked, ids) { this.selected = checked ? ids : []; },
             toggle(id) {
                 const idx = this.selected.indexOf(id);
                 idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
             }
         }">
        <!-- Bulk Actions Bar -->
        <div x-show="selected.length > 0"
             class="px-5 py-3 bg-primary-50 border-b border-primary-200 flex items-center justify-between gap-3">
            <span class="text-sm font-medium text-primary-700">
                <span x-text="selected.length"></span> selected
            </span>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.newsletter.bulk-action') }}"
                      @submit.prevent="$el.querySelector('input[name=ids]').value = JSON.stringify(selected); $el.submit()">
                    @csrf
                    <input type="hidden" name="ids" value="">
                    <div class="flex gap-2">
                        <button type="submit" name="action" value="activate" class="btn btn-sm btn-secondary text-success-600">Activate</button>
                        <button type="submit" name="action" value="deactivate" class="btn btn-sm btn-secondary text-warning-600">Deactivate</button>
                        <button type="submit" name="action" value="delete"
                                onclick="return confirm('Delete selected subscribers?')"
                                class="btn btn-sm btn-secondary text-danger-600">Delete</button>
                    </div>
                </form>
            </div>
        </div>

        @if($subscribers->total() > 0)
            <div class="px-5 py-3 border-b border-neutral-200">
                {{ $subscribers->links('vendor.pagination.info-bar') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-5 py-3 text-left">
                            @php $ids = $subscribers->pluck('id')->toArray(); @endphp
                            <input type="checkbox" class="form-checkbox"
                                   @change="toggleAll($event.target.checked, {{ json_encode($ids) }})"
                                   :checked="selected.length === {{ count($ids) }} && {{ count($ids) }} > 0">
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider hidden sm:table-cell">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider hidden md:table-cell">Source</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider hidden lg:table-cell">Subscribed</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($subscribers as $subscriber)
                        <tr class="hover:bg-neutral-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <input type="checkbox" class="form-checkbox"
                                       :checked="selected.includes({{ $subscriber->id }})"
                                       @change="toggle({{ $subscriber->id }})">
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-neutral-900 text-sm">{{ $subscriber->email }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-neutral-600 hidden sm:table-cell">
                                {{ $subscriber->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 hidden md:table-cell">
                                <span class="badge badge-neutral text-xs">{{ ucfirst($subscriber->source) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($subscriber->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-neutral">Unsubscribed</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-neutral-600 hidden lg:table-cell">
                                {{ ($subscriber->subscribed_at ?? $subscriber->created_at)->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <form action="{{ route('admin.newsletter.toggle-status', $subscriber) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-icon"
                                                title="{{ $subscriber->is_active ? 'Unsubscribe' : 'Reactivate' }}">
                                            @if($subscriber->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.newsletter.destroy', $subscriber) }}" method="POST"
                                          onsubmit="return confirm('Remove this subscriber?')" class="inline">
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
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-neutral-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-neutral-700 mb-1">No subscribers found</p>
                                    <p class="text-sm text-neutral-600">
                                        @if(request()->hasAny(['search', 'status', 'source']))
                                            No subscribers match your current filters.
                                        @else
                                            Subscribers will appear here once people sign up.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'status', 'source']))
                                        <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary btn-sm mt-3">Clear Filters</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscribers->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
