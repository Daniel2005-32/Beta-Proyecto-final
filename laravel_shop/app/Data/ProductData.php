<?php

namespace App\Data;

class ProductData
{
    public static function getAll(): array
    {
        return [
            // ========== CONSOLAS ==========
            'ps5' => [
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
            'switch-oled' => [
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
            'xbox-series-x' => [
                'name' => 'Xbox Series X',
                'slug' => 'xbox-series-x',
                'description' => 'La consola más potente de Microsoft con 12 teraflops',
                'price' => 499.99,
                'image' => 'https://images.unsplash.com/photo-1621259182978-fbf931c6f07c?w=500',
                'category' => 'Consolas',
                'category_slug' => 'consolas',
                'stock' => 3,
                'featured' => false,
                'trending' => true
            ],

            // ========== VIDEOJUEGOS ==========
            'spider-man-2' => [
                'name' => 'Marvel\'s Spider-Man 2',
                'slug' => 'spider-man-2',
                'description' => 'Exclusivo de PlayStation 5 - Continúa la historia de Peter Parker y Miles Morales',
                'price' => 79.99,
                'image' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?w=500',
                'category' => 'Videojuegos',
                'category_slug' => 'videojuegos',
                'platform' => 'PS5',
                'stock' => 15,
                'featured' => true,
                'trending' => true
            ],
            'zelda-totk' => [
                'name' => 'The Legend of Zelda: Tears of the Kingdom',
                'slug' => 'zelda-totk',
                'description' => 'La aventura épica de Link continúa en Nintendo Switch',
                'price' => 69.99,
                'image' => 'https://images.unsplash.com/photo-1616587894289-86480e533129?w=500',
                'category' => 'Videojuegos',
                'category_slug' => 'videojuegos',
                'platform' => 'Nintendo Switch',
                'stock' => 8,
                'featured' => true,
                'trending' => true
            ],
            'starfield' => [
                'name' => 'Starfield',
                'slug' => 'starfield',
                'description' => 'El épico RPG espacial de Bethesda para Xbox',
                'price' => 69.99,
                'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=500',
                'category' => 'Videojuegos',
                'category_slug' => 'videojuegos',
                'platform' => 'Xbox Series X|S',
                'stock' => 12,
                'featured' => false,
                'trending' => true
            ],

            // ========== MANGA ==========
            'one-piece-box' => [
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
            'naruto-box' => [
                'name' => 'Naruto Box Set',
                'slug' => 'naruto-box',
                'description' => 'Colección completa tomos 1-27',
                'price' => 199.99,
                'image' => 'https://images.unsplash.com/photo-1535311584861-8645c64edc40?w=500',
                'category' => 'Manga',
                'category_slug' => 'manga',
                'stock' => 3,
                'featured' => false,
                'trending' => false
            ],
            'jujutsu-kaisen' => [
                'name' => 'Jujutsu Kaisen Vol. 1-10',
                'slug' => 'jujutsu-kaisen',
                'description' => 'Los primeros 10 volúmenes del éxito de ventas',
                'price' => 89.99,
                'image' => 'https://images.unsplash.com/photo-1618336753974-8f4e44f1c1b4?w=500',
                'category' => 'Manga',
                'category_slug' => 'manga',
                'stock' => 5,
                'featured' => true,
                'trending' => false
            ],

            // ========== PRODUCTOS ANIME ==========
            'figura-goku' => [
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
            'figura-naruto' => [
                'name' => 'Figura Naruto Sage Mode',
                'slug' => 'figura-naruto',
                'description' => 'Figura articulada de 25cm con accesorios',
                'price' => 39.99,
                'image' => 'https://images.unsplash.com/photo-1613376023733-0a44915a269c?w=500',
                'category' => 'Productos Anime',
                'category_slug' => 'productos-anime',
                'stock' => 6,
                'featured' => true,
                'trending' => false
            ],
            'taza-anime' => [
                'name' => 'Taza Térmica Anime',
                'slug' => 'taza-anime',
                'description' => 'Taza con diseño de varios personajes, capacidad 350ml',
                'price' => 14.99,
                'image' => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=500',
                'category' => 'Productos Anime',
                'category_slug' => 'productos-anime',
                'stock' => 15,
                'featured' => true,
                'trending' => false
            ],

            // ========== COSPLAY ==========
            'annayamada-cosplay' => [
                'name' => 'Cosplay Anna Yamada',
                'slug' => 'annayamada-cosplay',
                'description' => 'Disfraz oficial de Anna Yamada del anime "Boku no Kokoro no Yabai Yatsu". Incluye peluca, uniforme escolar y accesorios. Tallas S-M-L disponibles.',
                'price' => 89.99,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTFfFsSLh5Cb-VR-lz16EHcGawo6v0Uo0jUA&s', // <-- IMAGEN ACTUALIZADA
                'category' => 'Cosplay',
                'category_slug' => 'cosplay',
                'stock' => 3,
                'featured' => true,
                'trending' => true
            ],
            'asuka-cosplay' => [
                'name' => 'Cosplay Asuka Langley',
                'slug' => 'asuka-cosplay',
                'description' => 'Disfraz de Asuka Langley de Evangelion. Mono de batalla rojo completo con accesorios.',
                'price' => 129.99,
                'image' => 'https://images.unsplash.com/photo-1541188498278-1c91f82d6f9d?w=500',
                'category' => 'Cosplay',
                'category_slug' => 'cosplay',
                'stock' => 2,
                'featured' => false,
                'trending' => true
            ]
        ];
    }

    public static function getByCategory($categorySlug): array
    {
        $all = self::getAll();
        return array_filter($all, function($product) use ($categorySlug) {
            return $product['category_slug'] === $categorySlug;
        });
    }

    public static function getCategories(): array
    {
        $all = self::getAll();
        $categories = [];
        foreach ($all as $product) {
            $categories[$product['category_slug']] = [
                'name' => $product['category'],
                'slug' => $product['category_slug'],
                'count' => isset($categories[$product['category_slug']]) ? 
                          $categories[$product['category_slug']]['count'] + 1 : 1
            ];
        }
        return $categories;
    }

    public static function getFeatured(): array
    {
        $all = self::getAll();
        return array_filter($all, function($product) {
            return $product['featured'] ?? false;
        });
    }

    public static function getTrending(): array
    {
        $all = self::getAll();
        return array_filter($all, function($product) {
            return $product['trending'] ?? false;
        });
    }

    public static function find($slug): ?array
    {
        $all = self::getAll();
        return $all[$slug] ?? null;
    }
}