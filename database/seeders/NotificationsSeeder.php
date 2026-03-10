<?php

namespace Database\Seeders;

use Core\MediaCenter\Models\Media;
use Core\Users\Models\Role;
use Core\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '512M');

        $chunkSize = 100;
        $users     = User::select('id')->get()->keyBy('id')->toArray();
        $counter   = 1200;

        DB::connection('mysql2')->table('notifications')->orderByDesc('id')->chunk($chunkSize, function ($notifications) use (&$counter, $users) {
            $notificationsData  = [];
            $notificationsUsers = [];

            foreach ($notifications as $notification) {
                $id = $counter++;

                try {
                    $notificationData = json_decode($notification->data);
                    $key = @$notificationData->key ?? null;



                    $notificationsData[] = [
                        'id'         => $id,
                        'types'      => json_encode(['apps']),
                        'for'        => 'users',
                        'for_data'   => json_encode([$notification->notifiable_id]),
                        'payload'    => json_encode($notificationData),
                        'title'      => @$notificationData?->body?->en ?? "",
                        'body'       => @$notificationData?->body?->en ?? "",
                        'sender_id'  => @$notificationData?->sender_data?->id,
                        'created_at' => @$notification->created_at,
                        'updated_at' => @$notification->updated_at,
                        'order_id'   => ($key=='order') ? @$notificationData?->order_id : null,
                    ];

                    if (isset($users[$notification->notifiable_id])) {
                        $notificationsUsers[] = [
                            'notifications_type' => 'Core\Notification\Models\Notification',
                            'notifications_id'   => $id,
                            'read_at'            => $notification->created_at,
                            'user_id'            => $notification->notifiable_id,
                        ];
                    }
                } catch (\Throwable $th) {
                    // Optional: log the error
                }
            }

            try {
                DB::transaction(function () use ($notificationsData, $notificationsUsers) {
                    DB::table('notifications')->insert($notificationsData);
                    DB::table('users_notifications')->insert($notificationsUsers);
                });
            } catch (\Throwable $th) {

                // Optional: log the error
            }
        });
    }
}
