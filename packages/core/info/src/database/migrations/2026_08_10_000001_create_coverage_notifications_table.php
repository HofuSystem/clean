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
        if (!Schema::hasTable('coverage_notifications')) {
            Schema::create('coverage_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->unsignedBigInteger('city_id')->nullable();
                $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
                $table->unsignedBigInteger('district_id')->nullable();
                $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
                $table->enum('type', ['resume', 'expansion'])->default('resume');
                $table->enum('status', ['pending', 'notified'])->default('pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coverage_notifications');
    }
};
