<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Suvee</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/admin.css', 'resources/js/admin.js']); ?>
</head>
<body class="bg-gradient-to-br from-primary-500 to-primary-700 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md animate-scale-in">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Suvee Admin</h1>
            <p class="text-gray-600 mt-2">Sign in to your account</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 animate-slide-down">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>
            <div>
                <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo e(old('mobile')); ?>" required
                    class="input-field <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="Enter your mobile number">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="input-field <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="Enter your password">
            </div>

            <button type="submit" class="w-full btn-primary bg-primary-600 text-white">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>

<?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/auth/login.blade.php ENDPATH**/ ?>