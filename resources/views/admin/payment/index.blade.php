@extends('layouts.admin')

@section('title', 'Payment Settings')

@section('content')
<div class="space-y-6">
    <div class="admin-card">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Payment Methods</h2>
        
        <form action="{{ route('admin.payment.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Manual Payment -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-semibold">Manual Payment</h3>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">QR Code/UPI</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="payment_methods[manual][is_active]" 
                                   value="1" 
                                   {{ old('payment_methods.manual.is_active', $paymentSettings->where('payment_method', 'manual')->first()?->is_active ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        </label>
                        <span class="text-sm text-gray-600">Enable</span>
                    </div>
                </div>
                
                <div class="text-sm text-gray-600 mb-3">
                    Customers upload payment proof after transferring via QR code, UPI, or bank transfer.
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                        <input type="number" 
                               name="payment_methods[manual][sort_order]" 
                               value="{{ old('payment_methods.manual.sort_order', $paymentSettings->where('payment_method', 'manual')->first()?->sort_order ?? 1) }}" 
                               min="0" 
                               class="input-field">
                    </div>
                </div>
            </div>

            <!-- Cash on Delivery -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-semibold">Cash on Delivery (COD)</h3>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Pay on Delivery</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="payment_methods[cod][is_active]" 
                                   value="1" 
                                   {{ old('payment_methods.cod.is_active', $paymentSettings->where('payment_method', 'cod')->first()?->is_active ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        </label>
                        <span class="text-sm text-gray-600">Enable</span>
                    </div>
                </div>
                
                <div class="text-sm text-gray-600 mb-3">
                    Customers pay cash when the order is delivered.
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                        <input type="number" 
                               name="payment_methods[cod][sort_order]" 
                               value="{{ old('payment_methods.cod.sort_order', $paymentSettings->where('payment_method', 'cod')->first()?->sort_order ?? 2) }}" 
                               min="0" 
                               class="input-field">
                    </div>
                </div>
            </div>

            <!-- Razorpay -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-semibold">Razorpay</h3>
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Online Payment</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="payment_methods[razorpay][is_active]" 
                                   value="1" 
                                   {{ old('payment_methods.razorpay.is_active', $paymentSettings->where('payment_method', 'razorpay')->first()?->is_active ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        </label>
                        <span class="text-sm text-gray-600">Enable</span>
                    </div>
                </div>
                
                <div class="text-sm text-gray-600 mb-3">
                    Accept online payments via credit cards, debit cards, UPI, wallets, etc.
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Key ID</label>
                        <input type="text" 
                               name="razorpay_key_id" 
                               value="{{ old('razorpay_key_id', $paymentSettings->where('payment_method', 'razorpay')->first()?->settings['key_id'] ?? '') }}" 
                               placeholder="rzp_test_..." 
                               class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Key Secret</label>
                        <input type="password" 
                               name="razorpay_key_secret" 
                               value="{{ old('razorpay_key_secret', $paymentSettings->where('payment_method', 'razorpay')->first()?->settings['key_secret'] ?? '') }}" 
                               placeholder="Enter your Razorpay key secret" 
                               class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                        <input type="number" 
                               name="payment_methods[razorpay][sort_order]" 
                               value="{{ old('payment_methods.razorpay.sort_order', $paymentSettings->where('payment_method', 'razorpay')->first()?->sort_order ?? 3) }}" 
                               min="0" 
                               class="input-field">
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="button" 
                            onclick="testRazorpayConnection()" 
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                        Test Connection
                    </button>
                    <div id="razorpay-test-result" class="mt-2 text-sm"></div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Save Payment Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function testRazorpayConnection() {
    const resultDiv = document.getElementById('razorpay-test-result');
    resultDiv.innerHTML = '<div class="text-blue-600">Testing connection...</div>';
    
    fetch('{{ route("admin.payment.test-razorpay") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<div class="text-green-600">' + data.message + '</div>';
            } else {
                resultDiv.innerHTML = '<div class="text-red-600">' + data.message + '</div>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '<div class="text-red-600">Connection test failed</div>';
        });
}
</script>
@endsection
