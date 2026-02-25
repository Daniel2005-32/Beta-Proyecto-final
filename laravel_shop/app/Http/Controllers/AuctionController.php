<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index()
    {
        $auctions = Auction::with('product.category')
            ->where('status', 'active')
            ->orderBy('end_time')
            ->get();
            
        return view('auctions.index', compact('auctions'));
    }

    public function show($id)
    {
        $auction = Auction::with('product.category', 'bids.user')->findOrFail($id);
        return view('auctions.show', compact('auction'));
    }

    public function placeBid(Request $request, $id)
    {
        $auction = Auction::findOrFail($id);
        
        $request->validate([
            'amount' => 'required|numeric|min:' . ($auction->current_bid + $auction->min_increment)
        ]);

        Bid::create([
            'auction_id' => $auction->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount
        ]);

        $auction->update(['current_bid' => $request->amount]);

        return redirect()->back()->with('success', 'Puja realizada correctamente');
    }
}
