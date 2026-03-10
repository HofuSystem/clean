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
        Schema::table('coupons', function (Blueprint $table) {
            // $table->dropColumn(["minimum_price","discount_type","expired_at"]);
            $table->enum("applying",["auto","manual"]);  
            $table->integer("max_use")->nullable();  
            $table->integer("max_use_per_user")->nullable();  
            $table->enum("payment_method",["cash","card"])->nullable();  
            $table->date("start_at")->nullable();  
            $table->date("end_at")->nullable();  
            $table->boolean("all_products")->nullable();  
            $table->boolean("all_users")->nullable();  
            $table->integer("order_minimum")->nullable();  
            $table->integer("order_maximum")->nullable();  
            $table->enum("type",["value","percentage","cashback"]);  
            $table->integer("max_value")->nullable();  

        });

        Schema::create('coupon_translations', function (Blueprint $table) {
            $table->id();
            $table->string("title",255);  
            
            $table->string('locale')->index();
            $table->unsignedBigInteger('coupon_id');
            $table->unique(['coupon_id', 'locale']);
            $table->foreign('coupon_id')->references('id')->on('coupons')
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
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('coupon_translations');
    
    }
};
