<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'draw_date', 'status', 'winner_id'];

    protected $casts = [
        'draw_date' => 'datetime',
    ];

    public function entries()
    {
        return $this->hasMany(RaffleEntry::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}