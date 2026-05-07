<x-layouts.app>
    <x-slot name="title">My Addresses</x-slot>

    @include('account.partials.sidebar')

                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-xl font-bold text-neutral-900">My Addresses</h1>
                    <a href="{{ route('account.addresses.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#202a40] text-white text-sm font-semibold rounded-lg hover:bg-[#151e30] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Address
                    </a>
                </div>

                @if($addresses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($addresses as $address)
                            <div class="bg-white rounded-xl border {{ $address->is_default ? 'border-[#202a40]/30 ring-1 ring-[#202a40]/10' : 'border-neutral-100' }} p-5 hover:shadow-sm transition-all">
                                <div class="flex items-center gap-2 mb-3">
                                    @if($address->label)
                                        <span class="px-2.5 py-0.5 rounded-md bg-neutral-100 text-[12px] font-medium text-neutral-600 capitalize">{{ $address->label }}</span>
                                    @endif
                                    @if($address->is_default)
                                        <span class="px-2.5 py-0.5 rounded-md bg-[#202a40]/10 text-[12px] font-semibold text-[#202a40]">Default</span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-semibold text-neutral-900">{{ $address->full_name }}</h3>
                                <p class="text-[13px] text-neutral-500 mt-0.5">{{ $address->phone }}</p>
                                <p class="text-[13px] text-neutral-500 mt-2 leading-relaxed">
                                    {{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif<br>
                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                </p>
                                <div class="flex items-center gap-3 mt-4 pt-3 border-t border-neutral-100">
                                    <a href="{{ route('account.addresses.edit', $address) }}" class="text-[13px] font-medium text-[#202a40] hover:underline">Edit</a>
                                    @if(!$address->is_default)
                                        <form action="{{ route('account.addresses.update', $address) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="name" value="{{ $address->full_name }}">
                                            <input type="hidden" name="phone" value="{{ $address->phone }}">
                                            <input type="hidden" name="address_line1" value="{{ $address->address_line_1 }}">
                                            <input type="hidden" name="address_line2" value="{{ $address->address_line_2 }}">
                                            <input type="hidden" name="city" value="{{ $address->city }}">
                                            <input type="hidden" name="state" value="{{ $address->state }}">
                                            <input type="hidden" name="postal_code" value="{{ $address->postal_code }}">
                                            <input type="hidden" name="country" value="{{ $address->country }}">
                                            <input type="hidden" name="is_default" value="1">
                                            <button type="submit" class="text-[13px] font-medium text-neutral-500 hover:text-[#202a40]">Set Default</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('account.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Delete this address?')" class="inline ml-auto">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[13px] font-medium text-neutral-400 hover:text-red-500">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-neutral-100 p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-neutral-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <h3 class="text-base font-semibold text-neutral-900 mb-1">No addresses saved</h3>
                        <p class="text-sm text-neutral-500 mb-5">Add an address to make checkout faster</p>
                        <a href="{{ route('account.addresses.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-[#202a40] text-white text-sm font-semibold rounded-lg hover:bg-[#151e30] transition-colors">Add Your First Address</a>
                    </div>
                @endif

    @include('account.partials.sidebar-end')
</x-layouts.app>
