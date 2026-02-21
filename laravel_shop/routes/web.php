<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart (simplified)
Route::post('/cart/add/{id}', [OrderController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [OrderController::class, 'viewCart'])->name('cart.index');
Route::post('/cart/checkout', [OrderController::class, 'checkout'])->name('cart.checkout');

// Auctions
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auctions/{id}', [AuctionController::class, 'show'])->name('auctions.show');
Route::post('/auctions/{id}/bid', [AuctionController::class, 'placeBid'])->name('auctions.bid');

// Auth routes (default Laravel Auth)
require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');