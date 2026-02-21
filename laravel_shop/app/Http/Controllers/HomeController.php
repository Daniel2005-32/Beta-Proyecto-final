<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::where('is_featured', true)->take(4)->get();
        $trending = Product::where('is_trend', true)->take(4)->get();
        $exclusive = Product::where('is_exclusive', true)->take(4)->get();
        
        return view('home', compact('featured', 'trending', 'exclusive'));
    }
}