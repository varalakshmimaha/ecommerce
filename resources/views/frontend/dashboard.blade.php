@extends('layouts.frontend')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-2xl font-semibold mb-6">My Orders</h1>
    <div id="orders-list" class="space-y-4">
        <p class="text-gray-500">Loading your orders...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = '/user/login';
        return;
    }

    fetch(`${API_BASE}/user/orders`, { headers: { 'Authorization': 'Bearer ' + token } })
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('orders-list');
            if (!data.data || data.data.length === 0) {
                container.innerHTML = '<p class="text-gray-500">You have no orders yet.</p>';
                return;
            }

            container.innerHTML = data.data.map(order => `
                <div class="card p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold">Order #${order.order_number}</h3>
                            <p class="text-sm text-gray-500">${new Date(order.created_at).toLocaleString()}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold">₹${parseFloat(order.total_amount).toFixed(2)}</p>
                            <p class="text-sm text-gray-500">${order.order_status}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        ${order.items.map(i => `
                            <div class="flex items-center space-x-4 mb-2">
                                <div class="flex-1">
                                    <div class="font-medium">${i.product_name}</div>
                                    <div class="text-sm text-gray-500">Qty: ${i.quantity} • ₹${parseFloat(i.price).toFixed(2)}</div>
                                </div>
                                <div class="font-semibold">₹${parseFloat(i.subtotal).toFixed(2)}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        })
        .catch(err => { console.error(err); document.getElementById('orders-list').innerHTML = '<p class="text-red-500">Failed to load orders</p>'; });
});
</script>

@endsection
