@extends('layouts.frontend')

@section('title', 'My Favourites')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-text-heading mb-8 flex items-center gap-3">
            <span class="w-2 h-8 bg-brand-gold rounded"></span>
            My Favourites
        </h1>
        
        {{-- Check feature from DB --}}
        @php $featureEnabled = \App\Models\Setting::get('enable_favourites', 'false') === 'true'; @endphp
        @if(!$featureEnabled)
            <div class="text-center py-16">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                <h2 class="text-xl font-semibold text-gray-600 mb-2">Favourites Disabled</h2>
                <p class="text-gray-500">This feature is currently turned off by the admin.</p>
            </div>
        @else
            <div id="favourites-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="col-span-full text-center py-8">
                    <div class="inline-block w-8 h-8 border-4 border-brand-gold border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>
            
            <div id="empty-state" class="hidden text-center py-16">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-600 mb-2">No favourites yet</h2>
                <p class="text-gray-500 mb-6">Start adding products you love to your favourites list.</p>
                <a href="{{ route('products.index') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Browse Products
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

document.addEventListener('DOMContentLoaded', function() {
    loadFavourites();
});

async function loadFavourites() {
    try {
        const response = await fetch('{{ url("/user-api/favourites") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF
            }
        });
        
        if (!response.ok) {
            if (response.status === 401 || response.status === 403) {
                window.location.href = '{{ route("user.login") }}';
                return;
            }
            throw new Error('Failed to load favourites');
        }
        
        const data = await response.json();
        const container = document.getElementById('favourites-container');
        const emptyState = document.getElementById('empty-state');
        
        if (!data.data || data.data.length === 0) {
            container.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        container.innerHTML = data.data.map(product => createFavouriteCard(product)).join('');
    } catch (error) {
        console.error('Error loading favourites:', error);
    }
}

function createFavouriteCard(product) {
    return `
        <div class="card overflow-hidden group">
            <div class="relative overflow-hidden">
                <a href="/products/${product.slug}">
                    <img src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                    ${product.discounted_price ? `<span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm">Sale</span>` : ''}
                </a>
                <button onclick="removeFromFavourites(${product.id})" 
                    class="absolute top-2 left-2 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-text-heading mb-2 line-clamp-2">${product.name}</h3>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-lg font-bold text-brand-gold">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                    ${product.discounted_price ? `<span class="text-sm text-gray-500 line-through">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                </div>
                <button onclick="addToCart(${product.id}, 1)" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full">
                    Add to Cart
                </button>
            </div>
        </div>
    `;
}

async function removeFromFavourites(productId) {
    try {
        const response = await fetch(`{{ url('/user-api/favourites') }}/${productId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF
            }
        });
        
        const data = await response.json();
        if (data.success) {
            loadFavourites();
            if (typeof updateFavouritesCount === 'function') updateFavouritesCount();
            if (window.showModal) showModal('Removed', 'Product removed from favourites', 'success');
        }
    } catch (error) {
        console.error('Error removing from favourites:', error);
    }
}
</script>
@endsection
