<?php $__env->startSection('title', 'Product Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
    <div id="product-detail" class="container mx-auto px-4">
        <!-- Product will be loaded here -->
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-[#D4AF37] border-t-transparent"></div>
            <p class="text-[#6B6B6B] mt-4">Loading product details...</p>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.6s ease-out;
}

.zoom-image {
    transition: transform 0.3s ease;
}

.zoom-image:hover {
    transform: scale(1.05);
}

.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid #f3f4f6;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

/* Enhanced Gallery Styles */
.product-gallery-main {
    position: relative;
    overflow: hidden;
}

.product-gallery-main::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #D4AF37, #B8962E, #D4AF37, #B8962E);
    border-radius: 1rem;
    opacity: 0;
    z-index: -1;
    transition: opacity 0.5s ease;
    background-size: 400% 400%;
    animation: gradientShift 3s ease infinite;
}

.product-gallery-main:hover::before {
    opacity: 1;
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Thumbnail selector enhancements */
.product-gallery-main + div .inline-flex > div {
    position: relative;
    overflow: hidden;
}

.product-gallery-main + div .inline-flex > div::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.3), transparent);
    transition: left 0.5s ease;
}

.product-gallery-main + div .inline-flex > div:hover::before {
    left: 100%;
}

/* Active thumbnail indicator */
.product-gallery-main + div .inline-flex > div.border-\[\#D4AF37\] {
    position: relative;
}

