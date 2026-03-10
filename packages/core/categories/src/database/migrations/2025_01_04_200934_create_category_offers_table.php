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
        Schema::create('category_offers', function (Blueprint $table) {
            $table->id();
            $table->double("price");  
            $table->double("sale_price");  
            $table->string("image",255)->nullable();  
            $table->integer("hours_num")->nullable();  
            $table->integer("workers_num")->nullable();  
            $table->enum("status",["active","not-active"]);  
            $table->string("type",255);  
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::create('category_offer_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            $table->string("desc",1000)->nullable();  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('category_offer_id');
            $table->unique(['category_offer_id', 'locale']);
            $table->foreign('category_offer_id')->references('id')->on('category_offers')
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
        Schema::dropIfExists('category_offers');
        Schema::dropIfExists('category_offer_translations');
    
    }
};
