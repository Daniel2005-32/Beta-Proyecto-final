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
        // TEMPORAL: Permitir acceso a cualquier usuario autenticado
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión');
        }

        $products = Product::with('category')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión');
        }

        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|url'
        ]);

        Product::create($request->all());
        return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente');
    }

    public function edit(Product $product)
    {
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión');
        }

        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($request->all());
        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Product $product)
    {
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión');
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado correctamente');
    }
}
