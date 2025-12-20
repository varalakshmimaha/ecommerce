<?php $__env->startSection('title', 'Create Product'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-card max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Product</h2>
    
    <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category_id" required class="input-field">
                    <option value="">Select Category</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select name="brand_id" class="input-field">
                    <option value="">Select Brand</option>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($brand->id); ?>" <?php echo e(old('brand_id') == $brand->id ? 'selected' : ''); ?>><?php echo e($brand->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sub Category</label>
                <select name="sub_category_id" class="input-field">
                    <option value="">Select Sub Category</option>
                    <?php $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($subCategory->id); ?>" <?php echo e(old('sub_category_id') == $subCategory->id ? 'selected' : ''); ?>><?php echo e($subCategory->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">MRP (₹) *</label>
                <input type="number" step="0.01" name="mrp" value="<?php echo e(old('mrp')); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹) *</label>
                <input type="number" step="0.01" name="selling_price" value="<?php echo e(old('selling_price')); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Discounted Price (₹)</label>
                <input type="number" step="0.01" name="discounted_price" value="<?php echo e(old('discounted_price')); ?>" class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">GST (%) *</label>
                <input type="number" step="0.01" name="gst" value="<?php echo e(old('gst', 0)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Order Quantity *</label>
                <input type="number" name="min_order_quantity" value="<?php echo e(old('min_order_quantity', 1)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                <input type="number" name="stock_quantity" value="<?php echo e(old('stock_quantity', 0)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select name="status" required class="input-field">
                    <option value="unpublished" <?php echo e(old('status') == 'unpublished' ? 'selected' : ''); ?>>Unpublished</option>
                    <option value="published" <?php echo e(old('status') == 'published' ? 'selected' : ''); ?>>Published</option>
                </select>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Main Image *</label>
            <input type="file" name="main_image" accept="image/*" required class="input-field">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images</label>
            <input type="file" name="gallery_images[]" accept="image/*" multiple class="input-field">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
            <textarea name="short_description" rows="3" class="input-field"><?php echo e(old('short_description')); ?></textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Description</label>
            <textarea name="full_description" id="full_description" rows="10" class="input-field"><?php echo e(old('full_description')); ?></textarea>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_new_arrival" value="1" <?php echo e(old('is_new_arrival') ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">New Arrival</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured') ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">Featured</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_trending" value="1" <?php echo e(old('is_trending') ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">Trending</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_top_rated" value="1" <?php echo e(old('is_top_rated') ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">Top Rated</span>
            </label>
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Attributes (Color, Size, etc.)</h3>
            <div id="attributes-container" class="space-y-4">
                <div class="attribute-row grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Type</label>
                        <select name="attributes[0][name]" class="input-field">
                            <option value="">Select Type</option>
                            <option value="color">Color</option>
                            <option value="size">Size</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                        <input type="text" name="attributes[0][value]" class="input-field" placeholder="e.g., Red, XL, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (₹)</label>
                        <input type="number" step="0.01" name="attributes[0][price_adjustment]" value="0" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment</label>
                        <input type="number" name="attributes[0][stock_adjustment]" value="0" class="input-field">
                    </div>
                </div>
            </div>
            <button type="button" onclick="addAttributeRow()" class="mt-4 text-[#D4AF37] hover:text-[#D4AF37] font-medium">
                + Add Another Attribute
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                <input type="text" name="meta_title" value="<?php echo e(old('meta_title')); ?>" class="input-field">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                <textarea name="meta_description" rows="2" class="input-field"><?php echo e(old('meta_description')); ?></textarea>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                <input type="text" name="meta_keywords" value="<?php echo e(old('meta_keywords')); ?>" class="input-field" placeholder="keyword1, keyword2, keyword3">
            </div>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn-secondary">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Create Product</button>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('full_description');

    let attributeIndex = 1;
    function addAttributeRow() {
        const container = document.getElementById('attributes-container');
        const newRow = document.createElement('div');
        newRow.className = 'attribute-row grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg';
        newRow.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Type</label>
                <select name="attributes[${attributeIndex}][name]" class="input-field">
                    <option value="">Select Type</option>
                    <option value="color">Color</option>
                    <option value="size">Size</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                <input type="text" name="attributes[${attributeIndex}][value]" class="input-field" placeholder="e.g., Red, XL, etc.">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Adjustment (₹)</label>
                <input type="number" step="0.01" name="attributes[${attributeIndex}][price_adjustment]" value="0" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Adjustment</label>
                <input type="number" name="attributes[${attributeIndex}][stock_adjustment]" value="0" class="input-field">
            </div>
            <div class="flex items-end">
                <button type="button" onclick="this.closest('.attribute-row').remove()" class="text-red-600 hover:text-red-700 font-medium">Remove</button>
            </div>
        `;
        container.appendChild(newRow);
        attributeIndex++;
    }
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/admin/products/create.blade.php ENDPATH**/ ?>