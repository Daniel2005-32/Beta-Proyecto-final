<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'address_id', 
        'status', 
        'subtotal', 
        'discount_amount', 
        'tax_amount', 
        'tax_type', 
        'total', 
        'points_used',
        'coupon_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'points_used' => 'integer',
        'coupon_id' => 'integer'
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Relación con entradas de sorteo
     */
    public function raffleEntries()
    {
        return $this->hasMany(RaffleEntry::class);
    }

    /**
     * Generar entradas de sorteo basadas en el total del pedido
     */
    public function generateRaffleEntries()
    {
        // Buscar sorteos activos (pending con fecha futura)
        $activeRaffles = Raffle::where('status', 'pending')
            ->where('draw_date', '>', now())
            ->get();

        foreach ($activeRaffles as $raffle) {
            $ticketPrice = $raffle->getTicketPrice();
            if ($ticketPrice <= 0) continue; // Evitar división por cero
            $quantity = floor($this->total / $ticketPrice);
            
            if ($quantity > 0) {
                $maxEntries = $raffle->getMaxEntries();
                if ($maxEntries) {
                    $currentEntries = $raffle->entries()->sum('quantity');
                    $availableEntries = $maxEntries - $currentEntries;
                    if ($availableEntries <= 0) continue;
                    $quantity = min($quantity, $availableEntries);
                }

                RaffleEntry::create([
                    'raffle_id' => $raffle->id,
                    'user_id' => $this->user_id,
                    'order_id' => $this->id,
                    'quantity' => $quantity,
                    'amount_spent' => $this->total
                ]);
            }
        }
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2) . '€';
    }
}
