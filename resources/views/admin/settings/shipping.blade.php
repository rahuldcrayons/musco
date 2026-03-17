<x-layouts.admin>
    <x-slot name="title">Shipping Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Settings</h1>
        <p class="text-neutral-600">Manage your store configuration</p>
    </div>

    <!-- Settings Navigation -->
    @include('admin.settings.partials.nav', ['active' => 'shipping'])

    <form action="{{ route('admin.settings.shipping.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Shipping Methods -->
            <div class="space-y-6">
                <!-- Free Shipping -->
                <div class="card" x-data="{ enabled: {{ ($settings['free_shipping_enabled'] ?? false) ? 'true' : 'false' }} }">
                    <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-neutral-900">Free Shipping</h2>
                            <p class="text-xs text-neutral-600">Offer free shipping above a threshold</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="free_shipping_enabled" value="1" x-model="enabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                    <div class="p-5" x-show="enabled" x-collapse>
                        <label class="form-label">Minimum Order Amount</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-600 text-sm">$</span>
                            <input type="number" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '') }}" step="0.01" min="0" class="form-input pl-7">
                        </div>
                    </div>
                </div>

                <!-- Flat Rate -->
                <div class="card" x-data="{ enabled: {{ ($settings['flat_rate_enabled'] ?? true) ? 'true' : 'false' }} }">
                    <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-neutral-900">Flat Rate Shipping</h2>
                            <p class="text-xs text-neutral-600">Charge a fixed shipping fee</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="flat_rate_enabled" value="1" x-model="enabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                    <div class="p-5" x-show="enabled" x-collapse>
                        <label class="form-label">Shipping Fee</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-600 text-sm">$</span>
                            <input type="number" name="flat_rate_amount" value="{{ old('flat_rate_amount', $settings['flat_rate_amount'] ?? '5.99') }}" step="0.01" min="0" class="form-input pl-7">
                        </div>
                    </div>
                </div>

                <!-- Local Pickup -->
                <div class="card" x-data="{ enabled: {{ ($settings['local_pickup_enabled'] ?? false) ? 'true' : 'false' }} }">
                    <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-neutral-900">Local Pickup</h2>
                            <p class="text-xs text-neutral-600">Allow in-store pickup</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="local_pickup_enabled" value="1" x-model="enabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                    <div class="p-5" x-show="enabled" x-collapse>
                        <label class="form-label">Pickup Address</label>
                        <textarea name="local_pickup_address" rows="3" class="form-textarea" placeholder="Enter your store address...">{{ old('local_pickup_address', $settings['local_pickup_address'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Shipping Origin -->
            <div class="card h-fit">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="text-sm font-semibold text-neutral-900">Shipping Origin</h2>
                    <p class="text-xs text-neutral-600">Where packages are shipped from</p>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="form-label form-label-required">Country</label>
                        <select name="shipping_origin_country" required class="form-select">
                            <option value="US" @selected(($settings['shipping_origin_country'] ?? 'US') === 'US')>United States</option>
                            <option value="CA" @selected(($settings['shipping_origin_country'] ?? '') === 'CA')>Canada</option>
                            <option value="GB" @selected(($settings['shipping_origin_country'] ?? '') === 'GB')>United Kingdom</option>
                            <option value="AU" @selected(($settings['shipping_origin_country'] ?? '') === 'AU')>Australia</option>
                            <option value="DE" @selected(($settings['shipping_origin_country'] ?? '') === 'DE')>Germany</option>
                            <option value="FR" @selected(($settings['shipping_origin_country'] ?? '') === 'FR')>France</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">State/Province</label>
                        <input type="text" name="shipping_origin_state" value="{{ old('shipping_origin_state', $settings['shipping_origin_state'] ?? '') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">ZIP/Postal Code</label>
                        <input type="text" name="shipping_origin_zip" value="{{ old('shipping_origin_zip', $settings['shipping_origin_zip'] ?? '') }}" class="form-input">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</x-layouts.admin>
