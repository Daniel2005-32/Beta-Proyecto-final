<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('banned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reason');
            $table->timestamp('banned_until')->nullable(); // null = permanente
            $table->boolean('is_permanent')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'banned_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bans');
    }
};
