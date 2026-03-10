<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:delete_old_week_notification')->weekly();
Schedule::command('app:send-inactive-new-users-notification')->dailyAt('10:00');
Schedule::command('app:send-abandoned-cart-notification')->everyFiveMinutes();
Schedule::command('app:send-inactive-after-order-notification')->dailyAt('11:00');
Schedule::command('app:send-feed-back')->hourly();
Schedule::command('app:handle-expired-wallet-transaction')->dailyAt('00:00');
Schedule::command('app:handle-expired-points')->dailyAt('00:00');
Schedule::command('app:handle-expired-contract')->dailyAt('23:59');
Schedule::command('app:order-is-pending-payment-for-ten-minutes')->everyMinute();

Schedule::command('app:order-starts-pickup-in-one-hour')->everyMinute();
Schedule::command('app:order-starts-delivery-in-one-hour')->everyMinute();
Schedule::command('app:order-is-late-for-pickup')->everyMinute();
Schedule::command('app:order-is-late-for-delivery')->everyMinute();

Schedule::command('app:cart-has-been-left-for-more-than-ten-minutes')->everyMinute();
Schedule::command('app:short-performance-report')->dailyAt('23:59');
Schedule::command('app:daily-summary-off-added-or-removed-items')->dailyAt('23:59');
Schedule::command('app:send-celebrate-birthday-notification')->dailyAt('15:00');
Schedule::command('app:send-no-order-period-notification')->dailyAt('12:00');
Schedule::command('app:create-scheduled-orders')->dailyAt('18:00');

//queue
Schedule::command('queue:work --stop-when-empty')->everyMinute();
Schedule::command('queue:restart')->everyFiveMinutes();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
