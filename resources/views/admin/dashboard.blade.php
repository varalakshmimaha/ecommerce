@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-3xl font-bold text-text-heading">Dashboard</h1>
        <p class="text-text-muted mt-1 text-sm">Welcome back! Here's an overview of your store.</p>
    </div>

    {{-- Row 1: Revenue + Orders --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(217,119,6,0.1);">
                <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Total Revenue</div>
                <div class="text-2xl font-bold text-text-heading tabular-nums mt-0.5">&#8377;{{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="text-xs text-text-muted mt-0.5">Verified payments</div>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(37,99,235,0.1);">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Total Orders</div>
                <div class="text-2xl font-bold text-text-heading tabular-nums mt-0.5">{{ number_format($stats['total_orders']) }}</div>
                <div class="text-xs mt-0.5">
                    <span class="text-amber-600 font-semibold">{{ $stats['pending_orders'] }} pending</span>
                    <span class="text-text-muted"> · </span>
                    <span class="text-green-600 font-semibold">{{ $stats['delivered_orders'] }} delivered</span>
                </div>
            </div>
        </div>

        {{-- Total Products --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(5,150,105,0.1);">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Products</div>
                <div class="text-2xl font-bold text-text-heading tabular-nums mt-0.5">{{ number_format($stats['total_products']) }}</div>
                <div class="text-xs text-text-muted mt-0.5">Active catalogue</div>
            </div>
        </div>

        {{-- Total Customers --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(124,58,237,0.1);">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Customers</div>
                <div class="text-2xl font-bold text-text-heading tabular-nums mt-0.5">{{ number_format($stats['total_customers']) }}</div>
                <div class="text-xs text-text-muted mt-0.5">Registered accounts</div>
            </div>
        </div>
    </div>

    {{-- Row 2: MLM Hierarchy + Wallet --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Affiliates --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(217,119,6,0.1);">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Affiliates</div>
                <div class="text-2xl font-bold text-text-heading tabular-nums mt-0.5">{{ number_format($stats['total_affiliates']) }}</div>
                <a href="{{ route('admin.affiliates.index') }}" class="text-xs text-brand-gold hover:underline mt-0.5 block">View all →</a>
            </div>
        </div>

        {{-- RMs --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(37,99,235,0.1);">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Rel. Managers</div>
                <div class="text-2xl font-bold text-text-heading tabular-nums mt-0.5">{{ number_format($stats['total_rms']) }}</div>
                <a href="{{ route('admin.rms.index') }}" class="text-xs text-brand-gold hover:underline mt-0.5 block">View all →</a>
            </div>
        </div>

        {{-- Managers --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(124,58,237,0.1);">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Managers</div>
                <div class="text-2xl font-bold text-text-heading tabular-nums mt-0.5">{{ number_format($stats['total_managers']) }}</div>
                <a href="{{ route('admin.managers.index') }}" class="text-xs text-brand-gold hover:underline mt-0.5 block">View all →</a>
            </div>
        </div>

        {{-- Total Available Balance --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(5,150,105,0.1);">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-text-muted uppercase font-bold tracking-wider">Total Available Balance</div>
                <div class="text-2xl font-bold text-green-600 tabular-nums mt-0.5">&#8377;{{ number_format($stats['total_wallet'], 2) }}</div>
                <div class="text-xs text-text-muted mt-0.5">Across all members</div>
            </div>
        </div>

    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl shadow-sm border border-ui-border overflow-hidden">
        <div class="px-5 py-4 border-b border-ui-border flex items-center justify-between">
            <h3 class="font-semibold text-text-heading flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Recent Orders
            </h3>
            <a href="{{ route('admin.orders.index') }}"
               class="text-xs font-semibold text-brand-gold hover:underline">View all →</a>
        </div>
        @if($recentOrders->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-text-muted text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold">Order #</th>
                            <th class="text-left px-4 py-3 font-semibold">Customer</th>
                            <th class="text-right px-4 py-3 font-semibold">Amount</th>
                            <th class="text-left px-4 py-3 font-semibold">Status</th>
                            <th class="text-left px-4 py-3 font-semibold">Payment</th>
                            <th class="text-left px-4 py-3 font-semibold">Date</th>
                            <th class="text-left px-4 py-3 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentOrders as $order)
                            @php
                                $statusColors = [
                                    'pending'    => 'bg-amber-100 text-amber-700',
                                    'confirmed'  => 'bg-blue-100 text-blue-700',
                                    'shipped'    => 'bg-indigo-100 text-indigo-700',
                                    'delivered'  => 'bg-green-100 text-green-700',
                                    'cancelled'  => 'bg-red-100 text-red-700',
                                ];
                                $payColors = [
                                    'verified'   => 'bg-green-100 text-green-700',
                                    'pending'    => 'bg-amber-100 text-amber-700',
                                    'failed'     => 'bg-red-100 text-red-700',
                                    'cod'        => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-text-heading font-semibold">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-text-heading text-xs">{{ $order->name }}</div>
                                    <div class="text-[10px] text-text-muted font-mono">{{ $order->mobile }}</div>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-semibold text-text-heading">&#8377;{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $payColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-text-muted text-xs">{{ $order->created_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-12 text-center text-sm text-text-muted">No orders yet.</div>
        @endif
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $quickLinks = [
                ['label' => 'Products',    'route' => 'admin.products.index',           'color' => '#7c3aed', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['label' => 'Orders',      'route' => 'admin.orders.index',             'color' => '#2563eb', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                ['label' => 'Customers',   'route' => 'admin.customers.index',          'color' => '#059669', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label' => 'Affiliates',  'route' => 'admin.affiliates.index',         'color' => '#d97706', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
                ['label' => 'Commissions', 'route' => 'admin.commissions.index',        'color' => '#0891b2', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Settings',    'route' => 'admin.settings.index',           'color' => '#b45309', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ];
        @endphp
        @foreach($quickLinks as $ql)
            <a href="{{ route($ql['route']) }}"
               class="bg-white rounded-xl shadow-sm border border-ui-border p-4 flex flex-col items-center gap-2 hover:shadow-md hover:-translate-y-0.5 transition-all group text-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(0,0,0,0.05);">
                    <svg class="w-5 h-5 transition-colors" fill="none" stroke="{{ $ql['color'] }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ql['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-text-heading">{{ $ql['label'] }}</span>
            </a>
        @endforeach
    </div>

</div>
@endsection
