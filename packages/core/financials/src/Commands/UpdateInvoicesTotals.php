<?php

namespace Core\Financials\Commands;

use Core\Financials\Models\Invoice;
use Illuminate\Console\Command;
use Core\Financials\Services\InvoiceService;
class UpdateInvoicesTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoices that are not fixed';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService)
    {
        $invoices = Invoice::where('fixed', 0)->get();
        $this->info('Found ' . $invoices->count() . ' invoices to update.');

        foreach ($invoices as $invoice) {
            $invoiceService->fixInvoice($invoice->id);
            $this->comment('Updated Invoice: ' . $invoice->invoice_number);
        }

        $this->info('Done.');
    }
}
