@extends('layouts.frontend')

@section('title', 'Category - Products')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
    <div class="container mx-auto px-4">
        <!-- Category Header - Mobile Optimized -->
        <div class="mb-4 animate-fade-in">
            <a href="/" class="text-brand-gold hover:text-brand-amber text-sm mb-2 inline-flex items-center gap-2 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
            <div class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson rounded-xl p-4 md:p-8 text-white shadow-xl shadow-brand-gold/20">
                <h1 class="text-2xl md:text-4xl font-bold mb-1 md:mb-2" id="category-name">Loading...</h1>
                <p class="text-white/80 text-sm md:text-lg" id="category-description"></p>
            </div>
        </div>

        <!-- Mobile Filter Toggle -->
        <div class="hide-in-desktop-view mb-4">
            <button onclick="toggleMobileFilters()" class="w-full bg-white border-2 border-brand-gold text-brand-gold py-3 px-4 rounded-xl font-semibold flex items-center justify-center gap-2 hover:bg-brand-gold hover:text-white transition-all duration-300 shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters & Categories
            </button>
        </div>

        <!-- Sub-Categories Slider - Mobile Optimized -->
        <div class="mb-6 animate-slide-up" id="subcategories-section">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg md:text-2xl font-bold text-text-heading flex items-center gap-2">
                    <span class="w-2 h-6 md:h-8 bg-brand-gold rounded"></span>
                    Sub-Categories
                </h2>
            </div>
            <div class="overflow-x-auto pb-2">
                <div class="flex gap-2 md:grid md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 md:gap-3 min-w-max justify-start" id="subcategories-grid">
                    <!-- Subcategories will load here -->
                </div>
            </div>
        </div>

        <!-- Filter & Products -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Mobile Filters (Collapsible) -->
            <div id="mobile-filters" class="lg:hidden hidden">
                <div class="fixed inset-0 bg-black/50 z-50" onclick="toggleMobileFilters()"></div>
                <div class="fixed top-0 left-0 right-0 h-full max-h-[80vh] overflow-y-auto bg-white z-50 rounded-b-2xl shadow-2xl">
                    <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-text-heading">Filters</h3>
                        <button onclick="toggleMobileFilters()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 space-y-4">
                        <!-- Sub-Category Filter -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-brand-gold mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                Sub-Categories
                            </h3>
                            <div id="subcategory-filters-mobile" class="space-y-2 text-text-muted">
                                <!-- Will load dynamically -->
                            </div>
                        </div>

                        <!-- Brand Filter -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-brand-gold mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Brands
                            </h3>
                            <div id="brand-filters-mobile" class="space-y-2 text-text-muted">
                                <!-- Will load dynamically -->
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-brand-gold mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Price Range
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm text-text-muted mb-2">Min Price</label>
                                    <input type="number" id="min-price-mobile" min="0" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted/50 focus:ring-2 focus:ring-brand-gold focus:border-transparent transition-all duration-300" placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm text-text-muted mb-2">Max Price</label>
                                    <input type="number" id="max-price-mobile" min="0" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted/50 focus:ring-2 focus:ring-brand-gold focus:border-transparent transition-all duration-300" placeholder="50000">
                                </div>
                                <button onclick="applyPriceFilterMobile()" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white py-2 rounded-lg font-semibold hover:shadow-lg hover:shadow-brand-gold/50 transition-all duration-300 transform hover:scale-105">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Sidebar Filters - Hidden -->
            <aside class="hide-in-mobile-view w-full lg:w-72 lg:flex-shrink-0" >
                <!-- Sub-Category Filter -->
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 mb-6 animate-slide-right">
                    <h3 class="font-semibold text-[#D4AF37] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Sub-Categories
                    </h3>
                    <div id="subcategory-filters" class="space-y-2 text-text-muted">
                        <!-- Will load dynamically -->
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 mb-6 animate-slide-right" style="animation-delay: 0.1s">
                    <h3 class="font-semibold text-[#D4AF37] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Brands
                    </h3>
                    <div id="brand-filters" class="space-y-2 text-text-muted">
                        <!-- Will load dynamically -->
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 animate-slide-right" style="animation-delay: 0.2s">
                    <h3 class="font-semibold text-[#D4AF37] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Price Range
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm text-text-muted mb-2">Min Price</label>
                            <input type="number" id="min-price" min="0" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted/50 focus:ring-2 focus:ring-brand-gold focus:border-transparent transition-all duration-300" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm text-text-muted mb-2">Max Price</label>
                            <input type="number" id="max-price" min="0" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-text-heading placeholder-text-muted/50 focus:ring-2 focus:ring-brand-gold focus:border-transparent transition-all duration-300" placeholder="50000">
                        </div>
                        <button onclick="applyPriceFilter()" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white py-2 rounded-lg font-semibold hover:shadow-lg hover:shadow-brand-gold/50 transition-all duration-300 transform hover:scale-105">Apply</button>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-4">
                    <h2 class="text-xl md:text-2xl font-bold text-text-heading flex items-center gap-2">
                        <span class="w-2 h-6 md:h-8 bg-brand-gold rounded"></span>
                        Products
                    </h2>
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <select id="sort-select" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-text-heading focus:ring-2 focus:ring-brand-gold focus:border-transparent transition-all duration-300 text-sm md:text-base">
                            <option value="latest">Latest</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 w-full">
                    <!-- Products will load here -->
                </div>

                <div id="pagination" class="mt-8 flex justify-center">
                    <!-- Pagination will load here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-right {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.6s ease-out;
}

