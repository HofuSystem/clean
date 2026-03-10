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
        Schema::table('page_translations', function (Blueprint $table) {
            $table->string('meta_title', 500)->nullable();
            $table->string('meta_description', 1000)->nullable();
        });
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('meta_title', 500)->nullable();
            $table->string('meta_description', 1000)->nullable();
        });
        Schema::table('blog_translations', function (Blueprint $table) {
            $table->dropColumn('meta');
            $table->string('meta_title', 500)->nullable();
            $table->string('meta_description', 1000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('page_translations', function (Blueprint $table) {
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
        });
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
        });
        Schema::table('blog_translations', function (Blueprint $table) {
            $table->string('meta')->nullable();
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
        });
    }
};
