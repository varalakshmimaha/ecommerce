@extends('layouts.frontend')

@section('title', 'Login')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">Login</h2>
        <form id="login-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Mobile *</label>
                <input type="text" name="mobile" required class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password *</label>
                <input type="password" name="password" required class="input-field">
            </div>
            <button type="submit" class="btn-primary w-full">Login</button>
        </form>
        <p class="text-sm text-gray-600 mt-4">Don't have an account? <a href="{{ route('user.register') }}" class="text-primary-600">Register</a></p>
    </div>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    const payload = {};
    fd.forEach((v,k) => payload[k] = v);

    fetch(`${API_BASE}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(res => res.json()).then(data => {
        if (data.token) {
            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            if (window.showModal) showModal('Success', 'Login successful', 'success'); else alert('Login successful');
            // redirect back to previous page if available (preserve cart)
            const redirect = localStorage.getItem('post_login_redirect');
            if (redirect) {
                localStorage.removeItem('post_login_redirect');
                if (window.updateCartCount) window.updateCartCount();
                setTimeout(() => { window.location.href = redirect; }, 600);
            } else if (document.referrer && !document.referrer.includes('/user/login')) {
                if (window.updateCartCount) window.updateCartCount();
                setTimeout(() => { window.location.href = document.referrer; }, 600);
            } else {
                setTimeout(() => { window.location.href = '/dashboard'; }, 800);
            }
        } else if (data.error || data.errors) {
            const errs = data.error || JSON.stringify(data.errors);
            if (window.showModal) showModal('Error', errs, 'error'); else alert(errs);
        }
    }).catch(err => { console.error(err); if (window.showModal) showModal('Error','Failed to login', 'error'); else alert('Failed to login'); });
});
</script>
@endsection
