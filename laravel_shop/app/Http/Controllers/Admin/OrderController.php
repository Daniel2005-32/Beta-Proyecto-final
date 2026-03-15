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

        $orders = Order::with(['user', 'address'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
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
            abort(403, 'Acceso no autorizado');
        }

        // Validación más estricta
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'completed', 'cancelled'])]
        ]);

        try {
            // Actualizar con el valor validado
            $order->update(['status' => $validated['status']]);
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Estado del pedido actualizado correctamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        try {
            $order->delete();
            return redirect()->route('admin.orders.index')
                ->with('success', 'Pedido eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el pedido');
        }
    }
}
