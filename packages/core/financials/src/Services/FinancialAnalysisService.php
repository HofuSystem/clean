<?php

namespace Core\Financials\Services;

use Core\Orders\Models\Order;
use Core\Orders\Models\OrderReport;
use Core\Orders\Models\OrderTransaction;
use Core\Wallet\Models\WalletTransaction;
use Core\Financials\Models\DailyFinancialReport;
use Core\Financials\Models\Financial;

class FinancialAnalysisService
{
    public $notValidStatuses = ['pending_payment', 'cancel_payment', 'failed_payment', 'canceled'];

    /**
     * Get detailed monthly financial analysis for a specific year, city, and company type.
     *
     * @param int|null $year
     * @param int|null $cityId
     * @param string|null $companyType
     * @return array
     */
    public function getMonthlyFinancialAnalysis($year = null, $cityId = null, $companyType = null)
    {
        $currentYear = $year ?: date('Y');
        $monthlyAnalysis = [];

        // 1. Deliveries, revenue, cost, profit grouped by month
        $deliveriesByMonth = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
            ->whereHas('orderRepresentatives', function ($query) use ($currentYear) {
                $query->where('type', 'delivery')
                    ->whereYear('date', $currentYear);
            })
            ->join('order_representatives', function($join) {
                $join->on('orders.id', '=', 'order_representatives.order_id')
                     ->where('order_representatives.type', '=', 'delivery');
            })
            ->whereYear('order_representatives.date', $currentYear)
            ->testAccounts(false)
            ->selectRaw('
                MONTH(order_representatives.date) as m,
                COUNT(DISTINCT orders.id) as orders_count,
                COALESCE(SUM(orders.total_price), 0) as total_revenue,
                COALESCE(SUM(orders.total_cost), 0) as total_cost,
                COALESCE(SUM(orders.total_price - orders.total_cost), 0) as total_profit
            ')
            ->groupBy(\Illuminate\Support\Facades\DB::raw("MONTH(order_representatives.date)"))
            ->get()
            ->keyBy('m');

        // 2. New orders count & remaining delivery grouped by month
        $newOrdersByMonth = Order::analysis($cityId, "{$currentYear}-01-01 00:00:00", "{$currentYear}-12-31 23:59:59", null, $companyType)
            ->whereNotIn('status', $this->notValidStatuses)
            ->testAccounts(false)
            ->selectRaw("
                MONTH(orders.created_at) as m,
                COUNT(*) as new_orders_count,
                COALESCE(SUM(CASE WHEN status NOT IN ('delivered', 'finished', 'canceled', 'failed_payment', 'pending_payment', 'cancel_payment') THEN total_price ELSE 0 END), 0) as remaining_delivery
            ")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("MONTH(orders.created_at)"))
            ->get()
            ->keyBy('m');

        // 3. Pickups count grouped by month
        $pickupsByMonth = Order::analysis($cityId, null, null, null, $companyType)
            ->whereNotIn('status', $this->notValidStatuses)
            ->whereHas('orderRepresentatives', function ($query) use ($currentYear) {
                $query->where('type', 'receiver')
                    ->whereYear('date', $currentYear);
            })
            ->join('order_representatives', function($join) {
                $join->on('orders.id', '=', 'order_representatives.order_id')
                     ->where('order_representatives.type', '=', 'receiver');
            })
            ->whereYear('order_representatives.date', $currentYear)
            ->testAccounts(false)
            ->selectRaw("MONTH(order_representatives.date) as m, COUNT(DISTINCT orders.id) as pickups_count")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("MONTH(order_representatives.date)"))
            ->get()
            ->keyBy('m');

        // 4. Online, Cash, and Wallet transactions grouped by month
        $txByMonth = OrderTransaction::whereIn('order_transactions.type', ['card', 'cash', 'wallet'])
            ->join('orders', 'order_transactions.order_id', '=', 'orders.id')
            ->join('order_representatives', function($join) {
                $join->on('orders.id', '=', 'order_representatives.order_id')
                     ->where('order_representatives.type', '=', 'delivery');
            })
            ->whereNull('orders.deleted_at')
            ->whereIn('orders.status', ['delivered', 'finished'])
            ->whereYear('order_representatives.date', $currentYear)
            ->when($cityId, function ($q) use ($cityId) {
                $q->where('orders.city_id', $cityId);
            })
            ->when($companyType, function ($q) use ($companyType) {
                if ($companyType == 'b2b') {
                    $q->whereNotNull('orders.company_id');
                } elseif ($companyType == 'b2c') {
                    $q->whereNull('orders.company_id');
                }
            })
            ->selectRaw("
                MONTH(order_representatives.date) as m,
                SUM(CASE WHEN order_transactions.type = 'card' THEN 1 ELSE 0 END) as online_count,
                COALESCE(SUM(CASE WHEN order_transactions.type = 'card' THEN order_transactions.amount ELSE 0 END), 0) as online_amount,
                SUM(CASE WHEN order_transactions.type = 'cash' THEN 1 ELSE 0 END) as cash_count,
                COALESCE(SUM(CASE WHEN order_transactions.type = 'cash' THEN order_transactions.amount ELSE 0 END), 0) as cash_amount,
                SUM(CASE WHEN order_transactions.type = 'wallet' AND order_transactions.amount > 0 THEN 1 ELSE 0 END) as wallet_count,
                COALESCE(SUM(CASE WHEN order_transactions.type = 'wallet' AND order_transactions.amount > 0 THEN order_transactions.amount ELSE 0 END), 0) as wallet_amount
            ")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("MONTH(order_representatives.date)"))
            ->get()
            ->keyBy('m');

        // 5. Complaints count grouped by month
        $complaintsByMonth = OrderReport::whereYear('order_reports.created_at', $currentYear)
            ->whereHas('order', function ($query) use ($cityId, $companyType) {
                $query->testAccounts(false)
                    ->when($cityId, function ($q) use ($cityId) {
                        $q->where('orders.city_id', $cityId);
                    })
                    ->when($companyType, function ($q) use ($companyType) {
                        if ($companyType == 'b2b') {
                            $q->whereNotNull('orders.company_id');
                        } elseif ($companyType == 'b2c') {
                            $q->whereNull('orders.company_id');
                        }
                    });
            })
            ->selectRaw("MONTH(order_reports.created_at) as m, COUNT(*) as complaints_count")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("MONTH(order_reports.created_at)"))
            ->get()
            ->keyBy('m');

        // 6. Compensations amount grouped by month
        $compensationsByMonth = Financial::where('type', 'owed')
            ->whereYear('collection_date', $currentYear)
            ->when($cityId, function ($query) use ($cityId) {
                $query->where(function ($q) use ($cityId) {
                    $q->whereHas('user.profile', function ($sq) use ($cityId) {
                        $sq->where('city_id', $cityId);
                    })->orWhereHas('company', function ($sq) use ($cityId) {
                        $sq->where('city_id', $cityId);
                    });
                });
            })
            ->when($companyType, function ($query) use ($companyType) {
                if ($companyType == 'b2b') {
                    $query->whereNotNull('company_id');
                } elseif ($companyType == 'b2c') {
                    $query->whereNull('company_id');
                }
            })
            ->selectRaw("MONTH(collection_date) as m, COALESCE(SUM(amount), 0) as compensations_amount")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("MONTH(collection_date)"))
            ->get()
            ->keyBy('m');

        // Build array in memory from pre-calculated query aggregates
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $deliveriesByMonth->get($month);
            $newOrders = $newOrdersByMonth->get($month);
            $pickups = $pickupsByMonth->get($month);
            $tx = $txByMonth->get($month);
            $complaints = $complaintsByMonth->get($month);
            $compensations = $compensationsByMonth->get($month);

            $ordersCount = (int) ($monthData->orders_count ?? 0);
            $rawRevenue = (float) ($monthData->total_revenue ?? 0);
            $rawCost = (float) ($monthData->total_cost ?? 0);
            $rawProfit = (float) ($monthData->total_profit ?? 0);

            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            $monthAbbr = date('M', mktime(0, 0, 0, $month, 1));

            $profitPercentage = $rawRevenue > 0 ? (($rawProfit / $rawRevenue) * 100) : 0;
            $profitColor = $rawProfit >= 0 ? '#03ad03' : '#cf1a02';

            $newOrdersCount = (int) ($newOrders->new_orders_count ?? 0);
            $rawRemainingDelivery = (float) ($newOrders->remaining_delivery ?? 0);
            $pickupsCount = (int) ($pickups->pickups_count ?? 0);
            $deliveriesCount = $ordersCount;

            $onlineOpsCount = (int) ($tx->online_count ?? 0);
            $onlineOpsAmount = (float) ($tx->online_amount ?? 0);
            $cashOpsCount = (int) ($tx->cash_count ?? 0);
            $cashOpsAmount = (float) ($tx->cash_amount ?? 0);
            $walletOpsCount = (int) ($tx->wallet_count ?? 0);
            $walletOpsAmount = (float) ($tx->wallet_amount ?? 0);

            $complaintsCount = (int) ($complaints->complaints_count ?? 0);
            $compensationsAmount = (float) ($compensations->compensations_amount ?? 0);

            $rawAvgDeliveryRevenue = $ordersCount > 0 ? ($rawRevenue / $ordersCount) : 0;
            $rawOnlinePercentage = $rawRevenue > 0 ? ($onlineOpsAmount / $rawRevenue * 100) : 0;

            $monthlyAnalysis[] = [
                'month' => $month,
                'month_name' => $monthName,
                'month_abbr' => $monthAbbr,
                'orders_count' => $ordersCount,
                'total_revenue' => number_format($rawRevenue, 2),
                'total_cost' => number_format($rawCost, 2),
                'total_profit' => number_format($rawProfit, 2),
                'profit_percentage' => number_format($profitPercentage, 2),
                'profit_color' => $profitColor,

                // Raw numerical values for view calculations and footer aggregation
                'new_orders_count' => $newOrdersCount,
                'pickups_count' => $pickupsCount,
                'deliveries_count' => $deliveriesCount,
                'raw_revenue' => $rawRevenue,
                'raw_cost' => $rawCost,
                'raw_profit' => $rawProfit,
                'raw_profit_percentage' => $profitPercentage,
                'raw_avg_delivery_revenue' => $rawAvgDeliveryRevenue,
                'raw_remaining_delivery' => $rawRemainingDelivery,
                'raw_online_ops_count' => $onlineOpsCount,
                'raw_online_ops_amount' => $onlineOpsAmount,
                'raw_cash_ops_count' => $cashOpsCount,
                'raw_cash_ops_amount' => $cashOpsAmount,
                'raw_wallet_ops_count' => $walletOpsCount,
                'raw_wallet_ops_amount' => $walletOpsAmount,
                'raw_online_percentage' => $rawOnlinePercentage,
                'raw_complaints_count' => $complaintsCount,
                'raw_compensations_amount' => $compensationsAmount,
            ];
        }

