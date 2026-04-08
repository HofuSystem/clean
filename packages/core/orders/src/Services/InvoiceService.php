<?php

namespace Core\Orders\Services;

use Core\Orders\Models\Order;
use Core\Orders\Models\Invoice;
use Core\Settings\Services\SettingsService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    protected $zatcaService;

    public function __construct(ZatcaService $zatcaService)
    {
        $this->zatcaService = $zatcaService;
    }


    /**
     * Update or Create invoice for an order.
     * 
     * @param int $orderId
     * @return Invoice|null
     */
    public function updateOrCreateInvoice(int $orderId): ?Invoice
    {
        $order = Order::find($orderId);
        if (!$order) {
            return null;
        }

        // 0. Only generate for delivered or finished orders
        if (!in_array($order->status, ['delivered', 'finished'])) {
            // Remove existing invoice if status is not delivered or finished
            Invoice::where('order_id', $order->id)->delete();
            return null;
        }

        // 1. Calculate values based on items
        $subtotal = 0;
        $vatAmount = 0;
        $vatRate = 0.15;

        foreach ($order->items as $item) {
            $itemTotal = (float)$item->total_price;
            $itemSub = $itemTotal / (1 + $vatRate);
            $itemVat = $itemTotal - $itemSub;

            $subtotal += $itemSub;
            $vatAmount += $itemVat;
        }

      
        $total = $subtotal + $vatAmount;

        // 2. Identify Type (B2B/B2C)
        $taxNumber = $this->getCustomerTaxNumber($order);
        $type = $taxNumber ? 'B2B' : 'B2C';

        // 3. Generate Invoice Number (INV-XXXX) IF NEW
        $existing = Invoice::where('order_id', $order->id)->first();
        $invoiceNumber = $existing ? $existing->invoice_number : 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

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
                'type'           => $type,
                'subtotal'       => $subtotal,
                'vat_amount'     => $vatAmount,
                'total'          => $total,
                'qr_code'        => $tlvBase64,
            ]
        );
    }

    /**
     * Download or stream the invoice PDF.
     * 
     * @param Invoice $invoice
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
            'taxNumber' => $taxNumber
        ]);

        return $pdf->download($invoice->invoice_number . '.pdf');
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
