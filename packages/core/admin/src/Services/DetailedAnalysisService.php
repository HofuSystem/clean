<?php

namespace Core\Admin\Services;

use Carbon\Carbon;
use Core\Admin\Models\FixedCost;
use Core\Info\Models\City;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;

class DetailedAnalysisService
{
    public $notValidStatuses = ['pending_payment','cancel_payment','failed_payment'];
    public $finishedStatuses = ['finished', 'delivered'];
    public $representativeTypes = ['technical', 'delivery'];
    /**
     * Get financial summary for all months in a year
     */
    public function getFinancialSummaryByYear($year = null, $cityId = null)
    {
        $year = $year ?? date('Y');
        $monthlySummaries = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlySummaries[] = $this->getMonthlyFinancialSummary($year, $month, $cityId);
        }

        return $monthlySummaries;
    }

    /**
     * Get financial summary for a given month
     */
    private function getMonthlyFinancialSummary($year, $month, $cityId = null)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Base query for delivered orders
        $deliveredOrdersQuery = Order::query()
            ->testAccounts(false)
            ->whereHas('orderRepresentatives', function($query) use ($startDate, $endDate) {
                $query->whereIn('type', $this->representativeTypes)
                ->whereBetween('date', [$startDate, $endDate]);
            })
            ->whereIn('status', $this->finishedStatuses);

        if ($cityId) {
            $deliveredOrdersQuery->where('city_id', $cityId);
        }

        // Get order transactions (all transactions in the period)
        $transactionsQuery = OrderTransaction::query()
            ->whereHas('order', function($query) use ($startDate, $endDate) {
               $query->testAccounts(false)
               ->whereNotIn('status', $this->notValidStatuses);
            })
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($cityId) {
            $transactionsQuery->whereHas('order', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        // Total coming money (transactions > 0)
        $totalComingMoney = (float) $transactionsQuery->clone()->where('amount', '>', 0)->sum('amount');

        // Total return money (transactions < 0)
        $totalReturnMoney = abs((float) $transactionsQuery->clone()->where('amount', '<', 0)->sum('amount'));

        // Total income (sum of all transactions)
        $totalIncome = $totalComingMoney - $totalReturnMoney;

        // For delivered orders
        $deliveredOrders = $deliveredOrdersQuery->get();

        // Total discount for delivered orders
        $totalDiscount = (float) $deliveredOrders->sum('total_coupon');

        // Total delivery for delivered orders
        $totalDelivery = (float) $deliveredOrders->sum('delivery_price');

        // Total provider invoice for delivered orders
        $totalProviderInvoice = (float) $deliveredOrders->sum('total_provider_invoice');

        // Fixed costs for this month
        $fixedCosts = FixedCost::getTotalForMonth($year ?? date('Y'), $month ?? date('m'));

        // Net income = Total income - Total provider invoice - Fixed costs
        $netIncome = $totalIncome - $totalProviderInvoice - $fixedCosts;

        return [
            'month' => $month,
            'month_name' => Carbon::create($year, $month, 1)->format('F'),
            'month_abbr' => Carbon::create($year, $month, 1)->format('M'),
            'total_coming_money' => $totalComingMoney,
            'total_return_money' => $totalReturnMoney,
            'total_income' => $totalIncome,
            'total_discount' => $totalDiscount,
            'total_delivery' => $totalDelivery,
            'total_provider_invoice' => $totalProviderInvoice,
            'fixed_costs' => $fixedCosts,
            'net_income' => $netIncome,
        ];
    }

    /**
     * Get financial summary for a given date range (deprecated - use getFinancialSummaryByYear)
     */
    public function getFinancialSummary($year = null, $month = null, $cityId = null)
    {
        if ($month) {
            return $this->getMonthlyFinancialSummary($year ?? date('Y'), $month, $cityId);
        }
        return $this->getFinancialSummaryByYear($year, $cityId);
    }

    /**
     * Get transactions per city for donut chart (for entire year)
     */
    public function getTransactionsPerCity($year = null)
    {
        $year = $year ?? date('Y');
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        $transactions = OrderTransaction::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('amount', '>', 0)
            ->whereHas('order', function($query) {
                $query->testAccounts(false)
                ->whereNotIn('status', $this->notValidStatuses);
            })
            ->with(['order' => function($query) {
                $query->with('city');
            }])
            ->get();

        $cityData = [];
        foreach ($transactions as $transaction) {
            if ($transaction->order) {
                $cityName = $transaction->order->city?->name ?? trans('no city');
            } else {
                $cityName = trans('Unknown');
            }
            if (!isset($cityData[$cityName])) {
                $cityData[$cityName] = 0;
            }
            $cityData[$cityName] += (float) $transaction->amount;
        }
        return [
            'labels' => array_keys($cityData),
            'data' => array_values($cityData),
        ];
    }

    /**
     * Get monthly growth comparison (transactions vs provider invoice)
     */
    public function getMonthlyGrowthComparison($year = null, $cityId = null)
    {
        $year = $year ?? date('Y');
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            // Get transactions for this month
            $transactionsQuery = OrderTransaction::query()
                ->whereHas('order', function($query) {
                    $query->testAccounts(false)
                    ->whereNotIn('status', $this->notValidStatuses);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('amount', '>', 0);

            if ($cityId) {
                $transactionsQuery->whereHas('order', function($q) use ($cityId) {
                    $q->where('city_id', $cityId);
                });
            }

            $totalTransactions = (float) $transactionsQuery->sum('amount');

            // Get provider invoice for delivered orders this month
            $ordersQuery = Order::query()
                ->testAccounts(false)
                ->whereIn('status', $this->finishedStatuses)
                ->whereHas('orderRepresentatives', function($query) use ($startDate, $endDate) {
                    $query->whereIn('type', $this->representativeTypes)
                    ->whereBetween('date', [$startDate, $endDate]);
                });

            if ($cityId) {
                $ordersQuery->where('city_id', $cityId);
            }

            $totalProviderInvoice = (float) $ordersQuery->sum('total_provider_invoice');

            $monthlyData[] = [
                'month' => Carbon::create($year, $month, 1)->format('M'),
                'transactions' => $totalTransactions,
                'provider_invoice' => $totalProviderInvoice,
            ];
        }

        return $monthlyData;
    }

    /**
     * Get payment method totals (for entire year)
     */
    public function getPaymentMethodTotals($year = null, $cityId = null)
    {
        $year = $year ?? date('Y');
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        $transactionsQuery = OrderTransaction::query()
            ->whereHas('order', function($query) {
                $query->testAccounts(false)
                ->whereNotIn('status', $this->notValidStatuses);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('amount', '>', 0);

        if ($cityId) {
            $transactionsQuery->whereHas('order', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        $transactions = $transactionsQuery->get();

        $paymentMethods = [
            'card' => 0,
            'cash' => 0,
            'wallet' => 0,
            'points' => 0,
        ];

        foreach ($transactions as $transaction) {
            $type = $transaction->type;
            if (isset($paymentMethods[$type])) {
                $paymentMethods[$type] += $transaction->amount;
            }
        }

        return $paymentMethods;
    }

    /**
     * Get order transactions with filters (for AJAX table)
     */
    public function getOrderTransactions($filters = [])
    {
        $query = OrderTransaction::query()
            ->whereHas('order', function($query) {
                $query->testAccounts(false)
                ->whereNotIn('status', $this->notValidStatuses);
            })
            ->with(['order.client', 'order.city'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['from_date']));
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['to_date']));
        }

        // Filter by order reference_id
        if (!empty($filters['reference_id'])) {
            $query->whereHas('order', function($q) use ($filters) {
                $q->where('reference_id', 'LIKE', '%' . $filters['reference_id'] . '%');
            });
        }

        // Filter by client phone number
        if (!empty($filters['phone'])) {
            $query->whereHas('order.client', function($q) use ($filters) {
                $q->where('phone', 'LIKE', '%' . $filters['phone'] . '%');
            });
        }

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->whereHas('order', function($q) use ($filters) {
                $q->where('city_id', $filters['city_id']);
            });
        }

        // Filter by transaction type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get all order transactions for export (no pagination)
     */
    public function getAllOrderTransactionsForExport($filters = [])
    {
        $query = OrderTransaction::query()
            ->with(['order.client', 'order.city'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['from_date']));
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['to_date']));
        }

        // Filter by order reference_id
        if (!empty($filters['reference_id'])) {
            $query->whereHas('order', function($q) use ($filters) {
                $q->where('reference_id', 'LIKE', '%' . $filters['reference_id'] . '%');
            });
        }

        // Filter by client phone number
        if (!empty($filters['phone'])) {
            $query->whereHas('order.client', function($q) use ($filters) {
                $q->where('phone', 'LIKE', '%' . $filters['phone'] . '%');
            });
        }

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->whereHas('order', function($q) use ($filters) {
                $q->where('city_id', $filters['city_id']);
            });
        }

        // Filter by transaction type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->get();
    }
}

