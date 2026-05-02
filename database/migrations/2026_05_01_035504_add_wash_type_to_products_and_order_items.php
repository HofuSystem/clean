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
        Schema::table('products', function (Blueprint $table) {
            $table->enum('wash_type', ['lab', 'washer'])->nullable()->after('type');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('wash_type', ['lab', 'washer'])->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('wash_type');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('wash_type');
        });
    }
};
