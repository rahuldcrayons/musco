<x-layouts.admin>
    <x-slot name="title">Support Tickets</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Support Tickets</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage customer support tickets</p>
            </div>
        </div>
    </x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Total</p>
                <p class="text-xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-warning-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Open</p>
                <p class="text-xl font-bold text-warning-600">{{ number_format($stats['open']) }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-success-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Answered</p>
                <p class="text-xl font-bold text-success-600">{{ number_format($stats['answered']) }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-neutral-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-600">Closed</p>
                <p class="text-xl font-bold text-neutral-600">{{ number_format($stats['closed']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4">
            <form action="{{ route('admin.support-tickets.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, subject, customer..."
                           class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <select name="status" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Status</option>
                    <option value="open" @selected(request('status') === 'open')>Open</option>
                    <option value="answered" @selected(request('status') === 'answered')>Answered</option>
                    <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                </select>
                <select name="priority" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Priority</option>
                    <option value="low" @selected(request('priority') === 'low')>Low</option>
                    <option value="normal" @selected(request('priority') === 'normal')>Normal</option>
                    <option value="high" @selected(request('priority') === 'high')>High</option>
                </select>
                <button type="submit" class="btn btn-primary text-sm">Filter</button>
                @if(request()->hasAny(['search', 'status', 'priority', 'category']))
                    <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-secondary text-sm">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">ID</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Customer</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Subject</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Category</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Priority</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-neutral-600">Date</th>
                        <th class="text-right px-4 py-3 font-medium text-neutral-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-3 text-neutral-600">#{{ $ticket->id }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-neutral-900">{{ $ticket->user->full_name }}</p>
                                <p class="text-xs text-neutral-600">{{ $ticket->user->email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-neutral-800">{{ Str::limit($ticket->subject, 40) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-neutral-600 capitalize">{{ $ticket->category }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @switch($ticket->priority)
                                    @case('high')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-error-100 text-error-700">High</span>
                                        @break
                                    @case('normal')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-700">Normal</span>
                                        @break
                                    @case('low')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">Low</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3">
                                @switch($ticket->status)
                                    @case('open')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-700">Open</span>
                                        @break
                                    @case('answered')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-700">Answered</span>
                                        @break
                                    @case('closed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">Closed</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-neutral-600 text-xs">
                                {{ $ticket->created_at->format('M d, Y') }}
                                <span class="block">{{ $ticket->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="text-primary-600 hover:text-primary-700 text-xs font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-neutral-600">
                                <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <p>No support tickets found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($tickets->hasPages())
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @endif
</x-layouts.admin>
