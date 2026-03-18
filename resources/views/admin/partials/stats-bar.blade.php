{{-- Shopify-style Stats Bar - Reusable across all admin pages --}}
@props(['stats' => []])

@if(count($stats) > 0)
<div class="mb-5">
    {{-- Date filter row --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <button class="flex items-center gap-2 px-3 py-1.5 text-sm text-neutral-700 bg-white rounded-lg" style="border:1px solid #c9cccf">
                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                Today
            </button>
        </div>
    </div>

    {{-- Stats cards row --}}
    <div class="bg-white overflow-hidden" style="border-radius:12px;border:1px solid #e3e3e3;box-shadow:0 1px 2px rgba(0,0,0,.04)">
        <div class="flex overflow-x-auto" style="scrollbar-width:none">
            @foreach($stats as $i => $stat)
            <div class="flex-1 min-w-0 px-5 py-4 {{ $i > 0 ? '' : '' }}" style="{{ $i > 0 ? 'border-left:1px solid #e3e3e3' : '' }}">
                <p class="text-xs text-neutral-500 font-medium mb-1 truncate">{{ $stat['label'] }}</p>
                <div class="flex items-center gap-3">
                    <span class="text-lg font-semibold text-neutral-900 truncate">{{ $stat['value'] }}</span>
                    {{-- Mini sparkline SVG --}}
                    <svg class="w-16 h-5 shrink-0" viewBox="0 0 64 20" fill="none">
                        <polyline points="{{ $stat['sparkline'] ?? '2,15 8,12 14,14 20,10 26,13 32,8 38,11 44,6 50,9 56,4 62,7' }}" stroke="{{ $stat['color'] ?? '#5c6ac4' }}" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                @if(isset($stat['change']))
                <p class="text-xs mt-0.5 {{ str_starts_with($stat['change'], '+') || str_starts_with($stat['change'], '↑') ? 'text-green-600' : (str_starts_with($stat['change'], '-') || str_starts_with($stat['change'], '↓') ? 'text-red-500' : 'text-neutral-400') }}">
                    {{ $stat['change'] }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
