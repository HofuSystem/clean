<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('wallet_transactions')
            ->where('type', 'deposit')
            ->whereIn('transaction_type', ['charge', 'deposit'])
            ->update(['wallet_bucket' => 'unclassified']);

        DB::table('wallet_transactions')
            ->where('type', 'deposit')
            ->where('transaction_type', 'remaining_amount')
            ->update(['wallet_bucket' => 'funded']);

        DB::table('wallet_transactions')
            ->where('type', 'deposit')
            ->whereIn('transaction_type', ['compensation_add', 'promotional_add', 'cashback', 'reward'])
            ->update(['wallet_bucket' => 'promotional']);

        DB::table('wallet_transactions as wt')
            ->where('wt.type', 'deposit')
            ->where('wt.transaction_type', 'charge')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')->from('payment_transactions as pt')
                    ->whereColumn('pt.transaction_id', 'wt.transaction_id')
                    ->whereColumn('pt.user_id', 'wt.user_id')
                    ->where('pt.for', 'wallet_charge')
                    ->where('pt.status', 'success');
            })
            ->update(['wallet_bucket' => 'funded']);
    }

    public function down(): void
    {
        // Classification is business data and must not be guessed during rollback.
    }
};
