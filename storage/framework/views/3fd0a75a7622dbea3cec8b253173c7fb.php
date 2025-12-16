<?php $__env->startSection('title', 'Brands'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    <!-- Brands List -->
    <div class="lg:col-span-2">
        <div class="admin-card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Brands</h2>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div id="brand-<?php echo e($brand->id); ?>" class="border rounded-lg p-4"
                     data-name="<?php echo e(e($brand->name)); ?>"
                     data-description="<?php echo e(e($brand->description)); ?>"
                     data-sort_order="<?php echo e($brand->sort_order); ?>"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 flex-1">
                            <?php if($brand->icon): ?>
                            <img src="<?php echo e(asset('storage/' . $brand->icon)); ?>" alt="<?php echo e($brand->name); ?>" class="w-12 h-12 object-contain rounded-lg bg-gray-100 p-1">
                            <?php else: ?>
                            <div class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded-lg text-xl">🏢</div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900"><?php echo e($brand->name); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo e($brand->products_count ?? 0); ?> products</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="editBrand(<?php echo e($brand->id); ?>)" class="text-[#D4AF37] text-sm">Edit</button>
                            <form action="<?php echo e(route('admin.brands.destroy', $brand)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-center text-gray-500 py-8">No brands yet. Create one!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Forms -->
    <div class="space-y-6">
        <!-- Add Brand Form -->
        <div class="admin-card">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Add Brand</h2>
            <form action="<?php echo e(route('admin.brands.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Optional)</label>
                    <input type="file" name="icon" accept="image/*" class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="input-field text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="input-field text-sm">
                </div>
                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full text-sm py-2">Add Brand</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<!-- Edit Brand Modal -->
<div id="editBrandModal" class="modal hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Edit Brand</h3>
            <button onclick="closeEditBrand()" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editBrandForm" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="edit_brand_name" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (leave blank to keep)</label>
                    <input type="file" name="icon" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_brand_description" rows="2" class="input-field"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_brand_sort_order" class="input-field w-32">
                </div>
                <div class="pt-4 flex gap-2 justify-end">
                    <button type="button" onclick="closeEditBrand()" class="btn">Cancel</button>
                    <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editBrand(id) {
        const el = document.getElementById('brand-' + id);
        if (!el) return alert('Brand not found');

        document.getElementById('edit_brand_name').value = el.dataset.name || '';
        document.getElementById('edit_brand_description').value = el.dataset.description || '';
        document.getElementById('edit_brand_sort_order').value = el.dataset.sort_order || 0;

        const form = document.getElementById('editBrandForm');
        form.action = '<?php echo e(url("admin/brands")); ?>/' + id;
        document.getElementById('editBrandModal').classList.remove('hidden');
    }

    function closeEditBrand() {
        document.getElementById('editBrandModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('editBrandModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditBrand();
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/admin/brands/index.blade.php ENDPATH**/ ?>