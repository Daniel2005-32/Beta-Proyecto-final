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
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_in_auction');
            $table->index('price');
            $table->index('featured');
            $table->index('is_exclusive');
            $table->index('trending');
            $table->index('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_in_auction']);
            $table->dropIndex(['price']);
            $table->dropIndex(['featured']);
            $table->dropIndex(['is_exclusive']);
            $table->dropIndex(['trending']);
            $table->dropIndex(['stock']);
        });
    }
};
