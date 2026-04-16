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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('street_name')->nullable();
            $table->string('building_no')->nullable();
            $table->string('district')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_number')->nullable();
            $table->string('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'name_en', 'street_name', 'building_no', 'district', 'postal_code', 'additional_number', 'city']);
        });
    }
};
