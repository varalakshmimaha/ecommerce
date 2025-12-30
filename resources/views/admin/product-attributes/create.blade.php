@extends('layouts.admin')

@section('title', 'Create Product Attribute')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Product Attribute</h2>

        @if($errors->any())
            <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.product-attributes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Value *</label>
                <input type="text" name="attribute_value" value="{{ old('attribute_value') }}" required class="input-field" placeholder="e.g., Red, Large, Premium">
                @error('attribute_value')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                <select name="attribute_name" required class="input-field">
                    <option value="">Select Type</option>
                    <option value="color" {{ old('attribute_name') === 'color' ? 'selected' : '' }}>Color</option>
                    <option value="size" {{ old('attribute_name') === 'size' ? 'selected' : '' }}>Size</option>
                    <option value="other" {{ old('attribute_name') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('attribute_name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (optional)</label>
                <input type="number" step="0.01" name="price_adjustment" value="{{ old('price_adjustment', 0) }}" class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment (optional)</label>
                <input type="number" name="stock_adjustment" value="{{ old('stock_adjustment', 0) }}" class="input-field">
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Create Attribute</button>
                <a href="{{ route('admin.product-attributes.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
