@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y " >

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
                    <li class="breadcrumb-item text-muted">@lang("wallet")</li>
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
        <div  class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->

                        <div  data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                     <div class="d-flex">
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.wallet-transactions.index') }}">
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
                                            <a href="{{ route('dashboard.wallet-transactions.index',['trash' => 1]) }}">
                                            <div class="fw-bolder fs-5 text-danger">
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt"></i>
                                                @lang('Trash')
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap">

                                    @can('dashboard.wallet-transactions.export')
                                        <a href="{{ route('dashboard.wallet-transactions.export') }}" id="export" type="button"
                                            class="btn-operation ">
                                            <i class="fas fa-upload"></i>
                                            <span>
                                                @lang('Export Report')
                                            </span>
                                        </a>
                                    @endcan
                                    @can('dashboard.wallet-transactions.import')
                                        <a href="{{ route('dashboard.wallet-transactions.import') }}"
                                            class="btn-operation">
                                            <i class="fas fa-file-import"></i>
                                            <span>
                                                @lang('import list')
                                            </span>
                                        </a>
                                    @endcan
                                    <!--begin::Add -->
                                    @can('dashboard.wallet-transactions.create')
                                        <a href="{{ route('dashboard.wallet-transactions.create') }}"
                                            class="btn-operation ">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>
                                                @lang('create new')
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

                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">

                                <div class="p-1 row" data-kt-user-table-filter="form">


                                        <div class="col-md-6 mb-1">
                                            <label for="type">@lang("type")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="type" id="type">
                                                <option value=""> @lang("select type")</option>
                                                <option value="deposit" @selected("deposit" == request("type")) >{{trans("deposit")}}</option>
                                                <option value="withdraw" @selected("withdraw" == request("type")) >{{trans("withdraw")}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="transaction_type">@lang("transaction type")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="transaction_type" id="transaction_type">
                                                <option value=""> @lang("select transaction type")</option>
                                                <option value="withdraw" @selected("withdraw" == request("transaction_type"))>{{ trans('withdraw') }}</option>
                                                <option value="charge" @selected("charge" == request("transaction_type"))>{{ trans('charge') }}</option>
                                                <option value="remaining_amount" @selected("remaining_amount" == request("transaction_type"))>{{ trans('remaining_amount') }}</option>
                                                <option value="compensation_add" @selected("compensation_add" == request("transaction_type"))>{{ trans('compensation_add') }}</option>
                                                <option value="promotional_add" @selected("promotional_add" == request("transaction_type"))>{{ trans('promotional_add') }}</option>
                                                <option value="order_payment" @selected("order_payment" == request("transaction_type"))>{{ trans('order_payment') }}</option>
                                                <option value="cashback" @selected("cashback" == request("transaction_type"))>{{ trans('cashback') }}</option>
                                                <option value="manual_admin_deduction" @selected("manual_admin_deduction" == request("transaction_type"))>{{ trans('manual_admin_deduction') }}</option>
                                                <option value="expiry_deduction" @selected("expiry_deduction" == request("transaction_type"))>{{ trans('expiry_deduction') }}</option>
                                                <option value="reward" @selected("reward" == request("transaction_type"))>{{ trans('reward') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="amount"> @lang("amount") </label>
                                            <input type="number" name="amount" class="form-control filter-input"
                                                placeholder="@lang("search for amount") " value="{{ request("amount") }}">
                                        </div>



                                        <div class="col-md-6 mb-1">
                                            <label for="status">@lang("status")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="status" id="status">
                                                <option value=""> @lang("select status")</option><option value="accepted" @selected("accepted" == request("status")) >{{trans("accepted")}}</option><option value="rejected" @selected("rejected" == request("status")) >{{trans("rejected")}}</option>
                                            </select>
                                        </div>


                                        <div class="col-md-6 mb-1">
                                            <label for="user_id">@lang("user")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="user_id" id="user_id">

                                                <option  value="" > @lang("select users")</option>
                                                @foreach($users as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("user_id")) >@lang($item->fullname)</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-1">
                                            <label for="added_by_id">@lang("added by")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="added_by_id" id="added_by_id">

                                                <option  value="" > @lang("select addedBies")</option>
                                                @foreach($addedBies as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("added_by_id")) >@lang($item->fullname)</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-1">
                                            <label for="package_id">@lang("package")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="package_id" id="package_id">

                                                <option  value="" > @lang("select packages")</option>
                                                @foreach($packages as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("package_id")) >@lang($item->price)</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="order_id">@lang("order")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="order_id" id="order_id">
                                                <option  value="" > @lang("select orders")</option>
                                                @foreach($orders as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("order_id")) >@lang($item->reference_id)</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang("Create Date from") </label>
                                        <input type="datetime-local" name="from_created_at" class="form-control filter-input"
                                            placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
                                            </div>
                                            <div class="col-md-6 mb-1">
                                            <label for="created_at"> @lang("Create Date to") </label>
                                            <input type="datetime-local" name="to_created_at" class="form-control filter-input"
                                                placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
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
                        data-load="{{ route('dashboard.wallet-transactions.index',['trash' => request()->trash]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                    <th class="w-10px pe-2" data-name="select_switch">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                        </div>
                                    </th>
                                    <th class="text-center p-0" data-name="created_at">@lang("added at")</th>
                                    <th class="text-center p-0" data-name="type">@lang("type")</th>
                                    <th class="text-center p-0" data-name="amount">@lang("amount")</th>
                                    <th class="text-center p-0" data-name="wallet_before">@lang("wallet before")</th>
                                    <th class="text-center p-0" data-name="wallet_after">@lang("wallet after")</th>
                                    <th class="text-center p-0" data-name="current_wallet">@lang("current_wallet")</th>
                                    <th class="text-center p-0" data-name="status">@lang("status")</th>
                                    <th class="text-center p-0" data-name="user_id">@lang("client")</th>
                                    <th class="text-center p-0" data-name="added_by_id">@lang("added by")</th>
                                    <th class="text-center p-0" data-name="package_id">@lang("package")</th>
                                    <th class="text-center p-0" data-name="order_id">@lang("order")</th>
                                    <th class="text-center p-0" data-name="expired_at">@lang("expired_at")</th>
                                    <th class="text-center p-0" data-name="actions">@lang("Actions")</th>

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
@endsection
@push('css')

@endpush
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.wallet-transactions.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
</script>
@endpush
