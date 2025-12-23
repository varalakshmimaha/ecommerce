<?php $__env->startSection('title', 'Order Success'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow text-center">
        <h2 class="text-2xl font-semibold mb-4">Thank you for your order!</h2>
        <p class="mb-4">Your order number is <strong id="order-number"></strong></p>
        <p class="text-sm text-gray-600 mb-6">We've received your order and will notify you about updates.</p>
        <div>
            <a href="/track-order" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 mr-2">Track Order</a>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/suveeindiaweb/htdocs/suveeindia.com/suvee/resources/views/frontend/order-success.blade.php ENDPATH**/ ?>