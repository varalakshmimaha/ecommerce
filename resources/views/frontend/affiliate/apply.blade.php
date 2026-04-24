@extends('layouts.frontend')

@section('title', 'Affiliate Application')

@section('content')
<section class="bg-gradient-to-br from-gray-50 to-white py-10 min-h-screen">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-text-heading">Affiliate Application</h1>
            <p class="text-text-muted mt-1">Fill in your KYC and bank details below. Approval usually takes 1–2 business days.</p>
        </div>

        @if($user->affiliate_status === 'rejected' && $profile->rejection_reason)
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                <div class="font-semibold mb-1">Your previous application was rejected.</div>
                <div class="text-sm">Reason: {{ $profile->rejection_reason }}</div>
                <div class="text-sm mt-2">Please update the details below and resubmit.</div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('become.affiliate.apply.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-text-heading mb-4">Your Account</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Name</label>
                        <input type="text" value="{{ $user->name }}" readonly
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-text-muted">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Mobile</label>
                        <input type="text" value="{{ $user->mobile }}" readonly
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-text-muted">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-text-heading mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold"
                               placeholder="you@example.com">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-text-heading mb-4">Address</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-text-heading mb-1">Address *</label>
                        <input type="text" name="address" value="{{ old('address', $profile->address) }}" required
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">City *</label>
                        <input type="text" name="city" value="{{ old('city', $profile->city) }}" required
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">State *</label>
                        <input type="text" name="state" value="{{ old('state', $profile->state) }}" required
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Pincode *</label>
                        <input type="text" name="pincode" value="{{ old('pincode', $profile->pincode) }}" required maxlength="10"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-text-heading mb-4">Bank Details <span class="text-xs text-text-muted font-normal">(for commission payouts)</span></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Account Holder Name *</label>
                        <input type="text" name="account_holder" value="{{ old('account_holder', $profile->account_holder ?? $user->name) }}" required
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Bank Name *</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" required
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Account Number *</label>
                        <input type="text" name="account_number" value="{{ old('account_number', $profile->account_number) }}" required
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">IFSC Code *</label>
                        <input type="text" name="ifsc" value="{{ old('ifsc', $profile->ifsc) }}" required maxlength="11"
                               style="text-transform: uppercase"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold uppercase"
                               placeholder="HDFC0001234">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-text-heading mb-1">UPI ID <span class="text-xs text-text-muted">(optional)</span></label>
                        <input type="text" name="upi_id" value="{{ old('upi_id', $profile->upi_id) }}"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold"
                               placeholder="name@bank">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-text-heading mb-4">KYC Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">PAN Number *</label>
                        <input type="text" name="pan_number" value="{{ old('pan_number', $profile->pan_number) }}" required maxlength="10"
                               style="text-transform: uppercase"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold uppercase"
                               placeholder="ABCDE1234F">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Aadhaar Last 4 Digits *</label>
                        <input type="text" name="aadhaar_last4" value="{{ old('aadhaar_last4', $profile->aadhaar_last4) }}" required maxlength="4" pattern="[0-9]{4}"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold"
                               placeholder="1234">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-text-heading mb-1">KYC Document * <span class="text-xs text-text-muted">(PAN or Aadhaar copy, max 2MB, jpg/png/pdf)</span></label>
                        <input type="file" name="kyc_doc" required accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
                        @if($profile->kyc_doc_path)
                            <p class="text-xs text-text-muted mt-1">Previously uploaded: <a href="{{ asset('storage/' . $profile->kyc_doc_path) }}" target="_blank" class="text-brand-gold underline">view</a>. Uploading a new file will replace it.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <label class="flex items-start gap-3">
                    <input type="checkbox" name="terms" value="1" required
                           class="mt-1 rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    <span class="text-sm text-text-heading">
                        I agree to the <strong>affiliate terms and commission policy</strong>. I confirm the KYC details above are accurate and that commissions may be held or reversed in case of policy violations.
                    </span>
                </label>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Submit Application
                </button>
                <a href="{{ route('become.affiliate') }}" class="text-text-muted hover:text-text-heading text-sm">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
