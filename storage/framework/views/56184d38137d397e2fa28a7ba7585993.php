<?php $__env->startSection('title', 'Customer Queries'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Customer Queries</h1>
        <div class="text-sm text-gray-600">
            Total Queries: <span class="font-semibold"><?php echo e($queries->total()); ?></span>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($queries->isEmpty()): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">No customer queries found.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $queries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $query): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-1"><?php echo e($query->subject); ?></h3>
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <span>From: <strong><?php echo e($query->user->name); ?></strong> (<?php echo e($query->user->email); ?>)</span>
                                    <?php if($query->order_number): ?>
                                        <span>Order: <strong><?php echo e($query->order_number); ?></strong></span>
                                    <?php endif; ?>
                                    <span><?php echo e($query->created_at->format('M d, Y h:i A')); ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <form action="<?php echo e(route('admin.queries.update-status', $query)); ?>" method="POST" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <select name="status" onchange="this.form.submit()" class="px-3 py-1 rounded-full text-sm font-medium border-0 <?php echo e($query->status === 'open' ? 'bg-yellow-100 text-yellow-800' :
                                        ($query->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                        ($query->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))); ?>">
                                        <option value="open" <?php echo e($query->status === 'open' ? 'selected' : ''); ?>>Open</option>
                                        <option value="in_progress" <?php echo e($query->status === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                                        <option value="resolved" <?php echo e($query->status === 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                                        <option value="closed" <?php echo e($query->status === 'closed' ? 'selected' : ''); ?>>Closed</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700"><?php echo e($query->message); ?></p>
                        </div>

                        <?php if($query->admin_response): ?>
                            <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                                <p class="text-sm font-semibold text-blue-900 mb-1">Your Response:</p>
                                <p class="text-blue-800"><?php echo e($query->admin_response); ?></p>
                                <?php if($query->resolved_at): ?>
                                    <p class="text-xs text-[#D4AF37] mt-2">Responded on <?php echo e($query->resolved_at->format('M d, Y h:i A')); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="border-t pt-4">
                            <button onclick="toggleResponseForm(<?php echo e($query->id); ?>)" class="px-4 py-2 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white rounded-lg hover:bg-blue-700 transition">
                                <?php echo e($query->admin_response ? 'Update Response' : 'Respond to Query'); ?>

                            </button>
                        </div>

                        <div id="response-form-<?php echo e($query->id); ?>" class="hidden mt-4 border-t pt-4">
                            <form action="<?php echo e(route('admin.queries.respond', $query)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Response</label>
                                    <textarea name="response" rows="4" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300" placeholder="Type your response here..."><?php echo e($query->admin_response); ?></textarea>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                                        Send Response
                                    </button>
                                    <button type="button" onclick="toggleResponseForm(<?php echo e($query->id); ?>)" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6">
            <?php echo e($queries->links()); ?>

        </div>
    <?php endif; ?>
</div>

<script>
function toggleResponseForm(queryId) {
    const form = document.getElementById('response-form-' + queryId);
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
    } else {
        form.classList.add('hidden');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/admin/queries/index.blade.php ENDPATH**/ ?>