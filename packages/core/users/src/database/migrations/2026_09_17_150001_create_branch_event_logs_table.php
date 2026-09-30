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
        Schema::create('branch_event_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique()->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('event_name');
            $table->timestamp('event_at')->nullable();
            $table->boolean('is_attributed')->default(false);
            $table->string('campaign_id')->nullable();
            $table->string('ad_set_id')->nullable();
            $table->string('ad_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->decimal('revenue', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('coupon_code')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_event_logs');
    }
};
