<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Products</h2>
    <a href="<?php echo e(route('admin.products.create')); ?>" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Product
    </a>
</div>

<div class="admin-card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="animate-fade-in">
                    <td>
                        <img src="<?php echo e(asset('storage/' . $product->main_image)); ?>" alt="<?php echo e($product->name); ?>" class="w-16 h-16 object-cover rounded-lg">
                    </td>
                    <td class="font-medium"><?php echo e($product->name); ?></td>
                    <td><?php echo e($product->category->name); ?></td>
                    <td>₹<?php echo e(number_format($product->selling_price, 2)); ?></td>
                    <td><?php echo e($product->stock_quantity); ?></td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($product->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo e(ucfirst($product->status)); ?>

                        </span>
                    </td>
                    <td>
                        <div class="flex space-x-2">
                            <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="text-[#D4AF37] hover:text-primary-800">Edit</a>
                            <form action="<?php echo e(route('admin.products.destroy', $product)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">No products found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <?php echo e($products->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suwish/resources/views/admin/products/index.blade.php ENDPATH**/ ?>