<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Verificar y añadir columna 'featured'
            if (!Schema::hasColumn('products', 'featured')) {
                $table->boolean('featured')->default(false)->after('stock');
            }
            
            // Verificar y añadir columna 'trending'
            if (!Schema::hasColumn('products', 'trending')) {
                $table->boolean('trending')->default(false)->after('featured');
            }
            
            // Verificar y añadir columna 'is_exclusive'
            if (!Schema::hasColumn('products', 'is_exclusive')) {
                $table->boolean('is_exclusive')->default(false)->after('trending');
            }
            
            // Verificar y añadir columna 'original_price'
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['featured', 'trending', 'is_exclusive', 'original_price'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};