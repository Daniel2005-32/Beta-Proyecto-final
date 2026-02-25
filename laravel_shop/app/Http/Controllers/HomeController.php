<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener productos desde la base de datos
        $featured = Product::with('category')->where('featured', true)->get();
        $trending = Product::with('category')->where('trending', true)->get();
        $exclusive = Product::with('category')
            ->whereHas('category', function($q) {
                $q->where('slug', 'figuras');
            })
            ->take(4)
            ->get();
        
        return view('home', compact('featured', 'trending', 'exclusive'));
    }
}