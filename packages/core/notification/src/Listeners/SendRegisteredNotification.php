<?php

namespace Core\Notification\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Core\Notification\Helpers\NotificationsHelper;
use Core\Users\Models\User;
use Core\General\Helpers\ToolHelper;
use Core\Logs\Helpers\LogHelper;

class SendRegisteredNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try{
            doAction("registered", $event->user);
        } catch(\Exception $e) {
            if(env("APP_DEBUG")) {
                dd($e->getMessage());
            }
            LogHelper::register("path", "users", $event->user->email." has been logged in at ".now(), "users");
        }
    }
}
