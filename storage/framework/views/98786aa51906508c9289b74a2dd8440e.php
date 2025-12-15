<?php $__env->startSection('title', 'Edit Product'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-card max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Product</h2>
    
    <?php if($errors->any()): ?>
        <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if(session('success')): ?>
        <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    
    <form action="<?php echo e(route('admin.products.update', $product)); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="<?php echo e(old('name', $product->name)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category_id" required class="input-field">
                    <option value="">Select Category</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id', $product->category_id) == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select name="brand_id" class="input-field">
                    <option value="">Select Brand</option>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($brand->id); ?>" <?php echo e(old('brand_id', $product->brand_id) == $brand->id ? 'selected' : ''); ?>><?php echo e($brand->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sub Category</label>
                <select name="sub_category_id" class="input-field">
                    <option value="">Select Sub Category</option>
                    <?php $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($subCategory->id); ?>" <?php echo e(old('sub_category_id', $product->sub_category_id) == $subCategory->id ? 'selected' : ''); ?>><?php echo e($subCategory->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">MRP (₹) *</label>
                <input type="number" step="0.01" name="mrp" value="<?php echo e(old('mrp', $product->mrp)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹) *</label>
                <input type="number" step="0.01" name="selling_price" value="<?php echo e(old('selling_price', $product->selling_price)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Discounted Price (₹)</label>
                <input type="number" step="0.01" name="discounted_price" value="<?php echo e(old('discounted_price', $product->discounted_price)); ?>" class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">GST (%) *</label>
                <input type="number" step="0.01" name="gst" value="<?php echo e(old('gst', $product->gst)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Order Quantity *</label>
                <input type="number" name="min_order_quantity" value="<?php echo e(old('min_order_quantity', $product->min_order_quantity)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                <input type="number" name="stock_quantity" value="<?php echo e(old('stock_quantity', $product->stock_quantity)); ?>" required class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select name="status" required class="input-field">
                    <option value="unpublished" <?php echo e(old('status', $product->status) == 'unpublished' ? 'selected' : ''); ?>>Unpublished</option>
                    <option value="published" <?php echo e(old('status', $product->status) == 'published' ? 'selected' : ''); ?>>Published</option>
                </select>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Main Image</label>
            <?php if($product->main_image): ?>
                <img src="<?php echo e(asset('storage/' . $product->main_image)); ?>" alt="<?php echo e($product->name); ?>" class="w-32 h-32 object-cover rounded-lg mb-2">
            <?php endif; ?>
            <input type="file" name="main_image" accept="image/*" class="input-field">
            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images</label>
            <?php if($product->images->count() > 0): ?>
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <img src="<?php echo e(asset('storage/' . $image->image_path)); ?>" alt="Gallery" class="w-full h-24 object-cover rounded-lg">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            <input type="file" name="gallery_images[]" accept="image/*" multiple class="input-field">
            <p class="text-xs text-gray-500 mt-1">Add more gallery images</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
            <textarea name="short_description" rows="3" class="input-field"><?php echo e(old('short_description', $product->short_description)); ?></textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Description</label>
            <textarea name="full_description" id="full_description" rows="10" class="input-field"><?php echo e(old('full_description', $product->full_description)); ?></textarea>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_new_arrival" value="1" <?php echo e(old('is_new_arrival', $product->is_new_arrival) ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">New Arrival</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured', $product->is_featured) ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">Featured</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_trending" value="1" <?php echo e(old('is_trending', $product->is_trending) ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">Trending</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_top_rated" value="1" <?php echo e(old('is_top_rated', $product->is_top_rated) ? 'checked' : ''); ?> class="mr-2">
                <span class="text-sm text-gray-700">Top Rated</span>
            </label>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                <input type="text" name="meta_title" value="<?php echo e(old('meta_title', $product->meta_title)); ?>" class="input-field">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                <textarea name="meta_description" rows="2" class="input-field"><?php echo e(old('meta_description', $product->meta_description)); ?></textarea>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                <input type="text" name="meta_keywords" value="<?php echo e(old('meta_keywords', $product->meta_keywords)); ?>" class="input-field" placeholder="keyword1, keyword2, keyword3">
            </div>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Product</button>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('full_description');
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/admin/products/edit.blade.php ENDPATH**/ ?>