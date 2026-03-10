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
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: scale(1.05);
        }
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px;
        }
        .pagination-wrapper .page-item {
            margin: 0 2px;
        }
        .pagination-wrapper .page-link {
            border-radius: 6px;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            color: #667eea;
        }
        .pagination-wrapper .page-item.active .page-link {
            background: #4a79b5;
            border-color: #667eea;
            color: white;
        }
        .pagination-wrapper .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
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
            <form method="GET" action="{{ route('dashboard.detailed-analysis') }}">
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
                        <label class="form-label fw-bold" for="year">{{ trans('Year') }}</label>
                        <select name="year" id="year" class="form-select form-select-lg">
                            @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                            @endfor
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

        <!-- First Row: Charts -->
        <div class="row mb-4">
            <!-- Donut Chart: Transactions per City -->
            <div class="col-md-6 mb-4">
                <div class="card modern-card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>{{ trans('Total Orders Transaction per City') }}
                            <p class="text-danger"> {{ trans('Note') }} : {{ trans('Based on the transactions of all orders that are not :notValidStatuses', ['notValidStatuses' => implode(', ', $notValidStatuses)]) }}</p>
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="cityTransactionsChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Growth Comparison Chart -->
            <div class="col-md-6 mb-4">
                <div class="card modern-card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-chart-line me-2 text-success"></i>{{ trans('Monthly Growth Comparison') }}
                            <p class="text-danger p-0 mt-1"> {{ trans('Note') }} : {{ trans('Based on the transactions of all orders that are not :notValidStatuses', ['notValidStatuses' => implode(', ', $notValidStatuses)]) }}</p>
                            <p class="text-danger p-0 mt-1"> {{ trans('Note') }} : {{ trans('provider invoice is based on the orders that are :finishedStatuses that is delivered this year', ['finishedStatuses' => implode(', ', $finishedStatuses)]) }}</p>
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="growthComparisonChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Monthly Financial Summary Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-table me-2 text-info"></i>{{ trans('Monthly Financial Summary') }} - {{ $year }}
                            <p class="text-danger p-0 mt-1"> {{ trans('Note') }} : {{ trans('Based on the transactions of all orders that are not :notValidStatuses', ['notValidStatuses' => implode(', ', $notValidStatuses)]) }}</p>
                            <p class="text-danger p-0 mt-1"> {{ trans('Note') }} : {{ trans('provider invoice is based on the orders that are :finishedStatuses that is delivered based on the month', ['finishedStatuses' => implode(', ', $finishedStatuses)]) }}</p>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover modern-table mb-0" id="monthly_financial_summary_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ trans('Month') }}</th>
                                        <th class="text-center">{{ trans('Total Coming Money') }}</th>
                                        <th class="text-center">{{ trans('Total Return Money') }}</th>
                                        <th class="text-center">{{ trans('Total Income') }}</th>
                                        <th class="text-center">{{ trans('Total Discount') }}</th>
                                        <th class="text-center">{{ trans('Total Delivery') }}</th>
                                        <th class="text-center">{{ trans('Total Provider Invoice') }}</th>
                                        <th class="text-center">{{ trans('Fixed Costs') }}</th>
                                        <th class="text-center">{{ trans('Net Income') }}</th>
                                        <th class="text-center">{{ trans('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlySummaries as $summary)
                                        <tr class="month-row {{ $summary['net_income'] >= 0 ? 'positive' : 'negative' }}">
                                            <td class="text-center fw-bold" data-order="{{ $summary['month'] }}">
                                                <div class="d-flex flex-column">
                                                    <span>{{ $summary['month_name'] }}</span>
                                                    <small class="text-muted">{{ $summary['month_abbr'] }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center text-success fw-bold" data-order="{{ $summary['total_coming_money'] }}">
                                                {{ number_format($summary['total_coming_money'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-danger fw-bold" data-order="-{{ $summary['total_return_money'] }}">
                                                -{{ number_format($summary['total_return_money'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-primary fw-bold" data-order="{{ $summary['total_income'] }}">
                                                {{ number_format($summary['total_income'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-warning" data-order="-{{ $summary['total_discount'] }}">
                                                -{{ number_format($summary['total_discount'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-info" data-order="{{ $summary['total_delivery'] }}">
                                                {{ number_format($summary['total_delivery'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-danger" data-order="-{{ $summary['total_provider_invoice'] }}">
                                                -{{ number_format($summary['total_provider_invoice'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-danger" data-order="-{{ $summary['fixed_costs'] }}">
                                                -{{ number_format($summary['fixed_costs'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center fw-bold {{ $summary['net_income'] >= 0 ? 'text-success' : 'text-danger' }}" data-order="{{ $summary['net_income'] }}">
                                                {{ number_format($summary['net_income'], 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        onclick="openAddFixedCostModal('{{ $summary['month_name'] }}', {{ $summary['month'] }}, {{ $year }})">
                                                    <i class="fas fa-plus me-1"></i>{{ trans('Add Cost') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                        <!-- Total Row -->
                                        <tr class="table-secondary fw-bold">
                                            <td class="text-center">{{ trans('TOTAL') }}</td>
                                            <td class="text-center text-success">
                                                {{ number_format(collect($monthlySummaries)->sum('total_coming_money'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-danger">
                                                {{ number_format(collect($monthlySummaries)->sum('total_return_money'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-primary">
                                                {{ number_format(collect($monthlySummaries)->sum('total_income'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-warning">
                                                {{ number_format(collect($monthlySummaries)->sum('total_discount'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-info">
                                                {{ number_format(collect($monthlySummaries)->sum('total_delivery'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-danger">
                                                {{ number_format(collect($monthlySummaries)->sum('total_provider_invoice'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center text-danger">
                                                {{ number_format(collect($monthlySummaries)->sum('fixed_costs'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center {{ collect($monthlySummaries)->sum('net_income') >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format(collect($monthlySummaries)->sum('net_income'), 2) }} {{ trans('SAR') }}
                                            </td>
                                            <td class="text-center">
                                                <!-- Empty cell for actions column -->
                                            </td>
                                        </tr>
                                    </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Fixed Cost Modal -->
        <div class="modal fade" id="addFixedCostModal" tabindex="-1" aria-labelledby="addFixedCostModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFixedCostModalLabel">{{ trans('Add Fixed Cost') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addFixedCostForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fixed_cost_name" class="form-label">{{ trans('Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fixed_cost_name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fixed_cost_amount" class="form-label">{{ trans('Amount') }} <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="fixed_cost_amount" name="amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fixed_cost_frequency" class="form-label">{{ trans('Frequency') }} <span class="text-danger">*</span></label>
                                        <select class="form-select" id="fixed_cost_frequency" name="frequency" required>
                                            <option value="">{{ trans('Select Frequency') }}</option>
                                            <option value="monthly">{{ trans('Monthly') }}</option>
                                            <option value="quarterly">{{ trans('Quarterly') }}</option>
                                            <option value="yearly">{{ trans('Yearly') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fixed_cost_date" class="form-label">{{ trans('Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="fixed_cost_date" name="date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="fixed_cost_description" class="form-label">{{ trans('Description') }}</label>
                                <textarea class="form-control" id="fixed_cost_description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ trans('Save Fixed Cost') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Third Row: Payment Method Totals -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-credit-card me-2 text-warning"></i>{{ trans('Payment Method Totals') }} - {{ $year }}
                            <p class="text-danger p-0 mt-1"> {{ trans('Note') }} : {{ trans('Based on the transactions of all orders that are not :notValidStatuses', ['notValidStatuses' => implode(', ', $notValidStatuses)]) }}</p>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <div class="stat-card" style="background-color: #667eea;">
                                    <h6 class="mb-2 text-white">
                                        <i class="fas fa-credit-card me-2"></i>{{ trans('Card') }}
                                    </h6>
                                    <h4 class="mb-0 text-white">{{ number_format($paymentMethods['card'], 2) }} {{ trans('SAR') }}</h4>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-card" style="background-color: #f5576c;">
                                    <h6 class="mb-2 text-white">
                                        <i class="fas fa-money-bill-wave me-2"></i>{{ trans('Cash') }}
                                    </h6>
                                    <h4 class="mb-0 text-white">{{ number_format($paymentMethods['cash'], 2) }} {{ trans('SAR') }}</h4>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-card" style="background-color: #4facfe;">
                                    <h6 class="mb-2 text-white">
                                        <i class="fas fa-wallet me-2"></i>{{ trans('Wallet') }}
                                    </h6>
                                    <h4 class="mb-0 text-white">{{ number_format($paymentMethods['wallet'], 2) }} {{ trans('SAR') }}</h4>
                                </div>
                            </div>
                          
                            <div class="col-md-2">
                                <div class="stat-card" style="background-color: #fa709a;">
                                    <h6 class="mb-2 text-white">
                                        <i class="fas fa-star me-2"></i>{{ trans('Points') }}
                                    </h6>
                                    <h4 class="mb-0 text-white">{{ number_format($paymentMethods['points'], 2) }} {{ trans('SAR') }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card" style="background-color: #30cfd0;">
                                    <h6 class="mb-2 text-white">
                                        <i class="fas fa-calculator me-2"></i>{{ trans('Total') }}
                                    </h6>
                                    <h4 class="mb-0 text-white">{{ number_format(array_sum($paymentMethods), 2) }} {{ trans('SAR') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fourth Row: Order Transactions Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-list me-2 text-danger"></i>{{ trans('Order Transactions') }}
                        </h5>
                        <button type="button" class="btn btn-success btn-sm" id="export_order_transactions_btn">
                            <i class="fas fa-file-export me-2"></i>{{ trans('Export') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label for="filter_from_date" class="form-label fw-bold">{{ trans('From Date') }}</label>
                                <input type="date" class="form-control" id="filter_from_date" name="filter_from_date">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_to_date" class="form-label fw-bold">{{ trans('To Date') }}</label>
                                <input type="date" class="form-control" id="filter_to_date" name="filter_to_date">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_reference_id" class="form-label fw-bold">{{ trans('Order Reference') }}</label>
                                <input type="text" class="form-control" id="filter_reference_id" name="filter_reference_id" placeholder="{{ trans('Order Reference') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_phone" class="form-label fw-bold">{{ trans('Phone Number') }}</label>
                                <input type="text" class="form-control" id="filter_phone" name="filter_phone" placeholder="{{ trans('Phone Number') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_city_id" class="form-label fw-bold">{{ trans('City') }}</label>
                                <select class="form-select" id="filter_city_id" name="filter_city_id">
                                    <option value="">{{ trans('All Cities') }}</option>
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" id="filter_transactions_btn">
                                    <i class="fas fa-search me-2"></i>{{ trans('Filter') }}
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover modern-table" id="transactions_table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('ID') }}</th>
                                        <th>{{ trans('Order') }}</th>
                                        <th>{{ trans('Client') }}</th>
                                        <th>{{ trans('Type') }}</th>
                                        <th>{{ trans('Amount') }}</th>
                                        <th>{{ trans('Date') }}</th>
                                        <th>{{ trans('Notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions_table_body">
                                    <tr>
                                        <td colspan="7" class="text-center">{{ trans('Loading...') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div id="transactions_pagination" class="mt-3 pagination-wrapper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Financial Summary DataTable
            if ($('#monthly_financial_summary_table').length) {
                $('#monthly_financial_summary_table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel me-1"></i> {{ trans("Export to Excel") }}',
                            className: 'btn btn-success btn-sm mb-3',
                            title: '{{ trans("Monthly Financial Summary") }} - {{ $year }}'
                        }
                    ],
                    paging: false,
                    searching: false,
                    info: false,
                    ordering: true,
                    order: [[0, 'asc']]
                });
            }

            // City Transactions Donut Chart
            const cityTransactionsCtx = document.getElementById('cityTransactionsChart');
            if (cityTransactionsCtx) {
                const cityData = @json($cityTransactions);
                new Chart(cityTransactionsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: cityData.labels,
                        datasets: [{
                            data: cityData.data,
                            backgroundColor: [
                                '#667eea', '#764ba2', '#f093fb', '#f5576c',
                                '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                                '#fa709a', '#fee140', '#30cfd0', '#330867'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Growth Comparison Chart
            const growthCtx = document.getElementById('growthComparisonChart');
            if (growthCtx) {
                const growthData = @json($monthlyGrowth);
                new Chart(growthCtx, {
                    type: 'line',
                    data: {
                        labels: growthData.map(item => item.month),
                        datasets: [
                            {
                                label: '{{ trans("Transactions") }}',
                                data: growthData.map(item => item.transactions),
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3
                            },
                            {
                                label: '{{ trans("Provider Invoice") }}',
                                data: growthData.map(item => item.provider_invoice),
                                borderColor: '#f5576c',
                                backgroundColor: 'rgba(245, 87, 108, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Order Transactions Table
            let currentPage = 1;
            
            // Define loadTransactions globally so it can be accessed by onclick handlers
            window.loadTransactions = function(page = 1) {
                const filters = {
                    from_date: document.getElementById('filter_from_date').value,
                    to_date: document.getElementById('filter_to_date').value,
                    reference_id: document.getElementById('filter_reference_id').value,
                    phone: document.getElementById('filter_phone').value,
                    city_id: document.getElementById('filter_city_id').value,
                    per_page: 15
                };

                fetch(`{{ route('dashboard.detailed-analysis.order-transactions') }}?page=${page}&${new URLSearchParams(filters)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('transactions_table_body');
                        tbody.innerHTML = '';

                        if (data.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="text-center">{{ trans("No transactions found") }}</td></tr>';
                        } else {
                            data.data.forEach(transaction => {
                                const row = document.createElement('tr');
                                const orderLink = transaction.order && transaction.order.edit_url 
                                    ? `<a href="${transaction.order.edit_url}" class="text-primary text-decoration-none fw-bold">${transaction.order.reference_id}</a>`
                                    : (transaction.order ? transaction.order.reference_id : '-');
                                const clientLink = transaction.order && transaction.order.client && transaction.order.client.edit_url
                                    ? `<a href="${transaction.order.client.edit_url}" class="text-primary text-decoration-none fw-bold">${transaction.order.client.name}</a><br><small class="text-muted">${transaction.order.client.phone || '-'}</small>`
                                    : (transaction.order && transaction.order.client 
                                        ? `${transaction.order.client.name}<br><small class="text-muted">${transaction.order.client.phone || '-'}</small>` 
                                        : '-');
                                row.innerHTML = `
                                    <td>${transaction.id}</td>
                                    <td>${orderLink}</td>
                                    <td>${clientLink}</td>
                                    <td><span class="badge bg-primary">${transaction.type}</span></td>
                                    <td class="${transaction.amount >= 0 ? 'text-success' : 'text-danger'} fw-bold">
                                        ${transaction.amount >= 0 ? '+' : ''}${parseFloat(transaction.amount).toFixed(2)} {{ trans('SAR') }}
                                    </td>
                                    <td>${transaction.created_at}</td>
                                    <td>${transaction.notes || '-'}</td>
                                `;
                                tbody.appendChild(row);
                            });
                        }

                        // Pagination with limited page numbers
                        const pagination = document.getElementById('transactions_pagination');
                        if (data.pagination.last_page > 1) {
                            let paginationHtml = '<nav><ul class="pagination pagination-wrapper">';
                            
                            const currentPage = data.pagination.current_page;
                            const lastPage = data.pagination.last_page;
                            const maxPages = 7; // Show max 7 page numbers
                            
                            let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
                            let endPage = Math.min(lastPage, startPage + maxPages - 1);
                            
                            if (endPage - startPage < maxPages - 1) {
                                startPage = Math.max(1, endPage - maxPages + 1);
                            }
                            
                            // Previous button
                            if (currentPage > 1) {
                                paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="${currentPage - 1}">{{ trans('Previous') }}</a></li>`;
                            } else {
                                paginationHtml += `<li class="page-item disabled"><span class="page-link">{{ trans('Previous') }}</span></li>`;
                            }
                            
                            // First page
                            if (startPage > 1) {
                                paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>`;
                                if (startPage > 2) {
                                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                                }
                            }
                            
                            // Page numbers
                            for (let i = startPage; i <= endPage; i++) {
                                if (i === currentPage) {
                                    paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                                } else {
                                    paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a></li>`;
                                }
                            }
                            
                            // Last page
                            if (endPage < lastPage) {
                                if (endPage < lastPage - 1) {
                                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                                }
                                paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="${lastPage}">${lastPage}</a></li>`;
                            }
                            
                            // Next button
                            if (currentPage < lastPage) {
                                paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="${currentPage + 1}">{{ trans('Next') }}</a></li>`;
                            } else {
                                paginationHtml += `<li class="page-item disabled"><span class="page-link">{{ trans('Next') }}</span></li>`;
                            }
                            
                            paginationHtml += '</ul></nav>';
                            pagination.innerHTML = paginationHtml;
                            
                            // Use event delegation for pagination links
                            pagination.addEventListener('click', function(e) {
                                const link = e.target.closest('a.page-link[data-page]');
                                if (link) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    const page = parseInt(link.getAttribute('data-page'));
                                    if (page && !isNaN(page)) {
                                        window.loadTransactions(page);
                                    }
                                }
                            });
                        } else {
                            pagination.innerHTML = '';
                        }

                        currentPage = page;
                        
                        // Scroll to transactions table smoothly
                        const transactionsTable = document.getElementById('transactions_table');
                        if (transactionsTable) {
                            transactionsTable.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading transactions:', error);
                    document.getElementById('transactions_table_body').innerHTML = 
                        '<tr><td colspan="7" class="text-center text-danger">{{ trans("Error loading transactions") }}</td></tr>';
                });
            };

            // Filter button
            document.getElementById('filter_transactions_btn').addEventListener('click', function() {
                loadTransactions(1);
            });

            // Export button
            document.getElementById('export_order_transactions_btn').addEventListener('click', function() {
                const filters = {
                    from_date: document.getElementById('filter_from_date').value,
                    to_date: document.getElementById('filter_to_date').value,
                    reference_id: document.getElementById('filter_reference_id').value,
                    phone: document.getElementById('filter_phone').value,
                    city_id: document.getElementById('filter_city_id').value,
                };

                const url = `{{ route('dashboard.detailed-analysis.order-transactions.export') }}?${new URLSearchParams(filters)}`;
                window.location.href = url;
            });

            // Load initial data
            loadTransactions(1);

            // Fixed Cost Modal Functions
            window.openAddFixedCostModal = function(monthName, month, year) {
                const modal = document.getElementById('addFixedCostModal');
                const form = document.getElementById('addFixedCostForm');
                const dateInput = document.getElementById('fixed_cost_date');
                
                // Set the date to the first day of the selected month
                const date = new Date(year, month - 1, 1);
                const dateString = date.toISOString().split('T')[0];
                dateInput.value = dateString;
                
                // Update modal title
                document.getElementById('addFixedCostModalLabel').textContent = 
                    `{{ trans('Add Fixed Cost') }} - ${monthName} ${year}`;
                
                // Reset form
                form.reset();
                dateInput.value = dateString;
                
                // Show modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            };

            // Handle form submission
            document.getElementById('addFixedCostForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Disable submit button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ trans("Saving...") }}';

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                 document.querySelector('input[name="_token"]')?.value;

                // Convert FormData to object
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                });

                fetch('{{ route("dashboard.detailed-analysis.store-fixed-cost") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formDataObj)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addFixedCostModal'));
                        modal.hide();

                        // Show success message with SweetAlert
                        Swal.fire({
                            text: data.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "{{ trans('Ok') }}",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        }).then(function(result) {
                            // Refresh the page
                            window.location.reload();
                        });
                    } else {
                        // Show validation errors
                        Swal.fire({
                            text: '{{ trans("Please check the form and try again.") }}',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "{{ trans('Ok') }}",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        });
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById('fixed_cost_' + field);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const feedback = input.parentNode.querySelector('.invalid-feedback') ||
                                                   input.parentNode.appendChild(document.createElement('div'));
                                    feedback.className = 'invalid-feedback';
                                    feedback.textContent = data.errors[field][0];
                                }
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        text: '{{ trans("An error occurred. Please try again.") }}',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "{{ trans('Ok') }}",
                        customClass: {
                            confirmButton: "btn fw-bold btn-success",
                        }
                    });
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });

            // Clear validation errors when modal is opened
            document.getElementById('addFixedCostModal').addEventListener('show.bs.modal', function() {
                const inputs = this.querySelectorAll('.is-invalid');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
                const feedbacks = this.querySelectorAll('.invalid-feedback');
                feedbacks.forEach(feedback => {
                    feedback.remove();
                });
            });
        });
    </script>
    @endpush
@endsection
