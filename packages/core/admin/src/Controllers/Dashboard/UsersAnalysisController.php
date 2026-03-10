<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Admin\Services\UserAnalyticsService;
use Core\Info\Services\CitiesService;
use Illuminate\Http\Request;

class UsersAnalysisController extends Controller
{
    public function __construct(
        protected UserAnalyticsService $userAnalyticsService,
        protected CitiesService $citiesService
    ) {}

    /**
     * Display comprehensive users analysis
     */
    public function index(Request $request)
    {
        // Increase memory and time limits for large data processing
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes
        // Handle CSV export request
        if ($request->has('export_users') && $request->export_users === 'csv') {
            return $this->exportUserAnalytics($request);
        }

        $title = trans('Users Analysis');
        $screen = 'users-analysis';
        $cities = $this->citiesService->selectable('id', 'name');
        $timePeriod = (isset($request->from) or isset($request->to)) ? 
            trans('from').' '.$request->from .' - '.trans('to').' '. $request->to : null;

        // Get comprehensive user analytics
        $analytics =  $this->userAnalyticsService->getComprehensiveUserAnalytics($request);

        return view('admin::pages.users-analysis', compact(
            'title',
            'screen',
            'cities',
            'timePeriod'
        ) + $analytics);
    }

    /**
     * Export user analytics data to CSV
     */
    private function exportUserAnalytics(Request $request)
    {
        $users = $this->userAnalyticsService->getExportableUserData($request);
        
        $filename = 'users_analysis_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Full Name',
                'Email',
                'Phone',
                'Role',
                'Registration Date',
                'Profile Status',
                'Device Type',
                'Total Orders',
                'Order Status',
                'Can Receive Notifications',
                'Account Status',
                'Wallet Balance'
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user['id'],
                    $user['fullname'],
                    $user['email'],
                    $user['phone'],
                    $user['role'],
                    $user['registration_date'],
                    $user['profile_complete'],
                    $user['device_type'],
                    $user['total_orders'],
                    $user['order_status'],
                    $user['can_receive_notifications'],
                    $user['is_active'],
                    $user['wallet_balance']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
