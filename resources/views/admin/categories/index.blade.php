@extends('layouts.admin')

@section('title', 'Categories & Sub-Categories')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    <!-- Categories List -->
    <div class="lg:col-span-2">
        <div class="admin-card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Categories</h2>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($categories as $category)
                <div id="category-{{ $category->id }}" class="border rounded-lg p-4"
                     data-name="{{ e($category->name) }}"
                     data-description="{{ e($category->description) }}"
                     data-sort_order="{{ $category->sort_order }}"
                >
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-4 flex-1">
                            @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $category->subCategories->count() }} sub-categories</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="editCategory({{ $category->id }})" class="text-[#D4AF37] text-sm">Edit</button>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Sub-categories for this category -->
                    @if($category->subCategories->count() > 0)
                    <div class="ml-4 mt-3 space-y-2 border-l-2 border-gray-300 pl-4">
                        @foreach($category->subCategories as $subcat)
                        <div id="subcat-{{ $subcat->id }}" class="flex items-center justify-between py-2 bg-gray-50 px-3 rounded text-sm"
                             data-name="{{ e($subcat->name) }}"
                             data-description="{{ e($subcat->description) }}"
                             data-sort_order="{{ $subcat->sort_order }}"
                        >
                            <div class="flex items-center space-x-2 flex-1">
                                @if($subcat->image)
                                <img src="{{ asset('storage/' . $subcat->image) }}" alt="{{ $subcat->name }}" class="w-8 h-8 object-cover rounded">
                                @endif
                                <span class="font-medium text-gray-700">{{ $subcat->name }}</span>
                            </div>
                            <div class="flex space-x-1">
                                <button type="button" onclick="editSubCategory({{ $category->id }}, {{ $subcat->id }})" class="text-[#D4AF37] text-xs">Edit</button>
                                <form action="{{ route('admin.sub-categories.destroy', $subcat) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 text-xs">Delete</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Forms -->
    <div class="space-y-6">
        <!-- Add Category Form -->
        <div class="admin-card">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Add Category</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="input-field text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="input-field text-sm">
                </div>
                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full text-sm py-2">Add Category</button>
            </form>
        </div>
        
        <!-- Add Sub-Category Form -->
        <div class="admin-card">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Add Sub-Category</h2>
            <form id="addSubcategoryForm" action="{{ route('admin.sub-categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category_id" required class="input-field text-sm">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="input-field text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="input-field text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="input-field text-sm">
                </div>
                <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full text-sm py-2">Add Sub-Category</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="modal hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Edit Category</h3>
            <button onclick="closeEditCategory()" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="edit_cat_name" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_cat_description" rows="3" class="input-field"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (leave blank to keep)</label>
                    <input type="file" name="image" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_cat_sort_order" class="input-field w-32">
                </div>
                <div class="pt-4 flex gap-2 justify-end">
                    <button type="button" onclick="closeEditCategory()" class="btn">Cancel</button>
                    <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Sub-Category Modal -->
<div id="editSubcategoryModal" class="modal hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Edit Sub-Category</h3>
            <button onclick="closeEditSubCategory()" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editSubcategoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="edit_subcat_name" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_subcat_description" rows="3" class="input-field"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (leave blank to keep)</label>
                    <input type="file" name="image" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_subcat_sort_order" class="input-field w-32">
                </div>
                <div class="pt-4 flex gap-2 justify-end">
                    <button type="button" onclick="closeEditSubCategory()" class="btn">Cancel</button>
                    <button type="submit" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editCategory(id) {
        const el = document.getElementById('category-' + id);
        if (!el) return alert('Category not found');

        document.getElementById('edit_cat_name').value = el.dataset.name || '';
        document.getElementById('edit_cat_description').value = el.dataset.description || '';
        document.getElementById('edit_cat_sort_order').value = el.dataset.sort_order || 0;

        const form = document.getElementById('editCategoryForm');
        form.action = '{{ url("admin/categories") }}/' + id;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }

    function closeEditCategory() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    }

    function editSubCategory(categoryId, subcatId) {
        const el = document.getElementById('subcat-' + subcatId);
        if (!el) return alert('Sub-category not found');

        document.getElementById('edit_subcat_name').value = el.dataset.name || '';
        document.getElementById('edit_subcat_description').value = el.dataset.description || '';
        document.getElementById('edit_subcat_sort_order').value = el.dataset.sort_order || 0;

        const form = document.getElementById('editSubcategoryForm');
        form.action = '{{ url("admin/sub-categories") }}/' + subcatId;
        document.getElementById('editSubcategoryModal').classList.remove('hidden');
    }

    function closeEditSubCategory() {
        document.getElementById('editSubcategoryModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    document.getElementById('editCategoryModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditCategory();
    });
    document.getElementById('editSubcategoryModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditSubCategory();
    });
</script>

@endsection

