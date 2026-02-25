<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->get('category');
        
        $query = Product::with('category');
        
        if ($categorySlug) {
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        $products = $query->get();
        $categories = Category::all();
        
        return view('products.index', compact('products', 'categories')); // 👈 DEBE SER view(), NO response()->json()
    }

    public function show($slug)
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        
        return view('products.show', compact('product')); // 👈 DEBE SER view(), NO response()->json()
    }

    public function byCategory($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->with('category')->get();
        $categories = Category::all();
        
        return view('products.category', compact('products', 'categories', 'category')); // 👈 DEBE SER view()
    }
}
