<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ActualizarCategoriasProductosSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar IDs de categorías
        $figurasId = Category::where('slug', 'figuras')->first()?->id;
        $merchandisingId = Category::where('slug', 'merchandising')->first()?->id;
        $productosAnimeId = Category::where('slug', 'productos-anime')->first()?->id;

        if (!$productosAnimeId) {
            $this->command->error('Primero debes crear la categoría "Productos Anime"');
            return;
        }

        // Mover productos de FIGURAS a Productos Anime
        if ($figurasId) {
            $movidosFiguras = Product::where('category_id', $figurasId)->update([
                'category_id' => $productosAnimeId
            ]);
            $this->command->info("Se movieron {$movidosFiguras} productos de Figuras a Productos Anime");
        }

        // Mover productos de MERCHANDISING a Productos Anime
        if ($merchandisingId) {
            $movidosMerch = Product::where('category_id', $merchandisingId)->update([
                'category_id' => $productosAnimeId
            ]);
            $this->command->info("Se movieron {$movidosMerch} productos de Merchandising a Productos Anime");
        }

        // Opcional: Eliminar categorías antiguas
        if ($figurasId) {
            Category::where('slug', 'figuras')->delete();
            $this->command->info('Categoría "Figuras" eliminada');
        }

        if ($merchandisingId) {
            Category::where('slug', 'merchandising')->delete();
            $this->command->info('Categoría "Merchandising" eliminada');
        }

        $this->command->info('¡Actualización completada!');
    }
}
