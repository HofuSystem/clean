<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_transactions', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('user_id')->constrained('orders')->nullOnDelete();
            }
            if (! Schema::hasColumn('payment_transactions', 'stage')) {
                $table->string('stage', 50)->nullable()->after('for');
            }
            if (! Schema::hasColumn('payment_transactions', 'gateway')) {
                $table->string('gateway', 80)->nullable()->after('stage');
            }
            if (! Schema::hasColumn('payment_transactions', 'attempt_number')) {
                $table->unsignedInteger('attempt_number')->default(1)->after('gateway');
            }
            if (! Schema::hasColumn('payment_transactions', 'wallet_bucket')) {
                $table->string('wallet_bucket', 30)->nullable()->after('attempt_number');
            }
            if (! Schema::hasColumn('payment_transactions', 'fee')) {
                $table->decimal('fee', 10, 2)->nullable()->after('amount');
            }
            if (! Schema::hasColumn('payment_transactions', 'failure_reason')) {
                $table->text('failure_reason')->nullable()->after('status');
            }
            if (! Schema::hasColumn('payment_transactions', 'succeeded_at')) {
                $table->timestamp('succeeded_at')->nullable()->after('failure_reason');
            }
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->index(['order_id', 'stage', 'attempt_number'], 'payment_transactions_order_attempt_idx');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex('payment_transactions_order_attempt_idx');
            $table->dropConstrainedForeignId('order_id');
            $table->dropColumn(['stage', 'gateway', 'attempt_number', 'wallet_bucket', 'fee', 'failure_reason', 'succeeded_at']);
        });
    }
};
