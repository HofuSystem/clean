<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_outbox', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 191)->unique();
            $table->string('event_name', 100);
            $table->string('destination', 20);
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->json('payload');
            $table->string('status', 32)->default('pending')->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('available_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_outbox');
    }
};
