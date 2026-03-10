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
        Schema::create('order_representatives_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger("order_representative_id");
            $table->foreign("order_representative_id",'representative_items_representative_id')->references("id")->on("order_representatives")->onDelete('cascade');
            $table->unsignedBigInteger("order_item_id");
            $table->foreign("order_item_id",'representative_items_item_id')->references("id")->on("order_items")->onDelete('cascade');
            $table->primary(["order_item_id", "order_representative_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_representatives_order_items');
    }
};
