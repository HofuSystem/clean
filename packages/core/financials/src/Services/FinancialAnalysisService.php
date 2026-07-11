<?php

namespace Core\Financials\Services;

use Core\Orders\Models\Order;
use Core\Orders\Models\OrderReport;
use Core\Wallet\Models\WalletTransaction;
use Core\Financials\Models\DailyFinancialReport;

class FinancialAnalysisService
{
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

        for ($month = 1; $month <= 12; $month++) {
            $startDate = $currentYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            // Deliveries query
            $monthData = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->selectRaw('
                    COUNT(*) as orders_count,
                    COALESCE(SUM(total_price), 0) as total_revenue,
                    COALESCE(SUM(total_cost), 0) as total_cost,
                    COALESCE(SUM(total_price - total_cost), 0) as total_profit
                ')
                ->first();

            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            $monthAbbr = date('M', mktime(0, 0, 0, $month, 1));

            // Calculate profit percentage
            $profitPercentage = $monthData->total_revenue > 0 ?
                (($monthData->total_profit / $monthData->total_revenue) * 100) : 0;

            // Determine profit color based on positive/negative profit
            $profitColor = $monthData->total_profit >= 0 ? '#03ad03' : '#cf1a02';

            // Additional reporting fields
            $newOrdersCount = Order::analysis($cityId, $startDate, $endDate, null, $companyType)
                ->testAccounts(false)
                ->count();

            $pickupsCount = Order::analysis($cityId, null, null, null, $companyType)
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'receiver')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->count();

            $rawRemainingDelivery = (float) Order::analysis($cityId, $startDate, $endDate, null, $companyType)
                ->whereNotIn('status', ['delivered', 'finished', 'canceled', 'failed_payment', 'pending_payment', 'cancel_payment'])
                ->testAccounts(false)
                ->sum('total_price');

            $onlineOpsCount = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'card')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->count();

            $onlineOpsAmount = (float) Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'card')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->sum('total_price');

            $cashOpsCount = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'cash')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->count();

            $cashOpsAmount = (float) Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'cash')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->sum('total_price');

            $complaintsCount = OrderReport::whereBetween('created_at', [$startDate, $endDate])
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
                ->count();

            $compensationsAmount = (float) WalletTransaction::where('transaction_type', 'compensation_add')
                ->where('status', 'accepted')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('user', function ($query) use ($cityId, $companyType) {
                    $query->when($cityId, function ($q) use ($cityId) {
                        $q->whereHas('profile', function ($pq) use ($cityId) {
                            $pq->where('city_id', $cityId);
                        });
                    })
                    ->when($companyType, function ($q) use ($companyType) {
                        if ($companyType == 'b2b') {
                            $q->whereNotNull('company_id');
                        } elseif ($companyType == 'b2c') {
                            $q->whereNull('company_id');
                        }
                    });
                })
                ->sum('amount');

            $rawRevenue = (float) $monthData->total_revenue;
            $rawCost = (float) $monthData->total_cost;
            $rawProfit = (float) $monthData->total_profit;
            $rawAvgDeliveryRevenue = $monthData->orders_count > 0 ? ($rawRevenue / $monthData->orders_count) : 0;
            $rawOnlinePercentage = $rawRevenue > 0 ? ($onlineOpsAmount / $rawRevenue * 100) : 0;

            $monthlyAnalysis[] = [
                'month' => $month,
                'month_name' => $monthName,
                'month_abbr' => $monthAbbr,
                'orders_count' => $monthData->orders_count,
                'total_revenue' => number_format($monthData->total_revenue, 2),
                'total_cost' => number_format($monthData->total_cost, 2),
                'total_profit' => number_format($monthData->total_profit, 2),
                'profit_percentage' => number_format($profitPercentage, 2),
                'profit_color' => $profitColor,

                // Raw numerical values for view calculations and footer aggregation
                'new_orders_count' => $newOrdersCount,
                'pickups_count' => $pickupsCount,
                'deliveries_count' => $monthData->orders_count,
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

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
            $dayDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . $dayStr;
            $startDate = "$dayDate 00:00:00";
            $endDate = "$dayDate 23:59:59";

            // Deliveries query
            $dayData = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->selectRaw('
                    COUNT(*) as orders_count,
                    COALESCE(SUM(total_price), 0) as total_revenue,
                    COALESCE(SUM(total_cost), 0) as total_cost,
                    COALESCE(SUM(total_price - total_cost), 0) as total_profit
                ')
                ->first();

            $dayName = date('l', strtotime($dayDate));

            // Calculate profit percentage
            $profitPercentage = $dayData->total_revenue > 0 ?
                (($dayData->total_profit / $dayData->total_revenue) * 100) : 0;

            // Determine profit color based on positive/negative profit
            $profitColor = $dayData->total_profit >= 0 ? '#03ad03' : '#cf1a02';

            // Additional reporting fields
            $newOrdersCount = Order::analysis($cityId, $startDate, $endDate, null, $companyType)
                ->testAccounts(false)
                ->count();

            // Value of New Orders
            $newOrdersValue = (float) Order::analysis($cityId, $startDate, $endDate, null, $companyType)
                ->testAccounts(false)
                ->sum('total_price');

            $pickupsCount = Order::analysis($cityId, null, null, null, $companyType)
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'receiver')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->count();

            $onlineOpsCount = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'card')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->count();

            $onlineOpsAmount = (float) Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'card')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->sum('total_price');

            $cashOpsCount = Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'cash')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->count();

            $cashOpsAmount = (float) Order::analysis($cityId, null, null, ['delivered', 'finished'], $companyType)
                ->where('pay_type', 'cash')
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->sum('total_price');

            $complaintsCount = OrderReport::whereBetween('created_at', [$startDate, $endDate])
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
                ->count();

            $compensationsAmount = (float) WalletTransaction::where('transaction_type', 'compensation_add')
                ->where('status', 'accepted')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('user', function ($query) use ($cityId, $companyType) {
                    $query->when($cityId, function ($q) use ($cityId) {
                        $q->whereHas('profile', function ($pq) use ($cityId) {
                            $pq->where('city_id', $cityId);
                        });
                    })
                    ->when($companyType, function ($q) use ($companyType) {
                        if ($companyType == 'b2b') {
                            $q->whereNotNull('company_id');
                        } elseif ($companyType == 'b2c') {
                            $q->whereNull('company_id');
                        }
                    });
                })
                ->sum('amount');

            $rawRevenue = (float) $dayData->total_revenue;
            $rawCost = (float) $dayData->total_cost;
            $rawProfit = (float) $dayData->total_profit;
            $rawAvgDeliveryRevenue = $dayData->orders_count > 0 ? ($rawRevenue / $dayData->orders_count) : 0;
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
                'deliveries_count' => $dayData->orders_count,
                'raw_revenue' => $rawRevenue,
                'raw_cost' => $rawCost,
                'raw_profit' => $rawProfit,
                'raw_profit_percentage' => $profitPercentage,
                'raw_avg_delivery_revenue' => $rawAvgDeliveryRevenue,
                'raw_online_ops_count' => $onlineOpsCount,
                'raw_online_ops_amount' => $onlineOpsAmount,
                'raw_cash_ops_count' => $cashOpsCount,
                'raw_cash_ops_amount' => $cashOpsAmount,
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
