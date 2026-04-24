@extends('layouts.frontend')

@section('title', 'My Dashboard')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
    <div class="container mx-auto px-4 py-12">
        <!-- Welcome Header -->
        <div class="mb-8 animate-slide-down">
            <h1 class="text-4xl font-bold text-text-heading mb-2">Welcome back, <span class="text-brand-gold">{{ $user->name ?? 'User' }}</span></h1>
            <p class="text-text-muted">Manage your profile, orders, addresses, and queries from your dashboard</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8 overflow-x-auto">
            <nav class="flex space-x-2 bg-white p-2 rounded-xl shadow-md min-w-max border border-gray-100">
                <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </span>
                </button>
                <button onclick="switchTab('orders')" id="tab-orders" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 text-text-muted hover:text-brand-gold hover:bg-gray-50">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        My Orders
                    </span>
                </button>
                <button onclick="switchTab('addresses')" id="tab-addresses" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 text-text-muted hover:text-brand-gold hover:bg-gray-50">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Addresses
                    </span>
                </button>
                <button onclick="switchTab('queries')" id="tab-queries" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 text-text-muted hover:text-brand-gold hover:bg-gray-50">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        My Queries
                    </span>
                </button>
                <button onclick="switchTab('referrals')" id="tab-referrals" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 text-text-muted hover:text-brand-gold hover:bg-gray-50">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        My Referrals / Earnings
                    </span>
                </button>
            </nav>
        </div>

        <!-- Profile Tab -->
        <div id="content-profile" class="tab-content animate-fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Update Profile -->
                <div class="bg-white rounded-2xl shadow-lg p-8 transform transition-all duration-300 hover:shadow-xl border border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-lg">
                            <svg class="w-6 h-6 text-[#1A1A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-text-heading">Update Profile</h2>
                    </div>
                    <form method="POST" action="{{ route('user.dashboard.profile.update') }}" class="space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#1A1A1A]">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-text-heading">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-text-heading">Mobile</label>
                            <input type="text" value="{{ $user->mobile }}" readonly class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-text-muted cursor-not-allowed">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Profile</button>
                    </form>
                </div>
                <!-- Change Password -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 transform transition-all duration-300 hover:shadow-xl ">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-text-heading">Change Password</h2>
                    </div>
                    <form method="POST" action="{{ route('user.dashboard.password.update') }}" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-text-heading">Current Password</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-text-heading">New Password</label>
                            <input type="password" name="new_password" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-text-heading">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Orders Tab -->
        <div id="content-orders" class="tab-content hidden">
            <div class="grid gap-6">
                @forelse($user->orders ?? [] as $order)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 transform transition-all duration-300 hover:shadow-xl  animate-slide-up">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-2 bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-text-heading">Order #{{ $order->order_number }}</h3>
                                </div>
                                <p class="text-sm text-text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-left lg:text-right">
                                <p class="text-3xl font-bold text-brand-gold">₹{{ number_format($order->total_amount, 2) }}</p>
                                <span class="inline-block px-4 py-1 text-sm rounded-full mt-2 {{ $order->order_status === 'delivered' ? 'bg-green-50 text-green-600 border border-green-200' : ($order->order_status === 'cancelled' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-yellow-50 text-yellow-600 border border-yellow-200') }}">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('user.orders.show', $order->id) }}" class="flex items-center gap-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                View
                            </a>
                            <a href="{{ route('user.dashboard.invoice', $order->order_number) }}" target="_blank" class="flex items-center gap-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 transform hover:shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download Invoice
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-12 text-center animate-fade-in">
                        <svg class="w-16 h-16 mx-auto mb-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-text-muted text-lg">You have no orders yet.</p>
                        <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 transform hover:shadow-xl">
                            Start Shopping
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
        <!-- Addresses Tab -->
        <div id="content-addresses" class="tab-content hidden">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 animate-fade-in">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-gradient-to-br from-brand-gold to-brand-amber rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-text-heading">My Addresses</h2>
                </div>
                <div class="mb-6">
                    <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300" onclick="showAddressForm()">Add New Address</button>
                </div>
                <div id="addresses-list">
                    <!-- Address list will be loaded here via AJAX or server-side include -->
                </div>
                <div id="address-form-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate-scale-in">
                        <button class="absolute top-2 right-2 text-brand-gold text-2xl font-bold" onclick="hideAddressForm()">&times;</button>
                        <h3 class="text-xl font-bold mb-4 text-text-heading">Add / Edit Address</h3>
                        <form id="address-form" class="space-y-4">
                            <input type="hidden" name="address_id" id="address_id">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-text-heading">Name</label>
                                    <input type="text" name="name" id="address_name" required class="input-field">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-text-heading">Phone</label>
                                    <input type="text" name="phone" id="address_phone" required class="input-field">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-text-heading">Address</label>
                                <input type="text" name="address" id="address_address" required class="input-field">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-text-heading">City</label>
                                    <input type="text" name="city" id="address_city" required class="input-field">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-text-heading">State</label>
                                    <input type="text" name="state" id="address_state" required class="input-field">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-text-heading">Pincode</label>
                                    <input type="text" name="pincode" id="address_pincode" required class="input-field">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-text-heading">Country</label>
                                    <input type="text" name="country" id="address_country" value="India" required class="input-field">
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_default" id="address_is_default" class="accent-brand-gold">
                                <label for="address_is_default" class="text-sm">Set as default address</label>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" class="px-6 py-2 rounded-lg border border-gray-200 text-text-heading hover:bg-gray-50" onclick="hideAddressForm()">Cancel</button>
                                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">Save Address</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Queries Tab -->
        <div id="content-queries" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Create Query Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-4 animate-slide-right">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-text-heading">Create New Query</h2>
                        </div>
                        <form method="POST" action="{{ route('user.dashboard.queries.create') }}" class="space-y-4">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-text-heading">Subject</label>
                                <input type="text" name="subject" required class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-text-heading">Order Number (Optional)</label>
                                <input type="text" name="order_number" placeholder="e.g., ORD-12345" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-text-heading">Message</label>
                                <textarea name="message" required rows="4" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Submit Query</button>
                        </form>
                    </div>
                </div>
                <!-- Queries List -->
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-text-heading mb-6 flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        My Queries
                    </h2>
                    <div class="space-y-4">
                        @forelse($user->queries ?? [] as $query)
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 animate-slide-up">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-text-heading">{{ $query->subject }}</h3>
                                        @if($query->order_number)
                                            <p class="text-sm text-brand-gold">Order: {{ $query->order_number }}</p>
                                        @endif
                                        <p class="text-sm text-text-muted">{{ $query->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <span class="inline-block px-3 py-1 text-sm rounded-full {{ $query->status === 'resolved' ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-yellow-50 text-yellow-600 border border-yellow-200' }}">
                                        {{ ucfirst($query->status) }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <p class="text-text-muted">{{ $query->message }}</p>
                                </div>
                                @if($query->admin_response)
                                    <div class="border-t border-gray-100 pt-3 mt-3">
                                        <p class="text-sm font-semibold text-brand-gold mb-1">Admin Response:</p>
                                        <p class="text-text-muted">{{ $query->admin_response }}</p>
                                        @if($query->resolved_at)
                                            <p class="text-xs text-text-muted/70 mt-1">Resolved on {{ $query->resolved_at->format('M d, Y h:i A') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-12 text-center animate-fade-in">
                                <svg class="w-16 h-16 mx-auto mb-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <p class="text-text-muted text-lg">You have no queries yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Referrals & Earnings Tab -->
        <div id="content-referrals" class="tab-content hidden">
            <div class="space-y-6">
                {{-- Referral code card (shown at the top) --}}
                @if($referralLink)
                    <div class="bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-2xl shadow-lg p-6 text-white">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wider opacity-80 font-semibold mb-1">Your Referral Code</div>
                                <div class="font-mono font-bold text-3xl">{{ $user->referral_code }}</div>
                                <div class="text-sm opacity-90 mt-3 truncate">{{ $referralLink }}</div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="(function(btn){navigator.clipboard.writeText('{{ $referralLink }}'); const t=btn.innerText; btn.innerText='Copied!'; setTimeout(()=>btn.innerText=t,1500);})(this)"
                                        class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-semibold backdrop-blur border border-white/20">Copy Link</button>
                                <a href="https://wa.me/?text={{ urlencode('Join via my referral link: ' . $referralLink) }}" target="_blank" rel="noopener" class="bg-white text-brand-crimson hover:bg-white/90 px-4 py-2 rounded-lg text-sm font-semibold">WhatsApp</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(in_array($user->role, ['affiliate','rm','manager']))
                    {{-- Earnings KPI strip --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-4 overflow-hidden">
                            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#ea580c;"></div>
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:#ffedd5;">
                                    <svg class="w-4 h-4" style="color:#c2410c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#9a3412;">Lifetime</div>
                            </div>
                            <div class="text-xl font-bold text-text-heading tabular-nums mt-2">&#8377;{{ number_format($commissionTotals['lifetime'], 2) }}</div>
                        </div>
                        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-4 overflow-hidden">
                            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#10b981;"></div>
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:#d1fae5;">
                                    <svg class="w-4 h-4" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#047857;">Paid</div>
                            </div>
                            <div class="text-xl font-bold text-text-heading tabular-nums mt-2">&#8377;{{ number_format($commissionTotals['paid'], 2) }}</div>
                        </div>
                        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-4 overflow-hidden">
                            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#f59e0b;"></div>
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:#fef3c7;">
                                    <svg class="w-4 h-4" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#b45309;">Pending</div>
                            </div>
                            <div class="text-xl font-bold text-text-heading tabular-nums mt-2">&#8377;{{ number_format($commissionTotals['pending'], 2) }}</div>
                        </div>
                        <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-4 overflow-hidden">
                            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:#3b82f6;"></div>
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:#dbeafe;">
                                    <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div class="text-[11px] font-bold uppercase tracking-wider" style="color:#1d4ed8;">Approved</div>
                            </div>
                            <div class="text-xl font-bold text-text-heading tabular-nums mt-2">&#8377;{{ number_format($commissionTotals['approved'], 2) }}</div>
                        </div>
                    </div>

                    @php
                        $myRate = ['affiliate' => 5, 'rm' => 3, 'manager' => 2][$user->role] ?? 0;
                        $referralsWithActivity = $referrals->filter(fn($r) => ($r->orders_count ?? 0) > 0 || ($baseByReferral[$r->id] ?? 0) > 0);
                    @endphp

                    {{-- My Referrals --}}
                    @if($referralsWithActivity->count())
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-xl shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-text-heading leading-tight">My Referrals</h2>
                                    <p class="text-xs text-text-muted">Direct referrals earning you {{ $myRate }}% commission</p>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-text-muted text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="text-left px-5 py-3 font-semibold">Name</th>
                                        <th class="text-left px-5 py-3 font-semibold">Date</th>
                                        <th class="text-left px-5 py-3 font-semibold">Role</th>
                                        <th class="text-right px-5 py-3 font-semibold">Base</th>
                                        <th class="text-right px-5 py-3 font-semibold">Rate</th>
                                        <th class="text-right px-5 py-3 font-semibold">Orders</th>
                                        <th class="text-right px-5 py-3 font-semibold">Amount</th>
                                        <th class="text-center px-5 py-3 font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($referrals as $r)
                                        @php
                                            $base = (float) ($baseByReferral[$r->id] ?? 0);
                                            $comm = (float) ($commByReferral[$r->id] ?? 0);
                                            $ordCount = $r->orders_count ?? 0;
                                            $initial = strtoupper(mb_substr($r->name ?? 'U', 0, 1));
                                        @endphp
                                        <tr class="hover:bg-gray-50/70 align-middle">
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-brand-gold/10 text-brand-gold flex items-center justify-center text-sm font-bold shrink-0">{{ $initial }}</div>
                                                    <div class="min-w-0">
                                                        <div class="font-semibold text-text-heading">{{ $r->name ?? 'User #'.$r->id }}</div>
                                                        @if($r->referral_code)<div class="text-xs text-text-muted font-mono">{{ $r->referral_code }}</div>@endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3 text-text-muted text-xs">{{ $r->created_at?->format('d M Y') }}</td>
                                            <td class="px-5 py-3 uppercase text-xs font-semibold">{{ $r->role }}</td>
                                            <td class="px-5 py-3 text-right tabular-nums {{ $base > 0 ? 'text-text-heading' : 'text-gray-400' }}">&#8377;{{ number_format($base, 2) }}</td>
                                            <td class="px-5 py-3 text-right tabular-nums text-xs text-text-muted">{{ $myRate }}%</td>
                                            <td class="px-5 py-3 text-right tabular-nums">{{ $ordCount }}</td>
                                            <td class="px-5 py-3 text-right tabular-nums {{ $comm > 0 ? 'text-brand-gold font-bold' : 'text-gray-400' }}">&#8377;{{ number_format($comm, 2) }}</td>
                                            <td class="px-5 py-3 text-center">
                                                <a href="{{ route('user.dashboard.referral.show', $r) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-brand-gold/10 text-brand-gold hover:bg-brand-gold/20 text-xs font-semibold transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                @else
                    {{-- "Referred by" note + Become an Affiliate CTA for customers --}}
                    @if(!empty($upline))
                        @php $referrer = $upline[0]; @endphp
                        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-brand-gold/10 flex items-center justify-center text-brand-gold font-bold">
                                    {{ strtoupper(substr($referrer->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs text-text-muted uppercase tracking-wide font-semibold">Referred by</div>
                                    <div class="font-semibold text-text-heading">{{ $referrer->name ?? 'Affiliate #'.$referrer->id }}
                                        @if($referrer->referral_code)
                                            <span class="ml-2 text-xs text-text-muted font-mono font-normal">{{ $referrer->referral_code }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Become an Affiliate CTA --}}
                    <div class="bg-gradient-to-br from-brand-gold via-brand-amber to-brand-crimson rounded-2xl shadow-lg p-6 text-white">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                <div class="p-3 bg-white/20 rounded-xl ring-2 ring-white/30 shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs uppercase tracking-wider opacity-80 font-semibold mb-1">Earn With Us</div>
                                    <h3 class="text-xl font-bold">Become an Affiliate</h3>
                                    <p class="text-sm opacity-90 mt-1">Get your own referral code and earn <strong>5% commission</strong> on every sale from people you refer.</p>
                                </div>
                            </div>
                            <a href="{{ route('become.affiliate') }}" class="bg-white text-brand-crimson hover:bg-white/90 px-6 py-3 rounded-lg font-bold text-sm inline-flex items-center gap-2 shadow-md whitespace-nowrap">
                                Apply Now
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Main script for dashboard functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Load addresses when the page loads if the address tab content exists
        if (document.getElementById('content-addresses')) {
            loadAddresses();
        }
    });

    // Function to load addresses via AJAX
    function loadAddresses() {
        fetch('/dashboard/addresses', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                const list = document.getElementById('addresses-list');
                if(list) list.innerHTML = html;
            });
    }

    // Function to show the address form modal (for adding or editing)
    function showAddressForm(address = null) {
        const modal = document.getElementById('address-form-modal');
        if(!modal) return;
        modal.classList.remove('hidden');

        const form = document.getElementById('address-form');
        if(!form) return;

        if (address && typeof address === 'object') {
            document.getElementById('address_id').value = address.id || '';
            document.getElementById('address_name').value = address.name || '';
            document.getElementById('address_phone').value = address.phone || '';
            document.getElementById('address_address').value = address.address || '';
            document.getElementById('address_city').value = address.city || '';
            document.getElementById('address_state').value = address.state || '';
            document.getElementById('address_pincode').value = address.pincode || '';
            document.getElementById('address_country').value = address.country || '';
            document.getElementById('address_is_default').checked = !!address.is_default;
        } else {
            form.reset();
            document.getElementById('address_id').value = '';
        }
    }

    // Function to hide the address form modal
    function hideAddressForm() {
        const modal = document.getElementById('address-form-modal');
        if(modal) modal.classList.add('hidden');
    }

    // Function to handle the edit address action
    function editAddress(id, address) {
        showAddressForm(address);
    }

    // Event listener for form submissions
    document.addEventListener('submit', function(e) {
        // Handle Add/Edit Address form submission
        if (e.target && e.target.id === 'address-form') {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const id = document.getElementById('address_id').value;
            const url = id ? `/dashboard/addresses/${id}` : '/dashboard/addresses';
            const formData = new FormData(form);
            if (id) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (submitBtn) submitBtn.disabled = false;
                    if (data.success) {
                        hideAddressForm();
                        loadAddresses();
                        // You can add a success notification here
                    } else {
                        alert('Error saving address. Please check the form and try again.');
                    }
                })
                .catch(() => {
                    if (submitBtn) submitBtn.disabled = false;
                    alert('An unexpected error occurred. Please try again later.');
                });
        }

        // Handle Delete Address form submission
        if (e.target && e.target.classList.contains('delete-address-form')) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this address?')) {
                return;
            }

            const form = e.target;
            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                    },
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadAddresses();
                        alert('Address deleted successfully.');
                    } else {
                        alert('Error deleting address.');
                    }
                })
                .catch(() => alert('An unexpected error occurred.'));
        }

        // Handle Set Default Address form submission
        if (e.target && e.target.action && e.target.action.includes('/default')) {
            e.preventDefault();
            const form = e.target;
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadAddresses();
                }
            });
        }
    });

    // Tab Switching functionality
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-gradient-to-r', 'from-brand-gold', 'via-brand-amber', 'to-brand-crimson', 'text-white', 'shadow-lg');
            btn.classList.add('text-text-muted');
        });
        const content = document.getElementById('content-' + tab);
        if(content) content.classList.remove('hidden');

        const activeBtn = document.getElementById('tab-' + tab);
        if(activeBtn) {
            activeBtn.classList.add('bg-gradient-to-r', 'from-brand-gold', 'via-brand-amber', 'to-brand-crimson', 'text-white', 'shadow-lg');
            activeBtn.classList.remove('text-text-muted');
        }
    }

    // Making functions globally available
    window.switchTab = switchTab;
    window.showAddressForm = showAddressForm;
    window.hideAddressForm = hideAddressForm;
    window.editAddress = editAddress;
</script>
@endsection
