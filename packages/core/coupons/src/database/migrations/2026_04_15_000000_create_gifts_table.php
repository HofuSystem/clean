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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'not-active'])->default('active');
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->string('coupon_code')->nullable();
            $table->enum('order_type', ['clothes', 'sales', 'service', 'maid', 'host'])->nullable();
            $table->date('register_from')->nullable();
            $table->date('register_to')->nullable();
            $table->date('orders_from')->nullable();
            $table->date('orders_to')->nullable();
            $table->integer('orders_min')->nullable();
            $table->integer('orders_max')->nullable();
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();
            $table->unsignedBigInteger('updater_id')->nullable();
            $table->foreign('updater_id')->references('id')->on('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('gift_translations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('intro')->nullable();
            $table->string('locale')->index();
            $table->unsignedBigInteger('gift_id');
            $table->unique(['gift_id', 'locale']);
            $table->foreign('gift_id')->references('id')->on('gifts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gift_translations');
        Schema::dropIfExists('gifts');
    }
};
