@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="admin-card">
    <div class="mb-4 flex space-x-4">
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">All</a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'pending' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Pending</a>
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'confirmed' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Confirmed</a>
        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'shipped' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Shipped</a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'delivered' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Delivered</a>
    </div>

    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order Number</th>
                    @if(!isset($isNonAdmin) || !$isNonAdmin)
                    <th>Customer</th>
                    <th>Mobile</th>
                    @endif
                    <th>Order Total</th>
                    <th>Wallet Used</th>
                    <th>Collected / Pending</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                @php
                    $walletUsed      = (float) $order->wallet_used;
                    $remaining       = (float) ($order->remaining_payable ?? $order->total_amount);
                    $isVerified      = $order->payment_status === 'verified';
                @endphp
                <tr>
                    <td class="font-medium">{{ $order->order_number }}</td>
                    @if(!isset($isNonAdmin) || !$isNonAdmin)
                    <td>{{ $order->name }}</td>
                    <td>{{ $order->mobile }}</td>
                    @endif
                    <td class="font-semibold">&#8377;{{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        @if($walletUsed > 0)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                -&#8377;{{ number_format($walletUsed, 2) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">&#8212;</span>
                        @endif
                    </td>
                    <td>
                        @if($isVerified)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Collected &#8377;{{ number_format($remaining, 2) }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Pending &#8377;{{ number_format($remaining, 2) }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            {{ $order->payment_method === 'cod' ? 'bg-green-100 text-green-800' : ($order->payment_method === 'razorpay' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ strtoupper($order->payment_method) }}
                        </span>
                    </td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_status === 'verified' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->order_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->order_status === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($order->order_status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-gold hover:text-primary-800">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-8 text-gray-500">No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
