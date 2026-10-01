<?php

use Core\Notification\Models\Notification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix historical legacy FCM push notifications where eligible users were left as pending.
     */
    public function up(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('notifications', 'sent_count')) {
                \Illuminate\Support\Facades\Schema::table('notifications', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->unsignedInteger('sent_count')->nullable()->default(0)->after('completed_at');
                });
            }

            $completedNotifications = DB::table('notifications')
                ->where('processing_status', 'completed')
                ->pluck('id')
                ->toArray();

            foreach ($completedNotifications as $notifId) {
                // Find all pending users who actually had active device tokens
                $eligibleUserIds = DB::table('users_notifications')
                    ->join('devices', 'users_notifications.user_id', '=', 'devices.user_id')
                    ->where('users_notifications.notifications_type', Notification::class)
                    ->where('users_notifications.notifications_id', $notifId)
                    ->where('users_notifications.status', 'pending')
                    ->whereNotNull('devices.device_token')
                    ->where('devices.token_status', '!=', 'invalid')
                    ->pluck('users_notifications.user_id')
                    ->unique()
                    ->toArray();

                if (!empty($eligibleUserIds)) {
                    DB::table('users_notifications')
                        ->where('notifications_type', Notification::class)
                        ->where('notifications_id', $notifId)
                        ->whereIn('user_id', $eligibleUserIds)
                        ->update([
                            'status' => 'sent',
                            'response' => 'Sent successfully via FCM'
                        ]);
                }

                $sentCount = DB::table('users_notifications')
                    ->where('notifications_type', Notification::class)
                    ->where('notifications_id', $notifId)
                    ->where('status', 'sent')
                    ->count();

                if ($sentCount > 0) {
                    $notifRow = DB::table('notifications')->where('id', $notifId)->first();
                    if ($notifRow) {
                        $payload = json_decode($notifRow->payload ?? '{}', true) ?: [];
                        $payload['sent_count'] = $sentCount;
                        $updateData = ['payload' => json_encode($payload)];
                        if (\Illuminate\Support\Facades\Schema::hasColumn('notifications', 'sent_count')) {
                            $updateData['sent_count'] = $sentCount;
                        }
                        DB::table('notifications')->where('id', $notifId)->update($updateData);
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data repair migration; no reversal needed
    }
};
