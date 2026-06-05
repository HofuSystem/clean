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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_for')->nullable()->default('me');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->boolean('request_address')->nullable()->default(false);
            $table->boolean('hide_identity')->nullable()->default(false);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('customizations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_for', 'recipient_name', 'recipient_phone', 'request_address', 'hide_identity']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('customizations');
        });
    }
};
