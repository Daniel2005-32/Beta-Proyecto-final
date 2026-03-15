<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PriceHelper;

class OrderController extends Controller
{
    /**
     * Verificar si el usuario está baneado
     */
    private function checkBanned()
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            return response()->json(['error' => 'No puedes realizar esta acción mientras estás baneado.'], 403);
        }
        return null;
    }

    public function checkout(Request $request)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        if (!auth()->check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1'
        ]);

        $address = Address::find($request->address_id);
        if ($address->user_id != auth()->id()) {
            return response()->json(['error' => 'Dirección inválida'], 403);
        }

        $cart = $request->cart;
        $subtotal = 0;

        // Verificar stock de todos los productos y calcular el total real basado en la BD
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            
            if ($product->stock < $item['quantity']) {
                return response()->json([
                    'error' => "Stock insuficiente para {$product->name}. Disponible: {$product->stock}"
                ], 400);
            }

            $subtotal += ($product->price * $item['quantity']);
        }

        // Crear el pedido
        $order = Order::create([
            'user_id' => auth()->id(),
            'address_id' => $request->address_id,
            'total' => $subtotal,
            'status' => 'pending'
        ]);

        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price
            ]);

            $product->decreaseStock($item['quantity']);
        }

        // Generar entradas de sorteo
        if (method_exists($order, 'generateRaffleEntries')) {
            $order->generateRaffleEntries();
        }

        return response()->json([
            'success' => true,
            'message' => 'Pedido realizado correctamente',
            'order' => $order->load('items.product')
        ], 201);
    }

    public function myOrders()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $orders = Order::where('user_id', auth()->id())
                    ->with('items.product', 'address')
                    ->latest()
                    ->get();
                    
        return response()->json(['orders' => $orders]);
    }

    public function show(Order $order)
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->is_admin) {
            return response()->json(['error' => 'No tienes permiso para ver este pedido'], 403);
        }
        
        return response()->json([
            'order' => $order->load('items.product', 'address')
        ]);
    }
}
