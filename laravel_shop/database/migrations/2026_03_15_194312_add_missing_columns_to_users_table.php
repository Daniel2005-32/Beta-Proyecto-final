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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar')) $table->string('avatar')->nullable();
            if (!Schema::hasColumn('users', 'is_super_admin')) $table->boolean('is_super_admin')->default(false);
            if (!Schema::hasColumn('users', 'auction_notifications')) $table->json('auction_notifications')->nullable();
            if (!Schema::hasColumn('users', 'avatar_url')) $table->string('avatar_url', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'is_super_admin', 'auction_notifications', 'avatar_url']);
        });
    }
};
