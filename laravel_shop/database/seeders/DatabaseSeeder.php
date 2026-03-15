<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'danielsmartin2005@gmail.com'],
            [
                'name' => 'Daniel',
                'password' => bcrypt('luigi2005'),
                'is_admin' => true,
            ]
        );

        // ===== CATEGORÍAS ACTUALIZADAS =====
        // Ahora en lugar de Figuras y Merchandising, creamos Productos Anime
        $categories = [
            'Consolas' => 'PlayStation, Xbox, Nintendo y consolas de última generación',
            'Videojuegos' => 'Juegos para todas las consolas',
            'Manga' => 'Comics japoneses y novelas ligeras',
            'Productos Anime' => 'Figuras, posters, llaveros, ropa y todo tipo de merchandising anime', // FUSIONADA
            'Cosplay' => 'Disfraces y accesorios',
        ];

        foreach ($categories as $name => $desc) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $desc,
                    'image' => null,
                ]
            );
        }

        // Obtener IDs de categorías
        $catConsolas = Category::where('slug', 'consolas')->first();
        $catVideojuegos = Category::where('slug', 'videojuegos')->first();
        $catManga = Category::where('slug', 'manga')->first();
        $catProductosAnime = Category::where('slug', 'productos-anime')->first(); // NUEVA
        $catCosplay = Category::where('slug', 'cosplay')->first();

        // ===== PRODUCTOS =====
        // Productos para CONSOLAS
        if ($catConsolas) {
            Product::firstOrCreate(
                ['slug' => 'ps5'],
                [
                    'name' => 'PlayStation 5',
                    'description' => 'La consola de nueva generación con gráficos 4K y SSD ultrarrápido',
                    'price' => 499.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1644571580646-7048372c491a?w=500',
                    'category_id' => $catConsolas->id,
                    'stock' => 10,
                    'featured' => true,
                    'trending' => true,
                    'specs' => null,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'switch-oled'],
                [
                    'name' => 'Nintendo Switch OLED',
                    'description' => 'La consola híbrida con pantalla OLED de 7 pulgadas',
                    'price' => 349.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1676261233849-0755de764396?w=500',
                    'category_id' => $catConsolas->id,
                    'stock' => 5,
                    'featured' => true,
                    'trending' => false,
                    'specs' => null,
                ]
            );
        }

        // Productos para VIDEOJUEGOS
        if ($catVideojuegos) {
            Product::firstOrCreate(
                ['slug' => 'elden-ring'],
                [
                    'name' => 'Elden Ring',
                    'description' => 'Juego del año. Edición estándar.',
                    'price' => 59.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?w=500',
                    'category_id' => $catVideojuegos->id,
                    'stock' => 10,
                    'featured' => true,
                    'trending' => true,
                    'specs' => null,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'ff7-rebirth'],
                [
                    'name' => 'Final Fantasy VII Rebirth',
                    'description' => 'La esperada continuación.',
                    'price' => 69.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=500',
                    'category_id' => $catVideojuegos->id,
                    'stock' => 50,
                    'featured' => true,
                    'trending' => false,
                    'specs' => null,
                ]
            );
        }

        // Productos para MANGA
        if ($catManga) {
            Product::firstOrCreate(
                ['slug' => 'one-piece-box'],
                [
                    'name' => 'One Piece Box Set',
                    'description' => 'Colección completa tomos 1-23 con estuche especial',
                    'price' => 189.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1760113426097-a4076e96a63d?w=500',
                    'category_id' => $catManga->id,
                    'stock' => 2,
                    'featured' => true,
                    'trending' => true,
                    'specs' => null,
                ]
            );
        }

        // ===== PRODUCTOS ANIME (FUSIÓN DE FIGURAS Y MERCHANDISING) =====
        if ($catProductosAnime) {
            // Antiguas figuras
            Product::firstOrCreate(
                ['slug' => 'goku-ssj'],
                [
                    'name' => 'Figura Goku SSJ',
                    'description' => 'Figura detallada de 20cm con base incluida',
                    'price' => 29.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1765633358966-45a72a11fdaa?w=500',
                    'category_id' => $catProductosAnime->id,
                    'stock' => 5,
                    'featured' => false,
                    'trending' => true,
                    'specs' => null,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'naruto-exclusive'],
                [
                    'name' => 'Figura Exclusiva Naruto',
                    'description' => 'Edición limitada. Figura articulada de 25cm',
                    'price' => 120.00,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1613376023733-0a44915a269c?w=500',
                    'category_id' => $catProductosAnime->id,
                    'stock' => 2,
                    'featured' => true,
                    'trending' => false,
                    'specs' => null,
                ]
            );

            // Antiguo merchandising
            Product::firstOrCreate(
                ['slug' => 'taza-anime'],
                [
                    'name' => 'Taza Térmica Anime',
                    'description' => 'Taza con diseño de varios personajes, capacidad 350ml',
                    'price' => 14.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=500',
                    'category_id' => $catProductosAnime->id,
                    'stock' => 15,
                    'featured' => true,
                    'trending' => false,
                    'specs' => null,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'llavero-anime'],
                [
                    'name' => 'Llavero Anime Surtido',
                    'description' => 'Llaveros de diferentes personajes (Pokémon, Dragon Ball, One Piece)',
                    'price' => 8.99,
                    'original_price' => null,
                    'image' => 'https://images.unsplash.com/photo-1602491453631-e2a5ad90a131?w=500',
                    'category_id' => $catProductosAnime->id,
                    'stock' => 30,
                    'featured' => false,
                    'trending' => false,
                    'specs' => null,
                ]
            );
        }

        // Productos para COSPLAY
        if ($catCosplay) {
            Product::firstOrCreate(
                ['slug' => 'annayamada-cosplay'],
                [
                    'name' => 'Cosplay Anna Yamada',
                    'description' => 'Disfraz oficial de Anna Yamada del anime "Boku no Kokoro no Yabai Yatsu"',
                    'price' => 89.99,
                    'original_price' => null,
                    'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTFfFsSLh5Cb-VR-lz16EHcGawo6v0Uo0jUA&s',
                    'category_id' => $catCosplay->id,
                    'stock' => 3,
                    'featured' => true,
                    'trending' => true,
                    'specs' => null,
                ]
            );
        }
        // Migración de Datos Locales
        if (class_exists('Database\\Seeders\\LocalDataSeeder')) {
            $this->call(LocalDataSeeder::class);
        }
    }
}
