<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Añadir columna is_in_auction
            if (!Schema::hasColumn('products', 'is_in_auction')) {
                $table->boolean('is_in_auction')->default(false)->after('stock');
            }

            // Añadir columna auction_start_price
            if (!Schema::hasColumn('products', 'auction_start_price')) {
                $table->decimal('auction_start_price', 10, 2)->nullable()->after('is_in_auction');
            }

            // Añadir columna auction_end_time
            if (!Schema::hasColumn('products', 'auction_end_time')) {
                $table->timestamp('auction_end_time')->nullable()->after('auction_start_price');
            }

            // Añadir columna auction_winner_id
            if (!Schema::hasColumn('products', 'auction_winner_id')) {
                $table->foreignId('auction_winner_id')->nullable()->constrained('users')->after('auction_end_time');
            }

            // Añadir columna auction_claimed
            if (!Schema::hasColumn('products', 'auction_claimed')) {
                $table->boolean('auction_claimed')->default(false)->after('auction_winner_id');
            }

            // Añadir columna auction_cancelled
            if (!Schema::hasColumn('products', 'auction_cancelled')) {
                $table->boolean('auction_cancelled')->default(false)->after('auction_claimed');
            }

            // Añadir columna auction_final_price
            if (!Schema::hasColumn('products', 'auction_final_price')) {
                $table->decimal('auction_final_price', 10, 2)->nullable()->after('auction_cancelled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'is_in_auction',
                'auction_start_price',
                'auction_end_time',
                'auction_winner_id',
                'auction_claimed',
                'auction_cancelled',
                'auction_final_price'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
