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
        Schema::create('category_types', function (Blueprint $table) {
            $table->id();
            $table->string("slug",255);  
            $table->unsignedBigInteger("category_id")->nullable();
            $table->foreign("category_id")->references("id")->on("categories")
              ->nullOnDelete();
    $table->integer("hour_price");  
            $table->enum("status",["active","not-active"]);  
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('category_type_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            $table->string("intro",1000)->nullable();  
            $table->string("desc",1000)->nullable();  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('category_type_id');
            $table->unique(['category_type_id', 'locale']);
            $table->foreign('category_type_id')->references('id')->on('category_types')
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
        Schema::dropIfExists('category_types');
        Schema::dropIfExists('category_type_translations');
    
    }
};
