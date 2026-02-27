<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id', 'user_id', 'order_id', 'quantity', 'amount_spent'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount_spent' => 'decimal:2',
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
