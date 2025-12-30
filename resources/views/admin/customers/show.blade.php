@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-heading">Customer Details</h1>
            <p class="text-text-muted mt-1">View customer information and order history</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.customers.edit', $customer) }}" 
               class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Customer
            </a>
            <a href="{{ route('admin.customers.index') }}" 
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Customers
            </a>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Customer Profile -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="text-center mb-6">
                    <div class="h-20 w-20 rounded-full bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold text-2xl">{{ substr($customer->name, 0, 1) }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-text-heading">{{ $customer->name }}</h2>
                    <p class="text-text-muted">Customer ID: #{{ $customer->id }}</p>
                    <div class="mt-3">
                    @if($customer->is_verified)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Verified Customer
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Unverified Customer
                        </span>
                    @endif
                </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="text-sm text-text-muted">Email</div>
                        <div class="font-medium text-text-heading">{{ $customer->email }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-text-muted">Mobile</div>
                        <div class="font-medium text-text-heading">{{ $customer->mobile }}</div>
                    </div>
                    @if($customer->addresses->where('is_default', true)->first())
                        <div>
                            <div class="text-sm text-text-muted">Address</div>
                            <div class="font-medium text-text-heading">
                                {{ $customer->addresses->where('is_default', true)->first()->address }}
                                @if($customer->addresses->where('is_default', true)->first()->city)
                                    {{ $customer->addresses->where('is_default', true)->first()->city }},
                                @endif
                                @if($customer->addresses->where('is_default', true)->first()->state)
                                    {{ $customer->addresses->where('is_default', true)->first()->state }}
                                @endif
                                @if($customer->addresses->where('is_default', true)->first()->pincode)
                                    - {{ $customer->addresses->where('is_default', true)->first()->pincode }}
                                @endif
                                @if($customer->addresses->where('is_default', true)->first()->country)
                                    {{ $customer->addresses->where('is_default', true)->first()->country }}
                                @endif
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="text-sm text-text-muted">Customer Since</div>
                        <div class="font-medium text-text-heading">{{ $customer->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.customers.toggle-status', $customer) }}" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-3 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300 text-sm"
                                    onclick="return confirm('Are you sure you want to {{ $customer->is_verified ? 'unverify' : 'verify' }} this customer?')">
                                {{ $customer->is_verified ? 'Unverify' : 'Verify' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Statistics & Recent Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Statistics Overview -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-text-heading mb-4">Customer Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-brand-gold/10 to-brand-amber/10 rounded-lg p-4 border border-brand-gold/20">
                        <div class="text-sm text-text-muted">Total Orders</div>
                        <div class="text-2xl font-bold text-brand-gold">{{ $statistics['total_orders'] }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-brand-gold/10 to-brand-amber/10 rounded-lg p-4 border border-brand-gold/20">
                        <div class="text-sm text-text-muted">Total Spent</div>
                        <div class="text-2xl font-bold text-brand-gold">₹{{ number_format($statistics['total_spent'], 2) }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-brand-gold/10 to-brand-amber/10 rounded-lg p-4 border border-brand-gold/20">
                        <div class="text-sm text-text-muted">Avg Order Value</div>
                        <div class="text-2xl font-bold text-brand-gold">₹{{ number_format($statistics['average_order_value'], 2) }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-brand-gold/10 to-brand-amber/10 rounded-lg p-4 border border-brand-gold/20">
                        <div class="text-sm text-text-muted">Total Queries</div>
                        <div class="text-2xl font-bold text-brand-gold">{{ $statistics['total_queries'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Order Status Breakdown -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-text-heading mb-4">Order Status</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="text-sm text-green-600">Completed Orders</div>
                        <div class="text-xl font-bold text-green-700">{{ $statistics['completed_orders'] }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="text-sm text-yellow-600">Pending Orders</div>
                        <div class="text-xl font-bold text-yellow-700">{{ $statistics['pending_orders'] }}</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="text-sm text-blue-600">Pending Queries</div>
                        <div class="text-xl font-bold text-blue-700">{{ $statistics['pending_queries'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-text-heading">Order History</h3>
            <p class="text-text-muted mt-1">View all orders placed by this customer</p>
        </div>
        <div class="overflow-x-auto">
            @if($customer->orders->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Items</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customer->orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-text-heading">#{{ $order->order_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-text-heading">{{ $order->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-text-heading">{{ $order->items->count() }} items</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-brand-gold">₹{{ number_format($order->total_amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($order->order_status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->order_status === 'cancelled') bg-red-100 text-red-800
                                        @elseif($order->order_status === 'shipped') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="text-brand-gold hover:text-brand-amber transition-colors" title="View Order">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order->order_number) }}" 
                                           class="text-brand-gold hover:text-brand-amber transition-colors" title="View Invoice" target="_blank">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <p class="text-lg font-medium text-gray-900 mb-2">No orders found</p>
                        <p class="text-sm text-gray-500">This customer hasn't placed any orders yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Customer Queries -->
    @if($customer->queries->count() > 0)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mt-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-text-heading">Customer Queries</h3>
                <p class="text-text-muted mt-1">Recent queries from this customer</p>
            </div>
            <div class="p-6 space-y-4">
                @foreach($customer->queries->take(5) as $query)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-text-heading">{{ $query->subject }}</h4>
                                <p class="text-sm text-text-muted mt-1">{{ Str::limit($query->message, 100) }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-xs text-text-muted">{{ $query->created_at->format('M d, Y H:i') }}</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        @if($query->status === 'resolved') bg-green-100 text-green-800
                                        @elseif($query->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($query->status) }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('admin.queries.index') }}#query-{{ $query->id }}" 
                               class="text-brand-gold hover:text-brand-amber transition-colors ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
