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
            return redirect()->back()->with('error', 'No puedes realizar esta acción mientras estás baneado.');
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
        
        return view('auctions.index', compact('activeAuctions'));
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
            return redirect()->route('home')->with('error', 'Esta subasta no está activa');
        }
        
        return view('auctions.show', compact('product'));
    }

    public function confirm($id)
    {
        // Verificar si el usuario está baneado
        $check = $this->checkBanned();
        if ($check) return $check;

        $product = Product::findOrFail($id);
        
        if (!$product->is_exclusive || $product->stock != 1) {
            return redirect()->route('products.show', $product->slug)
                ->with('error', 'Este producto no está disponible para subasta');
        }
        
        return view('auctions.confirm', compact('product'));
    }

    public function bid(Request $request, $id)
    {
        // Verificar si el usuario está baneado
        $check = $this->checkBanned();
        if ($check) return $check;

        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $product = Product::findOrFail($id);
        
        if (!$product->isAuctionActive()) {
            return back()->with('error', 'Esta subasta ya ha finalizado o no está activa');
        }
        
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para pujar');
        }
        
        // SIN IVA - Precio actual en BD (sin impuestos)
        $currentBid = $product->price;
        
        // La puja del usuario también es sin IVA
        $bidAmount = $request->amount;
        
        if ($bidAmount <= $currentBid) {
            $minBid = number_format($currentBid + 0.01, 2);
            return back()->with('error', "La puja debe ser mayor a {$minBid}€");
        }
        
        // Actualizar el precio en BD (sin IVA)
        $product->price = $bidAmount;
        $product->auction_winner_id = Auth::id();
        $product->save();
        
        return back()->with('success', '¡Puja realizada correctamente! Ahora eres el mejor postor');
    }

    public function start(Request $request, $id)
    {
        // Verificar si el usuario está baneado
        $check = $this->checkBanned();
        if ($check) return $check;

        $product = Product::findOrFail($id);
        
        if (!$product->is_exclusive || $product->stock != 1) {
            return redirect()->route('home')->with('error', 'Este producto no puede iniciar subasta');
        }
        
        $product->startAuction();
        
        return redirect()->route('auctions.show', $product->id)
            ->with('success', '¡Subasta iniciada! 24 horas para pujar');
    }

    public function cancel(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $product->cancelAuction();
        
        return redirect()->route('home')
            ->with('info', 'Subasta cancelada');
    }

    public function claimPrize($id)
    {
        // Verificar si el usuario está baneado
        $check = $this->checkBanned();
        if ($check) return $check;

        $product = Product::findOrFail($id);
        
        if (!Auth::check() || Auth::id() != $product->auction_winner_id) {
            return redirect()->route('home')->with('error', 'No eres el ganador de esta subasta');
        }
        
        if ($product->auction_claimed) {
            return redirect()->route('profile.index')->with('info', 'Ya has reclamado este premio');
        }
        
        // Marcar como reclamado
        $product->auction_claimed = true;
        $product->save();
        
        return redirect()->route('profile.index')->with('success', '¡Premio reclamado correctamente!');
    }

    // ============================================
    // MÉTODOS PARA ADMINISTRADORES (no requieren verificación de baneo)
    // ============================================
    
    public function extendAuction(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }
        
        $request->validate([
            'hours' => 'required|integer|min:1|max:72'
        ]);
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return back()->with('error', 'Este producto no está en subasta');
        }
        
        $hours = (int) $request->hours;
        $newEndTime = Carbon::parse($product->auction_end_time)->addHours($hours);
        $product->auction_end_time = $newEndTime;
        $product->save();
        
        return redirect()->back()->with('success', "✅ Subasta extendida {$hours} horas");
    }
    
    public function reduceAuction(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }
        
        $request->validate([
            'hours' => 'required|integer|min:1|max:24'
        ]);
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return back()->with('error', 'Este producto no está en subasta');
        }
        
        $hours = (int) $request->hours;
        $newEndTime = Carbon::parse($product->auction_end_time)->subHours($hours);
        
        if ($newEndTime < Carbon::now()) {
            return back()->with('error', '❌ No puedes reducir la subasta por debajo del tiempo actual');
        }
        
        $product->auction_end_time = $newEndTime;
        $product->save();
        
        return redirect()->back()->with('success', "✅ Subasta reducida {$hours} horas");
    }
    
    public function resetAuctionTime(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return back()->with('error', 'Este producto no está en subasta');
        }
        
        $product->auction_end_time = Carbon::now()->addHours(24);
        $product->save();
        
        return back()->with('success', '✅ Subasta reiniciada a 24 horas');
    }
    
    public function forceEndAuction($id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }
        
        $product = Product::findOrFail($id);
        
        if (!$product->is_in_auction) {
            return back()->with('error', 'Este producto no está en subasta');
        }
        
        $product->auction_end_time = Carbon::now();
        $product->save();
        $product->endAuctionAndRemoveFromCatalog();
        
        return back()->with('success', '✅ Subasta finalizada forzosamente');
    }
}