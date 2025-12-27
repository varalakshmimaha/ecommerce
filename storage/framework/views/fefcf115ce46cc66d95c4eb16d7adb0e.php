<?php $__env->startSection('title', 'Checkout'); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php $__env->stopSection(); ?>

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
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Method</h2>
                    
                    <div id="payment-methods" class="space-y-4 mb-6">
                        <!-- Payment methods will be loaded here -->
                    </div>

                    <!-- Manual Payment Section (shown when manual is selected) -->
                    <div id="manual-payment-section" class="hidden space-y-4">
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h3 class="font-semibold text-gray-900 mb-2">
                              Open your payment app <br>
                              Scan this and pay <br>
                              <?php
                                $qrCode = \App\Models\Setting::get('qr_code');
                              ?>
                              <?php if($qrCode): ?>
                                  <img src="<?php echo e(asset('storage/' . $qrCode)); ?>" alt="QR Code" class="w-48 h-48 object-contain border rounded-lg p-2"><br>
                              <?php endif; ?>
                              Or <br>

                              <div class="flex items-center gap-2 mt-2">
                                  <!-- PhonePe -->
                                  <svg width="24" height="24" viewBox="0 0 24 24">
                                      <circle cx="12" cy="12" r="12" fill="#5F259F"/>
                                      <text x="12" y="16" text-anchor="middle" fill="white" font-size="12" font-weight="bold">P</text>
                                  </svg>

                                  <!-- Google Pay -->
                                  <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Google_Pay_Logo.svg"
                                       class="h-12 w-12" alt="Google Pay">

                                  <!-- Mobile Number -->
                                  <span id="mobileNumber" class="font-bold select-all">
                                      9916849109
                                  </span>
                                  &nbsp;
                                   <button
                                      onclick="copyMobileNumber()"
                                      type="button"
                                      class="text-gray-500 hover:text-gray-800 transition"
                                      title="Copy number"
                                  >
                                     Copy
                                  </button>
                              </div>
                          </h3>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Proof *</label>
                            <input type="file" name="payment_proof" accept="image/*" class="input-field">
                            <p class="text-xs text-gray-500 mt-1">Upload screenshot of payment transaction</p>
                        </div>
                    </div>

                    <!-- COD Section (shown when COD is selected) -->
                    <div id="cod-payment-section" class="hidden">
                        <div class="border rounded-lg p-4 bg-green-50">
                            <h3 class="font-semibold text-green-900 mb-2">Cash on Delivery</h3>
                            <p class="text-sm text-green-700">Pay cash when your order is delivered. No additional charges.</p>
                        </div>
                    </div>

                    <!-- Razorpay Section (shown when Razorpay is selected) -->
                    <div id="razorpay-payment-section" class="hidden">
                        <div class="border rounded-lg p-4 bg-purple-50">
                            <h3 class="font-semibold text-purple-900 mb-2">Online Payment</h3>
                            <p class="text-sm text-purple-700">Pay securely using credit card, debit card, UPI, wallets, etc.</p>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full text-lg py-4">Place Order</button>
            </form>
        </div>
    </div>
</div>

