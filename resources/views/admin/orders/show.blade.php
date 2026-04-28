@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="space-y-6">
    <div class="admin-card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                View Invoice
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Customer Information</h3>
                <div class="space-y-2 text-sm">
                    @if($order->address_id && $order->address)
                        <p><span class="font-medium">Name:</span> {{ $order->addresses->name }}</p>
                        <p><span class="font-medium">Mobile:</span> {{ $order->addresses->phone }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Address:</span> {{ $order->addresses->address }}, {{ $order->addresses->city }}, {{ $order->addresses->state }} - {{ $order->addresses->pincode }}, {{ $order->addresses->country }}</p>
                        <p><span class="font-medium">Pincode:</span> {{ $order->addresses->pincode }}</p>
                    @else
                        <p><span class="font-medium">Name:</span> {{ $order->name }}</p>
                        <p><span class="font-medium">Mobile:</span> {{ $order->mobile }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Address:</span> {{ $order->address }}</p>
                        <p><span class="font-medium">Pincode:</span> {{ $order->pincode }}</p>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Order Information</h3>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium">Order Date:</span> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    <p><span class="font-medium">Payment Method:</span> 
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_method === 'razorpay' ? 'bg-blue-100 text-blue-800' : ($order->payment_method === 'cod' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ ucfirst($order->payment_method) }}
                        </span>
                    </p>
                    @if($order->razorpay_payment_id)
                    <p><span class="font-medium">Razorpay Payment ID:</span> {{ $order->razorpay_payment_id }}</p>
                    @endif
                    <p><span class="font-medium">Payment Status:</span> 
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_status === 'verified' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                    <p><span class="font-medium">Order Status:</span> 
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->order_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->order_status === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($order->order_status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </p>
                    @if($order->tracking_id)
                    <p><span class="font-medium">Tracking ID:</span> {{ $order->tracking_id }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        @if($order->payment_proof)
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Payment Proof</h3>
            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Payment Proof" class="max-w-md rounded-lg shadow-md">
        </div>
        @endif
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Order Items</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                              <img src="{{ asset('storage/' . $item->product->main_image) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-lg">
                              {{ $item->product_name }}
                            </td>
                            <td>₹{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        @php
            $walletUsed  = (float) $order->wallet_used;
            $remaining   = (float) ($order->remaining_payable ?? $order->total_amount);
            $isVerified  = $order->payment_status === 'verified';
        @endphp
        <div class="border-t pt-4">
            <div class="flex justify-end">
                <div class="w-72 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>&#8377;{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">GST</span>
                        <span>&#8377;{{ number_format($order->gst_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span>&#8377;{{ number_format($order->shipping_charge, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-base border-t pt-2">
                        <span>Order Total</span>
                        <span>&#8377;{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    @if($walletUsed > 0)
                    <div class="flex justify-between font-semibold" style="color:#16a34a;">
                        <span class="flex items-center gap-1">
                            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                            Wallet Points Applied
                        </span>
                        <span>-&#8377;{{ number_format($walletUsed, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-base border-t pt-2">
                        <span>{{ $walletUsed > 0 ? 'Amount to Pay' : 'Amount to Pay' }}</span>
                        <span>&#8377;{{ number_format($remaining, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-gray-500 text-xs">Payment Status</span>
                        @if($isVerified)
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">
                                ✓ Collected &#8377;{{ number_format($remaining, 2) }}
                            </span>
                        @elseif($order->payment_status === 'rejected')
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#fee2e2;color:#dc2626;">
                                ✕ Rejected
                            </span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#fef9c3;color:#854d0e;">
                                ⏳ Pending &#8377;{{ number_format($remaining, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Payment pending → Delivered block modal --}}
    <div id="payment-pending-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:32px 28px;max-width:420px;width:90%;text-align:center;">
            <div style="width:52px;height:52px;border-radius:50%;background:#fef9c3;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg style="width:28px;height:28px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <h3 style="font-size:18px;font-weight:700;color:#1f2937;margin-bottom:8px;">Payment Not Verified</h3>
            <p style="font-size:14px;color:#6b7280;margin-bottom:24px;line-height:1.6;">
                You cannot mark this order as <strong>Delivered</strong> while payment is still <strong>pending</strong>.<br>
                Please verify the payment first using the <em>Verify Payment</em> section below.
            </p>
            <button onclick="document.getElementById('payment-pending-modal').style.display='none';"
                style="background:linear-gradient(to right,#f59e0b,#d97706,#dc2626);color:#fff;border:none;padding:10px 28px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;">
                OK, Got It
            </button>
        </div>
    </div>

    <div class="admin-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Order Status</h3>
        @php $paymentVerified = $order->payment_status === 'verified'; $paymentRejected = $order->payment_status === 'rejected'; @endphp
        @if($paymentRejected)
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:16px;">
                <strong>Payment Rejected.</strong> The payment for this order has been rejected. Order status cannot be changed.
            </div>
        @else
        @if(!$paymentVerified)
            <div style="background:#fef9c3;border:1px solid #fde047;color:#854d0e;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">
                <strong>Note:</strong> Payment not yet verified. You can confirm or ship the order. To mark as <strong>Delivered</strong>, please verify payment first.
            </div>
        @endif
        <form id="order-status-form" action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                    <select id="order-status-select" name="order_status" class="input-field">
                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tracking ID</label>
                    <input type="text" name="tracking_id" value="{{ $order->tracking_id }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tracking URL</label>
                    <input type="url" name="tracking_url" value="{{ $order->tracking_url }}" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3" class="input-field">{{ $order->notes }}</textarea>
            </div>
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Status</button>
        </form>
        @endif
    </div>

    <script>
    (function () {
        var paymentVerified = {{ $paymentVerified ? 'true' : 'false' }};
        var form = document.getElementById('order-status-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                var sel = document.getElementById('order-status-select');
                if (!paymentVerified && sel && sel.value === 'delivered') {
                    e.preventDefault();
                    document.getElementById('payment-pending-modal').style.display = 'flex';
                }
            });
        }
        // Close modal on backdrop click
        var modal = document.getElementById('payment-pending-modal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) modal.style.display = 'none';
            });
        }
    })();
    </script>
    
    @if($order->payment_status === 'pending')
    <div class="admin-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Verify Payment</h3>
        <form action="{{ route('admin.orders.verify-payment', $order) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                <select name="payment_status" class="input-field">
                    <option value="verified">Verify Payment</option>
                    <option value="rejected">Reject Payment</option>
                </select>
            </div>
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Payment Status</button>
        </form>
    </div>
    @endif
</div>
@endsection

