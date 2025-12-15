@extends('layouts.frontend')

@section('title', 'My Dashboard')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">My Dashboard</h1>

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b">
        <nav class="flex space-x-8">
            <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn py-4 px-1 border-b-2 border-blue-600 font-medium text-blue-600">
                Profile
            </button>
            <button onclick="switchTab('orders')" id="tab-orders" class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                My Orders
            </button>
            <button onclick="switchTab('queries')" id="tab-queries" class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                My Queries
            </button>
        </nav>
    </div>

    <!-- Profile Tab -->
    <div id="content-profile" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Update Profile -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Update Profile</h2>
                <form id="profile-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="profile-name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="profile-email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                        <input type="text" id="profile-mobile" name="mobile" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                    </div>
                    <div id="profile-message" class="hidden"></div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Change Password</h2>
                <form id="password-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" id="current-password" name="current_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="new-password" name="new_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" id="confirm-password" name="new_password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div id="password-message" class="hidden"></div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Orders Tab -->
    <div id="content-orders" class="tab-content hidden">
        <div id="orders-list" class="space-y-4">
            <p class="text-gray-500">Loading your orders...</p>
        </div>
    </div>

    <!-- Queries Tab -->
    <div id="content-queries" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Create Query Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">Create New Query</h2>
                    <form id="query-form" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" id="query-subject" name="subject" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Number (Optional)</label>
                            <input type="text" id="query-order" name="order_number" placeholder="e.g., ORD-12345" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea id="query-message" name="message" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        <div id="query-message-alert" class="hidden"></div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                            Submit Query
                        </button>
                    </form>
                </div>
            </div>

            <!-- Queries List -->
            <div class="lg:col-span-2">
                <h2 class="text-xl font-semibold mb-4">My Queries</h2>
                <div id="queries-list" class="space-y-4">
                    <p class="text-gray-500">Loading your queries...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="order-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold">Order Details</h2>
                <button onclick="closeOrderModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="order-details-content"></div>
        </div>
    </div>
</div>

<script>
const API_BASE = '/api';
const token = localStorage.getItem('auth_token');

if (!token) {
    window.location.href = '/user/login';
}

// Tab Switching
function switchTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');

    // Load data for specific tabs
    if (tab === 'orders') {
        loadOrders();
    } else if (tab === 'queries') {
        loadQueries();
    }
}

// Load Profile
function loadProfile() {
    fetch(`${API_BASE}/user/profile`, {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('profile-name').value = data.data.name;
            document.getElementById('profile-email').value = data.data.email;
            document.getElementById('profile-mobile').value = data.data.mobile;
        }
    })
    .catch(err => console.error(err));
}

// Update Profile
document.getElementById('profile-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {
        name: document.getElementById('profile-name').value,
        email: document.getElementById('profile-email').value
    };

    fetch(`${API_BASE}/user/profile`, {
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        const msgEl = document.getElementById('profile-message');
        msgEl.classList.remove('hidden');
        if (data.success) {
            msgEl.className = 'p-3 bg-green-100 text-green-700 rounded-lg';
            msgEl.textContent = data.message;
        } else {
            msgEl.className = 'p-3 bg-red-100 text-red-700 rounded-lg';
            msgEl.textContent = data.message || 'Failed to update profile';
        }
    })
    .catch(err => {
        const msgEl = document.getElementById('profile-message');
        msgEl.classList.remove('hidden');
        msgEl.className = 'p-3 bg-red-100 text-red-700 rounded-lg';
        msgEl.textContent = 'An error occurred';
    });
});

// Change Password
document.getElementById('password-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {
        current_password: document.getElementById('current-password').value,
        new_password: document.getElementById('new-password').value,
        new_password_confirmation: document.getElementById('confirm-password').value
    };

    fetch(`${API_BASE}/user/password`, {
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        const msgEl = document.getElementById('password-message');
        msgEl.classList.remove('hidden');
        if (data.success) {
            msgEl.className = 'p-3 bg-green-100 text-green-700 rounded-lg';
            msgEl.textContent = data.message;
            document.getElementById('password-form').reset();
        } else {
            msgEl.className = 'p-3 bg-red-100 text-red-700 rounded-lg';
            msgEl.textContent = data.message || 'Failed to change password';
        }
    })
    .catch(err => {
        const msgEl = document.getElementById('password-message');
        msgEl.classList.remove('hidden');
        msgEl.className = 'p-3 bg-red-100 text-red-700 rounded-lg';
        msgEl.textContent = 'An error occurred';
    });
});

// Load Orders
function loadOrders() {
    fetch(`${API_BASE}/user/orders`, {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('orders-list');
        if (!data.data || data.data.length === 0) {
            container.innerHTML = '<div class="bg-white rounded-lg shadow p-6"><p class="text-gray-500">You have no orders yet.</p></div>';
            return;
        }

        container.innerHTML = data.data.map(order => `
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Order #${order.order_number}</h3>
                        <p class="text-sm text-gray-500">${new Date(order.created_at).toLocaleString()}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold">₹${parseFloat(order.total_amount).toFixed(2)}</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full ${getStatusColor(order.order_status)}">${order.order_status}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="viewOrderDetails('${order.order_number}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        View Details
                    </button>
                    <button onclick="downloadInvoice('${order.order_number}')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Download Invoice
                    </button>
                </div>
            </div>
        `).join('');
    })
    .catch(err => {
        console.error(err);
        document.getElementById('orders-list').innerHTML = '<div class="bg-white rounded-lg shadow p-6"><p class="text-red-500">Failed to load orders</p></div>';
    });
}

