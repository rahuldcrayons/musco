<x-layouts.admin>
    <x-slot name="title">Navigation Menus</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Navigation Menus</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage header and footer navigation links</p>
            </div>
            <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">Back to Homepage</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add Menu Item -->
        <div class="card p-6">
            <h2 class="text-lg font-semibold text-neutral-900 mb-4">Add Menu Item</h2>

            <form action="{{ route('admin.homepage.navigation.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label form-label-required">Location</label>
                        <select name="location" class="form-select" required>
                            <option value="header">Header Navigation</option>
                            <option value="footer_col1">Footer - Quick Links</option>
                            <option value="footer_col2">Footer - Customer Service</option>
                            <option value="footer_col3">Footer - Policies</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label form-label-required">Label</label>
                        <input type="text" name="label" required class="form-input" placeholder="Menu item text">
                    </div>
                    <div>
                        <label class="form-label form-label-required">URL</label>
                        <input type="text" name="url" required class="form-input" placeholder="/about or https://...">
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Add Menu Item</button>
                </div>
            </form>
        </div>

        <!-- Current Menus -->
        <div class="space-y-6">
            <!-- Header Menu -->
            <div class="card p-6">
                <h3 class="font-semibold text-neutral-900 mb-3">Header Navigation</h3>
                <div class="space-y-2">
                    @forelse($headerMenus as $item)
                        <div class="flex items-center justify-between p-2 bg-neutral-50 rounded">
                            <div>
                                <span class="text-sm font-medium">{{ $item->label }}</span>
                                <span class="text-xs text-neutral-600 ml-2">{{ $item->url }}</span>
                            </div>
                            <form action="{{ route('admin.homepage.navigation.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-error-500 hover:text-error-700 text-sm">Remove</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No header menu items</p>
                    @endforelse
                </div>
            </div>

            @foreach(['footer_col1' => 'Quick Links', 'footer_col2' => 'Customer Service', 'footer_col3' => 'Policies'] as $loc => $label)
                <div class="card p-6">
                    <h3 class="font-semibold text-neutral-900 mb-3">Footer: {{ $label }}</h3>
                    <div class="space-y-2">
                        @php $items = ${str_replace('footer_', 'footerCol', ucfirst(str_replace('footer_col', 'footerCol', $loc)))} ?? collect(); @endphp
                        @forelse($$loc as $item)
                            <div class="flex items-center justify-between p-2 bg-neutral-50 rounded">
                                <div>
                                    <span class="text-sm font-medium">{{ $item->label }}</span>
                                    <span class="text-xs text-neutral-600 ml-2">{{ $item->url }}</span>
                                </div>
                                <form action="{{ route('admin.homepage.navigation.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-error-500 hover:text-error-700 text-sm">Remove</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-600">No items</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
