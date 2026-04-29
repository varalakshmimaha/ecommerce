<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductVariationController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\CompareController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\QueryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Public routes (no auth)
|--------------------------------------------------------------------------
*/
Route::get('/home', [HomeController::class, 'index']);
Route::get('/settings/public', [SettingController::class, 'public']);
Route::get('/theme', [SettingController::class, 'theme']);
Route::get('/settings/features', function () {
    return response()->json([
        'success' => true,
        'data'    => [
            'enable_favourites' => \App\Models\Setting::get('enable_favourites', 'false') === 'true',
            'enable_compare'    => \App\Models\Setting::get('enable_compare', 'false') === 'true',
        ],
    ]);
});

// Pages
Route::get('/pages', [PageController::class, 'index']);
Route::get('/pages/{slug}', [PageController::class, 'show']);

// Products & catalog
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/filters', [ProductController::class, 'filters']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);
Route::get('/categories/{category}', [ProductController::class, 'showCategory']);
Route::get('/brands', [ProductController::class, 'brands']);
Route::get('/banners', [ProductController::class, 'banners']);

// Product variations
Route::get('/products/{product}/variations', [ProductVariationController::class, 'index']);
Route::get('/products/{product}/variations/find', [ProductVariationController::class, 'findByAttributes']);
Route::get('/products/{product}/variations/available', [ProductVariationController::class, 'getAvailableCombinations']);
Route::get('/products/{product}/variations/attributes', [ProductVariationController::class, 'getAttributes']);
Route::get('/products/{product}/variations/{variation}/stock', [ProductVariationController::class, 'checkStock']);
Route::get('/products/{product}/variations/price-range', [ProductVariationController::class, 'getPriceRange']);

// Payment methods
Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Checkout (works for both authenticated and guest users)
Route::post('/checkout/calculate', [CheckoutController::class, 'calculateTotal']);
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);
Route::post('/checkout/create-razorpay-order', [CheckoutController::class, 'createRazorpayOrder']);
Route::post('/checkout/verify-razorpay-payment', [CheckoutController::class, 'verifyRazorpayPayment']);
Route::post('/checkout/verify-wallet-order', [CheckoutController::class, 'verifyWalletOrder']);

// Public order tracking
Route::get('/orders/track/{orderNumber}', [OrderController::class, 'track']);

/*
|--------------------------------------------------------------------------
| Authenticated routes (sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'changePassword']);

    // Address book
    Route::get('/user/addresses',                 [AddressController::class, 'index']);
    Route::post('/user/addresses',                [AddressController::class, 'store']);
    Route::get('/user/addresses/{id}',            [AddressController::class, 'show']);
    Route::put('/user/addresses/{id}',            [AddressController::class, 'update']);
    Route::delete('/user/addresses/{id}',         [AddressController::class, 'destroy']);
    Route::post('/user/addresses/{id}/default',   [AddressController::class, 'setDefault']);

    // Orders
    Route::get('/user/orders', [UserController::class, 'orders']);
    Route::get('/user/orders/{orderNumber}', [UserController::class, 'orderDetails']);
    Route::get('/user/orders/{orderNumber}/invoice', [UserController::class, 'downloadInvoice']);
    Route::post('/user/orders/{orderNumber}/cancel', [UserController::class, 'cancelOrder']);

    // Queries
    Route::get('/user/queries', [QueryController::class, 'index']);
    Route::post('/user/queries', [QueryController::class, 'store']);

    // Favourites
    Route::get('/favourites', [FavouriteController::class, 'index']);
    Route::post('/favourites', [FavouriteController::class, 'store']);
    Route::post('/favourites/toggle', [FavouriteController::class, 'toggle']);
    Route::delete('/favourites/{productId}', [FavouriteController::class, 'destroy']);
    Route::get('/favourites/check/{productId}', [FavouriteController::class, 'check']);

    // Compare
    Route::get('/compare', [CompareController::class, 'index']);
    Route::post('/compare', [CompareController::class, 'store']);
    Route::post('/compare/toggle', [CompareController::class, 'toggle']);
    Route::delete('/compare/{productId}', [CompareController::class, 'destroy']);
    Route::get('/compare/check/{productId}', [CompareController::class, 'check']);
    Route::delete('/compare', [CompareController::class, 'clear']);

    // Wallet
    Route::get('/wallet/balance',      [WalletController::class, 'balance']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

    // Withdrawals
    Route::get('/withdrawals',         [WithdrawalController::class, 'index']);
    Route::post('/withdrawals',        [WithdrawalController::class, 'store']);
    Route::get('/withdrawals/{id}',    [WithdrawalController::class, 'show']);
    Route::delete('/withdrawals/{id}', [WithdrawalController::class, 'destroy']);

    // Commissions
    Route::get('/commissions',         [CommissionController::class, 'index']);
    Route::get('/commissions/summary', [CommissionController::class, 'summary']);

    // Affiliate
    Route::get('/affiliate/status',     [AffiliateController::class, 'status']);
    Route::get('/affiliate/dashboard',  [AffiliateController::class, 'dashboard']);
    Route::post('/affiliate/apply-kyc', [AffiliateController::class, 'applyKyc']);

    // Referrals & team
    Route::get('/referrals',       [ReferralController::class, 'index']);
    Route::get('/referrals/{id}',  [ReferralController::class, 'show']);
    Route::get('/team',            [ReferralController::class, 'team']);
});
