<x-layouts.admin>
    <x-slot name="title">SEO Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Settings</h1>
        <p class="text-neutral-600">Manage your store configuration</p>
    </div>

    <!-- Settings Navigation -->
    @include('admin.settings.partials.nav', ['active' => 'seo'])

    <form action="{{ route('admin.settings.seo.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Meta Tags -->
            <div class="card">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="text-sm font-semibold text-neutral-900">Default Meta Tags</h2>
                    <p class="text-xs text-neutral-600">Used when pages don't have specific meta tags</p>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $settings['meta_title'] ?? '') }}" maxlength="70" class="form-input">
                        <p class="text-xs text-neutral-600 mt-1">Max 70 characters</p>
                    </div>

                    <div>
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" rows="3" maxlength="160" class="form-textarea">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                        <p class="text-xs text-neutral-600 mt-1">Max 160 characters</p>
                    </div>

                    <div>
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}" placeholder="keyword1, keyword2, keyword3" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Open Graph Image URL</label>
                        <input type="text" name="og_image" value="{{ old('og_image', $settings['og_image'] ?? '') }}" placeholder="https://example.com/og-image.jpg" class="form-input">
                        <p class="text-xs text-neutral-600 mt-1">Recommended size: 1200x630 pixels</p>
                    </div>
                </div>
            </div>

            <!-- Analytics & Tracking -->
            <div class="space-y-6">
                <div class="card">
                    <div class="px-5 py-3.5 border-b border-neutral-200">
                        <h2 class="text-sm font-semibold text-neutral-900">Analytics & Tracking</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="form-label">Google Analytics ID</label>
                            <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" placeholder="G-XXXXXXXXXX" class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Google Tag Manager ID</label>
                            <input type="text" name="google_tag_manager_id" value="{{ old('google_tag_manager_id', $settings['google_tag_manager_id'] ?? '') }}" placeholder="GTM-XXXXXXX" class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Facebook Pixel ID</label>
                            <input type="text" name="facebook_pixel_id" value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}" placeholder="XXXXXXXXXXXXXXXX" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="px-5 py-3.5 border-b border-neutral-200">
                        <h2 class="text-sm font-semibold text-neutral-900">Robots.txt</h2>
                    </div>
                    <div class="p-5">
                        <textarea name="robots_txt" rows="8" class="form-textarea font-mono" placeholder="User-agent: *
Allow: /
Disallow: /admin/
Disallow: /account/">{{ old('robots_txt', $settings['robots_txt'] ?? "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /account/") }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</x-layouts.admin>
