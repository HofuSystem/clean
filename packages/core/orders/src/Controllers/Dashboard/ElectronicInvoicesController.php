<?php

namespace Core\Orders\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Orders\Models\Invoice;
use Core\Orders\Services\InvoiceService;
use Core\Orders\Services\ZatcaService;
use Illuminate\Http\Request;

class ElectronicInvoicesController extends Controller
{
    protected $invoiceService;
    protected $zatcaService;

    public function __construct(InvoiceService $invoiceService, ZatcaService $zatcaService)
    {
        $this->invoiceService = $invoiceService;
        $this->zatcaService = $zatcaService;
    }

    /**
     * Display the specified invoice.
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $invoice = Invoice::with(['order.items.product', 'order.client'])->findOrFail($id);
        
        $qrCodeImage = $this->zatcaService->generateQrCode($invoice->qr_code);
        
        $title = trans('Electronic Invoice') . ' - ' . $invoice->invoice_number;
        $screen = 'electronic-invoices-show';

        return view('orders::pages.electronic-invoices.show', compact('invoice', 'qrCodeImage', 'title', 'screen'));
    }

    /**
     * Download the invoice PDF.
     * 
     * @param int $id
     * @return mixed
     */
    public function downloadPdf($id)
    {
        $invoice = Invoice::findOrFail($id);
        return $this->invoiceService->downloadPdf($invoice);
    }
    /**
     * Generate or Get the electronic invoice for an order.
     * 
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate($orderId)
    {
        $order = \Core\Orders\Models\Order::findOrFail($orderId);
        $invoice = $this->invoiceService->updateOrCreateInvoice($order->id);
        
        return redirect()->route('dashboard.electronic-invoices.show', $invoice->id)
                         ->with('success', trans('Taxable invoice generated successfully'));
    }
}
