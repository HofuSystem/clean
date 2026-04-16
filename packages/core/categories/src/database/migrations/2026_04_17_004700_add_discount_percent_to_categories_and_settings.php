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
        Schema::table('categories', function (Blueprint $table) {
            $table->double('discount_percent')->nullable()->after('delivery_price');
        });

        Schema::table('category_settings', function (Blueprint $table) {
            $table->double('discount_percent')->nullable()->after('addon_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });

        Schema::table('category_settings', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }
};
