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
        Schema::disableForeignKeyConstraints();
        $products = [
            // Videojuegos (Category 2)
            ['name' => 'The Legend of Zelda: Breath of the Wild', 'category_id' => 2, 'price' => 59.90, 'stock' => 50, 'description' => 'Aventura épica en un mundo abierto masivo.'],
            ['name' => 'Elden Ring', 'category_id' => 2, 'price' => 69.99, 'stock' => 30, 'description' => 'Desafío y fantasía oscura en las Tierras Intermedias.'],
            ['name' => 'God of War Ragnarök', 'category_id' => 2, 'price' => 79.99, 'stock' => 20, 'description' => 'Kratos y Atreus enfrentan el fin del mundo.'],
            ['name' => 'Super Mario Odyssey', 'category_id' => 2, 'price' => 49.95, 'stock' => 40, 'description' => 'Plataformas 3D llenas de creatividad y color.'],
            ['name' => 'Hollow Knight', 'category_id' => 2, 'price' => 14.99, 'stock' => 100, 'description' => 'Obra maestra del género metroidvania.'],
            ['name' => 'Minecraft', 'category_id' => 2, 'price' => 29.95, 'stock' => 200, 'description' => 'Construye y sobrevive en un mundo infinito.'],
            ['name' => 'EA Sports FC 24', 'category_id' => 2, 'price' => 69.90, 'stock' => 60, 'description' => 'La experiencia de fútbol más realista.'],
            ['name' => 'Spider-Man 2', 'category_id' => 2, 'price' => 79.99, 'stock' => 25, 'description' => 'Peter y Miles se unen contra Venom.'],
            ['name' => 'Final Fantasy VII Rebirth', 'category_id' => 2, 'price' => 79.99, 'stock' => 15, 'description' => 'La continuación del viaje de Cloud.'],
            ['name' => 'Resident Evil 4 Remake', 'category_id' => 2, 'price' => 59.90, 'stock' => 35, 'description' => 'Leon S. Kennedy en una misión de rescate mítica.'],

            // Consolas (Category 1)
            ['name' => 'PlayStation 5 (Disc Edition)', 'category_id' => 1, 'price' => 549.90, 'stock' => 10, 'description' => 'La consola de nueva generación de Sony.'],
            ['name' => 'Xbox Series X', 'category_id' => 1, 'price' => 499.00, 'stock' => 8, 'description' => 'La potencia máxima de Microsoft.'],
            ['name' => 'Nintendo Switch OLED', 'category_id' => 1, 'price' => 349.95, 'stock' => 25, 'description' => 'Colores vibrantes en cualquier lugar.'],
            ['name' => 'Steam Deck (512GB)', 'category_id' => 1, 'price' => 549.00, 'stock' => 5, 'description' => 'Toda tu biblioteca de Steam en tus manos.'],
            ['name' => 'PlayStation 4 Pro (Segunda Mano)', 'category_id' => 1, 'price' => 249.00, 'stock' => 3, 'description' => 'Calidad 4K a un precio accesible.'],
            ['name' => 'Xbox Series S', 'category_id' => 1, 'price' => 299.00, 'stock' => 12, 'description' => 'Nueva generación, tamaño compacto.'],
            ['name' => 'Nintendo Switch Lite', 'category_id' => 1, 'price' => 219.00, 'stock' => 20, 'description' => 'Enfocada totalmente al juego portátil.'],
            ['name' => 'Analogue Pocket', 'category_id' => 1, 'price' => 219.00, 'stock' => 2, 'description' => 'La Game Boy definitiva de alta gama.'],

            // Manga (Category 3)
            ['name' => 'One Piece Vol. 1', 'category_id' => 3, 'price' => 7.95, 'stock' => 150, 'description' => 'El inicio de la leyenda de Luffy.'],
            ['name' => 'Naruto Vol. 1', 'category_id' => 3, 'price' => 7.50, 'stock' => 100, 'description' => 'La historia del ninja que quería ser Hokage.'],
            ['name' => 'Dragon Ball Vol. 1', 'category_id' => 3, 'price' => 7.95, 'stock' => 120, 'description' => 'Goku conoce a Bulma.'],
            ['name' => 'Chainsaw Man Vol. 1', 'category_id' => 3, 'price' => 8.00, 'stock' => 80, 'description' => 'Acción visceral y demonios.'],
            ['name' => 'Berserk Deluxe Edition Vol. 1', 'category_id' => 3, 'price' => 49.95, 'stock' => 15, 'description' => 'La obra maestra de Kentaro Miura en formato gigante.'],
            ['name' => 'Death Note Black Edition Vol. 1', 'category_id' => 3, 'price' => 14.95, 'stock' => 40, 'description' => 'Intriga y moralidad en formato de lujo.'],
            ['name' => 'Spy x Family Vol. 1', 'category_id' => 3, 'price' => 8.00, 'stock' => 60, 'description' => 'Espías, asesinos y telepatía familiar.'],

            // Merchandising (Category 6)
            ['name' => 'Figura Funko Pop! Iron Man', 'category_id' => 6, 'price' => 15.90, 'stock' => 50, 'description' => 'El héroe acorazado en formato cabezón.'],
            ['name' => 'Llavero Escudo de Hyrule', 'category_id' => 6, 'price' => 9.95, 'stock' => 300, 'description' => 'Protege tus llaves con el escudo legendario.'],
            ['name' => 'Taza Game Over Termo-sensible', 'category_id' => 6, 'price' => 12.50, 'stock' => 80, 'description' => 'Cambia de diseño con el calor de tu bebida.'],

            // Cosplay (Category 5)
            ['name' => 'Capa Akatsuki Premium', 'category_id' => 5, 'price' => 45.00, 'stock' => 20, 'description' => 'Únete a los renegados con esta capa de alta calidad.'],
        ];

        foreach ($products as $p) {
            \App\Models\Product::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($p['name'])],
                [
                    'name' => $p['name'],
                    'category_id' => $p['category_id'],
                    'price' => $p['price'],
                    'stock' => $p['stock'],
                    'description' => $p['description'],
                    'is_active' => true,
                    'is_exclusive' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: No eliminamos para evitar pérdida accidental, 
        // pero se podría hacer por slug si fuera necesario.
    }
};
