<?php

namespace Core\Settings\Providers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Core\General\Events\SettingsUpdated;
use Core\General\Helpers\Hooks\HooksManger;
use Core\General\Listeners\UpdateGeneralSettings;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
      
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
