<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE features MODIFY COLUMN section ENUM('b2b', 'b2c', 'services') NOT NULL DEFAULT 'b2c'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE features MODIFY COLUMN section ENUM('b2b', 'b2c') NOT NULL DEFAULT 'b2c'");
    }
};
