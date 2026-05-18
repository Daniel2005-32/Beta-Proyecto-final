<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\PriceHelper;

class AuctionController extends Controller
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

    public function index()
    {
        // Finalizar subastas que han terminado
        $endedAuctions = Product::where('is_in_auction', true)
            ->where('auction_cancelled', false)
            ->where('auction_end_time', '<=', Carbon::now())
            ->get();
            
        foreach ($endedAuctions as $auction) {
            $auction->endAuctionAndRemoveFromCatalog();
        }
        
        $activeAuctions = Product::with('category', 'auctionWinner')
            ->where('is_in_auction', true)
            ->where('auction_cancelled', false)
            ->where('auction_end_time', '>', Carbon::now())
            ->orderBy('auction_end_time')
            ->paginate(12);
        
        return response()->json(['activeAuctions' => $activeAuctions]);
    }

    public function show($id)
    {
        $product = Product::with('category', 'auctionWinner')->findOrFail($id);
        
        // Si la subasta acaba de terminar, la finalizamos
        if ($product->isAuctionEnded()) {
            $product->endAuctionAndRemoveFromCatalog();
            $product->refresh();
        }
        
        if (!$product->isAuctionActive() && !$product->isAuctionEnded()) {
            return response()->json(['error' => 'Esta subasta no está activa'], 400);
        }
        
        return response()->json(['product' => $product]);
    }

    public function bid(Request $request, $id)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $request->validate([
            'amount' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($id);
        
        if (!$product->isAuctionActive()) {
            return response()->json(['error' => 'Esta subasta ya ha finalizado o no está activa'], 400);
        }
        
        if (!Auth::check()) {
            return response()->json(['error' => 'Debes iniciar sesión para pujar'], 401);
        }
        
        // SIN IVA - Precio actual en BD (sin impuestos)
        $currentBid = $product->price;
        
        // La puja del usuario también es sin IVA
        $bidAmount = $request->amount;
        
        $minIncrement = ceil($currentBid * 0.10); // 10% de incremento mínimo
        if ($minIncrement < 1) $minIncrement = 1; // Incremento mínimo de 1€ si el precio es muy bajo
        $minBidAllowed = ceil($currentBid + $minIncrement);
        $maxBidAllowed = ceil($currentBid * 5.0); // 500% del precio actual

        if ($bidAmount < $minBidAllowed) {
            return response()->json(['error' => "La puja debe ser de al menos " . number_format($minBidAllowed, 0) . "€ (incremento mínimo del 10%)"], 400);
        }

        if ($bidAmount > $maxBidAllowed) {
            return response()->json(['error' => "La puja no puede superar el 500% del precio actual (máximo " . number_format($maxBidAllowed, 0) . "€)"], 400);
        }

        if ($product->user_id && Auth::id() == $product->user_id) {
            return response()->json(['error' => 'No puedes pujar en tu propia subasta'], 400);
        }
        
        $oldWinnerId = $product->auction_winner_id;

        // Actualizar el precio en BD (sin IVA)
        $product->price = $bidAmount;
        $product->auction_winner_id = Auth::id();
        $product->save();
        
        // Notificar al usuario superado (si existe y es distinto al actual)
        if ($oldWinnerId && $oldWinnerId != Auth::id()) {
            event(new \App\Events\AuctionOutbid($product, $oldWinnerId));
        }

        // Difundir la puja en tiempo real (Pública)
        broadcast(new \App\Events\AuctionBidPlaced($product->load('auctionWinner')));

        return response()->json([
            'success' => true, 
            'message' => '¡Puja realizada correctamente! Ahora eres el mejor postor',
            'product' => $product->load('auctionWinner')
        ]);
    }

    public function start(Request $request, $id)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $product = Product::findOrFail($id);
        
        if (!$product->is_exclusive || $product->stock != 1) {
            return response()->json(['error' => 'Este producto no puede iniciar subasta'], 400);
        }
        
        if (!$product->startAuction()) {
            return response()->json(['error' => 'No se pudo iniciar la subasta'], 400);
        }
        
        return response()->json([
            'success' => true, 
            'message' => '¡Subasta iniciada! 24 horas para pujar con 20% dto.',
            'product' => $product
        ]);
    }


    public function cancel(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->cancelAuction();
        
        return response()->json([
            'success' => true, 
            'message' => 'Subasta cancelada'
        ]);
    }

    public function claimPrize($id)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $product = Product::findOrFail($id);
        
        if (!Auth::check() || Auth::id() != $product->auction_winner_id) {
            return response()->json(['error' => 'No eres el ganador de esta subasta'], 403);
        }
        
        if ($product->auction_claimed) {
            return response()->json(['error' => 'Ya has reclamado este premio'], 400);
        }
        
        // Marcar como reclamado
        $product->auction_claimed = true;
        $product->save();
        
        return response()->json([
            'success' => true, 
            'message' => '¡Premio reclamado correctamente!'
        ]);
    }

    /**
     * Crear subasta por parte de un usuario
     */
    public function storeUserAuction(Request $request)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'duration' => 'required|integer|min:1|max:1000',
            'image_url' => 'required|url'
        ]);


        $product = Product::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name) . '-' . uniqid(),
            'description' => $request->description,
            'price' => $request->price,
            'original_price' => $request->price,
            'stock' => 1,
            'category_id' => $request->category_id,
            'image' => $request->image_url,
            'user_id' => Auth::id(),
            'is_in_auction' => true,
            'is_exclusive' => true,
            'auction_end_time' => Carbon::now()->addHours((int) $request->duration)
        ]);



        return response()->json([
            'success' => true,
            'message' => '¡Subasta creada correctamente!',
            'product' => $product
        ], 201);
    }

    // ============================================
    // MÉTODOS PARA ADMINISTRADORES (no requieren verificación de baneo)
    // ============================================
    
    public function extendAuction(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }
        
        $request->validate([
            'hours' => 'required|integer|min:1|max:1000'
        ]);
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return response()->json(['error' => 'Este producto no está en subasta'], 400);
        }
        
        $hours = (int) $request->hours;
        $newEndTime = Carbon::parse($product->auction_end_time)->addHours($hours);
        
        if (Carbon::now()->diffInHours($newEndTime) > 1000) {
            return response()->json(['error' => 'El tiempo restante de la subasta no puede superar las 1000 horas.'], 400);
        }

        $product->auction_end_time = $newEndTime;
        $product->save();
        
        return response()->json(['success' => true, 'message' => "Subasta extendida {$hours} horas", 'product' => $product]);
    }
    
    public function reduceAuction(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }
        
        $request->validate([
            'hours' => 'required|integer|min:1|max:1000'
        ]);
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return response()->json(['error' => 'Este producto no está en subasta'], 400);
        }
        
        $hours = (int) $request->hours;
        $newEndTime = Carbon::parse($product->auction_end_time)->subHours($hours);
        
        if ($newEndTime < Carbon::now()) {
            return response()->json(['error' => 'No puedes reducir la subasta por debajo del tiempo actual'], 400);
        }
        
        $product->auction_end_time = $newEndTime;
        $product->save();
        
        return response()->json(['success' => true, 'message' => "Subasta reducida {$hours} horas", 'product' => $product]);
    }
    
    public function resetAuctionTime(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return response()->json(['error' => 'Este producto no está en subasta'], 400);
        }
        
        $product->auction_end_time = Carbon::now()->addHours(24);
        $product->save();
        
        return response()->json(['success' => true, 'message' => 'Subasta reiniciada a 24 horas', 'product' => $product]);
    }
    
    public function forceEndAuction($id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return response()->json(['error' => 'Este producto no está en subasta'], 400);
        }
        
        $product->auction_end_time = Carbon::now();
        $product->save();
        $product->endAuctionAndRemoveFromCatalog();
        
        return response()->json(['success' => true, 'message' => 'Subasta finalizada forzosamente', 'product' => $product]);
    }
}