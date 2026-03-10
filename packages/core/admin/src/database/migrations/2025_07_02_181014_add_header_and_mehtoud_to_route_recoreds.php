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
        Schema::table('routes_records', function (Blueprint $table) {
            $table->json("headers")->nullable();
            $table->string("method")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes_records', function (Blueprint $table) {
            $table->dropColumn("headers");
            $table->dropColumn("method");
        });
    }
};
