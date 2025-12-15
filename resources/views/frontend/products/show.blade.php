@extends('layouts.frontend')

@section('title', 'Product Details')

@section('content')
<div id="product-detail" class="container mx-auto px-4 py-8">
    <!-- Product will be loaded here -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slug = '{{ $slug }}';
    
    fetch(`${API_BASE}/products/${slug}`)
        .then(res => res.json())
        .then(product => {
            const container = document.getElementById('product-detail');
            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="mb-4">
                            <img id="main-image" src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-96 object-cover rounded-lg">
                        </div>
                        ${product.images && product.images.length > 0 ? `
                            <div class="grid grid-cols-4 gap-2">
                                <img src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-24 object-cover rounded-lg cursor-pointer border-2 border-primary-600" onclick="changeMainImage('/storage/${product.main_image}')">
                                ${product.images.map(img => `
                                    <img src="/storage/${img.image_path}" alt="${product.name}" class="w-full h-24 object-cover rounded-lg cursor-pointer hover:border-2 hover:border-primary-600" onclick="changeMainImage('/storage/${img.image_path}')">
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">${product.name}</h1>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-primary-600">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                            ${product.discounted_price ? `<span class="text-xl text-gray-500 line-through ml-2">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                            ${product.mrp > parseFloat(product.discounted_price || product.selling_price) ? `<span class="text-sm text-gray-500 ml-2">MRP: ₹${parseFloat(product.mrp).toFixed(2)}</span>` : ''}
                        </div>
                        ${product.short_description ? `<p class="text-gray-700 mb-4">${product.short_description}</p>` : ''}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center space-x-4">
                                <button onclick="decreaseQuantity()" class="w-10 h-10 border rounded-lg flex items-center justify-center">-</button>
                                <input type="number" id="quantity" value="${product.min_order_quantity}" min="${product.min_order_quantity}" class="w-20 text-center border rounded-lg">
                                <button onclick="increaseQuantity()" class="w-10 h-10 border rounded-lg flex items-center justify-center">+</button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Stock: ${product.stock_quantity} available</p>
                            <p class="text-sm text-gray-600">Min Order: ${product.min_order_quantity}</p>
                        </div>
                        <button onclick="addToCart(${product.id})" class="btn-primary w-full mb-4">Add to Cart</button>
                        <div class="border-t pt-4">
                            <h3 class="font-semibold mb-2">Product Details</h3>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p><strong>Category:</strong> ${product.category.name}</p>
                                ${product.sub_category ? `<p><strong>Sub Category:</strong> ${product.sub_category.name}</p>` : ''}
                                <p><strong>GST:</strong> ${product.gst}%</p>
                            </div>
                        </div>
                    </div>
                </div>
                ${product.full_description ? `
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Description</h2>
                        <div class="prose max-w-none">${product.full_description}</div>
                    </div>
                ` : ''}
                ${product.related_products && product.related_products.length > 0 ? `
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Related Products</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            ${product.related_products.map(related => `
                                <div class="card overflow-hidden group">
                                    <a href="/products/${related.slug}">
                                        <img src="/storage/${related.main_image}" alt="${related.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                                        <div class="p-4">
                                            <h3 class="font-semibold text-gray-900 mb-2">${related.name}</h3>
                                            <p class="text-lg font-bold text-primary-600">₹${parseFloat(related.discounted_price || related.selling_price).toFixed(2)}</p>
                                        </div>
                                    </a>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            `;
        });
    
    window.changeMainImage = function(src) {
        document.getElementById('main-image').src = src;
    };
    
    window.increaseQuantity = function() {
        const input = document.getElementById('quantity');
        input.value = parseInt(input.value) + 1;
    };
    
    window.decreaseQuantity = function() {
        const input = document.getElementById('quantity');
        const minQty = parseInt(input.getAttribute('min') || 1);
        if (parseInt(input.value) > minQty) {
            input.value = parseInt(input.value) - 1;
        }
    };
    
    window.addToCart = function(productId) {
        const quantity = parseInt(document.getElementById('quantity').value);
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existing = cart.find(item => item.product_id === productId);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({ product_id: productId, quantity: quantity });
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        const msg = 'Product added to cart!';
        if (window.showModal) showModal('Success', msg, 'success'); else alert(msg);
        // Go to cart page
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

