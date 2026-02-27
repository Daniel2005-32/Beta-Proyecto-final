<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Productos específicos (los que tenías en ProductData)
        $products = [
            // Consolas
            [
                'name' => 'PlayStation 5',
                'slug' => 'ps5',
                'description' => 'La consola de nueva generación con gráficos 4K y SSD ultrarrápido',
                'price' => 499.99,
                'image' => 'https://images.unsplash.com/photo-1644571580646-7048372c491a?w=500',
                'category' => 'Consolas',
                'category_slug' => 'consolas',
                'stock' => 10,
                'featured' => true,
                'trending' => true
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'slug' => 'switch-oled',
                'description' => 'La consola híbrida con pantalla OLED de 7 pulgadas',
                'price' => 349.99,
                'image' => 'https://images.unsplash.com/photo-1676261233849-0755de764396?w=500',
                'category' => 'Consolas',
                'category_slug' => 'consolas',
                'stock' => 5,
                'featured' => true,
                'trending' => false
            ],
            
            // Videojuegos
            [
                'name' => 'Marvel\'s Spider-Man 2',
                'slug' => 'spider-man-2',
                'description' => 'Exclusivo de PlayStation 5 - Continúa la historia de Peter Parker y Miles Morales',
                'price' => 79.99,
                'image' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?w=500',
                'category' => 'Videojuegos',
                'category_slug' => 'videojuegos',
                'stock' => 15,
                'featured' => true,
                'trending' => true
            ],
            
            // Manga
            [
                'name' => 'One Piece Box Set',
                'slug' => 'one-piece-box',
                'description' => 'Colección completa tomos 1-23 con estuche especial',
                'price' => 189.99,
                'image' => 'https://images.unsplash.com/photo-1760113426097-a4076e96a63d?w=500',
                'category' => 'Manga',
                'category_slug' => 'manga',
                'stock' => 2,
                'featured' => true,
                'trending' => true
            ],
            
            // Productos Anime
            [
                'name' => 'Figura Goku Ultra Instinct',
                'slug' => 'figura-goku',
                'description' => 'Figura de colección de 30cm con base incluida',
                'price' => 45.99,
                'image' => 'https://images.unsplash.com/photo-1765633358966-45a72a11fdaa?w=500',
                'category' => 'Productos Anime',
                'category_slug' => 'productos-anime',
                'stock' => 4,
                'featured' => true,
                'trending' => true
            ],
            
            // Cosplay - ANNA YAMADA
            [
                'name' => 'Cosplay Anna Yamada',
                'slug' => 'annayamada-cosplay',
                'description' => 'Disfraz oficial de Anna Yamada del anime "Boku no Kokoro no Yabai Yatsu". Incluye peluca, uniforme escolar y accesorios. Tallas S-M-L disponibles.',
                'price' => 89.99,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTFfFsSLh5Cb-VR-lz16EHcGawo6v0Uo0jUA&s',
                'category' => 'Cosplay',
                'category_slug' => 'cosplay',
                'stock' => 3,
                'featured' => true,
                'trending' => true
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Crear 20 productos aleatorios adicionales
        Product::factory()->count(20)->create();
    }
}