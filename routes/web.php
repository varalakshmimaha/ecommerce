<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;

// Frontend Routes
Route::get('/', function () {
    return view('frontend.home');
})->name('home');

// Frontend auth and dashboard (client-driven auth via API tokens)
Route::get('/user/register', function () {
    return view('frontend.auth.register');
})->name('user.register');

Route::get('/user/login', function () {
    return view('frontend.auth.login');
})->name('user.login');

Route::get('/dashboard', function () {
    return view('frontend.dashboard');
})->name('user.dashboard');

Route::get('/products', function () {
    return view('frontend.products.index');
})->name('products.index');

Route::get('/category/{slug}', [\App\Http\Controllers\Frontend\CategoryController::class, 'show'])->name('category.show');

Route::get('/products/{slug}', function ($slug) {
    return view('frontend.products.show', compact('slug'));
})->name('products.show');

Route::get('/checkout', function () {
    return view('frontend.checkout');
})->name('checkout');

// Cart page for guests and authenticated users
Route::get('/cart', function () {
    return view('frontend.cart');
})->name('cart');

Route::get('/order/success/{orderNumber}', function($orderNumber){
    return view('frontend.order-success', compact('orderNumber'));
})->name('order.success');

Route::get('/track-order', function(){
    return view('frontend.track-order');
})->name('track.order');

// Admin Routes
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/payment', [OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/product-sales', [ReportController::class, 'productSales'])->name('reports.product-sales');
    Route::get('/reports/order-sales', [ReportController::class, 'orderSales'])->name('reports.order-sales');
    Route::get('/reports/overall-sales', [ReportController::class, 'overallSales'])->name('reports.overall-sales');
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update.post');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/sub-categories', [CategoryController::class, 'storeSubCategory'])->name('sub-categories.store');
    Route::put('/sub-categories/{subCategory}', [CategoryController::class, 'updateSubCategory'])->name('sub-categories.update');
    Route::post('/sub-categories/{subCategory}', [CategoryController::class, 'updateSubCategory'])->name('sub-categories.update.post');
    Route::delete('/sub-categories/{subCategory}', [CategoryController::class, 'destroySubCategory'])->name('sub-categories.destroy');

    // Brands
    Route::get('/brands', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('brands.store');
    Route::put('/brands/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brands.update');
    Route::post('/brands/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brands.update.post');
    Route::delete('/brands/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('brands.destroy');

    // Product Attributes
    Route::get('/product-attributes', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'index'])->name('product-attributes.index');
    Route::get('/product-attributes/create', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'create'])->name('product-attributes.create');
    Route::post('/product-attributes', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'store'])->name('product-attributes.store');
    Route::get('/product-attributes/{productAttribute}/edit', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'edit'])->name('product-attributes.edit');
    Route::put('/product-attributes/{productAttribute}', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'update'])->name('product-attributes.update');
    Route::delete('/product-attributes/{productAttribute}', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'destroy'])->name('product-attributes.destroy');

    // Pages (dynamic pages managed by admin)
    Route::get('/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy');

    // Banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::post('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/footer-sections', [SettingController::class, 'storeFooterSection'])->name('settings.footer-sections.store');
    Route::post('/settings/footer-sections/{footerSection}', [SettingController::class, 'updateFooterSection'])->name('settings.footer-sections.update');
    Route::delete('/settings/footer-sections/{footerSection}', [SettingController::class, 'destroyFooterSection'])->name('settings.footer-sections.destroy');
    Route::post('/settings/footer-links', [SettingController::class, 'storeFooterLink'])->name('settings.footer-links.store');
    Route::post('/settings/footer-links/{footerLink}', [SettingController::class, 'updateFooterLink'])->name('settings.footer-links.update');
    Route::delete('/settings/footer-links/{footerLink}', [SettingController::class, 'destroyFooterLink'])->name('settings.footer-links.destroy');
    // Shipping
    Route::get('/shipping', [\App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
    Route::post('/shipping', [\App\Http\Controllers\Admin\ShippingController::class, 'update'])->name('shipping.update');
});

// Public dynamic pages
Route::get('/page/{page:slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');

require __DIR__.'/auth.php';

