@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson rounded-2xl px-6 py-5 text-white flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold uppercase">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div>
            <div class="text-lg font-bold">Welcome, {{ $user->name }}</div>
            <div class="text-sm text-white/80 capitalize">{{ $user->role }} &bull; {{ $user->mobile }}</div>
        </div>
    </div>

    {{-- KPI cards --}}
    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
        {{-- Total Orders --}}
        <div class="admin-card flex items-center gap-4" style="flex:1;min-width:180px;">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold tracking-wide">My Orders</div>
            </div>
        </div>

        {{-- Lifetime Earnings --}}
        <div class="admin-card flex items-center gap-4" style="flex:1;min-width:180px;">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-amber-600">&#8377;{{ number_format($lifetimeEarnings, 2) }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Lifetime Earnings</div>
            </div>
        </div>

        {{-- Available Balance --}}
        <div class="admin-card flex items-center gap-4" style="flex:1;min-width:180px;">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">&#8377;{{ number_format($walletBalance, 2) }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Available Balance</div>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="admin-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-gold hover:underline font-medium">View All</a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="text-center py-10 text-gray-400 text-sm">No orders yet.</div>
        @else
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Wallet Used</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td class="font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-gold hover:underline">{{ $order->order_number }}</a>
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>&#8377;{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @if((float)$order->wallet_used > 0)
                                <span class="text-green-700 font-semibold">&#8377;{{ number_format($order->wallet_used, 2) }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $order->payment_method === 'cod' ? 'bg-green-100 text-green-800' : ($order->payment_method === 'razorpay' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </td>
                        <td>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $order->order_status === 'delivered' ? 'bg-green-100 text-green-800' : ($order->order_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->order_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Wallet request --}}
    @if($walletBalance > 0)
    <div class="admin-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Withdrawal</h3>
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('affiliate.wallet-request.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Amount (max &#8377;{{ number_format($walletBalance, 2) }})</label>
                <input type="number" name="amount" min="1" step="0.01" max="{{ $walletBalance }}" required
                    placeholder="0.00"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-44 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Remark (optional)</label>
                <input type="text" name="remark" placeholder="e.g. Bank transfer"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
            </div>
            <button type="submit"
                class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-5 py-2 rounded-lg font-semibold text-sm hover:shadow-lg transition-all duration-300">
                Request
            </button>
        </form>
        <p class="text-xs text-gray-400 mt-2">You can only request up to your available wallet balance. Admin will approve your request.</p>
    </div>
    @endif

</div>
@endsection
