<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Debes iniciar sesión'], 401);
        }

        if (auth()->user()->isBanned()) {
            return response()->json(['error' => 'No puedes valorar productos mientras estás baneado'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Verificar si ya valoró
        if ($product->reviewedByUser(auth()->id())) {
            return response()->json(['error' => 'Ya has valorado este producto'], 400);
        }

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false // Pendiente de moderación
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Valoración enviada correctamente. Pendiente de aprobación.',
            'review' => $review->load('user')
        ]);
    }

    public function update(Request $request, ProductReview $review)
    {
        if (auth()->id() !== $review->user_id && !auth()->user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false // Requiere nueva aprobación
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Valoración actualizada. Pendiente de aprobación.'
        ]);
    }

    public function destroy(ProductReview $review)
    {
        if (auth()->id() !== $review->user_id && !auth()->user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Valoración eliminada'
        ]);
    }

    // Para administradores
    public function approve(ProductReview $review)
    {
        if (!auth()->user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $review->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Valoración aprobada'
        ]);
    }
}
