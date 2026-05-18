<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use Illuminate\Support\Str;

class AddFranchiseProductsToStore extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $products = [
            // STAR WARS (10)
            [
                'name' => 'LEGO Star Wars: The Skywalker Saga',
                'description' => 'Toda la saga Skywalker en un solo juego de LEGO.',
                'price' => 59.99,
                'category_id' => 2,
                'is_star_wars' => true,
                'image' => 'https://gaming-cdn.com/images/products/4346/orig/lego-star-wars-the-skywalker-saga-pc-juego-steam-europe-cover.jpg'
            ],
            [
                'name' => 'Star Wars Jedi: Survivor',
                'description' => 'La continuación de la historia de Cal Kestis.',
                'price' => 69.99,
                'category_id' => 2,
                'is_star_wars' => true,
                'image' => 'https://gaming-cdn.com/images/products/12338/orig/star-wars-jedi-survivor-pc-juego-ea-app-cover.jpg'
            ],
            [
                'name' => 'Figura Black Series Darth Vader',
                'description' => 'Figura articulada de 6 pulgadas con gran detalle.',
                'price' => 29.99,
                'category_id' => 6,
                'is_star_wars' => true,
                'image' => 'https://m.media-amazon.com/images/I/71u9S+HndCL._AC_SL1500_.jpg'
            ],
            [
                'name' => 'Lámpara Han Solo en Carbonita',
                'description' => 'Lámpara de ambiente con el icónico Han Solo congelado.',
                'price' => 45.00,
                'category_id' => 6,
                'is_star_wars' => true,
                'image' => 'https://m.media-amazon.com/images/I/61i0nTo-GLL._AC_SL1001_.jpg'
            ],
            [
                'name' => 'Casco Electrónico Stormtrooper',
                'description' => 'Casco a escala real con modulador de voz.',
                'price' => 120.00,
                'category_id' => 5,
                'is_star_wars' => true,
                'image' => 'https://m.media-amazon.com/images/I/717v2W9W+jL._AC_SL1500_.jpg'
            ],
            [
                'name' => 'Sable de Luz Rey (Skywalker) Replica',
                'description' => 'Sable de luz con luz amarilla y efectos de sonido.',
                'price' => 249.99,
                'category_id' => 5,
                'is_star_wars' => true,
                'image' => 'https://m.media-amazon.com/images/I/51r70hC9IUL._AC_SL1200_.jpg'
            ],
            [
                'name' => 'Manga Star Wars: Visiones',
                'description' => 'Antología de historias cortas en formato manga.',
                'price' => 12.95,
                'category_id' => 3,
                'is_star_wars' => true,
                'image' => 'https://m.media-amazon.com/images/I/81+mU8aY5iL.jpg'
            ],
            [
                'name' => 'Manga Star Wars: El Caballero Errante',
                'description' => 'Historia clásica ambientada en la Antigua República.',
                'price' => 14.95,
                'category_id' => 3,
                'is_star_wars' => true,
                'image' => 'https://m.media-amazon.com/images/I/91rP+Wb6GjL.jpg'
            ],
            [
                'name' => 'PlayStation 5 Edición Star Wars',
                'description' => 'Consola personalizada con motivos de la Alianza Rebelde.',
                'price' => 549.99,
                'category_id' => 1,
                'is_star_wars' => true,
                'image' => 'https://media.vandal.net/m/6-2023/20236118402127_1.jpg'
            ],
            [
                'name' => 'Nintendo Switch Skin Star Wars Edition',
                'description' => 'Consola personalizada con vinilo de Darth Vader.',
                'price' => 329.99,
                'category_id' => 1,
                'is_star_wars' => true,
                'image' => 'https://m.media-amazon.com/images/I/61L9R6NfTzL._AC_SL1000_.jpg'
            ],

            // MARVEL (7)
            [
                'name' => 'Marvel\'s Spider-Man 2',
                'description' => 'Explora Nueva York con Peter y Miles.',
                'price' => 79.99,
                'category_id' => 2,
                'is_marvel' => true,
                'image' => 'https://gaming-cdn.com/images/products/9343/orig/marvel-s-spider-man-2-ps5-juego-playstation-store-cover.jpg'
            ],
            [
                'name' => 'Marvel\'s Guardians of the Galaxy',
                'description' => 'Lidera a los Guardianes en esta aventura galáctica.',
                'price' => 39.99,
                'category_id' => 2,
                'is_marvel' => true,
                'image' => 'https://gaming-cdn.com/images/products/9027/orig/marvel-s-guardians-of-the-galaxy-pc-juego-steam-europe-cover.jpg'
            ],
            [
                'name' => 'Guantelete del Infinito Replica',
                'description' => 'Lleva el poder de Thanos en tu mano.',
                'price' => 110.00,
                'category_id' => 6,
                'is_marvel' => true,
                'image' => 'https://m.media-amazon.com/images/I/81KjB1O3iFL._AC_SL1500_.jpg'
            ],
            [
                'name' => 'Martillo Mjolnir Thor Replica',
                'description' => 'Martillo a escala real para coleccionistas.',
                'price' => 95.00,
                'category_id' => 6,
                'is_marvel' => true,
                'image' => 'https://m.media-amazon.com/images/I/71G8N4yR6CL._AC_SL1500_.jpg'
            ],
            [
                'name' => 'Disfraz Completo Iron Man MK85',
                'description' => 'Traje de alta calidad para convenciones.',
                'price' => 180.00,
                'category_id' => 5,
                'is_marvel' => true,
                'image' => 'https://m.media-amazon.com/images/I/61c8v38X-9L._AC_UX679_.jpg'
            ],
            [
                'name' => 'Escudo de Capitán América',
                'description' => 'Réplica en metal vibranium (efecto pintura).',
                'price' => 145.00,
                'category_id' => 5,
                'is_marvel' => true,
                'image' => 'https://m.media-amazon.com/images/I/71Nn5V+I0tL._AC_SL1500_.jpg'
            ],
            [
                'name' => 'Manga Deadpool: Samurai',
                'description' => 'Las aventuras de Deadpool en el universo Shonen Jump.',
                'price' => 15.00,
                'category_id' => 3,
                'is_marvel' => true,
                'image' => 'https://m.media-amazon.com/images/I/81Xm+P5U66L.jpg'
            ],

            // DC (5)
            [
                'name' => 'Suicide Squad: Kill the Justice League',
                'description' => 'Únete al Escuadrón Suicida en Metrópolis.',
                'price' => 69.99,
                'category_id' => 2,
                'is_dc' => true,
                'image' => 'https://gaming-cdn.com/images/products/7331/orig/suicide-squad-kill-the-justice-league-pc-juego-steam-cover.jpg'
            ],
            [
                'name' => 'Batman: Arkham Knight',
                'description' => 'El épico final de la saga Arkham de Rocksteady.',
                'price' => 19.99,
                'category_id' => 2,
                'is_dc' => true,
                'image' => 'https://gaming-cdn.com/images/products/447/orig/batman-arkham-knight-pc-juego-steam-europe-cover.jpg'
            ],
            [
                'name' => 'Busto Joker Edición Coleccionista',
                'description' => 'Busto altamente detallado con base temática.',
                'price' => 85.00,
                'category_id' => 6,
                'is_dc' => true,
                'image' => 'https://m.media-amazon.com/images/I/71X8vG-uCFL._AC_SL1500_.jpg'
            ],
            [
                'name' => 'Lazo de la Verdad Wonder Woman',
                'description' => 'Réplica con iluminación LED para cosplay.',
                'price' => 55.00,
                'category_id' => 5,
                'is_dc' => true,
                'image' => 'https://m.media-amazon.com/images/I/61k0H7r2BvL._AC_SL1000_.jpg'
            ],
            [
                'name' => 'Capucha de Batman (The Batman 2022)',
                'description' => 'Réplica oficial del casco de Robert Pattinson.',
                'price' => 75.00,
                'category_id' => 5,
                'is_dc' => true,
                'image' => 'https://m.media-amazon.com/images/I/61pD7X-rFPL._AC_SL1200_.jpg'
            ],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['name' => $p['name']],
                array_merge($p, [
                    'slug' => Str::slug($p['name']) . '-' . uniqid(),
                    'stock' => 500,
                    'featured' => false,
                    'trending' => false,
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No borramos por seguridad, pero se podría implementar
    }
}
