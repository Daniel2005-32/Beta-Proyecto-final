<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->route('products.index');
        }
        
        $products = Product::with('category')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('category', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->paginate(20); // Cambiado de 12 a 20
            
        return view('search.results', compact('products', 'query'));
    }

    public function suggestions(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $products = Product::with('category')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->limit(5)
            ->get(['id', 'name', 'slug', 'price', 'image']);
            
        return response()->json($products);
    }
}
