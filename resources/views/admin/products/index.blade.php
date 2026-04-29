@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<!-- Quick Status Filter Bar -->
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">All</a>
    <a href="{{ route('admin.products.index', ['status' => 'published']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'published' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Published</a>
    <a href="{{ route('admin.products.index', ['status' => 'unpublished']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'unpublished' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Unpublished</a>
    <a href="{{ route('admin.products.index', ['stock_status' => 'out_of_stock']) }}" class="px-4 py-2 rounded-lg {{ request('stock_status') == 'out_of_stock' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Out of Stock</a>
    <a href="{{ route('admin.products.index', ['stock_status' => 'low_stock']) }}" class="px-4 py-2 rounded-lg {{ request('stock_status') == 'low_stock' ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white' : 'bg-gray-200 text-gray-700' }}">Low Stock</a>
</div>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-text-heading">Products</h2>
    <a href="{{ route('admin.products.create') }}" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Product
    </a>
</div>

<!-- Search and Filter Section -->
<div class="admin-card mb-6">
    <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name or SKU..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                </div>
            </div>
            
            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Brand Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select name="brand_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="unpublished" {{ request('status') == 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                </select>
            </div>
        </div>
        
        <!-- Second Row of Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Stock Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                <select name="stock_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                    <option value="">All Stock</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock (≤10)</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            
            <!-- Min Price Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                <input type="number" 
                       name="min_price" 
                       value="{{ request('min_price') }}" 
                       placeholder="0.00" 
                       step="0.01"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
            </div>
            
            <!-- Max Price Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                <input type="number" 
                       name="max_price" 
                       value="{{ request('max_price') }}" 
                       placeholder="99999.99" 
                       step="0.01"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
            </div>
            
            <!-- Sort By -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="selling_price" {{ request('sort_by') == 'selling_price' ? 'selected' : '' }}>Price</option>
                    <option value="stock_quantity" {{ request('sort_by') == 'stock_quantity' ? 'selected' : '' }}>Stock Quantity</option>
                </select>
            </div>
            
            <!-- Sort Order -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                <select name="sort_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition-all duration-300">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>
        </div>
        
        <!-- Filter Actions -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                @if(request()->hasAny(['search', 'category_id', 'brand_id', 'status', 'stock_status', 'min_price', 'max_price']))
                    <span class="text-brand-gold font-medium">{{ $products->total() }}</span> products found
                @else
                    Showing all {{ $products->total() }} products
                @endif
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-300">
                    Clear Filters
                </a>
                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>

<div class="admin-card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="animate-fade-in">
                    <td>
                        <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg">
                    </td>
                    <td class="font-medium">{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>₹{{ number_format($product->selling_price, 2) }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-gold hover:text-brand-burnt">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">No products found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection

