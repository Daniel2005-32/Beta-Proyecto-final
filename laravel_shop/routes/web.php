<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// ============================================
// RUTAS DE PRODUCTOS
// ============================================
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/category/{categorySlug}', [ProductController::class, 'byCategory'])->name('category');
    Route::get('/exclusivos', [ProductController::class, 'exclusivos'])->name('exclusivos');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// ============================================
// RUTAS DE ADMINISTRACIÓN
// ============================================

// Admin routes - Productos
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
});

// Admin routes - Usuarios
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-admin', [App\Http\Controllers\Admin\UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
});

// Ruta para limpiar mensajes manualmente (solo admins)
Route::get('/admin/clean-messages', function() {
    if (!auth()->check() || !auth()->user()->is_admin) {
        abort(403, 'Solo administradores');
    }
    
    $deleted = \Illuminate\Support\Facades\Artisan::call('messages:delete-old --hours=1');
    
    return redirect()->back()->with('success', "Mensajes antiguos eliminados correctamente.");
})->name('admin.clean.messages');

// ============================================
// RUTAS DE SUBASTAS (COMPLETAS)
// ============================================
Route::prefix('auctions')->name('auctions.')->group(function () {
    // Rutas públicas
    Route::get('/', [AuctionController::class, 'index'])->name('index');
    Route::get('/{id}', [AuctionController::class, 'show'])->name('show');
    
    // Ruta para confirmar subasta (desde producto exclusivo)
    Route::get('/confirm/{id}', [AuctionController::class, 'confirm'])->name('confirm');
    
    // Rutas para usuarios autenticados (pujar)
    Route::post('/{id}/bid', [AuctionController::class, 'bid'])->name('bid')->middleware('auth');
    
    // Rutas para iniciar/cancelar subastas (usuario que compró el exclusivo)
    Route::post('/start/{id}', [AuctionController::class, 'start'])->name('start')->middleware('auth');
    Route::post('/cancel/{id}', [AuctionController::class, 'cancel'])->name('cancel')->middleware('auth');
    
    // Ruta para reclamar premio (ganador de subasta)
    Route::post('/claim/{id}', [AuctionController::class, 'claimPrize'])->name('claim')->middleware('auth');
});

// ============================================
// RUTAS DE ADMINISTRACIÓN DE SUBASTAS (SOLO ADMINS)
// ============================================
Route::prefix('auctions')->name('auctions.')->middleware('auth')->group(function () {
    // Extender tiempo de subasta
    Route::post('/{id}/extend', [AuctionController::class, 'extendAuction'])->name('extend');
    
    // Reducir tiempo de subasta
    Route::post('/{id}/reduce', [AuctionController::class, 'reduceAuction'])->name('reduce');
    
    // Reiniciar a 24 horas
    Route::post('/{id}/reset', [AuctionController::class, 'resetAuctionTime'])->name('reset');
    
    // Finalizar subasta forzosamente
    Route::post('/{id}/force-end', [AuctionController::class, 'forceEndAuction'])->name('force-end');
});

// ============================================
// RUTAS DE DIRECCIONES
// ============================================
Route::middleware(['auth'])->prefix('addresses')->name('addresses.')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('index');
    Route::get('/create', [AddressController::class, 'create'])->name('create');
    Route::post('/', [AddressController::class, 'store'])->name('store');
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
    Route::put('/{address}', [AddressController::class, 'update'])->name('update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
    Route::get('/{address}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
});

// ============================================
// RUTAS DE CHAT
// ============================================
Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/refresh', [ChatController::class, 'refresh'])->name('refresh');
    Route::post('/', [ChatController::class, 'store'])->name('store')->middleware('auth');
});

// ============================================
// RUTAS DE PERFIL
// ============================================
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
    Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('avatar');
});

// ============================================
// RUTAS DE OFERTAS Y CONTACTO
// ============================================
Route::get('/ofertas', function () {
    $offers = App\Models\Product::where('original_price', '>', 0)
        ->whereColumn('price', '<', 'original_price')
        ->take(8)
        ->get();
    
    return view('offers', compact('offers'));
})->name('offers');

Route::get('/contacto', function () {
    return view('contact');
})->name('contact');

// ============================================
// RUTAS DE CARRITO Y PEDIDOS
// ============================================
Route::post('/cart/add/{id}', [OrderController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [OrderController::class, 'viewCart'])->name('cart.index');
Route::post('/cart/update/{id}', [OrderController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/clear', [OrderController::class, 'clearCart'])->name('cart.clear');
Route::get('/checkout', [OrderController::class, 'checkoutForm'])->name('cart.checkout.form')->middleware('auth');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('cart.checkout')->middleware('auth');

// ============================================
// RUTAS DE PEDIDOS
// ============================================
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('auth');

// ============================================
// RUTAS DE AUTENTICACIÓN (Laravel Breeze/Jetstream)
// ============================================
require __DIR__.'/auth.php';
