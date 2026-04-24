@extends('layouts.admin')

@section('title', $manager->exists ? 'Edit Manager' : 'Add Manager')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-3xl font-bold text-text-heading mb-6">{{ $manager->exists ? 'Edit Manager' : 'Add Manager' }}</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $manager->exists ? route('admin.managers.update', $manager) : route('admin.managers.store') }}" class="bg-white rounded-xl shadow-sm border border-ui-border p-6 space-y-4">
        @csrf
        @if($manager->exists) @method('PUT') @endif
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
                <label class="block text-sm font-medium text-text-heading mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $manager->email) }}" class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Password {{ $manager->exists ? '(leave blank to keep)' : '*' }}</label>
                <input type="text" name="password" minlength="6" {{ $manager->exists ? '' : 'required' }} class="w-full px-3 py-2 border border-ui-border rounded-lg">
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2.5 rounded-lg font-semibold">{{ $manager->exists ? 'Update' : 'Create Manager' }}</button>
            <a href="{{ route('admin.managers.index') }}" class="text-text-muted hover:text-text-heading text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
