<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/category/{categorySlug}', [ProductController::class, 'byCategory'])->name('category');
    Route::get('/exclusivos', [ProductController::class, 'exclusivos'])->name('exclusivos');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// Admin routes (sin middleware, la verificación está en el controlador)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
});

// Offers
Route::get('/ofertas', function () {
    $offers = App\Models\Product::where('original_price', '>', 0)
        ->whereColumn('price', '<', 'original_price')
        ->take(8)
        ->get();
    
    return view('offers', compact('offers'));
})->name('offers');

// Contact
Route::get('/contacto', function () {
    return view('contact');
})->name('contact');

// Cart
Route::post('/cart/add/{id}', [OrderController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [OrderController::class, 'viewCart'])->name('cart.index');
Route::post('/cart/update/{id}', [OrderController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/clear', [OrderController::class, 'clearCart'])->name('cart.clear');
Route::post('/cart/checkout', [OrderController::class, 'checkout'])->name('cart.checkout');

// Orders
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('auth');

// Auctions
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auctions/{id}', [AuctionController::class, 'show'])->name('auctions.show');
Route::post('/auctions/{id}/bid', [AuctionController::class, 'placeBid'])->name('auctions.bid')->middleware('auth');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', function () { return view('profile.edit'); })->name('edit');
    Route::patch('/', function () { return back()->with('success', 'Perfil actualizado'); })->name('update');
    Route::delete('/', function () { 
        auth()->user()->delete(); 
        return redirect('/'); 
    })->name('destroy');
});

require __DIR__.'/auth.php';
