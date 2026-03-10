@extends('admin::layouts.dashboard')

@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-primary mb-0">
                            <i class="fas fa-users"></i> {{ trans('Users Analysis Dashboard') }}
                        </h3>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle"></i> by default, the data is for the current month.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form  method="GET" action="{{ route('dashboard.users-analysis') }}">
                                <h4 class="text-primary mb-3 fs-5">{{ trans('filter for the users register in the period') }}</h4>
                                <div class="row col-12">
                                    <div class="col-md-4 mb-3">
                                        <label class="" for="city_id">{{ trans('city') }}</label>
                                        <select class="custom-select form-select advance-select" name="city_id"
                                            id="city_id">
                                            <option value="">{{ trans('select city') }}</option>
                                            @foreach ($cities ?? [] as $sItem)
                                                <option data-id="{{ $sItem->id }}" @selected(request('city_id') == $sItem->id)
                                                    value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="" for="from">{{ trans('from') }}</label>
                                        <input type="date" name="from" id="from" class="form-control"
                                            value="{{ request('from') }}" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="" for="to">{{ trans('to') }}</label>
                                        <input type="date" name="to" id="to" class="form-control"
                                            value="{{ request('to') }}" />
                                    </div>
                                </div>
                                <h4 class="text-primary mb-3 fs-">{{ trans('did not order in the period') }}</h4>
                                <div class="row col-12">
                                    <div class="col-md-6 mb-3">
                                        <label class="" for="did_not_order_from">{{ trans('from') }}</label>
                                        <input type="date" name="did_not_order_from" id="did_not_order_from" class="form-control"
                                            value="{{ request('did_not_order_from') }}" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="" for="did_not_order_to">{{ trans('to') }}</label>
                                        <input type="date" name="did_not_order_to" id="did_not_order_to" class="form-control"
                                            value="{{ request('did_not_order_to') }}" />
                                    </div>
                                </div>
                                <h4 class="text-primary mb-3 fs-5">{{ trans('did not appear in the period') }}</h4>
                                <div class="row col-12">
                                    <div class="col-md-6 mb-3">
                                        <label class="" for="did_not_appear_from">{{ trans('from') }}</label>
                                        <input type="date" name="did_not_appear_from" id="did_not_appear_from" class="form-control"
                                            value="{{ request('did_not_appear_from') }}" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="" for="did_not_order_to">{{ trans('to') }}</label>
                                        <input type="date" name="did_not_appear_to" id="did_not_appear_to" class="form-control"
                                            value="{{ request('did_not_appear_to') }}" />
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <button type="submit" class="btn btn-primary w-100">{{ trans('filter') }}</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if ($timePeriod)
            <div class="alert alert-info">
                <strong>{{ trans('Time Period') }}:</strong> {{ $timePeriod }}
            </div>
        @endif

        <!-- User Registration Analysis -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-info">
                            <i class="fas fa-user-plus"></i> {{ trans('User Registration Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left border-left-primary"
                                    style="border-left: 4px solid #007bff !important;">
                                    <div class="mr-3">
                                        <i class="fas fa-users fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-dark">{{ $user_registration_analysis['total_users'] ?? 0 }}
                                        </h4>
                                        <span class="text-muted">{{ trans('Total Users') }}</span>
                                    </div>
                                </div>
                            </div>
                            @if (isset($user_registration_analysis['users_by_roles']))
                                @foreach ($user_registration_analysis['users_by_roles'] as $roleData)
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                            style="border-left: 4px solid {{ ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6f42c1'][$loop->index % 5] }} !important;">
                                            <div class="mr-3">
                                                <i class="fas fa-user-tag fa-2x"
                                                    style="color: {{ ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6f42c1'][$loop->index % 5] }};"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-0 text-dark">{{ $roleData['count'] }}</h4>
                                                <span class="text-muted">{{ ucfirst($roleData['role']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Role Growth Chart -->
                        @if (isset($user_registration_analysis['role_growth_data']))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-secondary">{{ trans('Role Growth Over Time') }}</h6>
                                    <div style="height: 300px; position: relative;">
                                        <canvas id="roleGrowthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Completion & Device Platform Analysis -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-user-check"></i> {{ trans('Profile Completion Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                    style="border-left: 4px solid #28a745 !important;">
                                    <div class="mr-3">
                                        <i class="fas fa-check-circle fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-dark">
                                            {{ $profile_completion_analysis['complete_profile']['count'] ?? 0 }}</h4>
                                        <span class="text-muted">{{ trans('Complete Profiles') }}
                                            ({{ $profile_completion_analysis['complete_profile']['percentage'] ?? 0 }}%)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                    style="border-left: 4px solid #ffc107 !important;">
                                    <div class="mr-3">
                                        <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-dark">
                                            {{ $profile_completion_analysis['incomplete_profile']['count'] ?? 0 }}</h4>
                                        <span class="text-muted">{{ trans('Incomplete Profiles') }}
                                            ({{ $profile_completion_analysis['incomplete_profile']['percentage'] ?? 0 }}%)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Completion Chart -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div style="height: 250px; position: relative;">
                                    <canvas id="profileCompletionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device Platform Analysis -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-mobile-alt"></i> {{ trans('Device Platform Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if (isset($device_platform_analysis['device_counts']))
                                @foreach ($device_platform_analysis['device_counts'] as $platform => $data)
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                            style="border-left: 4px solid {{ $platform == 'android' ? '#28a745' : ($platform == 'ios' ? '#007bff' : '#6c757d') }} !important;">
                                            <div class="mr-3">
                                                <i
                                                    class="fas fa-{{ $platform == 'android' ? 'robot' : ($platform == 'ios' ? 'apple' : 'mobile-alt') }} fa-2x text-{{ $platform == 'android' ? 'success' : ($platform == 'ios' ? 'primary' : 'secondary') }}"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-0 text-dark">{{ $data['count'] ?? 0 }}</h4>
                                                <span class="text-muted">{{ ucfirst($platform) }}
                                                    ({{ $data['percentage'] ?? 0 }}%)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Platform Growth Chart -->
                        @if (isset($device_platform_analysis['platform_growth_data']))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div style="height: 250px; position: relative;">
                                        <canvas id="platformGrowthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- User Order Status Analysis -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-warning">
                            <i class="fas fa-shopping-cart"></i> {{ trans('User Order Status Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (isset($user_order_analysis['user_order_status']))
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                        style="border-left: 4px solid #17a2b8 !important;">
                                        <div class="mr-3">
                                            <i class="fas fa-shopping-cart fa-2x text-info"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 text-dark">
                                                {{ $user_order_analysis['user_order_status']['users_with_orders'] ?? 0 }}
                                            </h4>
                                            <span class="text-muted">{{ trans('Users with Orders') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                        style="border-left: 4px solid #6c757d !important;">
                                        <div class="mr-3">
                                            <i class="fas fa-user-times fa-2x text-secondary"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 text-dark">
                                                {{ $user_order_analysis['user_order_status']['users_without_orders'] ?? 0 }}
                                            </h4>
                                            <span class="text-muted">{{ trans('Users without Orders') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                        style="border-left: 4px solid #ffc107 !important;">
                                        <div class="mr-3">
                                            <i class="fas fa-clock fa-2x text-warning"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 text-dark">
                                                {{ $user_order_analysis['user_order_status']['users_with_pending_orders'] ?? 0 }}
                                            </h4>
                                            <span class="text-muted">{{ trans('Users with Pending Orders') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border-left"
                                        style="border-left: 4px solid #dc3545 !important;">
                                        <div class="mr-3">
                                            <i class="fas fa-ban fa-2x text-danger"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 text-dark">
                                                {{ $user_order_analysis['user_order_status']['users_with_canceled_orders'] ?? 0 }}
                                            </h4>
                                            <span class="text-muted">{{ trans('Users with Canceled Orders') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Order Type Growth Chart -->
                        @if (isset($user_order_analysis['order_type_growth_data']))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-secondary">{{ trans('Order Type Growth Over Time') }}</h6>
                                    <div style="height: 300px; position: relative;">
                                        <canvas id="orderTypeGrowthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Analysis by Role -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-danger">
                            <i class="fas fa-bell"></i> {{ trans('Notification Capability Analysis by Role') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (isset($notification_analysis) && is_array($notification_analysis))
                            @foreach ($notification_analysis as $roleData)
                                <div class="role-section mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-primary mb-0">
                                            <i class="fas fa-user-tag"></i>
                                            {{ $roleData['role_title'] ?? $roleData['role'] }}
                                            <span class="badge badge-secondary ml-2">{{ $roleData['total_users'] }}
                                                users</span>
                                        </h6>
                                    </div>

                                    <div class="row">
                                        @foreach ($roleData['data'] as $item)
                                        @if($item['total'] < 1)
                                            @continue
                                        @endif
                                            <!-- Has Ordered Users -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-{{ $item['color'] }}">
                                                    <div
                                                        class="card-header bg-light d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-{{ $item['color'] }}">
                                                            <i class="fas fa-shopping-cart"></i>
                                                            {{ $item['title'] }}
                                                        </h6>
                                                        <div class="row">

                                                            @if (($item['notifiable_fcm'] ?? 0) > 0)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-{{ $item['color'] }} notify-btn mb-2"
                                                                    data-role="{{ $roleData['role'] }}"
                                                                    data-type="{{ $item['type'] }}"
                                                                    data-ids="{{ $item['fcm_ids']->implode(',') }}"
                                                                    data-count="{{ $item['notifiable_fcm'] ?? 0 }}">
                                                                    <i class="fas fa-bell fs-3 mx-2"></i> {{ trans('apps') }}
                                                                    ({{ $item['notifiable_fcm'] ?? 0 }})
                                                                </button>
                                                            @endif
                                                            @if (($item['notifiable_whatsapp'] ?? 0) > 0)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-{{ $item['color'] }} notify-btn"
                                                                    data-role="{{ $roleData['role'] }}"
                                                                    data-type="{{ $item['type'] }}"
                                                                    data-ids="{{ $item['whatsapp_ids']->implode(',') }}"
                                                                    data-count="{{ $item['notifiable_whatsapp'] ?? 0 }}">
                                                                    <i class="fab fa-whatsapp fs-3 mx-2"></i> {{ trans('whatsapps') }}
                                                                    ({{ $item['notifiable_whatsapp'] ?? 0 }})
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row text-center">
                                                            <div class="col-4">
                                                                <div class="border-right">
                                                                    <h4 class="text-{{ $item['color'] }} mb-1">
                                                                        {{ $item['notifiable_fcm'] ?? 0 }}
                                                                    </h4>
                                                                    <small
                                                                        class="text-muted">{{ trans('Notifiable fcm') }}</small>
                                                                    <div class="text-xs text-{{ $item['color'] }}">
                                                                        {{ $item['notifiable_percentage_fcm'] ?? 0 }}%
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <h4 class="text-muted mb-1">
                                                                    {{ $item['notifiable_whatsapp'] ?? 0 }}
                                                                </h4>
                                                                <small
                                                                    class="text-muted">{{ trans('Notifiable whatsapp') }}</small>
                                                                <div class="text-xs text-muted">
                                                                    {{ $item['notifiable_percentage_whatsapp'] ?? 0 }}%
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <h4 class="text-muted mb-1">
                                                                    {{ $item['not_notifiable'] ?? 0 }}
                                                                </h4>
                                                                <small
                                                                    class="text-muted">{{ trans('Not Notifiable') }}</small>
                                                                <div class="text-xs text-muted">
                                                                    {{ $item['not_notifiable_percentage'] ?? 0 }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <small class="text-muted">{{ trans('Total') }}:
                                                                {{ $item['total'] ?? 0 }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <hr class="my-4">
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->




        <!-- User Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-table"></i> {{ trans('User Data Table') }}
                        </h5>
                        <div>
                            <span class="badge badge-info mr-2">{{ count($exportable_user_data ?? []) }} users</span>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Use buttons above table to export data
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="userDataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ trans('ID') }}</th>
                                        <th>{{ trans('Name') }}</th>
                                        <th>{{ trans('Phone') }}</th>
                                        <th>{{ trans('Email') }}</th>
                                        <th>{{ trans('Role') }}</th>
                                        <th>{{ trans('Registration Date') }}</th>
                                        <th>{{ trans('Profile Status') }}</th>
                                        <th>{{ trans('Device') }}</th>
                                        <th>{{ trans('Orders') }}</th>
                                        <th>{{ trans('Order Status') }}</th>
                                        <th>{{ trans('Notifications') }}</th>
                                        <th>{{ trans('Wallet') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($exportable_user_data))
                                        @foreach ($exportable_user_data as $user)
                                            <tr>
                                                <td>{{ $user['id'] }}</td>
                                                <td>{{ $user['fullname'] ?? 'N/A' }}</td>
                                                <td>{{ $user['phone'] ?? 'N/A' }}</td>
                                                <td>{{ $user['email'] ?? 'N/A' }}</td>
                                                <td><span
                                                        class="badge rounded-pill text-bg-primary">{{ $user['role'] }}</span>
                                                </td>
                                                <td>{{ $user['registration_date'] }}</td>
                                                <td>
                                                    <span
                                                        class="badge rounded-pill text-bg-{{ $user['profile_complete'] == 'Complete' ? 'success' : 'warning' }}">
                                                        {{ $user['profile_complete'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge rounded-pill text-bg-{{ $user['device_type'] == 'android' ? 'success' : ($user['device_type'] == 'ios' ? 'primary' : 'secondary') }}">
                                                        {{ $user['device_type'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $user['total_orders'] }}</td>
                                                <td>
                                                    <span
                                                        class="badge rounded-pill text-bg-{{ $user['order_status'] == 'has_ordered'
                                                            ? 'success'
                                                            : ($user['order_status'] == 'order_halfway'
                                                                ? 'warning'
                                                                : ($user['order_status'] == 'order_canceled'
                                                                    ? 'danger'
                                                                    : 'secondary')) }}">
                                                        {{ str_replace('_', ' ', ucfirst($user['order_status'])) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $user['can_receive_notifications'] == 'Yes' ? 'success' : 'secondary' }}">
                                                        {{ $user['can_receive_notifications'] }}
                                                    </span>
                                                </td>
                                                <td>${{ number_format($user['wallet_balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
    @include('notification::inc.notifyModal')
@endsection

@push('js')
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Include DataTables with export buttons -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

    <style>
        /* Custom DataTable styling */
        .dt-buttons {
            margin-bottom: 15px;
        }

        .dt-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 12px;
        }

        #userDataTable {
            font-size: 0.9rem;
        }

        #userDataTable thead th {
            background-color: #343a40 !important;
            color: white !important;
            border-color: #454d55 !important;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
        }

        #userDataTable tbody td {
            vertical-align: middle;
            padding: 8px;
        }

        .badge {
            font-size: 0.75em;
            font-weight: 500;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        /* Export button spacing and styling */
        .dt-button {
            white-space: nowrap;
        }

        /* Loading indicator */
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
            font-size: 14px;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%);
            width: auto !important;
            margin: 0 !important;
            padding: 10px 20px;
        }

        /* Search highlight */
        .highlight {
            background-color: #fff3cd;
            padding: 0;
        }

        /* Modern card styling with border accent */
        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }

        .modern-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }

        /* Chart container styling */
        .chart-container {
            position: relative;
            width: 100%;
            margin: 20px 0;
        }

        /* Ensure charts respect container height */
        canvas {
            max-height: 100% !important;
            width: 100% !important;
        }

        /* Enhanced spacing for metric cards */
        .metric-card {
            transition: all 0.3s ease;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>

    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {

            $('.notify-btn').on('click', function() {
                $('#notifyModal').modal('show')
                $('#notifyModal [name=for]').val('users')

                let ids = $(this).data('ids').split(',');
                ids = JSON.stringify(ids);
                $('#notifyModal [name=for_data]').val(ids)
                console.log(ids);

            });
            // Initialize DataTable for user data with export options
            if ($.fn.DataTable) {
                $('#userDataTable').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [
                        [0, 'desc']
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                        '<"row"<"col-sm-12 col-md-12"B>>' +
                        '<"row"<"col-sm-12"tr>>' +
                        '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    buttons: [{
                            extend: 'copy',
                            text: '<i class="fas fa-copy"></i> Copy',
                            className: 'btn btn-secondary btn-sm',
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="fas fa-file-csv"></i> CSV',
                            className: 'btn btn-success btn-sm',
                            filename: 'users_analysis_' + new Date().toISOString().slice(0, 10),
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn btn-success btn-sm',
                            filename: 'users_analysis_' + new Date().toISOString().slice(0, 10),
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            className: 'btn btn-danger btn-sm',
                            filename: 'users_analysis_' + new Date().toISOString().slice(0, 10),
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            },
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                    .length + 1).join('*').split('');
                                doc.defaultStyle.fontSize = 8;
                                doc.styles.tableHeader.fontSize = 9;
                                doc.styles.title.fontSize = 12;
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            className: 'btn btn-info btn-sm',
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            }
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="fas fa-columns"></i> Columns',
                            className: 'btn btn-warning btn-sm'
                        }
                    ],
                    columnDefs: [{
                            targets: [5, 6, 8, 9],
                            orderable: false
                        },
                        {
                            targets: '_all',
                            className: 'text-center'
                        },
                        {
                            targets: [1, 2], // Name and Email columns
                            className: 'text-left'
                        }
                    ],
                    language: {
                        buttons: {
                            copy: 'Copy to Clipboard',
                            copyTitle: 'Copy to Clipboard',
                            copySuccess: {
                                _: 'Copied %d rows to clipboard',
                                1: 'Copied 1 row to clipboard'
                            }
                        },
                        search: 'Search users:',
                        lengthMenu: 'Show _MENU_ users per page',
                        info: 'Showing _START_ to _END_ of _TOTAL_ users',
                        infoEmpty: 'No users found',
                        infoFiltered: '(filtered from _MAX_ total users)',
                        zeroRecords: 'No matching users found',
                        emptyTable: 'No user data available',
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Previous'
                        }
                    },
                    processing: true,
                    searchHighlight: true,
                    stateSave: true,
                    stateDuration: 60 * 60 * 24, // Save state for 24 hours
                });
            }

            // Initialize charts when Chart.js is loaded
            if (typeof Chart !== 'undefined') {

                // Role Growth Chart
                @if (isset($user_registration_analysis['role_growth_data']) &&
                        count($user_registration_analysis['role_growth_data']) > 0)
                    const roleGrowthCanvas = document.getElementById('roleGrowthChart');
                    if (roleGrowthCanvas) {
                        const roleGrowthCtx = roleGrowthCanvas.getContext('2d');
                        new Chart(roleGrowthCtx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                    'Oct', 'Nov', 'Dec'
                                ],
                                datasets: [
                                    @foreach ($user_registration_analysis['role_growth_data'] as $roleData)
                                        {
                                            label: '{{ $roleData['role_title'] ?? $roleData['role'] }}',
                                            data: [
                                                @foreach ($roleData['data'] as $monthData)
                                                    {{ $monthData['count'] }},
                                                @endforeach
                                            ],
                                            borderColor: 'hsl({{ ($loop->index * 60) % 360 }}, 70%, 50%)',
                                            backgroundColor: 'hsla({{ ($loop->index * 60) % 360 }}, 70%, 50%, 0.1)',
                                            tension: 0.4,
                                            fill: false
                                        }
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    }
                                }
                            }
                        });
                    }
                @endif

                // Profile Completion Chart
                @if (isset($profile_completion_analysis))
                    const profileCanvas = document.getElementById('profileCompletionChart');
                    if (profileCanvas) {
                        const profileCtx = profileCanvas.getContext('2d');
                        new Chart(profileCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Complete Profiles', 'Incomplete Profiles'],
                                datasets: [{
                                    data: [
                                        {{ $profile_completion_analysis['complete_profile']['count'] ?? 0 }},
                                        {{ $profile_completion_analysis['incomplete_profile']['count'] ?? 0 }}
                                    ],
                                    backgroundColor: ['#28a745', '#ffc107'],
                                    borderWidth: 2,
                                    borderColor: '#fff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                    }
                @endif

                // Platform Growth Chart
                @if (isset($device_platform_analysis['platform_growth_data']) &&
                        count($device_platform_analysis['platform_growth_data']) > 0)
                    const platformCanvas = document.getElementById('platformGrowthChart');
                    if (platformCanvas) {
                        const platformCtx = platformCanvas.getContext('2d');
                        new Chart(platformCtx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                    'Oct', 'Nov', 'Dec'
                                ],
                                datasets: [
                                    @foreach ($device_platform_analysis['platform_growth_data'] as $platformData)
                                        {
                                            label: '{{ ucfirst($platformData['platform']) }}',
                                            data: [
                                                @foreach ($platformData['data'] as $monthData)
                                                    {{ $monthData['count'] }},
                                                @endforeach
                                            ],
                                            borderColor: '{{ $platformData['platform'] == 'android' ? '#28a745' : ($platformData['platform'] == 'ios' ? '#007bff' : '#6c757d') }}',
                                            backgroundColor: '{{ $platformData['platform'] == 'android' ? 'rgba(40, 167, 69, 0.1)' : ($platformData['platform'] == 'ios' ? 'rgba(0, 123, 255, 0.1)' : 'rgba(108, 117, 125, 0.1)') }}',
                                            tension: 0.4,
                                            fill: false
                                        }
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    }
                                }
                            }
                        });
                    }
                @endif

                // Order Type Growth Chart
                @if (isset($user_order_analysis['order_type_growth_data']) && count($user_order_analysis['order_type_growth_data']) > 0)
                    const orderTypeCanvas = document.getElementById('orderTypeGrowthChart');
                    if (orderTypeCanvas) {
                        const orderTypeCtx = orderTypeCanvas.getContext('2d');
                        new Chart(orderTypeCtx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                    'Oct', 'Nov', 'Dec'
                                ],
                                datasets: [
                                    @foreach ($user_order_analysis['order_type_growth_data'] as $typeData)
                                        {
                                            label: '{{ ucfirst($typeData['type']) }}',
                                            data: [
                                                @foreach ($typeData['data'] as $monthData)
                                                    {{ $monthData['count'] }},
                                                @endforeach
                                            ],
                                            borderColor: 'hsl({{ ($loop->index * 45) % 360 }}, 80%, 50%)',
                                            backgroundColor: 'hsla({{ ($loop->index * 45) % 360 }}, 80%, 50%, 0.1)',
                                            tension: 0.4,
                                            fill: false
                                        }
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    }
                                }
                            }
                        });
                    }
                @endif

            } else {
                console.warn('Chart.js is not loaded. Charts will not be displayed.');
            }
        });
    </script>
@endpush
