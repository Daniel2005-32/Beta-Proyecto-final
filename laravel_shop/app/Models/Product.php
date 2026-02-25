<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'original_price',
        'image', 'category_id', 'stock', 'featured', 'trending', 'specs'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'featured' => 'boolean',
        'trending' => 'boolean',
        'specs' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function auctions()
    {
        return $this->hasMany(Auction::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('trending', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Métodos
    public function inStock()
    {
        return $this->stock > 0;
    }

    public function decreaseStock($quantity = 1)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . '€';
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }
}
