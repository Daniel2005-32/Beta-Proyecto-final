<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Mail;


Route::get('/home', [HomeController::class, 'index']);



Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset.api');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckBanned::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Soporte
    Route::get('/support/my-tickets', [SupportController::class, 'userTickets']);
    Route::post('/support', [SupportController::class, 'store']);
    Route::delete('/support/{ticket}', [SupportController::class, 'destroy']);
    // Checkout & Orders
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice']);
    Route::get('/orders/{order}/resend-invoice', [OrderController::class, 'resendInvoice']);
    
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
    Route::post('/user/auctions', [AuctionController::class, 'storeUserAuction']);
    
    // Protected Raffle Routes
    Route::post('/raffles/{id}/enter', [RaffleController::class, 'enter']);
    
    // Admin Auction Routes (auth implied by sanctum, need to check admin privileges in controller logic)
    Route::post('/auctions/{id}/extend', [AuctionController::class, 'extendAuction']);
    Route::post('/auctions/{id}/reduce', [AuctionController::class, 'reduceAuction']);
    Route::post('/auctions/{id}/reset', [AuctionController::class, 'resetAuctionTime']);
    Route::post('/auctions/{id}/force-end', [AuctionController::class, 'forceEndAuction']);
    
    // Chat Routes
    Route::post('/loyalty/claim-coupon', [\App\Http\Controllers\LoyaltyController::class, 'claimRouletteCoupon']);
    Route::post('/loyalty/claim-battle-coupon', [\App\Http\Controllers\LoyaltyController::class, 'claimBattleCoupon']);
    Route::get('/my-coupons', [\App\Http\Controllers\LoyaltyController::class, 'myCoupons']);
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index']);
    Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'store']);
    Route::delete('/chat/clear', [\App\Http\Controllers\ChatController::class, 'clear']);

    // Loyalty & Points
    Route::get('/loyalty/points', [\App\Http\Controllers\LoyaltyController::class, 'getPoints']);
    Route::post('/loyalty/add-points', [\App\Http\Controllers\LoyaltyController::class, 'addPoints']);
    Route::get('/loyalty/ranking', [\App\Http\Controllers\LoyaltyController::class, 'getRanking']);


    // Admin Management Routes (Updated to use Admin\UserController)
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
    Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store']);
    Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update']);

    Route::post('/admin/users/{user}/toggle-admin', [\App\Http\Controllers\Admin\UserController::class, 'toggleAdmin']);
    Route::post('/admin/users/{user}/points', [\App\Http\Controllers\Admin\UserController::class, 'updatePoints']);
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);
    Route::post('/admin/users/{user}/ban', [\App\Http\Controllers\Admin\UserController::class, 'ban']);
    Route::post('/admin/users/{user}/unban', [\App\Http\Controllers\Admin\UserController::class, 'unban']);

    // Admin Chat Ban Management
    Route::get('/admin/bans', [\App\Http\Controllers\AdminController::class, 'listBans']);
    Route::post('/admin/bans/{id}/extend', [\App\Http\Controllers\AdminController::class, 'extendBan']);
    Route::post('/admin/bans/{id}/reduce', [\App\Http\Controllers\AdminController::class, 'reduceBan']);

    Route::post('/admin/raffles', [\App\Http\Controllers\AdminController::class, 'createRaffle']);
    Route::post('/admin/raffles/{id}', [\App\Http\Controllers\AdminController::class, 'updateRaffle']);
    Route::post('/admin/raffles/{id}/draw', [\App\Http\Controllers\AdminController::class, 'drawRaffle']);
    Route::post('/admin/raffles/{id}/cancel', [\App\Http\Controllers\AdminController::class, 'cancelRaffle']);


    // Admin Orders & Reviews Routes
    Route::get('/admin/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index']);
    Route::post('/admin/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
    Route::delete('/admin/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy']);
    Route::get('/admin/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index']);
    Route::post('/admin/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve']);
    Route::delete('/admin/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy']);

    // Admin Product Routes
    Route::get('/admin/products', [ProductController::class, 'adminIndex']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']); // Admin update with PUT method support
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Wishlist Routes
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index']);
    Route::post('/wishlist', [\App\Http\Controllers\WishlistController::class, 'store']);
    Route::delete('/wishlist/{product_id}', [\App\Http\Controllers\WishlistController::class, 'destroy']);

    // Coupon Validation
    Route::post('/validate-coupon', [OrderController::class, 'validateCoupon']);

    // Admin Analytics & Dashboard
    Route::get('/admin/stats', [\App\Http\Controllers\Admin\StatsController::class, 'index']);

    // Admin Coupon Management
    Route::get('/admin/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'index']);
    Route::post('/admin/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'store']);
    Route::post('/admin/coupons/{coupon}/toggle', [\App\Http\Controllers\Admin\CouponController::class, 'toggle']);
    Route::delete('/admin/coupons/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy']);

    // Soporte Administrativo
    Route::get('/admin/support', [SupportController::class, 'index']);
    Route::put('/admin/support/{ticket}', [SupportController::class, 'update']);
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
