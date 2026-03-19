<x-layouts.admin>
<x-slot name="title">Abandoned Checkouts</x-slot>
<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Abandoned Checkouts</h1>
        <span class="text-sm text-gray-500">{{ $abandoned->total() }} total</span>
    </div>
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">ID</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Phone</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Cart Total</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Step</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Recovered</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Notified</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($abandoned as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $item->id }}</td>
                    <td class="px-4 py-3">{{ $item->email ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $item->phone ?: '-' }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format($item->cart_total, 2) }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-800">{{ $item->step }}</span></td>
                    <td class="px-4 py-3 text-center">
                        @if($item->recovered)
                            <span class="text-green-600 font-medium">Yes</span>
                        @else
                            <span class="text-red-500">No</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs">{{ $item->notified_at ?: '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">No abandoned checkouts yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($abandoned->hasPages())
    <div class="mt-4">{{ $abandoned->links() }}</div>
    @endif
</div>
</x-layouts.admin>
