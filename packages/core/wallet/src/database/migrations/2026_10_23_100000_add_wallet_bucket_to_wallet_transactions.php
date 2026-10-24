<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('wallet_bucket', 30)->default('unclassified')->after('transaction_type')->index();
        });

        \Illuminate\Support\Facades\DB::table('wallet_transactions')
            ->where('type', 'deposit')
            ->whereIn('transaction_type', ['charge', 'deposit', 'remaining_amount'])
            ->update(['wallet_bucket' => 'funded']);

        \Illuminate\Support\Facades\DB::table('wallet_transactions')
            ->whereIn('transaction_type', ['promotional_add', 'cashback', 'reward'])
            ->update(['wallet_bucket' => 'promotional']);
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['wallet_bucket']);
            $table->dropColumn('wallet_bucket');
        });
    }
};
