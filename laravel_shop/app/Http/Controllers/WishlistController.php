<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Listar todos los productos en la wishlist del usuario
     */
    public function index()
    {
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id', $user->id)
            ->with(['product' => function($q) {
                $q->withoutGlobalScope('parent_only');
            }])
            ->get();
        
        return response()->json([
            'status' => 'success',
            'wishlist' => $wishlist
        ]);
    }

    /**
     * Añadir un producto a la wishlist
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        
        // Prevent duplicates (though handled by migration, good to check here)
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'El producto ya está en tu lista de deseos'
            ], 422);
        }

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto añadido a favoritos',
            'wishlist' => $wishlist
        ]);
    }

    /**
     * Eliminar un producto de la wishlist
     */
    public function destroy($product_id)
    {
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Producto eliminado de favoritos'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Producto no encontrado en tu lista'
        ], 404);
    }
}
