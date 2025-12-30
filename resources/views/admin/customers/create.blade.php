@extends('layouts.admin')

@section('title', 'Add Customer')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-heading">Add Customer</h1>
            <p class="text-text-muted mt-1">Create a new customer account</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Customers
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-6">
            @csrf
            
            <!-- Customer Information -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Customer Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-text-heading mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="Enter customer name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-text-heading mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="customer@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-text-heading mb-2">Mobile Number *</label>
                        <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="+91 98765 43210">
                        @error('mobile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-2">Status</label>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                   class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                            <label for="is_active" class="ml-2 text-sm text-text-heading">Active Customer</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Address Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-text-heading mb-2">Street Address</label>
                        <textarea id="address" name="address" rows="2" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                                  placeholder="123 Main Street, Apartment 4B">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-text-heading mb-2">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="Mumbai">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-text-heading mb-2">State</label>
                        <input type="text" id="state" name="state" value="{{ old('state') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="Maharashtra">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-text-heading mb-2">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="400001">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-text-heading mb-2">Country</label>
                        <input type="text" id="country" name="country" value="{{ old('country', 'India') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="India">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Additional Information
                </h3>
                <div>
                    <label for="notes" class="block text-sm font-medium text-text-heading mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                              placeholder="Add any additional notes about this customer...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.customers.index') }}" 
                   class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                    Cancel
                </a>
                <div class="flex gap-3">
                    <button type="submit" 
                            class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                        Create Customer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
