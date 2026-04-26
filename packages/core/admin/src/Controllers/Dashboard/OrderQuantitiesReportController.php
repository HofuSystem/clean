<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Admin\Services\OrderReportService;
use Core\B2B\Models\Company;
use Core\Orders\Models\Order;
use Core\Users\Services\UsersService;
use Core\Users\Models\User;

use Core\Admin\Exports\OrderQuantitiesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


use Illuminate\Support\Facades\DB;

class OrderQuantitiesReportController extends Controller
{
    public function __construct(
        protected OrderReportService $orderReportService,
        protected UsersService $usersService
    ) {}

    public function index(Request $request)
    {
        $title = trans('Order Quantities Report');
        $screen = 'order-quantities-report';

        // Get filter options (only load what's needed)
        $selectedCompany = null;
        if ($request->filled('company_id')) {
            $selectedCompany = Company::find($request->company_id);
        }

        $selectedClient = null;
        if ($request->filled('client_id')) {
            $selectedClient = User::find($request->client_id);
        }


        $statuses = Order::groupBy('status')->select('status')->get()->pluck('status');

        $filters = [
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
            'type' => $request->get('type'),
            'company_id' => $request->get('company_id'),
            'client_id' => $request->get('client_id'),
            'status' => $request->get('status'),
        ];

        $reportData = $this->orderReportService->getOrderQuantitiesReport($filters);

        return view('admin::pages.order-quantities-report', compact(
            'title',
            'screen',
            'selectedCompany',
            'selectedClient',
            'statuses',
            'reportData',
            'filters'
        ));
    }

    public function selectCompanies(Request $request)
    {
        $q = $request->input('q');
        $companies = Company::where('fullname', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(20)
            ->get(['id', 'fullname']);

        return response()->json([
            'results' => $companies->map(function($company) {
                return [
                    'id' => $company->id,
                    'text' => $company->fullname
                ];
            })
        ]);
    }

    public function selectClients(Request $request)
    {
        $q = $request->input('q');
        $clients = User::where('fullname', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(20)
            ->get(['id', 'fullname', 'phone']);


        return response()->json([
            'results' => $clients->map(function($client) {
                return [
                    'id' => $client->id,
                    'text' => $client->fullname . ' (' . $client->phone . ')'
                ];
            })
        ]);
    }

    public function export(Request $request)

    {
        $filters = [
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
            'type' => $request->get('type'),
            'company_id' => $request->get('company_id'),
            'client_id' => $request->get('client_id'),
            'status' => $request->get('status'),
        ];

        $reportData = $this->orderReportService->getOrderQuantitiesReport($filters);

        return Excel::download(new OrderQuantitiesExport($reportData), 'order-quantities-report.xlsx');
    }
}


