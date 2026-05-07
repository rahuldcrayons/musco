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

            {{-- PayPal --}}
            <div class="card" x-data="{ enabled: {{ ($settings['paypal_enabled'] ?? '0') === '1' ? 'true' : 'false' }} }">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#003087;">
                            <span class="text-white font-bold text-xs">PP</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-neutral-900 flex items-center gap-2">PayPal</h3>
                            <p class="text-xs text-neutral-600">Accept payments via PayPal checkout</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="paypal_enabled" value="1" x-model="enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
                <div class="px-5 pb-5 space-y-4 border-t border-neutral-100" x-show="enabled" x-collapse>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        <div>
                            <label class="form-label">Client ID</label>
                            <input type="text" name="paypal_client_id" value="{{ old('paypal_client_id', $settings['paypal_client_id'] ?? '') }}" placeholder="AcfGk..." class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Client Secret</label>
                            <input type="password" name="paypal_client_secret" value="" placeholder="{{ !empty($settings['paypal_client_secret']) ? '••••••••••••' : 'Enter client secret' }}" class="form-input">
                            @if(!empty($settings['paypal_client_secret']))
                                <p class="text-xs text-neutral-600 mt-1">Secret is saved. Leave blank to keep current value.</p>
                            @endif
                        </div>
                    </div>
                    <div class="max-w-xs">
                        <label class="form-label">Mode</label>
                        <select name="paypal_mode" class="form-select">
                            <option value="sandbox" @selected(($settings['paypal_mode'] ?? 'live') === 'sandbox')>Sandbox / Test</option>
                            <option value="live" @selected(($settings['paypal_mode'] ?? 'live') === 'live')>Live / Production</option>
                        </select>
                    </div>
                    <p class="text-xs text-neutral-600">Get your API credentials from <span class="font-medium text-neutral-600">PayPal Developer Dashboard → Apps & Credentials</span></p>
                </div>
            </div>

            {{-- Stripe --}}
            <div class="card" x-data="{ enabled: {{ ($settings['stripe_enabled'] ?? '0') === '1' ? 'true' : 'false' }} }">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#635BFF;">
                            <span class="text-white font-bold text-xs">S</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-neutral-900 flex items-center gap-2">Stripe</h3>
                            <p class="text-xs text-neutral-600">Accept credit/debit card payments via Stripe</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="stripe_enabled" value="1" x-model="enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
                <div class="px-5 pb-5 space-y-4 border-t border-neutral-100" x-show="enabled" x-collapse>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        <div>
                            <label class="form-label">Publishable Key</label>
                            <input type="text" name="stripe_publishable_key" value="{{ old('stripe_publishable_key', $settings['stripe_publishable_key'] ?? '') }}" placeholder="pk_live_..." class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Secret Key</label>
                            <input type="password" name="stripe_secret_key" value="" placeholder="{{ !empty($settings['stripe_secret_key']) ? str_repeat(chr(8226), 12) : 'Enter secret key' }}" class="form-input">
                            @if(!empty($settings['stripe_secret_key']))
                                <p class="text-xs text-neutral-600 mt-1">Secret key is saved. Leave blank to keep current value.</p>
                            @endif
                        </div>
                    </div>
                    <div class="max-w-sm">
                        <label class="form-label">Webhook Secret</label>
                        <input type="password" name="stripe_webhook_secret" value="" placeholder="{{ !empty($settings['stripe_webhook_secret']) ? str_repeat(chr(8226), 12) : 'whsec_...' }}" class="form-input">
                        @if(!empty($settings['stripe_webhook_secret']))
                            <p class="text-xs text-neutral-600 mt-1">Webhook secret is saved. Leave blank to keep current value.</p>
                        @endif
                    </div>
                    <p class="text-xs text-neutral-600">Get your API keys from <span class="font-medium text-neutral-600">Stripe Dashboard &rarr; Developers &rarr; API keys</span>. Webhook endpoint: <code class="text-xs bg-neutral-100 px-1 py-0.5 rounded">{{ url('/webhook/stripe') }}</code></p>
                </div>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</x-layouts.admin>
