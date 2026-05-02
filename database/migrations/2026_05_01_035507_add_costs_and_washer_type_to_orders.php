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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('lab_cost', 15, 2)->default(0)->after('total_cost');
            $table->decimal('washer_cost', 15, 2)->default(0)->after('lab_cost');
            $table->enum('wash_type', ['lab', 'washer', 'mixed'])->nullable()->after('washer_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['lab_cost', 'washer_cost', 'wash_type']);
        });
    }
};
