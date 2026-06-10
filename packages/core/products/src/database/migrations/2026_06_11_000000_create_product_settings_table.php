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
        Schema::create('product_settings', function (Blueprint $table) {
            $table->id();
            $table->string("slug", 255);  
            $table->integer("addon_price")->nullable();  
            $table->double('discount_percent')->nullable();
            $table->double('cost')->nullable();
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->foreign("parent_id")->references("id")->on("product_settings")
              ->nullOnDelete();
            $table->enum("status", ["active", "not-active"]);  
            $table->string("color", 255)->nullable();
            $table->string("icon", 255)->nullable();
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('product_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name", 255);  
            $table->text("description")->nullable();
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('product_setting_id');
            $table->unique(['product_setting_id', 'locale']);
            $table->foreign('product_setting_id')->references('id')->on('product_settings')
                ->cascadeOnDelete();
        });

        Schema::create('product_product_setting', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('product_setting_id');
            $table->foreign('product_setting_id')->references('id')->on('product_settings')
                ->cascadeOnDelete();
            $table->unique(['product_id', 'product_setting_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_product_setting');
        Schema::dropIfExists('product_setting_translations');
        Schema::dropIfExists('product_settings');
    }
};
