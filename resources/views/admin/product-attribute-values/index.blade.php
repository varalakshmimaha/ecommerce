@extends('layouts.admin')

@section('title', 'Product Attribute Values')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Product Attribute Values</h2>
        <a href="{{ route('admin.product-attribute-values.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Add Attribute Value</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b-2 border-gray-300">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Value</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Attribute</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Slug</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hex Color</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sort Order</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attributeValues as $attributeValue)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $attributeValue->value }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $attributeValue->attribute->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $attributeValue->slug }}</code>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($attributeValue->hex_color)
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $attributeValue->hex_color }}"></div>
                                    <span class="text-xs text-gray-600">{{ $attributeValue->hex_color }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $attributeValue->sort_order }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($attributeValue->is_active)
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.product-attribute-values.edit', $attributeValue) }}" 
                                   class="text-brand-gold hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="text-{{ $attributeValue->is_active ? 'gray' : 'green' }}-600 hover:text-{{ $attributeValue->is_active ? 'gray' : 'green' }}-900"
                                        onclick="toggleStatus({{ $attributeValue->id }})"
                                        title="{{ $attributeValue->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $attributeValue->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                </button>
                                <form action="{{ route('admin.product-attribute-values.destroy', $attributeValue) }}" 
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
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No attribute values found. <a href="{{ route('admin.product-attribute-values.create') }}" class="text-brand-gold hover:text-blue-900">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $attributeValues->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(id) {
    fetch(`/admin/product-attribute-values/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong');
    });
}
</script>
@endpush
