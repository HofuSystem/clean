<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->decimal('value_before_tax', 10, 2)->default(0);
            $table->decimal('tax_value', 10, 2)->default(0);
            $table->decimal('value_after_tax', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('updater_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
