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
                    <th>Amount</th>
                    @if(isset($isNonAdmin) && $isNonAdmin)
                    <th>Wallet Used</th>
                    <th>Payment Type</th>
                    @endif
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="font-medium">{{ $order->order_number }}</td>
                    @if(!isset($isNonAdmin) || !$isNonAdmin)
                    <td>{{ $order->name }}</td>
                    <td>{{ $order->mobile }}</td>
                    @endif
                    <td>&#8377;{{ number_format($order->total_amount, 2) }}</td>
                    @if(isset($isNonAdmin) && $isNonAdmin)
                    <td>
                        @if((float)$order->wallet_used > 0)
                            <span class="text-green-700 font-semibold">&#8377;{{ number_format($order->wallet_used, 2) }}</span>
                        @else
                            <span class="text-gray-400">&#8212;</span>
                        @endif
                    </td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            {{ $order->payment_method === 'cod' ? 'bg-green-100 text-green-800' : ($order->payment_method === 'razorpay' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ strtoupper($order->payment_method) }}
                        </span>
                    </td>
                    @endif
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
                    <td colspan="8" class="text-center py-8 text-gray-500">No orders found</td>
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
