<?php

use Core\Coupons\Models\Coupon;
use Core\Orders\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $coupons = Coupon::get();
        foreach($coupons as $coupon){
            if(!isset($coupon->order_minimum)){
                $coupon->update(['order_minimum' => $coupon->minimum_price]);
            }
        }
        $orders = Order::whereNotNull('coupon_data')->get();
        foreach($orders as $order){
            $coupon = json_decode($order->coupon_data);
            if(isset($coupon->minimum_price)){
                $coupon->order_minimum = $coupon->minimum_price;
                unset($coupon->minimum_price);
                $order->update(['coupon_data' => json_encode($coupon)]);
            }
        }
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn("minimum_price");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->double("minimum_price")->nullable();
        });
    }
};
