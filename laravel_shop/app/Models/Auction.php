<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'starting_price', 
        'current_bid', 
        'min_increment', 
        'end_time', 
        'winner_id', 
        'status'
    ];

    protected $casts = [
        'end_time' => 'datetime',
        'starting_price' => 'decimal:2',
        'current_bid' => 'decimal:2',
        'min_increment' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_time > now();
    }
}
