<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="text-center pt-8 pb-6 px-8">
                <div class="inline-block p-3 bg-gradient-to-br from-[#D4AF37] to-[#B8962E] rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-[#1A1A1A]">Forgot Password</h2>
                <p class="text-[#6B6B6B] mt-2">Enter your email to reset your password</p>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8">
                <?php if(session('status')): ?>
                    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6 animate-slide-down">
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 animate-slide-down">
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('password.email')); ?>" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-[#1A1A1A]">Email Address *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                                class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300"
                                placeholder="Enter your email address">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                        Email Password Reset Link
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-down {
    animation: slide-down 0.6s ease-out;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suwish/resources/views/frontend/auth/forgot-password.blade.php ENDPATH**/ ?>