<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Update Profile -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Update Profile</h2>
        <form id="profile-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" id="profile-name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="profile-email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                <input type="text" id="profile-mobile" name="mobile" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
            </div>
            <div id="profile-message" class="hidden"></div>
            <button type="submit" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Update Profile
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Change Password</h2>
        <form id="password-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" id="current-password" name="current_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" id="new-password" name="new_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" id="confirm-password" name="new_password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div id="password-message" class="hidden"></div>
            <button type="submit" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Change Password
            </button>
        </form>
    </div>
</div>
