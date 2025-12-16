@extends('layouts.admin')

@section('title', 'Pages')

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#D4AF37] to-[#B8962E] bg-clip-text text-transparent">Pages Management</h2>
        <a href="{{ route('admin.pages.create') }}" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Page
            </span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Navbar</th>
                    <th>Footer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td class="font-semibold">{{ $page->title }}</td>
                        <td class="text-gray-600 text-sm font-mono">{{ $page->slug }}</td>
                        <td>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $page->show_in_navbar ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $page->show_in_navbar ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $page->show_in_footer ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $page->show_in_footer ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $page->is_active ? 'bg-gradient-to-r from-green-50 to-green-100 text-green-700 border border-green-200' : 'bg-gradient-to-r from-yellow-50 to-yellow-100 text-yellow-700 border border-yellow-200' }}">
                                {{ $page->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="space-x-2">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white rounded-lg text-sm font-medium hover:shadow-md transition-all duration-300">
                                Edit
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this page?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 hover:shadow-md transition-all duration-300">Delete</button>
                            </form>
                            <a href="{{ route('page.show', $page) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 hover:shadow-md transition-all duration-300">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">No pages found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $pages->links() }}</div>
</div>
@endsection
