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
        for ($i = 1; $i <= 13; $i++) {
            \App\Models\Product::updateOrCreate(
                ['slug' => 'boku-no-kokoro-no-yabai-yatsu-vol-' . $i],
                [
                    'name' => 'Boku no Kokoro no Yabai Yatsu Vol. ' . $i,
                    'category_id' => 3, // Manga
                    'price' => 8.00,
                    'stock' => 500,
                    'description' => 'Tomo ' . $i . ' del manga Boku no Kokoro no Yabai Yatsu (The Dangers in My Heart).',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // Opcional: No eliminamos para evitar pérdida accidental.
    }
};
