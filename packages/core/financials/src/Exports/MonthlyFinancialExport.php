<?php

namespace Core\Financials\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlyFinancialExport implements FromArray, WithHeadings
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
                trans($row['month_name']),
                $row['new_orders_count'],
                $row['pickups_count'],
                $row['deliveries_count'],
                $row['raw_revenue'],
                $row['raw_cost'],
                $row['raw_profit'],
                number_format($row['raw_profit_percentage'], 2) . '%',
                $row['raw_avg_delivery_revenue'],
                $row['raw_remaining_delivery'],
                $row['raw_online_ops_count'],
                $row['raw_online_ops_amount'],
                $row['raw_cash_ops_count'],
                $row['raw_cash_ops_amount'],
                number_format($row['raw_online_percentage'], 2) . '%',
                $row['raw_complaints_count'],
                $row['raw_compensations_amount'],
            ];
        }

        // Add a blank row and then the Totals row at the bottom
        $rows[] = [];

        $totalNewOrders = collect($this->data)->sum('new_orders_count');
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
        $totalRemaining = collect($this->data)->sum('raw_remaining_delivery');

        $totalProfitPercentage = $totalRevenue > 0 ? ($totalProfit / $totalRevenue * 100) : 0;
        $totalAvgDeliveryRevenue = $totalDeliveries > 0 ? ($totalRevenue / $totalDeliveries) : 0;
        $totalOnlinePercentage = $totalRevenue > 0 ? ($totalOnlineOpsAmount / $totalRevenue * 100) : 0;

        $rows[] = [
            trans('TOTAL'),
            $totalNewOrders,
            $totalPickups,
            $totalDeliveries,
            $totalRevenue,
            $totalCost,
            $totalProfit,
            number_format($totalProfitPercentage, 2) . '%',
            $totalAvgDeliveryRevenue,
            $totalRemaining,
            $totalOnlineOps,
            $totalOnlineOpsAmount,
            $totalCashOps,
            $totalCashOpsAmount,
            number_format($totalOnlinePercentage, 2) . '%',
            $totalComplaints,
            $totalCompensations,
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [
            trans('Month'),
            trans('New Orders'),
            trans('Pickups'),
            trans('Deliveries'),
            trans('Delivery Revenue'),
            trans('Cost of Delivered Orders'),
            trans('Gross Profit'),
            trans('Profit Margin'),
            trans('Average Delivery Revenue'),
            trans('Remaining Delivery'),
            trans('Online Operations'),
            trans('Online Amount'),
            trans('Cash Operations'),
            trans('Cash Amount'),
            trans('Online % of Revenue'),
            trans('Complaints'),
            trans('Compensations'),
        ];
    }
}
