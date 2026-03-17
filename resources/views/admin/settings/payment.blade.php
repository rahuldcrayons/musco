<x-layouts.admin>
    <x-slot name="title">Payment Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Settings</h1>
        <p class="text-neutral-600">Manage your store configuration</p>
    </div>

    @include('admin.settings.partials.nav', ['active' => 'payment'])

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-success-50 border border-success-200 rounded-xl text-sm text-success-700">
            <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.payment.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">

            {{-- Razorpay --}}
            <div class="card" x-data="{ enabled: {{ ($settings['razorpay_enabled'] ?? '0') === '1' ? 'true' : 'false' }} }">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#072654;">
                            <span class="text-white font-bold text-sm tracking-tight">R₹</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-neutral-900 flex items-center gap-2">
                                Razorpay
                                <span class="text-[10px] font-medium text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded">Recommended</span>
                            </h3>
                            <p class="text-xs text-neutral-600">Accept payments via Razorpay checkout</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="razorpay_enabled" value="1" x-model="enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
                <div class="px-5 pb-5 space-y-4 border-t border-neutral-100" x-show="enabled" x-collapse>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        <div>
                            <label class="form-label">Key ID</label>
                            <input type="text" name="razorpay_key_id" value="{{ old('razorpay_key_id', $settings['razorpay_key_id'] ?? '') }}" placeholder="rzp_live_..." class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Key Secret</label>
                            <input type="password" name="razorpay_key_secret" value="{{ old('razorpay_key_secret', $settings['razorpay_key_secret'] ?? '') }}" placeholder="••••••••••••" class="form-input">
                        </div>
                    </div>
                    <div class="max-w-xs">
                        <label class="form-label">Mode</label>
                        <select name="razorpay_mode" class="form-select">
                            <option value="test" @selected(($settings['razorpay_mode'] ?? 'test') === 'test')>Test / Sandbox</option>
                            <option value="live" @selected(($settings['razorpay_mode'] ?? '') === 'live')>Live / Production</option>
                        </select>
                    </div>
                    <p class="text-xs text-neutral-600">Get your API keys from <span class="font-medium text-neutral-600">Razorpay Dashboard → Settings → API Keys</span></p>
                </div>
            </div>

            {{-- UPI --}}
            <div class="card">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-info-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">UPI</p>
                            <p class="text-xs text-neutral-600">Google Pay, PhonePe, Paytm, BHIM</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="upi_enabled" value="1" {{ ($settings['upi_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
            </div>

            {{-- COD --}}
            <div class="card" x-data="{ enabled: {{ ($settings['cod_enabled'] ?? '0') === '1' ? 'true' : 'false' }} }">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-success-50 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">Cash on Delivery (COD)</p>
                            <p class="text-xs text-neutral-600">Customer pays cash when order arrives</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="cod_enabled" value="1" x-model="enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
                <div class="px-5 pb-4 border-t border-neutral-100" x-show="enabled" x-collapse>
                    <label class="form-label mt-4">Instructions for Customer <span class="text-neutral-600 font-normal">(optional)</span></label>
                    <textarea name="cod_instructions" rows="2" class="form-textarea" placeholder="e.g. Please keep exact change ready at delivery.">{{ old('cod_instructions', $settings['cod_instructions'] ?? '') }}</textarea>
                </div>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Save Payment Settings</button>
        </div>
    </form>
</x-layouts.admin>
