<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('stock', '>', 0)
            ->where('is_in_auction', false); // EXCLUIR PRODUCTOS EN SUBASTA

        // Filtrar por exclusivos si viene el parámetro
        if ($request->has('exclusive') && $request->exclusive == 1) {
            $query->where('is_exclusive', true);
        }
        
        // Filtrar por categoría si viene el parámetro
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->paginate(12);
        
        // Categorías con contador de productos (excluyendo los que están en subasta)
        $categories = Category::withCount(['products' => function($q) {
            $q->where('stock', '>', 0)
               ->where('is_in_auction', false); // EXCLUIR SUBASTAS DEL CONTEO
        }])->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        
        // Si el producto está en subasta, redirigir a la página de subasta
        if ($product->is_in_auction && !$product->auction_cancelled) {
            return redirect()->route('auctions.show', $product->id)
                ->with('info', 'Este producto está en subasta');
        }
        
        return view('products.show', compact('product'));
    }

    public function byCategory($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $products = Product::where('category_id', $category->id)
            ->with('category')
            ->where('stock', '>', 0)
            ->where('is_in_auction', false) // EXCLUIR PRODUCTOS EN SUBASTA
            ->paginate(12);
            
        $categories = Category::withCount(['products' => function($q) {
            $q->where('stock', '>', 0)
               ->where('is_in_auction', false);
        }])->get();
        
        return view('products.category', compact('products', 'categories', 'category'));
    }

    public function exclusivos(Request $request)
    {
        $query = Product::with('category')
            ->where('is_exclusive', true)
            ->where('stock', '>', 0)
            ->where('is_in_auction', false); // EXCLUIR PRODUCTOS EN SUBASTA
        
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->paginate(12);
        
        // Categorías que tienen productos exclusivos (excluyendo subastas)
        $categories = Category::whereHas('products', function($q) {
            $q->where('is_exclusive', true)
               ->where('stock', '>', 0)
               ->where('is_in_auction', false);
        })->withCount(['products' => function($q) {
            $q->where('is_exclusive', true)
               ->where('stock', '>', 0)
               ->where('is_in_auction', false);
        }])->get();
        
        return view('products.exclusivos', compact('products', 'categories'));
    }
}
