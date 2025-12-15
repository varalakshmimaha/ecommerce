<?php $__env->startSection('title', 'Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-card">
    <div class="mb-4 flex space-x-4">
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="px-4 py-2 rounded-lg <?php echo e(!request('status') ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'); ?>">All</a>
        <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending'])); ?>" class="px-4 py-2 rounded-lg <?php echo e(request('status') == 'pending' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'); ?>">Pending</a>
        <a href="<?php echo e(route('admin.orders.index', ['status' => 'confirmed'])); ?>" class="px-4 py-2 rounded-lg <?php echo e(request('status') == 'confirmed' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'); ?>">Confirmed</a>
        <a href="<?php echo e(route('admin.orders.index', ['status' => 'shipped'])); ?>" class="px-4 py-2 rounded-lg <?php echo e(request('status') == 'shipped' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'); ?>">Shipped</a>
        <a href="<?php echo e(route('admin.orders.index', ['status' => 'delivered'])); ?>" class="px-4 py-2 rounded-lg <?php echo e(request('status') == 'delivered' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'); ?>">Delivered</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Mobile</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-medium"><?php echo e($order->order_number); ?></td>
                    <td><?php echo e($order->name); ?></td>
                    <td><?php echo e($order->mobile); ?></td>
                    <td>₹<?php echo e(number_format($order->total_amount, 2)); ?></td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($order->payment_status === 'verified' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                            <?php echo e(ucfirst($order->payment_status)); ?>

                        </span>
                    </td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($order->order_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->order_status === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($order->order_status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))); ?>">
                            <?php echo e(ucfirst($order->order_status)); ?>

                        </span>
                    </td>
                    <td><?php echo e($order->created_at->format('M d, Y')); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="text-primary-600 hover:text-primary-800">View</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-500">No orders found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <?php echo e($orders->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/admin/orders/index.blade.php ENDPATH**/ ?>