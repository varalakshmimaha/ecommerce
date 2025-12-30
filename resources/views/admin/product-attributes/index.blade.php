@extends('layouts.admin')

@section('title', 'Product Attributes')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Product Attributes</h2>
        <a href="{{ route('admin.product-attributes.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Add Attribute</a>
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
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Slug</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Values Count</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sort Order</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attributes as $attribute)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $attribute->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $attribute->slug }}</code>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $attribute->description ? Str::limit($attribute->description, 50) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $attribute->active_values_count }} values
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $attribute->sort_order }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($attribute->is_active)
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.product-attributes.show', $attribute) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.product-attributes.edit', $attribute) }}" 
                                   class="text-brand-gold hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="text-{{ $attribute->is_active ? 'gray' : 'green' }}-600 hover:text-{{ $attribute->is_active ? 'gray' : 'green' }}-900"
                                        onclick="toggleStatus({{ $attribute->id }})"
                                        title="{{ $attribute->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $attribute->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                </button>
                                <form action="{{ route('admin.product-attributes.destroy', $attribute) }}" 
                                      method="POST" class="inline-block" 
                                      onsubmit="return confirm('Are you sure you want to delete this attribute?')">
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
                            No attributes found. <a href="{{ route('admin.product-attributes.create') }}" class="text-brand-gold hover:text-blue-900">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $attributes->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(id) {
    fetch(`/admin/product-attributes/${id}/toggle-status`, {
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
