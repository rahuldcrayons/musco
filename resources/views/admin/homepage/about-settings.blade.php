<x-layouts.admin>
    <x-slot name="title">About Page Settings</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">About Page Settings</h1>
                <p class="text-sm text-neutral-600 mt-1">Manage all content on the About Us page</p>
            </div>
            <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">Back to Homepage</a>
        </div>
    </x-slot>

    <form action="{{ route('admin.homepage.about-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            <!-- Hero Image -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Hero Image</h2>
                <div>
                    @if($settings['about_image'])
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $settings['about_image']) }}" alt="Current about image" class="max-h-40 object-cover rounded">
                        </div>
                    @endif
                    <label class="form-label">Upload Image</label>
                    <input type="file" name="about_image" accept="image/*" class="form-input">
                    <p class="form-help">Recommended: wide landscape image, 1200x400px or similar</p>
                </div>
            </div>

            <!-- Story Section -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Story Section</h2>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Heading</label>
                        <input type="text" name="about_heading" value="{{ $settings['about_heading'] }}" class="form-input" placeholder="We Are A Jewellery Brand Focused On...">
                    </div>
                    <div>
                        <label class="form-label">Paragraph 1</label>
                        <textarea name="about_paragraph_1" rows="3" class="form-input" placeholder="Founded with a passion for fine craftsmanship...">{{ $settings['about_paragraph_1'] }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Paragraph 2</label>
                        <textarea name="about_paragraph_2" rows="3" class="form-input" placeholder="Today, we've grown into a trusted destination...">{{ $settings['about_paragraph_2'] }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Three Services / USPs -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Three Services</h2>
                <div class="space-y-6">
                    @foreach([1, 2, 3] as $n)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ !$loop->last ? 'pb-6 border-b border-neutral-200' : '' }}">
                            <div>
                                <label class="form-label">Service {{ $n }} Title</label>
                                <input type="text" name="about_usp_{{ $n }}_title" value="{{ $settings['about_usp_'.$n.'_title'] }}" class="form-input" placeholder="{{ ['Creative Design', '100% Quality Guarantee', 'Online Support 24/7'][$n-1] }}">
                            </div>
                            <div>
                                <label class="form-label">Service {{ $n }} Description</label>
                                <textarea name="about_usp_{{ $n }}_desc" rows="2" class="form-input">{{ $settings['about_usp_'.$n.'_desc'] }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Three Cards -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Three Cards</h2>
                <div class="space-y-6">
                    @foreach([1, 2, 3] as $n)
                        <div class="{{ !$loop->last ? 'pb-6 border-b border-neutral-200' : '' }}">
                            <h3 class="text-sm font-semibold text-neutral-700 mb-3">Card {{ $n }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Title</label>
                                    <input type="text" name="about_card_{{ $n }}_title" value="{{ $settings['about_card_'.$n.'_title'] }}" class="form-input" placeholder="{{ ['What Do We Do?', 'Our Mission', 'History Of Us'][$n-1] }}">
                                </div>
                                <div>
                                    <label class="form-label">Description</label>
                                    <textarea name="about_card_{{ $n }}_desc" rows="2" class="form-input">{{ $settings['about_card_'.$n.'_desc'] }}</textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($settings['about_card_'.$n.'_image'])
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings['about_card_'.$n.'_image']) }}" alt="Card {{ $n }} image" class="h-20 object-cover rounded">
                                    </div>
                                @endif
                                <label class="form-label">Image</label>
                                <input type="file" name="about_card_{{ $n }}_image" accept="image/*" class="form-input">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- FAQ / Why Choose Us -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">Why Choose Us (FAQ Accordion)</h2>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Section Subtitle</label>
                        <input type="text" name="about_why_subtitle" value="{{ $settings['about_why_subtitle'] }}" class="form-input" placeholder="We believe in quality, transparency...">
                    </div>

                    @foreach([1, 2, 3, 4] as $n)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 {{ !$loop->last ? 'pb-4 border-b border-neutral-200' : '' }}">
                            <div>
                                <label class="form-label">FAQ {{ $n }} Title</label>
                                <input type="text" name="about_faq_{{ $n }}_title" value="{{ $settings['about_faq_'.$n.'_title'] }}" class="form-input" placeholder="{{ ['Fast Free Delivery', 'BIS Hallmarked & Certified', '100% Anti-Tarnish Quality', '7 Days Easy Returns'][$n-1] }}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="form-label">FAQ {{ $n }} Content</label>
                                <textarea name="about_faq_{{ $n }}_content" rows="2" class="form-input">{{ $settings['about_faq_'.$n.'_content'] }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Save -->
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Save About Page Settings</button>
            </div>
        </div>
    </form>
</x-layouts.admin>
