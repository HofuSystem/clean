<?php

namespace Core\Users\Providers;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Core\Users\Events\UserSecSettingsUpdated;
use Core\Users\Events\UserSettingsUpdated;
use Core\Users\Listeners\LoginSuccessful;
use Core\Users\Listeners\LogoutSuccessful;
use Core\Users\Listeners\UpdateUserSettings;
use Core\Users\Listeners\UpdateUserSecSettings;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
           LoginSuccessful::class
       ],
        Logout::class => [
           LogoutSuccessful::class
       ],
       UserSecSettingsUpdated::class => [
        UpdateUserSecSettings::class
       ],
       UserSettingsUpdated::class => [
        UpdateUserSettings::class
       ]
   ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
