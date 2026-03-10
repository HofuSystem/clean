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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("slug",255)->unique();  
            $table->string("image",255);  
            $table->string("type",255);  
            $table->double("delivery_price")->nullable();  
            $table->integer("sort")->nullable();  
            $table->boolean("is_package")->nullable();  
            $table->enum("status",["active","not-active"])->nullable();  
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->foreign("parent_id")->references("id")->on("categories")
              ->nullOnDelete();
            $table->unsignedBigInteger("city_id")->nullable();
            $table->foreign("city_id")->references("id")->on("cities")
              ->nullOnDelete();
    
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            $table->string("intro",1000)->nullable();  
            $table->longText("desc")->nullable();  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('category_id');
            $table->unique(['category_id', 'locale']);
            $table->foreign('category_id')->references('id')->on('categories')
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
        Schema::dropIfExists('categories');
        Schema::dropIfExists('category_translations');
    
    }
};
