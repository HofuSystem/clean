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
        Schema::create('order_representatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_id")->nullable();
            $table->foreign("order_id")->references("id")->on("orders")
              ->cascadeOnDelete();
            $table->unsignedBigInteger("representative_id")->nullable();
            $table->foreign("representative_id")->references("id")->on("users")
              ->nullOnDelete();
            $table->enum("type",["delivery","technical","receiver"])->nullable();  
            $table->date("date")->nullable();  
            $table->time("time")->nullable();  
            $table->time("to_time")->nullable();  
            $table->string("lat",255)->nullable();  
            $table->string("lng",255)->nullable();  
            $table->string("location",255)->nullable();  
            $table->boolean("has_problem")->nullable();  
            
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
        Schema::dropIfExists('order_representatives');
    }
};
