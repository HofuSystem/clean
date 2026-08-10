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
        if (Schema::hasTable('districts') && !Schema::hasColumn('districts', 'status')) {
            Schema::table('districts', function (Blueprint $table) {
                $table->enum('status', ['active', 'paused', 'not-active'])->default('active')->after('postal_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('districts') && Schema::hasColumn('districts', 'status')) {
            Schema::table('districts', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
