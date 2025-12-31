@extends('layouts.admin')

@section('title', 'Add Variation - ' . $product->name)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-heading">Add Product Variation</h1>
            <p class="text-text-muted mt-1">Create a new variation for: {{ $product->name }}</p>
        </div>
        <a href="{{ route('admin.products.variations.index', $product) }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Variations
        </a>
    </div>

    <!-- Product Info -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4">
            @if($product->main_image)
                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" 
                     class="w-16 h-16 object-cover rounded-lg border border-gray-200">
            @else
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
            <div>
                <h3 class="text-lg font-semibold text-text-heading">{{ $product->name }}</h3>
                <p class="text-text-muted text-sm">Base Price: ₹{{ number_format($product->final_price, 2) }}</p>
                <p class="text-text-muted text-sm">Base Stock: {{ $product->stock_quantity }}</p>
            </div>
        </div>
    </div>

    <!-- Variation Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.products.variations.store', $product) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Basic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sku" class="block text-sm font-medium text-text-heading mb-2">SKU (Optional)</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="Leave blank to auto-generate">
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-text-heading mb-2">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="0">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Variation Attributes -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    Variation Attributes
                    <span class="text-sm text-gray-500 font-normal">(Select one value for each attribute)</span>
                </h3>
                @if($attributes->count() > 0)
                    <div class="space-y-4">
                        @foreach($attributes as $attribute)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-text-heading mb-3">
                                    {{ $attribute->name }}
                                    @if($attribute->description)
                                        <span class="text-xs text-gray-500 ml-2">({{ $attribute->description }})</span>
                                    @endif
                                </label>
                                @if($attribute->activeValues->count() > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach($attribute->activeValues as $value)
                                            <label class="cursor-pointer">
                                                <input type="radio" 
                                                       name="attribute_values[{{ $attribute->id }}]" 
                                                       value="{{ $value->id }}" 
                                                       class="sr-only peer"
                                                       required>
                                                <div class="relative rounded-lg border-2 border-gray-200 p-3 text-center peer-checked:border-brand-gold peer-checked:bg-brand-gold/10 peer-checked:ring-2 peer-checked:ring-brand-gold/20 transition-all hover:border-gray-300">
                                                    @if($value->hex_color)
                                                        <div class="flex flex-col items-center space-y-2">
                                                            <div class="w-8 h-8 rounded-full border-2 border-gray-300" 
                                                                 style="background-color: {{ $value->hex_color }}"></div>
                                                            <span class="text-xs font-medium">{{ $value->value }}</span>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center justify-center">
                                                            <span class="text-sm font-medium">{{ $value->value }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="absolute top-1 right-1 w-5 h-5 bg-brand-gold rounded-full flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-all">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Select one {{ $attribute->name }} option</p>
                                @else
                                    <p class="text-sm text-gray-500">No active values available for this attribute.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Selected Attributes Preview -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-text-heading mb-2">Selected Combination:</h4>
                        <div id="selected-attributes-preview" class="text-sm text-gray-600">
                            <span class="text-gray-400">Select attributes above to see the combination</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-600 font-medium">No Attributes Available</p>
                        <p class="text-sm text-gray-500 mt-1">Please create product attributes first from the admin sidebar.</p>
                        <a href="{{ route('admin.product-attributes.index') }}" class="inline-block mt-3 text-brand-gold hover:text-brand-amber font-medium">
                            Manage Attributes →
                        </a>
                    </div>
                @endif
            </div>
            @error('attributes')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Pricing -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pricing (Optional - Leave blank to use product pricing)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-text-heading mb-2">Selling Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">₹</span>
                            <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" 
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                                   placeholder="0.00">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Leave blank to use product price (₹{{ number_format($product->final_price, 2) }})</p>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="compare_price" class="block text-sm font-medium text-text-heading mb-2">Compare Price (MRP)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">₹</span>
                            <input type="number" id="compare_price" name="compare_price" step="0.01" min="0" value="{{ old('compare_price') }}" 
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                                   placeholder="0.00">
                        </div>
                        @error('compare_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Stock & Weight -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Stock & Weight (Optional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-text-heading mb-2">Stock Quantity</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="{{ old('stock_quantity') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="0">
                        <p class="mt-1 text-xs text-gray-500">Leave blank to use product stock ({{ $product->stock_quantity }})</p>
                        @error('stock_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="weight" class="block text-sm font-medium text-text-heading mb-2">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" step="0.01" min="0" value="{{ old('weight') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="0.00">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Variation Images -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Variation Images (Optional)
                </h3>
                
                <!-- Image Selection Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-text-heading mb-2">Image Selection Type</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="image_type" value="upload" checked class="mr-2" onchange="toggleImageType()">
                            <span class="text-sm">Upload New Image</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="image_type" value="gallery" class="mr-2" onchange="toggleImageType()">
                            <span class="text-sm">Select from Gallery</span>
                        </label>
                    </div>
                </div>

                <!-- Upload Option -->
                <div id="upload-section" class="space-y-4">
                    <div>
                        <label for="variation_image" class="block text-sm font-medium text-text-heading mb-2">Upload Image</label>
                        <input type="file" id="variation_image" name="variation_image" accept="image/*" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Allowed formats: JPG, PNG, GIF (Max 2MB)</p>
                    </div>
                    <div id="image-preview" class="hidden">
                        <img id="preview-img" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                    </div>
                </div>

                <!-- Gallery Selection Option -->
                <div id="gallery-section" class="hidden space-y-4">
                    @if($product->images->count() > 0)
                        <label class="block text-sm font-medium text-text-heading mb-2">Select from Product Gallery</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($product->images as $image)
                                <label class="cursor-pointer">
                                    <input type="radio" name="gallery_image_id" value="{{ $image->id }}" class="sr-only peer">
                                    <div class="relative rounded-lg overflow-hidden border-2 border-gray-200 peer-checked:border-brand-gold peer-checked:ring-2 peer-checked:ring-brand-gold transition-all">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery Image" 
                                             class="w-full h-24 object-cover">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 peer-checked:bg-opacity-10 transition-all"></div>
                                        <div class="absolute top-2 right-2 w-6 h-6 bg-brand-gold rounded-full flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-all">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">No Gallery Images Available</p>
                            <p class="text-sm text-gray-500 mt-1">This product has no gallery images to select from.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Status
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_default" name="is_default" value="1" 
                               class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                        <label for="is_default" class="ml-2 text-sm text-text-heading">Set as Default Variation</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                               class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                        <label for="is_active" class="ml-2 text-sm text-text-heading">Active</label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.products.variations.index', $product) }}" 
                   class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                    Create Variation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle image selection type
function toggleImageType() {
    const uploadSection = document.getElementById('upload-section');
    const gallerySection = document.getElementById('gallery-section');
    const imageType = document.querySelector('input[name="image_type"]:checked').value;
    
    if (imageType === 'upload') {
        uploadSection.classList.remove('hidden');
        gallerySection.classList.add('hidden');
    } else {
        uploadSection.classList.add('hidden');
        gallerySection.classList.remove('hidden');
    }
}

// Update selected attributes preview
function updateSelectedAttributesPreview() {
    const preview = document.getElementById('selected-attributes-preview');
    const selectedOptions = [];
    
    // Get all selected radio buttons
    const selectedRadios = document.querySelectorAll('input[name^="attribute_values"]:checked');
    
    selectedRadios.forEach(radio => {
        const label = radio.closest('label');
        const valueText = label.querySelector('span').textContent.trim();
        const attributeName = radio.name.replace('attribute_values[', '').replace(']', '');
        
        // Find the attribute name from the DOM
        const attributeContainer = radio.closest('.border-gray-200');
        const attributeNameElement = attributeContainer.querySelector('label.font-medium');
        const attributeTitle = attributeNameElement.textContent.trim().split(' ')[0];
        
        selectedOptions.push(`${attributeTitle}: ${valueText}`);
    });
    
    if (selectedOptions.length > 0) {
        preview.innerHTML = `
            <div class="flex flex-wrap gap-2">
                ${selectedOptions.map(option => 
                    `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-brand-gold text-white">
                        ${option}
                    </span>`
                ).join('')}
            </div>
        `;
    } else {
        preview.innerHTML = '<span class="text-gray-400">Select attributes above to see the combination</span>';
    }
}

// Image preview for upload
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('variation_image');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        });
    }
    
    // Add event listeners to attribute radio buttons
    const attributeRadios = document.querySelectorAll('input[name^="attribute_values"]');
    attributeRadios.forEach(radio => {
        radio.addEventListener('change', updateSelectedAttributesPreview);
    });
    
    // Initialize preview
    updateSelectedAttributesPreview();
});
</script>
@endsection
