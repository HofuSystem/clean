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
        Schema::table('notifications', function (Blueprint $table) {
            $table->date('register_from')->nullable()->after('for_data');
            $table->date('register_to')->nullable()->after('register_from');
            $table->date('orders_from')->nullable()->after('register_to');
            $table->date('orders_to')->nullable()->after('orders_from');
            $table->integer('orders_min')->nullable()->after('orders_to');
            $table->integer('orders_max')->nullable()->after('orders_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('register_from');
            $table->dropColumn('register_to');
            $table->dropColumn('orders_from');
            $table->dropColumn('orders_to');
            $table->dropColumn('orders_min');
            $table->dropColumn('orders_max');
        });
    }
};
