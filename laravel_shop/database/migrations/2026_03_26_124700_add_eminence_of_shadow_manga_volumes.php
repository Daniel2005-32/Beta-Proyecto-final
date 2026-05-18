<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use Illuminate\Support\Str;

class AddEminenceOfShadowMangaVolumes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $volumes = 12;
        $baseUrl = "https://www.ivrea.com.ar/eminenceinshadow/eminenceinshadow_";
        // Nota: Las URLs de Ivrea suelen variar, usaré una genérica o de una tienda conocida
        $storeUrl = "https://m.media-amazon.com/images/I/";
        $images = [
            "81OndY3YVmL.jpg", // Vol 1
            "81G5MvYvCcL.jpg", // Vol 2
            "81+m0r9p1hL.jpg", // Vol 3
            "71-tS2j8fRL.jpg", // Vol 4
            "71-tS2j8fRL.jpg", // Vol 5 (Placeholder if not found)
            "71-tS2j8fRL.jpg", // Vol 6
            "71-tS2j8fRL.jpg", // Vol 7
            "71-tS2j8fRL.jpg", // Vol 8
            "71-tS2j8fRL.jpg", // Vol 9
            "71-tS2j8fRL.jpg", // Vol 10
            "71-tS2j8fRL.jpg", // Vol 11
            "71-tS2j8fRL.jpg", // Vol 12
        ];

        for ($i = 1; $i <= $volumes; $i++) {
            $name = "The Eminence in Shadow Vol. " . $i;
            $img = isset($images[$i-1]) ? $storeUrl . $images[$i-1] : "https://m.media-amazon.com/images/I/71-tS2j8fRL.jpg";

            Product::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name) . '-' . uniqid(),
                    'description' => "Tomo número {$i} del manga 'The Eminence in Shadow'. ¡Sigue las aventuras de Cid en su camino por ser el eminencia en las sombras!",
                    'price' => 9.90,
                    'stock' => 500,
                    'category_id' => 3,
                    'is_anime' => true,
                    'image' => $img,
                    'featured' => false,
                    'trending' => false,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
}
