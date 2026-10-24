<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('adjustment_type', 50);
            $table->string('reason', 80);
            $table->decimal('previous_total', 12, 2);
            $table->decimal('final_total', 12, 2);
            $table->decimal('value_delta', 12, 2);
            $table->json('before_snapshot');
            $table->json('after_snapshot');
            $table->timestamps();
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_adjustments');
    }
};
