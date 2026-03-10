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
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('action_type'); // e.g., 'status_changed', 'pay_type_changed', 'item_added', etc.
            $table->text('notes'); // Description of the change
            $table->json('changed_by')->nullable(); // JSON: {user_id, name, email, phone}
            $table->json('old_value')->nullable(); // The old value(s) before change
            $table->json('new_value')->nullable(); // The new value(s) after change
            $table->timestamps();
            
            $table->index(['order_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};
