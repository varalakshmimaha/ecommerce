@extends('layouts.frontend')

@section('title', 'Order Success')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow text-center">
        <h2 class="text-2xl font-semibold mb-4">Thank you for your order!</h2>
        <p class="mb-4">Your order number is <strong id="order-number"></strong></p>
        <p class="text-sm text-gray-600 mb-6">We've received your order and will notify you about updates.</p>
        <div>
            <a href="/track-order" class="btn-primary mr-2">Track Order</a>
            <a href="/" class="btn">Continue Shopping</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const parts = window.location.pathname.split('/');
    const orderNumber = parts[parts.length - 1] || '';
    if (orderNumber) {
        document.getElementById('order-number').textContent = orderNumber;
        // show modal with success
        if (window.showModal) showModal('Order Placed', 'Your order ' + orderNumber + ' has been placed.', 'success');
    }
});
</script>
@endsection
