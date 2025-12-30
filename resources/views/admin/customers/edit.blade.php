@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-heading">Edit Customer</h1>
            <p class="text-text-muted mt-1">Update customer information</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Customers
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
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
                        <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="Enter customer name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-text-heading mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="customer@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-text-heading mb-2">Mobile Number *</label>
                        <input type="tel" id="mobile" name="mobile" value="{{ old('mobile', $customer->mobile) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="+91 98765 43210">
                        @error('mobile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-text-heading mb-2">Password (leave blank to keep current)</label>
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="Enter new password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-text-heading mb-2">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                               placeholder="Confirm new password">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-2">Status</label>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_verified" name="is_verified" value="1" 
                                   {{ old('is_verified', $customer->is_verified) ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                            <label for="is_verified" class="ml-2 text-sm text-text-heading">Verified Customer</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-text-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Addresses ({{ $customer->addresses->count() }})
                    </h3>
                    <button type="button" onclick="addNewAddress()" class="bg-brand-gold text-white px-4 py-2 rounded-lg font-semibold hover:bg-brand-amber transition-all duration-300 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Address
                    </button>
                </div>
                
                <!-- Existing Addresses -->
                <div id="addresses-container" class="space-y-4 mb-4">
                    @foreach($customer->addresses as $index => $address)
                        <div class="address-item border border-gray-200 rounded-lg p-4 @if($address->is_default) border-brand-gold @endif" data-index="{{ $index }}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    @if($address->is_default)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-brand-gold text-white">
                                            Default
                                        </span>
                                    @endif
                                    <span class="text-sm font-medium text-text-heading">Address {{ $index + 1 }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="setDefaultAddress({{ $index }})" class="text-brand-gold hover:text-brand-amber text-sm font-medium" @if($address->is_default) disabled @endif>
                                        Set as Default
                                    </button>
                                    <button type="button" onclick="removeAddress({{ $index }})" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-text-heading mb-1">Street Address</label>
                                    <textarea name="addresses[{{ $index }}][address]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="123 Main Street, Apartment 4B">{{ $address->address }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-heading mb-1">City</label>
                                    <input type="text" name="addresses[{{ $index }}][city]" value="{{ $address->city }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="Mumbai">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-heading mb-1">State</label>
                                    <input type="text" name="addresses[{{ $index }}][state]" value="{{ $address->state }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="Maharashtra">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-heading mb-1">Postal Code</label>
                                    <input type="text" name="addresses[{{ $index }}][pincode]" value="{{ $address->pincode }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="400001">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-heading mb-1">Country</label>
                                    <input type="text" name="addresses[{{ $index }}][country]" value="{{ $address->country ?? 'India' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="India">
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="addresses[{{ $index }}][is_default]" value="1" {{ $address->is_default ? 'checked' : '' }} class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                                    <label class="ml-2 text-sm text-text-heading">Default Address</label>
                                </div>
                            </div>
                            <input type="hidden" name="addresses[{{ $index }}][id]" value="{{ $address->id }}">
                        </div>
                    @endforeach
                </div>
                
                <!-- New Address Template (hidden) -->
                <div id="new-address-template" class="address-item border border-gray-200 rounded-lg p-4 hidden">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-text-heading">New Address</span>
                        </div>
                        <button type="button" onclick="removeNewAddress(this)" class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-text-heading mb-1">Street Address</label>
                            <textarea name="addresses[new_0][address]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="123 Main Street, Apartment 4B"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">City</label>
                            <input type="text" name="addresses[new_0][city]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="Mumbai">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">State</label>
                            <input type="text" name="addresses[new_0][state]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="Maharashtra">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Postal Code</label>
                            <input type="text" name="addresses[new_0][pincode]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="400001">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Country</label>
                            <input type="text" name="addresses[new_0][country]" value="India" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-transparent text-sm" placeholder="India">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="addresses[new_0][is_default]" value="1" class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                            <label class="ml-2 text-sm text-text-heading">Default Address</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex gap-3">
                    <a href="{{ route('admin.customers.show', $customer) }}" 
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                        View Customer
                    </a>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.customers.index') }}" 
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                        Update Customer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let newAddressCounter = 0;

function addNewAddress() {
    newAddressCounter++;
    const template = document.getElementById('new-address-template');
    const clone = template.cloneNode(true);
    clone.id = '';
    clone.classList.remove('hidden');
    
    // Update all input names to use the new counter
    const inputs = clone.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace('new_0', 'new_' + newAddressCounter));
        }
    });
    
    // Update the label text
    const label = clone.querySelector('.text-sm.font-medium');
    if (label) {
        label.textContent = 'New Address ' + (newAddressCounter + 1);
    }
    
    document.getElementById('addresses-container').appendChild(clone);
}

function removeAddress(index) {
    if (confirm('Are you sure you want to remove this address?')) {
        const addressItem = document.querySelector(`[data-index="${index}"]`);
        if (addressItem) {
            // Add a hidden input to mark for deletion
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `addresses[${index}][delete]`;
            hiddenInput.value = '1';
            addressItem.appendChild(hiddenInput);
            addressItem.style.display = 'none';
        }
    }
}

function removeNewAddress(button) {
    if (confirm('Are you sure you want to remove this address?')) {
        const addressItem = button.closest('.address-item');
        addressItem.remove();
    }
}

function setDefaultAddress(index) {
    // Uncheck all default checkboxes
    const checkboxes = document.querySelectorAll('input[name*="[is_default]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Check the selected one
    const selectedCheckbox = document.querySelector(`input[name="addresses[${index}][is_default]"]`);
    if (selectedCheckbox) {
        selectedCheckbox.checked = true;
    }
    
    // Disable all set default buttons
    const buttons = document.querySelectorAll('button[onclick^="setDefaultAddress"]');
    buttons.forEach(button => {
        button.disabled = true;
    });
}

// Handle default checkbox changes
document.addEventListener('change', function(e) {
    if (e.target.name && e.target.name.includes('[is_default]') && e.target.checked) {
        // Uncheck all other default checkboxes
        const checkboxes = document.querySelectorAll('input[name*="[is_default]"]');
        checkboxes.forEach(checkbox => {
            if (checkbox !== e.target) {
                checkbox.checked = false;
            }
        });
        
        // Disable all set default buttons
        const buttons = document.querySelectorAll('button[onclick^="setDefaultAddress"]');
        buttons.forEach(button => {
            button.disabled = true;
        });
    }
});
</script>
@endsection
