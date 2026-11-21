<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('devices')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            if (!Schema::hasIndex('devices', 'devices_last_seen_at_index')) {
                $table->index('last_seen_at', 'devices_last_seen_at_index');
            }
            if (!Schema::hasIndex('devices', 'devices_user_id_last_seen_at_index')) {
                $table->index(['user_id', 'last_seen_at'], 'devices_user_id_last_seen_at_index');
            }
            if (!Schema::hasIndex('devices', 'devices_app_version_index')) {
                $table->index('app_version', 'devices_app_version_index');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('devices')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            if (Schema::hasIndex('devices', 'devices_last_seen_at_index')) {
                $table->dropIndex('devices_last_seen_at_index');
            }
            if (Schema::hasIndex('devices', 'devices_user_id_last_seen_at_index')) {
                $table->dropIndex('devices_user_id_last_seen_at_index');
            }
            if (Schema::hasIndex('devices', 'devices_app_version_index')) {
                $table->dropIndex('devices_app_version_index');
            }
        });
    }
};
