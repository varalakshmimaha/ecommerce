<?php $__env->startSection('title', 'Order Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">Order Details</h1>
            <p class="mt-1 text-sm text-gray-500">Track your order and manage your purchase.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-8">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="p-6 flex items-center space-x-6">
                            <img src="<?php echo e($item->product->images->first() ? asset('storage/' . $item->product->images->first()->image_path) : 'https://via.placeholder.com/150'); ?>"
                                 alt="<?php echo e($item->product->name); ?>" class="w-24 h-24 rounded-md object-cover">
                            <div class="flex-1">
                                <h3 class="text-base font-medium text-gray-900"><?php echo e($item->product->name); ?></h3>
                                <p class="text-sm text-gray-500">Qty: <?php echo e($item->quantity); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-medium text-gray-900">₹<?php echo e(number_format($item->price * $item->quantity, 2)); ?></p>
                                <p class="text-sm text-gray-500">₹<?php echo e(number_format($item->price, 2)); ?> each</p>
                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Shipping Address</h2>
                    <?php if($order->addresses->first()): ?>
                    <address class="not-italic text-gray-600">
                        <p class="font-semibold"><?php echo e($order->addresses->first()->name); ?></p>
                        <p><?php echo e($order->addresses->first()->address); ?></p>
                        <p><?php echo e($order->addresses->first()->city); ?>, <?php echo e($order->addresses->first()->state); ?> - <?php echo e($order->addresses->first()->pincode); ?></p>
                        <p>Phone: <?php echo e($order->addresses->first()->phone); ?></p>
                    </address>
                    <?php else: ?>
                    <p class="text-gray-500">No shipping address found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Order Number</dt>
                            <dd class="text-sm font-medium text-gray-900">#<?php echo e($order->order_number); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Order Date</dt>
                            <dd class="text-sm font-medium text-gray-900"><?php echo e($order->created_at->format('M d, Y')); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo e($order->order_status === 'delivered' ? 'bg-green-100 text-green-800' : ''); ?>

                                    <?php echo e($order->order_status === 'cancelled' ? 'bg-red-100 text-red-800' : ''); ?>

                                    <?php echo e(!in_array($order->order_status, ['delivered', 'cancelled']) ? 'bg-yellow-100 text-yellow-800' : ''); ?>">
                                    <?php echo e(ucfirst($order->order_status)); ?>

                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-4">
                            <dt class="text-base font-medium text-gray-900">Total Amount</dt>
                            <dd class="text-base font-medium text-gray-900">₹<?php echo e(number_format($order->total_amount, 2)); ?></dd>
                        </div>
                    </dl>
                </div>

                <?php if($order->order_status === 'pending' || $order->order_status === 'processing'): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <h2 class="text-lg font-medium text-red-900 mb-4">Cancel Order</h2>
                    <form action="<?php echo e(route('user.orders.cancel', $order->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="cancellation_remark" class="block text-sm font-medium text-gray-700">Reason for Cancellation</label>
                            <textarea name="cancellation_remark" id="cancellation_remark" rows="3" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this order?')" class="mt-4 w-full bg-red-600 border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Confirm Cancellation
                        </button>
                    </form>
                </div>
                <?php endif; ?>
                
                <div class="text-center">
                    <a href="<?php echo e(route('user.dashboard')); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        &larr; Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/user/orders/show.blade.php ENDPATH**/ ?>