<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->date('register_from')->nullable();
            $table->date('register_to')->nullable();
            $table->date('orders_from')->nullable();
            $table->date('orders_to')->nullable();
        });

        Schema::table('coupon_translations', function (Blueprint $table) {
            $table->text('intro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['register_from', 'register_to', 'orders_from', 'orders_to']);
        });

        Schema::table('coupon_translations', function (Blueprint $table) {
            $table->dropColumn('intro');
        });
    }
};
