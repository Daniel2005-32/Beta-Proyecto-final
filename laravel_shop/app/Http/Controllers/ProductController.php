<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('stock', '>', 0);
        
        if ($request->has('exclusive') && $request->exclusive == 1) {
            $query->where('is_exclusive', true);
        }
        
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        return view('products.show', compact('product'));
    }

    public function byCategory($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $products = Product::where('category_id', $category->id)
            ->with('category')
            ->where('stock', '>', 0)
            ->paginate(12);
            
        $categories = Category::withCount('products')->get();
        
        return view('products.category', compact('products', 'categories', 'category'));
    }

    public function exclusivos(Request $request)
    {
        $query = Product::with('category')
            ->where('is_exclusive', true)
            ->where('stock', '>', 0);
        
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->paginate(12);
        
        // Categorías que tienen productos exclusivos
        $categories = Category::whereHas('products', function($q) {
            $q->where('is_exclusive', true)->where('stock', '>', 0);
        })->withCount(['products' => function($q) {
            $q->where('is_exclusive', true)->where('stock', '>', 0);
        }])->get();
        
        return view('products.exclusivos', compact('products', 'categories'));
    }
}
