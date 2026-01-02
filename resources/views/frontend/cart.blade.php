@extends('layouts.frontend')

@section('title', 'Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-6">Your Cart</h1>

    <div id="cart-container">
        <p class="text-gray-500">Loading cart...</p>
    </div>

    <div class="mt-6 flex justify-end">
        <a id="checkout-btn" href="/checkout" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 px-6 py-3 hidden">Proceed to Checkout</a>
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
                <a href="/products" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Browse Products</a>
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
                            <tr data-product-id="${item.product_id}" data-variation-id="${item.variation_id || ''}">
                                <td class="px-6 py-4 flex items-center space-x-4">
                                    <img src="/storage/${item.image || item.product.main_image}" class="w-16 h-16 object-cover rounded">
                                    <div>
                                        <div class="font-semibold">${item.product.name}</div>
                                        ${item.variation_title ? `<div class="text-sm text-gray-500">${item.variation_title}</div>` : ''}
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
                const vid = row.dataset.variationId ? parseInt(row.dataset.variationId) : null;
                const input = row.querySelector('.qty-input');
                input.value = parseInt(input.value) + 1;
                updateQuantity(pid, vid, parseInt(input.value));
            });
        });

        container.querySelectorAll('.qty-decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const pid = parseInt(row.dataset.productId);
                const vid = row.dataset.variationId ? parseInt(row.dataset.variationId) : null;
                const input = row.querySelector('.qty-input');
                const min = parseInt(input.getAttribute('min') || '1');
                if (parseInt(input.value) > min) {
                    input.value = parseInt(input.value) - 1;
                    updateQuantity(pid, vid, parseInt(input.value));
                }
            });
        });

        container.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                const pid = parseInt(row.dataset.productId);
                const vid = row.dataset.variationId ? parseInt(row.dataset.variationId) : null;
                let v = parseInt(this.value) || 1;
                if (v < 1) v = 1; this.value = v;
                updateQuantity(pid, vid, v);
            });
        });

        container.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const pid = parseInt(row.dataset.productId);
                const vid = row.dataset.variationId ? parseInt(row.dataset.variationId) : null;
                removeItem(pid, vid);
            });
        });
    }

    function updateQuantity(productId, variationId, quantity) {
        const cart = getCart();
        const item = cart.find(i =>
            i.product_id === productId &&
            (i.variation_id || null) === variationId
        );
        if (item) {
            item.quantity = quantity;
            saveCart(cart);
            refresh();
        }
    }

    function removeItem(productId, variationId) {
        let cart = getCart();
        cart = cart.filter(i =>
            !(i.product_id === productId && (i.variation_id || null) === variationId)
        );
        saveCart(cart);
        refresh();
    }

    function refresh() {
        const cart = getCart();
        if (cart.length === 0) {
            renderEmpty();
            return;
        }

        // Fetch products list and variations
        const fetchPromises = [fetch(`${API_BASE}/products?per_page=1000`).then(res => res.json())];

        // Fetch variations for products that have variations
        const productsWithVariations = [...new Set(cart.filter(ci => ci.variation_id).map(ci => ci.product_id))];
        const variationPromises = productsWithVariations.map(pid =>
            fetch(`${API_BASE}/products/${pid}/variations`).then(res => res.json())
        );

        Promise.all([...fetchPromises, ...variationPromises])
            .then(results => {
                const productsData = results[0].data;
                const variationsData = results.slice(1);

                // Build variations lookup map
                const variationsMap = {};
                variationsData.forEach((varData, index) => {
                    if (varData.success && varData.data.variations) {
                        const productId = productsWithVariations[index];
                        variationsMap[productId] = varData.data.variations;
                    }
                });

                const itemsData = cart.map(ci => {
                    const product = productsData.find(p => p.id === ci.product_id) || {};
                    let price, image, variationTitle;

                    if (ci.variation_id && variationsMap[ci.product_id]) {
                        const variation = variationsMap[ci.product_id].find(v => v.id === ci.variation_id);
                        if (variation) {
                            price = parseFloat(variation.price || 0);
                            image = variation.image;
                            variationTitle = variation.title;
                        } else {
                            price = parseFloat(product.discounted_price || product.selling_price || 0);
                        }
                    } else {
                        price = parseFloat(product.discounted_price || product.selling_price || 0);
                    }

                    const subtotal = price * ci.quantity;
                    return {
                        product_id: ci.product_id,
                        variation_id: ci.variation_id,
                        quantity: ci.quantity,
                        price: price,
                        subtotal: subtotal,
                        image: image,
                        variation_title: variationTitle,
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
@endsection
