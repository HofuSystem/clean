<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Financials\Services\FinancialAnalysisService;
use Illuminate\Http\Request;
use Core\Info\Models\City;
use Core\Financials\Models\DailyFinancialReport;
use Maatwebsite\Excel\Facades\Excel;
use Core\Financials\Exports\MonthlyFinancialExport;
use Core\Financials\Exports\DailyFinancialExport;

class FinancialAnalysisController extends Controller
{
    protected $financialAnalysisService;

    public function __construct(FinancialAnalysisService $financialAnalysisService)
    {
        $this->financialAnalysisService = $financialAnalysisService;
    }

    public function index(Request $request)
    {
        $title = trans('Financial Analysis');
        $year = $request->get('year', date('Y'));
        $cityId = $request->get('city_id');
        $companyType = $request->get('company_type');

        // Fetch monthly analysis from service
        $monthlyAnalysis = $this->financialAnalysisService->getMonthlyFinancialAnalysis($year, $cityId, $companyType);

        $cities = City::get();

        return view('financials::pages.financial-analysis', compact(
            'title',
            'year',
            'cityId',
            'companyType',
            'monthlyAnalysis',
            'cities'
        ));
    }

    /**
     * Show daily details for a specific month
     */
    public function daily(Request $request, $year, $month)
    {
        $title = trans('Daily Financial Analysis');
        $cityId = $request->get('city_id');
        $companyType = $request->get('company_type');

        // Fetch daily analysis from service
        $dailyAnalysis = $this->financialAnalysisService->getDailyFinancialAnalysis($year, $month, $cityId, $companyType);

        $cities = City::get();
        $monthName = date('F', mktime(0, 0, 0, (int)$month, 1));

        return view('financials::pages.financial-analysis-daily', compact(
            'title',
            'year',
            'month',
            'monthName',
            'cityId',
            'companyType',
            'dailyAnalysis',
            'cities'
        ));
    }

    /**
     * Save manual daily entries (Ad Cost, Operating Expenses, Bank Balance, Note)
     */
    public function storeDaily(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'ad_cost' => 'nullable|numeric|min:0',
            'operating_expenses' => 'nullable|numeric|min:0',
            'bank_balance' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        $report = DailyFinancialReport::updateOrCreate(
            ['date' => $request->date],
            [
                'ad_cost' => $request->get('ad_cost') ?: 0.00,
                'operating_expenses' => $request->get('operating_expenses') ?: 0.00,
                'bank_balance' => $request->get('bank_balance') ?: 0.00,
                'note' => $request->get('note'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => trans('Data saved successfully'),
            'data' => $report
        ]);
    }

    /**
     * Export monthly summary to Excel
     */
    public function exportMonthly(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $cityId = $request->get('city_id');
        $companyType = $request->get('company_type');

        $monthlyAnalysis = $this->financialAnalysisService->getMonthlyFinancialAnalysis($year, $cityId, $companyType);

        $filename = "monthly-financial-analysis-{$year}.xlsx";
        return Excel::download(new MonthlyFinancialExport($monthlyAnalysis), $filename);
    }

    /**
     * Export daily summary to Excel
     */
    public function exportDaily(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $cityId = $request->get('city_id');
        $companyType = $request->get('company_type');

        $dailyAnalysis = $this->financialAnalysisService->getDailyFinancialAnalysis($year, $month, $cityId, $companyType);

        $monthName = strtolower(date('F', mktime(0, 0, 0, (int)$month, 1)));
        $filename = "daily-financial-analysis-{$year}-{$monthName}.xlsx";
        return Excel::download(new DailyFinancialExport($dailyAnalysis), $filename);
    }
}
