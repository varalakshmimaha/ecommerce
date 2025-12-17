@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="space-y-6">
    <div class="admin-card">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Order #{{ $order->order_number }}</h2>
        
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
        
        @if($order->order_status === 'cancelled' && $order->cancellation_remark)
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Cancellation Remark</h3>
            <p class="text-sm text-gray-600">{{ $order->cancellation_remark }}</p>
        </div>
        @endif
        
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
                            <td>{{ $item->product_name }}</td>
                            <td>₹{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="border-t pt-4">
            <div class="flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>GST:</span>
                        <span>₹{{ number_format($order->gst_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping:</span>
                        <span>₹{{ number_format($order->shipping_charge, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span>₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if(!in_array($order->order_status, ['cancelled', 'delivered']))
    <div class="admin-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Order Status</h3>
        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                    <select name="order_status" class="input-field">
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
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Status</button>
        </form>
    </div>
    @endif
    
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
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Payment Status</button>
        </form>
    </div>
    @endif
</div>
@endsection

