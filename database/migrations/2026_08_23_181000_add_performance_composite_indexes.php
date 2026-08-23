<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Indexes on 'orders' table
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!$this->indexExists('orders', 'orders_reference_id_index')) {
                    $table->index('reference_id', 'orders_reference_id_index');
                }
                if (!$this->indexExists('orders', 'orders_client_status_created_index')) {
                    $table->index(['client_id', 'status', 'created_at'], 'orders_client_status_created_index');
                }
                if (!$this->indexExists('orders', 'orders_type_status_created_index')) {
                    $table->index(['type', 'status', 'created_at'], 'orders_type_status_created_index');
                }
            });
        }

        // 2. Indexes on 'order_items' table
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!$this->indexExists('order_items', 'order_items_order_final_deleted_index')) {
                    $table->index(['order_id', 'final_delete', 'deleted_at'], 'order_items_order_final_deleted_index');
                }
            });
        }

        // 3. Indexes on 'order_representatives' table
        if (Schema::hasTable('order_representatives')) {
            Schema::table('order_representatives', function (Blueprint $table) {
                if (!$this->indexExists('order_representatives', 'order_reps_rep_type_date_time_index')) {
                    $table->index(['representative_id', 'type', 'date', 'time'], 'order_reps_rep_type_date_time_index');
                }
            });
        }

        // 4. Indexes on 'users_notifications' table
        if (Schema::hasTable('users_notifications')) {
            Schema::table('users_notifications', function (Blueprint $table) {
                if (!$this->indexExists('users_notifications', 'users_notif_user_status_vision_index')) {
                    $table->index(['user_id', 'status', 'next_vision_date'], 'users_notif_user_status_vision_index');
                }
            });
        }

        // 5. Indexes on 'wallet_transactions' table
        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                if (!$this->indexExists('wallet_transactions', 'wallet_tx_user_created_index')) {
                    $table->index(['user_id', 'created_at'], 'wallet_tx_user_created_index');
                }
                if (!$this->indexExists('wallet_transactions', 'wallet_tx_user_type_index')) {
                    $table->index(['user_id', 'type'], 'wallet_tx_user_type_index');
                }
            });
        }

        // 6. Indexes on 'banner_notifications' table
        if (Schema::hasTable('banner_notifications')) {
            Schema::table('banner_notifications', function (Blueprint $table) {
                if (!$this->indexExists('banner_notifications', 'banner_notif_dates_status_index')) {
                    $table->index(['publish_date', 'expired_date', 'status'], 'banner_notif_dates_status_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('banner_notifications')) {
            Schema::table('banner_notifications', function (Blueprint $table) {
                if ($this->indexExists('banner_notifications', 'banner_notif_dates_status_index')) {
                    $table->dropIndex('banner_notif_dates_status_index');
                }
            });
        }

        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                if ($this->indexExists('wallet_transactions', 'wallet_tx_user_created_index')) {
                    $table->dropIndex('wallet_tx_user_created_index');
                }
                if ($this->indexExists('wallet_transactions', 'wallet_tx_user_type_index')) {
                    $table->dropIndex('wallet_tx_user_type_index');
                }
            });
        }

        if (Schema::hasTable('users_notifications')) {
            Schema::table('users_notifications', function (Blueprint $table) {
                if ($this->indexExists('users_notifications', 'users_notif_user_status_vision_index')) {
                    $table->dropIndex('users_notif_user_status_vision_index');
                }
            });
        }

        if (Schema::hasTable('order_representatives')) {
            Schema::table('order_representatives', function (Blueprint $table) {
                if ($this->indexExists('order_representatives', 'order_reps_rep_type_date_time_index')) {
                    $table->dropIndex('order_reps_rep_type_date_time_index');
                }
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if ($this->indexExists('order_items', 'order_items_order_final_deleted_index')) {
                    $table->dropIndex('order_items_order_final_deleted_index');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if ($this->indexExists('orders', 'orders_reference_id_index')) {
                    $table->dropIndex('orders_reference_id_index');
                }
                if ($this->indexExists('orders', 'orders_client_status_created_index')) {
                    $table->dropIndex('orders_client_status_created_index');
                }
                if ($this->indexExists('orders', 'orders_type_status_created_index')) {
                    $table->dropIndex('orders_type_status_created_index');
                }
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return count($indexes) > 0;
    }
};
