<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('installation_id')->nullable()->after('user_id')->index();
            $table->string('app_context')->default('unknown')->after('installation_id');
            $table->string('token_status')->default('unknown')->after('app_context')->index();
            $table->string('notification_permission')->default('unknown')->after('token_status');
            $table->timestamp('permission_checked_at')->nullable()->after('notification_permission');
            $table->timestamp('token_refreshed_at')->nullable()->after('permission_checked_at');
            $table->timestamp('last_seen_at')->nullable()->after('token_refreshed_at');
            $table->string('app_version')->nullable()->after('last_seen_at');
            $table->string('os_version')->nullable()->after('app_version');
            $table->string('device_model')->nullable()->after('os_version');
            $table->timestamp('invalidated_at')->nullable()->after('device_model');
            $table->string('invalid_reason')->nullable()->after('invalidated_at');
            $table->timestamp('last_notification_received_at')->nullable()->after('invalid_reason');
            $table->timestamp('last_notification_opened_at')->nullable()->after('last_notification_received_at');

            // Non-unique index on device_token for performance
            $table->index('device_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex(['device_token']);
            $table->dropIndex(['installation_id']);
            $table->dropIndex(['token_status']);
            $table->dropColumn([
                'installation_id',
                'app_context',
                'token_status',
                'notification_permission',
                'permission_checked_at',
                'token_refreshed_at',
                'last_seen_at',
                'app_version',
                'os_version',
                'device_model',
                'invalidated_at',
                'invalid_reason',
                'last_notification_received_at',
                'last_notification_opened_at',
            ]);
        });
    }
};
