<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Verificar si el usuario está baneado
     */
    private function checkBanned()
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            return redirect()->back()->with('error', 'No puedes realizar esta acción mientras estás baneado.');
        }
        return null;
    }

    public function addToCart(Request $request, $id)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $product = Product::findOrFail($id);
        
        if (!$product->inStock()) {
            return redirect()->back()->with('error', 'Producto sin stock');
        }

        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
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
        $check = $this->checkBanned();
        if ($check) return $check;

        $cart = Session::get('cart', []);
        $total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
        
        return view('cart.index', compact('cart', 'total'));
    }

    public function updateCart(Request $request, $id)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            $product = Product::find($id);
            
            if ($product && $product->stock >= $request->quantity) {
                $cart[$id]['quantity'] = $request->quantity;
                Session::put('cart', $cart);
                
                $total = array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $cart));
                
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
        
        return redirect()->route('cart.index')->with('error', 'Producto no encontrado en el carrito');
    }

    public function removeFromCart($id)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }
        
        return redirect()->route('cart.index')->with('success', 'Producto eliminado del carrito');
    }

    public function clearCart()
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', 'Carrito vaciado correctamente');
    }

    public function checkoutForm()
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'El carrito está vacío');
        }

        $addresses = Address::where('user_id', auth()->id())->get();
        $total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return view('cart.checkout', compact('cart', 'total', 'addresses'));
    }

    public function checkout(Request $request)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'El carrito está vacío');
        }

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id'
        ]);

        $address = Address::find($request->address_id);
        if ($address->user_id != auth()->id()) {
            abort(403);
        }

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
            'address_id' => $request->address_id,
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
