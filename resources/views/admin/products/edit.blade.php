@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="admin-card max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Product</h2>
    
    @if($errors->any())
        <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="product-form">
        @csrf
        @method('PUT')
        
        <!-- Hidden inputs for removed images -->
        <input type="hidden" name="removed_gallery_images" id="removed_gallery_images" value="">
        <input type="hidden" name="removed_main_image" id="removed_main_image" value="0">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Related Products</label>
                <select name="related_products[]" id="related-products-select" class="input-field" multiple="multiple">
                    @php
                        // Get related product IDs
                        $relatedProductIds = $product->relatedProducts->pluck('id')->toArray() ?? [];
                    @endphp
                    @foreach(App\Models\Product::where('id', '!=', $product->id)->get() as $prod)
                        <option value="{{ $prod->id }}" {{ in_array($prod->id, $relatedProductIds) ? 'selected' : '' }}>
                           <img src="{{ asset('storage/' . $prod->main_image) }}" 
                             alt="{{ $prod->name }}" 
                             class="w-6 h-6 object-cover rounded-full mr-2"> &nbsp; {{ $prod->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Type to search products. Hold Ctrl/Cmd to select multiple.</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category_id" required class="input-field">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select name="brand_id" class="input-field">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sub Category</label>
                <select name="sub_category_id" class="input-field">
                    <option value="">Select Sub Category</option>
                    @foreach($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}" {{ old('sub_category_id', $product->sub_category_id) == $subCategory->id ? 'selected' : '' }}>{{ $subCategory->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">MRP (₹) *</label>
                <input type="number" step="0.01" name="mrp" value="{{ old('mrp', $product->mrp) }}" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹) *</label>
                <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Discounted Price (₹)</label>
                <input type="number" step="0.01" name="discounted_price" value="{{ old('discounted_price', $product->discounted_price) }}" class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">GST (%) *</label>
                <input type="number" step="0.01" name="gst" value="{{ old('gst', $product->gst) }}" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Order Quantity *</label>
                <input type="number" name="min_order_quantity" value="{{ old('min_order_quantity', $product->min_order_quantity) }}" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select name="status" required class="input-field">
                    <option value="unpublished" {{ old('status', $product->status) == 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                    <option value="published" {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Main Image</label>
            @if($product->main_image)
                <div class="flex items-center space-x-4 mb-4">
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <button type="button" 
                                    onclick="removeMainImage()" 
                                    class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm">
                                Remove Image
                            </button>
                        </div>
                    </div>
                    <div id="main-image-removed" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-600 text-sm">Main image will be removed</p>
                    </div>
                </div>
            @endif
            <input type="file" name="main_image" accept="image/*" class="input-field">
            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images</label>
            @if($product->images->count() > 0)
                <div class="mb-4">
                    <div class="grid grid-cols-4 gap-2" id="gallery-images-container">
                        @foreach($product->images as $image)
                            <div class="relative group gallery-image-item" data-id="{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery" class="w-full h-24 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                    <button type="button" 
                                            onclick="removeGalleryImage({{ $image->id }})" 
                                            class="text-white bg-red-500 hover:bg-red-600 px-2 py-1 rounded text-sm">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="gallery-removed-notice" class="hidden mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-700 text-sm"><span id="removed-count">0</span> gallery image(s) marked for removal</p>
                    </div>
                </div>
            @endif
            <input type="file" name="gallery_images[]" accept="image/*" multiple class="input-field">
            <p class="text-xs text-gray-500 mt-1">Add more gallery images. Select multiple files.</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
            <textarea name="short_description" rows="3" class="input-field">{{ old('short_description', $product->short_description) }}</textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Description</label>
            <textarea name="full_description" id="full_description" rows="10" class="input-field">{{ old('full_description', $product->full_description) }}</textarea>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }} class="mr-2">
                <span class="text-sm text-gray-700">New Arrival</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="mr-2">
                <span class="text-sm text-gray-700">Featured</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_trending" value="1" {{ old('is_trending', $product->is_trending) ? 'checked' : '' }} class="mr-2">
                <span class="text-sm text-gray-700">Trending</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_top_rated" value="1" {{ old('is_top_rated', $product->is_top_rated) ? 'checked' : '' }} class="mr-2">
                <span class="text-sm text-gray-700">Top Rated</span>
            </label>
        </div>

        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Product Attributes</h3>
                <a href="{{ route('admin.product-attributes.create') }}?product_id={{ $product->id }}" 
                   class="text-sm text-[#D4AF37] hover:text-[#D4AF37]">
                    Manage Attributes Separately
                </a>
            </div>
            
            <!-- Hidden field to store attributes to delete -->
            <input type="hidden" name="attributes_to_delete" id="attributes_to_delete" value="">
            
            @if($product->attributes->count() > 0)
                <div class="space-y-2 mb-4" id="existing-attributes">
                    @foreach($product->attributes as $attr)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded attribute-item" data-id="{{ $attr->id }}">
                            <div class="flex-1">
                                <span class="font-medium">{{ ucfirst($attr->attribute_name) }}:</span>
                                <span>{{ $attr->attribute_value }}</span>
                                @if($attr->price_adjustment != 0)
                                    <span class="text-sm text-gray-600">(₹{{ $attr->price_adjustment }})</span>
                                @endif
                            </div>
                            <button type="button" 
                                    onclick="markAttributeForRemoval({{ $attr->id }})" 
                                    class="text-red-600 hover:text-red-700 text-sm">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm mb-4">No attributes added yet.</p>
            @endif
            
            <div id="attributes-container" class="space-y-4">
                <!-- New attributes will be added here -->
            </div>
            
            <button type="button" onclick="addAttributeRow()" class="mt-4 text-[#D4AF37] hover:text-[#D4AF37] font-medium">
                + Add New Attribute
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="input-field">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                <textarea name="meta_description" rows="2" class="input-field">{{ old('meta_description', $product->meta_description) }}</textarea>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}" class="input-field" placeholder="keyword1, keyword2, keyword3">
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                Update Product
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Initialize CKEditor
    CKEDITOR.replace('full_description');
    
    // Track removed gallery images
    let removedGalleryImages = [];
    let mainImageRemoved = false;
    
    // Initialize Select2 for related products
    $(document).ready(function() {
        $('#related-products-select').select2({
            placeholder: 'Search products by name or SKU...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("admin.products.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        exclude: '{{ $product->id }}' // Exclude current product
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.map(function(product) {
                            return {
                                id: product.id,
                                text: product.name + ' (SKU: ' + product.sku + ')'
                            };
                        }),
                        pagination: {
                            more: data.current_page < data.last_page
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            templateResult: formatProduct,
            templateSelection: formatProductSelection
        });
        
        function formatProduct(product) {
            if (product.loading) {
                return product.text;
            }
            return $('<span>' + product.text + '</span>');
        }
        
        function formatProductSelection(product) {
            return product.text;
        }
    });
    
    // Track attributes to delete
    let attributesToDelete = [];
    
    function markAttributeForRemoval(attributeId) {
        // Add to deletion list
        if (!attributesToDelete.includes(attributeId)) {
            attributesToDelete.push(attributeId);
        }
        
        // Update hidden field
        document.getElementById('attributes_to_delete').value = attributesToDelete.join(',');
        
        // Mark element for visual removal
        const element = document.querySelector(`.attribute-item[data-id="${attributeId}"]`);
        if (element) {
            element.style.opacity = '0.5';
            element.style.textDecoration = 'line-through';
            element.style.backgroundColor = '#fef2f2';
            
            // Remove remove button
            const removeBtn = element.querySelector('button');
            if (removeBtn) {
                removeBtn.innerHTML = 'Removing...';
                removeBtn.disabled = true;
            }
        }
    }
    
    // Remove main image
    function removeMainImage() {
        if (confirm('Are you sure you want to remove the main image? This cannot be undone.')) {
            mainImageRemoved = true;
            document.getElementById('removed_main_image').value = '1';
            
            // Hide the image and show removal notice
            const mainImageContainer = document.querySelector('div[onclick="removeMainImage()"]').closest('.group');
            mainImageContainer.style.display = 'none';
            document.getElementById('main-image-removed').classList.remove('hidden');
            
            // Also hide the checkbox if it exists
            const removeCheckbox = document.querySelector('input[name="remove_main_image"]');
            if (removeCheckbox) {
                removeCheckbox.closest('label').style.display = 'none';
            }
        }
    }
    
    // Handle gallery image removal
    function removeGalleryImage(imageId) {
        if (confirm('Are you sure you want to remove this gallery image?')) {
            // Add to removal list
            if (!removedGalleryImages.includes(imageId)) {
                removedGalleryImages.push(imageId);
            }
            
            // Update hidden field
            document.getElementById('removed_gallery_images').value = removedGalleryImages.join(',');
            
            // Mark image for visual removal
            const imageElement = document.querySelector(`.gallery-image-item[data-id="${imageId}"]`);
            if (imageElement) {
                imageElement.style.opacity = '0.5';
                imageElement.style.transform = 'scale(0.95)';
                imageElement.style.transition = 'all 0.3s ease';
                
                // Change remove button to "Removed"
                const removeBtn = imageElement.querySelector('button');
                if (removeBtn) {
                    removeBtn.innerHTML = 'Removed';
                    removeBtn.disabled = true;
                    removeBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                    removeBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                }
                
                // Update removal notice
                updateGalleryRemovalNotice();
            }
        }
    }
    
    // Update gallery removal notice
    function updateGalleryRemovalNotice() {
        const notice = document.getElementById('gallery-removed-notice');
        const countSpan = document.getElementById('removed-count');
        
        if (removedGalleryImages.length > 0) {
            countSpan.textContent = removedGalleryImages.length;
            notice.classList.remove('hidden');
        } else {
            notice.classList.add('hidden');
        }
    }
    
    // Add new attribute row
    let attributeIndex = 0;
    function addAttributeRow() {
        const container = document.getElementById('attributes-container');
        const newRow = document.createElement('div');
        newRow.className = 'attribute-row grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg';
        newRow.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Type</label>
                <select name="new_attributes[${attributeIndex}][name]" class="input-field">
                    <option value="">Select Type</option>
                    <option value="color">Color</option>
                    <option value="size">Size</option>
                    <option value="weight">Weight</option>
                    <option value="material">Material</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                <input type="text" name="new_attributes[${attributeIndex}][value]" class="input-field" placeholder="e.g., Red, XL, etc." required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (₹)</label>
                <input type="number" step="0.01" name="new_attributes[${attributeIndex}][price_adjustment]" value="0" class="input-field">
            </div>
            <div class="flex items-end space-x-2">
                <input type="number" name="new_attributes[${attributeIndex}][stock_adjustment]" value="0" class="input-field" placeholder="Stock Adj">
                <button type="button" onclick="this.closest('.attribute-row').remove()" class="text-red-600 hover:text-red-700 font-medium px-2">×</button>
            </div>
        `;
        container.appendChild(newRow);
        attributeIndex++;
    }
    
    // Form validation before submit
    document.getElementById('product-form').addEventListener('submit', function(e) {
        // Validate selling price is not greater than MRP
        const mrp = parseFloat(document.querySelector('input[name="mrp"]').value);
        const sellingPrice = parseFloat(document.querySelector('input[name="selling_price"]').value);
        
        if (sellingPrice > mrp) {
            e.preventDefault();
            alert('Selling price cannot be greater than MRP!');
            return false;
        }
        
        // Validate discounted price
        const discountedPrice = document.querySelector('input[name="discounted_price"]').value;
        if (discountedPrice) {
            const discPrice = parseFloat(discountedPrice);
            if (discPrice > sellingPrice) {
                e.preventDefault();
                alert('Discounted price cannot be greater than selling price!');
                return false;
            }
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Updating...';
    });
</script>
@endsection