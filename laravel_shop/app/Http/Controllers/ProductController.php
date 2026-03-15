<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_in_auction', false)
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

        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function show($slug)
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        
        // Si el producto está en subasta, redirigir a la página de subasta o devolver aviso en JSON
        if ($product->is_in_auction && !$product->auction_cancelled) {
            return response()->json([
                'redirect' => true,
                'redirect_url' => route('auctions.show', $product->id),
                'message' => 'Este producto está en subasta'
            ], 200);
        }
        
        return response()->json([
            'product' => $product
        ]);
    }

    public function byCategory($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        
        $products = Product::where('category_id', $category->id)
            ->where('is_in_auction', false)
            ->where('stock', '>', 0)
            ->latest()
            ->paginate(20); // Cambiado de 12 a 20
        
        return response()->json([
            'category' => $category,
            'products' => $products
        ]);
    }

    public function exclusivos(Request $request)
    {
        $query = Product::with('category')->where('is_exclusive', true)
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
        
        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function offers()
    {
        $offers = Product::with('category')->where('original_price', '>', 0)
            ->whereColumn('price', '<', 'original_price')
            ->where('stock', '>', 0)
            ->where('is_in_auction', false)
            ->latest()
            ->paginate(20); 
        
        return response()->json([
            'offers' => $offers
        ]);
    }

    // --- ADMIN CRUD METHODS ---

    public function adminIndex(Request $request) 
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $products = Product::with('category')->latest()->get();
        $categories = Category::all();

        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        // Require admin privileges (we assume Sanctum auth + a basic check here or via middleware)
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_exclusive' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $validated['is_exclusive'] = $request->boolean('is_exclusive');

        if ($request->filled('image_url')) {
            $validated['image'] = $request->input('image_url');
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Producto creado con éxito.',
            'product' => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_exclusive' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $validated['is_exclusive'] = $request->boolean('is_exclusive');

        // Only update slug if name changed
        if ($request->name !== $product->name) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . uniqid();
        }

        if ($request->filled('image_url')) {
            $validated['image'] = $request->input('image_url');
        } elseif ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Producto actualizado.',
            'product' => $product
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product = Product::findOrFail($id);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado.'
        ], 200);
    }
}
