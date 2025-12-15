<?php $__env->startSection('title', 'Categories & Sub-Categories'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    <!-- Categories List -->
    <div class="lg:col-span-2">
        <div class="admin-card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Categories</h2>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div id="category-<?php echo e($category->id); ?>" class="border rounded-lg p-4"
                     data-name="<?php echo e(e($category->name)); ?>"
                     data-description="<?php echo e(e($category->description)); ?>"
                     data-sort_order="<?php echo e($category->sort_order); ?>"
                >
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-4 flex-1">
                            <?php if($category->image): ?>
                            <img src="<?php echo e(asset('storage/' . $category->image)); ?>" alt="<?php echo e($category->name); ?>" class="w-12 h-12 object-cover rounded-lg">
                            <?php endif; ?>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900"><?php echo e($category->name); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo e($category->subCategories->count()); ?> sub-categories</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="editCategory(<?php echo e($category->id); ?>)" class="text-primary-600 text-sm">Edit</button>
                            <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Sub-categories for this category -->
                    <?php if($category->subCategories->count() > 0): ?>
                    <div class="ml-4 mt-3 space-y-2 border-l-2 border-gray-300 pl-4">
                        <?php $__currentLoopData = $category->subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div id="subcat-<?php echo e($subcat->id); ?>" class="flex items-center justify-between py-2 bg-gray-50 px-3 rounded text-sm"
                             data-name="<?php echo e(e($subcat->name)); ?>"
                             data-description="<?php echo e(e($subcat->description)); ?>"
                             data-sort_order="<?php echo e($subcat->sort_order); ?>"
                        >
                            <div class="flex items-center space-x-2 flex-1">
                                <?php if($subcat->image): ?>
                                <img src="<?php echo e(asset('storage/' . $subcat->image)); ?>" alt="<?php echo e($subcat->name); ?>" class="w-8 h-8 object-cover rounded">
                                <?php endif; ?>
                                <span class="font-medium text-gray-700"><?php echo e($subcat->name); ?></span>
                            </div>
                            <div class="flex space-x-1">
                                <button type="button" onclick="editSubCategory(<?php echo e($category->id); ?>, <?php echo e($subcat->id); ?>)" class="text-primary-600 text-xs">Edit</button>
                                <form action="<?php echo e(route('admin.sub-categories.destroy', $subcat)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 text-xs">Delete</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Forms -->
    <div class="space-y-6">
        <!-- Add Category Form -->
        <div class="admin-card">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Add Category</h2>
            <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="input-field text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="input-field text-sm">
                </div>
                <button type="submit" class="btn-primary w-full text-sm py-2">Add Category</button>
            </form>
        </div>
        
        <!-- Add Sub-Category Form -->
        <div class="admin-card">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Add Sub-Category</h2>
            <form id="addSubcategoryForm" action="<?php echo e(route('admin.sub-categories.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category_id" required class="input-field text-sm">
                        <option value="">Select Category</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="input-field text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="input-field text-sm">
                </div>
                <button type="submit" class="btn-primary w-full text-sm py-2">Add Sub-Category</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="modal hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Edit Category</h3>
            <button onclick="closeEditCategory()" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="edit_cat_name" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_cat_description" rows="3" class="input-field"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (leave blank to keep)</label>
                    <input type="file" name="image" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_cat_sort_order" class="input-field w-32">
                </div>
                <div class="pt-4 flex gap-2 justify-end">
                    <button type="button" onclick="closeEditCategory()" class="btn">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Sub-Category Modal -->
<div id="editSubcategoryModal" class="modal hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Edit Sub-Category</h3>
            <button onclick="closeEditSubCategory()" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editSubcategoryForm" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="edit_subcat_name" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_subcat_description" rows="3" class="input-field"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (leave blank to keep)</label>
                    <input type="file" name="image" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_subcat_sort_order" class="input-field w-32">
                </div>
                <div class="pt-4 flex gap-2 justify-end">
                    <button type="button" onclick="closeEditSubCategory()" class="btn">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editCategory(id) {
        const el = document.getElementById('category-' + id);
        if (!el) return alert('Category not found');

        document.getElementById('edit_cat_name').value = el.dataset.name || '';
        document.getElementById('edit_cat_description').value = el.dataset.description || '';
        document.getElementById('edit_cat_sort_order').value = el.dataset.sort_order || 0;

        const form = document.getElementById('editCategoryForm');
        form.action = '<?php echo e(url("admin/categories")); ?>/' + id;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }

    function closeEditCategory() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    }

    function editSubCategory(categoryId, subcatId) {
        const el = document.getElementById('subcat-' + subcatId);
        if (!el) return alert('Sub-category not found');

        document.getElementById('edit_subcat_name').value = el.dataset.name || '';
        document.getElementById('edit_subcat_description').value = el.dataset.description || '';
        document.getElementById('edit_subcat_sort_order').value = el.dataset.sort_order || 0;

        const form = document.getElementById('editSubcategoryForm');
        form.action = '<?php echo e(url("admin/sub-categories")); ?>/' + subcatId;
        document.getElementById('editSubcategoryModal').classList.remove('hidden');
    }

    function closeEditSubCategory() {
        document.getElementById('editSubcategoryModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    document.getElementById('editCategoryModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditCategory();
    });
    document.getElementById('editSubcategoryModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditSubCategory();
    });
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/admin/categories/index.blade.php ENDPATH**/ ?>