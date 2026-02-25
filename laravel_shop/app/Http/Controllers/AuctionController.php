<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuctionController extends Controller
{
    public function index()
    {
        $activeAuctions = Auction::with(['product', 'currentWinner'])
            ->where('status', 'active')
            ->where('end_time', '>', Carbon::now())
            ->orderBy('end_time')
            ->paginate(12);
        
        $endedAuctions = Auction::with(['product', 'currentWinner'])
            ->where('status', 'ended')
            ->orderBy('end_time', 'desc')
            ->take(5)
            ->get();
        
        return view('auctions.index', compact('activeAuctions', 'endedAuctions'));
    }

    public function show($id)
    {
        $auction = Auction::with(['product', 'bids.user', 'currentWinner'])
            ->findOrFail($id);
        
        return view('auctions.show', compact('auction'));
    }

    public function placeBid(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $auction = Auction::findOrFail($id);
        
        if (!$auction->isActive()) {
            return back()->with('error', 'Esta subasta ya ha finalizado');
        }
        
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para pujar');
        }
        
        $minAmount = $auction->current_price + $auction->min_bid;
        
        if ($request->amount < $minAmount) {
            return back()->with('error', "La puja mínima es de " . number_format($minAmount, 2) . "€");
        }
        
        $bid = Bid::create([
            'auction_id' => $auction->id,
            'user_id' => Auth::id(),
            'amount' => $request->amount
        ]);
        
        $auction->update([
            'current_price' => $request->amount,
            'current_winner_id' => Auth::id(),
            'total_bids' => $auction->total_bids + 1
        ]);
        
        return back()->with('success', '¡Puja realizada correctamente!');
    }

    public function checkEnded()
    {
        $endedAuctions = Auction::where('status', 'active')
            ->where('end_time', '<=', Carbon::now())
            ->get();
        
        foreach ($endedAuctions as $auction) {
            $auction->update(['status' => 'ended']);
            
            if ($auction->current_winner_id) {
                $auction->product->decrement('stock');
            }
        }
        
        return response()->json(['message' => count($endedAuctions) . ' subastas finalizadas']);
    }
}
