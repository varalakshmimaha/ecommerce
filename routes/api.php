<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductVariationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);
Route::get('/categories/{category}', [ProductController::class, 'showCategory']);
Route::get('/brands', [ProductController::class, 'brands']);
Route::get('/banners', [ProductController::class, 'banners']);

// Product Variation Routes
Route::get('/products/{product}/variations', [ProductVariationController::class, 'index']);
Route::get('/products/{product}/variations/find', [ProductVariationController::class, 'findByAttributes']);
Route::get('/products/{product}/variations/available', [ProductVariationController::class, 'getAvailableCombinations']);
Route::get('/products/{product}/variations/attributes', [ProductVariationController::class, 'getAttributes']);
Route::get('/products/{product}/variations/{variation}/stock', [ProductVariationController::class, 'checkStock']);
Route::get('/products/{product}/variations/price-range', [ProductVariationController::class, 'getPriceRange']);

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

// Checkout Routes (works for both authenticated and guest users)
Route::post('/checkout/calculate', [CheckoutController::class, 'calculateTotal']);
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);
Route::post('/checkout/create-razorpay-order', [CheckoutController::class, 'createRazorpayOrder']);
Route::post('/checkout/verify-razorpay-payment', [CheckoutController::class, 'verifyRazorpayPayment']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Management
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'changePassword']);

    // Orders
    Route::get('/user/orders', [UserController::class, 'orders']);
    Route::get('/user/orders/{orderNumber}', [UserController::class, 'orderDetails']);
    Route::get('/user/orders/{orderNumber}/invoice', [UserController::class, 'downloadInvoice']);

    // Queries
    Route::get('/user/queries', [\App\Http\Controllers\Api\QueryController::class, 'index']);
    Route::post('/user/queries', [\App\Http\Controllers\Api\QueryController::class, 'store']);
});

// Public order tracking
Route::get('/orders/track/{orderNumber}', [OrderController::class, 'track']);

