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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string("reference_id",255);  
            $table->string("type",255);  
            $table->string("status")->nullable();  
            $table->unsignedBigInteger("client_id")->nullable();
            $table->foreign("client_id")->references("id")->on("users")
              ->nullOnDelete();
            $table->unsignedBigInteger("operator_id")->nullable();
            $table->foreign("operator_id")->references("id")->on("users")
              ->nullOnDelete();
            $table->enum("pay_type",["card","cash","wallet"])->nullable();  
            $table->string("transaction_id",255)->nullable();  
            $table->json("order_status_times")->nullable();  
            $table->integer("days_per_week")->nullable();  
            $table->json("days_per_week_names")->nullable();  
            $table->json("days_per_month_dates")->nullable();  
            $table->longText("note")->nullable();  
            $table->unsignedBigInteger("coupon_id")->nullable();
            $table->foreign("coupon_id")->references("id")->on("coupons")
              ->nullOnDelete();
            $table->json("coupon_data")->nullable();  
            $table->double("order_price");  
            $table->double("delivery_price")->nullable();  
            $table->double("total_price")->nullable();  
            $table->double("paid")->nullable();  
            $table->boolean("is_admin_accepted")->nullable();  
            $table->string("admin_cancel_reason",1000)->nullable();  
            $table->boolean("wallet_used")->nullable();  
            $table->double("wallet_amount_used")->nullable();  
            
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
        Schema::dropIfExists('orders');
    }
};
