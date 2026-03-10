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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string("slug",255)->unique();  
            $table->double("lat")->nullable();  
            $table->double("lng")->nullable();  
            $table->string("postal_code",255)->nullable();  
            $table->unsignedBigInteger("city_id")->nullable();
            $table->foreign("city_id")->references("id")->on("cities")
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

        Schema::create('district_translations', function (Blueprint $table) {
            $table->id();
            $table->string("name",255);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('district_id');
            $table->unique(['district_id', 'locale']);
            $table->foreign('district_id')->references('id')->on('districts')
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
        Schema::dropIfExists('districts');
        Schema::dropIfExists('district_translations');
    
    }
};