function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'shipped': 'bg-purple-100 text-purple-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return colors[status.toLowerCase()] || 'bg-gray-100 text-gray-800';
}

// View Order Details
function viewOrderDetails(orderNumber) {
    fetch(`${API_BASE}/user/orders/${orderNumber}`, {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const order = data.data;
            const content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-500">Order Number</p>
                            <p class="font-semibold">${order.order_number}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="inline-block px-3 py-1 text-sm rounded-full ${getStatusColor(order.order_status)}">${order.order_status}</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order Date</p>
                            <p class="font-semibold">${new Date(order.created_at).toLocaleDateString()}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Amount</p>
                            <p class="font-semibold">₹${parseFloat(order.total_amount).toFixed(2)}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-2">Shipping Address</h3>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p>${order.shipping_address}</p>
                            <p>${order.shipping_city}, ${order.shipping_state} ${order.shipping_pincode}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-2">Order Items</h3>
                        <div class="space-y-2">
                            ${order.items.map(item => `
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium">${item.product_name}</p>
                                        <p class="text-sm text-gray-500">Qty: ${item.quantity} × ₹${parseFloat(item.price).toFixed(2)}</p>
                                    </div>
                                    <p class="font-semibold">₹${parseFloat(item.subtotal).toFixed(2)}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-2xl font-bold">₹${parseFloat(order.total_amount).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('order-details-content').innerHTML = content;
            document.getElementById('order-modal').classList.remove('hidden');
        }
    })
    .catch(err => console.error(err));
}

function closeOrderModal() {
    document.getElementById('order-modal').classList.add('hidden');
}

// Download Invoice
function downloadInvoice(orderNumber) {
    window.open(`${API_BASE}/user/orders/${orderNumber}/invoice?token=${token}`, '_blank');
}

// Load Queries
function loadQueries() {
    fetch(`${API_BASE}/user/queries`, {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('queries-list');
        if (!data.data || data.data.length === 0) {
            container.innerHTML = '<div class="bg-white rounded-lg shadow p-6"><p class="text-gray-500">You have no queries yet.</p></div>';
            return;
        }

        container.innerHTML = data.data.map(query => `
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">${query.subject}</h3>
                        ${query.order_number ? `<p class="text-sm text-gray-500">Order: ${query.order_number}</p>` : ''}
                        <p class="text-sm text-gray-500">${new Date(query.created_at).toLocaleString()}</p>
                    </div>
                    <span class="inline-block px-3 py-1 text-sm rounded-full ${getQueryStatusColor(query.status)}">${query.status}</span>
                </div>
                <div class="mb-3">
                    <p class="text-gray-700">${query.message}</p>
                </div>
                ${query.admin_response ? `
                    <div class="border-t pt-3 mt-3">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Admin Response:</p>
                        <p class="text-gray-600">${query.admin_response}</p>
                        ${query.resolved_at ? `<p class="text-xs text-gray-500 mt-1">Resolved on ${new Date(query.resolved_at).toLocaleString()}</p>` : ''}
                    </div>
                ` : ''}
            </div>
        `).join('');
    })
    .catch(err => {
        console.error(err);
        document.getElementById('queries-list').innerHTML = '<div class="bg-white rounded-lg shadow p-6"><p class="text-red-500">Failed to load queries</p></div>';
    });
}

function getQueryStatusColor(status) {
    const colors = {
        'open': 'bg-yellow-100 text-yellow-800',
        'in_progress': 'bg-blue-100 text-blue-800',
        'resolved': 'bg-green-100 text-green-800',
        'closed': 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

// Submit Query
document.getElementById('query-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {
        subject: document.getElementById('query-subject').value,
        order_number: document.getElementById('query-order').value || null,
        message: document.getElementById('query-message').value
    };

    fetch(`${API_BASE}/user/queries`, {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        const msgEl = document.getElementById('query-message-alert');
        msgEl.classList.remove('hidden');
        if (data.success) {
            msgEl.className = 'p-3 bg-green-100 text-green-700 rounded-lg';
            msgEl.textContent = 'Query submitted successfully!';
            document.getElementById('query-form').reset();
            loadQueries();
        } else {
            msgEl.className = 'p-3 bg-red-100 text-red-700 rounded-lg';
            msgEl.textContent = data.message || 'Failed to submit query';
        }
    })
    .catch(err => {
        const msgEl = document.getElementById('query-message-alert');
        msgEl.classList.remove('hidden');
        msgEl.className = 'p-3 bg-red-100 text-red-700 rounded-lg';
        msgEl.textContent = 'An error occurred';
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadProfile();
});
</script>
@endsection
