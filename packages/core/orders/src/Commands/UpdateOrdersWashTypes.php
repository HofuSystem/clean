<?php

namespace Core\Orders\Commands;

use Core\Orders\Models\Invoice;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderItem;
use Core\Products\Models\Product;
use Illuminate\Console\Command;

class UpdateOrdersWashTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-wash-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update orders with lab_cost, washer_cost, and wash_type from invoice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(10000);
        ini_set('memory_limit', '512M');
        Product::query()->update([
            'wash_type' => 'washer'
        ]);
        Product::whereHas('category', function ($query) {
            $query->whereTranslationLike('name', 'b2b');
        })->update([
            'wash_type' => 'lab'
        ]);
        OrderItem::query()->update([
            'wash_type' => 'washer'
        ]);
        OrderItem::whereHas('product', function ($query) {
            $query->whereHas('category', function ($query) {
                $query->whereTranslationLike('name', 'b2b');
            });
        })->update([
            'wash_type' => 'lab'
        ]);
        Order::query()->update([
            'wash_type' => 'washer'
        ]);
        Order::whereNotNull('company_id')->update([
            'wash_type' => 'lab'
        ]);

       Order::where('total_provider_invoice','>', 0)
       ->whereNull('lab_cost')
       ->whereNull('washer_cost')
       ->each(function($order){
            if(isset($order->company_id)){
                $order->update([
                    'lab_cost' => $order->total_provider_invoice,
                    'total_cost' => $order->total_provider_invoice,
                ]);
            }else{
                $order->update([
                    'washer_cost' => $order->total_provider_invoice,
                    'total_cost' => $order->total_provider_invoice,
                ]);
            }
       });
    }
}
