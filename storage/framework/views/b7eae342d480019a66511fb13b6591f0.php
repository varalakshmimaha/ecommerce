<?php $__env->startSection('title', 'Cart'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-6">Your Cart</h1>

    <div id="cart-container">
        <p class="text-gray-500">Loading cart...</p>
    </div>

    <div class="mt-6 flex justify-end">
        <a id="checkout-btn" href="/checkout" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 px-6 py-3 hidden">Proceed to Checkout</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function getCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        if (window.updateCartCount) window.updateCartCount();
    }

    function renderEmpty() {
        document.getElementById('cart-container').innerHTML = `
            <div class="text-center py-12">
                <p class="text-gray-500 mb-4">Your cart is empty.</p>
                <a href="/products" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Browse Products</a>
            </div>
        `;
        document.getElementById('checkout-btn').classList.add('hidden');
    }

    function renderCart(itemsData, totals) {
        const container = document.getElementById('cart-container');
        container.innerHTML = `
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Product</th>
                            <th class="px-6 py-3 text-left">Price</th>
                            <th class="px-6 py-3 text-left">Quantity</th>
                            <th class="px-6 py-3 text-left">Subtotal</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        ${itemsData.map(item => `
                            <tr data-product-id="${item.product_id}">
                                <td class="px-6 py-4 flex items-center space-x-4">
                                    <img src="/storage/${item.product.main_image}" class="w-16 h-16 object-cover rounded">
                                    <div>
                                        <div class="font-semibold">${item.product.name}</div>
                                        <div class="text-sm text-gray-500">${item.product.short_description || ''}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">₹${parseFloat(item.price).toFixed(2)}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <button class="qty-decrease px-3 py-1 border rounded-l">-</button>
                                        <input type="number" class="qty-input w-16 text-center border-t border-b" value="${item.quantity}" min="1">
                                        <button class="qty-increase px-3 py-1 border rounded-r">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4">₹${parseFloat(item.subtotal).toFixed(2)}</td>
                                <td class="px-6 py-4"><button class="remove-item text-red-600">Remove</button></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>

            <div class="mt-6 p-4 bg-white rounded shadow text-right">
                <div class="text-sm text-gray-600">Subtotal: ₹${parseFloat(totals.subtotal).toFixed(2)}</div>
                <div class="text-sm text-gray-600">GST: ₹${parseFloat(totals.gst_amount).toFixed(2)}</div>
                <div class="text-sm text-gray-600">Shipping: ₹${parseFloat(totals.shipping_charge).toFixed(2)}</div>
                <div class="text-xl font-bold mt-2">Total: ₹${parseFloat(totals.total_amount).toFixed(2)}</div>
            </div>
        `;

        document.getElementById('checkout-btn').classList.remove('hidden');

        // Wire up buttons
        container.querySelectorAll('.qty-increase').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const pid = parseInt(row.dataset.productId);
                const input = row.querySelector('.qty-input');
                input.value = parseInt(input.value) + 1;
                updateQuantity(pid, parseInt(input.value));
            });
        });

        container.querySelectorAll('.qty-decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const pid = parseInt(row.dataset.productId);
                const input = row.querySelector('.qty-input');
                const min = parseInt(input.getAttribute('min') || '1');
                if (parseInt(input.value) > min) {
                    input.value = parseInt(input.value) - 1;
                    updateQuantity(pid, parseInt(input.value));
                }
            });
        });

        container.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const pid = parseInt(row.dataset.productId);
                let v = parseInt(this.value) || 1;
                if (v < 1) v = 1; this.value = v;
                updateQuantity(pid, v);
            });
        });

        container.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const pid = parseInt(row.dataset.productId);
                removeItem(pid);
            });
        });
    }

    function updateQuantity(productId, quantity) {
        const cart = getCart();
        const item = cart.find(i => i.product_id === productId);
        if (item) {
            item.quantity = quantity;
            saveCart(cart);
            refresh();
        }
    }

    function removeItem(productId) {
        let cart = getCart();
        cart = cart.filter(i => i.product_id !== productId);
        saveCart(cart);
        refresh();
    }

    function refresh() {
        const cart = getCart();
        if (cart.length === 0) {
            renderEmpty();
            return;
        }

        // Fetch products list and map
        fetch(`${API_BASE}/products?per_page=1000`)
            .then(res => res.json())
            .then(data => {
                const products = data.data;
                const itemsData = cart.map(ci => {
                    const product = products.find(p => p.id === ci.product_id) || {};
                    const price = parseFloat(product.discounted_price || product.selling_price || 0);
                    const subtotal = price * ci.quantity;
                    return {
                        product_id: ci.product_id,
                        quantity: ci.quantity,
                        price: price,
                        subtotal: subtotal,
                        product: product
                    };
                });

                // Ask server to calculate totals (GST/shipping) for consistency
                fetch(`${API_BASE}/checkout/calculate`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ items: cart })
                }).then(res => res.json()).then(totals => {
                    renderCart(itemsData, totals);
                }).catch(err => {
                    // fallback compute locally
                    const subtotal = itemsData.reduce((s,i) => s + i.subtotal, 0);
                    renderCart(itemsData, { subtotal, gst_amount: 0, shipping_charge: 0, total_amount: subtotal });
                });
            });
    }

    refresh();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/frontend/cart.blade.php ENDPATH**/ ?>