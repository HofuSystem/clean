<?php

namespace Core\B2B\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Core\B2B\Models\B2BFinancial;
use Core\B2B\Models\Company;
use Core\B2B\Requests\B2BFinancialsRequest;
use Core\B2B\Services\B2BFinancialsService;
use Core\B2B\Exports\CompanyStatementCsvExport;
use Core\Orders\Models\Invoice;
use Core\Orders\Services\ZatcaService;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class CompanyStatementController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected B2BFinancialsService $financialsService,
        protected ZatcaService $zatcaService
        )
    {
    }

    /**
     * Show the account statement for a company.
     */
    public function show(Request $request, $companyId)
    {
        $companies = Company::underMyControl()->get(['id', 'fullname']);
        $nextOwedRefrence = $this->financialsService->getNextOwedRefrence();
        $company = Company::with(['owner', 'city', 'district', 'contracts' => function ($q) {
            $q->latest()->first();
        }])->findOrFail($companyId);

        $query = B2BFinancial::where('company_id', $companyId)->orderBy('collection_date', 'asc')->orderBy('created_at', 'asc');

       

        $financials = $query->get()->map(function ($fin) {
            return [
            'id' => $fin->id,
            'reference_id' => $fin->reference_id,
            'date' => Carbon::parse($fin->collection_date)->format('Y-m-d'),
            'note' => $fin->note,
            'type' => $fin->type, // owed or paid
            'amount' => $fin->amount,
            'attachment' => $fin->attachment,
            'source' => 'financial',
            ];
        });

        // Fetch Invoices
        $invoicesQuery = Invoice::whereHas('order', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->orderBy('filed_at', 'asc');

      
        $invoices = $invoicesQuery->get()->map(function ($inv) {
            return [
            'id' => $inv->id,
            'reference_id' => $inv->invoice_number,
            'date' => Carbon::parse($inv->filed_at)->format('Y-m-d'),
            'note' => $inv->invoice_number,
            'type' => 'invoice', // Invoices are always charges (owed)
            'amount' => $inv->total,
            'attachment' => null,
            'source' => 'invoice',
            'url' => route('dashboard.electronic-invoices.show', $inv->id),
            ];
        });

        // Merge and sort
        $merged = $financials->concat($invoices)->sortBy(function ($item) {
            return $item['date'];
        });

        // Calculate running balance
        $balance = 0;
        $totalsOwed = 0;
        $totalsPaid = 0;

        $rows = $merged->map(function ($item) use (&$balance, &$totalsOwed, &$totalsPaid) {
            if ($item['type'] === 'owed') {
                // If it's a Credit Note (compensation), it reduces debt
                $balance -= $item['amount'];
                $totalsOwed += $item['amount']; // Keep tracking total adjustments
                $debit = null;
                $credit = $item['amount'];
            }
            else {
                // Payments also reduce debt
                $balance -= $item['amount'];
                $totalsPaid += $item['amount'];
                $debit = null;
                $credit = $item['amount'];
            }

            // Wait, Invoices (orders) should INCREASE debt.
            // I need to check the source.
            if ($item['source'] === 'invoice') {
            // Re-calculating correctly:
            // Start with balance = 0.
            // If Invoice: balance += amount (Debit)
            // If Paid: balance -= amount (Credit)
            // If Owed (Credit Note): balance -= amount (Credit)
            }
            return $item;
        });

        // RE-CALCULATING BALANCE LOGIC
        $balance = 0;
        $totalsDebit = 0;
        $totalsCredit = 0;

        $rows = $merged->map(function ($item) use (&$balance, &$totalsDebit, &$totalsCredit) {
            if ($item['source'] === 'invoice') {
                $balance += $item['amount'];
                $totalsDebit += $item['amount'];
                $debit = $item['amount'];
                $credit = null;
            }
            elseif ($item['type'] === 'paid') {
                $balance -= $item['amount'];
                $totalsCredit += $item['amount'];
                $debit = null;
                $credit = $item['amount'];
            }
            else { // 'owed' = Credit Note
                $balance -= $item['amount'];
                $totalsCredit += $item['amount'];
                $debit = null;
                $credit = $item['amount'];
            }
            $item['debit'] = $debit;
            $item['credit'] = $credit;
            $item['balance'] = $balance;
            $item['date_rendered'] = $item['date'] instanceof \Carbon\Carbon ? $item['date']->format('Y-m-d') : substr($item['date'], 0, 10);
            return $item;
        });

        $totalsOwed = $totalsDebit;
        $totalsPaid = $totalsCredit;

        $contract = $company->contracts()->latest()->first();
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $title = trans('Company Statement') . ' - ' . $company->fullname;
        $screen = 'company-statement';

        return view('b2b::pages.companies.statement', compact(
            'title', 'screen', 'company', 'contract', 'companies',
            'rows', 'totalsOwed', 'totalsPaid', 'balance',
            'from_date', 'to_date', 'nextOwedRefrence'
        ));
    }

    /**
     * Store a new financial record (owed or paid) via AJAX.
     */
    /**
     * Helper to store financial record.
     */
    protected function storeFinancial(B2BFinancialsRequest $request, $companyId, $type = null)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $data['company_id'] = $companyId;
            if ($type) {
                $data['type'] = $type;
            }

            $this->financialsService->storeOrUpdate($data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Financial record saved successfully'));
        }
        catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        }
        catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    /**
     * Action to add an Owed financial record.
     */
    public function addOwed(B2BFinancialsRequest $request, $companyId)
    {
        return $this->storeFinancial($request, $companyId, 'owed');
    }

    /**
     * Action to add a Paid financial record.
     */
    public function addPaid(B2BFinancialsRequest $request, $companyId)
    {
        return $this->storeFinancial($request, $companyId, 'paid');
    }

    /**
     * Export statement as XLSX download.
     */
    public function exportExcel(Request $request, $companyId)
    {
        $filename = 'company_statement_' . $companyId . '_' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new CompanyStatementCsvExport($companyId, $request), $filename);
    }

    /**
     * Show/Print a Tax Credit Note.
     */
    public function printCreditNote($companyId, $financialId)
    {
        $financial = B2BFinancial::with('company')->findOrFail($financialId);

        if ($financial->type !== 'owed') {
            abort(404);
        }

        // Generate ZATCA QR
        $sellerName = SettingsService::getDataBaseSetting('name_ar') ?: 'CleanStation';
        $sellerVat = SettingsService::getDataBaseSetting('tax_tax_number') ?: '300000000000003';
        $timestamp = $financial->created_at->toIso8601String();
        $total = (float)$financial->amount;
        $vatAmount = $total - ($total / 1.15);

        $tlv = $this->zatcaService->generateTlvString($sellerName, $sellerVat, $timestamp, $total, $vatAmount);
        $qrCode = $this->zatcaService->generateQrCode($tlv);

        $title = trans('Tax Credit Note') . ' - ' . $financial->reference_id;

        return view('b2b::pages.companies.credit-note', compact('financial', 'qrCode', 'title'));
    }
}