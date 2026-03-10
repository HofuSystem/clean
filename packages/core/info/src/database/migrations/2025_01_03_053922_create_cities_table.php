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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string("slug",255)->unique();  
            $table->double("lat")->nullable();  
            $table->double("lng")->nullable();  
            $table->string("postal_code",255)->nullable();  
            $table->string("image",255)->nullable();  
            $table->double("delivery_price")->nullable();  
            $table->enum("status",["active","not-active"])->nullable();  
            $table->unsignedBigInteger("country_id")->nullable();
            $table->foreign("country_id")->references("id")->on("countries")
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

        Schema::create('city_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('city_id');
            $table->unique(['city_id', 'locale']);
            $table->foreign('city_id')->references('id')->on('cities')
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
        Schema::dropIfExists('cities');
        Schema::dropIfExists('city_translations');
    
    }
};
