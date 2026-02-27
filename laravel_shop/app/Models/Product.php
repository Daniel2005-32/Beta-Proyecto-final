<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'original_price',
        'image', 'category_id', 'stock', 'featured', 'trending', 
        'is_exclusive', 'is_in_auction', 'auction_start_price',
        'auction_end_time', 'auction_winner_id', 'auction_claimed',
        'auction_cancelled'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'auction_start_price' => 'decimal:2',
        'featured' => 'boolean',
        'trending' => 'boolean',
        'is_exclusive' => 'boolean',
        'is_in_auction' => 'boolean',
        'auction_claimed' => 'boolean',
        'auction_cancelled' => 'boolean',
        'auction_end_time' => 'datetime'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function auctionWinner()
    {
        return $this->belongsTo(User::class, 'auction_winner_id');
    }

    // ============================================
    // MÉTODOS DE STOCK
    // ============================================
    
    public function inStock()
    {
        return $this->stock > 0;
    }

    public function hasStock($quantity = 1)
    {
        return $this->stock >= $quantity;
    }

    public function decreaseStock($quantity = 1)
    {
        if ($this->hasStock($quantity)) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    public function increaseStock($quantity = 1)
    {
        $this->increment('stock', $quantity);
        return true;
    }

    // ============================================
    // MÉTODOS DE SUBASTA
    // ============================================
    
    public function startAuction()
    {
        $this->is_in_auction = true;
        $this->auction_start_price = $this->price * 0.8; // 20% descuento
        $this->auction_end_time = Carbon::now()->addHours(24);
        $this->auction_cancelled = false;
        $this->save();
    }

    public function cancelAuction()
    {
        $this->is_in_auction = false;
        $this->auction_cancelled = true;
        $this->auction_end_time = null;
        $this->auction_start_price = null;
        $this->save();
    }

    public function isAuctionActive()
    {
        return $this->is_in_auction && 
               !$this->auction_cancelled &&
               $this->auction_end_time && 
               Carbon::now()->lt($this->auction_end_time);
    }

    public function isAuctionEnded()
    {
        return $this->is_in_auction && 
               !$this->auction_cancelled &&
               $this->auction_end_time && 
               Carbon::now()->gte($this->auction_end_time);
    }

    public function auctionTimeLeft()
    {
        if (!$this->isAuctionActive()) return 'Finalizada';
        
        $diff = Carbon::now()->diff($this->auction_end_time);
        
        if ($diff->days > 0) return $diff->days . 'd ' . $diff->h . 'h';
        if ($diff->h > 0) return $diff->h . 'h ' . $diff->i . 'm';
        if ($diff->i > 0) return $diff->i . 'm ' . $diff->s . 's';
        return $diff->s . 's';
    }

    public function getAuctionPercentage()
    {
        if (!$this->auction_end_time) return 0;
        
        $total = 24 * 3600; // 24 horas en segundos
        $elapsed = Carbon::now()->diffInSeconds(Carbon::parse($this->auction_end_time)->subHours(24));
        
        return min(100, ($elapsed / $total) * 100);
    }

    public function getCurrentBid()
    {
        return $this->price;
    }

    public function endAuctionAndRemoveFromCatalog()
    {
        if ($this->isAuctionEnded()) {
            // Si hay ganador, el producto se marca como reclamable pero no aparece en catálogo
            if ($this->auction_winner_id) {
                $this->stock = 0; // Se agota el producto
            } else {
                // Nadie pujó, la subasta queda desierta
                $this->is_in_auction = false;
                $this->auction_cancelled = true;
            }
            $this->save();
        }
    }
}
