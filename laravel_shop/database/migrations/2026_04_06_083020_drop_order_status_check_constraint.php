<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL enum columns are often implemented with a CHECK constraint.
        // We drop it to allow any string value since status was changed to string(20) previously.
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: restore a basic check if needed, but usually not required for down() 
        // in this context as the previous state was also supposed to be a string.
    }
};
