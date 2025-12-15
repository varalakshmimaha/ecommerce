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
                    <a href="{{ $banner->link }}" class="btn-primary mt-6 inline-block">Shop Now</a>
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
<section class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Shop by Category</h2>
    <div id="categories" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <!-- Categories will be loaded here -->
    </div>
</section>

<!-- Product Tabs -->
<section class="container mx-auto px-4 py-12 bg-white">
    <div class="flex flex-wrap justify-center mb-8 border-b border-gray-200">
        <button class="product-tab px-6 py-3 font-semibold text-gray-700 border-b-2 border-primary-600 active" data-type="featured">Featured</button>
        <button class="product-tab px-6 py-3 font-semibold text-gray-700 border-b-2 border-transparent" data-type="trending">Trending</button>
        <button class="product-tab px-6 py-3 font-semibold text-gray-700 border-b-2 border-transparent" data-type="new-arrival">New Arrival</button>
        <button class="product-tab px-6 py-3 font-semibold text-gray-700 border-b-2 border-transparent" data-type="top-rated">Top Rated</button>
    </div>
    <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Products will be loaded here -->
    </div>
    <div class="text-center mt-8">
        <a href="{{ route('products.index') }}" class="btn-primary">View All Products</a>
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
                <a href="/category/${cat.slug}" class="group">
                    <div class="card p-6 text-center">
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                            ${cat.image ? `<img src="/storage/${cat.image}" alt="${cat.name}" class="w-full h-full object-cover">` : '<div class="text-4xl">📦</div>'}
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">${cat.name}</h3>
                    </div>
                </a>
            `).join('');
        });
    
    // Load Products
    function loadProducts(type = 'featured') {
        fetch(`${API_BASE}/products?type=${type}&per_page=8`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('products-container');
                container.innerHTML = data.data.map(product => `
                    <div class="card overflow-hidden group">
                        <a href="/products/${product.slug}">
                            <div class="relative overflow-hidden">
                                <img src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                                ${product.discounted_price ? `<span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm">Sale</span>` : ''}
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${product.name}</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-primary-600">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                                    ${product.discounted_price ? `<span class="text-sm text-gray-500 line-through">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                                </div>
                                <button onclick="addToCart(${product.id}, 1); event.preventDefault();" class="btn-primary w-full mt-4">Add to Cart</button>
                            </div>
                        </a>
                    </div>
                `).join('');
            });
    }
    
    loadProducts('featured');
    
    // Tab switching
    document.querySelectorAll('.product-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.product-tab').forEach(t => {
                t.classList.remove('active', 'border-primary-600');
                t.classList.add('border-transparent');
            });
            this.classList.add('active', 'border-primary-600');
            this.classList.remove('border-transparent');
            loadProducts(this.dataset.type);
        });
    });
    
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


