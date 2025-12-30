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
        <form method="POST" action="{{ route('admin.products.variations.store', $product) }}" class="space-y-6">
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
                </h3>
                <div id="attributes-container" class="space-y-4">
                    <div class="attribute-row grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-2">Attribute Name</label>
                            <input type="text" name="attribute_names[]" placeholder="e.g., Color, Size" 
                                   class="attribute-name w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-2">Attribute Value</label>
                            <input type="text" name="attribute_values[]" placeholder="e.g., Red, Large" 
                                   class="attribute-value w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="removeAttribute(this)" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addAttribute()" class="mt-4 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-all duration-300">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Attribute
                </button>
                @error('attributes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

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
                <div id="variation-images" class="space-y-4">
                    <div class="image-row grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-2">Image URL</label>
                            <input type="text" name="images[]" placeholder="Enter image URL" 
                                   class="variation-image w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="removeImage(this)" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addImage()" class="mt-4 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-all duration-300">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Image
                </button>
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
function addAttribute() {
    const container = document.getElementById('attributes-container');
    const newRow = document.createElement('div');
    newRow.className = 'attribute-row grid grid-cols-1 md:grid-cols-3 gap-4';
    newRow.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-text-heading mb-2">Attribute Name</label>
            <input type="text" name="attribute_names[]" placeholder="e.g., Color, Size" 
                   class="attribute-name w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-medium text-text-heading mb-2">Attribute Value</label>
            <input type="text" name="attribute_values[]" placeholder="e.g., Red, Large" 
                   class="attribute-value w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
        </div>
        <div class="flex items-end">
            <button type="button" onclick="removeAttribute(this)" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeAttribute(button) {
    const row = button.closest('.attribute-row');
    if (document.querySelectorAll('.attribute-row').length > 1) {
        row.remove();
    }
}

function addImage() {
    const container = document.getElementById('variation-images');
    const newRow = document.createElement('div');
    newRow.className = 'image-row grid grid-cols-1 md:grid-cols-2 gap-4';
    newRow.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-text-heading mb-2">Image URL</label>
            <input type="text" name="images[]" placeholder="Enter image URL" 
                   class="variation-image w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
        </div>
        <div class="flex items-end">
            <button type="button" onclick="removeImage(this)" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeImage(button) {
    const row = button.closest('.image-row');
    if (document.querySelectorAll('.image-row').length > 1) {
        row.remove();
    }
}

// Form submission - build attributes object
document.querySelector('form').addEventListener('submit', function(e) {
    const attributeNames = document.querySelectorAll('.attribute-name');
    const attributeValues = document.querySelectorAll('.attribute-value');
    const attributes = {};
    
    attributeNames.forEach((input, index) => {
        const name = input.value.trim();
        const value = attributeValues[index].value.trim();
        if (name && value) {
            attributes[name] = value;
        }
    });
    
    // Add hidden input for attributes
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'attributes';
    hiddenInput.value = JSON.stringify(attributes);
    this.appendChild(hiddenInput);
    
    // Remove individual attribute inputs
    attributeNames.forEach(input => input.closest('.attribute-row').remove());
});
</script>
@endsection
