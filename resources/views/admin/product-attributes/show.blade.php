@extends('layouts.admin')

@section('title', 'Product Attribute - ' . $attribute->name)

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Product Attribute Details</h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.product-attributes.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Back to Attributes
            </a>
            <a href="{{ route('admin.product-attributes.edit', $attribute) }}" 
               class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                <i class="fas fa-edit mr-2"></i> Edit Attribute
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="text-sm text-gray-900">{{ $attribute->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="text-sm text-gray-900">
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $attribute->slug }}</code>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="text-sm text-gray-900">{{ $attribute->description ?: 'No description provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                        <dd class="text-sm text-gray-900">{{ $attribute->sort_order }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="text-sm text-gray-900">
                            @if($attribute->is_active)
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Values</dt>
                        <dd class="text-sm text-gray-900">{{ $attribute->values_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Active Values</dt>
                        <dd class="text-sm text-gray-900">{{ $attribute->active_values_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="text-sm text-gray-900">{{ $attribute->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="text-sm text-gray-900">{{ $attribute->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Attribute Values</h3>
                <a href="{{ route('admin.product-attribute-values.create') }}" 
                   class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg text-sm font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-plus mr-2"></i> Add Value
                </a>
            </div>

            @if($attribute->values->count() > 0)
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attribute->values as $value)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $value->value }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($value->hex_color)
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $value->hex_color }}"></div>
                                                <span class="text-xs text-gray-600">{{ $value->hex_color }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $value->sort_order }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($value->is_active)
                                            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.product-attribute-values.edit', $value) }}" 
                                               class="text-brand-gold hover:text-blue-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.product-attribute-values.destroy', $value) }}" 
                                                  method="POST" class="inline-block" 
                                                  onsubmit="return confirm('Are you sure you want to delete this attribute value?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <p class="text-gray-500 mb-4">No attribute values found.</p>
                    <a href="{{ route('admin.product-attribute-values.create') }}" 
                       class="text-brand-gold hover:text-blue-900 font-medium">
                        Create the first value
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
