<?php $__env->startSection('title', 'Create Page'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="<?php echo e(route('admin.pages.index')); ?>" class="inline-flex items-center text-[#6B6B6B] hover:text-[#D4AF37] transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Pages
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-[#D4AF37] to-[#B8962E] bg-clip-text text-transparent mb-8">Create New Page</h2>

        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-lg">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.pages.store')); ?>" method="POST" id="pageForm" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-semibold text-[#1A1A1A] mb-2">Title *</label>
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
                <label class="block text-sm font-semibold text-[#1A1A1A] mb-2">Content *</label>
                <textarea name="content" id="editor" class="input-field" rows="10"><?php echo e(old('content')); ?></textarea>
                <input type="hidden" name="content_required" id="content_required" required>
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
                    <label class="block text-sm font-semibold text-[#1A1A1A] mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" class="input-field" placeholder="0">
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
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="hidden" name="show_in_navbar" value="0">
                        <input type="checkbox" name="show_in_navbar" value="1" <?php echo e(old('show_in_navbar') ? 'checked' : ''); ?> class="w-5 h-5 rounded border-gray-300 text-[#D4AF37] focus:ring-[#D4AF37] accent-[#D4AF37]">
                        <span class="text-sm font-medium text-[#1A1A1A]">Show in Navbar</span>
                    </label>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="hidden" name="show_in_footer" value="0">
                        <input type="checkbox" name="show_in_footer" value="1" <?php echo e(old('show_in_footer') ? 'checked' : ''); ?> class="w-5 h-5 rounded border-gray-300 text-[#D4AF37] focus:ring-[#D4AF37] accent-[#D4AF37]">
                        <span class="text-sm font-medium text-[#1A1A1A]">Show in Footer</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-lg">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-gray-300 text-[#D4AF37] focus:ring-[#D4AF37] accent-[#D4AF37]">
                    <span class="text-sm font-semibold text-[#1A1A1A]">Active (Publish this page immediately)</span>
                </label>
            </div>

            <div class="flex gap-4 pt-6 border-t border-gray-100">
                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Create Page
                </button>
                <a href="<?php echo e(route('admin.pages.index')); ?>" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-all duration-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.0/classic/ckeditor.js"></script>
<script>
    let editorInstance;
    ClassicEditor
        .create(document.getElementById('editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        })
        .then(editor => {
            editorInstance = editor;

            // Update hidden field on editor change
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                document.getElementById('editor').value = data;
                document.getElementById('content_required').value = data;
            });

            // Sync editor data before form submit
            document.getElementById('pageForm').addEventListener('submit', function(e) {
                const data = editor.getData();
                document.getElementById('editor').value = data;
                document.getElementById('content_required').value = data;
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/admin/pages/create.blade.php ENDPATH**/ ?>