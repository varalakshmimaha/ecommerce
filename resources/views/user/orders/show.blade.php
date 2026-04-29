@extends('layouts.frontend')

@section('title', 'Order Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Order Details</h1>
                <p class="mt-1 text-sm text-gray-500">Track your order and manage your purchase.</p>
            </div>
            <a href="{{ route('user.dashboard.invoice', $order->order_number) }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm text-white shadow-md hover:shadow-lg transition-all duration-300"
               style="background:linear-gradient(to right,#f59e0b,#d97706,#dc2626);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Yesb Confident
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-8">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <li class="p-6 flex items-center space-x-6">
                            <img src="{{ $item->product->main_image ? asset('storage/' . $item->product->main_image) : 'https://via.placeholder.com/150' }}"
                                 alt="{{ $item->product->name }}" class="w-24 h-24 rounded-md object-cover">
                            <div class="flex-1">
                                <h3 class="text-base font-medium text-gray-900">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-medium text-gray-900">₹{{ number_format($item->price * $item->quantity, 2) }}</p>
                                <p class="text-sm text-gray-500">₹{{ number_format($item->price, 2) }} each</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Shipping Address</h2>
                    @if($order->addresses->first())
                    <address class="not-italic text-gray-600">
                        <p class="font-semibold">{{ $order->addresses->first()->name }}</p>
                        <p>{{ $order->addresses->first()->address }}</p>
                        <p>{{ $order->addresses->first()->city }}, {{ $order->addresses->first()->state }} - {{ $order->addresses->first()->pincode }}</p>
                        <p>Phone: {{ $order->addresses->first()->phone }}</p>
                    </address>
                    @else
                    <p class="text-gray-500">No shipping address found.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Order Number</dt>
                            <dd class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Order Date</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Payment Method</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->payment_method === 'razorpay' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->payment_method === 'cod' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->payment_method === 'manual' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ ucfirst($order->payment_method) }}
                                </span>
                            </dd>
                        </div>
                        @if($order->razorpay_payment_id)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Razorpay Payment ID</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->razorpay_payment_id }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->order_status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->order_status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !in_array($order->order_status, ['delivered', 'cancelled']) ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </dd>
                        </div>
                        @php
                            $walletUsed = (float) $order->wallet_used;
                            $remaining  = (float) ($order->remaining_payable ?? $order->total_amount);
                            $isVerified = $order->payment_status === 'verified';
                        @endphp
                        <div class="flex justify-between border-t border-gray-200 pt-4">
                            <dt class="text-sm text-gray-500">Order Total</dt>
                            <dd class="text-sm font-semibold text-gray-900">&#8377;{{ number_format($order->total_amount, 2) }}</dd>
                        </div>
                        @if($walletUsed > 0)
                        <div class="flex justify-between" style="color:#16a34a;">
                            <dt class="text-sm flex items-center gap-1 font-medium">
                                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                Wallet Points
                            </dt>
                            <dd class="text-sm font-semibold">-&#8377;{{ number_format($walletUsed, 2) }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between border-t border-gray-200 pt-3">
                            <dt class="text-base font-semibold text-gray-900">Amount to Pay</dt>
                            <dd class="text-base font-bold text-gray-900">&#8377;{{ number_format($remaining, 2) }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-gray-500">Payment</dt>
                            <dd>
                                @if($isVerified)
                                    <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">
                                        ✓ Collected
                                    </span>
                                @elseif($order->payment_status === 'rejected')
                                    <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;background:#fee2e2;color:#dc2626;">
                                        ✕ Rejected
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;background:#fef9c3;color:#854d0e;">
                                        ⏳ Pending
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                @if($order->order_status === 'pending' || $order->order_status === 'processing')
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <h2 class="text-lg font-medium text-red-900 mb-4">Cancel Order</h2>
                    <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        <div>
                            <label for="cancellation_remark" class="block text-sm font-medium text-gray-700">Reason for Cancellation</label>
                            <textarea name="cancellation_remark" id="cancellation_remark" rows="3" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this order?')" class="mt-4 w-full bg-red-600 border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Confirm Cancellation
                        </button>
                    </form>
                </div>
                @endif
                
                <div class="text-center">
                    <a href="{{ route('user.dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        &larr; Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection