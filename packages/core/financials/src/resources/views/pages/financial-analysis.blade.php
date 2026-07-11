@extends('admin::layouts.dashboard')

@section('content')
    <style>
        .modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .modern-table {
            border-radius: 8px;
            overflow: hidden;
        }
        .modern-table thead {
            background: #4a79b5;
            color: white;
        }
        .modern-table tbody tr {
            transition: background-color 0.2s;
        }
        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .filter-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .month-row {
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }
        .month-row:hover {
            border-left-color: #667eea;
            background-color: #f8f9ff;
        }
        .month-row.positive {
            border-left-color: #10b981;
        }
        .month-row.negative {
            border-left-color: #ef4444;
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('dashboard.financial-analysis') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold" for="city_id">{{ trans("city") }}</label>
                        <select class="form-select form-select-lg" name="city_id" id="city_id">
                            <option value="">{{trans("select city")}}</option>
                            @foreach($cities ?? [] as $city)
                                <option data-id="{{$city->id }}" @selected($cityId == $city->id) value="{{$city->id }}" >{{$city->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" for="year">{{ trans('Year') }}</label>
                        <select name="year" id="year" class="form-select form-select-lg">
                            @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold" for="company_type">{{ trans('type') }}</label>
                        <select name="company_type" id="company_type" class="form-select form-select-lg">
                            <option value="">{{ trans('all') }}</option>
                            <option value="b2b" @selected($companyType == 'b2b')>{{ trans('b2b') }}</option>
                            <option value="b2c" @selected($companyType == 'b2c')>{{ trans('b2c') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary btn-lg w-100" type="submit">
                            <i class="fas fa-filter me-2"></i>{{ trans('filter') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Monthly Analysis Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-table text-info me-2"></i>{{ trans('Monthly Financial Summary') }} - {{ $year }}
                        </h5>
                        <a href="{{ route('dashboard.financial-analysis.export-monthly', request()->all()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>{{ trans('Export to Excel') }}
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover modern-table mb-0" id="monthly_analysis_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ trans('Month') }}</th>
                                        <th class="text-center">{{ trans('New Orders') }}</th>
                                        <th class="text-center">{{ trans('Pickups') }}</th>
                                        <th class="text-center">{{ trans('Deliveries') }}</th>
                                        <th class="text-center">{{ trans('Delivery Revenue') }}</th>
                                        <th class="text-center">{{ trans('Cost of Delivered Orders') }}</th>
                                        <th class="text-center">{{ trans('Gross Profit') }}</th>
                                        <th class="text-center">{{ trans('Profit Margin') }}</th>
                                        <th class="text-center">{{ trans('Average Delivery Revenue') }}</th>
                                        <th class="text-center">{{ trans('Remaining Delivery') }}</th>
                                        <th class="text-center">{{ trans('Online Operations') }}</th>
                                        <th class="text-center">{{ trans('Online Amount') }}</th>
                                        <th class="text-center">{{ trans('Cash Operations') }}</th>
                                        <th class="text-center">{{ trans('Cash Amount') }}</th>
                                        <th class="text-center">{{ trans('Online % of Revenue') }}</th>
                                        <th class="text-center">{{ trans('Complaints') }}</th>
                                        <th class="text-center">{{ trans('Compensations') }}</th>
                                        <th class="text-center">{{ trans('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyAnalysis as $month)
                                        <tr class="month-row {{ $month['raw_profit'] >= 0 ? 'positive' : 'negative' }}">
                                            <td class="text-center fw-bold">
                                                <div class="d-flex flex-column">
                                                    <span>{{ trans($month['month_name']) }}</span>
                                                    <small class="text-muted">{{ trans($month['month_abbr']) }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ number_format($month['new_orders_count']) }}</td>
                                            <td class="text-center">{{ number_format($month['pickups_count']) }}</td>
                                            <td class="text-center">{{ number_format($month['deliveries_count']) }}</td>
                                            <td class="text-center text-success fw-bold">{{ number_format($month['raw_revenue'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center text-danger">{{ number_format($month['raw_cost'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center fw-bold {{ $month['raw_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($month['raw_profit'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center fw-bold" style="color: {{ $month['profit_color'] }};">
                                                {{ number_format($month['raw_profit_percentage'], 2) }}%
                                            </td>
                                            <td class="text-center">{{ number_format($month['raw_avg_delivery_revenue'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center text-warning fw-bold">{{ number_format($month['raw_remaining_delivery'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center">{{ number_format($month['raw_online_ops_count']) }}</td>
                                            <td class="text-center text-primary">{{ number_format($month['raw_online_ops_amount'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center">{{ number_format($month['raw_cash_ops_count']) }}</td>
                                            <td class="text-center text-secondary">{{ number_format($month['raw_cash_ops_amount'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center fw-bold text-info">{{ number_format($month['raw_online_percentage'], 2) }}%</td>
                                            <td class="text-center text-danger">{{ number_format($month['raw_complaints_count']) }}</td>
                                            <td class="text-center text-danger fw-bold">{{ number_format($month['raw_compensations_amount'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.financial-analysis.daily', ['year' => $year, 'month' => $month['month'], 'city_id' => $cityId, 'company_type' => $companyType]) }}" class="btn btn-sm btn-info text-white">
                                                    <i class="fas fa-eye me-1"></i>{{ trans('Daily Details') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @php
                                        $totalNewOrders = collect($monthlyAnalysis)->sum('new_orders_count');
                                        $totalPickups = collect($monthlyAnalysis)->sum('pickups_count');
                                        $totalDeliveries = collect($monthlyAnalysis)->sum('deliveries_count');
                                        $totalRevenue = collect($monthlyAnalysis)->sum('raw_revenue');
                                        $totalCost = collect($monthlyAnalysis)->sum('raw_cost');
                                        $totalProfit = collect($monthlyAnalysis)->sum('raw_profit');
                                        $totalRemaining = collect($monthlyAnalysis)->sum('raw_remaining_delivery');
                                        $totalOnlineOps = collect($monthlyAnalysis)->sum('raw_online_ops_count');
                                        $totalOnlineOpsAmount = collect($monthlyAnalysis)->sum('raw_online_ops_amount');
                                        $totalCashOps = collect($monthlyAnalysis)->sum('raw_cash_ops_count');
                                        $totalCashOpsAmount = collect($monthlyAnalysis)->sum('raw_cash_ops_amount');
                                        $totalComplaints = collect($monthlyAnalysis)->sum('raw_complaints_count');
                                        $totalCompensations = collect($monthlyAnalysis)->sum('raw_compensations_amount');
                                        
                                        $totalProfitPercentage = $totalRevenue > 0 ? ($totalProfit / $totalRevenue * 100) : 0;
                                        $totalAvgDeliveryRevenue = $totalDeliveries > 0 ? ($totalRevenue / $totalDeliveries) : 0;
                                        $totalOnlinePercentage = $totalRevenue > 0 ? ($totalOnlineOpsAmount / $totalRevenue * 100) : 0;
                                    @endphp
                                    <tr class="table-secondary fw-bold">
                                        <td class="text-center">{{ trans('TOTAL') }}</td>
                                        <td class="text-center">{{ number_format($totalNewOrders) }}</td>
                                        <td class="text-center">{{ number_format($totalPickups) }}</td>
                                        <td class="text-center">{{ number_format($totalDeliveries) }}</td>
                                        <td class="text-center text-success">{{ number_format($totalRevenue, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center text-danger">{{ number_format($totalCost, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($totalProfit, 2) }} {{ trans('SAR') }}
                                        </td>
                                        <td class="text-center fw-bold" style="color: {{ $totalProfit >= 0 ? '#03ad03' : '#cf1a02' }};">
                                            {{ number_format($totalProfitPercentage, 2) }}%
                                        </td>
                                        <td class="text-center">{{ number_format($totalAvgDeliveryRevenue, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center text-warning">{{ number_format($totalRemaining, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center">{{ number_format($totalOnlineOps) }}</td>
                                        <td class="text-center text-primary">{{ number_format($totalOnlineOpsAmount, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center">{{ number_format($totalCashOps) }}</td>
                                        <td class="text-center text-secondary">{{ number_format($totalCashOpsAmount, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center text-info">{{ number_format($totalOnlinePercentage, 2) }}%</td>
                                        <td class="text-center text-danger">{{ number_format($totalComplaints) }}</td>
                                        <td class="text-center text-danger">{{ number_format($totalCompensations, 2) }} {{ trans('SAR') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
