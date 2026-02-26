<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Productos destacados (excluyendo los que están en subasta)
        $featured = Product::where('featured', true)
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        // Productos en tendencia (excluyendo los que están en subasta)
        $trending = Product::where('trending', true)
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        // Productos exclusivos (excluyendo los que están en subasta)
        $exclusive = Product::where('is_exclusive', true)
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        return view('home', compact('featured', 'trending', 'exclusive'));
    }
}
