@extends('layouts.admin')

@section('title', 'Banners')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
    <div class="admin-card">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Banners</h2>
        <div class="space-y-4">
            @forelse($banners as $banner)
            <div id="banner-{{ $banner->id }}" class="border rounded-lg p-4" 
                 data-title="{{ e($banner->title) }}"
                 data-description="{{ e($banner->description) }}"
                 data-link="{{ e($banner->link) }}"
                 data-sort_order="{{ $banner->sort_order }}"
                 data-is_active="{{ $banner->is_active }}"
            >
                <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover rounded-lg mb-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold">{{ $banner->title ?? 'No Title' }}</h3>
                        <p class="text-sm text-gray-500">{{ $banner->is_active ? 'Active' : 'Inactive' }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editBanner({{ $banner->id }})" class="text-primary-600">Edit</button>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-500">No banners found</p>
            @endforelse
        </div>
    </div>
    
    <div class="admin-card">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add Banner</h2>
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="title" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="input-field"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image *</label>
                <input type="file" name="image" accept="image/*" required class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Link (URL or path like /products)</label>
                <input type="text" name="link" class="input-field" placeholder="/products or https://example.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="0" class="input-field">
            </div>
            <button type="submit" class="btn-primary w-full">Add Banner</button>
        </form>
    </div>
</div>
<!-- Edit Banner Modal -->
<div id="editBannerModal" class="modal hidden">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Edit Banner</h3>
            <button onclick="closeEditModal()" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editBannerForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="is_active" value="0" id="edit_is_active_hidden">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="edit_title" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="input-field"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (leave blank to keep)</label>
                    <input type="file" name="image" id="edit_image" accept="image/*" class="input-field">
                    <p class="text-xs text-gray-500 mt-1">Select a new image to replace the existing one</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link (URL or path like /products)</label>
                    <input type="text" name="link" id="edit_link" class="input-field" placeholder="/products or https://example.com">
                </div>
                <div class="flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="edit_sort_order" class="input-field w-32">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="mr-2">
                        <label for="edit_is_active" class="text-sm">Active</label>
                    </div>
                </div>
                <div class="pt-4 flex gap-2 justify-end">
                    <button type="button" onclick="closeEditModal()" class="btn">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const adminBannersBase = "{{ url('admin/banners') }}";

    function editBanner(id) {
        const el = document.getElementById('banner-' + id);
        if (!el) return alert('Banner not found');

        document.getElementById('edit_title').value = el.dataset.title || '';
        document.getElementById('edit_description').value = el.dataset.description || '';
        document.getElementById('edit_link').value = el.dataset.link || '';
        document.getElementById('edit_sort_order').value = el.dataset.sort_order || 0;
        document.getElementById('edit_is_active').checked = (el.dataset.is_active === '1' || el.dataset.is_active === 'true');

        const form = document.getElementById('editBannerForm');
        form.action = adminBannersBase + '/' + id;

        document.getElementById('editBannerModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editBannerModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('editBannerModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    // Handle form submission
    document.getElementById('editBannerForm').addEventListener('submit', function(e){
        // Update hidden is_active field based on checkbox state
        const checkbox = document.getElementById('edit_is_active');
        document.getElementById('edit_is_active_hidden').value = checkbox.checked ? '1' : '0';

        // Allow normal form submit - browser will handle multipart/form-data correctly
    });
</script>
@endsection

