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

        $query = ProductReview::with(['user', 'product']);

        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('comment', 'LIKE', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%$search%");
                  })
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'LIKE', "%$search%");
                  });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        if (request()->wantsJson()) {
            return response()->json([
                'reviews' => $reviews->items(),
                'total' => $reviews->total(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage()
            ]);
        }

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(ProductReview $review)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $review->update(['is_approved' => true]);
        if (request()->wantsJson()) return response()->json(["status" => "success", "message" => "Valoración aprobada"]);

        // Redirigir de vuelta con mensaje de éxito (NO JSON)
        return redirect()->back()->with('success', 'Valoración aprobada correctamente');
    }

    public function destroy(ProductReview $review)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $review->delete();
        if (request()->wantsJson()) return response()->json(["status" => "success", "message" => "Valoración eliminada"]);

        // Redirigir de vuelta con mensaje de éxito (NO JSON)
        return redirect()->back()->with('success', 'Valoración eliminada correctamente');
    }
}
