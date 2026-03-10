<?php

namespace Core\Notification\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Core\Notification\Helpers\NotificationsHelper;
use Core\Users\Models\User;
use Core\General\Helpers\ToolHelper;
use Core\Logs\Helpers\LogHelper;

class SendLoggedInNotification
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
            if(isset($event->user->email)){
                $notificationHelper = new NotificationsHelper(['email']);
                $notificationHelper->emails_list = ([$event->user->email]);
                $notificationHelper->title      = trans("RM email System");
                $notificationHelper->message = "welcome";
                $notificationHelper->message = "loggedin";
                $notificationHelper->email_template = "loggedin";
                $notificationHelper->loggerMsg = $event->user->email . " has been login";
                $notificationHelper->send();
                LogHelper::register("path", "users", $event->user->email." has been logged in at ".now(), "users");
            }
            doAction("loggedIn", $event->user);
        } catch(\Exception $e) {
            if(env("APP_DEBUG")) {
                dd($e->getMessage());
            }
            LogHelper::register("path", "users", $event->user->email." has been logged in at ".now(), "users");
        }
    }
}
