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
        Schema::table('notification_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('notification_id')->index();
            $table->unsignedBigInteger('device_id')->nullable()->after('user_id')->index();
            $table->string('installation_id')->nullable()->after('device_id')->index();
            $table->string('platform')->nullable()->after('installation_id');
            $table->string('fcm_message_id')->nullable()->after('platform');
            $table->string('error_code')->nullable()->after('fcm_message_id');
            $table->text('error_message')->nullable()->after('error_code');
            $table->unsignedSmallInteger('attempts')->default(0)->after('error_message');
            $table->timestamp('queued_at')->nullable()->after('attempts');
            $table->timestamp('accepted_at')->nullable()->after('queued_at');
            $table->timestamp('failed_at')->nullable()->after('accepted_at');
            $table->timestamp('received_at')->nullable()->after('failed_at');
            $table->timestamp('opened_at')->nullable()->after('received_at');

            // Compound index for fast campaign stats
            $table->index(['notification_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_tokens', function (Blueprint $table) {
            $table->dropIndex(['notification_id', 'status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['device_id']);
            $table->dropIndex(['installation_id']);
            $table->dropColumn([
                'user_id',
                'device_id',
                'installation_id',
                'platform',
                'fcm_message_id',
                'error_code',
                'error_message',
                'attempts',
                'queued_at',
                'accepted_at',
                'failed_at',
                'received_at',
                'opened_at',
            ]);
        });
    }
};
