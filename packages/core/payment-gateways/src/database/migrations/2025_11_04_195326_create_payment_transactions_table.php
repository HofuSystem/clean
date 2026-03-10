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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string("transaction_id",255);  
            $table->string("gateway_transaction_id",255)->nullable();  
            $table->decimal("amount",10,2)->nullable();
            $table->string("for",255)->nullable();  
            $table->string("status",255)->nullable();  
            $table->longText("request_data")->nullable();  
            $table->string("payment_method",255)->nullable();  
            $table->longText("payment_data")->nullable();  
            $table->longText("payment_response")->nullable(); 
            $table->unsignedBigInteger("user_id")->nullable();
            $table->foreign("user_id")->references("id")->on("users")
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
        Schema::dropIfExists('payment_transactions');
    }
};