<script>
  function copyMobileNumber() {
        const number = document.getElementById('mobileNumber').innerText;

        navigator.clipboard.writeText(number).then(() => {
            alert('Mobile number copied!');
        }).catch(() => {
            alert('Failed to copy');
        });
    }

    // Payment methods functionality
    let selectedPaymentMethod = null;

    function loadPaymentMethods() {
        fetch('/api/payment-methods')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.methods.length > 0) {
                    renderPaymentMethods(data.methods);
                } else {
                    // Fallback to manual payment if no methods configured
                    renderPaymentMethods([{
                        method: 'manual',
                        name: 'Manual Payment',
                        description: 'Pay via QR code, UPI, or bank transfer'
                    }]);
                }
            })
            .catch(error => {
                console.error('Error loading payment methods:', error);
                // Fallback to manual payment
                renderPaymentMethods([{
                    method: 'manual',
                    name: 'Manual Payment',
                    description: 'Pay via QR code, UPI, or bank transfer'
                }]);
            });
    }

    function renderPaymentMethods(methods) {
        const container = document.getElementById('payment-methods');
        container.innerHTML = methods.map((method, index) => `
            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-[#D4AF37] transition-all">
                <input type="radio" 
                       name="payment_method" 
                       value="${method.method}" 
                       ${index === 0 ? 'checked' : ''}
                       onchange="selectPaymentMethod('${method.method}')"
                       class="accent-[#D4AF37] w-5 h-5">
                <div class="ml-4 flex-1">
                    <div class="font-semibold text-gray-900">${method.name}</div>
                    <div class="text-sm text-gray-600">${method.description}</div>
                </div>
            </label>
        `).join('');

        // Select first method by default
        if (methods.length > 0) {
            selectPaymentMethod(methods[0].method);
        }
    }

    function selectPaymentMethod(method) {
        selectedPaymentMethod = method;
        
        // Hide all payment sections
        document.getElementById('manual-payment-section').classList.add('hidden');
        document.getElementById('cod-payment-section').classList.add('hidden');
        document.getElementById('razorpay-payment-section').classList.add('hidden');
        
        // Show relevant section
        if (method === 'manual') {
            document.getElementById('manual-payment-section').classList.remove('hidden');
        } else if (method === 'cod') {
            document.getElementById('cod-payment-section').classList.remove('hidden');
        } else if (method === 'razorpay') {
            document.getElementById('razorpay-payment-section').classList.remove('hidden');
        }
    }
    
    // Checkout Address Management AJAX
    document.addEventListener('DOMContentLoaded', function() {
        // Load payment methods
        loadPaymentMethods();
        
        <?php if(auth()->guard()->check()): ?>
        if (document.getElementById('checkout-addresses-list')) {
            loadCheckoutAddresses();
        }
        <?php endif; ?>
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
        
        // Validate payment method selection
        if (!selectedPaymentMethod) {
            alert('Please select a payment method');
            return;
        }
        
        // For manual payment, validate payment proof
        if (selectedPaymentMethod === 'manual') {
            const paymentProof = document.querySelector('input[name="payment_proof"]');
            if (!paymentProof || !paymentProof.files.length) {
                alert('Please upload payment proof for manual payment');
                return;
            }
        }
        
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
        // Add selected payment method
        formData.append('payment_method', selectedPaymentMethod);
        
        // For Razorpay, handle payment flow
        if (selectedPaymentMethod === 'razorpay') {
            // Create Razorpay order first
            fetch(`${API_BASE}/checkout/create-razorpay-order`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    items: cart.map(item => ({
                        product_id: item.product_id,
                        quantity: item.quantity,
                        attributes: item.attributes || []
                    })),
                    address_id: typeof selectedAddressId !== 'undefined' ? selectedAddressId : null,
                    ...Object.fromEntries(formData.entries())
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    openRazorpayCheckout(data.razorpay_order_id, data.order_id, data.amount);
                } else {
                    alert('Error creating payment order: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        } else {
            // For manual and COD, proceed with normal order placement
            placeOrder(formData);
        }
    });
    
    function placeOrder(formData) {
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
    }
    
    function openRazorpayCheckout(razorpayOrderId, orderId, amount) {
        // Get Razorpay key from server
        fetch('/api/payment-methods')
            .then(response => response.json())
            .then(data => {
                const razorpayMethod = data.methods.find(m => m.method === 'razorpay');
                if (!razorpayMethod) {
                    alert('Razorpay is not available');
                    return;
                }

                // Get Razorpay key from settings
                const razorpayKey = '<?php echo e(\App\Models\PaymentSetting::getSettings("razorpay")["key_id"] ?? ""); ?>';
                
                if (!razorpayKey) {
                    alert('Razorpay is not configured properly');
                    return;
                }

                // Initialize Razorpay
                const options = {
                    key: razorpayKey,
                    amount: amount * 100, // Convert to paise
                    currency: 'INR',
                    name: 'Suvee',
                    description: 'Order Payment',
                    order_id: razorpayOrderId,
                    handler: function (response) {
                        // Payment successful
                        verifyRazorpayPayment(response.razorpay_payment_id, orderId, razorpayOrderId);
                    },
                    prefill: {
                        name: document.querySelector('input[name="name"]')?.value || '',
                        email: document.querySelector('input[name="email"]')?.value || '',
                        contact: document.querySelector('input[name="mobile"]')?.value || ''
                    },
                    theme: {
                        color: '#D4AF37'
                    },
                    modal: {
                        ondismiss: function() {
                            console.log('Razorpay modal closed');
                        }
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to initialize payment: ' + error.message);
            });
    }
    
    function verifyRazorpayPayment(paymentId, orderId, razorpayOrderId) {
        fetch('/api/checkout/verify-razorpay-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                razorpay_payment_id: paymentId,
                razorpay_order_id: razorpayOrderId,
                order_id: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                localStorage.removeItem('cart');
                updateCartCount();
                window.location.href = '/order/success/' + encodeURIComponent(data.order_number);
            } else {
                alert('Payment verification failed: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Payment verification failed');
        });
    }
    
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
    }
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suwish/resources/views/frontend/checkout.blade.php ENDPATH**/ ?>