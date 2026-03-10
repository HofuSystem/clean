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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string("image",255)->nullable();  
            $table->string("name",255);  
            $table->string("phone",255)->unique()->nullable();  
            $table->string("email",255)->unique();  
            $table->integer("years_experience")->nullable();  
            $table->string("address",255)->nullable();  
            $table->date("birth_date")->nullable();  
            $table->integer("hour_price")->nullable();  
            $table->enum("gender",["male","female"])->nullable();  
            $table->enum("status",["active","not-active"])->nullable();  
            $table->enum("identity",["passport","id","driver-licence"])->nullable();  
            $table->unsignedBigInteger("leader_id")->nullable();
            $table->foreign("leader_id")->references("id")->on("users")
              ->nullOnDelete();
            $table->unsignedBigInteger("nationality_id")->nullable();
            $table->foreign("nationality_id")->references("id")->on("nationalities")
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

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workers');
    }
};
