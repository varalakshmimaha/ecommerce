@extends('layouts.admin')

@section('title', 'Edit Page')

@section('content')
<div class="max-w-4xl overflow-x-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Page</h2>

        <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="input-field" placeholder="e.g., About Us">
                @error('title')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                <textarea name="content" id="editor" required class="input-field">{{ old('content', $page->content) }}</textarea>
                @error('content')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order) }}" class="input-field">
                    @error('sort_order')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-end">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_in_navbar" {{ old('show_in_navbar', $page->show_in_navbar) ? 'checked' : '' }} class="w-4 h-4">
                        <span class="text-sm font-medium text-gray-700">Show in Navbar</span>
                    </label>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_in_footer" {{ old('show_in_footer', $page->show_in_footer) ? 'checked' : '' }} class="w-4 h-4">
                        <span class="text-sm font-medium text-gray-700">Show in Footer</span>
                    </label>
                </div>
            </div>

            <div class="flex items-end">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="is_active" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="w-4 h-4">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" class="btn-primary">Update Page</button>
                <a href="{{ route('admin.pages.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.getElementById('editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });
</script>
