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
                    <a href="{{ $banner->link }}" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 mt-6 inline-block">Shop Now</a>
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
    <h2 class="text-3xl font-bold text-[#1A1A1A] mb-8 text-center">Shop by Category</h2>
    <div class="w-full flex justify-center">
        <div id="categories" class="grid gap-6 text-center" style="grid-auto-flow: column; grid-auto-columns: minmax(0,1fr);"></div>
    </div>
        <!-- Categories will be loaded here -->
    </div>
</section>

<!-- Product Tabs -->
<section class="container mx-auto px-4 py-12 bg-white">
    <div class="flex flex-wrap justify-center mb-8 border-b border-gray-200">
        <button class="product-tab px-6 py-3 font-semibold text-[#1A1A1A] border-b-2 border-[#D4AF37] active" data-type="featured">Featured</button>
        <button class="product-tab px-6 py-3 font-semibold text-[#1A1A1A] border-b-2 border-transparent" data-type="trending">Trending</button>
        <button class="product-tab px-6 py-3 font-semibold text-[#1A1A1A] border-b-2 border-transparent" data-type="new-arrival">New Arrival</button>
        <button class="product-tab px-6 py-3 font-semibold text-[#1A1A1A] border-b-2 border-transparent" data-type="top-rated">Top Rated</button>
    </div>
    <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Products will be loaded here -->
    </div>
    <div class="text-center mt-8">
        <a href="{{ route('products.index') }}" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">View All Products</a>
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
                    <div class="w-24 h-24 mb-3 rounded-full overflow-hidden border-4 border-[#fffbe6] group-hover:border-[#D4AF37] bg-gradient-to-tr from-[#fffbe6] to-[#f9e7b3] flex items-center justify-center shadow-md transition-all duration-300">
                        ${cat.image ? `<img src="/storage/${cat.image}" alt="${cat.name}" class="w-full h-full object-cover scale-100 group-hover:scale-110 transition-transform duration-300">` : '<div class="text-4xl">📦</div>'}
                    </div>
                    <span class="font-semibold text-base md:text-lg text-[#1A1A1A] group-hover:text-[#D4AF37] transition-colors tracking-wide text-center">${cat.name}</span>
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
                                <h3 class="font-semibold text-[#1A1A1A] mb-2 line-clamp-2">${product.name}</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-[#D4AF37]">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                                    ${product.discounted_price ? `<span class="text-sm text-gray-500 line-through">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                                </div>
                                <button onclick="addToCart(${product.id}, 1); event.preventDefault();" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full mt-4">Add to Cart</button>
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
                t.classList.remove('active', 'border-[#D4AF37]');
                t.classList.add('border-transparent');
            });
            this.classList.add('active', 'border-[#D4AF37]');
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
<style>
.whatsapp-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    animation: float 2.5s ease-in-out infinite;
}

.whatsapp-float img {
    width: 55px;
    height: 55px;
}

/* Floating animation */
@keyframes float {
    0%   { transform: translateY(0); }
    50%  { transform: translateY(-10px); }
    100% { transform: translateY(0); }
}
</style>

<a href="https://wa.me/919845145363?text=Hi%20I%20am%20interested%20in%20your%20products"
   class="whatsapp-float"
   target="_blank">
    <!--<img src="{{ asset('build/assets/whatsapp.png') }}" alt="WhatsApp">-->
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="64" height="64">
  <path fill="#25D366"
    d="M12 2a10 10 0 0 0-8.7 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/>
  <path fill="#ffffff"
    d="M16.7 14.5c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.7.9-.1.1-.3.2-.5.1-.2-.1-.9-.3-1.7-1-.6-.5-1-1.2-1.1-1.4-.1-.2 0-.4.1-.5l.4-.5c.1-.2.2-.3.2-.5s0-.3-.1-.5l-.7-1.7c-.2-.5-.4-.4-.5-.4H9.6c-.2 0-.5.1-.7.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.3c.1.1 1.6 2.4 3.8 3.3.5.2.9.3 1.2.4.5.2 1 .1 1.4.1.4-.1 1.4-.6 1.6-1.1.2-.5.2-.9.1-1-.1-.1-.2-.2-.4-.3z"/>
</svg>

</a>
@endsection


