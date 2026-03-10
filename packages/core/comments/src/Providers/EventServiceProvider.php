<?php

namespace Core\Comments\Providers;


use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Core\General\Helpers\Hooks\HooksManger;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

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
