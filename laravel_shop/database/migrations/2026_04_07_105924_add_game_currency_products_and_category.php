<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $category = \App\Models\Category::updateOrCreate(
            ['slug' => 'gacha-moneda'],
            ['name' => 'Gacha & Moneda']
        );

        $products = [
            [
                'name' => 'Genesis Crystals (Genshin Impact)',
                'slug' => 'genesis-crystals',
                'description' => 'Especial Gacha Currency for Genshin Impact. High value crystals.',
                'price' => 5.00,
                'stock' => 999,
                'category_id' => $category->id
            ],
            [
                'name' => 'Oneiric Shards (Star Rail)',
                'slug' => 'oneiric-shards',
                'description' => 'Premium Astral Shards for Honkai: Star Rail.',
                'price' => 5.00,
                'stock' => 999,
                'category_id' => $category->id
            ],
            [
                'name' => 'Originite Prime (Arknights)',
                'slug' => 'originite-prime',
                'description' => 'Core energy for Arknights. Used for scouting and resources.',
                'price' => 5.00,
                'stock' => 999,
                'category_id' => $category->id
            ],
            [
                'name' => 'Monochromes (ZZZ)',
                'slug' => 'monochromes-zzz',
                'description' => 'New Eridu premium currency for Zenless Zone Zero.',
                'price' => 5.00,
                'stock' => 999,
                'category_id' => $category->id
            ],
        ];

        foreach ($products as $productData) {
            \App\Models\Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Product::whereIn('slug', ['genesis-crystals', 'oneiric-shards', 'originite-prime', 'monochromes-zzz'])->delete();
        \App\Models\Category::where('slug', 'gacha-moneda')->delete();
    }
};
