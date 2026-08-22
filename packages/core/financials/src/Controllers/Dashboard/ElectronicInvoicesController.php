<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Financials\Models\Invoice;
use Core\Financials\Services\InvoiceService;
use Core\Financials\Services\ZatcaService;
use Core\Financials\Models\Financial;
use Core\Financials\Models\Purchase;
use Illuminate\Http\Request;
use Core\Financials\DataResources\TaxableInvoicesResource;

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
     * Display a listing of taxable invoices.
     */
    public function index(Request $request)
    {
        $title = trans('Electronic Invoices');
        $screen = 'electronic-invoices-index';

        $invoicesStats = Invoice::selectRaw("
            SUM(CASE WHEN type = 'B2B' THEN 1 ELSE 0 END) as count_b2b,
            SUM(CASE WHEN type = 'B2C' THEN 1 ELSE 0 END) as count_b2c,
            SUM(total) as total_invoices,
            SUM(vat_amount) as vat_invoices
        ")->first();

        $creditStats = Financial::where('type', 'owed')
            ->selectRaw("
                COUNT(*) as count_credit,
                SUM(amount) as total_credit
            ")->first();

        $countB2B = (int) ($invoicesStats->count_b2b ?? 0);
        $countB2C = (int) ($invoicesStats->count_b2c ?? 0);
        $countCredit = (int) ($creditStats->count_credit ?? 0);

        $totalInvoices = (float) ($invoicesStats->total_invoices ?? 0);
        $totalCredit = (float) ($creditStats->total_credit ?? 0);
        $totalIncludingTax = $totalInvoices - $totalCredit;

        $vatInvoices = (float) ($invoicesStats->vat_invoices ?? 0);
        $vatCredit = $totalCredit;
        $totalVat = $vatInvoices - $vatCredit;

        return view('financials::pages.electronic-invoices.index', compact(
            'title', 'screen', 'countB2B', 'countB2C', 'countCredit', 'totalIncludingTax', 'totalVat'
        ));
    }

    /**
     * Handle AJAX request for the datatable.
     */
    public function dataTable(Request $request)
    {
        // 1. Get Invoices
        $invoicesQuery = Invoice::with(['order.client', 'order.company'])->search();
        
        // 2. Get Financials (Credit Notes)
        $financialsQuery = Financial::with('company')->where('type', 'owed');
            
        // Apply filters to financials (manual matching of Invoice search scope)
        if ($request->has('filters.search') && !empty($request->input('filters.search'))) {
            $search = $request->input('filters.search');
            $financialsQuery->where(function($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhereHas('company', function($cq) use ($search) {
                      $cq->where('fullname', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->has('filters.from_date') && !empty($request->input('filters.from_date'))) {
            $financialsQuery->whereDate('collection_date', '>=', $request->input('filters.from_date'));
        }
        if ($request->has('filters.to_date') && !empty($request->input('filters.to_date'))) {
            $financialsQuery->whereDate('collection_date', '<=', $request->input('filters.to_date'));
        }
        
        // Type filter
        $typeFilter = $request->input('filters.type', 'all');
        
        $invoices = collect();
        if ($typeFilter === 'all' || $typeFilter === 'B2B' || $typeFilter === 'B2C') {
            $invoices = $invoicesQuery->get();
        }
        
        $financials = collect();
        if ($typeFilter === 'all' || $typeFilter === 'CREDIT') {
            $financials = $financialsQuery->get();
        }
        
        $merged = $invoices->concat($financials)->sortByDesc('created_at');
        
        $total = $merged->count();
        
        // Paging
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $pagedData = $merged->slice($start, $length)->values();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => TaxableInvoicesResource::collection($pagedData)
        ];
    }

    /**
     * Display the specified invoice.
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $invoice = Invoice::with(['order.items.product', 'order.client', 'order.company.city', 'order.company.district'])->findOrFail($id);
        
        $qrCodeImage = $this->zatcaService->generateQrCode($invoice->qr_code);
        
        $title = trans('Electronic Invoice') . ' - ' . $invoice->invoice_number;
        $screen = 'electronic-invoices-show';

        return view('financials::pages.electronic-invoices.show', compact('invoice', 'qrCodeImage', 'title', 'screen'));
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
        $invoice = $this->invoiceService->generateInvoice($order->id);
        
        return redirect()->route('dashboard.electronic-invoices.show', $invoice->id)
                         ->with('success', trans('Taxable invoice generated successfully'));
    }

    /**
     * Display the ZATCA Tax Declaration Report.
     */
    public function declaration(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $title = trans('Tax Declaration') . ' - ' . $year;
        $screen = 'electronic-invoices-declaration';

        // Helper to get totals for a date range using direct SQL aggregate queries (0 Eloquent hydration)
        $getTotals = function($startDate, $endDate) {
            $invoices = Invoice::whereBetween('filed_at', [$startDate, $endDate])
                ->selectRaw("
                    SUM(CASE WHEN type = 'B2C' THEN subtotal ELSE 0 END) as b2c_sales,
                    SUM(CASE WHEN type = 'B2C' THEN vat_amount ELSE 0 END) as b2c_vat,
                    SUM(CASE WHEN type = 'B2B' THEN subtotal ELSE 0 END) as b2b_sales,
                    SUM(CASE WHEN type = 'B2B' THEN vat_amount ELSE 0 END) as b2b_vat
                ")
                ->first();

            $b2cSales = (float) ($invoices->b2c_sales ?? 0);
            $b2cVat   = (float) ($invoices->b2c_vat ?? 0);
            $b2bSales = (float) ($invoices->b2b_sales ?? 0);
            $b2bVat   = (float) ($invoices->b2b_vat ?? 0);

            // Adjustments (Credit Notes)
            $adjAmount = (float) Financial::where('type', 'owed')
                ->whereBetween('collection_date', [$startDate, $endDate])
                ->sum('amount');
            $adjVat = $adjAmount * 0.15;

            // Purchases (Domestic Purchases)
            $purchases = Purchase::whereBetween('collection_date', [$startDate, $endDate])
                ->selectRaw("
                    SUM(value_before_tax) as purchases_amount,
                    SUM(tax_value) as purchases_vat
                ")
                ->first();

            $purchasesAmount = (float) ($purchases->purchases_amount ?? 0);
            $purchasesVat    = (float) ($purchases->purchases_vat ?? 0);

            return [
                'b2c_sales'        => $b2cSales,
                'b2c_vat'          => $b2cVat,
                'b2b_sales'        => $b2bSales,
                'b2b_vat'          => $b2bVat,
                'net_vat'          => $b2cVat + $b2bVat,
                'adj_amount'       => $adjAmount,
                'adj_vat'          => $adjVat,
                'net_sales'        => $b2cSales + $b2bSales,
                'purchases_amount' => $purchasesAmount,
                'purchases_vat'    => $purchasesVat,
            ];
        };

        $quarters = [];
        for ($q = 1; $q <= 4; $q++) {
            $start = \Carbon\Carbon::create($year, ($q - 1) * 3 + 1, 1)->startOfDay();
            $end = $start->copy()->addMonths(3)->subSecond();

            if ($start->isFuture()) {
                $quarters[$q] = null;
                continue;
            }

            $quarters[$q] = $getTotals($start, $end);
            $quarters[$q]['due_date'] = $end->format('j F');
        }

        // Summary for current quarter or last active one
        $currentQ = (int) ceil(date('n') / 3);
        $summary = $quarters[$currentQ] ?? $quarters[count(array_filter($quarters)) ?: 1];

        // System Totals
        $actualCurrentYear = date('Y');
        $actualCurrentQ = (int) ceil(date('n') / 3);

        if ($year == $actualCurrentYear && isset($quarters[$actualCurrentQ]) && $quarters[$actualCurrentQ] !== null) {
            $currentQTotals = $quarters[$actualCurrentQ];
        } else {
            $currentQStart = \Carbon\Carbon::create($actualCurrentYear, ($actualCurrentQ - 1) * 3 + 1, 1)->startOfDay();
            $currentQEnd = $currentQStart->copy()->addMonths(3)->subSecond();
            $currentQTotals = $getTotals($currentQStart, $currentQEnd);
        }

        $invoicesOverall = Invoice::selectRaw("
            SUM(vat_amount) as total_vat,
            SUM(CASE WHEN type = 'B2B' THEN subtotal ELSE 0 END) as total_b2b_sales,
            SUM(CASE WHEN type = 'B2C' THEN subtotal ELSE 0 END) as total_b2c_sales
        ")->first();

        $totalPurchasesVat = (float) Purchase::sum('tax_value');

        $systemTotals = [
            'current_quarter_vat' => ($currentQTotals['net_vat'] ?? 0) - ($currentQTotals['purchases_vat'] ?? 0),
            'total_vat_overall'   => (float) ($invoicesOverall->total_vat ?? 0) - $totalPurchasesVat,
            'total_b2b_sales'     => (float) ($invoicesOverall->total_b2b_sales ?? 0),
            'total_b2c_sales'     => (float) ($invoicesOverall->total_b2c_sales ?? 0),
        ];

        return view('financials::pages.electronic-invoices.declaration', compact(
            'title', 'screen', 'year', 'quarters', 'summary', 'systemTotals'
        ));
    }
}
