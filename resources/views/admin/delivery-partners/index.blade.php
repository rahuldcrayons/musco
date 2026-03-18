<x-layouts.admin>
    <x-slot name="title">Delivery partners</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 style="font-size:20px;font-weight:600;color:#1a1a1a;line-height:1.2">Delivery partners</h1>
            <a href="{{ route('admin.delivery-partners.create') }}"
               style="background:#1a1a1a;border:1px solid #1a1a1a;color:#fff;font-size:13px;font-weight:500;padding:6px 12px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center"
               onmouseover="this.style.background='#333'" onmouseout="this.style.background='#1a1a1a'">
                Add partner
            </a>
        </div>
    </x-slot>

    @php
        $currentStatus = request('status', '');
        $pageIds = $partners->pluck('id')->toArray();
    @endphp

    <div style="background:#fff;border-radius:12px;box-shadow:0 1px 2px rgba(0,0,0,0.06),0 1px 3px rgba(0,0,0,0.1);overflow:hidden"
         x-data="{
             selected: [],
             toggleAll(checked) {
                 this.selected = checked ? {{ json_encode($pageIds) }} : [];
             },
             toggle(id) {
                 const idx = this.selected.indexOf(id);
                 idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
             },
             get allChecked() {
                 return this.selected.length === {{ count($pageIds) }} && {{ count($pageIds) }} > 0;
             }
         }">

        {{-- Tab row --}}
        <div class="flex items-center justify-between" style="border-bottom:1px solid #e1e1e1;padding:0 12px">
            <div class="flex items-center gap-0">
                @php
                    $tabs = [
                        '' => 'All',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ];
                @endphp
                @foreach($tabs as $statusKey => $label)
                    <a href="{{ route('admin.delivery-partners.index', array_merge(request()->except('status', 'page'), $statusKey ? ['status' => $statusKey] : [])) }}"
                       style="padding:10px 12px;font-size:13px;font-weight:500;text-decoration:none;position:relative;{{ $currentStatus === $statusKey ? 'color:#1a1a1a;' : 'color:#616161;' }}"
                       onmouseover="this.style.color='#1a1a1a'" onmouseout="this.style.color='{{ $currentStatus === $statusKey ? '#1a1a1a' : '#616161' }}'">
                        {{ $label }}
                        @if($currentStatus === $statusKey)
                            <span style="position:absolute;bottom:-1px;left:12px;right:12px;height:2px;background:#1a1a1a;border-radius:1px"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Search row --}}
        <div class="flex items-center gap-2" style="padding:12px;border-bottom:1px solid #e1e1e1">
            <form action="{{ route('admin.delivery-partners.index') }}" method="GET" class="flex items-center gap-2 flex-1">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex-1 relative">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="#8a8a8a" style="position:absolute;left:10px;top:50%;transform:translateY(-50%)"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Filter delivery partners"
                           style="width:100%;padding:7px 10px 7px 32px;font-size:13px;border:1px solid #c9c9c9;border-radius:8px;outline:none;color:#1a1a1a;background:#fff"
                           onfocus="this.style.borderColor='#005bd3';this.style.boxShadow='0 0 0 1px #005bd3'" onblur="this.style.borderColor='#c9c9c9';this.style.boxShadow='none'">
                </div>
                @if(request()->hasAny(['search']))
                    <a href="{{ route('admin.delivery-partners.index', request('status') ? ['status' => request('status')] : []) }}"
                       style="padding:7px 12px;font-size:13px;color:#323232;text-decoration:none;white-space:nowrap">
                        Clear filters
                    </a>
                @endif
            </form>
        </div>

        {{-- Bulk actions --}}
        <div x-show="selected.length > 0" x-cloak
             style="padding:8px 12px;background:#f3f3f3;border-bottom:1px solid #e1e1e1" class="flex items-center gap-3">
            <span style="font-size:13px;font-weight:500;color:#323232">
                <span x-text="selected.length"></span> selected
            </span>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="border-bottom:1px solid #e1e1e1">
                        <th style="padding:8px 8px 8px 12px;width:36px;text-align:center">
                            <input type="checkbox"
                                   style="width:16px;height:16px;cursor:pointer;accent-color:#1a1a1a"
                                   @change="toggleAll($event.target.checked)"
                                   :checked="allChecked">
                        </th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Name</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Phone</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Zone</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Orders</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Status</th>
                        <th style="padding:8px 12px;text-align:left;font-size:12px;font-weight:500;color:#616161">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partners as $partner)
                        <tr style="border-bottom:1px solid #f1f1f1;cursor:pointer"
                            onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='#fff'"
                            onclick="if(!event.target.closest('input[type=checkbox]')) window.location='{{ route('admin.delivery-partners.show', $partner) }}'">
                            <td style="padding:8px 8px 8px 12px;width:36px;text-align:center" onclick="event.stopPropagation()">
                                <input type="checkbox"
                                       style="width:16px;height:16px;cursor:pointer;accent-color:#1a1a1a"
                                       :checked="selected.includes({{ $partner->id }})"
                                       @change="toggle({{ $partner->id }})">
                            </td>
                            <td style="padding:8px 12px">
                                <div class="flex items-center gap-3">
                                    <div style="width:32px;height:32px;border-radius:50%;background:#f1f1f1;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid #e1e1e1">
                                        <span style="font-size:12px;font-weight:600;color:#616161">{{ strtoupper(substr($partner->user->first_name ?? '', 0, 1) . substr($partner->user->last_name ?? '', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span style="font-size:13px;font-weight:500;color:#1a1a1a">{{ $partner->user->full_name ?? 'N/A' }}</span>
                                        <p style="font-size:12px;color:#616161;margin-top:1px">{{ $partner->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:8px 12px;font-size:13px;color:#323232">
                                {{ $partner->phone ?? '--' }}
                            </td>
                            <td style="padding:8px 12px;font-size:13px;color:#323232">
                                {{ $partner->zone ?? '--' }}
                            </td>
                            <td style="padding:8px 12px;font-size:13px;color:#323232">
                                {{ $partner->orders_count ?? 0 }}
                            </td>
                            <td style="padding:8px 12px">
                                @if($partner->is_active)
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:12px;font-weight:500;background:#c8f9d4;color:#1a7f37">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#1a7f37;display:inline-block"></span>
                                        Active
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:12px;font-weight:500;background:#e4e4e4;color:#616161">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#8a8a8a;display:inline-block"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td style="padding:8px 12px;font-size:13px;color:#323232">
                                {{ $partner->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:48px 12px;text-align:center">
                                <div class="flex flex-col items-center">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c9c9c9" stroke-width="1.5" style="margin-bottom:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p style="font-size:14px;font-weight:500;color:#1a1a1a;margin-bottom:4px">No delivery partners found</p>
                                    <p style="font-size:13px;color:#616161;margin-bottom:16px">
                                        @if(request()->hasAny(['search', 'status']))
                                            Try changing the filters or search term.
                                        @else
                                            Add a delivery partner to get started.
                                        @endif
                                    </p>
                                    @if(!request()->hasAny(['search', 'status']))
                                        <a href="{{ route('admin.delivery-partners.create') }}"
                                           style="background:#1a1a1a;color:#fff;font-size:13px;font-weight:500;padding:7px 14px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center">
                                            Add partner
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($partners->total() > 0)
            <div class="flex items-center justify-center gap-4" style="padding:12px;border-top:1px solid #e1e1e1">
                <div class="flex items-center gap-2">
                    @if($partners->onFirstPage())
                        <span style="padding:6px;color:#c9c9c9;cursor:not-allowed">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @else
                        <a href="{{ $partners->previousPageUrl() }}" style="padding:6px;color:#616161;text-decoration:none;border-radius:6px;display:inline-flex" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='transparent'">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    @endif

                    <span style="font-size:13px;color:#323232">
                        {{ $partners->firstItem() }}-{{ $partners->lastItem() }} of {{ $partners->total() }}
                    </span>

                    @if($partners->hasMorePages())
                        <a href="{{ $partners->nextPageUrl() }}" style="padding:6px;color:#616161;text-decoration:none;border-radius:6px;display:inline-flex" onmouseover="this.style.background='#f1f1f1'" onmouseout="this.style.background='transparent'">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    @else
                        <span style="padding:6px;color:#c9c9c9;cursor:not-allowed">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
