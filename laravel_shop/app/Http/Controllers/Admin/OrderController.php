<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\PriceHelper;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $query = Order::with(['user', 'address', 'items.product']);

        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%$search%")
                         ->orWhere('email', 'LIKE', "%$search%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        if (request()->wantsJson()) {
            return response()->json([
                'orders' => $orders
            ]);
        }
            
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $order->load(['user', 'address', 'items.product']);
        
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Acceso no autorizado'], 403);
            }
            abort(403, 'Acceso no autorizado');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'completed', 'cancelled'])]
        ]);

        try {
            $order->update(['status' => $validated['status']]);
            
            // Dispatch Real-time Notification
            event(new \App\Events\OrderStatusUpdated($order));
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado del pedido actualizado correctamente',
                    'order' => $order
                ]);
            }

            return redirect()->route('admin.orders.index')
                ->with('success', 'Estado del pedido actualizado correctamente');
                
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el estado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Acceso no autorizado'], 403);
            }
            abort(403, 'Acceso no autorizado');
        }

        try {
            $order->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido eliminado correctamente'
                ]);
            }

            return redirect()->route('admin.orders.index')
                ->with('success', 'Pedido eliminado correctamente');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el pedido: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar el pedido');
        }
    }
}
