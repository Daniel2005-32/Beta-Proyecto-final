<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\ContactController;
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
    Route::post('users/{user}/ban', [App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
    Route::post('users/{user}/unban', [App\Http\Controllers\Admin\UserController::class, 'unban'])->name('users.unban');
});

// Admin routes - Sorteos
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('raffles', App\Http\Controllers\Admin\RaffleController::class);
    Route::post('raffles/{raffle}/activate', [App\Http\Controllers\Admin\RaffleController::class, 'activate'])->name('raffles.activate');
    Route::post('raffles/{raffle}/draw', [App\Http\Controllers\Admin\RaffleController::class, 'drawWinner'])->name('raffles.draw');
});

// Admin routes - Pedidos
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->except(['create', 'store', 'edit']);
    Route::post('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
});

// Admin routes - Valoraciones
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('reviews', App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'destroy']);
    Route::post('reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
});

// Ruta para limpiar mensajes - AHORA LIMPIA LA TABLA 'messages'
Route::post('/admin/clean-messages', function() {
    if (!auth()->check() || !auth()->user()->is_admin) {
        return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
    }
    
    try {
        // Limpiar la tabla 'messages' que es donde están los mensajes
        $count = DB::table('messages')->count();
        DB::table('messages')->truncate();
        
        return response()->json([
            'success' => true,
            'deleted' => $count,
            'message' => "✅ $count mensajes eliminados"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'error' => '❌ Error: ' . $e->getMessage()
        ], 500);
    }
})->name('admin.clean-messages')->middleware('auth');
// ============================================
// RUTAS DE SUBASTAS
// ============================================
Route::prefix('auctions')->name('auctions.')->group(function () {
    // Rutas públicas
    Route::get('/', [AuctionController::class, 'index'])->name('index');
    Route::get('/{id}', [AuctionController::class, 'show'])->name('show');
    
    // Ruta para confirmar subasta
    Route::get('/confirm/{id}', [AuctionController::class, 'confirm'])->name('confirm')->middleware('auth');
    
    // Rutas para usuarios autenticados
    Route::post('/{id}/bid', [AuctionController::class, 'bid'])->name('bid')->middleware('auth');
    Route::post('/start/{id}', [AuctionController::class, 'start'])->name('start')->middleware('auth');
    Route::post('/cancel/{id}', [AuctionController::class, 'cancel'])->name('cancel')->middleware('auth');
    Route::post('/claim/{id}', [AuctionController::class, 'claimPrize'])->name('claim')->middleware('auth');
    
    // Rutas de administración de subastas (solo admins)
    Route::post('/{id}/extend', [AuctionController::class, 'extendAuction'])->name('extend')->middleware('auth');
    Route::post('/{id}/reduce', [AuctionController::class, 'reduceAuction'])->name('reduce')->middleware('auth');
    Route::post('/{id}/reset', [AuctionController::class, 'resetAuctionTime'])->name('reset')->middleware('auth');
    Route::post('/{id}/force-end', [AuctionController::class, 'forceEndAuction'])->name('force-end')->middleware('auth');
});

// ============================================
// RUTAS DE SORTEOS (PÚBLICAS)
// ============================================
Route::prefix('raffles')->name('raffles.')->group(function () {
    Route::get('/', [RaffleController::class, 'index'])->name('index');
    Route::get('/{id}', [RaffleController::class, 'show'])->name('show');
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

Route::match(['get', 'post'], '/contacto', function () {
    if (request()->isMethod('post')) {
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);
        
        return redirect()->back()->with('success', '¡Mensaje enviado correctamente! Te responderemos pronto.');
    }
    
    return view('contact');
})->name('contact');

// ============================================
// RUTAS DE VALORACIONES (PÚBLICAS)
// ============================================
Route::get('/products/{product}/reviews', function(App\Models\Product $product) {
    return response()->json([
        'reviews' => $product->approvedReviews()->with('user')->latest()->get()
    ]);
})->name('products.reviews');

Route::post('/products/{product}/reviews', [App\Http\Controllers\ProductReviewController::class, 'store'])
    ->name('products.reviews.store')
    ->middleware('auth');

Route::put('/reviews/{review}', [App\Http\Controllers\ProductReviewController::class, 'update'])
    ->name('reviews.update')
    ->middleware('auth');

Route::delete('/reviews/{review}', [App\Http\Controllers\ProductReviewController::class, 'destroy'])
    ->name('reviews.destroy')
    ->middleware('auth');

// ============================================
// RUTAS DE CARRITO Y PEDIDOS
// ============================================
Route::post('/cart/add/{id}', [OrderController::class, 'addToCart'])->name('cart.add')->middleware('auth');
Route::get('/cart', [OrderController::class, 'viewCart'])->name('cart.index')->middleware('auth');
Route::post('/cart/update/{id}', [OrderController::class, 'updateCart'])->name('cart.update')->middleware('auth');
Route::post('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove')->middleware('auth');
Route::post('/cart/clear', [OrderController::class, 'clearCart'])->name('cart.clear')->middleware('auth');
Route::get('/checkout', [OrderController::class, 'checkoutForm'])->name('cart.checkout.form')->middleware('auth');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('cart.checkout')->middleware('auth');

// ============================================
// RUTAS DE BÚSQUEDA
// ============================================
Route::get('/buscar', [App\Http\Controllers\SearchController::class, 'search'])->name('search');
Route::get('/buscar/sugerencias', [App\Http\Controllers\SearchController::class, 'suggestions'])->name('search.suggestions');

// ============================================
// RUTAS DE PEDIDOS
// ============================================
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('auth');

// ============================================
// RUTAS DE AUTENTICACIÓN
// ============================================
require __DIR__.'/auth.php';
