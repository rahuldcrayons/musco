<x-layouts.admin>
    <x-slot name="title">Features Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Settings</h1>
        <p class="text-neutral-600">Manage your store configuration</p>
    </div>

    <!-- Settings Navigation -->
    @include('admin.settings.partials.nav', ['active' => 'product-card'])

    <form action="{{ route('admin.settings.product-card.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="max-w-2xl">
            <div class="card">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="text-sm font-semibold text-neutral-900">Product Card Hover Actions</h2>
                    <p class="text-xs text-neutral-600 mt-0.5">Control which actions appear when customers hover over product cards on the storefront.</p>
                </div>
                <div class="p-5 space-y-4">
                    <!-- Quick View -->
                    <label class="flex items-center justify-between p-4 rounded-lg border border-neutral-200 cursor-pointer hover:bg-neutral-50 transition-colors" x-data="{ enabled: {{ $settings['product_card_quick_view'] ? 'true' : 'false' }} }">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">Quick View</p>
                                <p class="text-xs text-neutral-600">Show an eye icon to preview product details in a popup without leaving the page.</p>
                            </div>
                        </div>
                        <div class="relative shrink-0 ml-4">
                            <input type="hidden" name="product_card_quick_view" value="0">
                            <input type="checkbox" name="product_card_quick_view" value="1" x-model="enabled" class="sr-only peer">
                            <div @click="enabled = !enabled"
                                 class="w-11 h-6 bg-neutral-300 rounded-full transition-colors cursor-pointer"
                                 :class="enabled ? 'bg-primary-600!' : 'bg-neutral-300'">
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                                     :class="enabled ? 'translate-x-5' : 'translate-x-0'"></div>
                            </div>
                        </div>
                    </label>

                    <!-- Add to Cart -->
                    <label class="flex items-center justify-between p-4 rounded-lg border border-neutral-200 cursor-pointer hover:bg-neutral-50 transition-colors" x-data="{ enabled: {{ $settings['product_card_add_to_cart'] ? 'true' : 'false' }} }">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">Add to Cart</p>
                                <p class="text-xs text-neutral-600">Show the Add to Bag button on hover for quick cart additions.</p>
                            </div>
                        </div>
                        <div class="relative shrink-0 ml-4">
                            <input type="hidden" name="product_card_add_to_cart" value="0">
                            <input type="checkbox" name="product_card_add_to_cart" value="1" x-model="enabled" class="sr-only peer">
                            <div @click="enabled = !enabled"
                                 class="w-11 h-6 bg-neutral-300 rounded-full transition-colors cursor-pointer"
                                 :class="enabled ? 'bg-primary-600!' : 'bg-neutral-300'">
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                                     :class="enabled ? 'translate-x-5' : 'translate-x-0'"></div>
                            </div>
                        </div>
                    </label>

                    <!-- Wishlist -->
                    <label class="flex items-center justify-between p-4 rounded-lg border border-neutral-200 cursor-pointer hover:bg-neutral-50 transition-colors" x-data="{ enabled: {{ $settings['product_card_wishlist'] ? 'true' : 'false' }} }">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-error-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">Wishlist</p>
                                <p class="text-xs text-neutral-600">Show a heart icon on hover so customers can save products to their wishlist.</p>
                            </div>
                        </div>
                        <div class="relative shrink-0 ml-4">
                            <input type="hidden" name="product_card_wishlist" value="0">
                            <input type="checkbox" name="product_card_wishlist" value="1" x-model="enabled" class="sr-only peer">
                            <div @click="enabled = !enabled"
                                 class="w-11 h-6 bg-neutral-300 rounded-full transition-colors cursor-pointer"
                                 :class="enabled ? 'bg-primary-600!' : 'bg-neutral-300'">
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                                     :class="enabled ? 'translate-x-5' : 'translate-x-0'"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="px-5 py-3 bg-neutral-50 border-t border-neutral-200 flex justify-end rounded-b-lg">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>

            <!-- Customer Features -->
            <div class="card mt-6">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="text-sm font-semibold text-neutral-900">Customer Features</h2>
                    <p class="text-xs text-neutral-600 mt-0.5">Enable or disable features available to customers.</p>
                </div>
                <div class="p-5 space-y-4">
                    <!-- Support Tickets -->
                    <label class="flex items-center justify-between p-4 rounded-lg border border-neutral-200 cursor-pointer hover:bg-neutral-50 transition-colors" x-data="{ enabled: {{ $settings['support_tickets_enabled'] ? 'true' : 'false' }} }">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">Support Tickets</p>
                                <p class="text-xs text-neutral-600">Allow customers to raise support tickets from their account dashboard.</p>
                            </div>
                        </div>
                        <div class="relative shrink-0 ml-4">
                            <input type="hidden" name="support_tickets_enabled" value="0">
                            <input type="checkbox" name="support_tickets_enabled" value="1" x-model="enabled" class="sr-only peer">
                            <div @click="enabled = !enabled"
                                 class="w-11 h-6 bg-neutral-300 rounded-full transition-colors cursor-pointer"
                                 :class="enabled ? 'bg-primary-600!' : 'bg-neutral-300'">
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                                     :class="enabled ? 'translate-x-5' : 'translate-x-0'"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="px-5 py-3 bg-neutral-50 border-t border-neutral-200 flex justify-end rounded-b-lg">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>
