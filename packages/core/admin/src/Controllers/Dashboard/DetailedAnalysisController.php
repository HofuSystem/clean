<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Admin\Models\FixedCost;
use Core\Admin\Services\DetailedAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Core\Info\Models\City;
use Core\Orders\Models\Order;

class DetailedAnalysisController extends Controller
{
    protected $detailedAnalysisService;

    public function __construct(DetailedAnalysisService $detailedAnalysisService)
    {
        $this->detailedAnalysisService = $detailedAnalysisService;
    }

    public function index(Request $request)
    {
        $title = trans('Detailed Yearly Financial Analysis');
        $year = $request->get('year', date('Y'));
        $cityId = $request->get('city_id');

        // Get transactions per city for donut chart (entire year)
        $cityTransactions = $this->detailedAnalysisService->getTransactionsPerCity($year);
        $notValidStatuses = $this->detailedAnalysisService->notValidStatuses;
        $finishedStatuses = $this->detailedAnalysisService->finishedStatuses;
        foreach ($notValidStatuses as $key => $notValidStatus) {
            $notValidStatuses[$key] = trans( $notValidStatus);
        }
        foreach ($finishedStatuses as $key => $finishedStatus) {
            $finishedStatuses[$key] = trans( $finishedStatus);
        }
        // Get financial summary for all 12 months
        $monthlySummaries = $this->detailedAnalysisService->getFinancialSummaryByYear($year, $cityId);

        // Get monthly growth comparison
        $monthlyGrowth = $this->detailedAnalysisService->getMonthlyGrowthComparison($year, $cityId);

        // Get payment method totals (entire year)
        $paymentMethods = $this->detailedAnalysisService->getPaymentMethodTotals($year, $cityId);

        $cities = City::get();

        return view('admin::pages.detailed-analysis', compact(
            'title',
            'monthlySummaries',
            'cityTransactions',
            'monthlyGrowth',
            'paymentMethods',
            'cities',
            'year',
            'cityId',
            'notValidStatuses',
            'finishedStatuses'
        ));
    }

    /**
     * AJAX endpoint for order transactions table
     */
    public function getOrderTransactions(Request $request)
    {
        $filters = [
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
            'reference_id' => $request->get('reference_id'),
            'phone' => $request->get('phone'),
            'city_id' => $request->get('city_id'),
            'type' => $request->get('type'),
            'per_page' => $request->get('per_page', 15),
        ];

        $transactions = $this->detailedAnalysisService->getOrderTransactions($filters);

        // Transform data for frontend
        $data = $transactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'order' => $transaction->order ? [
                    'id' => $transaction->order->id,
                    'reference_id' => $transaction->order->reference_id,
                    'edit_url' => route('dashboard.orders.edit', $transaction->order->reference_id),
                    'client' => $transaction->order->client ? [
                        'id' => $transaction->order->client->id,
                        'name' => $transaction->order->client->name ?? $transaction->order->client->fullname ?? '-',
                        'phone' => $transaction->order->client->phone ?? '-',
                        'edit_url' => route('dashboard.users.edit', $transaction->order->client->id),
                    ] : null,
                ] : null,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'created_at' => $transaction->created_at->format('d/m/Y h:iA'),
                'notes' => $transaction->notes,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }

    /**
     * Export order transactions to CSV
     */
    public function exportOrderTransactions(Request $request)
    {
        $filters = [
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
            'reference_id' => $request->get('reference_id'),
            'phone' => $request->get('phone'),
            'city_id' => $request->get('city_id'),
            'type' => $request->get('type'),
        ];

        $transactions = $this->detailedAnalysisService->getAllOrderTransactionsForExport($filters);

        $filename = 'order_transactions_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                trans('ID'),
                trans('Order Reference'),
                trans('Client Name'),
                trans('Phone Number'),
                trans('Type'),
                trans('Amount'),
                trans('Date'),
                trans('Notes')
            ]);

            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->order?->reference_id ?? '-',
                    $transaction->order?->client?->name ?? $transaction->order?->client?->fullname ?? '-',
                    $transaction->order?->client?->phone ?? '-',
                    $transaction->type,
                    number_format($transaction->amount, 2),
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->notes ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeFixedCost(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,yearly',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        FixedCost::create($request->all());

        return response()->json([
            'success' => true,
            'message' => trans('Fixed cost created successfully')
        ]);
    }

}
