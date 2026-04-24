@extends('layouts.admin')

@section('title', $rm->exists ? 'Edit RM' : 'Add RM')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-3xl font-bold text-text-heading mb-6">{{ $rm->exists ? 'Edit Relationship Manager' : 'Add Relationship Manager' }}</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $rm->exists ? route('admin.rms.update', $rm) : route('admin.rms.store') }}" class="bg-white rounded-xl shadow-sm border border-ui-border p-6 space-y-4">
        @csrf
        @if($rm->exists) @method('PUT') @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $rm->name) }}" required class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Mobile *</label>
                <input type="text" name="mobile" value="{{ old('mobile', $rm->mobile) }}" required maxlength="15" class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $rm->email) }}" class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Password {{ $rm->exists ? '(leave blank to keep)' : '*' }}</label>
                <input type="text" name="password" minlength="6" {{ $rm->exists ? '' : 'required' }} class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-text-heading mb-1">Assign to Manager</label>
                <select name="parent_id" class="w-full px-3 py-2 border border-ui-border rounded-lg">
                    <option value="">— none —</option>
                    @foreach($managers as $m)
                        <option value="{{ $m->id }}" {{ old('parent_id', $rm->parent_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2.5 rounded-lg font-semibold">{{ $rm->exists ? 'Update' : 'Create RM' }}</button>
            <a href="{{ route('admin.rms.index') }}" class="text-text-muted hover:text-text-heading text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
