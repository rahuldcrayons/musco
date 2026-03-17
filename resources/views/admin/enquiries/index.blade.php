<x-layouts.admin>
    <x-slot name="title">Enquiries</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Enquiries</h1>
                <p class="text-sm text-neutral-600 mt-1">Customer contact form submissions</p>
            </div>
        </div>
    </x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Total</p>
                <p class="text-xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-error-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">New</p>
                <p class="text-xl font-bold text-error-600">{{ number_format($stats['new']) }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-info-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Read</p>
                <p class="text-xl font-bold text-info-600">{{ number_format($stats['read']) }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-success-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Replied</p>
                <p class="text-xl font-bold text-success-600">{{ number_format($stats['replied']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4">
            <form action="{{ route('admin.enquiries.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or subject..."
                           class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <select name="status" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Status</option>
                    <option value="new" @selected(request('status') === 'new')>New</option>
                    <option value="read" @selected(request('status') === 'read')>Read</option>
                    <option value="replied" @selected(request('status') === 'replied')>Replied</option>
                    <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                </select>
                <button type="submit" class="btn btn-primary text-sm">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.enquiries.index') }}" class="btn btn-secondary text-sm">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Enquiries Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Sender</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Subject</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Date</th>
                        <th class="text-right px-4 py-3 font-medium text-neutral-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($enquiries as $enquiry)
                        <tr class="hover:bg-neutral-50 {{ !$enquiry->is_read ? 'bg-primary-50/30' : '' }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @unless($enquiry->is_read)
                                        <span class="w-2 h-2 bg-primary-500 rounded-full shrink-0"></span>
                                    @endunless
                                    <div>
                                        <p class="font-medium text-neutral-900 {{ !$enquiry->is_read ? 'font-semibold' : '' }}">{{ $enquiry->name }}</p>
                                        <p class="text-xs text-neutral-600">{{ $enquiry->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-neutral-800 {{ !$enquiry->is_read ? 'font-semibold' : '' }}">{{ Str::limit($enquiry->subject, 50) }}</p>
                                <p class="text-xs text-neutral-600 mt-0.5">{{ Str::limit($enquiry->message, 60) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @switch($enquiry->status)
                                    @case('new')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-error-100 text-error-700">New</span>
                                        @break
                                    @case('read')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-700">Read</span>
                                        @break
                                    @case('replied')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-700">Replied</span>
                                        @break
                                    @case('closed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-700">Closed</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-neutral-600">
                                {{ $enquiry->created_at->format('M d, Y') }}
                                <span class="block text-xs">{{ $enquiry->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium text-primary-600 hover:bg-primary-50 transition-colors" title="View">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                    <form action="{{ route('admin.enquiries.toggle-read', $enquiry) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium text-neutral-600 hover:bg-neutral-100 transition-colors" title="{{ $enquiry->is_read ? 'Mark as unread' : 'Mark as read' }}">
                                            @if($enquiry->is_read)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                Unread
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5"/>
                                                </svg>
                                                Read
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.enquiries.destroy', $enquiry) }}" method="POST"
                                          onsubmit="return confirm('Delete this enquiry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium text-error-600 hover:bg-error-50 transition-colors" title="Delete">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-neutral-600">
                                <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p>No enquiries found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($enquiries->hasPages())
        <div class="mt-4">
            {{ $enquiries->links() }}
        </div>
    @endif
</x-layouts.admin>
