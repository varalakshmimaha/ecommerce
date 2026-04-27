<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ThemeColorController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Webhook\RazorpayWebhookController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Frontend\AddressController;
use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController as FrontendAuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Admin login at /admin/login
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');
});

// Frontend Routes
Route::get('/', function () {
    return view('frontend.home');
})->name('home');

use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\CompareController;

use App\Http\Controllers\Frontend\Auth\ForgotPasswordController;
use App\Http\Controllers\Frontend\Auth\NewPasswordController;

Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('guest')->name('password.update');

// Frontend auth and dashboard
Route::get('/user/register', [RegisterController::class, 'create'])->name('user.register');
Route::post('/user/register', [RegisterController::class, 'store']);

Route::get('/become-affiliate', function () {
    return view('frontend.become-affiliate');
})->name('become.affiliate');

Route::middleware('auth')->group(function () {
    Route::get('/become-affiliate/apply', [\App\Http\Controllers\Frontend\Affiliate\ApplicationController::class, 'create'])->name('become.affiliate.apply.create');
    Route::post('/become-affiliate/apply', [\App\Http\Controllers\Frontend\Affiliate\ApplicationController::class, 'store'])->name('become.affiliate.apply.store');
});

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
    Route::get('/dashboard/referrals/{referral}', [DashboardController::class, 'referralShow'])->name('user.dashboard.referral.show');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('user.dashboard.profile');
    Route::post('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('user.dashboard.profile.update');
    Route::post('/dashboard/password', [DashboardController::class, 'updatePassword'])->name('user.dashboard.password.update');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('user.dashboard.orders');
    Route::get('/dashboard/queries', [DashboardController::class, 'queries'])->name('user.dashboard.queries');
    Route::post('/dashboard/queries', [DashboardController::class, 'createQuery'])->name('user.dashboard.queries.create');
    Route::get('/dashboard/orders/{orderNumber}/invoice', [DashboardController::class, 'downloadInvoice'])->name('user.dashboard.invoice');

    // Favourites & Compare pages
    Route::get('/favourites', function() { return view('frontend.favourites'); })->name('favourites');
    Route::get('/compare', function() { return view('frontend.compare'); })->name('compare');

    // User Orders (My Orders, Show, Cancel)
    Route::get('/user/orders', [UserOrderController::class, 'index'])->name('user.orders.index');
    Route::get('/user/orders/{id}', [UserOrderController::class, 'show'])->name('user.orders.show');
    Route::post('/user/orders/{id}/cancel', [UserOrderController::class, 'cancel'])->name('user.orders.cancel');

    // Favourites API (session auth) - /user-api prefix to avoid conflict with /api routes
    Route::get('/user-api/favourites', [FavouriteController::class, 'index']);
    Route::post('/user-api/favourites/toggle', [FavouriteController::class, 'toggle']);
    Route::get('/user-api/favourites/check/{productId}', [FavouriteController::class, 'check']);
    Route::delete('/user-api/favourites/{productId}', [FavouriteController::class, 'destroy']);

    // Compare API (session auth)
    Route::get('/user-api/compare', [CompareController::class, 'index']);
    Route::post('/user-api/compare/toggle', [CompareController::class, 'toggle']);
    Route::get('/user-api/compare/check/{productId}', [CompareController::class, 'check']);
    Route::delete('/user-api/compare/{productId}', [CompareController::class, 'destroy']);
    Route::delete('/user-api/compare', [CompareController::class, 'clear']);
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

// API Routes
Route::get('/api/payment-methods', [PaymentMethodController::class, 'index']);
Route::post('/api/checkout/create-razorpay-order', [\App\Http\Controllers\Api\CheckoutController::class, 'createRazorpayOrder']);
Route::post('/api/checkout/verify-razorpay-payment', [\App\Http\Controllers\Api\CheckoutController::class, 'verifyRazorpayPayment']);

// Webhook Routes
Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle']);

Route::get('admin/products/search', [ProductController::class, 'search'])->name('admin.products.search');

// Admin Routes
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Products
    Route::resource('products', ProductController::class);
    Route::resource('products.variations', ProductVariationController::class);
    Route::post('products/{product}/variations/bulk-update', [ProductVariationController::class, 'bulkUpdate'])->name('products.variations.bulk-update');
    Route::post('products/{product}/variations/{variation}/toggle-status', [ProductVariationController::class, 'toggleStatus'])->name('products.variations.toggle-status');
    Route::post('products/{product}/variations/{variation}/set-default', [ProductVariationController::class, 'setDefault'])->name('products.variations.set-default');
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/payment', [OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/backfill-commissions', [OrderController::class, 'backfillCommissions'])->name('orders.backfill-commissions');
    
    // Customers (non-admin users)
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    
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
    Route::get('/product-attributes/{productAttribute}', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'show'])->name('product-attributes.show');
    Route::get('/product-attributes/{productAttribute}/edit', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'edit'])->name('product-attributes.edit');
    Route::put('/product-attributes/{productAttribute}', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'update'])->name('product-attributes.update');
    Route::delete('/product-attributes/{productAttribute}', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'destroy'])->name('product-attributes.destroy');
    Route::post('/product-attributes/{productAttribute}/toggle-status', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'toggleStatus'])->name('product-attributes.toggle-status');

    // Product Attribute Values
    Route::get('/product-attribute-values', [\App\Http\Controllers\Admin\ProductAttributeValueController::class, 'index'])->name('product-attribute-values.index');
    Route::get('/product-attribute-values/create', [\App\Http\Controllers\Admin\ProductAttributeValueController::class, 'create'])->name('product-attribute-values.create');
    Route::post('/product-attribute-values', [\App\Http\Controllers\Admin\ProductAttributeValueController::class, 'store'])->name('product-attribute-values.store');
    Route::get('/product-attribute-values/{productAttributeValue}/edit', [\App\Http\Controllers\Admin\ProductAttributeValueController::class, 'edit'])->name('product-attribute-values.edit');
    Route::put('/product-attribute-values/{productAttributeValue}', [\App\Http\Controllers\Admin\ProductAttributeValueController::class, 'update'])->name('product-attribute-values.update');
    Route::delete('/product-attribute-values/{productAttributeValue}', [\App\Http\Controllers\Admin\ProductAttributeValueController::class, 'destroy'])->name('product-attribute-values.destroy');
    Route::post('/product-attribute-values/{productAttributeValue}/toggle-status', [\App\Http\Controllers\Admin\ProductAttributeValueController::class, 'toggleStatus'])->name('product-attribute-values.toggle-status');

    // Pages (dynamic pages managed by admin)
    Route::get('/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy');

    // Theme Colors
    Route::get('/theme-colors', [\App\Http\Controllers\Admin\ThemeColorController::class, 'index'])->name('theme-colors.index');
    Route::get('/theme-colors/create', [\App\Http\Controllers\Admin\ThemeColorController::class, 'create'])->name('theme-colors.create');
    Route::post('/theme-colors', [\App\Http\Controllers\Admin\ThemeColorController::class, 'store'])->name('theme-colors.store');
    Route::get('/theme-colors/{themeColor}/edit', [\App\Http\Controllers\Admin\ThemeColorController::class, 'edit'])->name('theme-colors.edit');
    Route::put('/theme-colors/{themeColor}', [\App\Http\Controllers\Admin\ThemeColorController::class, 'update'])->name('theme-colors.update');
    Route::delete('/theme-colors/{themeColor}', [\App\Http\Controllers\Admin\ThemeColorController::class, 'destroy'])->name('theme-colors.destroy');
    Route::post('/theme-colors/{themeColor}/activate', [\App\Http\Controllers\Admin\ThemeColorController::class, 'activate'])->name('theme-colors.activate');
    Route::get('/theme-colors/{themeColor}/preview', [\App\Http\Controllers\Admin\ThemeColorController::class, 'preview'])->name('theme-colors.preview');

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
    
    // Payment Settings
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment', [PaymentController::class, 'update'])->name('payment.update');
    Route::post('/payment/test-razorpay', [PaymentController::class, 'testRazorpayConnection'])->name('payment.test-razorpay');
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

    // Managers CRUD
    Route::get('/managers', [\App\Http\Controllers\Admin\ManagerController::class, 'index'])->name('managers.index');
    Route::get('/managers/create', [\App\Http\Controllers\Admin\ManagerController::class, 'create'])->name('managers.create');
    Route::post('/managers', [\App\Http\Controllers\Admin\ManagerController::class, 'store'])->name('managers.store');
    Route::get('/managers/{manager}', [\App\Http\Controllers\Admin\ManagerController::class, 'show'])->name('managers.show');
    Route::get('/managers/{manager}/edit', [\App\Http\Controllers\Admin\ManagerController::class, 'edit'])->name('managers.edit');
    Route::put('/managers/{manager}', [\App\Http\Controllers\Admin\ManagerController::class, 'update'])->name('managers.update');
    Route::delete('/managers/{manager}', [\App\Http\Controllers\Admin\ManagerController::class, 'destroy'])->name('managers.destroy');
    Route::post('/managers/{manager}/toggle-section', [\App\Http\Controllers\Admin\ManagerController::class, 'toggleSection'])->name('managers.toggle-section');
    Route::post('/managers/{manager}/wallet', [\App\Http\Controllers\Admin\ManagerController::class, 'walletTransaction'])->name('managers.wallet');
    Route::post('/managers/wallet/{transaction}/approve', [\App\Http\Controllers\Admin\ManagerController::class, 'approveWallet'])->name('managers.wallet.approve');
    Route::post('/managers/wallet/{transaction}/reject', [\App\Http\Controllers\Admin\ManagerController::class, 'rejectWallet'])->name('managers.wallet.reject');

    // RMs CRUD
    Route::get('/rms', [\App\Http\Controllers\Admin\RmController::class, 'index'])->name('rms.index');
    Route::get('/rms/create', [\App\Http\Controllers\Admin\RmController::class, 'create'])->name('rms.create');
    Route::post('/rms', [\App\Http\Controllers\Admin\RmController::class, 'store'])->name('rms.store');
    Route::get('/rms/{rm}', [\App\Http\Controllers\Admin\RmController::class, 'show'])->name('rms.show');
    Route::get('/rms/{rm}/edit', [\App\Http\Controllers\Admin\RmController::class, 'edit'])->name('rms.edit');
    Route::put('/rms/{rm}', [\App\Http\Controllers\Admin\RmController::class, 'update'])->name('rms.update');
    Route::delete('/rms/{rm}', [\App\Http\Controllers\Admin\RmController::class, 'destroy'])->name('rms.destroy');
    Route::post('/rms/{rm}/wallet', [\App\Http\Controllers\Admin\RmController::class, 'walletTransaction'])->name('rms.wallet');
    Route::post('/rms/wallet/{transaction}/approve', [\App\Http\Controllers\Admin\RmController::class, 'approveWallet'])->name('rms.wallet.approve');
    Route::post('/rms/wallet/{transaction}/reject', [\App\Http\Controllers\Admin\RmController::class, 'rejectWallet'])->name('rms.wallet.reject');
    Route::post('/rms/{rm}/toggle-section', [\App\Http\Controllers\Admin\RmController::class, 'toggleSection'])->name('rms.toggle-section');

    // Commission Settings
    Route::get('/commission-settings', [\App\Http\Controllers\Admin\CommissionSettingController::class, 'index'])->name('commission-settings.index');
    Route::put('/commission-settings', [\App\Http\Controllers\Admin\CommissionSettingController::class, 'update'])->name('commission-settings.update');

    // Commissions ledger
    Route::get('/commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/export', [\App\Http\Controllers\Admin\CommissionController::class, 'export'])->name('commissions.export');
    Route::post('/commissions/{commission}/approve', [\App\Http\Controllers\Admin\CommissionController::class, 'approve'])->name('commissions.approve');
    Route::post('/commissions/{commission}/reverse', [\App\Http\Controllers\Admin\CommissionController::class, 'reverse'])->name('commissions.reverse');
    Route::post('/commissions/{commission}/pay', [\App\Http\Controllers\Admin\CommissionController::class, 'markPaid'])->name('commissions.pay');

    // Withdrawals (payouts)
    Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/create', [\App\Http\Controllers\Admin\WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::get('/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::delete('/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'destroy'])->name('withdrawals.destroy');

    // Withdrawal Requests (user-initiated)
    Route::get('/withdrawal-requests', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'index'])->name('withdrawal-requests.index');
    Route::get('/withdrawal-requests/{withdrawalRequest}', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'show'])->name('withdrawal-requests.show');
    Route::get('/withdrawal-requests/{withdrawalRequest}/edit', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'edit'])->name('withdrawal-requests.edit');
    Route::post('/withdrawal-requests/{withdrawalRequest}/approve', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'approve'])->name('withdrawal-requests.approve');
    Route::post('/withdrawal-requests/{withdrawalRequest}/reject', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'reject'])->name('withdrawal-requests.reject');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Affiliates
    Route::get('/affiliates', [\App\Http\Controllers\Admin\AffiliateController::class, 'index'])->name('affiliates.index');
    Route::get('/affiliates/create', [\App\Http\Controllers\Admin\AffiliateController::class, 'create'])->name('affiliates.create');
    Route::post('/affiliates', [\App\Http\Controllers\Admin\AffiliateController::class, 'store'])->name('affiliates.store');
    Route::get('/affiliates/{user}', [\App\Http\Controllers\Admin\AffiliateController::class, 'show'])->name('affiliates.show');
    Route::get('/affiliates/{user}/edit', [\App\Http\Controllers\Admin\AffiliateController::class, 'edit'])->name('affiliates.edit');
    Route::put('/affiliates/{user}', [\App\Http\Controllers\Admin\AffiliateController::class, 'update'])->name('affiliates.update');
    Route::delete('/affiliates/{user}', [\App\Http\Controllers\Admin\AffiliateController::class, 'destroy'])->name('affiliates.destroy');
    Route::post('/affiliates/{user}/wallet', [\App\Http\Controllers\Admin\AffiliateController::class, 'walletTransaction'])->name('affiliates.wallet');
    Route::post('/affiliates/wallet/{transaction}/approve', [\App\Http\Controllers\Admin\AffiliateController::class, 'approveWallet'])->name('affiliates.wallet.approve');
    Route::post('/affiliates/wallet/{transaction}/reject', [\App\Http\Controllers\Admin\AffiliateController::class, 'rejectWallet'])->name('affiliates.wallet.reject');
    Route::post('/affiliates/{user}/approve', [\App\Http\Controllers\Admin\AffiliateController::class, 'approve'])->name('affiliates.approve');
    Route::post('/affiliates/{user}/reject', [\App\Http\Controllers\Admin\AffiliateController::class, 'reject'])->name('affiliates.reject');
    Route::post('/affiliates/{user}/verify-kyc', [\App\Http\Controllers\Admin\AffiliateController::class, 'verifyKyc'])->name('affiliates.verify-kyc');
    Route::put('/affiliates/{user}/parent', [\App\Http\Controllers\Admin\AffiliateController::class, 'assignParent'])->name('affiliates.assign-parent');
    Route::post('/affiliates/{user}/toggle-section', [\App\Http\Controllers\Admin\AffiliateController::class, 'toggleSection'])->name('affiliates.toggle-section');
});

// Affiliate / RM / Manager dashboard + team management
Route::middleware('auth')->group(function () {
    Route::get('/affiliate/dashboard', [\App\Http\Controllers\Frontend\Affiliate\DashboardController::class, 'index'])->name('affiliate.dashboard');
    Route::post('/affiliate/withdrawal-request', [\App\Http\Controllers\Frontend\WithdrawalRequestController::class, 'store'])->name('affiliate.withdrawal-request.store');
    Route::post('/affiliate/wallet-request', [\App\Http\Controllers\Frontend\Affiliate\DashboardController::class, 'walletRequest'])->name('affiliate.wallet-request.store');
    Route::get('/my-team', [\App\Http\Controllers\Frontend\Affiliate\TeamController::class, 'index'])->name('team.index');
    Route::post('/my-team', [\App\Http\Controllers\Frontend\Affiliate\TeamController::class, 'store'])->name('team.store');
    Route::delete('/my-team/{member}', [\App\Http\Controllers\Frontend\Affiliate\TeamController::class, 'destroy'])->name('team.destroy');
});

// Public dynamic pages
Route::get('/page/{page:slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');

require __DIR__.'/auth.php';

