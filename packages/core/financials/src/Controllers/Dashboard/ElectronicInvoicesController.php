<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Financials\Models\Invoice;
use Core\Financials\Services\InvoiceService;
use Core\Financials\Services\ZatcaService;
use Core\Financials\Models\Financial;
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

        // Summary counts (for cards) - Approximate global totals
        $countB2B = Invoice::where('type', 'B2B')->count();
        $countB2C = Invoice::where('type', 'B2C')->count();
        $countCredit = Financial::where('type', 'owed')->count();
        
        $totalInvoices = Invoice::sum('total');
        $totalCredit = Financial::where('type', 'owed')->sum('amount');
        
        $totalIncludingTax = $totalInvoices - $totalCredit;
        
        $vatInvoices = Invoice::sum('vat_amount');
        $vatCredit = Financial::where('type', 'owed')->get()->sum('amount');
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

        // Helper to get totals for a date range
        $getTotals = function($startDate, $endDate) {
            $invoices = Invoice::whereBetween('filed_at', [$startDate, $endDate])->get();
            
            $b2c = $invoices->where('type', 'B2C');
            $b2b = $invoices->where('type', 'B2B');
            
            // Adjustments (Credit Notes)
            $adjustments = Financial::where('type', 'owed')
                ->whereBetween('collection_date', [$startDate, $endDate])
                ->get();
            
            $b2cSales = $b2c->sum('subtotal');
            $b2cVat = $b2c->sum('vat_amount');
            
            $b2bSales = $b2b->sum('subtotal');
            $b2bVat = $b2b->sum('vat_amount');
            
            $adjAmount = $adjustments->sum('amount');
            $adjVat = $adjAmount * 0.15;
        
            
            return [
                'b2c_sales' => $b2cSales,
                'b2c_vat' => $b2cVat,
                'b2b_sales' => $b2bSales,
                'b2b_vat' => $b2bVat,
                'net_vat' => $b2cVat + $b2bVat,
                'adj_amount' => $adjAmount,
                'adj_vat' => $adjVat,
                'net_sales' => $b2cSales + $b2bSales,
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
        $currentQ = ceil(date('n') / 3);
        $summary = $quarters[$currentQ] ?? $quarters[count(array_filter($quarters)) ?: 1];

        // System Totals
        $actualCurrentYear = date('Y');
        $actualCurrentQ = ceil(date('n') / 3);
        $currentQStart = \Carbon\Carbon::create($actualCurrentYear, ($actualCurrentQ - 1) * 3 + 1, 1)->startOfDay();
        $currentQEnd = $currentQStart->copy()->addMonths(3)->subSecond();
        
        $currentQTotals = $getTotals($currentQStart, $currentQEnd);
        $systemTotals = [
            'current_quarter_vat' => $currentQTotals['net_vat'],
            'total_vat_overall' => Invoice::sum('vat_amount'),
            'total_b2b_sales' => Invoice::where('type', 'B2B')->sum('subtotal'),
            'total_b2c_sales' => Invoice::where('type', 'B2C')->sum('subtotal'),
        ];

        return view('financials::pages.electronic-invoices.declaration', compact(
            'title', 'screen', 'year', 'quarters', 'summary', 'systemTotals'
        ));
    }
}
