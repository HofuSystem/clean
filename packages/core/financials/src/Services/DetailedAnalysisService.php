<?php

namespace Core\Financials\Services;

use Carbon\Carbon;
use Core\Financials\Models\FixedCost;
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
    public function getFinancialSummaryByYear($year = null, $cityId = null, $companyType = null)
    {
        $year = $year ?? date('Y');
        $monthlySummaries = [];

        // Single query for monthly delivered orders totals
        $ordersStats = Order::query()
            ->testAccounts(false)
            ->join('order_representatives', 'orders.id', '=', 'order_representatives.order_id')
            ->whereIn('order_representatives.type', $this->representativeTypes)
            ->whereYear('order_representatives.date', $year)
            ->whereIn('orders.status', $this->finishedStatuses)
            ->when($cityId, function($q) use ($cityId) {
                $q->where('orders.city_id', $cityId);
            })
            ->when($companyType, function ($q) use ($companyType) {
                if ($companyType == 'b2b') {
                    $q->whereNotNull('orders.company_id');
                } elseif ($companyType == 'b2c') {
                    $q->whereNull('orders.company_id');
                }
            })
            ->selectRaw('
                MONTH(order_representatives.date) as month,
                COALESCE(SUM(orders.total_coupon), 0) as total_discount,
                COALESCE(SUM(orders.delivery_price), 0) as total_delivery,
                COALESCE(SUM(orders.total_cost), 0) as total_cost
            ')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Single query for monthly transaction totals
        $transactionsStats = OrderTransaction::query()
            ->join('orders', 'order_transactions.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', $this->notValidStatuses)
            ->whereYear('order_transactions.created_at', $year)
            ->when($cityId, function($q) use ($cityId) {
                $q->where('orders.city_id', $cityId);
            })
            ->when($companyType, function ($q) use ($companyType) {
                if ($companyType == 'b2b') {
                    $q->whereNotNull('orders.company_id');
                } elseif ($companyType == 'b2c') {
                    $q->whereNull('orders.company_id');
                }
            })
            ->selectRaw('
                MONTH(order_transactions.created_at) as month,
                COALESCE(SUM(CASE WHEN order_transactions.amount > 0 THEN order_transactions.amount ELSE 0 END), 0) as total_coming_money,
                COALESCE(SUM(CASE WHEN order_transactions.amount < 0 THEN ABS(order_transactions.amount) ELSE 0 END), 0) as total_return_money
            ')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        for ($month = 1; $month <= 12; $month++) {
            $orderData = $ordersStats->get($month);
            $transData = $transactionsStats->get($month);

            $totalComingMoney = (float) ($transData ? $transData->total_coming_money : 0);
            $totalReturnMoney = (float) ($transData ? $transData->total_return_money : 0);
            $totalIncome = $totalComingMoney - $totalReturnMoney;

            $totalDiscount = (float) ($orderData ? $orderData->total_discount : 0);
            $totalDelivery = (float) ($orderData ? $orderData->total_delivery : 0);
            $totalProviderInvoice = (float) ($orderData ? $orderData->total_cost : 0);

            $fixedCosts = FixedCost::getTotalForMonth($year, $month);
            $netIncome = $totalIncome - $totalProviderInvoice - $fixedCosts;

            $monthlySummaries[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month, 1)->format('F'),
                'month_abbr' => Carbon::create($year, $month, 1)->format('M'),
                'total_coming_money' => $totalComingMoney,
                'total_return_money' => $totalReturnMoney,
                'total_income' => $totalIncome,
                'total_discount' => $totalDiscount,
                'total_delivery' => $totalDelivery,
                'total_cost' => $totalProviderInvoice,
                'fixed_costs' => $fixedCosts,
                'net_income' => $netIncome,
            ];
        }

        return $monthlySummaries;
    }

    /**
     * Get financial summary for a given month
     */
    private function getMonthlyFinancialSummary($year, $month, $cityId = null, $companyType = null)
    {
        // Kept for backward compatibility or simple direct calls
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

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

        if ($companyType) {
            if ($companyType == 'b2b') {
                $deliveredOrdersQuery->whereNotNull('company_id');
            } elseif ($companyType == 'b2c') {
                $deliveredOrdersQuery->whereNull('company_id');
            }
        }

        $transactionsQuery = OrderTransaction::query()
            ->whereHas('order', function($query) use ($startDate, $endDate, $companyType) {
               $query->testAccounts(false)
               ->whereNotIn('status', $this->notValidStatuses)
               ->when($companyType, function ($q) use ($companyType) {
                   if ($companyType == 'b2b') {
                       $q->whereNotNull('company_id');
                   } elseif ($companyType == 'b2c') {
                       $q->whereNull('company_id');
                   }
               });
            })
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($cityId) {
            $transactionsQuery->whereHas('order', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        $totalComingMoney = (float) $transactionsQuery->clone()->where('amount', '>', 0)->sum('amount');
        $totalReturnMoney = abs((float) $transactionsQuery->clone()->where('amount', '<', 0)->sum('amount'));
        $totalIncome = $totalComingMoney - $totalReturnMoney;

        $deliveredOrders = $deliveredOrdersQuery->select(['id', 'total_coupon', 'delivery_price', 'total_cost'])->get();
        $totalDiscount = (float) $deliveredOrders->sum('total_coupon');
        $totalDelivery = (float) $deliveredOrders->sum('delivery_price');
        $totalProviderInvoice = (float) $deliveredOrders->sum('total_cost');

        $fixedCosts = FixedCost::getTotalForMonth($year, $month);
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
            'total_cost' => $totalProviderInvoice,
            'fixed_costs' => $fixedCosts,
            'net_income' => $netIncome,
        ];
    }

    /**
     * Get financial summary for a given date range (deprecated - use getFinancialSummaryByYear)
     */
    public function getFinancialSummary($year = null, $month = null, $cityId = null, $companyType = null)
    {
        if ($month) {
            return $this->getMonthlyFinancialSummary($year ?? date('Y'), $month, $cityId, $companyType);
        }
        return $this->getFinancialSummaryByYear($year, $cityId, $companyType);
    }

    /**
     * Get transactions per city for donut chart (for entire year)
     */
    public function getTransactionsPerCity($year = null, $companyType = null)
    {
        $year = $year ?? date('Y');
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        $cityData = OrderTransaction::query()
            ->join('orders', 'order_transactions.order_id', '=', 'orders.id')
            ->leftJoin('cities', 'orders.city_id', '=', 'cities.id')
            ->leftJoin('city_translations', function($join) {
                $join->on('cities.id', '=', 'city_translations.city_id')
                     ->where('city_translations.locale', '=', app()->getLocale());
            })
            ->whereBetween('order_transactions.created_at', [$startDate, $endDate])
            ->where('order_transactions.amount', '>', 0)
            ->whereNotIn('orders.status', $this->notValidStatuses)
            ->when($companyType, function ($q) use ($companyType) {
                if ($companyType == 'b2b') {
                    $q->whereNotNull('orders.company_id');
                } elseif ($companyType == 'b2c') {
                    $q->whereNull('orders.company_id');
                }
            })
            ->selectRaw('COALESCE(city_translations.name, "Unknown") as city_name, SUM(order_transactions.amount) as total_amount')
            ->groupBy('city_name')
            ->pluck('total_amount', 'city_name')
            ->toArray();

        return [
            'labels' => array_keys($cityData),
            'data' => array_values($cityData),
        ];
    }

    /**
     * Get monthly growth comparison (transactions vs provider invoice)
     */
    public function getMonthlyGrowthComparison($year = null, $cityId = null, $companyType = null)
    {
        $year = $year ?? date('Y');
        $monthlyData = [];

        // Single query for monthly transaction sums
        $transactionsStats = OrderTransaction::query()
            ->join('orders', 'order_transactions.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', $this->notValidStatuses)
            ->whereYear('order_transactions.created_at', $year)
            ->where('order_transactions.amount', '>', 0)
            ->when($cityId, function($q) use ($cityId) {
                $q->where('orders.city_id', $cityId);
            })
            ->when($companyType, function ($q) use ($companyType) {
                if ($companyType == 'b2b') {
                    $q->whereNotNull('orders.company_id');
                } elseif ($companyType == 'b2c') {
                    $q->whereNull('orders.company_id');
                }
            })
            ->selectRaw('MONTH(order_transactions.created_at) as month, COALESCE(SUM(order_transactions.amount), 0) as total_amount')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Single query for monthly provider invoice cost sums
        $ordersStats = Order::query()
            ->testAccounts(false)
            ->join('order_representatives', 'orders.id', '=', 'order_representatives.order_id')
            ->whereIn('order_representatives.type', $this->representativeTypes)
            ->whereYear('order_representatives.date', $year)
            ->whereIn('orders.status', $this->finishedStatuses)
            ->when($cityId, function($q) use ($cityId) {
                $q->where('orders.city_id', $cityId);
            })
            ->when($companyType, function ($q) use ($companyType) {
                if ($companyType == 'b2b') {
                    $q->whereNotNull('orders.company_id');
                } elseif ($companyType == 'b2c') {
                    $q->whereNull('orders.company_id');
                }
            })
            ->selectRaw('MONTH(order_representatives.date) as month, COALESCE(SUM(orders.total_cost), 0) as total_cost')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        for ($month = 1; $month <= 12; $month++) {
            $transData = $transactionsStats->get($month);
            $orderData = $ordersStats->get($month);

            $monthlyData[] = [
                'month' => Carbon::create($year, $month, 1)->format('M'),
                'transactions' => (float) ($transData ? $transData->total_amount : 0),
                'provider_invoice' => (float) ($orderData ? $orderData->total_cost : 0),
            ];
        }

        return $monthlyData;
    }

    /**
     * Get payment method totals (for entire year)
     */
    public function getPaymentMethodTotals($year = null, $cityId = null, $companyType = null)
    {
        $year = $year ?? date('Y');
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        $transactionsQuery = OrderTransaction::query()
            ->whereHas('order', function($query) use ($companyType) {
                $query->testAccounts(false)
                ->whereNotIn('status', $this->notValidStatuses)
                ->when($companyType, function ($q) use ($companyType) {
                    if ($companyType == 'b2b') {
                        $q->whereNotNull('company_id');
                    } elseif ($companyType == 'b2c') {
                        $q->whereNull('company_id');
                    }
                });
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('amount', '>', 0);

        if ($cityId) {
            $transactionsQuery->whereHas('order', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        $paymentMethods = [
            'card' => 0,
            'cash' => 0,
            'wallet' => 0,
            'points' => 0,
        ];

        $totals = $transactionsQuery
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        foreach ($paymentMethods as $type => $val) {
            $paymentMethods[$type] = (float) ($totals[$type] ?? 0);
        }

        return $paymentMethods;
    }

    /**
     * Get order transactions with filters (for AJAX table)
     */
    public function getOrderTransactions($filters = [])
    {
        $companyType = $filters['company_type'] ?? null;
        $query = OrderTransaction::query()
            ->whereHas('order', function($query) use ($companyType) {
                $query->testAccounts(false)
                ->whereNotIn('status', $this->notValidStatuses)
                ->when($companyType, function ($q) use ($companyType) {
                    if ($companyType == 'b2b') {
                        $q->whereNotNull('company_id');
                    } elseif ($companyType == 'b2c') {
                        $q->whereNull('company_id');
                    }
                });
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
        $companyType = $filters['company_type'] ?? null;
        $query = OrderTransaction::query()
            ->whereHas('order', function($query) use ($companyType) {
                $query->testAccounts(false)
                ->whereNotIn('status', $this->notValidStatuses)
                ->when($companyType, function ($q) use ($companyType) {
                    if ($companyType == 'b2b') {
                        $q->whereNotNull('company_id');
                    } elseif ($companyType == 'b2c') {
                        $q->whereNull('company_id');
                    }
                });
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

        return $query->get();
    }
}

