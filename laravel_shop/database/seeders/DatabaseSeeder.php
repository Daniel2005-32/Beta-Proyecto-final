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
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Categories
        $categories = [
            'Videojuegos' => 'Juegos para todas las consolas',
            'Manga' => 'Comics japoneses y novelas ligeras',
            'Figuras' => 'Figuras de colección y estatuas',
            'Merchandising' => 'Ropa, tazas, llaveros y más',
            'Cosplay' => 'Disfraces y accesorios',
        ];

        foreach ($categories as $name => $desc) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $desc,
            ]);
        }

        // Products
        $catVideojuegos = Category::where('slug', 'videojuegos')->first();
        $catFiguras = Category::where('slug', 'figuras')->first();

        Product::create([
            'category_id' => $catVideojuegos->id,
            'name' => 'Elden Ring',
            'slug' => 'elden-ring',
            'description' => 'Juego del año. Edición estándar.',
            'price' => 59.99,
            'stock' => 10,
            'is_featured' => true,
            'is_trend' => true,
        ]);

        Product::create([
            'category_id' => $catVideojuegos->id,
            'name' => 'Final Fantasy VII Rebirth',
            'slug' => 'ff7-rebirth',
            'description' => 'La esperada continuación.',
            'price' => 69.99,
            'stock' => 50,
            'is_featured' => true,
        ]);

        Product::create([
            'category_id' => $catFiguras->id,
            'name' => 'Figura Goku SSJ',
            'slug' => 'goku-ssj',
            'description' => 'Figura detallada de 20cm.',
            'price' => 29.99,
            'stock' => 5,
            'is_trend' => true,
        ]);

        Product::create([
            'category_id' => $catFiguras->id,
            'name' => 'Figura Exclusiva Naruto',
            'slug' => 'naruto-exclusive',
            'description' => 'Edición limitada.',
            'price' => 120.00,
            'stock' => 2, // Close to auction trigger
            'is_exclusive' => true,
            'is_featured' => true,
        ]);
    }
}