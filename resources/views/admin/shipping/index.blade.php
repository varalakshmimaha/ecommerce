@extends('layouts.admin')

@section('title', 'Shipping')

@section('content')
<div class="max-w-md">
    <form action="{{ route('admin.shipping.update') }}" method="POST" class="space-y-4 admin-card p-6 bg-white">
        @csrf
        <h2 class="text-xl font-semibold">Shipping Settings</h2>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Default Shipping Charge (₹)</label>
            <input type="number" name="shipping_charge" step="0.01" value="{{ $shipping }}" class="input-field w-full">
        </div>
        <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Save</button>
    </form>
</div>
@endsection
