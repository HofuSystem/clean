<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->string('image_en')->nullable();
            $table->string('image_ar')->nullable();
        });
  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('image_en');
            $table->dropColumn('image_ar');
            $table->string('image')->nullable();
        });
  
    }
};
