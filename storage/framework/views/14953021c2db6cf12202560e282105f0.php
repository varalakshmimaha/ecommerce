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
                        <div class="space-y-4">
                            <div class="bg-white rounded-2xl border border-gray-100 p-4 overflow-hidden">
                                <img id="main-image" src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-96 object-contain rounded-lg zoom-image">
                            </div>
                            ${product.images && product.images.length > 0 ? `
                                <div class="grid grid-cols-5 gap-2">
                                    <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-[#D4AF37] bg-white" onclick="changeMainImage('/storage/${product.main_image}')">
                                        <img src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-20 object-contain">
                                    </div>
                                    ${product.images.map(img => `
                                        <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-gray-100 hover:border-[#D4AF37] transition-all bg-white" onclick="changeMainImage('/storage/${img.image_path}')">
                                            <img src="/storage/${img.image_path}" alt="${product.name}" class="w-full h-20 object-contain">
                                        </div>
                                    `).join('')}
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
                                <div class="mt-2 text-sm text-[#6B6B6B]">
                                    <span>GST: ${product.gst}% ${product.gst_type === 'inclusive' ? 'Inclusive' : 'Exclusive'}</span>
                                </div>
                            </div>

                            <!-- Stock & Details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white rounded-xl border border-gray-100 p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="text-sm text-[#6B6B6B]">Stock</span>
                                    </div>
                                    <p class="text-xl font-bold text-[#1A1A1A]">${product.stock_quantity} Available</p>
                                </div>
                                <div class="bg-white rounded-xl border border-gray-100 p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm text-[#6B6B6B]">Min Order</span>
                                    </div>
                                    <p class="text-xl font-bold text-[#1A1A1A]">${product.min_order_quantity}</p>
                                </div>
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

                            <!-- Product Meta -->
                            <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-3">
                                ${product.category ? `
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-[#D4AF37] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-[#6B6B6B]">Category</p>
                                            <p class="text-[#1A1A1A] font-semibold">${product.category.name}</p>
                                        </div>
                                    </div>
                                ` : ''}
                                ${product.sub_category ? `
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-[#D4AF37] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-[#6B6B6B]">Sub Category</p>
                                            <p class="text-[#1A1A1A] font-semibold">${product.sub_category.name}</p>
                                        </div>
                                    </div>
                                ` : ''}
                                ${product.brand ? `
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-[#D4AF37] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-[#6B6B6B]">Brand</p>
                                            <p class="text-[#1A1A1A] font-semibold">${product.brand.name}</p>
                                        </div>
                                    </div>
                                ` : ''}
                                ${product.sku ? `
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-[#D4AF37] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-[#6B6B6B]">SKU</p>
                                            <p class="text-[#1A1A1A] font-semibold">${product.sku}</p>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>

                    <!-- Product Attributes -->
                    ${product.attributes && product.attributes.length > 0 ? `
                        <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8 animate-slide-up">
                            <h2 class="text-2xl font-bold text-[#1A1A1A] mb-6 flex items-center gap-3">
                                <span class="w-2 h-8 bg-[#D4AF37] rounded"></span>
                                Product Specifications
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                ${product.attributes.map(attr => `
                                    <div class="bg-white/50 rounded-lg p-4 border border-[#D4AF37]/10">
                                        <p class="text-sm text-[#D4AF37] mb-1">${attr.attribute_name}</p>
                                        <p class="text-[#1A1A1A] font-semibold">${attr.value}</p>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}

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
                        <div class="animate-slide-up">
                            <h2 class="text-2xl font-bold text-[#1A1A1A] mb-6 flex items-center gap-3">
                                <span class="w-2 h-8 bg-[#D4AF37] rounded"></span>
                                Related Products
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                ${product.related_products.map(related => `
                                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-xl hover:shadow-[#D4AF37]/30 transition-all duration-300 transform hover:scale-105 group">
                                        <a href="/products/${related.slug}">
                                            <div class="relative overflow-hidden">
                                                <img src="/storage/${related.main_image}" alt="${related.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                                            </div>
                                            <div class="p-4">
                                                <h3 class="font-semibold text-[#1A1A1A] mb-2 line-clamp-2 group-hover:text-[#D4AF37] transition-colors">${related.name}</h3>
                                                <p class="text-lg font-bold text-[#D4AF37]">₹${parseFloat(related.discounted_price || related.selling_price).toFixed(2)}</p>
                                            </div>
                                        </a>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
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
        if (window.updateCartCount) window.updateCartCount();
        if (window.showModal) showModal('Success', 'Product added to cart!', 'success');
        // Redirect to cart
        setTimeout(() => window.location.href = '/cart', 1000);
    };
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/frontend/products/show.blade.php ENDPATH**/ ?>