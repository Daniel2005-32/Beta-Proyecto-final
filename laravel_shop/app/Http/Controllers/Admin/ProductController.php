<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Verificación manual de admin
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $products = Product::with('category')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|url'
        ]);

        $data = $request->all();

        // Manejar checkboxes (si no vienen, son false)
        $data['featured'] = $request->has('featured') ? true : false;
        $data['trending'] = $request->has('trending') ? true : false;
        $data['is_exclusive'] = $request->has('is_exclusive') ? true : false;

        // Manejar oferta
        if ($request->has('on_sale') && $request->on_sale) {
            $data['original_price'] = $request->original_price;
        } else {
            $data['original_price'] = null;
        }

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente');
    }

    public function edit(Product $product)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        // Validación SIMPLE - sin restricciones de unicidad en el slug
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'slug' => 'required|string|max:255', // Solo requerido, no único
        ]);

        $data = [
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image' => $request->image,
        ];

        // Manejar checkboxes
        $data['featured'] = $request->has('featured') ? true : false;
        $data['trending'] = $request->has('trending') ? true : false;
        $data['is_exclusive'] = $request->has('is_exclusive') ? true : false;

        // Manejar oferta
        if ($request->has('on_sale') && $request->on_sale) {
            $data['original_price'] = $request->original_price;
        } else {
            $data['original_price'] = null;
        }

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Product $product)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado correctamente');
    }
}
