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
        Schema::create('daily_financial_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('ad_cost', 10, 2)->default(0.00);
            $table->decimal('operating_expenses', 10, 2)->default(0.00);
            $table->decimal('bank_balance', 10, 2)->default(0.00);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_financial_reports');
    }
};
