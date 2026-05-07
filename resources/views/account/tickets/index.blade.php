<x-layouts.app>
    <x-slot name="title">Support Tickets</x-slot>

    @include('account.partials.sidebar')

                <div class="flex items-center justify-between mb-5">
                    <h1 class="text-xl font-bold text-neutral-900">Support Tickets</h1>
                    <a href="{{ route('account.tickets.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#202a40] text-white text-sm font-semibold rounded-lg hover:bg-[#151e30] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Raise Ticket
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
                @endif

                <div class="flex gap-2 mb-4">
                    <a href="{{ route('account.tickets.index') }}" class="px-3 py-1.5 rounded-full text-xs font-medium {{ !request('status') ? 'bg-[#202a40]/10 text-[#202a40]' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">All</a>
                    <a href="{{ route('account.tickets.index', ['status' => 'open']) }}" class="px-3 py-1.5 rounded-full text-xs font-medium {{ request('status') === 'open' ? 'bg-[#202a40]/10 text-[#202a40]' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">Open</a>
                    <a href="{{ route('account.tickets.index', ['status' => 'answered']) }}" class="px-3 py-1.5 rounded-full text-xs font-medium {{ request('status') === 'answered' ? 'bg-[#202a40]/10 text-[#202a40]' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">Answered</a>
                    <a href="{{ route('account.tickets.index', ['status' => 'closed']) }}" class="px-3 py-1.5 rounded-full text-xs font-medium {{ request('status') === 'closed' ? 'bg-[#202a40]/10 text-[#202a40]' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition-colors">Closed</a>
                </div>

                <div class="space-y-3">
                    @forelse($tickets as $ticket)
                        <a href="{{ route('account.tickets.show', $ticket) }}" class="block bg-white border border-neutral-100 rounded-xl p-4 hover:border-[#202a40]/20 hover:shadow-sm transition-all">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs text-neutral-400">#{{ $ticket->id }}</span>
                                        @switch($ticket->status)
                                            @case('open')<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Open</span>@break
                                            @case('answered')<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Answered</span>@break
                                            @case('closed')<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-neutral-100 text-neutral-500">Closed</span>@break
                                        @endswitch
                                        @if($ticket->priority === 'high')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">High</span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-semibold text-neutral-900 truncate">{{ $ticket->subject }}</h3>
                                    <p class="text-xs text-neutral-500 mt-0.5">{{ Str::limit($ticket->message, 80) }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs text-neutral-500">{{ $ticket->created_at->format('M d, Y') }}</p>
                                    <p class="text-[10px] text-neutral-400 mt-0.5">{{ $ticket->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="bg-white border border-neutral-100 rounded-xl p-8 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            <p class="text-sm text-neutral-500 mb-3">No support tickets yet</p>
                            <a href="{{ route('account.tickets.create') }}" class="text-sm text-[#202a40] font-medium hover:underline">Raise your first ticket</a>
                        </div>
                    @endforelse
                </div>

                @if($tickets->hasPages())
                    <div class="mt-4">{{ $tickets->links() }}</div>
                @endif

    @include('account.partials.sidebar-end')
</x-layouts.app>
