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
        Schema::table('category_date_times', function (Blueprint $table) {
            $table->integer('receiver_count')->nullable()->after('order_count');
            $table->integer('delivery_count')->nullable()->after('receiver_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_date_times', function (Blueprint $table) {
            $table->dropColumn('receiver_count');
            $table->dropColumn('delivery_count');
        });
    }
};
