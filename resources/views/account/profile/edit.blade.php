<x-layouts.app>
    <x-slot name="title">Profile Settings - {{ config('app.name') }}</x-slot>


    @include('account.partials.sidebar')

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Avatar Header --}}
                <div class="bg-white rounded-xl border border-neutral-100 p-5 mb-6"
                     x-data="{
                        uploading: false,
                        avatarUrl: '{{ $user->avatar_url ? Storage::disk('public')->url($user->avatar_url) : '' }}',
                        async upload(e) {
                            const file = e.target.files[0];
                            if (!file) return;
                            const form = new FormData();
                            form.append('avatar', file);
                            form.append('_token', document.querySelector('meta[name=csrf-token]').content);
                            this.uploading = true;
                            try {
                                const res = await axios.post('{{ route('account.avatar.update') }}', form, { headers: { 'Content-Type': 'multipart/form-data' } });
                                this.avatarUrl = res.data.avatar_url + '?t=' + Date.now();
                                $store.toast.success('Profile photo updated!');
                            } catch(err) {
                                $store.toast.error(err.response?.data?.message || 'Upload failed. Max 2MB.');
                            }
                            this.uploading = false;
                            e.target.value = '';
                        }
                     }">
                    <div class="flex items-center gap-4">
                        {{-- Avatar with upload overlay --}}
                        <label class="relative w-16 h-16 rounded-full cursor-pointer shrink-0 group" title="Change photo">
                            <template x-if="avatarUrl">
                                <img :src="avatarUrl" alt="Profile photo" class="w-16 h-16 rounded-full object-cover border-2 border-neutral-100">
                            </template>
                            <template x-if="!avatarUrl">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#202a40] to-[#c9a96e] flex items-center justify-center">
                                    <span class="text-xl font-bold text-white">{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</span>
                                </div>
                            </template>
                            {{-- Hover overlay --}}
                            <div class="absolute inset-0 rounded-full bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <template x-if="!uploading">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </template>
                                <template x-if="uploading">
                                    <svg class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </template>
                                <span class="text-[9px] text-white/80 mt-1" x-show="!uploading">Change</span>
                            </div>
                            <input type="file" accept="image/jpg,image/jpeg,image/png,image/webp" class="hidden" @change="upload($event)" :disabled="uploading">
                        </label>

                        <div>
                            <h2 class="text-base font-bold text-neutral-900">{{ $user->first_name }} {{ $user->last_name }}</h2>
                            <p class="text-sm text-neutral-500">{{ $user->email }}</p>
                            <p class="text-xs text-neutral-400 mt-0.5">Member since {{ $user->created_at->format('F Y') }}</p>
                            <p class="text-[11px] text-neutral-400 mt-1">Click photo to change · JPG, PNG, WEBP · max 2 MB</p>
                        </div>
                    </div>
                </div>

                {{-- Two Column Layout --}}
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                    {{-- LEFT: Personal Information --}}
                    <div class="bg-white rounded-xl border border-neutral-100 overflow-hidden">
                        <div class="px-5 py-3 border-b border-neutral-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#202a40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <h2 class="text-sm font-bold text-neutral-900">Personal Information</h2>
                        </div>

                        <form action="{{ route('account.profile.update') }}" method="POST" class="p-5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label for="first_name" class="block text-xs font-medium text-neutral-500 mb-1">First Name</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                           class="w-full rounded-lg border border-neutral-200 text-sm px-3 py-2.5 focus:border-[#202a40] focus:outline-none transition-colors">
                                    @error('first_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="last_name" class="block text-xs font-medium text-neutral-500 mb-1">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                           class="w-full rounded-lg border border-neutral-200 text-sm px-3 py-2.5 focus:border-[#202a40] focus:outline-none transition-colors">
                                    @error('last_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="block text-xs font-medium text-neutral-500 mb-1">Email Address</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="w-full rounded-lg border border-neutral-200 text-sm pl-9 pr-3 py-2.5 focus:border-[#202a40] focus:outline-none transition-colors">
                                </div>
                                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-xs font-medium text-neutral-500 mb-1">Phone Number</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-neutral-400 font-medium pointer-events-none">+44</span>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="7459914080"
                                           class="w-full rounded-lg border border-neutral-200 text-sm pl-12 pr-3 py-2.5 focus:border-[#202a40] focus:outline-none transition-colors">
                                </div>
                                @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-[#202a40] hover:bg-[#151e30] text-white text-sm font-semibold rounded-lg transition-colors">
                                Save Changes
                            </button>
                        </form>
                    </div>

                    {{-- RIGHT: Change Password --}}
                    <div class="bg-white rounded-xl border border-neutral-100 overflow-hidden"
                         x-data="{ showCur:false, showNew:false, showConf:false }">
                        <div class="px-5 py-3 border-b border-neutral-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#c9a96e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <h2 class="text-sm font-bold text-neutral-900">Change Password</h2>
                        </div>

                        <form action="{{ route('account.password.update') }}" method="POST" class="p-5">
                            @csrf
                            @method('PUT')

                            <div class="space-y-3 mb-4">
                                <div>
                                    <label class="block text-xs font-medium text-neutral-500 mb-1">Current Password</label>
                                    <div class="relative">
                                        <input :type="showCur?'text':'password'" name="current_password" required
                                               class="w-full rounded-lg border border-neutral-200 text-sm px-3 py-2.5 pr-10 focus:border-[#202a40] focus:outline-none transition-colors">
                                        <button type="button" @click="showCur=!showCur" class="absolute inset-y-0 right-3 flex items-center text-neutral-400 hover:text-neutral-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                    </div>
                                    @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-neutral-500 mb-1">New Password</label>
                                    <div class="relative">
                                        <input :type="showNew?'text':'password'" name="password" required
                                               class="w-full rounded-lg border border-neutral-200 text-sm px-3 py-2.5 pr-10 focus:border-[#202a40] focus:outline-none transition-colors">
                                        <button type="button" @click="showNew=!showNew" class="absolute inset-y-0 right-3 flex items-center text-neutral-400 hover:text-neutral-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                    </div>
                                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-neutral-500 mb-1">Confirm New Password</label>
                                    <div class="relative">
                                        <input :type="showConf?'text':'password'" name="password_confirmation" required
                                               class="w-full rounded-lg border border-neutral-200 text-sm px-3 py-2.5 pr-10 focus:border-[#202a40] focus:outline-none transition-colors">
                                        <button type="button" @click="showConf=!showConf" class="absolute inset-y-0 right-3 flex items-center text-neutral-400 hover:text-neutral-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-[#222] hover:bg-neutral-800 text-white text-sm font-semibold rounded-lg transition-colors">
                                Update Password
                            </button>
                        </form>
                    </div>

                </div>{{-- end 2-col grid --}}
    @include('account.partials.sidebar-end')
</x-layouts.app>
