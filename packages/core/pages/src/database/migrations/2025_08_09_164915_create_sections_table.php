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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string("images",255)->nullable();  
            $table->string("video",255)->nullable();  
            // Fix: MariaDB does not allow empty enum, so use string instead or provide at least one value
            $table->string("template", 100)->nullable();  
            $table->unsignedBigInteger("page_id")->nullable();
            $table->foreign("page_id")->references("id")->on("pages")
              ->cascadeOnDelete();
    
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('section_translations', function (Blueprint $table) {
            $table->id();
            $table->string("title",255);  
            $table->string("small_title",255)->nullable();  
            $table->longText("description")->nullable();  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('section_id');
            $table->unique(['section_id', 'locale']);
            $table->foreign('section_id')->references('id')->on('sections')
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
        Schema::dropIfExists('section_translations');
        Schema::dropIfExists('sections');
    }
};
