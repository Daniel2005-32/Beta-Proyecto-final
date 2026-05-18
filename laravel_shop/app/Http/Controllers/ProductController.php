<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_in_auction', false);

        // Bypass parent-only scope if searching
        if ($request->has('q')) {
            $searchTerm = strtolower(trim($request->q));
            // Si la búsqueda es solo un término genérico, no saltamos el scope global
            // para evitar que aparezcan todos los tomos individuales por error.
            $genericTerms = ['tomo', 'vol', 'volumen', 'volume'];
            if (!in_array($searchTerm, $genericTerms)) {
                $query->withoutGlobalScopes();
            }
        }

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

        // Filtros por franquicia
        if ($request->has('anime')) {
            $query->where('is_anime', true);
        }
        if ($request->has('marvel')) {
            $query->where('is_marvel', true);
        }
        if ($request->has('star_wars')) {
            $query->where('is_star_wars', true);
        }
        if ($request->has('dc')) {
            $query->where('is_dc', true);
        }

        // Filtro por ofertas (descuentos)
        if ($request->has('offers')) {
            $query->whereColumn('original_price', '>', 'price');
        }


        // Filtro por búsqueda (q)
        if ($request->has('q')) {
            $q = strtolower($request->q); // Convert query to lowercase once
            $query->where(function($b) use ($q) {
                $b->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), 'LIKE', '%' . $q . '%')
                  ->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(description)'), 'LIKE', '%' . $q . '%');
            });

        }


        // Ordenar dinámicamente
        if ($request->has('sort')) {
            $sort = $request->sort;
            if ($sort === 'oldest') {
                $query->oldest();
            } elseif ($sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }


        // Obtener versión de caché para invalidación inteligente
        $version = Cache::get('products_cache_version', 1);
        $cacheKey = "products_index_v{$version}_" . md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function() use ($query, $request) {
            $products = $query->paginate(30);
            
            // Caché de categorías por separado para que sea más eficiente
            $categories = Cache::remember('categories_with_counts', now()->addMinutes(60), function() {
                return Category::withCount('products')->get();
            });

            return [
                'products' => $products,
                'categories' => $categories
            ];
        });

        return response()->json($data);
    }

    public function show($slug)
    {
        try {
            // Bypass global scope to allow landing on a child volume directly via search
            $product = Product::withoutGlobalScopes()->with(['category', 'approvedReviews.user', 'children', 'parent.children'])->where('slug', $slug)->firstOrFail();
        } catch (\Exception $e) {
            // Fallback
            $product = Product::withoutGlobalScopes()->with(['category', 'children', 'parent.children'])->where('slug', $slug)->firstOrFail();
        }


        
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
        $version = Cache::get('products_cache_version', 1);
        $cacheKey = 'products_category_' . $categorySlug . '_v' . $version . '_page_' . request('page', 1);

        return Cache::remember($cacheKey, now()->addMinutes(10), function() use ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->firstOrFail();
            $products = Product::where('category_id', $category->id)
                ->where('is_in_auction', false)

                ->whereNull('parent_id')
                ->latest()
                ->paginate(30);

            return [
                'category' => $category,
                'products' => $products
            ];
        });
    }

    public function exclusivos(Request $request)
    {
        $query = Product::with('category')->where('is_exclusive', true)
            ->whereNull('parent_id')

            ->where('is_in_auction', false);

        // Filtro por categoría en exclusivos
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $products = $query->latest()->paginate(30); 

        
        $categories = Category::whereHas('products', function($q) {
            $q->where('is_exclusive', true)
            
            ->where('is_in_auction', false);
        })->withCount(['products' => function($q) {
            $q->where('is_exclusive', true)
            
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
            ->whereNull('parent_id')

            ->where('is_in_auction', false)
            ->latest()
            ->paginate(30); 

        
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

        $query = Product::withoutGlobalScopes()->with('category');



        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('slug', 'LIKE', "%$search%");
            });
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();

        return response()->json([
            'products' => $products->items(),
            'categories' => $categories,
            'total' => $products->total(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage()
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $productData = $this->processProductRequest($request);

        $productData['user_id'] = $request->user()->id;

        $productData['slug'] = Str::slug($productData['name']) . '-' . uniqid();

        if ($request->filled('image_url')) {
            $productData['image'] = $request->input('image_url');
        } elseif ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('products', 'public');
        } else {
            $productData['image'] = ''; 
        }

        $product = Product::create($productData);

        // Invalidar caché
        Cache::increment('products_cache_version');
        Cache::forget('categories_with_counts');

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

        $product = Product::withoutGlobalScopes()->findOrFail($id);

        $productData = $this->processProductRequest($request);




        if ($request->name !== $product->name) {
            $productData['slug'] = Str::slug($request->name) . '-' . uniqid();
        }

        if ($request->filled('image_url')) {
            $productData['image'] = $request->input('image_url');
        } elseif ($request->hasFile('image')) {
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            $productData['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($productData);

        // Invalidar caché
        Cache::increment('products_cache_version');
        Cache::forget('categories_with_counts');

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

        $product = Product::withoutGlobalScopes()->findOrFail($id);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        // Invalidar caché
        Cache::increment('products_cache_version');
        Cache::forget('categories_with_counts');

        return response()->json([
            'message' => 'Producto eliminado.'
        ], 200);
    }
    private function processProductRequest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'parent_id' => 'nullable|exists:products,id'
        ]);

        // Manejo automático de booleanos
        $booleans = ['is_exclusive', 'featured', 'trending', 'is_anime', 'is_marvel', 'is_star_wars', 'is_dc', 'is_censored'];
        foreach ($booleans as $bool) {
            $validated[$bool] = $request->boolean($bool);
        }

        return $validated;
    }
}
