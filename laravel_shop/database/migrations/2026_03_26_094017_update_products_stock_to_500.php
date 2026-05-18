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
        // Actualizamos el stock de los productos recién añadidos (o todos si se prefiere)
        // Usaremos una lista de slugs para ser precisos con los 29 que añadimos
        $slugs = [
            'the-legend-of-zelda-breath-of-the-wild', 'elden-ring', 'god-of-war-ragnarok',
            'super-mario-odyssey', 'hollow-knight', 'minecraft', 'ea-sports-fc-24',
            'spider-man-2', 'final-fantasy-vii-rebirth', 'resident-evil-4-remake',
            'playstation-5-disc-edition', 'xbox-series-x', 'nintendo-switch-oled',
            'steam-deck-512gb', 'playstation-4-pro-segunda-mano', 'xbox-series-s',
            'nintendo-switch-lite', 'analogue-pocket', 'one-piece-vol-1',
            'naruto-vol-1', 'dragon-ball-vol-1', 'chainsaw-man-vol-1',
            'berserk-deluxe-edition-vol-1', 'death-note-black-edition-vol-1',
            'spy-x-family-vol-1', 'figura-funko-pop-iron-man', 'llavero-escudo-de-hyrule',
            'taza-game-over-termo-sensible', 'capa-akatsuki-premium'
        ];

        \App\Models\Product::whereIn('slug', $slugs)->update(['stock' => 500]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertimos, se mantiene en 500.
    }
};
