@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-heading">Customers</h1>
            <p class="text-text-muted mt-1">Manage customer information and view order history</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.customers.export') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </a>
            <a href="{{ route('admin.customers.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Customer
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name, email, or mobile..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
                        <option value="">All Customers</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Sort By</label>
                    <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="total_orders" {{ request('sort_by') == 'total_orders' ? 'selected' : '' }}>Total Orders</option>
                        <option value="total_spent" {{ request('sort_by') == 'total_spent' ? 'selected' : '' }}>Total Spent</option>
                        <option value="last_order_at" {{ request('sort_by') == 'last_order_at' ? 'selected' : '' }}>Last Order</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                    Search
                </button>
                <a href="{{ route('admin.customers.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Total Spent</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Last Order</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-text-heading uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">{{ substr($customer->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-text-heading">{{ $customer->name }}</div>
                                        <div class="text-sm text-text-muted">ID: #{{ $customer->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-text-heading">{{ $customer->email }}</div>
                                <div class="text-sm text-text-muted">{{ $customer->mobile }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-text-heading">
                                    @if($customer->addresses()->where('is_default', true)->first())
                                        {{ $customer->addresses()->where('is_default', true)->first()->city }}
                                        @if($customer->addresses()->where('is_default', true)->first()->state)
                                            , {{ $customer->addresses()->where('is_default', true)->first()->state }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-text-heading">{{ $customer->orders()->count() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-brand-gold">₹{{ number_format($customer->orders()->sum('total_amount'), 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-text-heading">
                                    @php
                                        $lastOrder = $customer->orders()->max('created_at');
                                    @endphp
                                    {{ $lastOrder ? $lastOrder->format('M d, Y') : 'No orders' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($customer->is_verified)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Unverified
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.customers.show', $customer) }}" 
                                       class="text-brand-gold hover:text-brand-amber transition-colors" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" 
                                       class="text-brand-gold hover:text-brand-amber transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.customers.toggle-status', $customer) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-brand-gold hover:text-brand-amber transition-colors" 
                                                title="{{ $customer->is_verified ? 'Unverify' : 'Verify' }}"
                                                onclick="return confirm('Are you sure you want to {{ $customer->is_verified ? 'unverify' : 'verify' }} this customer?')">
                                            @if($customer->is_verified)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-2">No customers found</p>
                                    <p class="text-sm text-gray-500 mb-4">Get started by creating your first customer.</p>
                                    <a href="{{ route('admin.customers.create') }}" 
                                       class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300 inline-block">
                                        Add Customer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
