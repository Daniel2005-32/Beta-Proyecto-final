<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            if (!Schema::hasColumn('auctions', 'starting_price')) {
                $table->decimal('starting_price', 10, 2)->after('product_id');
            }
            if (!Schema::hasColumn('auctions', 'current_price')) {
                $table->decimal('current_price', 10, 2)->after('starting_price');
            }
            if (!Schema::hasColumn('auctions', 'min_bid')) {
                $table->decimal('min_bid', 10, 2)->default(1.00)->after('current_price');
            }
            if (!Schema::hasColumn('auctions', 'start_time')) {
                $table->timestamp('start_time')->useCurrent()->after('min_bid');
            }
            if (!Schema::hasColumn('auctions', 'end_time')) {
                $table->timestamp('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('auctions', 'current_winner_id')) {
                $table->foreignId('current_winner_id')->nullable()->constrained('users')->after('end_time');
            }
            if (!Schema::hasColumn('auctions', 'total_bids')) {
                $table->integer('total_bids')->default(0)->after('current_winner_id');
            }
            if (!Schema::hasColumn('auctions', 'status')) {
                $table->enum('status', ['active', 'ended', 'cancelled'])->default('active')->after('total_bids');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $columns = ['starting_price', 'current_price', 'min_bid', 'start_time', 'end_time', 'current_winner_id', 'total_bids', 'status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('auctions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
