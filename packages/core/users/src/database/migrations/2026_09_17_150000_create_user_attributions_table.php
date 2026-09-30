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
        Schema::create('user_attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('install_uuid');
            $table->string('branch_device_token')->nullable();
            $table->boolean('is_attributed')->default(false);
            $table->string('ad_partner')->nullable();
            $table->string('channel')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('campaign_id')->nullable();
            $table->string('ad_set_name')->nullable();
            $table->string('ad_set_id')->nullable();
            $table->string('ad_name')->nullable();
            $table->string('ad_id')->nullable();
            $table->string('referring_link')->nullable();
            $table->string('platform')->nullable();
            $table->string('app_version')->nullable();
            $table->string('os_version')->nullable();
            $table->string('device_model')->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->json('branch_raw_data')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'install_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_attributions');
    }
};