.product-gallery-main + div .inline-flex > div.border-\[\#D4AF37\]::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #D4AF37, #B8962E);
    border-radius: 0.75rem;
    z-index: -1;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.5;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slug = '<?php echo e($slug); ?>';

    fetch(`${API_BASE}/products/${slug}`)
        .then(res => res.json())
        .then(product => {
            const container = document.getElementById('product-detail');
            container.innerHTML = `
                <div class="animate-fade-in">
                    <!-- Breadcrumb -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 text-sm text-[#6B6B6B]">
                            <a href="/" class="hover:text-[#D4AF37] transition-colors">Home</a>
                            <span>/</span>
                            <a href="/products" class="hover:text-[#D4AF37] transition-colors">Products</a>
                            ${product.category ? `
                                <span>/</span>
                                <a href="/category/${product.category.slug}" class="hover:text-[#D4AF37] transition-colors">${product.category.name}</a>
                            ` : ''}
                            <span>/</span>
                            <span class="text-[#D4AF37]">${product.name}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                        <!-- Image Gallery -->
                        <div class="space-y-6">
                            <div class="product-gallery-main bg-white rounded-2xl border-2 border-gray-100 overflow-hidden relative group">
                                <!-- Animated border -->
                                <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-[#D4AF37] via-[#B8962E] to-[#D4AF37] opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                                <div class="absolute inset-0 rounded-2xl bg-white m-1"></div>
                                <div class="relative m-1 rounded-xl overflow-hidden">
                                    <img id="main-image" src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-96 object-contain rounded-lg zoom-image transition-all duration-500 group-hover:scale-105">
                                </div>
                            </div>
                            ${product.images && product.images.length > 0 ? `
                                <div class="flex justify-center">
                                    <div class="w-full max-w-md overflow-x-auto">
                                        <div class="inline-flex gap-2 p-3 bg-white rounded-2xl border-2 border-gray-100 shadow-lg min-w-max">
                                            <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-[#D4AF37] bg-white transition-all duration-300 hover:scale-105 hover:shadow-lg flex-shrink-0" onclick="changeMainImage('/storage/${product.main_image}')">
                                                <img src="/storage/${product.main_image}" alt="${product.name}" class="w-16 h-16 sm:w-20 sm:h-20 object-cover">
                                            </div>
                                            ${product.images.map(img => `
                                                <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-[#D4AF37] bg-white transition-all duration-300 hover:scale-105 hover:shadow-lg flex-shrink-0" onclick="changeMainImage('/storage/${img.image_path}')">
                                                    <img src="/storage/${img.image_path}" alt="${product.name}" class="w-16 h-16 sm:w-20 sm:h-20 object-cover">
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                        </div>

                        <!-- Product Info -->
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-4xl font-bold text-[#1A1A1A] mb-2">${product.name}</h1>
                                ${product.short_description ? `<p class="text-[#6B6B6B]">${product.short_description}</p>` : ''}
                            </div>

                            <!-- Price -->
                            <div class="bg-white rounded-xl border border-gray-100 p-6">
                                <div class="flex items-end gap-3 mb-2">
                                    <span class="text-4xl font-bold text-[#D4AF37]">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                                    ${product.discounted_price ? `
                                        <span class="text-2xl text-[#6B6B6B] line-through mb-1">₹${parseFloat(product.selling_price).toFixed(2)}</span>
                                    ` : ''}
                                </div>
                                ${product.mrp > parseFloat(product.discounted_price || product.selling_price) ? `
                                    <div class="flex items-center gap-2 text-sm text-[#6B6B6B]">
                                        <span>MRP: ₹${parseFloat(product.mrp).toFixed(2)}</span>
                                        <span class="text-green-400">(${Math.round(((product.mrp - parseFloat(product.discounted_price || product.selling_price)) / product.mrp) * 100)}% OFF)</span>
                                    </div>
                                ` : ''}
                                                            </div>

                            <!-- Quantity Selector -->
                            <div class="bg-white rounded-xl border border-gray-100 p-6">
                                <label class="block text-sm font-semibold text-[#D4AF37] mb-3">Quantity</label>
                                <div class="flex items-center gap-4">
                                    <button onclick="decreaseQuantity()" class="w-12 h-12 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-[#1A1A1A] hover:bg-[#D4AF37] hover:text-white transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <input type="number" id="quantity" value="${product.min_order_quantity}" min="${product.min_order_quantity}" class="w-24 text-center px-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] text-xl font-bold focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent">
                                    <button onclick="increaseQuantity()" class="w-12 h-12 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-[#1A1A1A] hover:bg-[#D4AF37] hover:text-white transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <button onclick="addToCart(${product.id})" class="w-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:shadow-[#D4AF37]/50 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                    <!-- Product Description -->
                    ${product.full_description ? `
                        <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8 animate-slide-up">
                            <h2 class="text-2xl font-bold text-[#1A1A1A] mb-6 flex items-center gap-3">
                                <span class="w-2 h-8 bg-[#D4AF37] rounded"></span>
                                Product Description
                            </h2>
                            <div class="prose prose-lg prose-invert max-w-none">
                                <div class="text-[#6B6B6B] leading-relaxed">${product.full_description}</div>
                            </div>
                        </div>
                    ` : ''}

                    <!-- Related Products -->
                    ${product.related_products && product.related_products.length > 0 ? `
                        <div class="animate-slide-up mb-12">
                            <h2 class="text-2xl font-bold text-[#1A1A1A] mb-8 flex items-center gap-3">
                                <span class="w-2 h-8 bg-[#D4AF37] rounded"></span>
                                Related Products
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                ${product.related_products.map(related => `
                                    <div class="card overflow-hidden group">
                                        <a href="/products/${related.slug}">
                                            <div class="relative overflow-hidden">
                                                <img src="/storage/${related.main_image}" alt="${related.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                                                ${related.discounted_price ? `<span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm">Sale</span>` : ''}
                                            </div>
                                            <div class="p-4">
                                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#D4AF37] transition-colors">${related.name}</h3>
                                                <div class="flex items-center space-x-2 mb-3">
                                                    <span class="text-lg font-bold text-[#D4AF37]">₹${parseFloat(related.discounted_price || related.selling_price).toFixed(2)}</span>
                                                    ${related.discounted_price ? `<span class="text-sm text-gray-500 line-through">₹${parseFloat(related.selling_price).toFixed(2)}</span>` : ''}
                                                </div>
                                                <button onclick="addToCartRelated(${related.id}, 1); event.preventDefault();" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full">Add to Cart</button>
                                            </div>
                                        </a>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
            
            // Initialize any interactive elements
            if (product.related_products && product.related_products.length > 0) {
                // Add event listeners for related products add to cart buttons
                setTimeout(() => {
                    document.querySelectorAll('button[onclick^="addToCartRelated"]').forEach(button => {
                        // Already handled by inline onclick
                    });
                }, 100);
            }
        })
        .catch(err => {
            console.error('Error loading product:', err);
            const container = document.getElementById('product-detail');
            container.innerHTML = `
                <div class="text-center py-12">
                    <p class="text-red-400 text-xl">Error loading product details</p>
                    <a href="/products" class="inline-block mt-4 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-8 py-3 rounded-lg font-semibold">
                        Back to Products
                    </a>
                </div>
            `;
        });
});

// Function for adding to cart from related products
window.addToCartRelated = function(productId, quantity) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existing = cart.find(item => item.product_id === productId);
    if (existing) {
        existing.quantity += quantity;
    } else {
        cart.push({ product_id: productId, quantity: quantity });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count if function exists
    if (typeof window.updateCartCount === 'function') {
        window.updateCartCount();
    }
    
    // Show success message
    if (typeof window.showModal === 'function') {
        window.showModal('Success', 'Product added to cart!', 'success');
    } else {
        alert('Product added to cart!');
    }
    
    // Redirect to cart after a delay
    setTimeout(() => {
        window.location.href = '/cart';
    }, 1000);
};

// Image gallery functions
window.changeMainImage = function(src) {
    const mainImage = document.getElementById('main-image');
    if (mainImage) {
        // Add fade effect
        mainImage.style.opacity = '0';
        setTimeout(() => {
            mainImage.src = src;
            mainImage.style.opacity = '1';
        }, 200);
        
        // Update active thumbnail border
        const thumbnails = document.querySelectorAll('.product-gallery-main + div .inline-flex > div');
        thumbnails.forEach(thumb => {
            const img = thumb.querySelector('img');
            if (img && img.src === window.location.origin + src) {
                thumb.classList.remove('border-gray-200');
                thumb.classList.add('border-[#D4AF37]');
            } else {
                thumb.classList.remove('border-[#D4AF37]');
                thumb.classList.add('border-gray-200');
            }
        });
    }
};

window.increaseQuantity = function() {
    const input = document.getElementById('quantity');
    if (input) {
        const currentValue = parseInt(input.value) || 0;
        input.value = currentValue + 1;
    }
};

window.decreaseQuantity = function() {
    const input = document.getElementById('quantity');
    if (input) {
        const minQty = parseInt(input.getAttribute('min')) || 1;
        const currentValue = parseInt(input.value) || minQty;
        if (currentValue > minQty) {
            input.value = currentValue - 1;
        }
    }
};

window.addToCart = function(productId) {
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
    
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existing = cart.find(item => item.product_id === productId);
    
    if (existing) {
        existing.quantity += quantity;
    } else {
        cart.push({ product_id: productId, quantity: quantity });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count if function exists
    if (typeof window.updateCartCount === 'function') {
        window.updateCartCount();
    }
    
    // Show success message
    if (typeof window.showModal === 'function') {
        window.showModal('Success', 'Product added to cart!', 'success');
    } else {
        alert('Product added to cart!');
    }
    
    // Redirect to cart after a delay
    setTimeout(() => {
        window.location.href = '/cart';
    }, 1000);
};
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/suveeindiaweb/htdocs/suveeindia.com/suvee/resources/views/frontend/products/show.blade.php ENDPATH**/ ?>