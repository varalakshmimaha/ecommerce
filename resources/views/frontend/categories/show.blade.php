@extends('layouts.frontend')

@section('title', 'Category - Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Category Header -->
    <div class="mb-8">
        <a href="/" class="text-primary-600 hover:text-primary-700 text-sm mb-4 inline-block">← Back to Home</a>
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg p-8 text-white">
            <h1 class="text-4xl font-bold mb-2" id="category-name">Loading...</h1>
            <p class="text-white opacity-90" id="category-description"></p>
        </div>
    </div>

    <!-- Sub-Categories Slider -->
    <div class="mb-8" id="subcategories-section">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Shop by Sub-Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4" id="subcategories-grid">
            <!-- Subcategories will load here -->
        </div>
    </div>

    <!-- Filter & Products -->
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <!-- Sub-Category Filter -->
            <div class="card p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Sub-Categories</h3>
                <div id="subcategory-filters" class="space-y-2">
                    <!-- Will load dynamically -->
                </div>
            </div>

            <!-- Brand Filter -->
            <div class="card p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Brands</h3>
                <div id="brand-filters" class="space-y-2">
                    <!-- Will load dynamically -->
                </div>
            </div>

            <!-- Price Filter -->
            <div class="card p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Price Range</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">Min Price</label>
                        <input type="number" id="min-price" min="0" class="input-field text-sm w-full" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">Max Price</label>
                        <input type="number" id="max-price" min="0" class="input-field text-sm w-full" placeholder="50000">
                    </div>
                    <button onclick="applyPriceFilter()" class="btn-primary w-full text-sm py-2">Apply</button>
                </div>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Products</h2>
                <div class="flex items-center space-x-4">
                    <select id="sort-select" class="input-field">
                        <option value="latest">Latest</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Products will load here -->
            </div>

            <div id="pagination" class="mt-8 flex justify-center">
                <!-- Pagination will load here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySlug = '{{ $slug }}';
    let categoryId = null;
    let currentPage = 1;
    let currentSubCategory = '';
    let currentBrand = '';
    let currentSort = 'latest';
    let minPrice = 0;
    let maxPrice = 999999;

    // Load category details
    function loadCategory() {
        fetch(`${API_BASE}/categories?slug=${categorySlug}`)
            .then(res => res.json())
            .then(data => {
                let cat = null;
                if (data.data && Array.isArray(data.data)) {
                    cat = data.data.find(c => c.slug === categorySlug);
                } else if (data.data && data.data.slug === categorySlug) {
                    cat = data.data;
                }
                
                if (!cat) {
                    document.getElementById('category-name').textContent = 'Category Not Found';
                    console.error('Category not found for slug:', categorySlug);
                    console.log('Available categories:', data);
                    return;
                }
                categoryId = cat.id;
                document.getElementById('category-name').textContent = cat.name;
                document.getElementById('category-description').textContent = cat.description || '';
                loadSubcategories();
                loadProducts();
            })
            .catch(err => {
                console.error('Error loading category:', err);
                document.getElementById('category-name').textContent = 'Error loading category';
            });
    }

    function loadSubcategories() {
        if (!categoryId) return;
        
        fetch(`${API_BASE}/categories/${categoryId}`)
            .then(res => res.json())
            .then(data => {
                const subcats = data.sub_categories || [];
                const gridEl = document.getElementById('subcategories-grid');
                const filterEl = document.getElementById('subcategory-filters');

                if (subcats.length === 0) {
                    document.getElementById('subcategories-section').style.display = 'none';
                    filterEl.innerHTML = '<p class="text-sm text-gray-500">No sub-categories</p>';
                    return;
                }

                // Grid display
                gridEl.innerHTML = subcats.map(subcat => `
                    <button onclick="filterBySubcategory(${subcat.id})" class="group text-left">
                        <div class="card p-4 h-full">
                            <div class="w-full h-24 bg-gray-100 rounded-lg flex items-center justify-center mb-3 overflow-hidden">
                                ${subcat.image ? `<img src="/storage/${subcat.image}" alt="${subcat.name}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">` : '<div class="text-3xl">📁</div>'}
                            </div>
                            <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors text-sm">${subcat.name}</h3>
                        </div>
                    </button>
                `).join('');

                // Filter display
                let filterHTML = `<label class="flex items-center"><input type="radio" name="subcat" value="" ${!currentSubCategory ? 'checked' : ''} class="mr-2 subcat-radio"><span class="font-medium">All</span></label>`;
                filterHTML += subcats.map(subcat => `
                    <label class="flex items-center">
                        <input type="radio" name="subcat" value="${subcat.id}" ${currentSubCategory == subcat.id ? 'checked' : ''} class="mr-2 subcat-radio">
                        <span>${subcat.name}</span>
                    </label>
                `).join('');
                filterEl.innerHTML = filterHTML;

                document.querySelectorAll('.subcat-radio').forEach(radio => {
                    radio.addEventListener('change', () => {
                        currentSubCategory = radio.value;
                        currentPage = 1;
                        loadProducts();
                    });
                });
            });
    }

    function loadBrands() {
        fetch(`${API_BASE}/brands`)
            .then(res => res.json())
            .then(data => {
                const brands = data.data || [];
                const filterEl = document.getElementById('brand-filters');

                if (brands.length === 0) {
                    filterEl.innerHTML = '<p class="text-sm text-gray-500">No brands</p>';
                    return;
                }

                let html = `<label class="flex items-center"><input type="radio" name="brand" value="" ${!currentBrand ? 'checked' : ''} class="mr-2 brand-radio"><span class="font-medium">All</span></label>`;
                html += brands.map(brand => `
                    <label class="flex items-center">
                        <input type="radio" name="brand" value="${brand.id}" ${currentBrand == brand.id ? 'checked' : ''} class="mr-2 brand-radio">
                        <span>${brand.name}</span>
                    </label>
                `).join('');
                filterEl.innerHTML = html;

                document.querySelectorAll('.brand-radio').forEach(radio => {
                    radio.addEventListener('change', () => {
                        currentBrand = radio.value;
                        currentPage = 1;
                        loadProducts();
                    });
                });
            });
    }

    function loadProducts() {
        let url = `${API_BASE}/products?per_page=12&page=${currentPage}&category_id=${categoryId}`;
        if (currentSort) url += `&sort=${encodeURIComponent(currentSort)}`;
        if (currentSubCategory) url += `&sub_category_id=${currentSubCategory}`;
        if (currentBrand) url += `&brand_id=${currentBrand}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const grid = document.getElementById('products-grid');
                grid.innerHTML = data.data.map(product => `
                    <div class="card overflow-hidden group">
                        <a href="/products/${product.slug}">
                            <div class="relative overflow-hidden">
                                <img src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                                ${product.discounted_price ? `<span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm">Sale</span>` : ''}
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${product.name}</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-primary-600">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                                    ${product.discounted_price ? `<span class="text-sm text-gray-500 line-through">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                                </div>
                                <button onclick="addToCart(${product.id}, 1); event.preventDefault();" class="btn-primary w-full mt-4">Add to Cart</button>
                            </div>
                        </a>
                    </div>
                `).join('');

                // Pagination
                const pagination = document.getElementById('pagination');
                if (data.last_page > 1) {
                    let paginationHTML = '<div class="flex space-x-2">';
                    if (data.current_page > 1) {
                        paginationHTML += `<button onclick="changePage(${data.current_page - 1})" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Previous</button>`;
                    }
                    for (let i = 1; i <= data.last_page; i++) {
                        if (i === data.current_page) {
                            paginationHTML += `<button class="px-4 py-2 bg-primary-600 text-white rounded-lg">${i}</button>`;
                        } else {
                            paginationHTML += `<button onclick="changePage(${i})" class="px-4 py-2 border rounded-lg hover:bg-gray-100">${i}</button>`;
                        }
                    }
                    if (data.current_page < data.last_page) {
                        paginationHTML += `<button onclick="changePage(${data.current_page + 1})" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Next</button>`;
                    }
                    paginationHTML += '</div>';
                    pagination.innerHTML = paginationHTML;
                }
            });
    }

    window.filterBySubcategory = function(subcatId) {
        currentSubCategory = subcatId;
        currentPage = 1;
        document.querySelector(`input[value="${subcatId}"]`)?.click();
    };

    window.applyPriceFilter = function() {
        minPrice = parseInt(document.getElementById('min-price').value) || 0;
        maxPrice = parseInt(document.getElementById('max-price').value) || 999999;
        currentPage = 1;
        loadProducts();
    };

    window.changePage = function(page) {
        currentPage = page;
        loadProducts();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    window.addToCart = function(productId, quantity) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existing = cart.find(item => item.product_id === productId);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({ product_id: productId, quantity: quantity });
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        if (window.updateCartCount) window.updateCartCount();
        if (window.showModal) showModal('Success', 'Added to cart', 'success');
    };

    document.getElementById('sort-select').addEventListener('change', function() {
        currentSort = this.value;
        currentPage = 1;
        loadProducts();
    });

    loadCategory();
    loadBrands();
    if (window.updateCartCount) window.updateCartCount();
});
</script>
@endsection
