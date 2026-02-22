<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/products', function () {
    return response()->json([
        ['id' => 1, 'name' => 'PlayStation 5', 'price' => 499, 'category' => 'Consolas'],
        ['id' => 2, 'name' => 'Nintendo Switch', 'price' => 349, 'category' => 'Consolas'],
    ]);
});
