<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_translations', function (Blueprint $table) {
            $table->string('seo_image', 500)->nullable();
            $table->string('social_share_title', 500)->nullable();
            $table->string('social_share_description', 1000)->nullable();
            $table->string('social_share_image', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('page_translations', function (Blueprint $table) {
            $table->dropColumn([
                'seo_image',
                'social_share_title',
                'social_share_description',
                'social_share_image',
            ]);
        });
    }
};
