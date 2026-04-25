<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Critical: deleted_at index allows soft-delete filtering without full table scans
            $table->index('deleted_at', 'users_deleted_at_index');
            // Composite index for common dashboard filter: active users sorted by created_at
            $table->index(['deleted_at', 'created_at'], 'users_deleted_at_created_at_index');
            // Index for sorting/filtering by is_active
            $table->index(['deleted_at', 'is_active'], 'users_deleted_at_is_active_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Composite index for the withCount and subquery: client_id + status
            if (!$this->indexExists('orders', 'orders_client_id_status_index')) {
                $table->index(['client_id', 'status'], 'orders_client_id_status_index');
            }
            // Index for MAX(created_at) subquery
            if (!$this->indexExists('orders', 'orders_client_id_created_at_index')) {
                $table->index(['client_id', 'created_at'], 'orders_client_id_created_at_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_deleted_at_index');
            $table->dropIndex('users_deleted_at_created_at_index');
            $table->dropIndex('users_deleted_at_is_active_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_client_id_status_index');
            $table->dropIndex('orders_client_id_created_at_index');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return count($indexes) > 0;
    }
};
