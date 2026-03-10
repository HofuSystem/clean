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
        Schema::create('category_settings', function (Blueprint $table) {
            $table->id();
            $table->string("slug",255);  
            $table->unsignedBigInteger("category_id")->nullable();
            $table->foreign("category_id")->references("id")->on("categories")
              ->nullOnDelete();
            $table->integer("addon_price")->nullable();  
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->foreign("parent_id")->references("id")->on("category_settings")
              ->nullOnDelete();
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

        Schema::create('category_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('category_setting_id');
            $table->unique(['category_setting_id', 'locale']);
            $table->foreign('category_setting_id')->references('id')->on('category_settings')
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
        Schema::dropIfExists('category_settings');
        Schema::dropIfExists('category_setting_translations');
    
    }
};
