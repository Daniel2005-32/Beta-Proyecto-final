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
        'stock', 'category_id', 'image', 'featured', 'trending',
        'is_exclusive', 'is_in_auction', 'auction_end_time',
        'auction_winner_id', 'auction_claimed', 'auction_cancelled',
        'auction_final_price', 'user_id',
        'is_anime', 'is_marvel', 'is_star_wars', 'is_dc',
        'parent_id', 'is_censored'
    ];

    protected static function booted()
    {
        static::addGlobalScope('parent_only', function ($builder) {
            $builder->whereNull('parent_id');
        });
    }

    protected $appends = ['image_url', 'average_rating', 'full_name'];


    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                // Si la URL es de localhost (proveniente del seeder local), la reemplazamos por la URL actual del backend
                return str_replace('http://localhost:8000', config('app.url'), $this->image);
            }
            return asset('storage/' . $this->image);
        }
        return null;
    }

    protected $casts = [
        'featured' => 'boolean',
        'trending' => 'boolean',
        'is_exclusive' => 'boolean',
        'is_in_auction' => 'boolean',
        'auction_claimed' => 'boolean',
        'auction_cancelled' => 'boolean',
        'is_anime' => 'boolean',
        'is_marvel' => 'boolean',
        'is_star_wars' => 'boolean',
        'is_dc' => 'boolean',

        'auction_end_time' => 'datetime',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'auction_final_price' => 'decimal:2',
        'parent_id' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auctionWinner()
    {
        return $this->belongsTo(User::class, 'auction_winner_id');
    }

    /**
     * Relación con valoraciones
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Obtener valoraciones aprobadas
     */
    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    /**
     * Calcular valoración media
     */
    public function getAverageRatingAttribute()
    {
        try {
            $reviews = $this->approvedReviews;
            if (!$reviews || $reviews->isEmpty()) {
                return 0;
            }
            return round($reviews->avg('rating'), 1);
        } catch (\Exception $e) {
            return 0; // Evitar caídas (500) si la tabla no existe en producción aún
        }
    }


    /**
     * Verificar si un usuario ya valoró este producto
     */
    public function reviewedByUser($userId)
    {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * Obtener la valoración de un usuario específico
     */
    public function getUserReview($userId)
    {
        return $this->reviews()->where('user_id', $userId)->first();
    }

    /**
     * Iniciar subasta para un producto exclusivo
     */
    public function startAuction()
    {
        if (!$this->is_exclusive || $this->stock != 1) {
            return false;
        }

        // Guardar el precio original ANTES de aplicar el descuento
        $this->original_price = $this->price;
        // Aplicar 20% de descuento para el inicio de la subasta
        $this->price = $this->price * 0.8;
        $this->is_in_auction = true;
        $this->auction_end_time = Carbon::now()->addHours(24);
        $this->auction_winner_id = null;
        $this->auction_claimed = false;
        $this->auction_cancelled = false;
        $this->auction_final_price = null;
        
        return $this->save();
    }

    /**
     * Verificar si la subasta está activa
     */
    public function isAuctionActive()
    {
        return $this->is_in_auction && 
               !$this->auction_cancelled && 
               $this->auction_end_time && 
               Carbon::now()->lt($this->auction_end_time);
    }

    /**
     * Verificar si la subasta ha terminado
     */
    public function isAuctionEnded()
    {
        return $this->is_in_auction && 
               !$this->auction_cancelled && 
               $this->auction_end_time && 
               Carbon::now()->gte($this->auction_end_time);
    }

    /**
     * Obtener el tiempo restante de la subasta
     */
    public function auctionTimeLeft()
    {
        if (!$this->isAuctionActive()) {
            return 'Finalizada';
        }
        
        $diff = Carbon::now()->diff($this->auction_end_time);
        
        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h';
        }
        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        }
        return $diff->i . 'm ' . $diff->s . 's';
    }

    /**
     * Obtener el porcentaje de tiempo transcurrido
     */
    public function getAuctionPercentage()
    {
        if (!$this->auction_end_time) {
            return 0;
        }
        
        $total = 24 * 60 * 60;
        $elapsed = Carbon::now()->diffInSeconds($this->auction_end_time, false);
        
        if ($elapsed <= 0) {
            return 100;
        }
        
        return max(0, min(100, (($total - $elapsed) / $total) * 100));
    }

    /**
     * Finalizar subasta - VERSIÓN SIMPLE Y DIRECTA
     * SIEMPRE restauramos el precio original cuando termina la subasta
     */
    public function endAuctionAndRemoveFromCatalog()
    {
        // Guardar el precio final de la subasta ANTES de restaurar
        $finalPrice = $this->price;
        
        // RESTAURAR EL PRECIO ORIGINAL SIEMPRE
        if ($this->original_price) {
            $this->price = $this->original_price;
        }
        
        if ($this->auction_winner_id) {
            // Hay ganador - guardamos el precio final
            $this->auction_final_price = $finalPrice;
            $this->stock = 0; // Producto vendido
        } else {
            // No hay ganador - solo restaurar precio
            $this->stock = 1;
        }
        
        // Limpiar campos de subasta
        $this->original_price = null;
        $this->is_in_auction = false;
        $this->auction_end_time = null;
        
        return $this->save();
    }

    /**
     * Cancelar subasta (admin)
     */
    public function cancelAuction()
    {
        $this->is_in_auction = false;
        $this->auction_cancelled = true;
        $this->auction_end_time = null;
        
        // Restauramos el precio original
        if ($this->original_price) {
            $this->price = $this->original_price;
            $this->original_price = null;
        }
        
        $this->stock = 1;
        $this->auction_final_price = null;
        
        return $this->save();
    }

    /**
     * Verificar si hay stock disponible
     */
    public function inStock()
    {
        return $this->stock > 0 && !$this->is_in_auction;
    }

    /**
     * Disminuir stock
     */
    public function decreaseStock($quantity = 1)
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            return $this->save();
        }
        return false;
    }

    /**
     * Scope para productos en oferta
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('original_price')
                     ->whereColumn('price', '<', 'original_price');
    }

    /**
     * Scope para productos exclusivos
     */
    public function scopeExclusive($query)
    {
        return $query->where('is_exclusive', true);
    }

    /**
     * Scope para subastas activas
     */
    public function scopeActiveAuctions($query)
    {
        return $query->where('is_in_auction', true)
                     ->where('auction_end_time', '>', Carbon::now());
    }

    /**
     * Relación con productos hijos (variantes/tomos)
     */
    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id')->withoutGlobalScope('parent_only');
    }

    /**
     * Obtener el nombre completo (incluyendo el nombre de la serie si es un tomo)
     */
    public function getFullNameAttribute()
    {
        if ($this->parent_id && $this->parent) {
            // Limpiar el nombre del padre (quitar Vol. 1, etc. para el prefijo)
            $parentName = preg_replace('/\s*(?:[Vv]ol\.?|[Vv]olumen|[Tt]omo)\s*\d+.*$/i', '', $this->parent->name);
            return trim($parentName) . ' - ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Relación con el producto padre (serie)
     */
    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id')->withoutGlobalScope('parent_only');
    }
}
