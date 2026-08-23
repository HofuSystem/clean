@extends('admin::layouts.dashboard')

@section('content')
    <style>
        .modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
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
        .day-row {
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }
        .day-row:hover {
            border-left-color: #667eea;
            background-color: #f8f9ff;
        }
        .day-row.positive {
            border-left-color: #10b981;
        }
        .day-row.negative {
            border-left-color: #ef4444;
        }
        .daily-input {
            border-radius: 4px;
            padding: 4px 6px;
            min-width: 90px;
            text-align: center;
            transition: all 0.2s;
            font-size: 0.875rem;
        }
        .daily-input:focus {
            background-color: #fff !important;
            border: 1px solid #4a79b5 !important;
            box-shadow: 0 0 4px rgba(74,121,181,0.4);
            color: #333 !important;
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <!--begin::Toolbar-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack mb-3">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }} - {{ trans($monthName) }} {{ $year }}</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.financial-analysis', ['year' => $year, 'city_id' => $cityId, 'company_type' => $companyType]) }}" class="text-muted text-hover-primary">{{ trans('Financial Analysis') }}</a>
                    </li>
                    <li class="breadcrumb-item text-dark">{{ trans($monthName) }} {{ $year }}</li>
                </ul>
            </div>
            <div>
                <a href="{{ route('dashboard.financial-analysis', ['year' => $year, 'city_id' => $cityId, 'company_type' => $companyType]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ trans('Back to Monthly Analysis') }}
                </a>
            </div>
        </div>
        <!--end::Toolbar-->

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('dashboard.financial-analysis.daily', ['year' => $year, 'month' => $month]) }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="city_id">{{ trans("city") }}</label>
                        <select class="form-select form-select-lg" name="city_id" id="city_id">
                            <option value="">{{trans("select city")}}</option>
                            @foreach($cities ?? [] as $city)
                                <option data-id="{{$city->id }}" @selected($cityId == $city->id) value="{{$city->id }}" >{{$city->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="company_type">{{ trans('type') }}</label>
                        <select name="company_type" id="company_type" class="form-select form-select-lg">
                            <option value="">{{ trans('all') }}</option>
                            <option value="b2b" @selected($companyType == 'b2b')>{{ trans('b2b') }}</option>
                            <option value="b2c" @selected($companyType == 'b2c')>{{ trans('b2c') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary btn-lg w-100" type="submit">
                            <i class="fas fa-filter me-2"></i>{{ trans('filter') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Daily Analysis Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-calendar-day text-info me-2"></i>{{ trans('Daily Summary') }}
                        </h5>
                        <a href="{{ route('dashboard.financial-analysis.export-daily', array_merge(['year' => $year, 'month' => $month], request()->all())) }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>{{ trans('Export to Excel') }}
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover modern-table mb-0" id="daily_analysis_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ trans('Date') }}</th>
                                        <th class="text-center">{{ trans('Day') }}</th>
                                        <th class="text-center">{{ trans('New Orders') }}</th>
                                        <th class="text-center">{{ trans('Value of New Orders') }}</th>
                                        <th class="text-center">{{ trans('Today\'s Pickups') }}</th>
                                        <th class="text-center">{{ trans('Today\'s Deliveries') }}</th>
                                        <th class="text-center">{{ trans('Delivery Revenue') }}</th>
                                        <th class="text-center">{{ trans('Cost of Delivered Orders') }}</th>
                                        <th class="text-center">{{ trans('Gross Profit') }}</th>
                                        <th class="text-center">{{ trans('Profit Margin') }}</th>
                                        <th class="text-center">{{ trans('Online Operations') }}</th>
                                        <th class="text-center">{{ trans('Online Amount') }}</th>
                                        <th class="text-center">{{ trans('Cash Operations') }}</th>
                                        <th class="text-center">{{ trans('Cash Amount') }}</th>
                                        <th class="text-center">{{ trans('Wallet Operations') }}</th>
                                        <th class="text-center">{{ trans('Wallet Amount') }}</th>
                                        <th class="text-center">{{ trans('Average per Delivery') }}</th>
                                        <th class="text-center">{{ trans('Complaints') }}</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dailyAnalysis as $day)
                                        <tr class="day-row {{ $day['raw_profit'] >= 0 ? 'positive' : 'negative' }}">
                                            <td class="text-center fw-bold">{{ $day['date'] }}</td>
                                            <td class="text-center">{{ trans($day['day_name']) }}</td>
                                            <td class="text-center">{{ number_format($day['new_orders_count']) }}</td>
                                            <td class="text-center text-primary">{{ number_format($day['new_orders_value'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center">{{ number_format($day['pickups_count']) }}</td>
                                            <td class="text-center">{{ number_format($day['deliveries_count']) }}</td>
                                            <td class="text-center text-success fw-bold">{{ number_format($day['raw_revenue'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center text-danger">{{ number_format($day['raw_cost'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center fw-bold {{ $day['raw_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($day['raw_profit'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center fw-bold" style="color: {{ $day['profit_color'] }};">
                                                {{ number_format($day['raw_profit_percentage'], 2) }}%
                                            </td>
                                            <td class="text-center">{{ number_format($day['raw_online_ops_count']) }}</td>
                                            <td class="text-center text-primary">{{ number_format($day['raw_online_ops_amount'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center">{{ number_format($day['raw_cash_ops_count']) }}</td>
                                            <td class="text-center text-secondary">{{ number_format($day['raw_cash_ops_amount'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center">{{ number_format($day['raw_wallet_ops_count']) }}</td>
                                            <td class="text-center text-dark fw-bold">{{ number_format($day['raw_wallet_ops_amount'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center">{{ number_format($day['raw_avg_delivery_revenue'], 2) }} {{ trans('SAR') }}</td>
                                            <td class="text-center text-danger">{{ number_format($day['raw_complaints_count']) }}</td>
                                          
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @php
                                        $totalNewOrders = collect($dailyAnalysis)->sum('new_orders_count');
                                        $totalNewOrdersValue = collect($dailyAnalysis)->sum('new_orders_value');
                                        $totalPickups = collect($dailyAnalysis)->sum('pickups_count');
                                        $totalDeliveries = collect($dailyAnalysis)->sum('deliveries_count');
                                        $totalRevenue = collect($dailyAnalysis)->sum('raw_revenue');
                                        $totalCost = collect($dailyAnalysis)->sum('raw_cost');
                                        $totalProfit = collect($dailyAnalysis)->sum('raw_profit');
                                        $totalOnlineOps = collect($dailyAnalysis)->sum('raw_online_ops_count');
                                        $totalOnlineOpsAmount = collect($dailyAnalysis)->sum('raw_online_ops_amount');
                                        $totalCashOps = collect($dailyAnalysis)->sum('raw_cash_ops_count');
                                        $totalCashOpsAmount = collect($dailyAnalysis)->sum('raw_cash_ops_amount');
                                        $totalWalletOps = collect($dailyAnalysis)->sum('raw_wallet_ops_count');
                                        $totalWalletOpsAmount = collect($dailyAnalysis)->sum('raw_wallet_ops_amount');
                                        $totalComplaints = collect($dailyAnalysis)->sum('raw_complaints_count');
                                        $totalCompensations = collect($dailyAnalysis)->sum('raw_compensations_amount');
                                        
                                        $totalAdCost = collect($dailyAnalysis)->sum('ad_cost');
                                        $totalOpsExpenses = collect($dailyAnalysis)->sum('operating_expenses');
                                        $totalBankBalance = collect($dailyAnalysis)->sum('bank_balance');

                                        $totalProfitPercentage = $totalRevenue > 0 ? ($totalProfit / $totalRevenue * 100) : 0;
                                        $totalAvgDeliveryRevenue = $totalDeliveries > 0 ? ($totalRevenue / $totalDeliveries) : 0;
                                    @endphp
                                    <tr class="table-secondary fw-bold">
                                        <td class="text-center" colspan="2">{{ trans('TOTAL') }}</td>
                                        <td class="text-center">{{ number_format($totalNewOrders) }}</td>
                                        <td class="text-center text-primary">{{ number_format($totalNewOrdersValue, 2) }} {{ trans('SAR') }}</td>
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
                                        <td class="text-center">{{ number_format($totalOnlineOps) }}</td>
                                        <td class="text-center text-primary">{{ number_format($totalOnlineOpsAmount, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center">{{ number_format($totalCashOps) }}</td>
                                        <td class="text-center text-secondary">{{ number_format($totalCashOpsAmount, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center">{{ number_format($totalWalletOps) }}</td>
                                        <td class="text-center text-dark">{{ number_format($totalWalletOpsAmount, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center">{{ number_format($totalAvgDeliveryRevenue, 2) }} {{ trans('SAR') }}</td>
                                        <td class="text-center text-danger">{{ number_format($totalComplaints) }}</td>
                                        
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.daily-input').forEach(input => {
                input.addEventListener('change', function() {
                    const date = this.getAttribute('data-date');
                    const row = this.closest('tr');
                    
                    const ad_cost = row.querySelector('[data-field="ad_cost"]').value || 0;
                    const operating_expenses = row.querySelector('[data-field="operating_expenses"]').value || 0;
                    const bank_balance = row.querySelector('[data-field="bank_balance"]').value || 0;
                    const note = row.querySelector('[data-field="note"]').value || '';
                    
                    this.style.backgroundColor = '#fff3cd'; // Loading background
                    
                    fetch('{{ route("dashboard.financial-analysis.store-daily") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            date: date,
                            ad_cost: ad_cost,
                            operating_expenses: operating_expenses,
                            bank_balance: bank_balance,
                            note: note
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.style.backgroundColor = '';
                        if (data.success) {
                            // Success flash green
                            const originalBg = this.style.backgroundColor;
                            this.style.backgroundColor = '#d1e7dd';
                            setTimeout(() => {
                                this.style.backgroundColor = originalBg;
                            }, 1000);

                            // Recalculate totals
                            recalculateTotals();
                        } else {
                            this.style.backgroundColor = '#f8d7da';
                            alert(data.message || 'Error saving data');
                        }
                    })
                    .catch(error => {
                        this.style.backgroundColor = '#f8d7da';
                        console.error('Error:', error);
                    });
                });
            });

            function recalculateTotals() {
                let totalAd = 0;
                let totalOps = 0;
                let totalBank = 0;

                document.querySelectorAll('[data-field="ad_cost"]').forEach(input => {
                    totalAd += parseFloat(input.value) || 0;
                });
                document.querySelectorAll('[data-field="operating_expenses"]').forEach(input => {
                    totalOps += parseFloat(input.value) || 0;
                });
                document.querySelectorAll('[data-field="bank_balance"]').forEach(input => {
                    totalBank += parseFloat(input.value) || 0;
                });

                document.getElementById('total_ad_cost').textContent = totalAd.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' {{ trans("SAR") }}';
                document.getElementById('total_ops_expenses').textContent = totalOps.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' {{ trans("SAR") }}';
                document.getElementById('total_bank_balance').textContent = totalBank.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' {{ trans("SAR") }}';
            }
        });
    </script>
@endsection
