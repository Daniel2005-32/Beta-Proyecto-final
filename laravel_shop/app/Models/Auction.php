<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'starting_price', 'current_price', 'min_bid',
        'start_time', 'end_time', 'current_winner_id', 'total_bids', 'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'starting_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'min_bid' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class)->orderBy('amount', 'desc');
    }

    public function currentWinner()
    {
        return $this->belongsTo(User::class, 'current_winner_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && Carbon::now()->lt($this->end_time);
    }

    public function timeLeft()
    {
        if (!$this->isActive()) return 'Finalizada';
        
        $diff = Carbon::now()->diff($this->end_time);
        
        if ($diff->days > 0) return $diff->days . 'd ' . $diff->h . 'h';
        if ($diff->h > 0) return $diff->h . 'h ' . $diff->i . 'm';
        if ($diff->i > 0) return $diff->i . 'm ' . $diff->s . 's';
        return $diff->s . 's';
    }

    public function nextBidAmount()
    {
        return $this->current_price + $this->min_bid;
    }
}
