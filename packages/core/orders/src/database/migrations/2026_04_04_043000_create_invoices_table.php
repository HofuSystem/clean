<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('invoice_number')->unique();
            $table->enum('type', ['B2C', 'B2B']);
            $table->double('subtotal')->default(0);
            $table->double('vat_amount')->default(0);
            $table->double('total')->default(0);
            $table->text('qr_code')->nullable();
            
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
