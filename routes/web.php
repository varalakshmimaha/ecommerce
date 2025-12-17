<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Frontend\AddressController;
use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController as FrontendAuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\UserOrderController;
// Frontend Routes
Route::get('/', function () {
    return view('frontend.home');
})->name('home');


use App\Http\Controllers\Frontend\Auth\ForgotPasswordController;
use App\Http\Controllers\Frontend\Auth\NewPasswordController;

Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('guest')->name('password.update');

// Frontend auth and dashboard
Route::get('/user/register', [RegisterController::class, 'create'])->name('user.register');
Route::post('/user/register', [RegisterController::class, 'store']);

Route::get('/user/login', [FrontendAuthenticatedSessionController::class, 'create'])->name('user.login');
Route::post('/user/login', [FrontendAuthenticatedSessionController::class, 'store']);
Route::post('/user/logout', [FrontendAuthenticatedSessionController::class, 'destroy'])->name('user.logout');

use App\Http\Controllers\Frontend\DashboardController;

Route::middleware(['auth'])->group(function () {
    // Address management
    Route::get('/dashboard/addresses', [AddressController::class, 'index'])->name('user.dashboard.addresses');
    Route::post('/dashboard/addresses', [AddressController::class, 'store'])->name('user.dashboard.addresses.store');
    Route::put('/dashboard/addresses/{address}', [AddressController::class, 'update'])->name('user.dashboard.addresses.update');
    Route::post('/dashboard/addresses/{address}', [AddressController::class, 'update']); // For AJAX edit with _method=PUT
    Route::delete('/dashboard/addresses/{address}', [AddressController::class, 'destroy'])->name('user.dashboard.addresses.destroy');
    Route::post('/dashboard/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('user.dashboard.addresses.default');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('user.dashboard.profile');
    Route::post('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('user.dashboard.profile.update');
    Route::post('/dashboard/password', [DashboardController::class, 'updatePassword'])->name('user.dashboard.password.update');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('user.dashboard.orders');
    Route::get('/dashboard/queries', [DashboardController::class, 'queries'])->name('user.dashboard.queries');
    Route::post('/dashboard/queries', [DashboardController::class, 'createQuery'])->name('user.dashboard.queries.create');
    Route::get('/dashboard/orders/{orderNumber}/invoice', [DashboardController::class, 'downloadInvoice'])->name('user.dashboard.invoice');

    // User Orders (My Orders, Show, Cancel)
    Route::get('/user/orders', [UserOrderController::class, 'index'])->name('user.orders.index');
    Route::get('/user/orders/{id}', [UserOrderController::class, 'show'])->name('user.orders.show');
    Route::post('/user/orders/{id}/cancel', [UserOrderController::class, 'cancel'])->name('user.orders.cancel');
});

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
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    
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

    // Queries
    Route::get('/queries', [\App\Http\Controllers\Admin\QueryController::class, 'index'])->name('queries.index');
    Route::post('/queries/{query}/respond', [\App\Http\Controllers\Admin\QueryController::class, 'respond'])->name('queries.respond');
    Route::post('/queries/{query}/status', [\App\Http\Controllers\Admin\QueryController::class, 'updateStatus'])->name('queries.update-status');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Public dynamic pages
Route::get('/page/{page:slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');

require __DIR__.'/auth.php';

