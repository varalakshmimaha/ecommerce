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
    
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
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
                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg mb-2">
            @endif
            <input type="file" name="main_image" accept="image/*" class="input-field">
            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images</label>
            @if($product->images->count() > 0)
                <div class="grid grid-cols-4 gap-2 mb-4">
                    @foreach($product->images as $image)
                        <div>
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery" class="w-full h-24 object-cover rounded-lg">
                        </div>
                    @endforeach
                </div>
            @endif
            <input type="file" name="gallery_images[]" accept="image/*" multiple class="input-field">
            <p class="text-xs text-gray-500 mt-1">Add more gallery images</p>
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
                <h3 class="text-lg font-semibold text-gray-900">Product Attributes (Color, Size, etc.)</h3>
                <a href="{{ route('admin.product-attributes.create') }}?product_id={{ $product->id }}" class="text-sm text-[#D4AF37] hover:text-[#D4AF37]">
                    Manage Attributes Separately
                </a>
            </div>
            @if($product->attributes->count() > 0)
                <div class="space-y-2 mb-4">
                    @foreach($product->attributes as $attr)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <div class="flex-1">
                                <span class="font-medium">{{ ucfirst($attr->attribute_name) }}:</span>
                                <span>{{ $attr->attribute_value }}</span>
                                @if($attr->price_adjustment != 0)
                                    <span class="text-sm text-gray-600">(₹{{ $attr->price_adjustment }})</span>
                                @endif
                            </div>
                            <form action="{{ route('admin.product-attributes.destroy', $attr) }}" method="POST" class="inline" onsubmit="return confirm('Delete this attribute?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm mb-4">No attributes added yet.</p>
            @endif
            <div id="attributes-container" class="space-y-4">
                <div class="attribute-row grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Type</label>
                        <select name="new_attributes[0][name]" class="input-field">
                            <option value="">Select Type</option>
                            <option value="color">Color</option>
                            <option value="size">Size</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                        <input type="text" name="new_attributes[0][value]" class="input-field" placeholder="e.g., Red, XL, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (₹)</label>
                        <input type="number" step="0.01" name="new_attributes[0][price_adjustment]" value="0" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment</label>
                        <input type="number" name="new_attributes[0][stock_adjustment]" value="0" class="input-field">
                    </div>
                </div>
            </div>
            <button type="button" onclick="addAttributeRow()" class="mt-4 text-[#D4AF37] hover:text-[#D4AF37] font-medium">
                + Add Another Attribute
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
        
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Product</button>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('full_description');

    let attributeIndex = 1;
    function addAttributeRow() {
        const container = document.getElementById('attributes-container');
        const newRow = document.createElement('div');
        newRow.className = 'attribute-row grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg';
        newRow.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Type</label>
                <select name="new_attributes[${attributeIndex}][name]" class="input-field">
                    <option value="">Select Type</option>
                    <option value="color">Color</option>
                    <option value="size">Size</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                <input type="text" name="new_attributes[${attributeIndex}][value]" class="input-field" placeholder="e.g., Red, XL, etc.">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (₹)</label>
                <input type="number" step="0.01" name="new_attributes[${attributeIndex}][price_adjustment]" value="0" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment</label>
                <input type="number" name="new_attributes[${attributeIndex}][stock_adjustment]" value="0" class="input-field">
            </div>
            <div class="flex items-end">
                <button type="button" onclick="this.closest('.attribute-row').remove()" class="text-red-600 hover:text-red-700 font-medium">Remove</button>
            </div>
        `;
        container.appendChild(newRow);
        attributeIndex++;
    }
</script>
@endsection

