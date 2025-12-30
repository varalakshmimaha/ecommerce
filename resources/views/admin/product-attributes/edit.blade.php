@extends('layouts.admin')

@section('title', 'Edit Product Attribute')

@section('content')
<div class="max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Product Attribute</h2>

    @if($errors->any())
        <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.product-attributes.update', $productAttribute) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Value *</label>
            <input type="text" name="attribute_value" value="{{ old('attribute_value', $productAttribute->attribute_value) }}" required class="input-field">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
            <select name="attribute_name" required class="input-field">
                <option value="color" {{ old('attribute_name', $productAttribute->attribute_name) === 'color' ? 'selected' : '' }}>Color</option>
                <option value="size" {{ old('attribute_name', $productAttribute->attribute_name) === 'size' ? 'selected' : '' }}>Size</option>
                <option value="other" {{ old('attribute_name', $productAttribute->attribute_name) === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (optional)</label>
            <input type="number" step="0.01" name="price_adjustment" value="{{ old('price_adjustment', $productAttribute->price_adjustment) }}" class="input-field">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment (optional)</label>
            <input type="number" name="stock_adjustment" value="{{ old('stock_adjustment', $productAttribute->stock_adjustment) }}" class="input-field">
        </div>

        <div class="flex gap-4 pt-6">
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Attribute</button>
            <a href="{{ route('admin.product-attributes.index') }}" class="btn">Cancel</a>
        </div>
    </form>
</div>
@endsection
