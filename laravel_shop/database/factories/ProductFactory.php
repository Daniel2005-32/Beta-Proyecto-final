<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            'consolas' => 'Consolas',
            'videojuegos' => 'Videojuegos',
            'manga' => 'Manga',
            'productos-anime' => 'Productos Anime',
            'cosplay' => 'Cosplay'
        ];
        
        $categorySlug = $this->faker->randomElement(array_keys($categories));
        $name = $this->faker->unique()->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'original_price' => $this->faker->optional(0.3)->randomFloat(2, 15, 600),
            'image' => $this->faker->imageUrl(400, 400, 'products'),
            'category' => $categories[$categorySlug],
            'category_slug' => $categorySlug,
            'stock' => $this->faker->numberBetween(0, 50),
            'featured' => $this->faker->boolean(20),
            'trending' => $this->faker->boolean(30),
            'specs' => $this->faker->optional()->randomElement([
                ['weight' => '1.5kg', 'color' => 'Negro'],
                ['material' => 'Plástico', 'origen' => 'Japón'],
                null
            ]),
        ];
    }
}