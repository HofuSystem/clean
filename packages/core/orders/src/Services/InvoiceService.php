<?php

namespace Core\Orders\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Core\Orders\Models\Invoice;
use Core\Orders\Models\Order;
use Core\Settings\Services\SettingsService;

class InvoiceService
{
    protected $zatcaService;

    public function __construct(ZatcaService $zatcaService)
    {
        $this->zatcaService = $zatcaService;
    }

    /**
     * Update or Create invoice for an order.
     */
    public function generateInvoice(int $orderId, $customDate = null): ?Invoice
    {
        $order = Order::find($orderId);
        if (! $order) {
            return null;
        }
        // 0. Only generate for delivered or finished orders
        if (! in_array($order->status, ['delivered', 'finished'])) {
            // Remove existing invoice if status is not delivered or finished
            Invoice::where('order_id', $order->id)->delete();

            return null;
        }

        $invoice = Invoice::where('order_id', $order->id)->first();
        if ($invoice) {
            return $invoice;
        }

        // 1. Calculate values based on items
        $subtotal = 0;
        $vatAmount = 0;
        $vatRate = 0.15;

        foreach ($order->items as $item) {
            $itemTotal = (float) $item->total_price;
            $itemSub = $itemTotal / (1 + $vatRate);
            $itemVat = $itemTotal - $itemSub;

            $subtotal += $itemSub;
            $vatAmount += $itemVat;
        }

        $total = $subtotal + $vatAmount;

        // 2. Identify Type (B2B/B2C)
        $taxNumber = $order->company;
        $type = $taxNumber ? 'B2B' : 'B2C';

        // 3. Generate Invoice Number (INV-XXXX) IF NEW
        $date = $customDate ? $customDate->format('Ymd') : date('Ymd');
        $invociePrefix = 'INV-'.$date;
        $lastInvoiceOfToday = Invoice::where('invoice_number', 'like', $invociePrefix.'%')->orderByDesc('invoice_number')->first();
        $lastInvoiceNumber = $lastInvoiceOfToday?->invoice_number;
        if ($lastInvoiceNumber) {
            $lastInvoiceNumber = explode('-', $lastInvoiceNumber);
            $lastInvoiceNumber = $lastInvoiceNumber[2];
            $invoiceOrderNumber = $lastInvoiceNumber + 1;
        } else {
            $invoiceOrderNumber = 1;
        }
        $invoiceNumber = $invociePrefix.'-'.str_pad($invoiceOrderNumber, 5, '0', STR_PAD_LEFT);
        // 4. Seller Details (from settings)
        $sellerName = SettingsService::getDataBaseSetting('name_en') ?: 'CleanStation';
        $sellerVat = SettingsService::getDataBaseSetting('clean_station_tax_number') ?: '300000000000003';

        // 5. Generate QR Code
        $timestamp = $order->created_at?->toIso8601String();
        $tlvBase64 = $this->zatcaService->generateTlvString(
            $sellerName,
            $sellerVat,
            $timestamp,
            $total,
            $vatAmount
        );

        // 6. Update or Create
        return Invoice::updateOrCreate(
            ['order_id' => $order->id],
            [
                'invoice_number' => $invoiceNumber,
                'type' => $type,
                'subtotal' => number_format($subtotal, 2),
                'vat_amount' => number_format($vatAmount, 2),
                'total' => number_format($total, 2),
                'qr_code' => $tlvBase64,
                'filed_at' => $customDate ?? now(),
            ]
        );
    }

    /**
     * Download or stream the invoice PDF.
     *
     * @return mixed
     */
    public function downloadPdf(Invoice $invoice)
    {
        $order = $invoice->order()->with(['items.product', 'client'])->first();

        $qrCodeImage = $this->zatcaService->generateQrCode($invoice->qr_code);
        $taxNumber = $this->getCustomerTaxNumber($order);

        $pdf = Pdf::loadView('orders::pdf.invoice', [
            'invoice' => $invoice,
            'order' => $order,
            'qrCodeImage' => $qrCodeImage,
            'taxNumber' => $taxNumber,
        ]);

        return $pdf->download($invoice->invoice_number.'.pdf');
    }

    /**
     * Get the customer tax number for the order.
     */
    private function getCustomerTaxNumber(Order $order): ?string
    {
        // Check if it's a B2B order and has a company with a contract that has a tax_number
        if ($order->company_id) {
            $company = $order->company;
            if ($company) {
                $contract = $company->contracts()->latest()->first();
                if ($contract && $contract->tax_number) {
                    return $contract->tax_number;
                }
            }
        }

        // Fallback for user direct
        // If there's another location for tax_number, add it here.
        return null;
    }
}
