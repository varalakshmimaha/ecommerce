<?php $__env->startSection('title', 'Order Sales Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header with Title and Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-lg shadow">
        <h2 class="text-3xl font-bold text-gray-900">Order Sales Report</h2>
        <form method="GET" class="flex flex-col md:flex-row gap-2">
            <div class="flex gap-2">
                <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="input-field px-3 py-2 border border-gray-300 rounded">
                <span class="flex items-center text-gray-500">to</span>
                <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="input-field px-3 py-2 border border-gray-300 rounded">
            </div>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 px-6">Filter</button>
            <a href="?download=1&start_date=<?php echo e($startDate); ?>&end_date=<?php echo e($endDate); ?>" class="btn px-6 text-center">Download</a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-600 font-medium">Total Orders</p>
            <p class="text-3xl font-bold text-[#D4AF37] mt-2"><?php echo e($orders->count()); ?></p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-600 font-medium">Total Revenue</p>
            <p class="text-3xl font-bold text-green-600 mt-2">₹<?php echo e(number_format($orders->sum('total_amount'), 2)); ?></p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-600 font-medium">Total GST</p>
            <p class="text-3xl font-bold text-orange-600 mt-2">₹<?php echo e(number_format($orders->sum('gst_amount'), 2)); ?></p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-600 font-medium">Avg Order Value</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">₹<?php echo e($orders->count() > 0 ? number_format($orders->avg('total_amount'), 2) : 0); ?></p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Order #</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Customer</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Mobile</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Subtotal</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">GST</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Shipping</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600"><span class="font-mono bg-gray-100 px-2 py-1 rounded text-xs font-semibold"><?php echo e($order->order_number); ?></span></td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold"><?php echo e($order->name); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($order->mobile); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900">₹<?php echo e(number_format($order->subtotal,2)); ?></td>
                            <td class="px-6 py-4 text-sm text-orange-600 font-semibold">₹<?php echo e(number_format($order->gst_amount,2)); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600">₹<?php echo e(number_format($order->shipping_charge,2)); ?></td>
                            <td class="px-6 py-4 text-sm text-green-600 font-bold">₹<?php echo e(number_format($order->total_amount,2)); ?></td>
                            <td class="px-6 py-4 text-sm"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold"><?php echo e(ucfirst($order->order_status)); ?></span></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($order->created_at->format('Y-m-d')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6"><?php echo e($orders->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

    <div class="mt-4"><?php echo e($orders->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/admin/reports/order-sales.blade.php ENDPATH**/ ?>