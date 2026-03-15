<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;

Route::get('/home', [HomeController::class, 'index']);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\RaffleController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Checkout & Orders
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    
    // Reviews modification
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ProductReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ProductReviewController::class, 'destroy']);

    // Profile & Addresses
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);

    Route::apiResource('addresses', AddressController::class)->except(['show']);
    Route::patch('/addresses/{address}/set-default', [AddressController::class, 'setDefault']);
    
    // Protected Auction Routes
    Route::post('/auctions/{id}/bid', [AuctionController::class, 'bid']);
    Route::post('/auctions/{id}/start', [AuctionController::class, 'start']);
    Route::post('/auctions/{id}/cancel', [AuctionController::class, 'cancel']);
    Route::post('/auctions/{id}/claim', [AuctionController::class, 'claimPrize']);
    
    // Admin Auction Routes (auth implied by sanctum, need to check admin privileges in controller logic)
    Route::post('/auctions/{id}/extend', [AuctionController::class, 'extendAuction']);
    Route::post('/auctions/{id}/reduce', [AuctionController::class, 'reduceAuction']);
    Route::post('/auctions/{id}/reset', [AuctionController::class, 'resetAuctionTime']);
    Route::post('/auctions/{id}/force-end', [AuctionController::class, 'forceEndAuction']);
    
    // Admin Product Routes
    Route::get('/admin/products', [ProductController::class, 'adminIndex']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']); // Admin update with PUT method support
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/category/{categorySlug}', [ProductController::class, 'byCategory']);
    Route::get('/exclusivos', [ProductController::class, 'exclusivos']);
    Route::get('/offers', [ProductController::class, 'offers']);
    Route::get('/{slug}', [ProductController::class, 'show']);
});

// Public Auction & Raffle Routes
Route::get('/auctions', [AuctionController::class, 'index']);
Route::get('/auctions/{id}', [AuctionController::class, 'show']);
Route::get('/raffles', [RaffleController::class, 'index']);
Route::get('/raffles/{id}', [RaffleController::class, 'show']);
