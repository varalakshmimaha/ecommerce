<?php $__env->startSection('title', 'Track Order'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">Track Your Order</h2>
        <form id="track-form" class="flex space-x-2">
            <input type="text" id="order_number" placeholder="Enter Order Number (e.g. ORD-...)" class="input-field flex-1">
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Track</button>
        </form>

        <div id="track-result" class="mt-6"></div>
    </div>
</div>

<script>
document.getElementById('track-form').addEventListener('submit', function(e){
    e.preventDefault();
    const orderNumber = document.getElementById('order_number').value.trim();
    if (!orderNumber) return;
    fetch(`${API_BASE}/orders/track/${encodeURIComponent(orderNumber)}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('track-result');
            if (data.error) {
                container.innerHTML = `<p class="text-red-500">${data.error}</p>`;
                return;
            }
            const order = data.data;
            container.innerHTML = `
                <div class="p-4 bg-gray-50 rounded">
                    <h3 class="font-semibold">Order ${order.order_number}</h3>
                    <p class="text-sm text-gray-600">Status: ${order.order_status}</p>
                    <p class="text-sm text-gray-600">Placed: ${new Date(order.created_at).toLocaleString()}</p>
                    <div class="mt-4">
                        <h4 class="font-medium">Items</h4>
                        ${order.items.map(i => `<div class="flex justify-between py-2 border-b"><div>${i.product_name} x ${i.quantity}</div><div>₹${parseFloat(i.subtotal).toFixed(2)}</div></div>`).join('')}
                    </div>
                </div>
            `;
        }).catch(err => { document.getElementById('track-result').innerHTML = '<p class="text-red-500">Failed to fetch order</p>'; });
});
</script>
<?php $__env->stopSection(); ?>  
<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/thesuwishclothing/htdocs/thesuwishclothing.com/suwish/resources/views/frontend/track-order.blade.php ENDPATH**/ ?>