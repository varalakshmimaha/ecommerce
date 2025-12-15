<?php $__env->startSection('title', 'Create Product Attribute'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Product Attribute</h2>

        <?php if($errors->any()): ?>
            <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.product-attributes.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Value *</label>
                <input type="text" name="attribute_value" value="<?php echo e(old('attribute_value')); ?>" required class="input-field" placeholder="e.g., Red, Large, Premium">
                <?php $__errorArgs = ['attribute_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                <select name="attribute_name" required class="input-field">
                    <option value="">Select Type</option>
                    <option value="color" <?php echo e(old('attribute_name') === 'color' ? 'selected' : ''); ?>>Color</option>
                    <option value="size" <?php echo e(old('attribute_name') === 'size' ? 'selected' : ''); ?>>Size</option>
                    <option value="other" <?php echo e(old('attribute_name') === 'other' ? 'selected' : ''); ?>>Other</option>
                </select>
                <?php $__errorArgs = ['attribute_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (optional)</label>
                <input type="number" step="0.01" name="price_adjustment" value="<?php echo e(old('price_adjustment', 0)); ?>" class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment (optional)</label>
                <input type="number" name="stock_adjustment" value="<?php echo e(old('stock_adjustment', 0)); ?>" class="input-field">
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn-primary">Create Attribute</button>
                <a href="<?php echo e(route('admin.product-attributes.index')); ?>" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/admin/product-attributes/create.blade.php ENDPATH**/ ?>