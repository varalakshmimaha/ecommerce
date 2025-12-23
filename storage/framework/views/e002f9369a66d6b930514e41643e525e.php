<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="text-center pt-8 pb-6 px-8">
                <div class="inline-block p-3 bg-gradient-to-br from-[#D4AF37] to-[#B8962E] rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-[#1A1A1A]">Welcome Back</h2>
                <p class="text-[#6B6B6B] mt-2">Login to your account</p>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8">
                <?php if($errors->any()): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 animate-slide-down">
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('user.login')); ?>" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-[#1A1A1A]">Mobile Number *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="text" name="mobile" value="<?php echo e(old('mobile')); ?>" required
                                class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300"
                                placeholder="Enter your mobile number">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-[#1A1A1A]">Password *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="password" required
                                class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-lg text-[#1A1A1A] placeholder-[#6B6B6B] focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300"
                                placeholder="Enter your password">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" type="checkbox" class="h-4 w-4 text-[#D4AF37] focus:ring-[#B8962E] border-gray-300 rounded">
                                <label for="remember_me" class="ml-2 block text-sm text-[#6B6B6B]">Remember me</label>
                            </div>

                            <div class="text-sm">
                                <a href="<?php echo e(route('password.request')); ?>" class="font-medium text-[#D4AF37] hover:text-[#B8962E]">
                                    Forgot your password?
                                </a>
                            </div>
                        </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                        Login
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-[#6B6B6B]">Don't have an account?
                        <a href="<?php echo e(route('user.register')); ?>" class="text-[#D4AF37] font-semibold hover:text-[#B8962E] transition-colors duration-300">Register</a>
                    </p>
                </div>
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

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/suveeindiaweb/htdocs/suveeindia.com/suvee/resources/views/frontend/auth/login.blade.php ENDPATH**/ ?>