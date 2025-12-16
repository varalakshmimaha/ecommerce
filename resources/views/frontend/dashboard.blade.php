@extends('layouts.frontend')

@section('title', 'My Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
    <div class="container mx-auto px-4 py-12">
        <!-- Welcome Header -->
        <div class="mb-8 animate-slide-down">
            <h1 class="text-4xl font-bold text-[#1A1A1A] mb-2">Welcome back, <span class="text-[#D4AF37]">{{ $user->name ?? 'User' }}</span></h1>
            <p class="text-[#6B6B6B]">Manage your profile, orders, and queries from your dashboard</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8 overflow-x-auto">
            <nav class="flex space-x-2 bg-white p-2 rounded-xl shadow-md min-w-max border border-gray-100">
                <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white shadow-md">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </span>
                </button>
                <button onclick="switchTab('orders')" id="tab-orders" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 text-[#6B6B6B] hover:text-[#D4AF37] hover:bg-gray-50">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        My Orders
                    </span>
                </button>
                <button onclick="switchTab('queries')" id="tab-queries" class="tab-btn px-6 py-3 rounded-lg font-semibold transition-all duration-300 text-[#6B6B6B] hover:text-[#D4AF37] hover:bg-gray-50">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        My Queries
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
                        <div class="p-3 bg-gradient-to-br from-[#D4AF37] to-[#B8962E] rounded-lg">
                            <svg class="w-6 h-6 text-[#1A1A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-[#1A1A1A]">Update Profile</h2>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-4 animate-slide-down">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4 animate-slide-down">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.dashboard.profile.update') }}" class="space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#1A1A1A]">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#1A1A1A]">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#1A1A1A]">Mobile</label>
                            <input type="text" value="{{ $user->mobile }}" readonly
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-[#6B6B6B] cursor-not-allowed">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-[#1A1A1A] py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 transform transition-all duration-300 hover:shadow-xl ">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-gradient-to-br from-[#D4AF37] to-[#B8962E] rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-[#1A1A1A]">Change Password</h2>
                    </div>
                    <form method="POST" action="{{ route('user.dashboard.password.update') }}" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#1A1A1A]">Current Password</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#1A1A1A]">New Password</label>
                            <input type="password" name="new_password" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#1A1A1A]">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                            Change Password
                        </button>
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
                                    <div class="p-2 bg-gradient-to-br from-[#D4AF37] to-[#B8962E] rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-[#1A1A1A]">Order #{{ $order->order_number }}</h3>
                                </div>
                                <p class="text-sm text-[#6B6B6B]">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-left lg:text-right">
                                <p class="text-3xl font-bold text-[#D4AF37]">₹{{ number_format($order->total_amount, 2) }}</p>
                                <span class="inline-block px-4 py-1 text-sm rounded-full mt-2 {{ $order->order_status === 'delivered' ? 'bg-green-50 text-green-600 border border-green-200' : ($order->order_status === 'cancelled' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-yellow-50 text-yellow-600 border border-yellow-200') }}">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('user.dashboard.invoice', $order->order_number) }}" target="_blank" class="flex items-center gap-2 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 transform hover:shadow-xl">
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
                        <p class="text-[#6B6B6B] text-lg">You have no orders yet.</p>
                        <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 transform hover:shadow-xl">
                            Start Shopping
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Queries Tab -->
        <div id="content-queries" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Create Query Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-4 animate-slide-right">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-gradient-to-br from-[#D4AF37] to-[#B8962E] rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-[#1A1A1A]">Create New Query</h2>
                        </div>
                        <form method="POST" action="{{ route('user.dashboard.queries.create') }}" class="space-y-4">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-[#1A1A1A]">Subject</label>
                                <input type="text" name="subject" required class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-[#1A1A1A]">Order Number (Optional)</label>
                                <input type="text" name="order_number" placeholder="e.g., ORD-12345" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-[#1A1A1A]">Message</label>
                                <textarea name="message" required rows="4" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                Submit Query
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Queries List -->
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-[#1A1A1A] mb-6 flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-[#D4AF37] to-[#B8962E] rounded-lg">
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
                                        <h3 class="text-lg font-bold text-[#1A1A1A]">{{ $query->subject }}</h3>
                                        @if($query->order_number)
                                            <p class="text-sm text-[#D4AF37]">Order: {{ $query->order_number }}</p>
                                        @endif
                                        <p class="text-sm text-[#6B6B6B]">{{ $query->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <span class="inline-block px-3 py-1 text-sm rounded-full {{ $query->status === 'resolved' ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-yellow-50 text-yellow-600 border border-yellow-200' }}">
                                        {{ ucfirst($query->status) }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <p class="text-[#6B6B6B]">{{ $query->message }}</p>
                                </div>
                                @if($query->admin_response)
                                    <div class="border-t border-gray-100 pt-3 mt-3">
                                        <p class="text-sm font-semibold text-[#D4AF37] mb-1">Admin Response:</p>
                                        <p class="text-[#6B6B6B]">{{ $query->admin_response }}</p>
                                        @if($query->resolved_at)
                                            <p class="text-xs text-[#6B6B6B]/70 mt-1">Resolved on {{ $query->resolved_at->format('M d, Y h:i A') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-12 text-center animate-fade-in">
                                <svg class="w-16 h-16 mx-auto mb-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <p class="text-[#6B6B6B] text-lg">You have no queries yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-right {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-down {
    animation: slide-down 0.6s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.6s ease-out;
}

.animate-slide-right {
    animation: slide-right 0.6s ease-out;
}
</style>

<script>
// Tab Switching
function switchTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-gradient-to-r', 'from-[#D4AF37]', 'to-[#B8962E]', 'text-white', 'shadow-lg');
        btn.classList.add('text-[#6B6B6B]');
    });

    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.classList.add('bg-gradient-to-r', 'from-[#D4AF37]', 'to-[#B8962E]', 'text-white', 'shadow-lg');
    activeBtn.classList.remove('text-[#6B6B6B]');
}
</script>
@endsection
