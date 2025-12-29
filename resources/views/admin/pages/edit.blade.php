@extends('layouts.admin')

@section('title', 'Edit Page')

@section('content')
<div class="w-full max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center text-text-muted hover:text-brand-gold transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Pages
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson bg-clip-text text-transparent mb-8">Edit Page</h2>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pages.update', $page) }}" method="POST" id="pageForm" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-[#1A1A1A] mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="input-field" placeholder="e.g., About Us">
                @error('title')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-[#1A1A1A] mb-2">Content *</label>
                <textarea name="content" id="editor" class="input-field" rows="10">{{ old('content', $page->content) }}</textarea>
                <input type="hidden" name="content_required" id="content_required" required value="{{ old('content', $page->content) }}">
                @error('content')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-[#1A1A1A] mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order) }}" class="input-field" placeholder="0">
                    @error('sort_order')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-end">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="hidden" name="show_in_navbar" value="0">
                        <input type="checkbox" name="show_in_navbar" value="1" {{ old('show_in_navbar', $page->show_in_navbar) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-brand-gold focus:ring-brand-gold accent-brand-gold">
                        <span class="text-sm font-medium text-text-heading">Show in Navbar</span>
                    </label>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="hidden" name="show_in_footer" value="0">
                        <input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', $page->show_in_footer) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-brand-gold focus:ring-brand-gold accent-brand-gold">
                        <span class="text-sm font-medium text-text-heading">Show in Footer</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-lg">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-brand-gold focus:ring-brand-gold accent-brand-gold">
                    <span class="text-sm font-semibold text-text-heading">Active (Publish this page)</span>
                </label>
            </div>

            <div class="flex gap-4 pt-6 border-t border-gray-100">
                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Update Page
                </button>
                <a href="{{ route('admin.pages.index') }}" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-all duration-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.0/classic/ckeditor.js"></script>
<script>
    let editorInstance;
    ClassicEditor
        .create(document.getElementById('editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        })
        .then(editor => {
            editorInstance = editor;

            // Update hidden field on editor change
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                document.getElementById('editor').value = data;
                document.getElementById('content_required').value = data;
            });

            // Sync editor data before form submit
            document.getElementById('pageForm').addEventListener('submit', function(e) {
                const data = editor.getData();
                document.getElementById('editor').value = data;
                document.getElementById('content_required').value = data;
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>
