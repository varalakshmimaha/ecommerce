@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
<!-- Hero Banner Slider -->
@php
    $banners = \App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
@endphp
@if($banners->count() > 0)
<div class="relative h-96 md:h-[500px] overflow-hidden">
    <div id="banner-slider" class="h-full">
        @foreach($banners as $banner)
        <div class="banner-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
            <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
            @if($banner->title || $banner->description)
            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                <div class="text-center text-white px-4 animate-slide-up">
                    @if($banner->title)
                    <h2 class="text-4xl md:text-6xl font-bold mb-4">{{ $banner->title }}</h2>
                    @endif
                    @if($banner->description)
                    <p class="text-xl md:text-2xl">{{ $banner->description }}</p>
                    @endif
                    @if($banner->link)
                    <a href="{{ $banner->link }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 mt-6 inline-block">Shop Now</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
        @foreach($banners as $index => $banner)
        <button class="banner-dot w-3 h-3 rounded-full bg-white {{ $index === 0 ? 'opacity-100' : 'opacity-50' }}" data-slide="{{ $index }}"></button>
        @endforeach
    </div>
</div>
@endif

<!-- Categories Section -->
<section class="container mx-auto px-4 py-6">
    <h2 class="text-3xl font-bold text-text-heading mb-8 text-center">Shop by Category</h2>
    <div class="w-full flex justify-center">
        <div id="categories" class="grid gap-6 text-center" style="grid-auto-flow: column; grid-auto-columns: minmax(0,1fr);"></div>
    </div>
        <!-- Categories will be loaded here -->
    </div>
</section>

<!-- Product Sections -->
<section class="container mx-auto px-4 py-6 bg-white space-y-12">
    
    <!-- Featured Products Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-text-heading flex items-center gap-3">
                <span class="w-2 h-8 bg-brand-gold rounded"></span>
                Featured Products
            </h2>
            <a href="{{ route('products.index') }}?type=featured" class="text-brand-gold hover:text-brand-amber text-sm font-medium transition-colors">
                View All →
            </a>
        </div>
        <div id="featured-products" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Featured products will be loaded here -->
        </div>
    </div>
    
    <!-- Trending Products Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-text-heading flex items-center gap-3">
                <span class="w-2 h-8 bg-brand-amber rounded"></span>
                Trending Products
            </h2>
            <a href="{{ route('products.index') }}?type=trending" class="text-brand-amber hover:text-brand-burnt text-sm font-medium transition-colors">
                View All →
            </a>
        </div>
        <div id="trending-products" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Trending products will be loaded here -->
        </div>
    </div>
    
    <!-- New Arrival Products Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-text-heading flex items-center gap-3">
                <span class="w-2 h-8 bg-brand-burnt rounded"></span>
                New Arrivals
            </h2>
            <a href="{{ route('products.index') }}?type=new-arrival" class="text-brand-burnt hover:text-brand-crimson text-sm font-medium transition-colors">
                View All →
            </a>
        </div>
        <div id="new-arrival-products" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- New arrival products will be loaded here -->
        </div>
    </div>
    
    <!-- Top Rated Products Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-text-heading flex items-center gap-3">
                <span class="w-2 h-8 bg-brand-crimson rounded"></span>
                Top Rated Products
            </h2>
            <a href="{{ route('products.index') }}?type=top-rated" class="text-brand-crimson hover:text-brand-gold text-sm font-medium transition-colors">
                View All →
            </a>
        </div>
        <div id="top-rated-products" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Top rated products will be loaded here -->
        </div>
    </div>
    
    <div class="text-center pt-8 border-t border-gray-200">
        <a href="{{ route('products.index') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-4 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 text-lg">View All Products</a>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Banner Slider
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.banner-dot');
    let currentSlide = 0;
    
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.style.opacity = i === index ? '1' : '0';
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('opacity-100', i === index);
            dot.classList.toggle('opacity-50', i !== index);
        });
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    if (slides.length > 0) {
        showSlide(0);
        setInterval(nextSlide, 5000);
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
    }
    
    // Load Categories
    fetch(`${API_BASE}/categories`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('categories');
            if (!data.data || data.data.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 col-span-full">No categories available</p>';
                return;
            }
            container.innerHTML = data.data.map(cat => `
                <a href="/category/${cat.slug}" class="flex flex-col items-center group p-2">
                    <div class="w-24 h-24 mb-3 rounded-full overflow-hidden border-4 border-surface-light group-hover:border-brand-gold bg-gradient-to-tr from-surface-light to-surface-medium flex items-center justify-center shadow-md transition-all duration-300">
                        ${cat.image ? `<img src="/storage/${cat.image}" alt="${cat.name}" class="w-full h-full object-cover scale-100 group-hover:scale-110 transition-transform duration-300">` : '<div class="text-4xl">📦</div>'}
                    </div>
                    <span class="font-semibold text-base md:text-lg text-text-heading group-hover:text-brand-gold transition-colors tracking-wide text-center">${cat.name}</span>
                </a>
            `).join('');
        });
    
    // Load Products for each section
    function loadProducts(type = 'featured', containerId = null, limit = 4) {
        fetch(`${API_BASE}/products?type=${type}&per_page=${limit}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById(containerId || `${type}-products`);
                if (!container) return;
                
                // Use the same product card design for all sections
                container.innerHTML = data.data.map(product => createProductCard(product)).join('');
            })
            .catch(error => {
                console.error(`Error loading ${type} products:`, error);
                const container = document.getElementById(containerId || `${type}-products`);
                if (container) {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">Failed to load products</p>';
                }
            });
    }
    
    // Product card - same design as original
    function createProductCard(product) {
        return `
            <div class="card overflow-hidden group">
                <a href="/products/${product.slug}">
                    <div class="relative overflow-hidden">
                        <img src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                        ${product.discounted_price ? `<span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm">Sale</span>` : ''}
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-text-heading mb-2 line-clamp-2">${product.name}</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold text-brand-gold">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                            ${product.discounted_price ? `<span class="text-sm text-gray-500 line-through">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                        </div>
                        <button onclick="addToCart(${product.id}, 1); event.preventDefault();" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full mt-4">Add to Cart</button>
                    </div>
                </a>
            </div>
        `;
    }
    
    // Load all sections
    loadProducts('featured', 'featured-products', 4);
    loadProducts('trending', 'trending-products', 4);
    loadProducts('new-arrival', 'new-arrival-products', 4);
    loadProducts('top-rated', 'top-rated-products', 8);
    
    // Add to Cart
    window.addToCart = function(productId, quantity) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existing = cart.find(item => item.product_id === productId);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({ product_id: productId, quantity: quantity });
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        // redirect to cart after adding
        if (window.showModal) showModal('Added to Cart', 'Product added to cart', 'success');
        window.location.href = '/cart';
    };
    
    function updateCartCount() {
        if (window.updateCartCount) return window.updateCartCount();
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
    }
    
    updateCartCount();
});
</script>

@endsection


