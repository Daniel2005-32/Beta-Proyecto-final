<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'auction_cancelled')) {
                $table->boolean('auction_cancelled')->default(false)->after('auction_claimed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'auction_cancelled')) {
                $table->dropColumn('auction_cancelled');
            }
        });
    }
};
