<x-layouts.admin>
    <x-slot name="title">Edit {{ $product->name }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-sm">
                    <a href="{{ route('admin.products.index') }}" class="text-neutral-500 hover:text-neutral-800 transition-colors">Products</a>
                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-neutral-900 font-semibold">{{ $product->name }}</span>
                </div>
                @if($product->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600">Draft</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary text-sm">
                    Duplicate
                </a>
                <a href="{{ route('product.show', $product) }}" target="_blank" class="btn btn-secondary text-sm">
                    View
                </a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button" class="btn btn-secondary text-sm">
                        More actions
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 py-1 z-50">
                        <a href="{{ route('admin.products.index') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Back to products</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="productForm()" x-ref="productFormRoot">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="flex gap-5" style="align-items: flex-start;">
                {{-- ============================================
                     LEFT COLUMN (~65%)
                     ============================================ --}}
                <div class="flex-1 space-y-5" style="min-width: 0;">

                    {{-- Title & Description Card --}}
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4">
                            <h2 class="text-sm font-semibold text-neutral-900">Title</h2>
                        </div>
                        <div class="px-5 pb-5 space-y-4">
                            <div>
                                <label for="name" class="form-label form-label-required">Product name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                                       class="form-input w-full @error('name') form-input-error @enderror"
                                       x-ref="productName"
                                       @input="if(!slugManual) slug = toSlug($event.target.value); autoGenerateSeo($event.target.value)">
                                @error('name')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" name="slug" id="slug" x-model="slug"
                                       class="form-input w-full @error('slug') form-input-error @enderror"
                                       @input="slugManual = ($event.target.value.trim() !== '')">
                                @error('slug')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="short_description" class="form-label">Short description</label>
                                <textarea name="short_description" id="short_description" rows="2"
                                          class="form-input w-full @error('short_description') form-input-error @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                                @error('short_description')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="form-label form-label-required">Description</label>
                                <textarea name="description" id="description" rows="6" required
                                          class="form-input w-full @error('description') form-input-error @enderror">{!! old('description', $product->description) !!}</textarea>
                                @error('description')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Media Card --}}
                    <div class="card overflow-hidden" x-data="imageManager()">
                        <div class="px-5 py-4">
                            <h2 class="text-sm font-semibold text-neutral-900">Media</h2>
                        </div>
                        <div class="px-5 pb-5 space-y-5">
                            {{-- Main Image --}}
                            <div>
                                <label class="form-label">Main image (thumbnail)</label>
                                <p class="text-xs text-neutral-500 mb-3">This is the primary display image for the product.</p>
                                <div class="flex items-start gap-4">
                                    @php $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                                    {{-- Current main image --}}
                                    <div class="relative w-32 h-32 rounded-lg overflow-hidden ring-2 ring-neutral-300 shrink-0"
                                         x-show="!mainImageChanged && !mainImageDeleted">
                                        @if($primaryImage)
                                            <img src="{{ $primaryImage->url }}" class="w-full h-full object-cover">
                                            <span class="absolute bottom-0 left-0 right-0 px-2 py-1 bg-neutral-800 text-white text-center" style="font-size: 10px; font-weight: 600;">Main Image</span>
                                        @else
                                            <div class="w-full h-full bg-neutral-100 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- New main image preview --}}
                                    <div x-show="mainPreview" x-transition
                                         class="relative w-32 h-32 rounded-lg overflow-hidden ring-2 ring-neutral-300 shrink-0">
                                        <img :src="mainPreview" class="w-full h-full object-cover">
                                        <button type="button" @click="removeNewMainImage()"
                                                class="absolute top-1.5 right-1.5 w-6 h-6 bg-white/90 hover:bg-red-50 rounded-full flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        <span class="absolute bottom-0 left-0 right-0 px-2 py-1 bg-neutral-800 text-white text-center" style="font-size: 10px; font-weight: 600;">New Main</span>
                                    </div>
                                    {{-- Upload zone --}}
                                    <div class="flex-1 border-2 border-dashed border-neutral-300 rounded-lg p-4 text-center hover:border-neutral-400 transition-colors cursor-pointer"
                                         @click="$refs.mainFileInput.click()"
                                         :class="{ 'border-blue-400 bg-blue-50/50': mainDragOver }"
                                         @dragover.prevent="mainDragOver = true"
                                         @dragleave.prevent="mainDragOver = false"
                                         @drop.prevent="mainDragOver = false; handleMainImage($event.dataTransfer.files[0])">
                                        <input type="file" name="main_image" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                                               x-ref="mainFileInput" class="hidden" @change="handleMainImage($event.target.files[0])">
                                        <div class="flex flex-col items-center py-2">
                                            <svg class="w-8 h-8 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-sm font-medium text-neutral-700">Click to replace main image</p>
                                            <p class="text-xs text-neutral-500 mt-1">JPEG, PNG, WebP, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                </div>
                                @error('main_image')
                                    <p class="form-error mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="border-t border-neutral-100"></div>

                            {{-- Gallery Images --}}
                            <div>
                                <label class="form-label">Gallery images</label>

                                @php $galleryImages = $product->images->where('id', '!=', $primaryImage?->id)->sortBy('position'); @endphp
                                @if($galleryImages->count())
                                    <p class="text-xs text-neutral-500 mb-3">Current gallery images</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mb-5">
                                        @foreach($galleryImages as $image)
                                            <div class="relative group rounded-lg overflow-hidden ring-1 ring-neutral-200"
                                                 x-show="!deletedIds.includes({{ $image->id }})">
                                                <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-full aspect-square object-cover">
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors"></div>
                                                <button type="button" @click="markForDelete({{ $image->id }})"
                                                        class="absolute top-1.5 right-1.5 w-6 h-6 bg-white/90 hover:bg-red-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" title="Delete">
                                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <template x-for="id in deletedIds" :key="id">
                                    <input type="hidden" name="delete_images[]" :value="id">
                                </template>

                                <p class="text-xs text-neutral-500 mb-3">Add more gallery images. Max 10 images, 2MB each.</p>
                                <div class="border-2 border-dashed border-neutral-300 rounded-lg p-5 text-center hover:border-neutral-400 transition-colors cursor-pointer"
                                     @click="$refs.galleryInput.click()"
                                     @dragover.prevent="galleryDragOver = true"
                                     @dragleave.prevent="galleryDragOver = false"
                                     @drop.prevent="galleryDragOver = false; handleGalleryFiles($event.dataTransfer.files)"
                                     :class="{ 'border-blue-400 bg-blue-50/50': galleryDragOver }">
                                    <input type="file" name="images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                                           x-ref="galleryInput" class="hidden" @change="handleGalleryFiles($event.target.files)">
                                    <div class="flex flex-col items-center py-1">
                                        <svg class="w-7 h-7 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <p class="text-sm font-medium text-neutral-700">Click to upload or drag and drop</p>
                                        <p class="text-xs text-neutral-500 mt-1">JPEG, PNG, WebP, GIF up to 2MB</p>
                                    </div>
                                </div>

                                <div x-show="galleryPreviews.length > 0" x-transition class="mt-4">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                        <template x-for="(preview, index) in galleryPreviews" :key="index">
                                            <div class="relative group rounded-lg overflow-hidden ring-1 ring-neutral-200">
                                                <img :src="preview.url" class="w-full aspect-square object-cover">
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors"></div>
                                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-green-600 text-white rounded" style="font-size: 10px; font-weight: 600;">New</span>
                                                <button type="button" @click="removeGalleryImage(index)"
                                                        class="absolute top-1.5 right-1.5 w-6 h-6 bg-white/90 hover:bg-red-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                                <div class="absolute bottom-0 left-0 right-0 px-2 py-1 bg-black/50 text-white truncate" style="font-size: 10px;">
                                                    <span x-text="preview.name"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-xs text-neutral-500 mt-2">
                                        <span x-text="galleryPreviews.length"></span> new image(s) to upload
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

                    {{-- Pricing Card --}}
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4">
                            <h2 class="text-sm font-semibold text-neutral-900">Pricing</h2>
                        </div>
                        <div class="px-5 pb-5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="price" class="form-label form-label-required">Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-sm">$</span>
                                        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required
                                               step="0.01" min="0"
                                               class="form-input w-full pl-7 @error('price') form-input-error @enderror">
                                    </div>
                                    @error('price')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="sale_price" class="form-label">Compare at price / MRP</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-sm">$</span>
                                        <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                                               step="0.01" min="0"
                                               class="form-input w-full pl-7 @error('sale_price') form-input-error @enderror">
                                    </div>
                                    @error('sale_price')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="cost_price" class="form-label">Cost price</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-sm">$</span>
                                        <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}"
                                               step="0.01" min="0"
                                               class="form-input w-full pl-7 @error('cost_price') form-input-error @enderror">
                                    </div>
                                    @error('cost_price')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Inventory Card --}}
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4">
                            <h2 class="text-sm font-semibold text-neutral-900">Inventory</h2>
                        </div>
                        <div class="px-5 pb-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="sku" class="form-label form-label-required">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required
                                           class="form-input w-full @error('sku') form-input-error @enderror">
                                    @error('sku')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="stock_quantity" class="form-label form-label-required">Stock quantity</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required
                                           min="0"
                                           class="form-input w-full @error('stock_quantity') form-input-error @enderror">
                                    @error('stock_quantity')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Attributes / Specifications Card --}}
                    @if($attributes->count())
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4">
                            <h2 class="text-sm font-semibold text-neutral-900">Attributes</h2>
                            <p class="text-xs text-neutral-500 mt-0.5">Select applicable attributes for this product. Leave blank to skip.</p>
                        </div>
                        <div class="px-5 pb-5">
                            @php $productAttrs = $product->attributes ?? []; @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($attributes as $attribute)
                                    <div>
                                        <label class="form-label">
                                            {{ $attribute->name }}
                                            @if(isset($productAttrs[$attribute->name]))
                                                <span class="inline-flex items-center justify-center w-1.5 h-1.5 rounded-full bg-green-500 ml-1"></span>
                                            @endif
                                        </label>
                                        @if($attribute->type === 'text')
                                            <input type="text" name="product_attributes[{{ $attribute->name }}]"
                                                   value="{{ old('product_attributes.' . $attribute->name, $productAttrs[$attribute->name] ?? '') }}"
                                                   class="form-input w-full text-sm"
                                                   placeholder="Enter {{ strtolower($attribute->name) }}">
                                        @else
                                            <select name="product_attributes[{{ $attribute->name }}]" class="form-input w-full text-sm">
                                                <option value="">-- Select --</option>
                                                @foreach($attribute->values as $value)
                                                    <option value="{{ $value->value }}"
                                                            {{ old('product_attributes.' . $attribute->name, $productAttrs[$attribute->name] ?? '') === $value->value ? 'selected' : '' }}>
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($attribute->type === 'color' && $attribute->values->count())
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @foreach($attribute->values->take(10) as $value)
                                                        @if($value->color_code)
                                                            <div class="w-5 h-5 rounded-full border border-neutral-200 cursor-default {{ isset($productAttrs[$attribute->name]) && $productAttrs[$attribute->name] === $value->value ? 'ring-2 ring-neutral-800' : '' }}" style="background-color: {{ $value->color_code }}" title="{{ $value->value }}"></div>
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

                    {{-- SEO Card --}}
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-neutral-900">Search engine listing</h2>
                            <button type="button" @click="autoFillSeo()" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                Auto-generate
                            </button>
                        </div>
                        <div class="px-5 pb-5 space-y-4">
                            {{-- Google Preview Snippet --}}
                            <div class="border border-neutral-200 rounded-lg p-4 bg-neutral-50">
                                <p class="text-xs text-neutral-500 mb-2 font-medium">Search engine preview</p>
                                <div>
                                    <p class="text-blue-700 text-base font-medium truncate" style="font-family: Arial, sans-serif;" x-text="seoTitle || '{{ addslashes($product->meta_title ?: $product->name) }}'"></p>
                                    <p class="text-green-700 text-xs truncate mt-0.5" style="font-family: Arial, sans-serif;">{{ url('/') }}/product/<span x-text="slug || '{{ $product->slug }}'"></span></p>
                                    <p class="text-neutral-600 text-xs mt-1 line-clamp-2" style="font-family: Arial, sans-serif;" x-text="seoDescription || '{{ addslashes($product->meta_description ?: Str::limit(strip_tags($product->description), 160)) }}'"></p>
                                </div>
                            </div>

                            <div>
                                <label for="meta_title" class="form-label">Meta title</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $product->meta_title) }}"
                                       class="form-input w-full @error('meta_title') form-input-error @enderror"
                                       x-model="seoTitle"
                                       maxlength="70">
                                <p class="text-xs text-neutral-400 mt-1"><span x-text="seoTitle ? seoTitle.length : 0"></span> / 70 characters</p>
                                @error('meta_title')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="meta_description" class="form-label">Meta description</label>
                                <textarea name="meta_description" id="meta_description" rows="2"
                                          class="form-input w-full @error('meta_description') form-input-error @enderror"
                                          x-model="seoDescription"
                                          maxlength="160">{{ old('meta_description', $product->meta_description) }}</textarea>
                                <p class="text-xs text-neutral-400 mt-1"><span x-text="seoDescription ? seoDescription.length : 0"></span> / 160 characters</p>
                                @error('meta_description')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ============================================
                     RIGHT COLUMN (~35%)
                     ============================================ --}}
                <div class="space-y-5" style="width: 340px; flex-shrink: 0;">

                    {{-- Status Card --}}
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4">
                            <h2 class="text-sm font-semibold text-neutral-900">Status</h2>
                        </div>
                        <div class="px-5 pb-5">
                            <select name="is_active" class="form-input w-full">
                                <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $product->is_active) ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                    </div>

                    {{-- Product Organization Card --}}
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4">
                            <h2 class="text-sm font-semibold text-neutral-900">Product organization</h2>
                        </div>
                        <div class="px-5 pb-5 space-y-4">
                            <div>
                                <label for="category_id" class="form-label form-label-required">Category</label>
                                <select name="category_id" id="category_id" required
                                        class="form-input w-full @error('category_id') form-input-error @enderror">
                                    <option value="">Select category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="brand_id" class="form-label">Brand</label>
                                <select name="brand_id" id="brand_id"
                                        class="form-input w-full @error('brand_id') form-input-error @enderror">
                                    <option value="">Select brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="seller_id" class="form-label">Seller</label>
                                <select name="seller_id" id="seller_id"
                                        class="form-input w-full @error('seller_id') form-input-error @enderror">
                                    <option value="">Select seller</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{ $seller->id }}" {{ old('seller_id', $product->seller_id) == $seller->id ? 'selected' : '' }}>
                                            {{ $seller->store_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('seller_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="border-t border-neutral-100 pt-4">
                                <label class="form-label">Tags</label>
                                <p class="text-xs text-neutral-500">Coming soon</p>
                            </div>
                        </div>
                    </div>

                    {{-- Featured Card --}}
                    <div class="card overflow-hidden">
                        <div class="px-5 py-4">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                       class="form-checkbox">
                                <span class="text-sm font-semibold text-neutral-900">Featured product</span>
                            </label>
                            <p class="text-xs text-neutral-500 mt-1 ml-6">Show this product in featured sections on the storefront.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Sticky Save Bar --}}
            <div class="sticky bottom-0 z-40 mt-5 -mx-6 px-6 py-3 bg-white border-t border-neutral-200 flex items-center justify-end gap-3" style="margin-left: -1.5rem; margin-right: -1.5rem; padding-left: 1.5rem; padding-right: 1.5rem;">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Discard</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>

        {{-- Danger Zone --}}
        <div class="mt-8 card overflow-hidden" style="border-color: #fca5a5;">
            <div class="px-5 py-4">
                <h2 class="text-sm font-semibold text-red-700">Danger zone</h2>
            </div>
            <div class="px-5 pb-5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-900">Delete this product</p>
                    <p class="text-xs text-neutral-500">Once deleted, this action cannot be undone.</p>
                </div>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete &quot;{{ $product->name }}&quot;? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete product</button>
                </form>
            </div>
        </div>
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
                slug: '{{ old("slug", $product->slug) }}',
                slugManual: true,
                seoTitle: '{{ addslashes(old("meta_title", $product->meta_title ?? "")) }}',
                seoDescription: '{{ addslashes(old("meta_description", $product->meta_description ?? "")) }}',
                seoManualTitle: {{ old('meta_title', $product->meta_title) ? 'true' : 'false' }},
                seoManualDescription: {{ old('meta_description', $product->meta_description) ? 'true' : 'false' }},
                toSlug(text) {
                    return text
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_]+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                },
                autoGenerateSeo(title) {
                    if (!this.seoManualTitle || this.seoTitle === '') {
                        this.seoTitle = title ? (title + ' - {{ addslashes(config("app.name")) }}').substring(0, 70) : '';
                        this.seoManualTitle = false;
                    }
                    if (!this.seoManualDescription || this.seoDescription === '') {
                        this.seoDescription = title ? ('Shop ' + title + ' at {{ addslashes(config("app.name")) }}. Great prices, fast shipping, and quality guaranteed.').substring(0, 160) : '';
                        this.seoManualDescription = false;
                    }
                },
                autoFillSeo() {
                    var titleEl = document.getElementById('name');
                    var title = titleEl ? titleEl.value : '';
                    if (!title) {
                        if (typeof toastr !== 'undefined') toastr.warning('Enter a product name first.');
                        return;
                    }
                    this.seoTitle = (title + ' - {{ addslashes(config("app.name")) }}').substring(0, 70);
                    this.seoDescription = ('Shop ' + title + ' at {{ addslashes(config("app.name")) }}. Great prices, fast shipping, and quality guaranteed.').substring(0, 160);
                    this.seoManualTitle = false;
                    this.seoManualDescription = false;
                }
            };
        }

        function imageManager() {
            return {
                deletedIds: [],
                mainPreview: null,
                mainImageChanged: false,
                mainImageDeleted: false,
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
                    this.mainImageChanged = true;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.mainPreview = e.target.result; };
                    reader.readAsDataURL(file);
                },
                removeNewMainImage() {
                    this.mainPreview = null;
                    this.mainImageChanged = false;
                    this.$refs.mainFileInput.value = '';
                },
                markForDelete(id) {
                    if (!confirm('Mark this image for deletion?')) return;
                    this.deletedIds.push(id);
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
