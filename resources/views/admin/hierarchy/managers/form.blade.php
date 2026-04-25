@extends('layouts.admin')

@section('title', $manager->exists ? 'Edit Manager' : 'Add Manager')

@section('content')
<div class="max-w-4xl">
    <h1 class="text-3xl font-bold text-text-heading mb-6">{{ $manager->exists ? 'Edit Manager' : 'Add Manager' }}</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $manager->exists ? route('admin.managers.update', $manager) : route('admin.managers.store') }}"
          class="space-y-6">
        @csrf
        @if($manager->exists) @method('PUT') @endif

        {{-- Account Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
            <h2 class="text-base font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Account Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $manager->name) }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Mobile *</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $manager->mobile) }}" required maxlength="15" class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $manager->email) }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Password {{ $manager->exists ? '(leave blank to keep)' : '*' }}</label>
                    <input type="text" name="password" minlength="6" {{ $manager->exists ? '' : 'required' }} class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
            <h2 class="text-base font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Address
            </h2>
            @php $profile = $manager->affiliateProfile; @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-text-heading mb-1">Address *</label>
                    <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">City *</label>
                    <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">State *</label>
                    <input type="text" name="state" value="{{ old('state', $profile->state ?? '') }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Pincode *</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $profile->pincode ?? '') }}" required maxlength="10" class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
            </div>
        </div>

        {{-- Bank Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
            <h2 class="text-base font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Bank / Account Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Account Holder Name *</label>
                    <input type="text" name="account_holder" value="{{ old('account_holder', $profile->account_holder ?? '') }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Bank Name *</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name ?? '') }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Account Number *</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $profile->account_number ?? '') }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">IFSC Code *</label>
                    <input type="text" name="ifsc" value="{{ old('ifsc', $profile->ifsc ?? '') }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg font-mono uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">UPI ID <span class="text-text-muted font-normal">(optional)</span></label>
                    <input type="text" name="upi_id" value="{{ old('upi_id', $profile->upi_id ?? '') }}" class="w-full px-3 py-2 border border-ui-border rounded-lg">
                </div>
            </div>
        </div>

        {{-- KYC Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-ui-border p-6">
            <h2 class="text-base font-semibold text-text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                KYC Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">PAN Number *</label>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $profile->pan_number ?? '') }}" required maxlength="20" class="w-full px-3 py-2 border border-ui-border rounded-lg font-mono uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-1">Aadhaar Number * <span class="text-text-muted font-normal">(12 digits)</span></label>
                    <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number', $profile->aadhaar_number ?? '') }}" required maxlength="12" minlength="12" pattern="\d{12}" class="w-full px-3 py-2 border border-ui-border rounded-lg font-mono">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-text-heading mb-1">KYC Document {{ $manager->exists ? '(leave blank to keep existing)' : '*' }} <span class="text-text-muted font-normal">(PAN / Aadhaar copy — JPG, PNG, PDF, max 2MB)</span></label>
                    @if($profile && $profile->kyc_doc_path)
                        <div class="mb-2 text-xs text-green-700">Current document uploaded. Upload new file to replace.</div>
                    @endif
                    <input type="file" name="kyc_doc" {{ $manager->exists ? '' : 'required' }} accept=".jpg,.jpeg,.png,.pdf" class="w-full px-3 py-2 border border-ui-border rounded-lg text-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2.5 rounded-lg font-semibold">{{ $manager->exists ? 'Update Manager' : 'Create Manager' }}</button>
            <a href="{{ route('admin.managers.index') }}" class="text-text-muted hover:text-text-heading text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
