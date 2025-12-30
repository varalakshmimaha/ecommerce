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
        
        <!-- Related Products Section -->
        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Related Products</h3>
                <button type="button" onclick="openSuveeRelatedProductsModal()" class="suvee-add-related-btn bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2.5 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-2 relative z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Add Related Products</span>
                </button>
            </div>
            
            <!-- Hidden field to store selected product IDs -->
            <input type="hidden" name="related_products" id="related_products" value="{{ $product->relatedProducts->pluck('id')->implode(',') }}">
            
            <!-- Display selected related products -->
            <div id="related-products-container" class="space-y-3 mb-6">
                @foreach($product->relatedProducts as $relatedProduct)
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 related-product-item" data-id="{{ $relatedProduct->id }}">
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <img src="{{ asset('storage/' . $relatedProduct->main_image) }}" alt="{{ $relatedProduct->name }}" class="w-14 h-14 object-cover rounded-lg border border-gray-300">
                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-brand-gold rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $relatedProduct->name }}</p>
                                <div class="flex items-center space-x-3 mt-1">
                                    <span class="text-sm text-gray-600 bg-gray-100 px-2 py-0.5 rounded">SKU: {{ $relatedProduct->sku }}</span>
                                    <span class="text-xs text-brand-gold bg-brand-gold/10 px-2 py-0.5 rounded">₹{{ number_format($relatedProduct->selling_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="removeRelatedProduct({{ $relatedProduct->id }})" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
                
                @if($product->relatedProducts->count() == 0)
                    <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-xl bg-gradient-to-b from-gray-50 to-white">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-gray-500 font-medium">No related products added yet</p>
                        <p class="text-sm text-gray-400 mt-1">Click "Add Related Products" to get started</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Product Attributes</h3>
                <a href="{{ route('admin.product-attributes.create') }}?product_id={{ $product->id }}" 
                   class="text-sm text-brand-gold hover:text-brand-amber">
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
            
            <button type="button" onclick="addAttributeRow()" class="mt-4 text-brand-gold hover:text-brand-amber font-medium">
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
            <a href="{{ route('admin.products.index') }}" class="suvee-form-cancel-btn px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-all duration-300 hover:shadow-md transform hover:-translate-y-0.5">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                Update Product
            </button>
        </div>
    </form>
</div>

<!-- Related Products Modal -->
<div id="suvee-related-products-modal" class="suvee-modal-overlay fixed inset-0 z-[999999] overflow-y-auto hidden">
    <div class="suvee-modal-container flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="suvee-modal-backdrop fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeSuveeRelatedProductsModal()"></div>
        
        <!-- Modal -->
        <div class="suvee-modal-content relative w-full max-w-3xl lg:max-w-4xl transform rounded-2xl bg-gradient-to-b from-white to-gray-50 shadow-2xl transition-all z-[999999]">
            <!-- Modal Header -->
            <div class="suvee-modal-header border-b border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="suvee-modal-title text-2xl font-bold text-gray-900">Add Related Products</h3>
                        <p class="suvee-modal-subtitle text-sm text-gray-500 mt-1">Search and select products to add as related items</p>
                    </div>
                    <button onclick="closeSuveeRelatedProductsModal()" class="suvee-modal-close-btn rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="suvee-modal-body p-6">
                <!-- Search Bar -->
                <div class="suvee-search-container relative mb-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="suvee-search-icon h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               id="suvee-product-search" 
                               placeholder="Search by product name or SKU..." 
                               class="suvee-search-input w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all text-base">
                    </div>
                    <p class="suvee-search-hint text-xs text-gray-400 mt-2 ml-1">Type at least 2 characters to search products</p>
                </div>
                
                <!-- Selected Products Count -->
                <div id="suvee-selected-count" class="suvee-selected-count mb-4 hidden">
                    <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-brand-gold/10 text-brand-gold text-sm font-medium">
                        <span id="suvee-selected-number">0</span> product(s) selected
                    </div>
                </div>
                
                <!-- Search Results -->
                <div class="suvee-search-results mb-6">
                    <h4 class="suvee-results-title text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Search Results</h4>
                    <div id="suvee-search-results-list" class="suvee-results-list space-y-3 max-h-[400px] overflow-y-auto pr-2">
                        <!-- Placeholder -->
                        <div class="text-center py-10">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">Start typing to search for products</p>
                            <p class="text-sm text-gray-400 mt-1">Results will appear here</p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="suvee-modal-footer flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="suvee-results-counter text-sm text-gray-500">
                        <span id="suvee-total-results">0</span> products found
                    </div>
                    <div class="suvee-footer-buttons flex space-x-3">
                        <button type="button" onclick="closeSuveeRelatedProductsModal()" class="suvee-cancel-btn px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="button" onclick="addSuveeSelectedProducts()" id="suvee-add-products-btn" class="suvee-add-btn px-5 py-2.5 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white rounded-lg hover:shadow-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300" disabled>
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Add Selected Products</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    // Initialize CKEditor
    CKEDITOR.replace('full_description');
    
    // Track removed gallery images
    let removedGalleryImages = [];
    let mainImageRemoved = false;
    
    // Track related products
    let selectedProducts = new Set(@json($product->relatedProducts->pluck('id')->toArray()));
    let searchTimeout = null;
    
    // Related Products Modal Functions
    function openSuveeRelatedProductsModal() {
        const modal = document.getElementById('suvee-related-products-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scroll
        document.getElementById('suvee-product-search').focus();
        updateSuveeSelectedCount();
    }
    
    function closeSuveeRelatedProductsModal() {
        const modal = document.getElementById('suvee-related-products-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore background scroll
        document.getElementById('suvee-search-results-list').innerHTML = `
            <div class="text-center py-10">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-gray-500 font-medium">Start typing to search for products</p>
                <p class="text-sm text-gray-400 mt-1">Results will appear here</p>
            </div>
        `;
        document.getElementById('suvee-product-search').value = '';
        document.getElementById('suvee-selected-count').classList.add('hidden');
        document.getElementById('suvee-add-products-btn').disabled = true;
    }
    
    // Update selected count display
    function updateSuveeSelectedCount() {
        const checkboxes = document.querySelectorAll('#suvee-search-results-list input[type="checkbox"]:checked:not(:disabled)');
        const count = checkboxes.length;
        const countElement = document.getElementById('suvee-selected-number');
        const selectedCountDiv = document.getElementById('suvee-selected-count');
        const addBtn = document.getElementById('suvee-add-products-btn');
        
        if (countElement) countElement.textContent = count;
        if (selectedCountDiv) {
            if (count > 0) {
                selectedCountDiv.classList.remove('hidden');
            } else {
                selectedCountDiv.classList.add('hidden');
            }
        }
        if (addBtn) {
            addBtn.disabled = count === 0;
        }
    }
    
    // Search products with debounce
    document.getElementById('suvee-product-search').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchSuveeProducts(e.target.value);
        }, 500);
    });
    
    async function searchSuveeProducts(query) {
        if (query.length < 2) {
            document.getElementById('suvee-search-results-list').innerHTML = `
                <div class="text-center py-10">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">Enter at least 2 characters to search</p>
                    <p class="text-sm text-gray-400 mt-1">Type product name or SKU</p>
                </div>
            `;
            document.getElementById('suvee-selected-count').classList.add('hidden');
            document.getElementById('suvee-total-results').textContent = '0';
            return;
        }
        
        // Show loading state
        document.getElementById('suvee-search-results-list').innerHTML = `
            <div class="text-center py-10">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-brand-gold border-t-transparent"></div>
                <p class="text-gray-500 font-medium mt-4">Searching products...</p>
            </div>
        `;
        
        try {
            const response = await fetch(`{{ route('admin.products.search') }}?search=${encodeURIComponent(query)}&exclude={{ $product->id }}`);
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.success) {
                if (data.data.length === 0) {
                    document.getElementById('suvee-search-results-list').innerHTML = `
                        <div class="text-center py-10">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">No products found</p>
                            <p class="text-sm text-gray-400 mt-1">Try different search terms</p>
                        </div>
                    `;
                    document.getElementById('suvee-total-results').textContent = '0';
                    return;
                }
                
                // Update total results count
                document.getElementById('suvee-total-results').textContent = data.data.length;
                
                let html = '';
                data.data.forEach(product => {
                    const isSelected = selectedProducts.has(product.id);
                    const imageUrl = product.main_image 
                        ? `/storage/${product.main_image}`
                        : '/images/placeholder-product.png';
                    
                    html += `
                        <div class="suvee-product-item group flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl hover:border-brand-gold hover:shadow-md transition-all duration-300 cursor-pointer" onclick="toggleSuveeProductCheckbox(${product.id})">
                            <div class="flex items-center space-x-4">
                                <input type="checkbox" 
                                       id="suvee-product-${product.id}" 
                                       value="${product.id}"
                                       ${isSelected ? 'checked disabled' : ''}
                                       onchange="updateSuveeSelectedCount()"
                                       onclick="event.stopPropagation()">
                                <div>
                                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <img src="${imageUrl}" 
                                     alt="${product.name}" 
                                     class="w-14 h-14 object-cover rounded-lg border border-gray-300">
                                <div class="flex-1 min-w-0">
                                    <label for="suvee-product-${product.id}" class="block font-semibold text-gray-900 truncate cursor-pointer hover:text-brand-gold transition-colors" onclick="event.stopPropagation()">${product.name}</label>
                                    <div class="flex items-center space-x-3 mt-1.5">
                                        ${product.sku ? `<span class="text-sm text-gray-600 bg-gray-100 px-2.5 py-1 rounded">SKU: ${product.sku}</span>` : ''}
                                        <span class="text-xs text-gray-500">${product.category_name}</span>
                                    </div>
                                </div>
                            </div>
                            ${isSelected ? `
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Added
                                </span>
                            ` : ''}
                        </div>
                    `;
                });
                document.getElementById('suvee-search-results-list').innerHTML = html;
                updateSuveeSelectedCount();
            }
        } catch (error) {
            console.error('Error searching products:', error);
            document.getElementById('suvee-search-results-list').innerHTML = `
                <div class="text-center py-10">
                    <svg class="w-16 h-16 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-500 font-medium">Error searching products</p>
                    <p class="text-sm text-red-400 mt-1">Please try again later</p>
                </div>
            `;
            document.getElementById('suvee-total-results').textContent = '0';
        }
    }
    
    function toggleSuveeProductCheckbox(productId) {
        const checkbox = document.getElementById(`suvee-product-${productId}`);
        if (checkbox && !checkbox.disabled) {
            checkbox.checked = !checkbox.checked;
            updateSuveeSelectedCount();
        }
    }
    
    function addSuveeSelectedProducts() {
        const checkboxes = document.querySelectorAll('#suvee-search-results-list input[type="checkbox"]:checked:not(:disabled)');
        
        checkboxes.forEach(checkbox => {
            const productId = parseInt(checkbox.value);
            if (!selectedProducts.has(productId)) {
                selectedProducts.add(productId);
                
                // Get product info from the checkbox's parent
                const parentDiv = checkbox.closest('.suvee-product-item');
                const productName = parentDiv.querySelector('label').textContent;
                const skuElement = parentDiv.querySelector('.text-sm');
                const productSku = skuElement ? skuElement.textContent.replace('SKU: ', '') : 'N/A';
                const imageSrc = parentDiv.querySelector('img').src;
                
                // Add to UI
                addSuveeRelatedProductToUI(productId, productName, productSku, imageSrc);
            }
        });
        
        // Update hidden field
        updateRelatedProductsField();
        closeSuveeRelatedProductsModal();
    }
    
    function addSuveeRelatedProductToUI(id, name, sku, imageUrl) {
        const container = document.getElementById('related-products-container');
        
        // Remove "no products" message if exists
        const noProductsMsg = container.querySelector('.text-center');
        if (noProductsMsg) {
            noProductsMsg.remove();
        }
        
        const productDiv = document.createElement('div');
        productDiv.className = 'flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 related-product-item';
        productDiv.setAttribute('data-id', id);
        productDiv.innerHTML = `
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <img src="${imageUrl}" alt="${name}" class="w-14 h-14 object-cover rounded-lg border border-gray-300">
                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-brand-gold rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">${name}</p>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="text-sm text-gray-600 bg-gray-100 px-2 py-0.5 rounded">SKU: ${sku}</span>
                        <span class="text-xs text-brand-gold bg-brand-gold/10 px-2 py-0.5 rounded">₹0.00</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="removeRelatedProduct(${id})" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        
        container.appendChild(productDiv);
    }
    
    function removeRelatedProduct(productId) {
        selectedProducts.delete(productId);
        
        // Remove from UI
        const element = document.querySelector(`.related-product-item[data-id="${productId}"]`);
        if (element) {
            element.remove();
        }
        
        // Show "no products" message if container is empty
        const container = document.getElementById('related-products-container');
        if (container.children.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-xl bg-gradient-to-b from-gray-50 to-white">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">No related products added yet</p>
                    <p class="text-sm text-gray-400 mt-1">Click "Add Related Products" to get started</p>
                </div>
            `;
        }
        
        // Update hidden field
        updateRelatedProductsField();
    }
    
    function updateRelatedProductsField() {
        document.getElementById('related_products').value = Array.from(selectedProducts).join(',');
    }
    
    // Track attributes to delete
    let attributesToDelete = [];
    
    function markAttributeForRemoval(attributeId) {
        if (!attributesToDelete.includes(attributeId)) {
            attributesToDelete.push(attributeId);
        }
        
        document.getElementById('attributes_to_delete').value = attributesToDelete.join(',');
        
        const element = document.querySelector(`.attribute-item[data-id="${attributeId}"]`);
        if (element) {
            element.style.opacity = '0.5';
            element.style.textDecoration = 'line-through';
            element.style.backgroundColor = '#fef2f2';
            
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
            
            const mainImageContainer = document.querySelector('div[onclick="removeMainImage()"]').closest('.group');
            mainImageContainer.style.display = 'none';
            document.getElementById('main-image-removed').classList.remove('hidden');
        }
    }
    
    // Handle gallery image removal
    function removeGalleryImage(imageId) {
        if (confirm('Are you sure you want to remove this gallery image?')) {
            if (!removedGalleryImages.includes(imageId)) {
                removedGalleryImages.push(imageId);
            }
            
            document.getElementById('removed_gallery_images').value = removedGalleryImages.join(',');
            
            const imageElement = document.querySelector(`.gallery-image-item[data-id="${imageId}"]`);
            if (imageElement) {
                imageElement.style.opacity = '0.5';
                imageElement.style.transform = 'scale(0.95)';
                imageElement.style.transition = 'all 0.3s ease';
                
                const removeBtn = imageElement.querySelector('button');
                if (removeBtn) {
                    removeBtn.innerHTML = 'Removed';
                    removeBtn.disabled = true;
                    removeBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                    removeBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                }
                
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
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSuveeRelatedProductsModal();
        }
    });
    
    // Prevent modal backdrop from interfering
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('suvee-related-products-modal');
        if (e.target === modal) {
            closeSuveeRelatedProductsModal();
        }
    });
