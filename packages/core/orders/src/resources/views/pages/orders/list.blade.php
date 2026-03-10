@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y ">

        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <!--end::Item-->
                    
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">@lang('orders')</li>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

        </div>
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols" id="visible_cols"
                                multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->

                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                    <div class="d-flex">
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.orders.index') }}">
                                            <div class="fw-bolder fs-5 text-success">
                                                {{ $total }}
                                                <i class="fas fa-list-alt"></i>
                                                @lang('total')
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-danger  text-danger rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.orders.index',['trash' => 1]) }}">
                                            <div class="fw-bolder fs-5 text-danger">
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt"></i>
                                                @lang('Trash')
                                            </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        @can('dashboard.orders.export')
                                            <a href="{{ route('dashboard.orders.export') }}" id="export" type="button"
                                                class="btn-operation ">
                                                <i class="fas fa-upload"></i>
                                                <span>
                                                    @lang('Export Report')
                                                </span>
                                            </a>
                                        @endcan
                                        @can('dashboard.orders.import')
                                            <a href="{{ route('dashboard.orders.import') }}" class="btn-operation">
                                                <i class="fas fa-file-import"></i>
                                                <span>
                                                    @lang('import list')
                                                </span>
                                            </a>
                                        @endcan
                                        <!--begin::Add -->
                                        @can('dashboard.orders.create')
                                            <a href="{{ route('dashboard.orders.create',['forCompany' => $forCompany ?? false ]) }}" class="btn-operation ">
                                                <i class="fas fa-plus-circle"></i>
                                                <span>
                                                    @lang('create new')
                                                </span>
                                            </a>
                                        @endcan
                                        <!--begin::Add -->
                                        @can('dashboard.orders.assign-representatives')
                                            <a id="assignRepresentatives"
                                                href="{{ route('dashboard.orders.assign-representatives',['forCompany' => $forCompany ?? false ]) }}"
                                                class="btn-operation ">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                                <i class="fas fa-users"></i>
                                                <span>
                                                    <!--end::Svg Icon-->@lang('assign representatives')

                                                </span>
                                            </a>
                                        @endcan
                                        @can('dashboard.orders.assign-operators')
                                            <a id="assignOperators" href="{{ route('dashboard.orders.assign-operators') }}"
                                                class="btn-operation ">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                                <i class="fas fa-cogs"></i>
                                                <span>
                                                    <!--end::Svg Icon-->@lang('assign operators')

                                                </span>
                                            </a>
                                        @endcan
                                    </div>
                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning  p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary"
                                data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                            <button id="assignSelected" type="button"
                                class="btn btn-primary mx-2">@lang('assign representative')</button>

                            <button id="assignSelectedOperators" type="button"
                                class="btn btn-primary mx-2">@lang('assign operator')</button>

                        </div>
                        <!--end::Group actions-->
                    </div>

                    <!--end::Card toolbar-->
                </div>

                <!--begin::Content-->
                <div class="container-fluid mt-1">
                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <i class="fas fa-filter"></i>
                        {{ trans('open filters of data') }}
                    </button>
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button data-type="all"
                                class="nav-link  type-switch @if (!request()->has('type')) active @endif"
                                id="pills-type-tab" data-bs-toggle="pill" data-bs-target="#pills-type" type="button"
                                role="tab" aria-controls="pills-type" aria-selected="true">{{ trans('All') }}</button>
                        </li>
                        @foreach ($types as $type)
                            <li class="nav-item" role="presentation">
                                <button data-type="{{ $type }}"
                                    class="nav-link  type-switch @if (request('type') == $type) active @endif"
                                    id="pills-{{ $type }}-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-{{ $type }}" type="button" role="tab"
                                    aria-controls="pills-{{ $type }}"
                                    aria-selected="true">{{ trans($type) }}</button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="px-7 py-5 row" data-kt-user-table-filter="form">
                                    <div class="col-md-6 mb-1">
                                        <label for="phone"> @lang('phone') </label>
                                        <input type="text" name="phone" class="form-control filter-input"
                                            placeholder="@lang('search for client phone') " value="{{ request('phone') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="city_id">@lang('city')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="city_id" id="city_id">
                                            <option value=""> @lang('select city')</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" @selected($city->id == request('city_id'))>
                                                    {{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="operator_id">@lang('operator')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="operator_id" id="operator_id">
                                            <option value=""> @lang('select operator')</option>
                                            @foreach ($operators as $user)
                                                <option value="{{ $user->id }}" @selected($user->id == request('operator_id'))>
                                                    {{ $user->fullname }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="operator_id">@lang('representative')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="representative_id" id="representative_id">
                                            <option value=""> @lang('select representative')</option>
                                            @foreach ($representatives as $user)
                                                <option value="{{ $user->id }}" @selected($user->id == request('representative_id'))>
                                                    {{ $user->fullname }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                 
                                    <div class="col-md-3 mb-1">
                                        <label for="reference_id"> @lang('Order Number') </label>
                                        <input type="text" name="reference_id" class="form-control filter-input"
                                            placeholder="@lang('search for order number') " value="{{ request('reference_id') }}">
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label for="type">@lang('type')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="type" id="type">
                                            <option value=""> @lang('select type')</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type }}" @selected($type == request('type'))>
                                                    {{ trans($type) }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label for="status">@lang('status')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="status" id="status">
                                            <option value=""> @lang('select status')</option>
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status }}" @selected($status == request('status'))>
                                                    {{ trans($status) }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label for="pay_type">@lang('pay type')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="pay_type" id="pay_type">
                                            <option value=""> @lang('select pay_type')</option>
                                            <option value="cash" @selected('cash' == request('pay_type'))>{{ trans('cash') }}
                                            </option>
                                            <option value="card" @selected('card' == request('pay_type'))>{{ trans('card') }}
                                            </option>
                                            <option value="wallet" @selected('wallet' == request('pay_type'))>{{ trans('wallet') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-1">
                                        <label for="rep_type">@lang('representative type')</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="rep_type" id="rep_type">
                                            <option value=""> @lang('select type')</option>
                                            <option value="receiver" @selected('receiver' == request('rep_type'))>{{ trans('receiver') }}</option>
                                            <option value="delivery" @selected('delivery' == request('rep_type'))>{{ trans('delivery') }}</option>
                                            <option value="technical" @selected('technical' == request('rep_type'))>{{ trans('technical') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="from_date"> @lang('Date from') </label>
                                        <input type="date" name="from_date" class="form-control filter-input"
                                            placeholder="@lang('search form date') " value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="from_time"> @lang('time from') </label>
                                        <input type="time" name="from_time" class="form-control filter-input"
                                            placeholder="@lang('search form time') " value="{{ request('from_time') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="to_date"> @lang('Date to') </label>
                                        <input type="date" name="to_date" class="form-control filter-input"
                                            placeholder="@lang('search form date') " value="{{ request('to_date') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="to_time"> @lang('time to') </label>
                                        <input type="time" name="to_time" class="form-control filter-input"
                                            placeholder="@lang('search form time') " value="{{ request('to_time') }}">
                                    </div>
   


                                    <!--begin::Actions-->
                                    <div class=" d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="reset">@lang('Reset')</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="filter">@lang('Apply')</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Content-->

                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.orders.index',['trash' => request()->trash, 'forCompany' => $forCompany ?? false ]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>

                                <th class="orderable text-center py-0" data-name="created_at"> @lang('added at')</th>
                                <th class="text-center py-0" data-name="reference_id">@lang('Order Number')</th>
                                <th class="text-center py-0" data-name="type">@lang('type')</th>
                                <th class="text-center py-0" data-name="status">@lang('status')</th>
                                <th class="text-center py-0" data-name="client_id">@lang('client')</th>
                                <th class="text-center py-0" data-name="pay_type">@lang('pay type')</th>
                                <th class="orderable text-center py-0" data-name="phone"> @lang('phone')</th>
                                <th class="text-center py-0" data-name="receiver_date"> @lang('receiver date')</th>
                                <th class="text-center py-0" data-name="receiver_time">@lang('receiver time')</th>
                                <th class="text-center py-0" data-name="delivery_date">@lang('delivery date')</th>
                                <th class="text-center py-0" data-name="delivery_time"> @lang('delivery time')</th>
                                <th class="text-center py-0" data-name="technical_date"> @lang('execute date')</th>
                                <th class=" text-center py-0" data-name="technical_time"> @lang('execute time')</th>
                                <th class="text-center py-0" data-name="total_price">@lang('order total')</th>
                                <th class="text-center py-0" data-name="total_provider_invoice">@lang('provider total')</th>
                                <th class="text-center py-0" data-name="paid">@lang('paid')</th>
                                <th class="text-center py-0" data-name="city_id">@lang('city')</th>
                                <th class="text-center py-0" data-name="district_id">@lang('district')</th>
                                <th class="text-center py-0" data-name="actions">@lang('Actions')</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">

                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>  
        <!--end::Container-->
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('orders::pages.orders.inc.assign-representatives-modal', ['users' => $representatives])
    @include('orders::pages.orders.inc.assign-operators-modal')
@endsection
@push('css')
@endpush
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.orders.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
</script>
    <script>
        $(document).ready(function() {

            $('#assignRepresentatives').click(function(e) {
                e.preventDefault();
                let ordersIds = [];
                $('table tbody .form-check-input').each(function(index, element) {
                    ordersIds.push($(this).val())

                });

                ordersIds = JSON.stringify(ordersIds)
                $('#assignRepresentativesModal #for').val(ordersIds);
                $('#assignRepresentativesModal').modal('show');
            });
            $('#assignSelected').click(function(e) {
                e.preventDefault();
                let ordersIds = [];
                $('table tbody .form-check-input:checked').each(function(index, element) {
                    ordersIds.push($(this).val())
                });
                ordersIds = JSON.stringify(ordersIds)
                $('#assignRepresentativesModal #for').val(ordersIds);
                $('#assignRepresentativesModal').modal('show');
            });

            $('#assignOperators').click(function(e) {
                e.preventDefault();
                let ordersIds = [];
                $('table tbody .form-check-input').each(function(index, element) {
                    ordersIds.push($(this).val())
                });

                ordersIds = JSON.stringify(ordersIds)
                $('#assignOperatorsModal #for').val(ordersIds);
                $('#assignOperatorsModal').modal('show');
            });
            $('#assignSelectedOperators').click(function(e) {
                e.preventDefault();
                let ordersIds = [];
                $('table tbody .form-check-input:checked').each(function(index, element) {
                    ordersIds.push($(this).val())
                });
                ordersIds = JSON.stringify(ordersIds)
                $('#assignOperatorsModal #for').val(ordersIds);
                $('#assignOperatorsModal').modal('show');
            });
            $(".type-switch").click(function() {

                var type = $(this).data("type");

                var visibleCol = $("#visible_cols"); // Select the dropdown

                $(".filter-input").val("").trigger("change");
                if (type != "all") {
                    $(".filter-input[name='type']").val(type).trigger("change");
                }

                var receiverFields = ["receiver_date", "receiver_time"];
                var deliveryFields = ["delivery_date", "delivery_time"];
                var technicalFields = ["technical_date", "technical_time"];
                if (type) {

                    if (type == "all") {
                        visibleCol.find("option").prop("selected", true); // Select all options
                    } else if (["services", "sales", "clothes"].includes(type)) {
                        visibleCol.find("option").each(function() {
                            var optionValue = $(this).val();
                            let column = datatable.column(optionValue);
                            optionValue = column.dataSrc();
                            if (receiverFields.includes(optionValue) || deliveryFields.includes(
                                    optionValue)) {
                                $(this).prop("selected", true);
                            } else if (technicalFields.includes(optionValue)) {
                                $(this).prop("selected", false);
                            }
                        });
                    } else if (["maid", "host"].includes(type)) {
                        visibleCol.find("option").each(function() {
                            var optionValue = $(this).val();
                            let column = datatable.column(optionValue);
                            optionValue = column.dataSrc();
                            if (receiverFields.includes(optionValue) || deliveryFields.includes(
                                    optionValue)) {
                                $(this).prop("selected", false);
                            } else if (technicalFields.includes(optionValue)) {
                                $(this).prop("selected", true);
                            }
                        });
                    }
                    visibleCol.trigger("change");
                }

                $('[data-kt-user-table-filter="filter"]').trigger('click');

            });

        });
    </script>
@endpush
