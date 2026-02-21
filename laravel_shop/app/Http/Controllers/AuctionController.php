<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionController extends Controller
{
    public function index()
    {
        $auctions = Auction::where('status', 'active')->with('product')->get();
        return view('auctions.index', compact('auctions'));
    }

    public function show($id)
    {
        $auction = Auction::with(['product', 'bids.user'])->findOrFail($id);
        return view('auctions.show', compact('auction'));
    }

    public function placeBid(Request $request, $id)
    {
        $auction = Auction::findOrFail($id);
        $amount = $request->input('amount');

        if($amount <= $auction->current_price){
            return back()->with('error', 'Bid must be higher than current price.');
        }

        Bid::create([
            'auction_id' => $auction->id,
            'user_id' => Auth::id(),
            'amount' => $amount
        ]);

        $auction->update(['current_price' => $amount, 'winner_id' => Auth::id()]);

        return back()->with('success', 'Bid placed successfully!');
    }
}
