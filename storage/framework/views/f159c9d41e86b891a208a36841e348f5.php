<?php $__env->startSection('title', 'Checkout'); ?>

<?php $__env->startSection('content'); ?>
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
                        <span id="total-amount" class="text-[#D4AF37]">₹0.00</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <?php if(auth()->guard()->check()): ?>
            <div class="card p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Select Shipping Address</h2>
                <div id="checkout-addresses-list">
                    <!-- Address selector will be loaded here -->
                </div>
                <button type="button" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300 mt-4" onclick="showCheckoutAddressForm()">Add New Address</button>
                <div id="checkout-address-form-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate-scale-in">
                        <button class="absolute top-2 right-2 text-[#D4AF37] text-2xl font-bold" onclick="hideCheckoutAddressForm()">&times;</button>
                        <h3 class="text-xl font-bold mb-4 text-[#1A1A1A]">Add / Edit Address</h3>
                        <form id="checkout-address-form" class="space-y-4">
                            <input type="hidden" name="address_id" id="checkout_address_id">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-[#1A1A1A]">Name</label>
                                    <input type="text" name="name" id="checkout_address_name" required class="input-field">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-[#1A1A1A]">Phone</label>
                                    <input type="text" name="phone" id="checkout_address_phone" required class="input-field">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-[#1A1A1A]">Address</label>
                                <input type="text" name="address" id="checkout_address_address" required class="input-field">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-[#1A1A1A]">City</label>
                                    <input type="text" name="city" id="checkout_address_city" required class="input-field">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-[#1A1A1A]">State</label>
                                    <input type="text" name="state" id="checkout_address_state" required class="input-field">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-[#1A1A1A]">Pincode</label>
                                    <input type="text" name="pincode" id="checkout_address_pincode" required class="input-field">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-[#1A1A1A]">Country</label>
                                    <input type="text" name="country" id="checkout_address_country" value="India" required class="input-field">
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_default" id="checkout_address_is_default" class="accent-[#D4AF37]">
                                <label for="checkout_address_is_default" class="text-sm">Set as default address</label>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" class="px-6 py-2 rounded-lg border border-gray-200 text-[#1A1A1A] hover:bg-gray-50" onclick="hideCheckoutAddressForm()">Cancel</button>
                                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">Save Address</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <form id="checkout-form" class="space-y-6">
                <?php if(auth()->guard()->guest()): ?>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input type="text" name="city" required class="input-field">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                        <input type="text" name="state" required class="input-field">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                        <input type="text" name="pincode" required class="input-field">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                        <input type="email" name="email" class="input-field">
                    </div>

                    <!-- Country fixed -->
                    <input type="hidden" name="country" value="India">
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Payment Section -->
                <div class="card p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Payment</h2>
                    <p class="text-gray-600 mb-4">Please make payment using one of the following methods:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">QR Code</h3>
                            <?php
                                $qrCode = \App\Models\Setting::get('qr_code');
                            ?>
                            <?php if($qrCode): ?>
                                <img src="<?php echo e(asset('storage/' . $qrCode)); ?>" alt="QR Code" class="w-48 h-48 object-contain border rounded-lg p-2">
                            <?php else: ?>
                                <p class="text-gray-500 text-sm">QR code not configured</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Bank Details</h3>
                            <div class="text-sm text-gray-700 space-y-1">
                                <?php if(\App\Models\Setting::get('bank_name')): ?>
                                <p><strong>Bank:</strong> <?php echo e(\App\Models\Setting::get('bank_name')); ?></p>
                                <?php endif; ?>
                                <?php if(\App\Models\Setting::get('bank_account_number')): ?>
                                <p><strong>Account Number:</strong> <?php echo e(\App\Models\Setting::get('bank_account_number')); ?></p>
                                <?php endif; ?>
                                <?php if(\App\Models\Setting::get('bank_ifsc')): ?>
                                <p><strong>IFSC:</strong> <?php echo e(\App\Models\Setting::get('bank_ifsc')); ?></p>
                                <?php endif; ?>
                                <?php if(\App\Models\Setting::get('bank_account_holder')): ?>
                                <p><strong>Account Holder:</strong> <?php echo e(\App\Models\Setting::get('bank_account_holder')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Proof *</label>
                        <input type="file" name="payment_proof" accept="image/*" required class="input-field">
                        <p class="text-xs text-gray-500 mt-1">Upload screenshot of payment transaction</p>
                    </div>
                </div>
                
                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full text-lg py-4">Place Order</button>
            </form>
        </div>
    </div>
</div>

<script>
<?php if(auth()->guard()->check()): ?>
// Checkout Address Management AJAX
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('checkout-addresses-list')) {
        loadCheckoutAddresses();
    }
});

let selectedAddressId = null;

function loadCheckoutAddresses() {
    fetch('/dashboard/addresses', {headers: {'X-Requested-With': 'XMLHttpRequest'}})
        .then(res => res.text())
        .then(html => {
            // Render as radio list for selection
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const addresses = Array.from(tempDiv.querySelectorAll('.bg-white.rounded-xl'));
            let listHtml = addresses.map((el, idx) => {
                const id = el.querySelector('button[onclick^="editAddress"]')?.getAttribute('onclick').match(/editAddress\((\d+),/)[1];
                const checked = el.innerHTML.includes('Default') || idx === 0 ? 'checked' : '';
                if (!selectedAddressId && checked) selectedAddressId = id;
                return `<label class="flex items-center gap-4 mb-4 p-4 border rounded-lg cursor-pointer hover:border-[#D4AF37] transition-all">
                    <input type="radio" name="shipping_address_id" value="${id}" ${checked} onchange="selectCheckoutAddress(${id})" class="accent-[#D4AF37] w-5 h-5">
                    <div class="flex-1">${el.innerHTML}</div>
                </label>`;
            }).join('');
            document.getElementById('checkout-addresses-list').innerHTML = listHtml;
        });
}

function selectCheckoutAddress(id) {
    selectedAddressId = id;
}

function showCheckoutAddressForm(address = null) {
    document.getElementById('checkout-address-form-modal').classList.remove('hidden');
    if (address) {
        document.getElementById('checkout_address_id').value = address.id;
        document.getElementById('checkout_address_name').value = address.name;
        document.getElementById('checkout_address_phone').value = address.phone;
        document.getElementById('checkout_address_address').value = address.address;
        document.getElementById('checkout_address_city').value = address.city;
        document.getElementById('checkout_address_state').value = address.state;
        document.getElementById('checkout_address_pincode').value = address.pincode;
        document.getElementById('checkout_address_country').value = address.country;
        document.getElementById('checkout_address_is_default').checked = !!address.is_default;
    } else {
        document.getElementById('checkout-address-form').reset();
        document.getElementById('checkout_address_id').value = '';
    }
}
function hideCheckoutAddressForm() {
    document.getElementById('checkout-address-form-modal').classList.add('hidden');
}

document.addEventListener('submit', function(e) {
    if (e.target && e.target.id === 'checkout-address-form') {
        e.preventDefault();
        const form = e.target;
        const id = document.getElementById('checkout_address_id').value;
        const url = id ? `/dashboard/addresses/${id}` : '/dashboard/addresses';
        const method = id ? 'PUT' : 'POST';
        const formData = new FormData(form);
        fetch(url, {
            method: method,
            headers: {'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hideCheckoutAddressForm();
                loadCheckoutAddresses();
            } else {
                alert('Error saving address');
            }
        });
    }
});
<?php endif; ?>
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
                                <p class="text-sm font-semibold text-[#D4AF37]">₹${parseFloat(price).toFixed(2)}</p>
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
        // If logged in, add selected address id
        if (typeof selectedAddressId !== 'undefined' && selectedAddressId) {
            formData.append('address_id', selectedAddressId);
        }
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
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/frontend/checkout.blade.php ENDPATH**/ ?>