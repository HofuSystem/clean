<?php

namespace Core\Orders\Jobs;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Order;
use Core\Settings\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Build and queue the new-order Telegram alert outside the create-order HTTP
 * response. Formatting counts the client's prior orders and must not delay
 * the mobile app waiting on POST /api/client/orders.
 */
class NotifyNewOrderTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 30;

    public function __construct(public int $orderId)
    {
        // Only notify after the surrounding create-order transaction commits.
        $this->afterCommit();
    }

    public function handle(TelegramNotificationService $telegram): void
    {
        $order = Order::query()
            ->with([
                'client',
                'city',
                'district',
                'items',
                'orderRepresentatives',
            ])
            ->find($this->orderId);

        if (! $order) {
            return;
        }

        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if (in_array($order->client_id, $testAccounts, false)) {
            return;
        }

        try {
            $telegram->sendMessage(
                '@cleanstationneworders',
                $telegram->formatNewOrderMessage($order)
            );
        } catch (Throwable $e) {
            // Telegram must never surface back into the order API.
            report($e);
        }
    }
}
