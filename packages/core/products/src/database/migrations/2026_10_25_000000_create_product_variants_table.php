<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedSmallInteger('pieces_count')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->decimal('points', 10, 2)->default(0);
            $table->decimal('extra_piece_price', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->enum('status', ['active', 'not-active'])->default('active');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['product_id', 'status', 'sort_order']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')
                ->constrained('product_variants')->nullOnDelete();
            $table->unsignedSmallInteger('extra_pieces_count')->default(0)->after('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
            $table->dropColumn('extra_pieces_count');
        });
        Schema::dropIfExists('product_variants');
    }
};
