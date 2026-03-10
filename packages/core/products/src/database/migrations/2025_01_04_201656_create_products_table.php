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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("image",255)->nullable();  
            $table->string("type");  
            $table->string("sku",255);  
            $table->boolean("is_package")->nullable();  
            $table->unsignedBigInteger("category_id")->nullable();
            $table->foreign("category_id")->references("id")->on("categories")
            ->nullOnDelete();
            $table->unsignedBigInteger("sub_category_id")->nullable();
            $table->foreign("sub_category_id")->references("id")->on("categories")
            ->nullOnDelete();
            $table->double("price");  
            $table->integer("quantity")->nullable();  
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

        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            $table->string("desc",1000)->nullable();  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('product_id');
            $table->unique(['product_id', 'locale']);
            $table->foreign('product_id')->references('id')->on('products')
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
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_translations');
    
    }
};
