<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_analytics_contexts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->unsignedSmallInteger('schema_version')->default(2);
            $table->string('app_instance_id', 255)->nullable()->index();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->string('platform', 16)->nullable();
            $table->json('branch_first')->nullable();
            $table->json('branch_latest')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_analytics_contexts');
    }
};
