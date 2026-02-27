<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use Illuminate\Http\Request;

class RaffleController extends Controller
{
    public function index()
    {
        $activeRaffles = Raffle::with('winner')
            ->where('status', 'active')
            ->where('draw_date', '>', now())
            ->orderBy('draw_date')
            ->get();

        $upcomingRaffles = Raffle::with('winner')
            ->where('status', 'pending')
            ->orderBy('draw_date')
            ->get();

        $endedRaffles = Raffle::with('winner')
            ->where('status', 'ended')
            ->orderBy('draw_date', 'desc')
            ->take(5)
            ->get();

        return view('raffles.index', compact('activeRaffles', 'upcomingRaffles', 'endedRaffles'));
    }

    public function show($id)
    {
        $raffle = Raffle::with('winner', 'entries.user')->findOrFail($id);
        
        return view('raffles.show', compact('raffle'));
    }
}
