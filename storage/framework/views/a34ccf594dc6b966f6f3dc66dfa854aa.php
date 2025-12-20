<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="<?php echo e(route('admin.reports.product-sales')); ?>" class="admin-card hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="text-center">
                <div class="bg-primary-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Product Sales Report</h3>
                <p class="text-sm text-gray-500 mt-2">View product-wise sales data</p>
            </div>
        </a>
        
        <a href="<?php echo e(route('admin.reports.order-sales')); ?>" class="admin-card hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="text-center">
                <div class="bg-green-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Order Sales Report</h3>
                <p class="text-sm text-gray-500 mt-2">View order-wise sales data</p>
            </div>
        </a>
        
        <a href="<?php echo e(route('admin.reports.overall-sales')); ?>" class="admin-card hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="text-center">
                <div class="bg-purple-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Overall Sales Report</h3>
                <p class="text-sm text-gray-500 mt-2">View overall sales statistics</p>
            </div>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/admin/reports/index.blade.php ENDPATH**/ ?>