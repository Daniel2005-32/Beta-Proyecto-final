<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $reviews = ProductReview::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(ProductReview $review)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $review->update(['is_approved' => true]);

        // Redirigir de vuelta con mensaje de éxito (NO JSON)
        return redirect()->back()->with('success', 'Valoración aprobada correctamente');
    }

    public function destroy(ProductReview $review)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $review->delete();

        // Redirigir de vuelta con mensaje de éxito (NO JSON)
        return redirect()->back()->with('success', 'Valoración eliminada correctamente');
    }
}
