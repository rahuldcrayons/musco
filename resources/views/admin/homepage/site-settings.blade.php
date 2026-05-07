<x-layouts.admin>
    <x-slot name="title">Site Settings</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Site Settings</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage logo, brand identity, and social links</p>
            </div>
            <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">Back to Homepage</a>
        </div>
    </x-slot>

    <form action="{{ route('admin.homepage.site-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Brand Identity -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Brand Identity</h2>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Site Logo</label>
                        @if($settings['site_logo'])
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Current Logo" class="h-16 object-contain">
                            </div>
                        @endif
                        <input type="file" name="site_logo" accept="image/*" class="form-input">
                        <p class="form-help">Recommended: PNG with transparent background, 200x60px</p>
                    </div>

                    <div>
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" value="{{ $settings['site_name'] }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Tagline</label>
                        <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Site Description</label>
                        <textarea name="site_description" rows="3" class="form-input">{{ $settings['site_description'] }}</textarea>
                    </div>

                    <div>
                        <label class="form-label">Announcement Bar Text</label>
                        <input type="text" name="announcement_text" value="{{ $settings['announcement_text'] }}" class="form-input" placeholder="e.g. Free Shipping on Orders Over £30!">
                        <p class="form-help">Displayed in the teal bar at the top of every page. Leave empty to hide.</p>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Social Media Links</h2>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Facebook</label>
                        <input type="url" name="social_facebook" value="{{ $settings['social_facebook'] }}" class="form-input" placeholder="https://facebook.com/...">
                    </div>
                    <div>
                        <label class="form-label">Instagram</label>
                        <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] }}" class="form-input" placeholder="https://instagram.com/...">
                    </div>
                    <div>
                        <label class="form-label">Twitter / X</label>
                        <input type="url" name="social_twitter" value="{{ $settings['social_twitter'] }}" class="form-input" placeholder="https://x.com/...">
                    </div>
                    <div>
                        <label class="form-label">YouTube</label>
                        <input type="url" name="social_youtube" value="{{ $settings['social_youtube'] }}" class="form-input" placeholder="https://youtube.com/...">
                    </div>
                    <div>
                        <label class="form-label">TikTok</label>
                        <input type="url" name="social_tiktok" value="{{ $settings['social_tiktok'] }}" class="form-input" placeholder="https://tiktok.com/...">
                    </div>
                    <div>
                        <label class="form-label">Pinterest</label>
                        <input type="url" name="social_pinterest" value="{{ $settings['social_pinterest'] }}" class="form-input" placeholder="https://pinterest.com/...">
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Contact Information</h2>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Address</label>
                        <textarea name="contact_address" rows="3" class="form-input">{{ $settings['contact_address'] }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Footer Content</h2>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">About Text</label>
                        <textarea name="footer_about" rows="4" class="form-input">{{ $settings['footer_about'] }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Copyright Text</label>
                        <input type="text" name="footer_copyright" value="{{ $settings['footer_copyright'] }}" class="form-input">
                    </div>
                </div>
            </div>
        </div>

        <!-- Homepage Display Settings -->
        <div class="card p-6 lg:col-span-2 mt-6">
            <h2 class="text-lg font-semibold text-neutral-900 mb-1">Homepage Display Settings</h2>
            <p class="text-sm text-neutral-500 mb-4">Control how many items appear in each homepage section</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Featured Products Count</label>
                    <input type="number" name="homepage_featured_count" value="{{ $settings['homepage_featured_count'] }}" class="form-input" min="1" max="50">
                    <p class="form-help">Number of featured products to display</p>
                </div>
                <div>
                    <label class="form-label">New Arrivals Count</label>
                    <input type="number" name="homepage_new_arrivals_count" value="{{ $settings['homepage_new_arrivals_count'] }}" class="form-input" min="1" max="50">
                    <p class="form-help">Number of new arrivals to display</p>
                </div>
                <div>
                    <label class="form-label">New Arrivals Days</label>
                    <input type="number" name="homepage_new_arrivals_days" value="{{ $settings['homepage_new_arrivals_days'] }}" class="form-input" min="1" max="365">
                    <p class="form-help">Products added within this many days are considered new</p>
                </div>
                <div>
                    <label class="form-label">Bestsellers Count</label>
                    <input type="number" name="homepage_bestsellers_count" value="{{ $settings['homepage_bestsellers_count'] }}" class="form-input" min="1" max="50">
                    <p class="form-help">Number of bestselling products to display</p>
                </div>
                <div>
                    <label class="form-label">Deals Count</label>
                    <input type="number" name="homepage_deals_count" value="{{ $settings['homepage_deals_count'] }}" class="form-input" min="1" max="50">
                    <p class="form-help">Number of deal products to display</p>
                </div>
                <div>
                    <label class="form-label">Testimonials Count</label>
                    <input type="number" name="homepage_testimonials_count" value="{{ $settings['homepage_testimonials_count'] }}" class="form-input" min="1" max="50">
                    <p class="form-help">Number of testimonials to display</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
        </div>
    </form>
</x-layouts.admin>
