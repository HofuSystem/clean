<?php

namespace Core\Orders\Commands;

use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Cart;
use Core\Orders\Models\Order;
use Illuminate\Console\Command;

class CartHasBeenLeftForMoreThanTenMinutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cart-has-been-left-for-more-than-ten-minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cart has been left for more than ten minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carts = Cart::whereBetween('updated_at', [now()->subMinutes(11), now()->subMinutes(10)])
        ->get();
        foreach ($carts as $cart) {
            $products = json_decode($cart->data, true);
            if(is_array($products) && count($products) > 0){
                $telegramNotificationService = new TelegramNotificationService();
                $telegramNotificationService->sendMessage("@cleanstationsupport", $telegramNotificationService->formatCartHasBeenLeftForMoreThanTenMinutesMessage($cart));
            }
          
        }
    }
}
