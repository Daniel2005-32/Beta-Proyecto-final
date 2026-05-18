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
        // 1. Limpiar productos maestros antiguos
        \Illuminate\Support\Facades\DB::table('products')
            ->where('name', 'The Eminence in Shadow (Manga)')
            ->delete();

        // 2. Obtener todos los productos para agrupar
        $products = \App\Models\Product::all();
        $series = [];
        $regex = '/^(.+?)\s*(?:[Vv]ol\.?|[Vv]olumen|[Tt]omo)\s*(\d+)$/i';

        foreach ($products as $p) {
            $normalizedName = null;
            $volNum = null;

            if (preg_match($regex, $p->name, $matches)) {
                $normalizedName = strtolower(trim($matches[1]));
                $volNum = (int)$matches[2];
            } else {
                if (preg_match('/^(.+?)-(?:vol|volumen|tomo)-(\d+)$/i', $p->slug, $matches)) {
                    $normalizedName = str_replace('-', ' ', strtolower(trim($matches[1])));
                    $volNum = (int)$matches[2];
                }
            }

            if ($normalizedName && $volNum !== null) {
                // Unificar alias comunes que podrían estar en producción
                if (str_contains($normalizedName, 'boku no kokoro') || str_contains($normalizedName, 'dangers in my heart')) {
                    $normalizedName = 'the dangers in my heart';
                }
                if (str_contains($normalizedName, 'eminence in shadow')) {
                    $normalizedName = 'the eminence in shadow';
                }
                if (str_contains($normalizedName, 'berserk deluxe')) {
                    $normalizedName = 'berserk deluxe edition';
                }

                $series[$normalizedName][$volNum][] = $p;
            }
        }

        foreach ($series as $name => $volumes) {
            ksort($volumes);
            $allVolNums = array_keys($volumes);
            $firstVolNum = $allVolNums[0];
            
            // El Maestro es el primer volumen del primer número disponible
            $master = $volumes[$firstVolNum][0];
            $master->parent_id = null;
            $master->save();
            
            foreach ($volumes as $num => $productSet) {
                foreach ($productSet as $p) {
                    if ($p->id === $master->id) continue;
                    
                    $p->parent_id = $master->id;
                    $p->name = "Tomo $num";
                    $p->save();
                }
            }
        }

        // Limpieza de caché
        \Illuminate\Support\Facades\Cache::increment('products_cache_version');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Product::whereNotNull('parent_id')->update(['parent_id' => null]);
    }
};
