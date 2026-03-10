@extends('admin::layouts.dashboard')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card text-start">
            <div class="card-body">
                <h1 class="card-title text-center"><i class="fas fa-chart-line"></i> {{ trans('Routes Analysis') }}</h1>
                <form action="{{ route('dashboard.routes-analysis.index') }}" method="get" style="display: flex; align-items: center; width:100%">
                    <label for="time-filter" style="margin-right: 10px;">{{ __('Filter by Time:') }}</label>
                    <select id="time-filter" name="time_filter" class="form-control select2 " style="width:200px">
                        <option @selected(request()->time_filter == "all-time") value="all-time">{{ trans('all-time') }}</option>
                        <option @selected(request()->time_filter == "last-minute") value="last-minute">{{ trans('last-minute') }}</option>
                        <option @selected(request()->time_filter == "10-minute") value="10-minute">{{ trans('10-minute') }}</option>
                        <option @selected(request()->time_filter == "30-minute") value="30-minute">{{ trans('30-minute') }}</option>
                        <option @selected(request()->time_filter == "last-hour") value="last-hour">{{ trans('last-hour') }}</option>
                        <option @selected(request()->time_filter == "last-day") value="last-day">{{ trans('last-day') }}</option>
                        <option @selected(request()->time_filter == "last-week") value="last-week">{{ trans('last-week') }}</option>
                        <option @selected(request()->time_filter == "last-month") value="last-month">{{ trans('last-month') }}</option>
                        <option @selected(request()->time_filter == "last-year") value="last-year">{{ trans('last-year') }}</option>
                    </select>
                
                    <button type="submit" class="btn btn-success">{{ __('Apply Filter') }}</button>
                </form>
        
              

                <!-- Peak Usage Analysis Section -->
                <div class="row my-4">
                    <div class="col-md-12">
                        <h3><i class="fas fa-chart-area"></i> {{ trans('Peak Usage Analysis') }}</h3>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5><i class="fas fa-clock"></i> {{ trans('Peak Hour') }}</h5>
                                        @if($peakUsageAnalysis['peak_hour'])
                                            <h4>{{ $peakUsageAnalysis['peak_hour']['hour_label'] }}</h4>
                                            <p>{{ $peakUsageAnalysis['peak_hour']['request_count'] }} {{ trans('requests') }}</p>
                                        @else
                                            <h4>{{ trans('No data') }}</h4>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5><i class="fas fa-calendar-day"></i> {{ trans('Peak Day') }}</h5>
                                        @if($peakUsageAnalysis['peak_day'])
                                            <h4>{{ $peakUsageAnalysis['peak_day']['day_name'] }}</h4>
                                            <p>{{ $peakUsageAnalysis['peak_day']['request_count'] }} {{ trans('requests') }}</p>
                                        @else
                                            <h4>{{ trans('No data') }}</h4>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5><i class="fas fa-chart-bar"></i> {{ trans('Avg Requests/Hour') }}</h5>
                                        <h4>{{ $peakUsageAnalysis['avg_requests_per_hour'] }}</h4>
                                        <p>{{ trans('average') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hourly Analysis Section -->
                <div class="row my-4">
                    <div class="col-md-8">
                        <h3><i class="fas fa-clock"></i> {{ trans('Hourly Usage Analysis') }}</h3>
                        <div class="card">
                            <div class="card-body">
                                <canvas id="hourlyChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h3><i class="fas fa-trophy"></i> {{ trans('Top 5 Most Active Hours') }}</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('Hour') }}</th>
                                        <th>{{ trans('Requests') }}</th>
                                        <th>{{ trans('Users') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hourlyAnalysis['top_active_hours'] as $index => $hour)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $hour['hour_label'] }}</strong></td>
                                            <td>{{ $hour['request_count'] }}</td>
                                            <td>{{ $hour['unique_users'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Daily Analysis Section -->
                <div class="row my-4">
                    <div class="col-md-8">
                        <h3><i class="fas fa-calendar-week"></i> {{ trans('Daily Usage Analysis') }}</h3>
                        <div class="card">
                            <div class="card-body">
                                <canvas id="dailyChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h3><i class="fas fa-trophy"></i> {{ trans('Top 3 Most Active Days') }}</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('Day') }}</th>
                                        <th>{{ trans('Requests') }}</th>
                                        <th>{{ trans('Users') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailyAnalysis['top_active_days'] as $index => $day)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $day['day_name'] }}</strong></td>
                                            <td>{{ $day['request_count'] }}</td>
                                            <td>{{ $day['unique_users'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- All Requests Per User Section -->
                <div class="row my-4">
                    <div class="col-md-12">
                        <h3><i class="fas fa-users"></i> {{ trans('All Users Request Analysis') }}</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('User') }}</th>
                                        <th>{{ trans('Email') }}</th>
                                        <th>{{ trans('Phone') }}</th>
                                        <th>{{ trans('Total Requests') }}</th>
                                        <th>{{ trans('Last Request') }}</th>
                                        <th>{{ trans('Request %') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requestsPerUser as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($user->avatar)
                                                    <img src="{{ $user->avatar }}" class="rounded-circle" width="30" height="30" alt="{{ $user->name }}">
                                                @endif
                                                {{ $user->name }}
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->request_count }}</td>
                                            <td>
                                                <div class="last-request-info">
                                                    <strong>{{ trans('Endpoint') }}:</strong> {{ $user->last_endpoint }}<br>
                                                    <strong>{{ trans('Time') }}:</strong> {{ $user->last_request_time ? Carbon\Carbon::parse($user->last_request_time)->diffForHumans() : 'N/A' }}
                                                    @if($user->last_request_attributes)
                                                        <button type="button" class="btn btn-sm btn-info ms-2" 
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-html="true"
                                                                title="{{ json_encode(json_decode($user->last_request_attributes), JSON_PRETTY_PRINT) }}">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $percentage = ($user->request_count / $totalRequests) * 100;
                                                @endphp
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $percentage }}%;" 
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        
                <div class="row my-4">
                    <div class="col-md-6">
                        <h3><i class="fas fa-user"></i> {{ trans('Top 10 Most Active Users') }}</h3>
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('Name') }}</th>
                                    <th>{{ trans('Email') }}</th>
                                    <th>{{ trans('Phone') }}</th>
                                    <th>{{ trans('Requests') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topUsers as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->request_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        
                    <div class="col-md-6">
                        <h3><i class="fas fa-user-clock"></i> {{ trans('Top 10 Least Active Users') }}</h3>
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('Name') }}</th>
                                    <th>{{ trans('Email') }}</th>
                                    <th>{{ trans('Phone') }}</th>
                                    <th>{{ trans('Requests') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lestUsers as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->request_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        
                <div class="row my-4">
                    <div class="col-md-6">
                        <h3><i class="fas fa-link"></i> {{ trans('Top 10 Most Used Endpoints') }}</h3>
                        <table class="table table-bordered">
                            <thead class="table-primary">

                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('Endpoint') }}</th>
                                    <th>{{ trans('Requests') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mostUsedEndpoints as $index => $endpoint)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $endpoint->end_point }}</td>
                                        <td>{{ $endpoint->request_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        
                    <div class="col-md-6">
                        <h3><i class="fas fa-network-wired"></i> {{ trans('Top 10 Most Used IPs') }}</h3>
                        <table class="table table-bordered">
                            <thead class="table-primary">

                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('IP Address') }}</th>
                                    <th>{{ trans('Requests') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mostUsedIpAddress as $index => $ip)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $ip->ip_address }}</td>
                                        <td>{{ $ip->request_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/select2/select2.css" />
    <style>
        .table-responsive {
            padding: 10px;
        }

        .multi-actions {
            display: flex;
        }

        .progress {
            height: 20px;
            margin-bottom: 0;
            background-color: #f5f5f5;
            border-radius: 4px;
        }

        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: #0d6efd;
            transition: width 0.6s ease;
            font-size: 12px;
            font-weight: bold;
        }

        .table td {
            vertical-align: middle;
        }

        .rounded-circle {
            margin-right: 8px;
            object-fit: cover;
        }

        .last-request-info {
            font-size: 0.9em;
            line-height: 1.4;
        }

        .last-request-info strong {
            color: #666;
        }

        .tooltip-inner {
            max-width: 350px;
            text-align: left;
            white-space: pre-wrap;
            font-family: monospace;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-body h4 {
            color: #0d6efd;
            font-weight: bold;
        }

        .card-body h5 {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
@endpush
@push('js')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Select2 -->
    <script src="{{ asset('control') }}/assets/vendor/libs/select2/select2.js"></script>
    <script>
        const selectPicker = $('.selectpicker'),
            select2 = $('.select2'),
            select2Icons = $('.select2-icons');

        // Bootstrap Select
        // --------------------------------------------------------------------
        if (selectPicker.length) {
            selectPicker.selectpicker();
        }

        // Select2
        // --------------------------------------------------------------------

        // Default
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select value',
                    dropdownParent: $this.parent()
                });
            });
        }

        // Select2 Icons
        if (select2Icons.length) {
            // custom template to render icons
            function renderIcons(option) {
                if (!option.id) {
                    return option.text;
                }
                var $icon = "<i class='" + $(option.element).data('icon') + " me-2'></i>" + option.text;

                return $icon;
            }
            select2Icons.wrap('<div class="position-relative"></div>').select2({
                templateResult: renderIcons,
                templateSelection: renderIcons,
                escapeMarkup: function(es) {
                    return es;
                }
            });
        }

        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    html: true,
                    placement: 'left'
                });
            });

            // Hourly Chart
            const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
            const hourlyChart = new Chart(hourlyCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($hourlyAnalysis['hourly_data'])->pluck('hour_label')),
                    datasets: [{
                        label: '{{ trans("Requests") }}',
                        data: @json(collect($hourlyAnalysis['hourly_data'])->pluck('request_count')),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }, {
                        label: '{{ trans("Unique Users") }}',
                        data: @json(collect($hourlyAnalysis['hourly_data'])->pluck('unique_users')),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: '{{ trans("Hourly Usage Pattern") }}'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Daily Chart
            const dailyCtx = document.getElementById('dailyChart').getContext('2d');
            const dailyChart = new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($dailyAnalysis['daily_data'])->pluck('day_name')),
                    datasets: [{
                        label: '{{ trans("Requests") }}',
                        data: @json(collect($dailyAnalysis['daily_data'])->pluck('request_count')),
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    }, {
                        label: '{{ trans("Unique Users") }}',
                        data: @json(collect($dailyAnalysis['daily_data'])->pluck('unique_users')),
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: '{{ trans("Daily Usage Pattern") }}'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush
