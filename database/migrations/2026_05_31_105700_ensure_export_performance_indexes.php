<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure indexes on 'users' table
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_deleted_at_index')) {
                $table->index('deleted_at', 'users_deleted_at_index');
            }
            if (!$this->indexExists('users', 'users_deleted_at_created_at_index')) {
                $table->index(['deleted_at', 'created_at'], 'users_deleted_at_created_at_index');
            }
            if (!$this->indexExists('users', 'users_deleted_at_is_active_index')) {
                $table->index(['deleted_at', 'is_active'], 'users_deleted_at_is_active_index');
            }
        });

        // 2. Ensure indexes on 'orders' table
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->indexExists('orders', 'orders_client_id_status_index')) {
                $table->index(['client_id', 'status'], 'orders_client_id_status_index');
            }
            if (!$this->indexExists('orders', 'orders_client_id_created_at_index')) {
                $table->index(['client_id', 'created_at'], 'orders_client_id_created_at_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->indexExists('users', 'users_deleted_at_index')) {
                $table->dropIndex('users_deleted_at_index');
            }
            if ($this->indexExists('users', 'users_deleted_at_created_at_index')) {
                $table->dropIndex('users_deleted_at_created_at_index');
            }
            if ($this->indexExists('users', 'users_deleted_at_is_active_index')) {
                $table->dropIndex('users_deleted_at_is_active_index');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if ($this->indexExists('orders', 'orders_client_id_status_index')) {
                $table->dropIndex('orders_client_id_status_index');
            }
            if ($this->indexExists('orders', 'orders_client_id_created_at_index')) {
                $table->dropIndex('orders_client_id_created_at_index');
            }
        });
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
