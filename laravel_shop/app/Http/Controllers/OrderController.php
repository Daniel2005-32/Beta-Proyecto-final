<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Raffle;
use App\Models\RaffleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }
        
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function viewCart()
    {
        return view('cart.index');
    }

    public function checkout(Request $request)
    {
        if(!Auth::check()){
            return redirect()->route('login');
        }

        $cart = session('cart');
        if(!$cart) return redirect()->route('cart.index');

        DB::beginTransaction();
        try {
            $total = 0;
            foreach($cart as $id => $details){
                $total += $details['price'] * $details['quantity'];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'paid', // Mocking payment
                'total' => $total,
                'shipping_address' => 'Test Address' // Simplified
            ]);

            foreach($cart as $id => $details){
                $product = Product::lockForUpdate()->find($id);
                
                if($product->stock < $details['quantity']){
                    throw new \Exception("Not enough stock for " . $product->name);
                }

                $product->decrement('stock', $details['quantity']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price']
                ]);

                // Auction Trigger: Exclusive + Stock becomes 1
                if($product->is_exclusive && $product->stock == 1){
                    if(!$product->auction){
                        Auction::create([
                            'product_id' => $product->id,
                            'start_price' => $product->price,
                            'current_price' => $product->price,
                            'end_time' => now()->addDays(7),
                            'status' => 'active'
                        ]);
                    }
                }
            }

            // Raffle Entry Trigger: Total >= 20
            if($total >= 20){
                $raffle = Raffle::firstOrCreate(
                    [
                        'title' => 'Sorteo Mensual ' . now()->format('F Y'),
                        'status' => 'pending'
                    ],
                    [
                        'description' => 'Sorteo automático por compras superiores a 20€',
                        'draw_date' => now()->endOfMonth()
                    ]
                );

                RaffleEntry::create([
                    'raffle_id' => $raffle->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id
                ]);
            }

            DB::commit();
            session()->forget('cart');
            return redirect()->route('home')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing order: ' . $e->getMessage());
        }
    }
}
