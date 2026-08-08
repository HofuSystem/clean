<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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
