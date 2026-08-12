@extends('admin::layouts.dashboard')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y mx-auto">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold py-1 mb-0"><span class="text-muted fw-light">@lang('Follow Ups') /</span> @lang('Follow Ups Analysis')</h4>
            <p class="text-muted mb-0">@lang('Observe sales conversion metrics, timings, and geographic success rates.')</p>
        </div>
        <div>
            <a href="{{ route('dashboard.cart-follow-ups.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i> @lang('Follow Ups List')
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dashboard.cart-follow-ups.analysis') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="from_date" class="form-label fw-semibold text-muted">@lang('From Date')</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="to_date" class="form-label fw-semibold text-muted">@lang('To Date')</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter me-1"></i> @lang('filter')
                    </button>
                    @if(request('from_date') || request('to_date'))
                        <a href="{{ route('dashboard.cart-follow-ups.analysis') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i> @lang('Reset')
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- Total Follow Ups -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-stats bg-primary bg-opacity-10 rounded p-3 me-3">
                            <i class="fas fa-phone-alt fs-3 text-primary"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted fs-6 fw-medium">@lang('Total Follow Ups')</span>
                            <h3 class="card-title text-dark mb-0 mt-1 fw-bold">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 text-muted fs-7">
                        <i class="fas fa-info-circle me-1"></i> @lang('Total initiated follow-up calls/messages')
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales (Conversions) -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-stats bg-success bg-opacity-10 rounded p-3 me-3">
                            <i class="fas fa-shopping-bag fs-3 text-success"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted fs-6 fw-medium">@lang('Follow Up Sales')</span>
                            <h3 class="card-title text-dark mb-0 mt-1 fw-bold">{{ $stats['sales_count'] }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 text-muted fs-7">
                        <i class="fas fa-check-circle me-1"></i> @lang('Successfully converted to order sales')
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-stats bg-warning bg-opacity-10 rounded p-3 me-3">
                            <i class="fas fa-percentage fs-3 text-warning"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted fs-6 fw-medium">@lang('Conversion Rate')</span>
                            <h3 class="card-title text-dark mb-0 mt-1 fw-bold">{{ $stats['conversion_rate'] }}%</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-light" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $stats['conversion_rate'] }}%" aria-valuenow="{{ $stats['conversion_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Conversion Time -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-stats bg-info bg-opacity-10 rounded p-3 me-3">
                            <i class="fas fa-clock fs-3 text-info"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted fs-6 fw-medium">@lang('Avg Conversion Time')</span>
                            <h3 class="card-title text-dark mb-0 mt-1 fw-bold">
                                {{ $stats['avg_time_mins'] !== null ? $stats['avg_time_mins'] . ' ' . __('mins') : '--' }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-3 text-muted fs-7">
                        <i class="fas fa-bolt me-1"></i> @lang('Average speed to place order after call')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="row g-4 mb-4">
        <!-- Monthly Conversion Progress / Trend -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-semibold text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>@lang('Conversion Progress Trend')</h5>
                </div>
                <div class="card-body">
                    <div id="monthlyTrendChart"></div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-semibold text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>@lang('Status Distribution')</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="statusDistributionChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Tables & Insights Grid -->
    <div class="row g-4 mb-4">
        <!-- Best Admins in Sales -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-semibold text-dark"><i class="fas fa-trophy me-2 text-warning"></i>@lang('Best Admins in Sales')</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="ps-4">@lang('Admin Name')</th>
                                <th class="text-center">@lang('Follow Ups')</th>
                                <th class="text-center">@lang('Sales')</th>
                                <th class="pe-4 text-center">@lang('Conversion Rate')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['admin_stats'] as $admin)
                                @php
                                    $rate = $admin->total_follow_ups > 0 ? round(($admin->sales_count / $admin->total_follow_ups) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">{{ $admin->admin_name }}</td>
                                    <td class="text-center text-muted">{{ $admin->total_follow_ups }}</td>
                                    <td class="text-center text-success fw-bold">{{ $admin->sales_count }}</td>
                                    <td class="pe-4 text-center">
                                        {{ $rate }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">@lang('No data available')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sales by Place / City -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-semibold text-dark"><i class="fas fa-map-marked-alt me-2 text-info"></i>@lang('Sales Conversion by City')</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="ps-4">@lang('City')</th>
                                <th class="text-center">@lang('Follow Ups')</th>
                                <th class="text-center">@lang('Sales')</th>
                                <th class="pe-4 text-center">@lang('Conversion Rate')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['city_stats'] as $city)
                                @php
                                    $cName = $city->city_name ?: __('Unknown City');
                                    $rate = $city->total_follow_ups > 0 ? round(($city->sales_count / $city->total_follow_ups) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">{{ $cName }}</td>
                                    <td class="text-center text-muted">{{ $city->total_follow_ups }}</td>
                                    <td class="text-center text-success fw-bold">{{ $city->sales_count }}</td>
                                    <td class="pe-4 text-center">
                                        {{ $rate }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">@lang('No data available')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/apex-charts/apex-charts.css" />
<style>
    .card-stats {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .avatar-stats {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .fs-7 {
        font-size: 0.85rem;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('control') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script>
$(document).ready(function() {
    // 1. Status Distribution Donut Chart
    let statusesObj = @json($stats['statuses']);
    let statusLabels = [];
    let statusValues = [];

    // Map labels to localized titles
    let statusMap = {
        'pending': @json(__('status_pending')),
        'sale': @json(__('sale')),
        'no_answer': @json(__('no answer')),
        'not_interested': @json(__('not interested'))
    };

    Object.keys(statusMap).forEach(function(key) {
        statusLabels.push(statusMap[key]);
        statusValues.push(statusesObj[key] || 0);
    });

    let statusColors = ['#ff9966', '#11998e', '#6c757d', '#2193b0'];

    let statusOptions = {
        series: statusValues,
        chart: {
            type: 'donut',
            height: 320
        },
        labels: statusLabels,
        colors: statusColors,
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: @json(__('Total')),
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 250
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    let statusChart = new ApexCharts(document.querySelector("#statusDistributionChart"), statusOptions);
    statusChart.render();

    // 2. Monthly Conversion Progress Chart (Area)
    let monthlyTrend = @json($stats['monthly_trend']);
    let trendMonths = [];
    let trendTotalFollowUps = [];
    let trendSales = [];

    monthlyTrend.forEach(function(item) {
        trendMonths.push(item.month);
        trendTotalFollowUps.push(parseInt(item.total_follow_ups) || 0);
        trendSales.push(parseInt(item.sales_count) || 0);
    });

    let trendOptions = {
        series: [{
            name: @json(__('Total Follow Ups')),
            data: trendTotalFollowUps
        }, {
            name: @json(__('Follow Up Sales')),
            data: trendSales
        }],
        chart: {
            type: 'area',
            height: 320,
            toolbar: {
                show: false
            }
        },
        colors: ['#667eea', '#11998e'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: trendMonths
        },
        fill: {
            type: 'solid',
            opacity: 0.1
        },
        tooltip: {
            x: {
                format: 'yyyy-MM'
            }
        }
    };

    let trendChart = new ApexCharts(document.querySelector("#monthlyTrendChart"), trendOptions);
    trendChart.render();
});
</script>
@endpush
