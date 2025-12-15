<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="admin-card">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Company Settings</h2>
        <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                    <?php if($settings['company_logo']): ?>
                    <img src="<?php echo e(asset('storage/' . $settings['company_logo'])); ?>" alt="Logo" class="w-32 h-32 object-contain mb-2">
                    <?php endif; ?>
                    <input type="file" name="company_logo" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
                    <?php if($settings['qr_code']): ?>
                    <img src="<?php echo e(asset('storage/' . $settings['qr_code'])); ?>" alt="QR Code" class="w-32 h-32 object-contain mb-2">
                    <?php endif; ?>
                    <input type="file" name="qr_code" accept="image/*" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Description</label>
                <textarea name="company_description" rows="3" class="input-field"><?php echo e($settings['company_description']); ?></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="<?php echo e($settings['whatsapp_number']); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone_number" value="<?php echo e($settings['phone_number']); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="<?php echo e($settings['email']); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3" class="input-field"><?php echo e($settings['address']); ?></textarea>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mt-8 mb-4">Bank Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" value="<?php echo e($settings['bank_name']); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                    <input type="text" name="bank_account_number" value="<?php echo e($settings['bank_account_number']); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">IFSC Code</label>
                    <input type="text" name="bank_ifsc" value="<?php echo e($settings['bank_ifsc']); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name</label>
                    <input type="text" name="bank_account_holder" value="<?php echo e($settings['bank_account_holder']); ?>" class="input-field">
                </div>
            </div>
            <button type="submit" class="btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/admin/settings/index.blade.php ENDPATH**/ ?>