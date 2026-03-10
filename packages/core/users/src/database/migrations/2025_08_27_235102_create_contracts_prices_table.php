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
        Schema::create('contracts_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("contract_id")->nullable();
            $table->foreign("contract_id")->references("id")->on("contracts")
              ->nullOnDelete();
            $table->unsignedBigInteger("product_id")->nullable();
                    $table->foreign("product_id")->references("id")->on("products")
                    ->nullOnDelete();
            $table->float("price");  
            
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
        Schema::dropIfExists('contracts_prices');
    }
};
