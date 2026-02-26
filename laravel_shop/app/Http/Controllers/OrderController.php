<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->inStock()) {
            return redirect()->back()->with('error', 'Producto sin stock');
        }

        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            // Verificar que no exceda el stock
            if ($product->stock > $cart[$id]['quantity']) {
                $cart[$id]['quantity']++;
            } else {
                return redirect()->back()->with('error', 'Stock insuficiente (máximo ' . $product->stock . ' unidades)');
            }
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
                'slug' => $product->slug
            ];
        }
        
        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Producto añadido al carrito');
    }

    public function viewCart()
    {
        $cart = Session::get('cart', []);
        $total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
        
        return view('cart.index', compact('cart', 'total'));
    }

    public function updateCart(Request $request, $id)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            $product = Product::find($id);
            
            // Validar que la cantidad no exceda el stock
            if ($product && $product->stock >= $request->quantity) {
                $cart[$id]['quantity'] = $request->quantity;
                Session::put('cart', $cart);
                
                // Calcular nuevo total
                $total = array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $cart));
                
                // Si es petición AJAX, devolver JSON
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'subtotal' => number_format($total, 2),
                        'total' => number_format($total, 2)
                    ]);
                }
                
                return redirect()->route('cart.index')->with('success', 'Carrito actualizado');
            } else {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Stock insuficiente'
                    ], 400);
                }
                return redirect()->route('cart.index')->with('error', 'Stock insuficiente. Máximo ' . ($product ? $product->stock : 0) . ' unidades');
            }
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Producto no encontrado'
            ], 404);
        }
        
        return redirect()->route('cart.index')->with('error', 'Producto no encontrado en el carrito');
    }

    public function removeFromCart($id)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }
        
        return redirect()->route('cart.index')->with('success', 'Producto eliminado del carrito');
    }

    public function clearCart()
    {
        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', 'Carrito vaciado correctamente');
    }

    public function checkout(Request $request)
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'El carrito está vacío');
        }

        // Verificar stock de todos los productos
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product) {
                return redirect()->route('cart.index')->with('error', 'Producto no encontrado');
            }
            if ($product->stock < $item['quantity']) {
                return redirect()->route('cart.index')->with('error', 
                    "Stock insuficiente para {$item['name']}. Disponible: {$product->stock}, solicitado: {$item['quantity']}");
            }
        }

        $total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'status' => 'pending'
        ]);

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);

            // Reducir stock
            $product->decreaseStock($item['quantity']);
        }

        Session::forget('cart');
        
        return redirect()->route('orders.show', $order)->with('success', 'Pedido realizado correctamente');
    }

    public function show(Order $order)
    {
        if (auth()->id() !== $order->user_id) {
            abort(403, 'No tienes permiso para ver este pedido');
        }
        
        return view('orders.show', compact('order'));
    }
}
