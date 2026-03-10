<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('avatar', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('rating')->default(5);
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('testimonial_translations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('title', 255)->nullable(); // Position/title of the person
            $table->text('body')->nullable(); // The testimonial content
            $table->string('location', 255)->nullable();
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('testimonial_id');
            $table->unique(['testimonial_id', 'locale']);
            $table->foreign('testimonial_id')->references('id')->on('testimonials')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testimonial_translations');
        Schema::dropIfExists('testimonials');
    }
};

