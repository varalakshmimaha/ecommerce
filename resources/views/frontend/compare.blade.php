@extends('layouts.frontend')

@section('title', 'Compare Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        {{-- Check feature from DB --}}
        @php $featureEnabled = \App\Models\Setting::get('enable_compare', 'false') === 'true'; @endphp
        @if(!$featureEnabled)
            <div class="text-center py-16">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <h2 class="text-xl font-semibold text-gray-600 mb-2">Compare Disabled</h2>
                <p class="text-gray-500">This feature is currently turned off by the admin.</p>
            </div>
        @else
        <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-text-heading flex items-center gap-3">
                    <span class="w-2 h-8 bg-brand-gold rounded"></span>
                    Compare Products
                </h1>
                <button id="clear-compare" onclick="clearCompare()" class="hidden text-red-500 hover:text-red-600 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear All
                </button>
            </div>

            <div id="compare-container" class="overflow-x-auto">
                <div class="text-center py-8">
                    <div class="inline-block w-8 h-8 border-4 border-brand-gold border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>
            
            <div id="empty-state" class="hidden text-center py-16">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-600 mb-2">No products to compare</h2>
                <p class="text-gray-500 mb-6">Add products to your compare list to see them side by side.</p>
                <a href="{{ route('products.index') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Browse Products
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

document.addEventListener('DOMContentLoaded', function() {
    loadCompare();
});

async function loadCompare() {
    const spinner = document.getElementById('loading-spinner');
    try {
        const response = await fetch('{{ url("/user-api/compare") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        
        if (spinner) spinner.classList.add('hidden');
        
        if (!response.ok) {
            if (response.status === 401 || response.status === 403) {
                window.location.href = '{{ route("user.login") }}';
                return;
            }
            throw new Error('Failed to load compare list: ' + response.status);
        }
        
        const data = await response.json();
        const container = document.getElementById('compare-container');
        const emptyState = document.getElementById('empty-state');
        const clearBtn = document.getElementById('clear-compare');
        
        if (!data.data || data.data.length === 0) {
            container.innerHTML = '';
            emptyState.classList.remove('hidden');
            if (clearBtn) clearBtn.classList.add('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        if (clearBtn) clearBtn.classList.remove('hidden');
        container.innerHTML = createCompareTable(data.data);
    } catch (error) {
        if (spinner) spinner.classList.add('hidden');
        console.error('Error loading compare list:', error);
        const container = document.getElementById('compare-container');
        if (container) container.innerHTML = '<p class="text-center text-red-500 py-8">Failed to load compare list. Please refresh.</p>';
    }
}

function createCompareTable(products) {
    let html = '<table class="w-full bg-white rounded-lg shadow overflow-hidden">';
    
    html += '<thead><tr class="bg-gray-50">';
    html += '<th class="p-4 text-left font-semibold text-gray-700 border-b w-32">Feature</th>';
    products.forEach(product => {
        html += `
            <th class="p-4 text-center border-b min-w-[200px]">
                <div class="relative inline-block">
                    <img src="/storage/${product.main_image}" alt="${product.name}" class="w-32 h-32 object-cover mx-auto rounded-lg mb-3">
                    <button onclick="removeFromCompare(${product.id})" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-full hover:bg-red-600 flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <a href="/products/${product.slug}" class="text-brand-gold hover:text-brand-amber font-semibold line-clamp-2">${product.name}</a>
            </th>
        `;
    });
    html += '</tr></thead>';
    
    html += '<tbody><tr class="border-b">';
    html += '<td class="p-4 font-medium text-gray-700 bg-gray-50">Price</td>';
    products.forEach(product => {
        const price = parseFloat(product.discounted_price || product.selling_price).toFixed(2);
        const originalPrice = product.discounted_price ? parseFloat(product.selling_price).toFixed(2) : null;
        html += `
            <td class="p-4 text-center">
                <span class="text-lg font-bold text-brand-gold">₹${price}</span>
                ${originalPrice ? `<br><span class="text-sm text-gray-500 line-through">₹${originalPrice}</span>` : ''}
            </td>
        `;
    });
    html += '</tr>';
    
    html += '<tr class="border-b">';
    html += '<td class="p-4 font-medium text-gray-700 bg-gray-50">Category</td>';
    products.forEach(product => {
        html += `<td class="p-4 text-center text-gray-600">${product.category?.name || '-'}</td>`;
    });
    html += '</tr>';
    
    html += '<tr class="border-b">';
    html += '<td class="p-4 font-medium text-gray-700 bg-gray-50">Brand</td>';
    products.forEach(product => {
        html += `<td class="p-4 text-center text-gray-600">${product.brand?.name || '-'}</td>`;
    });
    html += '</tr>';
    
    html += '<tr class="border-b">';
    html += '<td class="p-4 font-medium text-gray-700 bg-gray-50">Description</td>';
    products.forEach(product => {
        const shortDesc = product.description ? product.description.substring(0, 100) + '...' : '-';
        html += `<td class="p-4 text-center text-gray-600 text-sm">${shortDesc}</td>`;
    });
    html += '</tr>';
    
    html += '<tr>';
    html += '<td class="p-4 font-medium text-gray-700 bg-gray-50">Actions</td>';
    products.forEach(product => {
        html += `
            <td class="p-4 text-center">
                <button onclick="addToCart(${product.id}, 1)" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Add to Cart
                </button>
            </td>
        `;
    });
    html += '</tr>';
    
    html += '</tbody></table>';
    return html;
}

async function removeFromCompare(productId) {
    try {
        const response = await fetch(`{{ url('/user-api/compare') }}/${productId}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await response.json();
        if (data.success) {
            loadCompare();
            if (typeof updateCompareCount === 'function') updateCompareCount();
        }
    } catch (error) {
        console.error('Error removing from compare:', error);
    }
}

async function clearCompare() {
    try {
        const response = await fetch('{{ url("/user-api/compare") }}', {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await response.json();
        if (data.success) {
            loadCompare();
            if (typeof updateCompareCount === 'function') updateCompareCount();
            if (window.showModal) showModal('Cleared', 'Compare list cleared', 'success');
        }
    } catch (error) {
        console.error('Error clearing compare list:', error);
    }
}
</script>
@endsection
