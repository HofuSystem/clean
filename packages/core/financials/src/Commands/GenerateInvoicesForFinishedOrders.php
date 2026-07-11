<?php

namespace Core\Financials\Commands;

use Core\Orders\Models\Order;
use Core\Financials\Services\InvoiceService;
use Illuminate\Console\Command;

class GenerateInvoicesForFinishedOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:generate-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for finished or delivered orders that do not have one';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService)
    {
        $orders = Order::whereIn('status', ['delivered', 'finished'])
            ->whereDoesntHave('invoice')
            ->get();

        $this->info("Found " . $orders->count() . " orders needing invoices.");

        foreach ($orders as $order) {
            $this->comment("Generating invoice for Order ID: {$order->id} (Ref: {$order->reference_id})...");
            try {
                $delivaryDate = $order->orderRepresentatives->where('type', 'delivery')->first()?->date;
                if(!$delivaryDate){
                    $delivaryDate = $order->orderRepresentatives()->whereNotNull('date')
                    ->whereNot('type','technical')
                    ->latest()->first()?->date;
                }
                if ($delivaryDate) {
                    $delivaryDate = \Carbon\Carbon::parse($delivaryDate);
                    $invoice = $invoiceService->generateInvoice($order->id, $delivaryDate);
                    if ($invoice) {
                        $this->info("Success: Invoice {$invoice->invoice_number} created.");
                        usleep(250000); // 0.25 seconds sleep to allow DB to register
                    } else {
                        $this->error("Failed to generate invoice for Order ID: {$order->id}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error generating invoice for Order ID: {$order->id}: " . $e->getMessage());
            }
        }

        $this->info("Done.");
    }
}
