<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="card p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Categories</h3>
                <div id="category-filters" class="space-y-2">
                    <!-- Categories will be loaded here -->
                </div>
            </div>
            <div class="card p-6" id="subcategory-filters-container" style="display: none;">
                <h3 class="font-semibold text-gray-900 mb-4">Sub-Categories</h3>
                <div id="subcategory-filters" class="space-y-2">
                    <!-- Sub-categories will be loaded here -->
                </div>
            </div>
        </aside>
        
        <!-- Products Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Products</h1>
                <div class="flex items-center space-x-4">
                    <select id="sort-select" class="input-field">
                        <option value="latest">Latest</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                    </select>
                </div>
            </div>
            
            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Products will be loaded here -->
            </div>
            
            <div id="pagination" class="mt-8 flex justify-center">
                <!-- Pagination will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    let currentPage = 1;
    let currentCategory = urlParams.get('category_id') || '';
    let currentSubCategory = urlParams.get('sub_category_id') || '';
    let currentType = urlParams.get('type') || '';
    let currentSearch = urlParams.get('search') || '';
    let currentSort = 'latest';
    
    function loadProducts() {
        let url = `${API_BASE}/products?per_page=20&page=${currentPage}`;
        if (currentSort) url += `&sort=${encodeURIComponent(currentSort)}`;
        if (currentCategory) url += `&category_id=${currentCategory}`;
        if (currentSubCategory) url += `&sub_category_id=${currentSubCategory}`;
        if (currentType) url += `&type=${currentType}`;
        if (currentSearch) url += `&search=${currentSearch}`;
        
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
                                    <span class="text-lg font-bold text-[#D4AF37]">₹${parseFloat(product.discounted_price || product.selling_price).toFixed(2)}</span>
                                    ${product.discounted_price ? `<span class="text-sm text-gray-500 line-through">₹${parseFloat(product.selling_price).toFixed(2)}</span>` : ''}
                                </div>
                                <button onclick="addToCart(${product.id}, 1); event.preventDefault();" class="bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 w-full mt-4">Add to Cart</button>
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
                            paginationHTML += `<button class="px-4 py-2 bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white rounded-lg">${i}</button>`;
                        } else {
                            paginationHTML += `<button onclick="changePage(${i})" class="px-4 py-2 border rounded-lg hover:bg-gray-100">${i}</button>`;
                        }
                    }
                    if (data.current_page < data.last_page) {
                        paginationHTML += `<button onclick="changePage(${data.current_page + 1})" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Next</button>`;
                    }
                    paginationHTML += '</div>';
                    pagination.innerHTML = paginationHTML;
                } else {
                    pagination.innerHTML = '';
                }
            });
    }
    
    function loadCategories() {
        fetch(`${API_BASE}/categories`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('category-filters');
                // Add "All" option first
                let html = `
                    <label class="flex items-center">
                        <input type="radio" name="category" value="" ${currentCategory == '' ? 'checked' : ''} class="mr-2 category-radio">
                        <span class="font-medium">All Categories</span>
                    </label>
                `;
                // Add category options
                html += data.data.map(cat => `
                    <label class="flex items-center">
                        <input type="radio" name="category" value="${cat.id}" ${currentCategory == cat.id ? 'checked' : ''} class="mr-2 category-radio" data-cat-id="${cat.id}">
                        <span>${cat.name}</span>
                    </label>
                `).join('');
                
                container.innerHTML = html;
                
                document.querySelectorAll('.category-radio').forEach(radio => {
                    radio.addEventListener('change', function() {
                        currentCategory = this.value;
                        currentSubCategory = '';
                        currentPage = 1;
                        loadSubcategories();
                        loadProducts();
                    });
                });
            });
    }
    
    function loadSubcategories() {
        const container = document.getElementById('subcategory-filters-container');
        const filterContainer = document.getElementById('subcategory-filters');
        
        if (!currentCategory) {
            container.style.display = 'none';
            return;
        }
        
        fetch(`${API_BASE}/categories/${currentCategory}`)
            .then(res => res.json())
            .then(data => {
                const subcats = data.sub_categories || [];
                if (subcats.length === 0) {
                    container.style.display = 'none';
                    return;
                }
                
                let html = `
                    <label class="flex items-center">
                        <input type="radio" name="subcategory" value="" ${currentSubCategory == '' ? 'checked' : ''} class="mr-2 subcat-radio">
                        <span class="font-medium">All Sub-Categories</span>
                    </label>
                `;
                
                html += subcats.map(subcat => `
                    <label class="flex items-center">
                        <input type="radio" name="subcategory" value="${subcat.id}" ${currentSubCategory == subcat.id ? 'checked' : ''} class="mr-2 subcat-radio">
                        <span>${subcat.name}</span>
                    </label>
                `).join('');
                
                filterContainer.innerHTML = html;
                container.style.display = 'block';
                
                document.querySelectorAll('.subcat-radio').forEach(radio => {
                    radio.addEventListener('change', function() {
                        currentSubCategory = this.value;
                        currentPage = 1;
                        loadProducts();
                    });
                });
            });
    }
    
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
        updateCartCount();
        // redirect to cart page after adding
        if (window.showModal) showModal('Added to Cart', 'Product added to cart', 'success');
        window.location.href = '/cart';
    };
    
    function updateCartCount() {
        if (window.updateCartCount) return window.updateCartCount();
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
    }
    
    document.getElementById('sort-select').addEventListener('change', function() {
        currentSort = this.value;
        loadProducts();
    });
    
    loadCategories();
    loadSubcategories();
    loadProducts();
    updateCartCount();
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/suveeindiaweb/htdocs/suveeindia.com/suvee/resources/views/frontend/products/index.blade.php ENDPATH**/ ?>