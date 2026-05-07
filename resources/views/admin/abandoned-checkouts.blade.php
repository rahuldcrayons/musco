<x-layouts.admin>
<x-slot name="title">Abandoned Checkouts</x-slot>
<div class="p-6 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-xl font-semibold text-gray-900">Abandoned checkouts</h1>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-[#202a40]/5 border border-[#202a40]/20 text-[#202a40] text-sm">{{ session('success') }}</div>
    @endif

    {{-- Tabs --}}
    @php
        $totalCount = \App\Models\AbandonedCheckout::count();
        $withContactCount = \App\Models\AbandonedCheckout::where(function($q) { $q->whereNotNull('email')->orWhereNotNull('phone'); })->where('recovered', false)->count();
        $recoveredCount = \App\Models\AbandonedCheckout::where('recovered', true)->count();
        $tab = request('tab', 'all');
    @endphp
    <div class="flex items-center gap-6 border-b border-gray-200 mb-0" x-data>
        <a href="{{ route('admin.abandoned-checkouts') }}"
           class="pb-3 text-sm font-medium border-b-2 {{ $tab === 'all' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            All <span class="ml-1 text-xs text-gray-400">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('admin.abandoned-checkouts', ['tab' => 'with-contact']) }}"
           class="pb-3 text-sm font-medium border-b-2 {{ $tab === 'with-contact' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            With contact info <span class="ml-1 text-xs text-gray-400">{{ $withContactCount }}</span>
        </a>
        <a href="{{ route('admin.abandoned-checkouts', ['tab' => 'recovered']) }}"
           class="pb-3 text-sm font-medium border-b-2 {{ $tab === 'recovered' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Recovered <span class="ml-1 text-xs text-gray-400">{{ $recoveredCount }}</span>
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-b-lg border border-t-0 border-gray-200" x-data="bulkActions()">
        {{-- Bulk actions bar --}}
        <div x-show="selected.length > 0" x-cloak class="px-4 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center gap-3">
            <span class="text-sm text-gray-700" x-text="selected.length + ' selected'"></span>
            <form method="POST" action="{{ route('admin.abandoned-checkouts.bulk') }}" class="inline" x-ref="bulkForm">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" name="action" value="delete" onclick="return confirm('Delete selected entries?')"
                        class="px-3 py-1 text-xs font-medium text-[#CC0C39] bg-white border border-gray-300 rounded-md hover:bg-[#CC0C39]/10 transition">
                    Delete selected
                </button>
                <button type="submit" name="action" value="remind" onclick="return confirm('Send reminders to entries with contact info?')"
                        class="px-3 py-1 text-xs font-medium text-[#202a40] bg-white border border-gray-300 rounded-md hover:bg-[#202a40]/5 transition ml-1">
                    Send reminder
                </button>
            </form>
        </div>

        <table class="w-full text-sm">
            <thead class="border-b border-gray-200">
                <tr>
                    <th class="w-10 px-4 py-3 text-left">
                        <input type="checkbox" class="rounded border-gray-300" @change="toggleAll($event)">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checkout</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recovery</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($abandoned as $item)
                <tr class="hover:bg-gray-50 transition-colors" x-data="{ showItems: false }">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="rounded border-gray-300" value="{{ $item->id }}" x-model="selected">
                    </td>
                    <td class="px-4 py-3">
                        <button @click="showItems = !showItems" class="text-[#202a40] hover:text-[#202a40]/80 font-medium text-sm hover:underline">
                            #{{ $item->id }}
                        </button>
                        <span class="text-gray-400 text-xs ml-1">{{ $item->items_count ?? ($item->cart_snapshot ? count($item->cart_snapshot) : 0) }} {{ Str::plural('item', $item->items_count ?? 1) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <span>{{ $item->created_at->format('M d') }}</span>
                        <span class="text-gray-400 text-xs block">{{ $item->created_at->format('h:i A') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($item->email || $item->phone || $item->name)
                            <p class="text-gray-900 font-medium text-sm">{{ $item->name ?: ($item->email ? Str::before($item->email, '@') : 'Guest') }}</p>
                            @if($item->email)<p class="text-gray-500 text-xs">{{ $item->email }}</p>@endif
                            @if($item->phone)<p class="text-gray-500 text-xs">{{ $item->phone }}</p>@endif
                        @else
                            <span class="text-gray-400 text-xs">No contact info</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900">{{ currency_symbol() }}{{ number_format($item->cart_total, 2) }}</td>
                    <td class="px-4 py-3">
                        @if($item->recovered && $item->order_id)
                            <a href="{{ route('admin.orders.show', $item->order_id) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-[#202a40]/5 text-[#202a40] hover:bg-[#202a40]/10 transition">
                                Recovered
                            </a>
                        @elseif($item->recovered)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#202a40]/5 text-[#202a40]">Recovered</span>
                        @elseif($item->notified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#506282]/10 text-[#506282]">Reminded {{ $item->notified_at->diffForHumans() }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#CC0C39]/10 text-[#CC0C39]">Not recovered</span>
                        @endif
                    </td>
                </tr>
                @if($item->cart_snapshot && count($item->cart_snapshot) > 0)
                <tr x-show="showItems" x-cloak class="bg-gray-50">
                    <td colspan="6" class="px-12 py-3 border-t border-gray-100">
                        <div class="text-xs text-gray-600 space-y-1.5">
                            @foreach($item->cart_snapshot as $cartItem)
                            <div class="flex items-center justify-between max-w-lg">
                                <span class="text-gray-700">{{ $cartItem['product_name'] ?? 'Product' }} <span class="text-gray-400">x{{ $cartItem['quantity'] ?? 1 }}</span></span>
                                <span class="font-medium text-gray-900">{{ currency_symbol() }}{{ number_format(($cartItem['price'] ?? 0) * ($cartItem['quantity'] ?? 1), 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="6" class="px-4 py-16 text-center text-gray-400 text-sm">No abandoned checkouts</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($abandoned->hasPages())
    <div class="mt-4">{{ $abandoned->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
function bulkActions() {
    return {
        selected: [],
        toggleAll(e) {
            if (e.target.checked) {
                this.selected = @json($abandoned->pluck('id')->map(fn($id) => (string) $id));
            } else {
                this.selected = [];
            }
        }
    };
}
</script>
@endpush
</x-layouts.admin>