        return $monthlyAnalysis;
    }

    /**
     * Get detailed daily financial analysis for a specific year, month, city, and company type.
     *
     * @param int $year
     * @param int $month
     * @param int|null $cityId
     * @param string|null $companyType
     * @return array
     */
    public function getDailyFinancialAnalysis($year, $month, $cityId = null, $companyType = null)
    {
        $daysInMonth = date('t', strtotime("$year-$month-01"));
        $dailyAnalysis = [];

        // Fetch manual records for this month
        $reports = DailyFinancialReport::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        // 1. Deliveries grouped by day
        $deliveriesByDay = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
            ->whereHas('orderRepresentatives', function ($query) use ($year, $month) {
                $query->where('type', 'delivery')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month);
            })
            ->join('order_representatives', function($join) {
                $join->on('orders.id', '=', 'order_representatives.order_id')
                     ->where('order_representatives.type', '=', 'delivery');
            })
            ->whereYear('order_representatives.date', $year)
            ->whereMonth('order_representatives.date', $month)
            ->testAccounts(false)
            ->selectRaw('
                DAY(order_representatives.date) as d,
                COUNT(DISTINCT orders.id) as orders_count,
                COALESCE(SUM(orders.total_price), 0) as total_revenue,
                COALESCE(SUM(orders.total_cost), 0) as total_cost,
                COALESCE(SUM(orders.total_price - orders.total_cost), 0) as total_profit
            ')
            ->groupBy(\Illuminate\Support\Facades\DB::raw("DAY(order_representatives.date)"))
            ->get()
            ->keyBy('d');

        // 2. New orders grouped by day
        $startDateMonth = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01 00:00:00";
        $endDateMonth = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-{$daysInMonth} 23:59:59";

        $newOrdersByDay = Order::analysis($cityId, $startDateMonth, $endDateMonth, null, $companyType)
            ->whereNotIn('status', $this->notValidStatuses)
            ->testAccounts(false)
            ->selectRaw('
                DAY(orders.created_at) as d,
                COUNT(*) as new_orders_count,
                COALESCE(SUM(total_price), 0) as new_orders_value
            ')
            ->groupBy(\Illuminate\Support\Facades\DB::raw("DAY(orders.created_at)"))
            ->get()
            ->keyBy('d');

        // 3. Pickups grouped by day
        $pickupsByDay = Order::analysis($cityId, null, null, null, $companyType)
            ->whereNotIn('status', $this->notValidStatuses)
            ->whereHas('orderRepresentatives', function ($query) use ($year, $month) {
                $query->where('type', 'receiver')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month);
            })
            ->join('order_representatives', function($join) {
                $join->on('orders.id', '=', 'order_representatives.order_id')
                     ->where('order_representatives.type', '=', 'receiver');
            })
            ->whereYear('order_representatives.date', $year)
            ->whereMonth('order_representatives.date', $month)
            ->testAccounts(false)
            ->selectRaw("DAY(order_representatives.date) as d, COUNT(DISTINCT orders.id) as pickups_count")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("DAY(order_representatives.date)"))
            ->get()
            ->keyBy('d');

        // 4. Online, Cash, and Wallet transactions grouped by day
        $txByDay = OrderTransaction::whereIn('order_transactions.type', ['card', 'cash', 'wallet'])
            ->join('orders', 'order_transactions.order_id', '=', 'orders.id')
            ->join('order_representatives', function($join) {
                $join->on('orders.id', '=', 'order_representatives.order_id')
                     ->where('order_representatives.type', '=', 'delivery');
            })
            ->whereNull('orders.deleted_at')
            ->whereIn('orders.status', ['delivered', 'finished'])
            ->whereYear('order_representatives.date', $year)
            ->whereMonth('order_representatives.date', $month)
            ->when($cityId, function ($q) use ($cityId) {
                $q->where('orders.city_id', $cityId);
            })
            ->when($companyType, function ($q) use ($companyType) {
                if ($companyType == 'b2b') {
                    $q->whereNotNull('orders.company_id');
                } elseif ($companyType == 'b2c') {
                    $q->whereNull('orders.company_id');
                }
            })
            ->selectRaw("
                DAY(order_representatives.date) as d,
                SUM(CASE WHEN order_transactions.type = 'card' THEN 1 ELSE 0 END) as online_count,
                COALESCE(SUM(CASE WHEN order_transactions.type = 'card' THEN order_transactions.amount ELSE 0 END), 0) as online_amount,
                SUM(CASE WHEN order_transactions.type = 'cash' THEN 1 ELSE 0 END) as cash_count,
                COALESCE(SUM(CASE WHEN order_transactions.type = 'cash' THEN order_transactions.amount ELSE 0 END), 0) as cash_amount,
                SUM(CASE WHEN order_transactions.type = 'wallet' AND order_transactions.amount > 0 THEN 1 ELSE 0 END) as wallet_count,
                COALESCE(SUM(CASE WHEN order_transactions.type = 'wallet' AND order_transactions.amount > 0 THEN order_transactions.amount ELSE 0 END), 0) as wallet_amount
            ")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("DAY(order_representatives.date)"))
            ->get()
            ->keyBy('d');

        // 5. Complaints grouped by day
        $complaintsByDay = OrderReport::whereYear('order_reports.created_at', $year)
            ->whereMonth('order_reports.created_at', $month)
            ->whereHas('order', function ($query) use ($cityId, $companyType) {
                $query->testAccounts(false)
                    ->when($cityId, function ($q) use ($cityId) {
                        $q->where('orders.city_id', $cityId);
                    })
                    ->when($companyType, function ($q) use ($companyType) {
                        if ($companyType == 'b2b') {
                            $q->whereNotNull('orders.company_id');
                        } elseif ($companyType == 'b2c') {
                            $q->whereNull('orders.company_id');
                        }
                    });
            })
            ->selectRaw("DAY(order_reports.created_at) as d, COUNT(*) as complaints_count")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("DAY(order_reports.created_at)"))
            ->get()
            ->keyBy('d');

        // 6. Compensations grouped by day
        $compensationsByDay = Financial::where('type', 'owed')
            ->whereYear('collection_date', $year)
            ->whereMonth('collection_date', $month)
            ->when($cityId, function ($query) use ($cityId) {
                $query->where(function ($q) use ($cityId) {
                    $q->whereHas('user.profile', function ($sq) use ($cityId) {
                        $sq->where('city_id', $cityId);
                    })->orWhereHas('company', function ($sq) use ($cityId) {
                        $sq->where('city_id', $cityId);
                    });
                });
            })
            ->when($companyType, function ($query) use ($companyType) {
                if ($companyType == 'b2b') {
                    $query->whereNotNull('company_id');
                } elseif ($companyType == 'b2c') {
                    $query->whereNull('company_id');
                }
            })
            ->selectRaw("DAY(collection_date) as d, COALESCE(SUM(amount), 0) as compensations_amount")
            ->groupBy(\Illuminate\Support\Facades\DB::raw("DAY(collection_date)"))
            ->get()
            ->keyBy('d');

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
            $dayDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . $dayStr;

            $newOrders = $newOrdersByDay->get($day);
            $pickups = $pickupsByDay->get($day);
            $dayData = $deliveriesByDay->get($day);
            $tx = $txByDay->get($day);
            $complaints = $complaintsByDay->get($day);
            $compensations = $compensationsByDay->get($day);

            $ordersCount = (int) ($dayData->orders_count ?? 0);
            $rawRevenue = (float) ($dayData->total_revenue ?? 0);
            $rawCost = (float) ($dayData->total_cost ?? 0);
            $rawProfit = (float) ($dayData->total_profit ?? 0);

            $dayName = date('l', strtotime($dayDate));
            $profitPercentage = $rawRevenue > 0 ? (($rawProfit / $rawRevenue) * 100) : 0;
            $profitColor = $rawProfit >= 0 ? '#03ad03' : '#cf1a02';

            $newOrdersCount = (int) ($newOrders->new_orders_count ?? 0);
            $newOrdersValue = (float) ($newOrders->new_orders_value ?? 0);
            $pickupsCount = (int) ($pickups->pickups_count ?? 0);

            $onlineOpsCount = (int) ($tx->online_count ?? 0);
            $onlineOpsAmount = (float) ($tx->online_amount ?? 0);
            $cashOpsCount = (int) ($tx->cash_count ?? 0);
            $cashOpsAmount = (float) ($tx->cash_amount ?? 0);
            $walletOpsCount = (int) ($tx->wallet_count ?? 0);
            $walletOpsAmount = (float) ($tx->wallet_amount ?? 0);

            $complaintsCount = (int) ($complaints->complaints_count ?? 0);
            $compensationsAmount = (float) ($compensations->compensations_amount ?? 0);

            $rawAvgDeliveryRevenue = $ordersCount > 0 ? ($rawRevenue / $ordersCount) : 0;
            $rawOnlinePercentage = $rawRevenue > 0 ? ($onlineOpsAmount / $rawRevenue * 100) : 0;

            // Merge manually entered daily reports (Ad Cost, Operating Expenses, Bank Balance, Note)
            $dayReport = $reports->get($dayDate);
            $adCost = $dayReport ? (float) $dayReport->ad_cost : 0.00;
            $operatingExpenses = $dayReport ? (float) $dayReport->operating_expenses : 0.00;
            $bankBalance = $dayReport ? (float) $dayReport->bank_balance : 0.00;
            $note = $dayReport ? $dayReport->note : '';

            $dailyAnalysis[] = [
                'date' => $dayDate,
                'day_name' => $dayName,
                'new_orders_count' => $newOrdersCount,
                'new_orders_value' => $newOrdersValue,
                'pickups_count' => $pickupsCount,
                'deliveries_count' => $ordersCount,
                'raw_revenue' => $rawRevenue,
                'raw_cost' => $rawCost,
                'raw_profit' => $rawProfit,
                'raw_profit_percentage' => $profitPercentage,
                'raw_avg_delivery_revenue' => $rawAvgDeliveryRevenue,
                'raw_online_ops_count' => $onlineOpsCount,
                'raw_online_ops_amount' => $onlineOpsAmount,
                'raw_cash_ops_count' => $cashOpsCount,
                'raw_cash_ops_amount' => $cashOpsAmount,
                'raw_wallet_ops_count' => $walletOpsCount,
                'raw_wallet_ops_amount' => $walletOpsAmount,
                'raw_online_percentage' => $rawOnlinePercentage,
                'raw_complaints_count' => $complaintsCount,
                'raw_compensations_amount' => $compensationsAmount,
                'ad_cost' => $adCost,
                'operating_expenses' => $operatingExpenses,
                'bank_balance' => $bankBalance,
                'note' => $note,
                'profit_color' => $profitColor,
            ];
        }

        return $dailyAnalysis;
    }
}
