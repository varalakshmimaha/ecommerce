@extends('layouts.admin')

@section('title', 'Product Variations - ' . $product->name)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-heading">Product Variations</h1>
            <p class="text-text-muted mt-1">Manage variations for: {{ $product->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.show', $product) }}" 
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Product
            </a>
            <a href="{{ route('admin.products.variations.create', $product) }}" 
               class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Variation
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4">
            @if($product->main_image)
                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" 
                     class="w-20 h-20 object-cover rounded-lg border border-gray-200">
            @else
                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
            <div>
                <h3 class="text-lg font-semibold text-text-heading">{{ $product->name }}</h3>
                <p class="text-text-muted">SKU: {{ $product->sku ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                        @if($product->status) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                        {{ $product->status ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Variations List -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-text-heading">Variations ({{ $variations->total() }})</h3>
                <div class="flex gap-2">
                    <button onclick="toggleBulkActions()" class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-200 transition-all duration-300">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Bulk Actions
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Panel (Hidden by default) -->
        <div id="bulkActionsPanel" class="hidden p-4 bg-gray-50 border-b border-gray-200">
            <form method="POST" action="{{ route('admin.products.variations.bulk-update', $product) }}">
                @csrf
                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-brand-gold text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-amber transition-all duration-300">
                        Update Selected
                    </button>
                    <button type="button" onclick="toggleBulkActions()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-400 transition-all duration-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($variations as $variation)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="selected_variations[]" value="{{ $variation->id }}" 
                                       class="variation-checkbox w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($variation->variation_image)
                                        <img src="{{ asset('storage/' . $variation->variation_image) }}" alt="Variation" 
                                             class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $variation->variation_title }}</div>
                                        <div class="text-sm text-gray-500">
                                            @foreach($variation->attributeValues as $attrValue)
                                                <span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs mr-1 mb-1">
                                                    {{ $attrValue->attribute->name }}: {{ $attrValue->attributeValue->value }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @if($variation->is_default)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-brand-gold text-white">
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $variation->sku ?? 'Auto-generated' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($variation->price)
                                    ₹{{ number_format($variation->price, 2) }}
                                @else
                                    <span class="text-gray-400">Use product price</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($variation->stock_quantity !== null)
                                    <span class="@if($variation->stock_quantity > 0) text-green-600 @else text-red-600 @endif">
                                        {{ $variation->stock_quantity }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Use product stock</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    @if($variation->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    {{ $variation->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.variations.edit', [$product, $variation]) }}" 
                                       class="text-brand-gold hover:text-brand-amber transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if(!$variation->is_default)
                                        <button onclick="setDefaultVariation({{ $variation->id }})" 
                                                class="text-blue-600 hover:text-blue-700 transition-colors" title="Set as Default">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    <button onclick="toggleVariationStatus({{ $variation->id }})" 
                                            class="text-yellow-600 hover:text-yellow-700 transition-colors" title="Toggle Status">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.products.variations.destroy', [$product, $variation]) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this variation?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No variations found. 
                                <a href="{{ route('admin.products.variations.create', $product) }}" 
                                   class="text-brand-gold hover:text-brand-amber font-medium">
                                    Create one
                                </a>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($variations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $variations->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleBulkActions() {
    const panel = document.getElementById('bulkActionsPanel');
    panel.classList.toggle('hidden');
}

function toggleVariationStatus(variationId) {
    fetch(`/admin/products/{{ $product->id }}/variations/${variationId}/toggle-status`, {
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

// Select all checkbox functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.variation-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endsection
