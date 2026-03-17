<x-layouts.admin>
    <x-slot name="title">Add Product</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm text-neutral-600 mb-1">
                    <a href="{{ route('admin.products.index') }}" class="hover:text-primary-600 transition-colors">Products</a>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-neutral-700">Add Product</span>
                </div>
                <h1 class="text-2xl font-bold text-neutral-900">Add New Product</h1>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl" x-data="productForm()">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h2 class="font-semibold text-neutral-900">Basic Information</h2>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="name" class="form-label form-label-required">Product Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="form-input w-full @error('name') form-input-error @enderror"
                               placeholder="e.g. Cotton Floral Dress"
                               @input="if(!slugManual) slug = toSlug($event.target.value)">
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="sku" class="form-label form-label-required">SKU</label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                   class="form-input w-full @error('sku') form-input-error @enderror"
                                   placeholder="e.g. FK-DRESS-001">
                            @error('sku')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" name="slug" id="slug" x-model="slug"
                                   class="form-input w-full @error('slug') form-input-error @enderror"
                                   placeholder="auto-generated-from-name"
                                   @input="slugManual = ($event.target.value.trim() !== '')">
                            <p class="form-help">Leave blank to auto-generate</p>
                            @error('slug')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="category_id" class="form-label form-label-required">Category</label>
                            <select name="category_id" id="category_id" required
                                    class="form-input w-full @error('category_id') form-input-error @enderror">
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="seller_id" class="form-label">Seller</label>
                        <select name="seller_id" id="seller_id"
                                class="form-input w-full @error('seller_id') form-input-error @enderror">
                            <option value="">Select seller</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->store_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('seller_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="brand_id" class="form-label">Brand</label>
                        <select name="brand_id" id="brand_id"
                                class="form-input w-full @error('brand_id') form-input-error @enderror">
                            <option value="">Select brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" rows="2"
                                  class="form-input w-full @error('short_description') form-input-error @enderror"
                                  placeholder="Brief product summary...">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="form-label form-label-required">Description</label>
                        <textarea name="description" id="description" rows="6" required
                                  class="form-input w-full @error('description') form-input-error @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="card overflow-hidden" x-data="imageUploader()">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h2 class="font-semibold text-neutral-900">Product Images</h2>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Main Image -->
                    <div>
                        <label class="form-label form-label-required">Main Image (Thumbnail)</label>
                        <p class="text-sm text-neutral-600 mb-3">This will be the primary display image for the product.</p>
                        <div class="flex items-start gap-4">
                            <!-- Preview -->
                            <div x-show="mainPreview" x-transition
                                 class="relative w-32 h-32 rounded-lg overflow-hidden ring-2 ring-primary-500 shrink-0">
                                <img :src="mainPreview" class="w-full h-full object-cover">
                                <button type="button" @click="removeMainImage()"
                                        class="absolute top-1.5 right-1.5 w-6 h-6 bg-white/90 hover:bg-error-50 rounded-full flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <span class="absolute bottom-0 left-0 right-0 px-2 py-1 bg-primary-600 text-white text-[10px] font-semibold text-center">
                                    Main Image
                                </span>
                            </div>
                            <!-- Upload zone -->
                            <div class="flex-1 border-2 border-dashed border-neutral-300 rounded-lg p-4 text-center hover:border-primary-400 transition-colors cursor-pointer"
                                 @click="$refs.mainFileInput.click()"
                                 :class="{ 'border-primary-400 bg-primary-50/50': mainDragOver }"
                                 @dragover.prevent="mainDragOver = true"
                                 @dragleave.prevent="mainDragOver = false"
                                 @drop.prevent="mainDragOver = false; handleMainImage($event.dataTransfer.files[0])">
                                <input type="file" name="main_image" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                                       x-ref="mainFileInput" class="hidden" @change="handleMainImage($event.target.files[0])">
                                <div class="flex flex-col items-center py-2">
                                    <svg class="w-8 h-8 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-neutral-700" x-text="mainPreview ? 'Click to change image' : 'Click to upload main image'"></p>
                                    <p class="text-xs text-neutral-600 mt-1">JPEG, PNG, WebP, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>
                        @error('main_image')
                            <p class="form-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-neutral-100"></div>

                    <!-- Gallery Images -->
                    <div>
                        <label class="form-label">Gallery Images (Optional)</label>
                        <p class="text-sm text-neutral-600 mb-3">Upload additional product images. Max 10 images, 2MB each.</p>

                        <div class="border-2 border-dashed border-neutral-300 rounded-lg p-5 text-center hover:border-primary-400 transition-colors cursor-pointer"
                             @click="$refs.galleryInput.click()"
                             @dragover.prevent="galleryDragOver = true"
                             @dragleave.prevent="galleryDragOver = false"
                             @drop.prevent="galleryDragOver = false; handleGalleryFiles($event.dataTransfer.files)"
                             :class="{ 'border-primary-400 bg-primary-50/50': galleryDragOver }">
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                                   x-ref="galleryInput" class="hidden" @change="handleGalleryFiles($event.target.files)">
                            <div class="flex flex-col items-center py-1">
                                <svg class="w-7 h-7 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <p class="text-sm font-medium text-neutral-700">Click to upload or drag and drop</p>
                                <p class="text-xs text-neutral-600 mt-1">JPEG, PNG, WebP, GIF up to 2MB</p>
                            </div>
                        </div>

                        <!-- Gallery preview grid -->
                        <div x-show="galleryPreviews.length > 0" x-transition class="mt-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                <template x-for="(preview, index) in galleryPreviews" :key="index">
                                    <div class="relative group rounded-lg overflow-hidden ring-1 ring-neutral-200">
                                        <img :src="preview.url" class="w-full aspect-square object-cover">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors"></div>
                                        <button type="button" @click="removeGalleryImage(index)"
                                                class="absolute top-1.5 right-1.5 w-6 h-6 bg-white/90 hover:bg-error-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-3.5 h-3.5 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        <div class="absolute bottom-0 left-0 right-0 px-2 py-1 bg-black/50 text-white text-[10px] truncate">
                                            <span x-text="preview.name"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <p class="text-xs text-neutral-600 mt-2">
                                <span x-text="galleryPreviews.length"></span> gallery image(s) selected
                            </p>
                        </div>

                        @error('images')
                            <p class="form-error mt-2">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="form-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="font-semibold text-neutral-900">Pricing & Inventory</h2>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div>
                            <label for="price" class="form-label form-label-required">Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-600 text-sm">$</span>
                                <input type="number" name="price" id="price" value="{{ old('price') }}" required
                                       step="0.01" min="0"
                                       class="form-input w-full pl-7 @error('price') form-input-error @enderror">
                            </div>
                            @error('price')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sale_price" class="form-label">Sale Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-600 text-sm">$</span>
                                <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}"
                                       step="0.01" min="0"
                                       class="form-input w-full pl-7 @error('sale_price') form-input-error @enderror">
                            </div>
                            @error('sale_price')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="cost_price" class="form-label">Cost Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-600 text-sm">$</span>
                                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}"
                                       step="0.01" min="0"
                                       class="form-input w-full pl-7 @error('cost_price') form-input-error @enderror">
                            </div>
                            @error('cost_price')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="stock_quantity" class="form-label form-label-required">Stock Qty</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                                   min="0"
                                   class="form-input w-full @error('stock_quantity') form-input-error @enderror">
                            @error('stock_quantity')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Visibility -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <h2 class="font-semibold text-neutral-900">Status & Visibility</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="form-checkbox">
                            <span class="text-sm text-neutral-700">Active</span>
                            <span class="text-xs text-neutral-600">Visible on the storefront</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                   class="form-checkbox">
                            <span class="text-sm text-neutral-700">Featured</span>
                            <span class="text-xs text-neutral-600">Show in featured sections</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Attributes / Specifications -->
            @if($attributes->count())
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <h2 class="font-semibold text-neutral-900">Attributes / Specifications</h2>
                    </div>
                    <p class="text-xs text-neutral-600 mt-1">Select applicable attributes for this product. Leave blank to skip.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($attributes as $attribute)
                            <div>
                                <label class="form-label flex items-center gap-2">
                                    @if($attribute->type === 'color')
                                        <svg class="w-3.5 h-3.5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                        </svg>
                                    @elseif($attribute->type === 'select')
                                        <svg class="w-3.5 h-3.5 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                        </svg>
                                    @endif
                                    {{ $attribute->name }}
                                </label>
                                @if($attribute->type === 'text')
                                    <input type="text" name="product_attributes[{{ $attribute->name }}]"
                                           value="{{ old('product_attributes.' . $attribute->name) }}"
                                           class="form-input w-full text-sm"
                                           placeholder="Enter {{ strtolower($attribute->name) }}">
                                @else
                                    <select name="product_attributes[{{ $attribute->name }}]" class="form-input w-full text-sm">
                                        <option value="">-- Select --</option>
                                        @foreach($attribute->values as $value)
                                            <option value="{{ $value->value }}"
                                                    {{ old('product_attributes.' . $attribute->name) === $value->value ? 'selected' : '' }}>
                                                {{ $value->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($attribute->type === 'color' && $attribute->values->count())
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($attribute->values->take(10) as $value)
                                                @if($value->color_code)
                                                    <div class="w-5 h-5 rounded-full border border-neutral-200 cursor-default" style="background-color: {{ $value->color_code }}" title="{{ $value->value }}"></div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- SEO -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h2 class="font-semibold text-neutral-900">SEO</h2>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                               class="form-input w-full @error('meta_title') form-input-error @enderror"
                               placeholder="Defaults to product name">
                        @error('meta_title')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="2"
                                  class="form-input w-full @error('meta_description') form-input-error @enderror"
                                  placeholder="SEO description for search engines...">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
        .ck-editor__editable { min-height: 200px; }
        .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) { border-color: #d4d4d4; }
        .ck.ck-editor__main>.ck-editor__editable.ck-focused { border-color: var(--color-primary-500, #7c3aed); box-shadow: 0 0 0 1px var(--color-primary-500, #7c3aed); }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        function productForm() {
            return {
                slug: '{{ old("slug", "") }}',
                slugManual: {{ old('slug') ? 'true' : 'false' }},
                toSlug(text) {
                    return text
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_]+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                }
            };
        }

        function imageUploader() {
            return {
                mainPreview: null,
                mainDragOver: false,
                galleryPreviews: [],
                galleryDragOver: false,
                galleryFileList: new DataTransfer(),
                handleMainImage(file) {
                    if (!file || !file.type.startsWith('image/')) return;
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error(file.name + ' exceeds 2MB limit.');
                        return;
                    }
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.mainFileInput.files = dt.files;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.mainPreview = e.target.result; };
                    reader.readAsDataURL(file);
                },
                removeMainImage() {
                    this.mainPreview = null;
                    this.$refs.mainFileInput.value = '';
                },
                handleGalleryFiles(files) {
                    for (const file of files) {
                        if (!file.type.startsWith('image/')) continue;
                        if (file.size > 2 * 1024 * 1024) {
                            toastr.error(file.name + ' exceeds 2MB limit.');
                            continue;
                        }
                        if (this.galleryPreviews.length >= 10) {
                            toastr.error('Maximum 10 gallery images.');
                            break;
                        }
                        this.galleryFileList.items.add(file);
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.galleryPreviews.push({ url: e.target.result, name: file.name });
                        };
                        reader.readAsDataURL(file);
                    }
                    this.$refs.galleryInput.files = this.galleryFileList.files;
                },
                removeGalleryImage(index) {
                    this.galleryPreviews.splice(index, 1);
                    this.galleryFileList.items.remove(index);
                    this.$refs.galleryInput.files = this.galleryFileList.files;
                }
            };
        }

        ClassicEditor
            .create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'link', 'blockQuote', '|', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                    ]
                }
            })
            .catch(error => console.error(error));
    </script>
    @endpush
</x-layouts.admin>
