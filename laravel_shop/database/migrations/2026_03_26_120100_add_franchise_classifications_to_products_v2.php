<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFranchiseClassificationsToProductsV2 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // No usamos after() porque PostgreSQL no lo soporta directamente y puede causar problemas
            if (!Schema::hasColumn('products', 'is_anime')) {
                $table->boolean('is_anime')->default(false);
            }
            if (!Schema::hasColumn('products', 'is_marvel')) {
                $table->boolean('is_marvel')->default(false);
            }
            if (!Schema::hasColumn('products', 'is_star_wars')) {
                $table->boolean('is_star_wars')->default(false);
            }
            if (!Schema::hasColumn('products', 'is_dc')) {
                $table->boolean('is_dc')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_anime', 'is_marvel', 'is_star_wars', 'is_dc']);
        });
    }
};
