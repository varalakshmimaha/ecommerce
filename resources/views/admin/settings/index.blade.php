@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <div class="admin-card">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Company Settings</h2>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                    @if($settings['company_logo'])
                    <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Logo" class="w-32 h-32 object-contain mb-2">
                    @endif
                    <input type="file" name="company_logo" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
                    @if($settings['qr_code'])
                    <img src="{{ asset('storage/' . $settings['qr_code']) }}" alt="QR Code" class="w-32 h-32 object-contain mb-2">
                    @endif
                    <input type="file" name="qr_code" accept="image/*" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Description</label>
                <textarea name="company_description" rows="3" class="input-field">{{ $settings['company_description'] }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="{{ $settings['whatsapp_number'] }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ $settings['phone_number'] }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ $settings['email'] }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3" class="input-field">{{ $settings['address'] }}</textarea>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mt-8 mb-4">Bank Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ $settings['bank_name'] }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ $settings['bank_account_number'] }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">IFSC Code</label>
                    <input type="text" name="bank_ifsc" value="{{ $settings['bank_ifsc'] }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name</label>
                    <input type="text" name="bank_account_holder" value="{{ $settings['bank_account_holder'] }}" class="input-field">
                </div>
            </div>
            <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Save Settings</button>
        </form>
    </div>
</div>
@endsection

