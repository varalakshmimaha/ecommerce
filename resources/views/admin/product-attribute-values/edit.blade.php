@extends('layouts.admin')

@section('title', 'Edit Product Attribute Value')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Edit Product Attribute Value</h2>
        <a href="{{ route('admin.product-attribute-values.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Attribute Values
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.product-attribute-values.update', $productAttributeValue) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if($errors->any())
                <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="product_attribute_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Attribute <span class="text-red-500">*</span>
                    </label>
                    <select id="product_attribute_id" 
                            name="product_attribute_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                            required>
                        <option value="">Select an attribute</option>
                        @foreach($attributes as $attribute)
                            <option value="{{ $attribute->id }}" {{ old('product_attribute_id', $productAttributeValue->product_attribute_id) == $attribute->id ? 'selected' : '' }}>
                                {{ $attribute->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">
                        Select the attribute this value belongs to
                    </p>
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                        Value <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="value" 
                           name="value" 
                           value="{{ old('value', $productAttributeValue->value) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                           placeholder="e.g., Red, Large, Premium"
                           required>
                    <p class="mt-1 text-sm text-gray-500">
                        The actual value (e.g., Red, Large, Premium)
                    </p>
                </div>

                <div>
                    <label for="hex_color" class="block text-sm font-medium text-gray-700 mb-2">
                        Hex Color
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="color" 
                               id="hex_color_preview" 
                               value="{{ old('hex_color', $productAttributeValue->hex_color ?? '#000000') }}"
                               class="w-12 h-12 border border-gray-300 rounded cursor-pointer"
                               onchange="document.getElementById('hex_color').value = this.value">
                        <input type="text" 
                               id="hex_color" 
                               name="hex_color" 
                               value="{{ old('hex_color', $productAttributeValue->hex_color) }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="#FF0000"
                               pattern="^#[0-9A-Fa-f]{6}$"
                               onchange="document.getElementById('hex_color_preview').value = this.value">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Optional: Hex color code for color attributes (e.g., #FF0000)
                    </p>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Sort Order
                    </label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="{{ old('sort_order', $productAttributeValue->sort_order ?? 0) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                           placeholder="0">
                    <p class="mt-1 text-sm text-gray-500">
                        Order in which this value appears (0 = first)
                    </p>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $productAttributeValue->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">
                        Whether this attribute value is available for use
                    </p>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.product-attribute-values.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white rounded-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Update Attribute Value
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sync color picker and text input
document.getElementById('hex_color_preview').addEventListener('input', function() {
    document.getElementById('hex_color').value = this.value;
});
</script>
@endpush