</script>

<style>
    /* SUVEE MODAL UNIQUE STYLES - NO CONFLICTS */
    
    /* Modal overlay with highest z-index */
    .suvee-modal-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 999999 !important;
        background: rgba(0, 0, 0, 0.6);
    }
    
    .suvee-modal-overlay.hidden {
        display: none !important;
    }
    
    .suvee-modal-overlay:not(.hidden) {
        display: block !important;
    }
    
    /* Modal container for centering */
    .suvee-modal-container {
        position: relative;
        z-index: 999999 !important;
    }
    
    /* Modal content with proper positioning */
    .suvee-modal-content {
        position: relative !important;
        z-index: 999999 !important;
        margin: auto !important;
        width: 95vw !important;
        max-width: 42rem !important;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    @media (min-width: 1024px) {
        .suvee-modal-content {
            max-width: 56rem !important;
        }
    }
    
    @media (max-width: 640px) {
        .suvee-modal-content {
            width: 100vw !important;
            max-width: 100vw !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }
        
        .suvee-modal-container {
            padding: 0 !important;
        }
        
        .suvee-modal-header {
            padding: 1rem !important;
        }
        
        .suvee-modal-body {
            padding: 1rem !important;
        }
        
        .suvee-modal-footer {
            padding: 1rem !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }
        
        .suvee-footer-buttons {
            flex-direction: column !important;
            width: 100% !important;
        }
        
        .suvee-cancel-btn,
        .suvee-add-btn {
            width: 100% !important;
        }
    }
    
    /* Modal backdrop */
    .suvee-modal-backdrop {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 1 !important;
    }
    
    /* Modal header styles */
    .suvee-modal-header {
        background: linear-gradient(to right, #ffffff, #f9fafb);
        border-bottom: 1px solid #e5e7eb;
    }
    
    .suvee-modal-title {
        color: #111827;
        font-weight: 700;
        margin: 0;
    }
    
    .suvee-modal-subtitle {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0;
    }
    
    .suvee-modal-close-btn {
        background: transparent;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 10;
    }
    
    .suvee-modal-close-btn:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    /* Search container styles */
    .suvee-search-container {
        position: relative;
    }
    
    .suvee-search-input {
        width: 100%;
        padding-left: 3rem !important;
        padding-right: 1rem !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        border: 2px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        font-size: 1rem !important;
        line-height: 1.5rem !important;
        transition: all 0.3s ease !important;
        min-height: 56px;
    }
    
    .suvee-search-input:focus {
        border-color: var(--brand-gold) !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1) !important;
        outline: none !important;
    }
    
    .suvee-search-container .absolute.inset-y-0.left-0.pl-3 {
        left: 0.75rem !important;
        padding-left: 0 !important;
    }
    
    .suvee-search-icon {
        color: #9ca3af;
        width: 1.25rem;
        height: 1.25rem;
    }
    
    .suvee-search-hint {
        color: #9ca3af;
        font-size: 0.75rem;
        margin-top: 0.5rem;
    }
    
    /* Selected count styles */
    .suvee-selected-count {
        margin-bottom: 1rem;
    }
    
    .suvee-selected-count.hidden {
        display: none !important;
    }
    
    /* Search results styles */
    .suvee-search-results {
        margin-bottom: 1.5rem;
    }
    
    .suvee-results-title {
        color: #374151;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }
    
    .suvee-results-list {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }
    
    /* Custom scrollbar for search results */
    .suvee-results-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .suvee-results-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .suvee-results-list::-webkit-scrollbar-thumb {
        background: var(--brand-gold);
        border-radius: 10px;
    }
    
    .suvee-results-list::-webkit-scrollbar-thumb:hover {
        background: var(--brand-amber);
    }
    
    /* Product item styles */
    .suvee-product-item {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .suvee-product-item:hover {
        border-color: var(--brand-gold);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    /* Checkbox fix */
    .suvee-product-item input[type="checkbox"] {
        position: absolute !important;
        opacity: 0 !important;
        cursor: pointer !important;
        height: 1.25rem !important;
        width: 1.25rem !important;
        top: 1rem !important;
        left: 1rem !important;
        z-index: 10 !important;
    }
    
    .suvee-product-item input[type="checkbox"] + div {
        width: 1.25rem !important;
        height: 1.25rem !important;
        border: 2px solid #d1d5db !important;
        border-radius: 0.25rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        position: absolute !important;
        top: 1rem !important;
        left: 1rem !important;
        z-index: 5 !important;
        pointer-events: none !important;
    }
    
    .suvee-product-item input[type="checkbox"]:checked + div {
        background: var(--brand-gold) !important;
        border-color: var(--brand-gold) !important;
    }
    
    .suvee-product-item input[type="checkbox"]:checked + div svg {
        display: block !important;
    }
    
    .suvee-product-item input[type="checkbox"] + div svg {
        display: none !important;
        width: 0.875rem !important;
        height: 0.875rem !important;
        color: white !important;
    }
    
    /* Make product info area clickable */
    .suvee-product-item .flex.items-center.space-x-4 {
        margin-left: 2.5rem !important;
        cursor: pointer !important;
    }
    
    .suvee-product-item .flex.items-center.space-x-4 > div:first-child {
        margin-left: 0 !important;
    }
    
    /* Modal footer styles */
    .suvee-modal-footer {
        border-top: 1px solid #e5e7eb;
        padding-top: 1.5rem;
    }
    
    .suvee-results-counter {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .suvee-footer-buttons {
        display: flex;
        gap: 0.75rem;
    }
    
    /* Button styles - NO CONFLICTS */
    .suvee-cancel-btn {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
        color: #475569 !important;
        border: 2px solid #e2e8f0 !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
        text-decoration: none !important;
        position: relative;
        z-index: 10;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        overflow: hidden;
    }
    
    .suvee-cancel-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
    }
    
    .suvee-cancel-btn:hover {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
        border-color: #cbd5e1 !important;
        color: #334155 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    
    .suvee-cancel-btn:hover::before {
        left: 100%;
    }
    
    .suvee-cancel-btn:active {
        transform: translateY(0) !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Form cancel button - matching design */
    .suvee-form-cancel-btn {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
        color: #475569 !important;
        border: 2px solid #e2e8f0 !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 0.75rem !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        position: relative;
        overflow: hidden;
    }
    
    .suvee-form-cancel-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
    }
    
    .suvee-form-cancel-btn:hover {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
        border-color: #cbd5e1 !important;
        color: #334155 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    
    .suvee-form-cancel-btn:hover::before {
        left: 100%;
    }
    
    .suvee-form-cancel-btn:active {
        transform: translateY(0) !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
    }
    
    .suvee-add-btn {
        background: linear-gradient(to right, var(--brand-gold), var(--brand-amber)) !important;
        color: white !important;
        border: none !important;
        padding: 0.625rem 1.25rem !important;
        border-radius: 0.5rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
        position: relative;
        z-index: 10;
    }
    
    .suvee-add-btn:hover:not(:disabled) {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-1px) !important;
    }
    
    .suvee-add-btn:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
        transform: none !important;
        box-shadow: none !important;
    }
    
    /* Add Related Products button - FIXED Z-INDEX */
    .suvee-add-related-btn {
        background: linear-gradient(to right, var(--brand-gold), var(--brand-amber)) !important;
        color: white !important;
        border: none !important;
        padding: 0.625rem 1.25rem !important;
        border-radius: 0.5rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
        position: relative !important;
        z-index: 1 !important;
    }
    
    .suvee-add-related-btn:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-1px) !important;
    }
    
    /* Modal animation */
    .suvee-modal-content {
        animation: suveeModalSlideIn 0.3s ease-out;
    }
    
    @keyframes suveeModalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    /* Fix for button appearing over modal */
    .suvee-modal-overlay:not(.hidden) ~ * .suvee-add-related-btn {
        z-index: 1 !important;
        pointer-events: none !important;
    }
    
    .suvee-modal-overlay:not(.hidden) ~ * .suvee-add-related-btn:hover {
        box-shadow: none !important;
        transform: none !important;
    }
</style>
@endsection