<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if(!Schema::hasColumn('invoices','subtotal')){
                $table->double('subtotal')->nullable()->after('vat_amount');
            }
            if(!Schema::hasColumn('invoices','subtotal')){
                $table->double('total_coupon')->nullable()->after('vat_amount');
            }
            if(!Schema::hasColumn('invoices','subtotal')){
                $table->double('delivery_price')->nullable()->after('vat_amount');
            }
            if(!Schema::hasColumn('invoices','subtotal')){
                $table->double('total_price')->nullable()->after('vat_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['delivery_price', 'total_coupon', 'total_price', 'subtotal']);
        });
    }
};
