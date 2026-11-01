<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // A failed queue batch does not undo devices already accepted by FCM.
        // Preserve the remaining token rows so an operator can inspect them.
        DB::table('notifications')
            ->where('processing_status', 'failed')
            ->where('accepted_by_fcm_count', '>', 0)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('notification_tokens')
                    ->whereColumn('notification_tokens.notification_id', 'notifications.id')
                    ->whereIn('notification_tokens.status', ['queued', 'processing']);
            })
            ->update(['processing_status' => 'partial']);
    }

    public function down(): void
    {
        // Historical delivery outcomes must not be reverted to a false failure.
    }
};
