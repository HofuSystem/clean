<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Indexes on 'order_representatives' table
        if (Schema::hasTable('order_representatives')) {
            Schema::table('order_representatives', function (Blueprint $table) {
                if (!$this->indexExists('order_representatives', 'order_reps_order_id_type_index')) {
                    $table->index(['order_id', 'type'], 'order_reps_order_id_type_index');
                }
            });
        }

        // 2. Indexes on 'orders' table for dashboard filters
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!$this->indexExists('orders', 'orders_status_type_index')) {
                    $table->index(['status', 'type'], 'orders_status_type_index');
                }
                if (!$this->indexExists('orders', 'orders_status_created_at_index')) {
                    $table->index(['status', 'created_at'], 'orders_status_created_at_index');
                }
                if (!$this->indexExists('orders', 'orders_operator_id_status_index')) {
                    $table->index(['operator_id', 'status'], 'orders_operator_id_status_index');
                }
                if (!$this->indexExists('orders', 'orders_city_id_status_index')) {
                    $table->index(['city_id', 'status'], 'orders_city_id_status_index');
                }
            });
        }
        // 3. Indexes on 'routes_records' table for route analysis
        if (Schema::hasTable('routes_records')) {
            Schema::table('routes_records', function (Blueprint $table) {
                if (!$this->indexExists('routes_records', 'routes_records_created_at_index')) {
                    $table->index('created_at', 'routes_records_created_at_index');
                }
                if (!$this->indexExists('routes_records', 'routes_records_user_id_id_index')) {
                    $table->index(['user_id', 'id'], 'routes_records_user_id_id_index');
                }
                if (!$this->indexExists('routes_records', 'routes_records_end_point_index')) {
                    $table->index('end_point', 'routes_records_end_point_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('routes_records')) {
            Schema::table('routes_records', function (Blueprint $table) {
                if ($this->indexExists('routes_records', 'routes_records_created_at_index')) {
                    $table->dropIndex('routes_records_created_at_index');
                }
                if ($this->indexExists('routes_records', 'routes_records_user_id_id_index')) {
                    $table->dropIndex('routes_records_user_id_id_index');
                }
                if ($this->indexExists('routes_records', 'routes_records_end_point_index')) {
                    $table->dropIndex('routes_records_end_point_index');
                }
            });
        }

        if (Schema::hasTable('order_representatives')) {
            Schema::table('order_representatives', function (Blueprint $table) {
                if ($this->indexExists('order_representatives', 'order_reps_order_id_type_index')) {
                    $table->dropIndex('order_reps_order_id_type_index');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if ($this->indexExists('orders', 'orders_status_type_index')) {
                    $table->dropIndex('orders_status_type_index');
                }
                if ($this->indexExists('orders', 'orders_status_created_at_index')) {
                    $table->dropIndex('orders_status_created_at_index');
                }
                if ($this->indexExists('orders', 'orders_operator_id_status_index')) {
                    $table->dropIndex('orders_operator_id_status_index');
                }
                if ($this->indexExists('orders', 'orders_city_id_status_index')) {
                    $table->dropIndex('orders_city_id_status_index');
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
