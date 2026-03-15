<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Productos destacados - 5 aleatorios
        $featuredProducts = Product::where('featured', true)
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->inRandomOrder()  // ← AÑADIDO: orden aleatorio
            ->take(5)
            ->get();
        
        // Productos en oferta - 5 aleatorios
        $offerProducts = Product::where('original_price', '>', 0)
            ->whereColumn('price', '<', 'original_price')
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->inRandomOrder()  // ← AÑADIDO: orden aleatorio
            ->take(5)
            ->get();
        
        // Productos en tendencia - 5 aleatorios
        $trendingProducts = Product::where('trending', true)
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->inRandomOrder()  // ← AÑADIDO: orden aleatorio
            ->take(5)
            ->get();
        
        return view('home', compact(
            'featuredProducts', 
            'offerProducts', 
            'trendingProducts'
        ));
    }
}