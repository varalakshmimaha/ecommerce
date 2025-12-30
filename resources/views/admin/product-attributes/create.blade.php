@extends('layouts.admin')

@section('title', 'Create Product Attribute')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create Product Attribute</h2>
        <a href="{{ route('admin.product-attributes.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Attributes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.product-attributes.store') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Attribute Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                           placeholder="e.g., Color, Size, Material"
                           required>
                    <p class="mt-1 text-sm text-gray-500">
                        The name of the attribute (e.g., Color, Size, Material)
                    </p>
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                              placeholder="Optional description for this attribute">{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Optional description to help explain this attribute
                    </p>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Sort Order
                    </label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="{{ old('sort_order', 0) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                           placeholder="0">
                    <p class="mt-1 text-sm text-gray-500">
                        Order in which this attribute appears (0 = first)
                    </p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}
                               class="w-4 h-4 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">
                        Whether this attribute is available for use
                    </p>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.product-attributes.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white rounded-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Create Attribute
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    
    // Only auto-fill if slug field is empty or hasn't been manually changed
    const slugField = document.getElementById('slug');
    if (!slugField || slugField.value === '' || slugField.dataset.autoGenerated === 'true') {
        if (slugField) {
            slugField.value = slug;
            slugField.dataset.autoGenerated = 'true';
        }
    }
});

// Mark slug as manually changed if user edits it
const slugField = document.getElementById('slug');
if (slugField) {
    slugField.addEventListener('input', function() {
        this.dataset.autoGenerated = 'false';
    });
}
</script>
@endpush
