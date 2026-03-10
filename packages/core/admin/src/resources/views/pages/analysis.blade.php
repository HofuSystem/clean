@extends('admin::layouts.dashboard')

@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <form class="d-flex" method="GET" action="{{ route('dashboard.analysis') }}">
                <div class="col-10">
                   <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="" for="city_id">{{ trans("city") }}</label>
                            <select class="custom-select  form-select advance-select" name="city_id" id="city_id"  >
                                <option   value="" >{{trans("select city")}}</option>
                                @foreach($cities ?? [] as $sItem)
                                    <option data-id="{{$sItem->id }}" @selected(request('city_id') == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                @endforeach
        
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="from">{{ trans('from') }}</label>
                            <input value="{{ request()->from }}" type="date" name="from" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="to">{{ trans('to') }}</label>
                            <input value="{{ request()->to }}" type="date" name="to" class="form-control">
                        </div>
                   </div>
                </div>
                <div class="col-2 p-2 mt-3">
                    <button class="btn btn-success mx-auto" type="submit"> {{ trans('filter') }}</button>

                </div>

            </form>
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('Monthly Analysis') }}</h5>
                            <h5 class="text-muted">{{ date('Y') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Monthly Analysis Slider -->
                        <div id="monthlyAnalysisCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @for ($i = 0; $i < count($monthlyAnalysis); $i += 4)
                                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                        <div class="row gy-3">
                                            @for ($j = $i; $j < min($i + 4, count($monthlyAnalysis)); $j++)
                                                @php $month = $monthlyAnalysis[$j]; @endphp
                                                <div class="col-md-3 col-6 mb-3">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <h6 class="card-title mb-0 text-muted">{{ $month['month_abbr'] }}</h6>
                                                                <div class="badge rounded-pill" style="background-color: {{ $month['profit_color'] }}; color: white;">
                                                                    <i class="fas fa-chart-line me-1"></i>
                                                                    {{ $month['profit_percentage'] }}%
                                                                </div>
                                                            </div>
                                                            <div class="row text-center">
                                                                <div class="col-6">
                                                                    <div class="mb-2">
                                                                        <small class="text-muted">{{ trans('Orders') }}</small>
                                                                        <h4 class="mb-0 fw-bold text-primary">{{ $month['orders_count'] }}</h4>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="mb-2">
                                                                        <small class="text-muted">{{ trans('Net Profit') }}</small>
                                                                        <h4 class="mb-0 fw-bold" style="color: {{ $month['profit_color'] }};">
                                                                            {{ $month['total_profit'] }}
                                                                        </h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row text-center">
                                                                <div class="col-6">
                                                                    <div class="mb-2">
                                                                        <small class="text-muted">{{ trans('Revenue') }}</small>
                                                                        <h6 class="mb-0 text-success">{{ $month['total_revenue'] }}</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="mb-2">
                                                                        <small class="text-muted">{{ trans('Cost') }}</small>
                                                                        <h6 class="mb-0 text-danger">{{ $month['total_cost'] }}</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-center mt-2">
                                                                <small class="text-muted">
                                                                    {{ trans('Profit Margin') }}: {{ $month['profit_percentage'] }}%
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            
                            <!-- Carousel Controls -->
                            @if(count($monthlyAnalysis) > 4)
                                <button class="carousel-control-prev" type="button" data-bs-target="#monthlyAnalysisCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#monthlyAnalysisCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                                
                                <!-- Carousel Indicators -->
                                <div class="carousel-indicators">
                                    @for ($i = 0; $i < ceil(count($monthlyAnalysis) / 4); $i++)
                                        <button type="button" data-bs-target="#monthlyAnalysisCarousel" data-bs-slide-to="{{ $i }}" 
                                                class="{{ $i === 0 ? 'active' : '' }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i + 1 }}"></button>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Monthly Analysis -->
            <!-- Donut Chart -->
            <div class="col-md-4 col-12 mt-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">{{ trans('orders status presents') }}</h5>
                        </div>

                    </div>
                    <div class="card-body">
                        <div id="statusChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Donut Chart -->
            <!-- Donut Chart -->
            <div class="col-md-4 col-12 mt-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">{{ trans('orders types presents') }}</h5>
                        </div>

                    </div>
                    <div class="card-body">
                        <div id="typesChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Donut Chart -->
            <!-- /Donut Chart -->
            <!-- Donut Chart -->
            <div class="col-md-4 mt-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">{{ trans('orders pay types presents') }}</h5>
                        </div>

                    </div>
                    <div class="card-body">
                        <div id="payTypesChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Donut Chart -->
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('Orders Status Counters') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans(  'all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded-pill bg-label-success me-3 p-2">
                                        <i class="ti ti-chart-pie-2 ti-sm"></i>
                                    </div>
                                    <div class="card-info">
                                        <a href="{{ route('dashboard.orders.index') }}">
                                            <h5 class="mb-0">{{ $ordersCount }}</h5>
                                            <h5>{{ trans('all orders') }}</h5>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @foreach ($ordersStatusCounts as $ordersStatusCount)
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge rounded-pill bg-label-primary me-3 p-2">
                                            <i class="{{ $ordersStatusCount->icon ?? '' }}"
                                                style="color:{{ $ordersStatusCount->color }}"></i>
                                        </div>
                                        <div class="card-info">
                                            <a href="{{ route('dashboard.orders.index',['status' => $ordersStatusCount->status ]) }}">
                                                <h5 class="mb-0">{{ $ordersStatusCount->count }}</h5>
                                                <h5>{{ $ordersStatusCount->label }}</h5>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
      
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('Orders type Counters') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans(  'all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded-pill bg-label-success me-3 p-2">
                                        <i class="ti ti-chart-pie-2 ti-sm"></i>
                                    </div>
                                    <div class="card-info">
                                        <a href="{{ route('dashboard.orders.index') }}">
                                            <h5 class="mb-0">{{ $ordersCount }}</h5>
                                            <h5>{{ trans('all orders') }}</h5>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @foreach ($ordersTypeCounts as $ordersTypeCount)
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge rounded-pill bg-label-primary me-3 p-2">
                                            <i class="{{ $ordersTypeCount->icon ?? '' }}"
                                                style="color:{{ $ordersTypeCount->color }}"></i>
                                        </div>
                                        <div class="card-info">
                                            <a href="{{ route('dashboard.orders.index',['type' => $ordersTypeCount->type ]) }}">
                                                <h5 class="mb-0">{{ $ordersTypeCount->count }}</h5>
                                                <h5>{{ $ordersTypeCount->label }}</h5>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('Orders pay type Counters') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans(  'all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4 col-12">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded-pill bg-label-success me-3 p-2">
                                        <i class="ti ti-chart-pie-2 ti-sm"></i>
                                    </div>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $ordersCount }}</h5>
                                        <h5>{{ trans('all orders') }}</h5>
                                    </div>
                                </div>
                            </div>
                            @foreach ($ordersPayTypeCounts as $ordersPayTypeCount)
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <div class="badge rounded-pill bg-label-primary me-3 p-2">
                                            <i class="{{ $ordersPayTypeCount->icon ?? '' }}"
                                                style="color:{{ $ordersPayTypeCount->color }}"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $ordersPayTypeCount->count }}</h5>
                                            <h5>{{ trans($ordersPayTypeCount->pay_type) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('Orders analysis') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans(  'all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead class=" table-primary">
                                    <tr>
                                        <th scope="col">{{ trans('type') }}</th>
                                        <th scope="col">{{ trans('count') }}</th>
                                        <th scope="col">{{ trans('average') }}</th>
                                        <th scope="col">{{ trans('total') }}</th>
                                        <th scope="col">{{ trans('total_discount') }}</th>
                                        <th scope="col">{{ trans('total_delivery') }}</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordersAnalysis as $orderAnalysis)
                                        <tr class="">
                                            <td scope="row">{{ $orderAnalysis->type }}
                                            <td>{{$orderAnalysis->type_count}}</td>
                                            <td>{{$orderAnalysis->order_average}}</td>
                                            <td>{{$orderAnalysis->total_revenue}}</td>
                                            <td>{{$orderAnalysis->total_discount}}</td>
                                            <td>{{$orderAnalysis->total_delivery}}</td>
                                        </tr>
                                        
                                    @endforeach
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('order Representative analysis') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans(  'all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead class=" table-primary">
                                    <tr>
                                        <th scope="col">{{ trans('representative') }}</th>
                                        <th scope="col">{{ trans('count') }}</th>
                                        <th scope="col">{{ trans('total') }}</th>
                                        <th scope="col">{{ trans('finished') }}</th>
                                        <th scope="col">{{ trans('issue') }}</th>
                                        <th scope="col">{{ trans('cancelled') }}</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordersRepresentativeAnalysis as $orderRepresentativeAnalysis)
                                        <tr class="">
                                            <td scope="row">{{ $orderRepresentativeAnalysis->fullname }}
                                            <td>{{$orderRepresentativeAnalysis->count_orders}}</td>
                                            <td>{{$orderRepresentativeAnalysis->total_orders}}</td>
                                            <td>{{$orderRepresentativeAnalysis->count_finished_orders}}</td>
                                            <td>{{$orderRepresentativeAnalysis->count_issue_orders}}</td>
                                            <td>{{$orderRepresentativeAnalysis->count_canceled_orders}}</td>
                                        </tr>
                                        
                                    @endforeach
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('operators analysis') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans(  'all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead class=" table-primary">
                                    <tr>
                                        <th scope="col">{{ trans('operator') }}</th>
                                        <th scope="col">{{ trans('technics') }}</th>
                                        <th scope="col">{{ trans('count') }}</th>
                                        <th scope="col">{{ trans('total') }}</th>
                                        <th scope="col">{{ trans('finished') }}</th>
                                        <th scope="col">{{ trans('issue') }}</th>
                                        <th scope="col">{{ trans('cancelled') }}</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operatorsAnalysis as $operatorAnalysis)
                                        <tr class="">
                                            <td scope="row">{{ $operatorAnalysis->fullname }}
                                            <td>{{$operatorAnalysis->count_technicals}}</td>
                                            <td>{{$operatorAnalysis->count_orders}}</td>
                                            <td>{{$operatorAnalysis->total_orders}}</td>
                                            <td>{{$operatorAnalysis->count_finished_orders}}</td>
                                            <td>{{$operatorAnalysis->count_issue_orders}}</td>
                                            <td>{{$operatorAnalysis->count_canceled_orders}}</td>
                                        </tr>
                                        
                                    @endforeach
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ trans('revenue analysis') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans(  'all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead class=" table-primary">
                                    <tr>
                                        <th scope="col">{{ trans('delivery date') }}</th>
                                        <th scope="col">{{ trans('orders count') }}</th>
                                        <th scope="col">{{ trans('orders total') }}</th>
                                        <th scope="col">{{ trans('orders cost') }}</th>
                                        <th scope="col">{{ trans('orders revenue') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($revenuesAnalysis ?? [] as $revenueAnalysis)
                                        <tr class="">
                                            <td scope="row">{{ $revenueAnalysis->date }}
                                            <td>{{$revenueAnalysis->count}}</td>
                                            <td>{{$revenueAnalysis->total}}</td>
                                            <td>{{$revenueAnalysis->cost}}</td>
                                            <td>{{$revenueAnalysis->total - $revenueAnalysis->cost}}</td>
                                        </tr>
                                    @endforeach
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            </div>

            <!-- Order Pattern Analysis Section -->
            <div class="col-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0"><i class="fas fa-chart-area"></i> {{ trans('Order Pattern Analysis') }}</h5>
                            <h5 class="text-muted">{{ $timePeriod ?? trans('all') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Peak Usage Analysis -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center border-primary">
                                    <div class="card-body">
                                        <h5><i class="fas fa-clock"></i> {{ trans('Peak Hour') }}</h5>
                                        @if($orderPeakUsageAnalysis['peak_hour'])
                                            <h4>{{ $orderPeakUsageAnalysis['peak_hour']['hour_label'] }}</h4>
                                            <p>{{ $orderPeakUsageAnalysis['peak_hour']['order_count'] }} {{ trans('orders') }}</p>
                                            <small class="text-muted">{{ number_format($orderPeakUsageAnalysis['peak_hour']['total_revenue'], 2) }} {{ trans('revenue') }}</small>
                                        @else
                                            <h4>{{ trans('No data') }}</h4>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-success">
                                    <div class="card-body">
                                        <h5><i class="fas fa-calendar-day"></i> {{ trans('Peak Day') }}</h5>
                                        @if($orderPeakUsageAnalysis['peak_day'])
                                            <h4>{{ $orderPeakUsageAnalysis['peak_day']['day_name'] }}</h4>
                                            <p>{{ $orderPeakUsageAnalysis['peak_day']['order_count'] }} {{ trans('orders') }}</p>
                                            <small class="text-muted">{{ number_format($orderPeakUsageAnalysis['peak_day']['total_revenue'], 2) }} {{ trans('revenue') }}</small>
                                        @else
                                            <h4>{{ trans('No data') }}</h4>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-info">
                                    <div class="card-body">
                                        <h5><i class="fas fa-chart-bar"></i> {{ trans('Avg Orders/Hour') }}</h5>
                                        <h4>{{ $orderPeakUsageAnalysis['avg_orders_per_hour'] }}</h4>
                                        <p>{{ trans('average') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center border-warning">
                                    <div class="card-body">
                                        <h5><i class="fas fa-users"></i> {{ trans('Unique Customers') }}</h5>
                                        <h4>{{ $orderPeakUsageAnalysis['unique_customers'] }}</h4>
                                        <p>{{ trans('customers') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hourly Analysis -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h6><i class="fas fa-clock"></i> {{ trans('Hourly Order Pattern') }}</h6>
                                <div class="card">
                                    <div class="card-body">
                                        <canvas id="orderHourlyChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fas fa-trophy"></i> {{ trans('Top 5 Most Active Hours') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ trans('Hour') }}</th>
                                                <th>{{ trans('Orders') }}</th>
                                                <th>{{ trans('Revenue') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orderHourlyAnalysis['top_active_hours'] as $index => $hour)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $hour['hour_label'] }}</strong></td>
                                                    <td>{{ $hour['order_count'] }}</td>
                                                    <td>{{ number_format($hour['total_revenue'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Analysis -->
                        <div class="row">
                            <div class="col-md-8">
                                <h6><i class="fas fa-calendar-week"></i> {{ trans('Daily Order Pattern') }}</h6>
                                <div class="card">
                                    <div class="card-body">
                                        <canvas id="orderDailyChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fas fa-trophy"></i> {{ trans('Top 3 Most Active Days') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ trans('Day') }}</th>
                                                <th>{{ trans('Orders') }}</th>
                                                <th>{{ trans('Revenue') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orderDailyAnalysis['top_active_days'] as $index => $day)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $day['day_name'] }}</strong></td>
                                                    <td>{{ $day['order_count'] }}</td>
                                                    <td>{{ number_format($day['total_revenue'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/apex-charts/apex-charts.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .fab,
        .fas,
        .ti {
            font-size: 25px
        }
        
        /* Custom Carousel Arrow Styles */
        #monthlyAnalysisCarousel .carousel-control-prev,
        #monthlyAnalysisCarousel .carousel-control-next {
            width: 50px;
            height: 50px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        #monthlyAnalysisCarousel .carousel-control-prev:hover,
        #monthlyAnalysisCarousel .carousel-control-next:hover {
            opacity: 1;
            background-color: rgba(0, 0, 0, 0.9);
        }
        
        #monthlyAnalysisCarousel .carousel-control-prev {
            left: -25px;
        }
        
        #monthlyAnalysisCarousel .carousel-control-next {
            right: -25px;
        }
        
        #monthlyAnalysisCarousel .carousel-control-prev-icon,
        #monthlyAnalysisCarousel .carousel-control-next-icon {
            width: 20px;
            height: 20px;
            filter: brightness(0) invert(1);
        }
        
        /* Carousel Indicators */
        #monthlyAnalysisCarousel .carousel-indicators {
            bottom: -40px;
        }
        
        #monthlyAnalysisCarousel .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ccc;
            border: none;
            margin: 0 4px;
        }
        
        #monthlyAnalysisCarousel .carousel-indicators button.active {
            background-color: #007bff;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            #monthlyAnalysisCarousel .carousel-control-prev,
            #monthlyAnalysisCarousel .carousel-control-next {
                width: 40px;
                height: 40px;
            }
            
            #monthlyAnalysisCarousel .carousel-control-prev {
                left: -20px;
            }
            
            #monthlyAnalysisCarousel .carousel-control-next {
                right: -20px;
            }
        }
    </style>
@endpush
@push('js')
    <script src="{{ asset('control') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        (function() {
            let totalOrders = {{ $ordersCount }};
            let status
            let cardColor, headingColor, labelColor, borderColor, legendColor;

            if (isDarkStyle) {
                cardColor = config.colors_dark.cardColor;
                headingColor = config.colors_dark.headingColor;
                labelColor = config.colors_dark.textMuted;
                legendColor = config.colors_dark.bodyColor;
                borderColor = config.colors_dark.borderColor;
            } else {
                cardColor = config.colors.cardColor;
                headingColor = config.colors.headingColor;
                labelColor = config.colors.textMuted;
                legendColor = config.colors.bodyColor;
                borderColor = config.colors.borderColor;
            }

            // Color constant
            const chartColors = {
                column: {
                    series1: '#826af9',
                    series2: '#d2b0ff',
                    bg: '#f8d3ff'
                },
                donut: {
                    series1: '#fee802',
                    series2: '#3fd0bd',
                    series3: '#826bf8',
                    series4: '#2b9bf4'
                },
                area: {
                    series1: '#29dac7',
                    series2: '#60f2ca',
                    series3: '#a5f8cd'
                }
            };


            function createChart(id, values) {
                let colors = [];
                let labels = [];
                let series = [];
                values.forEach(element => {
                    colors.push(element.color)
                    labels.push(element.label)
                    series.push(parseFloat(element.percentage.toFixed(0)))
                });

                const statusChartEl = document.querySelector('#' + id),
                    statusChartConfig = {
                        chart: {
                            height: 390,
                            type: 'donut'
                        },
                        labels: labels,
                        series: series,
                        colors: colors,
                        stroke: {
                            show: false,
                            curve: 'straight'
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val, opt) {
                                return parseInt(val, 10) + '%';
                            }
                        },
                        legend: {
                            show: true,
                            position: 'bottom',
                            markers: {
                                offsetX: -3
                            },
                            itemMargin: {
                                vertical: 3,
                                horizontal: 10
                            },
                            labels: {
                                colors: legendColor,
                                useSeriesColors: false
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true,
                                        name: {
                                            fontSize: '2rem',
                                            fontFamily: 'Public Sans'
                                        },
                                        value: {
                                            fontSize: '1.2rem',
                                            color: legendColor,
                                            fontFamily: 'Public Sans',
                                            formatter: function(val) {
                                                return parseInt(val, 10) + '%';
                                            }
                                        },
                                        total: {
                                            show: true,
                                            fontSize: '1.5rem',
                                            color: headingColor,
                                            label: trans('orders'),
                                            formatter: function(w) {
                                                return '100%';
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        responsive: [{
                                breakpoint: 992,
                                options: {
                                    chart: {
                                        height: 380
                                    },
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            colors: legendColor,
                                            useSeriesColors: false
                                        }
                                    }
                                }
                            },
                            {
                                breakpoint: 576,
                                options: {
                                    chart: {
                                        height: 320
                                    },
                                    plotOptions: {
                                        pie: {
                                            donut: {
                                                labels: {
                                                    show: true,
                                                    name: {
                                                        fontSize: '1.5rem'
                                                    },
                                                    value: {
                                                        fontSize: '1rem'
                                                    },
                                                    total: {
                                                        fontSize: '1.5rem'
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            colors: legendColor,
                                            useSeriesColors: false
                                        }
                                    }
                                }
                            },
                            {
                                breakpoint: 420,
                                options: {
                                    chart: {
                                        height: 280
                                    },
                                    legend: {
                                        show: false
                                    }
                                }
                            },
                            {
                                breakpoint: 360,
                                options: {
                                    chart: {
                                        height: 250
                                    },
                                    legend: {
                                        show: false
                                    }
                                }
                            }
                        ]
                    };
                if (typeof statusChartEl !== undefined && statusChartEl !== null) {
                    const statusChart = new ApexCharts(statusChartEl, statusChartConfig);
                    statusChart.render();
                }
            }


            let statusValues = {!! json_encode($ordersStatusCounts) !!}
            createChart('statusChart', statusValues);

            let typesValues = {!! json_encode($ordersTypeCounts) !!}
            createChart('typesChart', typesValues);

            let ordersPayTypeCounts = {!! json_encode($ordersPayTypeCounts) !!}
            createChart('payTypesChart', ordersPayTypeCounts);

            $('.data-table').DataTable({
                "paging": true,        
                "searching": true,    
                "ordering": true,     
                "info": true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf'
                ],     
            });

            // Order Hourly Chart
            const orderHourlyCtx = document.getElementById('orderHourlyChart');
            if (orderHourlyCtx) {
                const orderHourlyChart = new Chart(orderHourlyCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json(collect($orderHourlyAnalysis['hourly_data'])->pluck('hour_label')),
                        datasets: [{
                            label: '{{ trans("Orders") }}',
                            data: @json(collect($orderHourlyAnalysis['hourly_data'])->pluck('order_count')),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1,
                            fill: true,
                            yAxisID: 'y'
                        }, {
                            label: '{{ trans("Revenue") }}',
                            data: @json(collect($orderHourlyAnalysis['hourly_data'])->pluck('total_revenue')),
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.1,
                            fill: true,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '{{ trans("Hourly Order Pattern") }}'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: '{{ trans("Orders") }}'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: '{{ trans("Revenue") }}'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        }
                    }
                });
            }

            // Order Daily Chart
            const orderDailyCtx = document.getElementById('orderDailyChart');
            if (orderDailyCtx) {
                const orderDailyChart = new Chart(orderDailyCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json(collect($orderDailyAnalysis['daily_data'])->pluck('day_name')),
                        datasets: [{
                            label: '{{ trans("Orders") }}',
                            data: @json(collect($orderDailyAnalysis['daily_data'])->pluck('order_count')),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgb(54, 162, 235)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        }, {
                            label: '{{ trans("Revenue") }}',
                            data: @json(collect($orderDailyAnalysis['daily_data'])->pluck('total_revenue')),
                            backgroundColor: 'rgba(255, 159, 64, 0.8)',
                            borderColor: 'rgb(255, 159, 64)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '{{ trans("Daily Order Pattern") }}'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: '{{ trans("Orders") }}'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: '{{ trans("Revenue") }}'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        }
                    }
                });
            }

        })();
    </script>
@endpush
