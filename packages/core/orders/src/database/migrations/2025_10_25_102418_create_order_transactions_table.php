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
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_id")->nullable();
            $table->foreign("order_id")->references("id")->on("orders")
              ->nullOnDelete();
            $table->string("type",255);  
            $table->string("online_payment_method",255)->nullable();  
            $table->integer("amount");  
            $table->string("transaction_id",255)->nullable();  
            $table->unsignedBigInteger("point_id")->nullable();
            $table->foreign("point_id")->references("id")->on("points")
              ->nullOnDelete();
            $table->unsignedBigInteger("wallet_transaction_id")->nullable();
            $table->foreign("wallet_transaction_id")->references("id")->on("wallet_transactions")
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
        Schema::dropIfExists('order_transactions');
    }
};
