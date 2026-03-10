<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('slider_views', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('slider_id');
      $table->string('url')->nullable();
      $table->uuid('uuid')->unique();
      $table->unsignedBigInteger('views_count')->default(0);
      $table->timestamps();

      $table->foreign('slider_id')->references('id')->on('sliders')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('slider_views');
  }
};