.animate-slide-right {
    animation: slide-right 0.6s ease-out;
}

/* Custom utility classes */
.hide-in-mobile-view {
    display: block;
}

.hide-in-desktop-view {
    display: none;
}

@media (max-width: 1023px) {
    .hide-in-mobile-view {
        display: none !important;
    }
    
    .hide-in-desktop-view {
        display: block !important;
    }
}

@media (min-width: 1024px) {
    .hide-in-mobile-view {
        display: block !important;
    }
    
    .hide-in-desktop-view {
        display: none !important;
    }
}
</style>

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
                    filterEl.innerHTML = '<p class="text-sm text-text-muted/70">No sub-categories</p>';
                    return;
                }

                // Grid display
                gridEl.innerHTML = subcats.map(subcat => `
                    <button onclick="filterBySubcategory(${subcat.id})" class="flex flex-col items-center group w-full max-w-full bg-transparent border-none outline-none focus:outline-none">
                        <div class="w-24 h-24 mb-3 rounded-full overflow-hidden border-4 border-surface-light group-hover:border-brand-gold bg-gradient-to-tr from-surface-light to-surface-medium flex items-center justify-center shadow-md transition-all duration-300">
                            ${subcat.image ? `<img src="/storage/${subcat.image}" alt="${subcat.name}" class="w-full h-full object-cover scale-100 group-hover:scale-110 transition-transform duration-300">` : '<div class="text-4xl">📁</div>'}
                        </div>
                        <span class="font-semibold text-base md:text-lg text-text-heading group-hover:text-brand-gold transition-colors tracking-wide text-center">${subcat.name}</span>
                    </button>
                `).join('');

                // Desktop Filter display (hidden)
                let filterHTML = `<label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors"><input type="radio" name="subcat" value="" ${!currentSubCategory ? 'checked' : ''} class="mr-2 subcat-radio accent-brand-gold"><span class="font-medium">All</span></label>`;
                filterHTML += subcats.map(subcat => `
                    <label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors">
                        <input type="radio" name="subcat" value="${subcat.id}" ${currentSubCategory == subcat.id ? 'checked' : ''} class="mr-2 subcat-radio accent-brand-gold">
                        <span>${subcat.name}</span>
                    </label>
                `).join('');
                filterEl.innerHTML = filterHTML;

                // Mobile Filter display
                const mobileFilterEl = document.getElementById('subcategory-filters-mobile');
                if (mobileFilterEl) {
                    let mobileFilterHTML = `<label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors"><input type="radio" name="subcat-mobile" value="" ${!currentSubCategory ? 'checked' : ''} class="mr-2 subcat-radio-mobile accent-brand-gold"><span class="font-medium">All</span></label>`;
                    mobileFilterHTML += subcats.map(subcat => `
                        <label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors">
                            <input type="radio" name="subcat-mobile" value="${subcat.id}" ${currentSubCategory == subcat.id ? 'checked' : ''} class="mr-2 subcat-radio-mobile accent-brand-gold">
                            <span>${subcat.name}</span>
                        </label>
                    `).join('');
                    mobileFilterEl.innerHTML = mobileFilterHTML;

                    // Add event listeners for mobile filters
                    document.querySelectorAll('.subcat-radio-mobile').forEach(radio => {
                        radio.addEventListener('change', () => {
                            currentSubCategory = radio.value;
                            currentPage = 1;
                            loadProducts();
                            // Update desktop filter to match
                            const desktopRadio = document.querySelector(`.subcat-radio[value="${radio.value}"]`);
                            if (desktopRadio) desktopRadio.checked = true;
                        });
                    });
                }

                document.querySelectorAll('.subcat-radio').forEach(radio => {
                    radio.addEventListener('change', () => {
                        currentSubCategory = radio.value;
                        currentPage = 1;
                        loadProducts();
                        // Update mobile filter to match
                        const mobileRadio = document.querySelector(`.subcat-radio-mobile[value="${radio.value}"]`);
                        if (mobileRadio) mobileRadio.checked = true;
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
                    filterEl.innerHTML = '<p class="text-sm text-text-muted/70">No brands</p>';
                    const mobileFilterEl = document.getElementById('brand-filters-mobile');
                    if (mobileFilterEl) {
                        mobileFilterEl.innerHTML = '<p class="text-sm text-text-muted/70">No brands</p>';
                    }
                    return;
                }

                // Desktop Filter display (hidden)
                let html = `<label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors"><input type="radio" name="brand" value="" ${!currentBrand ? 'checked' : ''} class="mr-2 brand-radio accent-brand-gold"><span class="font-medium">All</span></label>`;
                html += brands.map(brand => `
                    <label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors">
                        <input type="radio" name="brand" value="${brand.id}" ${currentBrand == brand.id ? 'checked' : ''} class="mr-2 brand-radio accent-brand-gold">
                        <span>${brand.name}</span>
                    </label>
                `).join('');
                filterEl.innerHTML = html;

                // Mobile Filter display
                const mobileFilterEl = document.getElementById('brand-filters-mobile');
                if (mobileFilterEl) {
                    let mobileFilterHTML = `<label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors"><input type="radio" name="brand-mobile" value="" ${!currentBrand ? 'checked' : ''} class="mr-2 brand-radio-mobile accent-brand-gold"><span class="font-medium">All</span></label>`;
                    mobileFilterHTML += brands.map(brand => `
                        <label class="flex items-center cursor-pointer hover:text-brand-gold transition-colors">
                            <input type="radio" name="brand-mobile" value="${brand.id}" ${currentBrand == brand.id ? 'checked' : ''} class="mr-2 brand-radio-mobile accent-brand-gold">
                            <span>${brand.name}</span>
                        </label>
                    `).join('');
                    mobileFilterEl.innerHTML = mobileFilterHTML;

                    // Add event listeners for mobile filters
                    document.querySelectorAll('.brand-radio-mobile').forEach(radio => {
                        radio.addEventListener('change', () => {
                            currentBrand = radio.value;
                            currentPage = 1;
                            loadProducts();
                            // Update desktop filter to match
                            const desktopRadio = document.querySelector(`.brand-radio[value="${radio.value}"]`);
                            if (desktopRadio) desktopRadio.checked = true;
                        });
                    });
                }

                document.querySelectorAll('.brand-radio').forEach(radio => {
                    radio.addEventListener('change', () => {
                        currentBrand = radio.value;
                        currentPage = 1;
                        loadProducts();
                        // Update mobile filter to match
                        const mobileRadio = document.querySelector(`.brand-radio-mobile[value="${radio.value}"]`);
                        if (mobileRadio) mobileRadio.checked = true;
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

                if (!data.data || data.data.length === 0) {
                    grid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-[#6B6B6B] text-lg">No products found</p></div>';
                    return;
                }

                grid.innerHTML = data.data.map(product => `
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-lg hover:shadow-xl transition-all duration-300 group max-w-full">
                        <a href="/products/${product.slug}" class="block">
                            <div class="relative overflow-hidden">
                                <img src="/storage/${product.main_image}" alt="${product.name}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                                ${product.discounted_price ? `<span class="absolute top-2 right-2 bg-gradient-to-r from-red-500 to-red-600 text-white px-3 py-1 rounded-lg text-sm font-semibold shadow-lg">Sale</span>` : ''}
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-[#1A1A1A] mb-2 line-clamp-2 group-hover:text-[#D4AF37] transition-colors break-words">${product.name}</h3>
                                <div class="flex items-center space-x-2 mb-4 flex-wrap">
                                    <span class="text-lg font-bold text-[#D4AF37]">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                                    ${product.discounted_price ? `<span class="text-sm text-[#6B6B6B] line-through">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                                </div>
                                <button onclick="addToCart(${product.id}, 1); event.preventDefault();" class="w-full bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Add to Cart</button>
                            </div>
                        </a>
                    </div>
                `).join('');

                // Pagination
                const pagination = document.getElementById('pagination');
                if (data.last_page > 1) {
                    let paginationHTML = '<div class="flex flex-wrap justify-center gap-2">';
                    if (data.current_page > 1) {
                        paginationHTML += `<button onclick="changePage(${data.current_page - 1})" class="px-4 py-2 bg-white border border-gray-200 text-[#6B6B6B] rounded-lg hover:bg-[#D4AF37] hover:text-white transition-all duration-300">Previous</button>`;
                    }
                    for (let i = 1; i <= data.last_page; i++) {
                        if (i === data.current_page) {
                            paginationHTML += `<button class="px-4 py-2 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white rounded-lg font-semibold shadow-lg">${i}</button>`;
                        } else {
                            paginationHTML += `<button onclick="changePage(${i})" class="px-4 py-2 bg-white border border-gray-200 text-[#6B6B6B] rounded-lg hover:bg-[#D4AF37] hover:text-white transition-all duration-300">${i}</button>`;
                        }
                    }
                    if (data.current_page < data.last_page) {
                        paginationHTML += `<button onclick="changePage(${data.current_page + 1})" class="px-4 py-2 bg-white border border-gray-200 text-[#6B6B6B] rounded-lg hover:bg-[#D4AF37] hover:text-white transition-all duration-300">Next</button>`;
                    }
                    paginationHTML += '</div>';
                    pagination.innerHTML = paginationHTML;
                } else {
                    pagination.innerHTML = '';
                }
            });
    }

    window.filterBySubcategory = function(subcatId) {
        currentSubCategory = subcatId;
        currentPage = 1;
        document.querySelector(`input[value="${subcatId}"]`)?.click();
    };

    window.toggleMobileFilters = function() {
        const mobileFilters = document.getElementById('mobile-filters');
        mobileFilters.classList.toggle('hidden');
    };

    window.applyPriceFilter = function() {
        minPrice = parseInt(document.getElementById('min-price').value) || 0;
        maxPrice = parseInt(document.getElementById('max-price').value) || 999999;
        currentPage = 1;
        loadProducts();
    };

    window.applyPriceFilterMobile = function() {
        minPrice = parseInt(document.getElementById('min-price-mobile').value) || 0;
        maxPrice = parseInt(document.getElementById('max-price-mobile').value) || 999999;
        currentPage = 1;
        loadProducts();
        toggleMobileFilters();
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
        if (window.showModal) showModal('Success', 'Added to cart!', 'success');
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
