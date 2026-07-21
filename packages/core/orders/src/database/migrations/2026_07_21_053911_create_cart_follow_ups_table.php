<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->enum('status', ['pending', 'sale', 'no_answer', 'not_interested'])->default('pending');
            $table->string('notes')->nullable();
            $table->timestamp('followed_up_at')->nullable(); // when admin did the follow up
            $table->timestamp('order_at')->nullable();       // when user placed the order (auto-filled)
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_follow_ups');
    }
};
