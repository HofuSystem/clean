<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Notification\Models\UsersNotification;
use Core\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RemoveLastWeekJobs implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger(__CLASS__.' was running '.now());
        try {
            Notification::where('created_at', '<', now()->subWeek())->forceDelete();
            //delete all poivit table users_notifications recorder where dodset gave notificaion to point to deleted notification
            UsersNotification::whereDoesntHave('notification')->delete();
        } catch (\Throwable $th) {
            logger(__CLASS__.' was faild '.now().' '.$th->getMessage());

        }
   
    }
}
