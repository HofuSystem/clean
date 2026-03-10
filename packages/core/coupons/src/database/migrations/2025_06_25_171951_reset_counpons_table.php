<?php

use Core\Coupons\Models\Coupon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $coupons = Coupon::get();
        foreach($coupons as $coupon){
            if($coupon->discount_type == 'percentage'){
                $coupon->type = 'percentage';
            }else if($coupon->discount_type == 'fixed_price'){
                $coupon->type = 'value';
            }else if($coupon->discount_type == 'cash_back'){
                $coupon->type = 'cashback';
            }
            $coupon->save();
        }
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn("discount_type");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->enum("type",["value","percentage","cashback"])->nullable();
        });
    }
};
