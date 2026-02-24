<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'original_price',
        'image', 'category', 'category_slug', 'stock', 
        'featured', 'trending', 'specs'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'featured' => 'boolean',
        'trending' => 'boolean',
        'specs' => 'array'
    ];

    // Scope para productos destacados
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Scope para productos en tendencia
    public function scopeTrending($query)
    {
        return $query->where('trending', true);
    }

    // Scope por categoría
    public function scopeByCategory($query, $categorySlug)
    {
        return $query->where('category_slug', $categorySlug);
    }

    // Verificar si hay stock
    public function inStock()
    {
        return $this->stock > 0;
    }

    // Reducir stock
    public function decreaseStock($quantity = 1)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }
}