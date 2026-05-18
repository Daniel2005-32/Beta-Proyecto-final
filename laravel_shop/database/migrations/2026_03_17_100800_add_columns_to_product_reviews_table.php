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
        if (Schema::hasTable('product_reviews')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('product_reviews', 'product_id')) {
                    $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('product_reviews', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('product_reviews', 'rating')) {
                    $table->integer('rating')->nullable();
                }
                if (!Schema::hasColumn('product_reviews', 'comment')) {
                    $table->text('comment')->nullable();
                }
                if (!Schema::hasColumn('product_reviews', 'is_approved')) {
                    $table->boolean('is_approved')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['product_id', 'user_id', 'rating', 'comment', 'is_approved']);
        });
    }
};
