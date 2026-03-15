<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_in_auction', false)
            ->where('stock', '>', 0);

        // Filtro por categoría
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filtro por exclusivos
        if ($request->has('exclusive')) {
            $query->where('is_exclusive', true);
        }

        // Ordenar por fecha de creación (los más nuevos primero)
        $query->latest();

        // Aumentamos de 12 a 20 productos por página
        $products = $query->paginate(20);

        $categories = Category::withCount('products')->get();

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
            ->where('is_in_auction', false)
            ->where('stock', '>', 0)
            ->latest()
            ->paginate(20); // Cambiado de 12 a 20
        
        return view('products.category', compact('category', 'products'));
    }

    public function exclusivos(Request $request)
    {
        $query = Product::where('is_exclusive', true)
            ->where('stock', '>', 0)
            ->where('is_in_auction', false);

        // Filtro por categoría en exclusivos
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $products = $query->latest()->paginate(20); // Cambiado de 12 a 20
        
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

    public function offers()
    {
        $offers = Product::where('original_price', '>', 0)
            ->whereColumn('price', '<', 'original_price')
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->latest()
            ->paginate(20); // Cambiado de 8 a 20
        
        return view('offers', compact('offers'));
    }
}
