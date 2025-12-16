@extends('layouts.admin')

@section('content')
<div class="p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-[#D4AF37] to-[#B8962E] bg-clip-text text-transparent mb-8">Profile Management</h1>

        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 text-green-800 px-6 py-4 rounded-lg shadow-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Profile Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 mb-8">
            <h2 class="text-xl font-semibold text-[#1A1A1A] mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile Information
            </h2>

            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-[#1A1A1A] mb-2">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300 bg-white text-[#1A1A1A]"
                        required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-[#1A1A1A] mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300 bg-white text-[#1A1A1A]"
                        required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-[#1A1A1A] mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Change Password
            </h2>

            <form method="POST" action="{{ route('admin.profile.password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-semibold text-[#1A1A1A] mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300 bg-white text-[#1A1A1A]"
                        required>
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="new_password" class="block text-sm font-semibold text-[#1A1A1A] mb-2">New Password</label>
                    <input type="password" id="new_password" name="new_password"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300 bg-white text-[#1A1A1A]"
                        required>
                    @error('new_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-[#1A1A1A] mb-2">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all duration-300 bg-white text-[#1A1A1A]"
                        required>
                </div>

                <button type="submit"
                    class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
