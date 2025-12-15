<?php $__env->startSection('title', 'Create Page'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl overflow-x-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Page</h2>

        <form action="<?php echo e(route('admin.pages.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="input-field" placeholder="e.g., About Us">
                <?php $__errorArgs = ['title'];
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                <textarea name="content" id="editor" required class="input-field"><?php echo e(old('content')); ?></textarea>
                <?php $__errorArgs = ['content'];
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" class="input-field">
                    <?php $__errorArgs = ['sort_order'];
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

                <div class="flex items-end">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_in_navbar" <?php echo e(old('show_in_navbar') ? 'checked' : ''); ?> class="w-4 h-4">
                        <span class="text-sm font-medium text-gray-700">Show in Navbar</span>
                    </label>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_in_footer" <?php echo e(old('show_in_footer') ? 'checked' : ''); ?> class="w-4 h-4">
                        <span class="text-sm font-medium text-gray-700">Show in Footer</span>
                    </label>
                </div>
            </div>

            <div class="flex items-end">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="is_active" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="w-4 h-4">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn-primary">Create Page</button>
                <a href="<?php echo e(route('admin.pages.index')); ?>" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.getElementById('editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });
</script>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/admin/pages/create.blade.php ENDPATH**/ ?>