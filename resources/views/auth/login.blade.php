<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ \App\Models\Setting::get('company_name', 'Suvee') }}</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="bg-gradient-to-br from-primary-500 to-primary-700 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md animate-scale-in">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ \App\Models\Setting::get('company_name', 'Suwish') }} Admin</h1>
            <p class="text-gray-600 mt-2">Sign in to your account</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 animate-slide-down">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" required
                    class="input-field @error('mobile') border-red-500 @enderror"
                    placeholder="Enter your mobile number">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="input-field @error('password') border-red-500 @enderror"
                    placeholder="Enter your password">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>

