<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('commercial_registration')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('building_no')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('updater_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_providers');
    }
};
