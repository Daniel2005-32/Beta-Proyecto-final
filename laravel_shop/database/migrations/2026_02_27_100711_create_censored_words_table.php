<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('censored_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->string('replacement')->default('*');
            $table->string('severity')->default('medium'); // low, medium, high
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('censored_words');
    }
};
