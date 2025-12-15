<?php $__env->startSection('title', 'Shipping'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-md">
    <form action="<?php echo e(route('admin.shipping.update')); ?>" method="POST" class="space-y-4 admin-card p-6 bg-white">
        <?php echo csrf_field(); ?>
        <h2 class="text-xl font-semibold">Shipping Settings</h2>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Default Shipping Charge (₹)</label>
            <input type="number" name="shipping_charge" step="0.01" value="<?php echo e($shipping); ?>" class="input-field w-full">
        </div>
        <button class="btn-primary">Save</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/admin/shipping/index.blade.php ENDPATH**/ ?>