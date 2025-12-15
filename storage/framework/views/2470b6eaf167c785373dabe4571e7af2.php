<?php $__env->startSection('title', 'Pages'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Pages</h2>
        <a href="<?php echo e(route('admin.pages.create')); ?>" class="btn-primary">Add New Page</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Navbar</th>
                    <th>Footer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="font-semibold"><?php echo e($page->title); ?></td>
                        <td class="text-gray-600 text-sm font-mono"><?php echo e($page->slug); ?></td>
                        <td>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($page->show_in_navbar ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e($page->show_in_navbar ? 'Yes' : 'No'); ?>

                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($page->show_in_footer ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e($page->show_in_footer ? 'Yes' : 'No'); ?>

                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($page->is_active ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php echo e($page->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td class="space-x-2 flex">
                            <a href="<?php echo e(route('admin.pages.edit', $page)); ?>" class="text-primary-600 hover:text-primary-800 font-medium">Edit</a>
                            <form action="<?php echo e(route('admin.pages.destroy', $page)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </form>
                            <a href="<?php echo e(route('page.show', $page)); ?>" target="_blank" class="text-gray-600 hover:text-gray-800 font-medium">View</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">No pages found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div><?php echo e($pages->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/admin/pages/index.blade.php ENDPATH**/ ?>