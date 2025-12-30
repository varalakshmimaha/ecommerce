@extends('layouts.admin')

@section('title', 'Theme Colors')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-text-heading">Theme Colors Management</h2>
    <a href="{{ route('admin.theme-colors.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Create Theme
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
@endif

<!-- Active Theme Preview -->
@if($activeTheme)
<div class="admin-card mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-text-heading">Active Theme</h3>
        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Currently Active</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="text-center">
            <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $activeTheme->brand_gold }}"></div>
            <p class="text-sm font-medium text-text-heading">Brand Gold</p>
            <p class="text-xs text-text-muted">{{ $activeTheme->brand_gold }}</p>
        </div>
        <div class="text-center">
            <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $activeTheme->brand_amber }}"></div>
            <p class="text-sm font-medium text-text-heading">Brand Amber</p>
            <p class="text-xs text-text-muted">{{ $activeTheme->brand_amber }}</p>
        </div>
        <div class="text-center">
            <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $activeTheme->brand_burnt }}"></div>
            <p class="text-sm font-medium text-text-heading">Brand Burnt</p>
            <p class="text-xs text-text-muted">{{ $activeTheme->brand_burnt }}</p>
        </div>
        <div class="text-center">
            <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $activeTheme->brand_crimson }}"></div>
            <p class="text-sm font-medium text-text-heading">Brand Crimson</p>
            <p class="text-xs text-text-muted">{{ $activeTheme->brand_crimson }}</p>
        </div>
    </div>
    <div class="flex justify-end mt-4">
        <a href="{{ route('admin.theme-colors.edit', $activeTheme) }}" class="text-brand-gold hover:text-brand-amber font-medium mr-4">Edit Theme</a>
        <a href="{{ route('admin.theme-colors.preview', $activeTheme) }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Preview</a>
    </div>
</div>
@endif

<!-- All Themes -->
<div class="admin-card">
    <h3 class="text-lg font-semibold text-text-heading mb-4">All Themes</h3>
    @if($themes->count() > 0)
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Preview</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($themes as $theme)
                    <tr>
                        <td class="font-medium">{{ $theme->name ?: 'Theme #' . $theme->id }}</td>
                        <td>
                            <div class="flex space-x-2">
                                <div class="w-8 h-8 rounded" style="background-color: {{ $theme->brand_gold }}" title="Brand Gold"></div>
                                <div class="w-8 h-8 rounded" style="background-color: {{ $theme->brand_amber }}" title="Brand Amber"></div>
                                <div class="w-8 h-8 rounded" style="background-color: {{ $theme->brand_burnt }}" title="Brand Burnt"></div>
                                <div class="w-8 h-8 rounded" style="background-color: {{ $theme->brand_crimson }}" title="Brand Crimson"></div>
                            </div>
                        </td>
                        <td>
                            @if($theme->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $theme->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.theme-colors.preview', $theme) }}" class="text-brand-gold hover:text-brand-amber">Preview</a>
                                <a href="{{ route('admin.theme-colors.edit', $theme) }}" class="text-brand-gold hover:text-brand-amber">Edit</a>
                                @if(!$theme->is_active)
                                    <form action="{{ route('admin.theme-colors.activate', $theme) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-brand-gold hover:text-brand-amber">Activate</button>
                                    </form>
                                @endif
                                @if(!$theme->is_active)
                                    <form action="{{ route('admin.theme-colors.destroy', $theme) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-text-muted mb-4">No themes created yet.</p>
            <a href="{{ route('admin.theme-colors.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Create First Theme</a>
        </div>
    @endif
</div>
@endsection
