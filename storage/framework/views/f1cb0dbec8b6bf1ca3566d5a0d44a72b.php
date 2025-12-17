<?php $__env->startSection('title', 'Order Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="admin-card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Order #<?php echo e($order->order_number); ?></h2>
            <a href="<?php echo e(route('admin.orders.invoice', $order)); ?>" target="_blank" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                View Invoice
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Customer Information</h3>
                <div class="space-y-2 text-sm">
                    <?php if($order->address_id && $order->address): ?>
                        <p><span class="font-medium">Name:</span> <?php echo e($order->addresses->name); ?></p>
                        <p><span class="font-medium">Mobile:</span> <?php echo e($order->addresses->phone); ?></p>
                        <p><span class="font-medium">Email:</span> <?php echo e($order->email ?? 'N/A'); ?></p>
                        <p><span class="font-medium">Address:</span> <?php echo e($order->addresses->address); ?>, <?php echo e($order->addresses->city); ?>, <?php echo e($order->addresses->state); ?> - <?php echo e($order->addresses->pincode); ?>, <?php echo e($order->addresses->country); ?></p>
                        <p><span class="font-medium">Pincode:</span> <?php echo e($order->addresses->pincode); ?></p>
                    <?php else: ?>
                        <p><span class="font-medium">Name:</span> <?php echo e($order->name); ?></p>
                        <p><span class="font-medium">Mobile:</span> <?php echo e($order->mobile); ?></p>
                        <p><span class="font-medium">Email:</span> <?php echo e($order->email ?? 'N/A'); ?></p>
                        <p><span class="font-medium">Address:</span> <?php echo e($order->address); ?></p>
                        <p><span class="font-medium">Pincode:</span> <?php echo e($order->pincode); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Order Information</h3>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium">Order Date:</span> <?php echo e($order->created_at->format('M d, Y H:i')); ?></p>
                    <p><span class="font-medium">Payment Status:</span> 
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($order->payment_status === 'verified' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                            <?php echo e(ucfirst($order->payment_status)); ?>

                        </span>
                    </p>
                    <p><span class="font-medium">Order Status:</span> 
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($order->order_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->order_status === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($order->order_status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))); ?>">
                            <?php echo e(ucfirst($order->order_status)); ?>

                        </span>
                    </p>
                    <?php if($order->tracking_id): ?>
                    <p><span class="font-medium">Tracking ID:</span> <?php echo e($order->tracking_id); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if($order->payment_proof): ?>
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Payment Proof</h3>
            <img src="<?php echo e(asset('storage/' . $order->payment_proof)); ?>" alt="Payment Proof" class="max-w-md rounded-lg shadow-md">
        </div>
        <?php endif; ?>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Order Items</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item->product_name); ?></td>
                            <td>₹<?php echo e(number_format($item->price, 2)); ?></td>
                            <td><?php echo e($item->quantity); ?></td>
                            <td>₹<?php echo e(number_format($item->subtotal, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="border-t pt-4">
            <div class="flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>₹<?php echo e(number_format($order->subtotal, 2)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>GST:</span>
                        <span>₹<?php echo e(number_format($order->gst_amount, 2)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping:</span>
                        <span>₹<?php echo e(number_format($order->shipping_charge, 2)); ?></span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span>₹<?php echo e(number_format($order->total_amount, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="admin-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Order Status</h3>
        <form action="<?php echo e(route('admin.orders.update-status', $order)); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                    <select name="order_status" class="input-field">
                        <option value="pending" <?php echo e($order->order_status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="confirmed" <?php echo e($order->order_status === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                        <option value="shipped" <?php echo e($order->order_status === 'shipped' ? 'selected' : ''); ?>>Shipped</option>
                        <option value="delivered" <?php echo e($order->order_status === 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                        <option value="cancelled" <?php echo e($order->order_status === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tracking ID</label>
                    <input type="text" name="tracking_id" value="<?php echo e($order->tracking_id); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tracking URL</label>
                    <input type="url" name="tracking_url" value="<?php echo e($order->tracking_url); ?>" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3" class="input-field"><?php echo e($order->notes); ?></textarea>
            </div>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Status</button>
        </form>
    </div>
    
    <?php if($order->payment_status === 'pending'): ?>
    <div class="admin-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Verify Payment</h3>
        <form action="<?php echo e(route('admin.orders.verify-payment', $order)); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                <select name="payment_status" class="input-field">
                    <option value="verified">Verify Payment</option>
                    <option value="rejected">Reject Payment</option>
                </select>
            </div>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Payment Status</button>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/admin/orders/show.blade.php ENDPATH**/ ?>