<?php

namespace Core\Financials\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyFinancialExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data as $row) {
            $rows[] = [
                $row['date'],
                trans($row['day_name']),
                $row['new_orders_count'],
                $row['new_orders_value'],
                $row['pickups_count'],
                $row['deliveries_count'],
                $row['raw_revenue'],
                $row['raw_cost'],
                $row['raw_profit'],
                number_format($row['raw_profit_percentage'], 2) . '%',
                $row['raw_online_ops_count'],
                $row['raw_online_ops_amount'],
                $row['raw_cash_ops_count'],
                $row['raw_cash_ops_amount'],
                $row['raw_avg_delivery_revenue'],
                $row['raw_complaints_count'],
                $row['raw_compensations_amount'],
                $row['ad_cost'],
                $row['operating_expenses'],
                $row['bank_balance'],
                $row['note'],
            ];
        }

        // Add a blank row and then the Totals row at the bottom
        $rows[] = [];

        $totalNewOrders = collect($this->data)->sum('new_orders_count');
        $totalNewOrdersValue = collect($this->data)->sum('new_orders_value');
        $totalPickups = collect($this->data)->sum('pickups_count');
        $totalDeliveries = collect($this->data)->sum('deliveries_count');
        $totalRevenue = collect($this->data)->sum('raw_revenue');
        $totalCost = collect($this->data)->sum('raw_cost');
        $totalProfit = collect($this->data)->sum('raw_profit');
        $totalOnlineOps = collect($this->data)->sum('raw_online_ops_count');
        $totalOnlineOpsAmount = collect($this->data)->sum('raw_online_ops_amount');
        $totalCashOps = collect($this->data)->sum('raw_cash_ops_count');
        $totalCashOpsAmount = collect($this->data)->sum('raw_cash_ops_amount');
        $totalComplaints = collect($this->data)->sum('raw_complaints_count');
        $totalCompensations = collect($this->data)->sum('raw_compensations_amount');
        
        $totalAdCost = collect($this->data)->sum('ad_cost');
        $totalOpsExpenses = collect($this->data)->sum('operating_expenses');
        $totalBankBalance = collect($this->data)->sum('bank_balance');

        $totalProfitPercentage = $totalRevenue > 0 ? ($totalProfit / $totalRevenue * 100) : 0;
        $totalAvgDeliveryRevenue = $totalDeliveries > 0 ? ($totalRevenue / $totalDeliveries) : 0;

        $rows[] = [
            trans('TOTAL'),
            '',
            $totalNewOrders,
            $totalNewOrdersValue,
            $totalPickups,
            $totalDeliveries,
            $totalRevenue,
            $totalCost,
            $totalProfit,
            number_format($totalProfitPercentage, 2) . '%',
            $totalOnlineOps,
            $totalOnlineOpsAmount,
            $totalCashOps,
            $totalCashOpsAmount,
            $totalAvgDeliveryRevenue,
            $totalComplaints,
            $totalCompensations,
            $totalAdCost,
            $totalOpsExpenses,
            $totalBankBalance,
            '',
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [
            trans('Date'),
            trans('Day'),
            trans('New Orders'),
            trans('Value of New Orders'),
            trans('Today\'s Pickups'),
            trans('Today\'s Deliveries'),
            trans('Delivery Revenue'),
            trans('Cost of Delivered Orders'),
            trans('Gross Profit'),
            trans('Profit Margin'),
            trans('Online Operations'),
            trans('Online Amount'),
            trans('Cash Operations'),
            trans('Cash Amount'),
            trans('Average per Delivery'),
            trans('Complaints'),
            trans('Compensations'),
            trans('Ad Cost'),
            trans('Operating Expenses'),
            trans('Bank Balance'),
            trans('Note'),
        ];
    }
}
