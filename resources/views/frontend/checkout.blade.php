@extends('layouts.frontend')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-24">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>
                <div id="order-summary">
                    <!-- Order summary will be loaded here -->
                </div>
                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">₹0.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>GST:</span>
                        <span id="gst-amount">₹0.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Shipping:</span>
                        <span id="shipping">₹0.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                        <span>Total:</span>
                        <span id="total-amount" class="text-primary-600">₹0.00</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form id="checkout-form" class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                            <input type="text" name="name" required class="input-field">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number *</label>
                            <input type="text" name="mobile" required class="input-field">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <textarea name="address" rows="3" required class="input-field"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                            <input type="text" name="pincode" required class="input-field">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" name="email" class="input-field">
                        </div>
                    </div>
                </div>
                
                <!-- Payment Section -->
                <div class="card p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Payment</h2>
                    <p class="text-gray-600 mb-4">Please make payment using one of the following methods:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">QR Code</h3>
                            @php
                                $qrCode = \App\Models\Setting::get('qr_code');
                            @endphp
                            @if($qrCode)
                                <img src="{{ asset('storage/' . $qrCode) }}" alt="QR Code" class="w-48 h-48 object-contain border rounded-lg p-2">
                            @else
                                <p class="text-gray-500 text-sm">QR code not configured</p>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Bank Details</h3>
                            <div class="text-sm text-gray-700 space-y-1">
                                @if(\App\Models\Setting::get('bank_name'))
                                <p><strong>Bank:</strong> {{ \App\Models\Setting::get('bank_name') }}</p>
                                @endif
                                @if(\App\Models\Setting::get('bank_account_number'))
                                <p><strong>Account Number:</strong> {{ \App\Models\Setting::get('bank_account_number') }}</p>
                                @endif
                                @if(\App\Models\Setting::get('bank_ifsc'))
                                <p><strong>IFSC:</strong> {{ \App\Models\Setting::get('bank_ifsc') }}</p>
                                @endif
                                @if(\App\Models\Setting::get('bank_account_holder'))
                                <p><strong>Account Holder:</strong> {{ \App\Models\Setting::get('bank_account_holder') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Proof *</label>
                        <input type="file" name="payment_proof" accept="image/*" required class="input-field">
                        <p class="text-xs text-gray-500 mt-1">Upload screenshot of payment transaction</p>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary w-full text-lg py-4">Place Order</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    if (cart.length === 0) {
        window.location.href = '/products';
        return;
    }
    
    // Load order summary
    function loadOrderSummary() {
        const items = cart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            attributes: item.attributes || []
        }));
        
        fetch(`${API_BASE}/checkout/calculate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ items })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('subtotal').textContent = `₹${parseFloat(data.subtotal).toFixed(2)}`;
            document.getElementById('gst-amount').textContent = `₹${parseFloat(data.gst_amount).toFixed(2)}`;
            document.getElementById('shipping').textContent = `₹${parseFloat(data.shipping_charge).toFixed(2)}`;
            document.getElementById('total-amount').textContent = `₹${parseFloat(data.total_amount).toFixed(2)}`;
            
            // Load product details for summary
            Promise.all(cart.map(item => 
                fetch(`${API_BASE}/products?per_page=1000`)
                    .then(res => res.json())
                    .then(data => data.data.find(p => p.id === item.product_id))
            )).then(products => {
                const summary = document.getElementById('order-summary');
                summary.innerHTML = products.map((product, index) => {
                    if (!product) return '';
                    const item = cart[index];
                    const price = (product.discounted_price || product.selling_price) * item.quantity;
                    return `
                        <div class="flex items-center space-x-4 mb-4 pb-4 border-b">
                            <img src="/storage/${product.main_image}" alt="${product.name}" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h4 class="font-semibold text-sm">${product.name}</h4>
                                <p class="text-sm text-gray-600">Qty: ${item.quantity}</p>
                                <p class="text-sm font-semibold text-primary-600">₹${parseFloat(price).toFixed(2)}</p>
                            </div>
                        </div>
                    `;
                }).join('');
            });
        });
    }
    
    loadOrderSummary();
    
    // Handle form submission
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        // Append items as indexed fields so Laravel receives them as arrays
        cart.forEach((item, idx) => {
            formData.append(`items[${idx}][product_id]`, item.product_id);
            formData.append(`items[${idx}][quantity]`, item.quantity);
            if (item.attributes) {
                item.attributes.forEach((attr, aidx) => {
                    formData.append(`items[${idx}][attributes][${aidx}][name]`, attr.name);
                    formData.append(`items[${idx}][attributes][${aidx}][value]`, attr.value);
                });
            }
        });

        fetch(`${API_BASE}/checkout/place-order`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.order) {
                const orderNumber = data.order.order_number;
                localStorage.removeItem('cart');
                updateCartCount();
                // redirect to order success page which will show modal with order number
                window.location.href = '/order/success/' + encodeURIComponent(orderNumber);
            } else {
                const err = data.error || 'Failed to place order';
                if (window.showModal) {
                    showModal('Error', err, 'error');
                } else {
                    alert('Error: ' + err);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
    }
});
</script>
@endsection

