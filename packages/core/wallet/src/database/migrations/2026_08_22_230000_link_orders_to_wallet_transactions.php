<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Link from order_transactions where wallet_transaction_id is stored
        DB::statement("
            UPDATE wallet_transactions wt
            JOIN order_transactions ot ON ot.wallet_transaction_id = wt.id
            SET wt.order_id = ot.order_id
            WHERE wt.order_id IS NULL AND ot.order_id IS NOT NULL
        ");

        // 2. Link from orders where user_id and amount matches within 1 minute if still null
        DB::statement("
            UPDATE wallet_transactions wt
            JOIN orders o ON o.client_id = wt.user_id 
                AND o.wallet_amount_used = wt.amount 
                AND ABS(TIMESTAMPDIFF(MINUTE, o.created_at, wt.created_at)) <= 10
            SET wt.order_id = o.id
            WHERE wt.order_id IS NULL AND wt.transaction_type = 'order_payment'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No reverse needed
    }
};
