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
        Schema::create('order_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("client_id")->nullable();
            $table->foreign("client_id")->references("id")->on("users")
              ->nullOnDelete();
            $table->enum("type",["day","date"]);  
            $table->enum("receiver_day",["sunday","monday","tuesday","wednesday","thursday","friday","saturday"])->nullable();  
            $table->date("receiver_date")->nullable();  
            $table->time("receiver_time")->nullable();  
            $table->time("receiver_to_time")->nullable();  
            $table->enum("delivery_day",["sunday","monday","tuesday","wednesday","thursday","friday","saturday"])->nullable();  
            $table->date("delivery_date")->nullable();  
            $table->time("delivery_time")->nullable();  
            $table->time("delivery_to_time")->nullable();  
            $table->unsignedBigInteger("receiver_address_id")->nullable();
            $table->foreign("receiver_address_id")->references("id")->on("addresses")
              ->nullOnDelete();
            $table->unsignedBigInteger("delivery_address_id")->nullable();
            $table->foreign("delivery_address_id")->references("id")->on("addresses")
              ->nullOnDelete();
            $table->longText("note")->nullable();  
            
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
        Schema::dropIfExists('order_schedules');
    }
};
