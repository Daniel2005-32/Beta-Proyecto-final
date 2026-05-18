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
            $table->boolean('is_censored')->default(false)->after('parent_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('show_censored_content')->default(false)->after('is_super_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_censored');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('show_censored_content');
        });
    }
};
