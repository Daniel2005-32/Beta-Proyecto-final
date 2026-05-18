<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $series_to_expand = [
            ['name' => 'Naruto', 'search' => 'Naruto Vol. 1', 'extra' => 10],
            ['name' => 'One Piece', 'search' => 'One Piece Vol. 1', 'extra' => 10],
            ['name' => 'Spy x Family', 'search' => 'Spy x Family Vol. 1', 'extra' => 5],
            ['name' => 'Chainsaw Man', 'search' => 'Chainsaw Man Vol. 1', 'extra' => 5],
            ['name' => 'Jujutsu Kaisen', 'search' => 'Jujutsu Kaisen Vol. 1', 'extra' => 9], 
            ['name' => 'Grand Blue', 'search' => 'Grand Blue Vol. 1', 'extra' => 4]
        ];

        foreach ($series_to_expand as $s) {
            // Buscar el padre por nombre exacto para evitar confundirlo con figuras
            $parent = Product::where('name', $s['search'])->whereNull('parent_id')->first();
            
            if (!$parent) {
                // Si no existe el Vol. 1 (como JJK o Grand Blue en el servidor), lo creamos
                $parent = Product::create([
                    'name' => $s['search'],
                    'description' => 'Manga de ' . $s['name'] . ' - Volumen 1 (Padre de serie)',
                    'price' => 8.00,
                    'stock' => 20,
                    'category_id' => 3, // Asumimos que 3 es Manga
                    'slug' => Str::slug($s['search']),
                    'image' => ''
                ]);
            }

            if (!$parent) continue;

            $current_children = Product::where('parent_id', $parent->id)->count();
            $start = $current_children + 2; 
            $end = ($current_children + 1) + $s['extra'];

            for ($i = $start; $i <= $end; $i++) {
                $name = 'Tomo ' . $i;
                $slug = Str::slug($s['name'] . '-' . $name);
                
                // Evitar duplicados si la migración se corre por error dos veces (aunque Laravel lo gestiona)
                if (!Product::withoutGlobalScopes()->where('slug', $slug)->exists()) {
                    Product::create([
                        'name' => $name,
                        'description' => 'Manga de ' . $s['name'] . ' - ' . $name,
                        'price' => $parent->price,
                        'stock' => 10,
                        'category_id' => 3,
                        'parent_id' => $parent->id,
                        'slug' => $slug,
                        'image' => $parent->image
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertimos por seguridad para no borrar datos si se equivoca, 
        // pero se podría implementar un borrado de productos con parent_id específico.
    }
};
