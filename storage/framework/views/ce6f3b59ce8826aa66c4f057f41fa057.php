<?php $__env->startSection('title', 'Edit Product Attribute'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Product Attribute</h2>

    <?php if($errors->any()): ?>
        <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.product-attributes.update', $productAttribute)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Value *</label>
            <input type="text" name="attribute_value" value="<?php echo e(old('attribute_value', $productAttribute->attribute_value)); ?>" required class="input-field">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
            <select name="attribute_name" required class="input-field">
                <option value="color" <?php echo e(old('attribute_name', $productAttribute->attribute_name) === 'color' ? 'selected' : ''); ?>>Color</option>
                <option value="size" <?php echo e(old('attribute_name', $productAttribute->attribute_name) === 'size' ? 'selected' : ''); ?>>Size</option>
                <option value="other" <?php echo e(old('attribute_name', $productAttribute->attribute_name) === 'other' ? 'selected' : ''); ?>>Other</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (optional)</label>
            <input type="number" step="0.01" name="price_adjustment" value="<?php echo e(old('price_adjustment', $productAttribute->price_adjustment)); ?>" class="input-field">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment (optional)</label>
            <input type="number" name="stock_adjustment" value="<?php echo e(old('stock_adjustment', $productAttribute->stock_adjustment)); ?>" class="input-field">
        </div>

        <div class="flex gap-4 pt-6">
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Attribute</button>
            <a href="<?php echo e(route('admin.product-attributes.index')); ?>" class="btn">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/admin/product-attributes/edit.blade.php ENDPATH**/ ?>