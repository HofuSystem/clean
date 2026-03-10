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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('faq_translations', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('answer');
            $table->string('locale')->index();
            $table->unsignedBigInteger('faq_id');
            $table->unique(['faq_id', 'locale']);
            $table->foreign('faq_id')->references('id')->on('faqs')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('faqs');
    }
};
