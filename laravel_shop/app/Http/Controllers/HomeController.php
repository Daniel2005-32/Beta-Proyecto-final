<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Productos destacados (featured)
        $featured = Product::where('featured', true)
            ->where('stock', '>', 0)
            ->take(4)
            ->get();
            
        // Productos en tendencia (trending)
        $trending = Product::where('trending', true)
            ->where('stock', '>', 0)
            ->take(4)
            ->get();
        
        // Productos exclusivos (is_exclusive)
        $exclusive = Product::where('is_exclusive', true)
            ->where('stock', '>', 0)
            ->take(4)
            ->get();
        
        return view('home', compact('featured', 'trending', 'exclusive'));
    }
}
