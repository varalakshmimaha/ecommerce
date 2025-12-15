<?php $__env->startSection('title', 'Register'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">Create an account</h2>
        <form id="register-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Mobile *</label>
                <input type="text" name="mobile" required class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password *</label>
                <input type="password" name="password" required class="input-field">
            </div>
            <button type="submit" class="btn-primary w-full">Register</button>
        </form>
        <p class="text-sm text-gray-600 mt-4">Already have an account? <a href="<?php echo e(route('user.login')); ?>" class="text-primary-600">Login</a></p>
    </div>
</div>

<script>
document.getElementById('register-form').addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    const payload = {};
    fd.forEach((v,k) => payload[k] = v);

    fetch(`${API_BASE}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(res => res.json()).then(data => {
        if (data.token && data.user) {
            // Auto-login after registration
            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));

            if (window.showModal) showModal('Success', 'Registration successful! You are now logged in.', 'success');
            else alert('Registration successful! You are now logged in.');

            // Update cart count if function exists
            if (window.updateCartCount) window.updateCartCount();

            // Redirect to dashboard or home
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1000);
        } else if (data.errors) {
            const errs = Object.values(data.errors).flat().join('\n');
            if (window.showModal) showModal('Error', errs, 'error');
            else alert(errs);
        } else if (data.error) {
            if (window.showModal) showModal('Error', data.error, 'error');
            else alert(data.error);
        }
    }).catch(err => {
        console.error(err);
        if (window.showModal) showModal('Error','Failed to register', 'error');
        else alert('Failed to register');
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vikasverma/Projects/suvee/resources/views/frontend/auth/register.blade.php ENDPATH**/ ?>