<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_loyalty_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->unique()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->decimal('eligible_amount', 12, 2);
            $table->unsignedInteger('points_earned');
            $table->decimal('points_per_riyal', 8, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_loyalty_awards');
    }
};
