<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Unificar 'Productos' a 'Merchandising'
        $productos = Category::where('name', 'Productos')->first();
        $merch = Category::where('slug', 'merchandising')->first();

        if (!$merch && $productos) {
            $productos->update([
                'name' => 'Merchandising',
                'slug' => 'merchandising',
                'description' => 'Todo tipo de productos merchandising gamer y anime'
            ]);
            $merch = $productos;
        } elseif (!$merch) {
            $merch = Category::create([
                'name' => 'Merchandising',
                'slug' => 'merchandising',
                'description' => 'Todo tipo de productos merchandising gamer y anime',
                'iva' => 21
            ]);
        }

        // 2. Unificar 'Productos Anime' en 'Merchandising'
        $anime = Category::where('name', 'Productos Anime')->first() ?? Category::find(4);
        if ($anime && $merch) {
            Product::where('category_id', $anime->id)->update(['category_id' => $merch->id]);
            $anime->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se puede revertir fácilmente la fusión de datos fácticos
    }
};
