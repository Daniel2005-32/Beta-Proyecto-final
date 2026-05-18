<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Verificar si el cupón es válido
     */
    public function isValid($subtotal)
    {
        if (!$this->is_active) return false;
        
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        
        if ($subtotal < $this->min_purchase) return false;
        
        return true;
    }

    /**
     * Calcular descuento
     */
    public function calculateDiscount($subtotal)
    {
        $discount = 0;
        if ($this->type === 'fixed') {
            $discount = $this->value;
        } else {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        }
        
        return min($discount, $subtotal); // No puede ser mayor al total
    }
}
