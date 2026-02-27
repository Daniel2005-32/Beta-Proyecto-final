<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(Bid::class);
    }

    public function currentWinner()
    {
        return $this->belongsTo(User::class, 'current_winner_id');
    }
}
