<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Productos destacados - 10 aleatorios
        $featuredProducts = Product::with('category')->where('featured', true)
            ->whereNull('parent_id')

            ->where('is_in_auction', false)
            ->inRandomOrder()
            ->take(10)
            ->get();
        
        // Productos en oferta - 10 aleatorios
        $offerProducts = Product::with('category')->where('original_price', '>', 0)
            ->whereColumn('price', '<', 'original_price')
            ->whereNull('parent_id')

            ->where('is_in_auction', false)
            ->inRandomOrder()
            ->take(10)
            ->get();
        
        // Productos en tendencia - 10 aleatorios
        $trendingProducts = Product::with('category')->where('trending', true)
            ->whereNull('parent_id')

            ->where('is_in_auction', false)
            ->inRandomOrder()
            ->take(10)
            ->get();
        
        return response()->json([
            'featuredProducts' => $featuredProducts,
            'offerProducts' => $offerProducts,
            'trendingProducts' => $trendingProducts
        ]);
    }
}