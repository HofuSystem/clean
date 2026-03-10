@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->
        <div class="toolbar my-3" id="kt_toolbar">
            <!--begin::Container-->
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
                            <a href="{{ route('dashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('orders')</li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $order->reference_id }}</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <div class="d-flex align-items-center gap-2 gap-lg-3 ms-auto">
                    @isset($editMode)
                        <!--begin::Primary button-->
                        <a href="#" class="btn fw-bold btn-success" id="addCouponBtn" data-id="{{ $order->id }}">
                            {{ trans('apply coupon') }} </a>
                        <!--end::Primary button-->
                        @if (isset($allowedRepresentatives) and !empty($allowedRepresentatives))
                            <!--begin::Primary button-->
                            <a href="#" class="btn  fw-bold btn-primary" id="assignRepresentativeBtn"
                                data-id="{{ $order->id }}">
                                {{ trans('assign Representative') }} </a>
                            <!--end::Primary button-->
                        @endif
                        @if (isset($allowedRepresentatives) and !empty($allowedRepresentatives))
                            <!--begin::Primary button-->
                            <a href="#" class="btn  fw-bold btn-primary" id="assignOperatorBtn"
                                data-id="{{ $order->id }}">
                                {{ trans('assign Operator') }} </a>
                            <!--end::Primary button-->
                        @endif
                        @can('dashboard.orders.update-delivery-price')
                            <!--begin::Primary button-->
                            <a href="#" class="btn  fw-bold btn-warning" id="updateDeliveryPriceBtn"
                                data-id="{{ $order->id }}">
                                {{ trans('update delivery price') }} </a>
                            <!--end::Primary button-->
                        @endcan
                        @can('dashboard.orders.update-total-provider-invoice')
                            <!--begin::Primary button-->
                            <a href="#" class="btn  fw-bold btn-secondary" id="updateTotalProviderInvoiceBtn"
                                data-id="{{ $order->id }}">
                                {{ trans('update total provider invoice') }} ({{ $order->total_provider_invoice }}) {{ trans('SAR') }} </a>
                            <!--end::Primary button-->
                        @endcan
                        @can('dashboard.orders.change-pay-type')
                        <!--begin::Primary button-->
                        <a href="#" class="btn fw-bold btn-info" id="changePayTypeBtn" data-id="{{ $order->id }}">
                            {{ trans('Change Payment Type') }} </a>
                        <!--end::Primary button-->
                        @endcan
                        @if ($order->reports()->exists())
                            <!--begin::Primary button-->
                            <a href="#" class="btn  fw-bold btn-info" id="returnOrderContinueBtn"
                                data-id="{{ $order->id }}">{{ trans('return order continue') }} </a>
                            <!--end::Primary button-->
                        @endif
                        @if ($order->status == 'issue')
                            <!--begin::Primary button-->
                            <a href="#" class="btn fw-bold btn-warning" id="changeStatusBtn"
                                data-id="{{ $order->id }}">
                                {{ trans('change status') }} </a>
                            <!--end::Primary button-->
                        @endif
                        @if (!in_array($order->status, ['finished', 'delivered']) and $order->admin_cancel_reason == null)
                            <!--begin::Primary button-->
                            <a href="#" class="btn fw-bold btn-danger" id="cancelBtn" data-id="{{ $order->id }}">
                                {{ trans('cancel') }} </a>
                        @endif
                        <!--end::Primary button-->
                        @if ($order->status != 'issue')
                            <!--begin::Primary button-->
                            <a href="#" class="btn fw-bold btn-danger" id="issueStatusBtn" data-id="{{ $order->id }}">
                                {{ trans('issue status') }} </a>
                            <!--end::Primary button-->
                        @endif
                    @endisset
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-body row">
                        <div id="operation-form" data-id="{{ $order->id ?? null }}"
                            action="{{ route('dashboard.orders.edit', $order->id) }}" data-mode="edit">
                        </div>

                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if (!request()->has('tab') or empty(request('tab'))) active @endif"
                                    id="pills-summary-tab" data-bs-toggle="pill" data-bs-target="#pills-summary"
                                    type="button" role="tab" aria-controls="pills-summary"
                                    aria-selected="true">{{ trans('Summary') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if (request('tab') == 'fulfillment-history') active @endif"
                                    id="pills-fulfillment-history-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-fulfillment-history" type="button" role="tab"
                                    aria-controls="pills-fulfillment-history"
                                    aria-selected="false">{{ trans('Fulfillment History') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-payments-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-payments" type="button" role="tab"
                                    aria-controls="pills-payments" aria-selected="false">{{ trans('Payments') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if (request('tab') == 'assignment') active @endif"
                                    id="pills-assignment-tab" data-bs-toggle="pill" data-bs-target="#pills-assignment"
                                    type="button" role="tab" aria-controls="pills-assignment"
                                    aria-selected="false">{{ trans('Assignment') }}</button>
                            </li>

                            <li class="nav-item " role="presentation">
                                <a class="nav-link" href="{{ route('dashboard.orders.invoice', $order->id) }}">
                                    {{ trans('invoice') }}</a>

                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade @if (!request()->has('tab') or empty(request('tab'))) show active @endif"
                                id="pills-summary" role="tabpanel" aria-labelledby="pills-summary-tab" tabindex="0">
                                @include('orders::pages.orders.inc.summary')
                            </div>
                            <div class="tab-pane fade @if (request('tab') == 'fulfillment-history') show active @endif"
                                id="pills-fulfillment-history" role="tabpanel"
                                aria-labelledby="pills-fulfillment-history-tab" tabindex="0">
                                @include('orders::pages.orders.inc.fulfillment')

                            </div>
                            <div class="tab-pane fade @if (request('tab') == 'payments') show active @endif"
                                id="pills-payments" role="tabpanel" aria-labelledby="pills-payments-tab" tabindex="0">
                                @include('orders::pages.orders.inc.payments')

                            </div>
                            <div class="tab-pane fade @if (request('tab') == 'assignment') show active @endif"
                                id="pills-assignment" role="tabpanel" aria-labelledby="pills-assignment-tab"
                                tabindex="0">
                                @include('orders::pages.orders.inc.assignment')

                            </div>

                            <div class="tab-pane fade" id="pills-invoice" role="tabpanel"
                                aria-labelledby="pills-invoice-tab" tabindex="0">invoice</div>
                        </div>
                        @include('comment::inc.comment-section', [
                            'commentUrl' => route('dashboard.orders.comment', $order->id),
                        ])
                    </div>
                    <div class="modal fade" id="order-itemsDeleteModel" aria-labelledby="order-itemsDeleteModelLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title  text-white" id="order-itemsDeleteModelLabel">
                                        {{ trans('Delete OrderItem') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the OrderItem') }} <span></span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{ trans('Close') }}</button>
                                    <button id="order-items-final-delete" type="button"
                                        class="btn btn-danger">{{ trans('Delete') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="order-representativesModal" aria-hidden="true"
                        aria-labelledby="order-representativesModalLabel"
                        data-store="{{ route('dashboard.order-representatives.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h1 class="modal-title  text-white" id="order-representativesModalLabel">
                                        {{ trans('OrderRepresentative') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">
                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="type">{{ trans('type') }}</label>
                                                <select class="custom-select  form-select advance-select" name="type"
                                                    id="order_id-type">

                                                    <option value="">{{ trans('select type') }}</option>
                                                    @if ($order->status == 'issue')
                                                        @if (in_array($order->type, ['clothes', 'sales', 'fastorder']))
                                                            <option value="delivery">
                                                                {{ trans('delivery') }}</option>
                                                            <option value="receiver">
                                                                {{ trans('receiver') }}</option>
                                                        @else
                                                            <option value="technical">
                                                                {{ trans('technical') }}</option>
                                                        @endif
                                                    @else
                                                        @foreach ($allowedRepresentatives ?? [] as $allowedRepresentative)
                                                            <option value="{{ $allowedRepresentative }}">
                                                                {{ trans($allowedRepresentative) }}</option>
                                                        @endforeach
                                                    @endif

                                                </select>

                                            </div>
                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required"
                                                    for="representative_id">{{ trans('representative') }}</label>
                                                <select class="custom-select  form-select advance-select"
                                                    name="representative_id" id="order_id-representative_id">

                                                    <option value="">{{ trans('select representative') }}</option>
                                                    @foreach ($users ?? [] as $sItem)
                                                        <option data-roles="{{ $sItem->roles->pluck('name')->toJson() }}"
                                                            value="{{ $sItem->id }}">{{ $sItem->fullname." - ".$sItem->phone }}</option>
                                                    @endforeach

                                                </select>

                                            </div>



                                            <div class="form-group mb-3 col-md-4">
                                                <label class=""
                                                    for="selected_date">{{ trans('selected date') }}</label>
                                                <select class="custom-select  form-select advance-select"
                                                    name="selected_date" id="order_id-selected_date">

                                                </select>
                                            </div>
                                            <div class="form-group mb-3 col-md-8">
                                                <label class=""
                                                    for="selected_time">{{ trans('selected time') }}</label>
                                                <select class="custom-select  form-select advance-select"
                                                    name="selected_time" id="order_id-selected_time">

                                                </select>
                                            </div>
                                            <div class="form-group mb-3 col-md-4">
                                                <label class="" for="date">{{ trans('date') }}</label>
                                                <input type="date" name="date" class="form-control "
                                                    placeholder="{{ trans('Enter date') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-4">
                                                <label class="" for="time">{{ trans('time') }}</label>
                                                <input type="time" name="time" class="form-control "
                                                    placeholder="{{ trans('Enter time') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-4">
                                                <label class="" for="to_time">{{ trans('to time') }}</label>
                                                <input type="time" name="to_time" class="form-control "
                                                    placeholder="{{ trans('Enter to time') }} " value="">

                                            </div>

                                            <div class="map-container">
                                                <h4>{{ trans('use the map to find the location') }}</h4>
                                                <div class="row">

                                                    <div class="form-group mb-3 col-md-6">
                                                        <label class="" for="lat">{{ trans('lat') }}</label>
                                                        <input type="text" id="lat" name="lat"
                                                            class="form-control lat"
                                                            placeholder="{{ trans('Enter lat') }} ">

                                                    </div>

                                                    <div class="form-group mb-3 col-md-6">
                                                        <label class="" for="lng">{{ trans('lng') }}</label>
                                                        <input type="text" id="lng" name="lng"
                                                            class="form-control lng"
                                                            placeholder="{{ trans('Enter lng') }} ">

                                                    </div>
                                                </div>

                                                <input type="text" class="search-box"
                                                    placeholder="{{ trans('Enter an address') }}...">
                                                <div class="suggestions"></div>
                                                <div class="map"></div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="location">{{ trans('location') }}</label>
                                                <input type="text" name="location" class="form-control "
                                                    placeholder="{{ trans('Enter location') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-12" style="display: none">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="has_problem"
                                                        name="has_problem">
                                                    <label class="form-check-label"
                                                        for="has_problem">{{ trans('has problem') }}</label>
                                                </div>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="for_all_items"
                                                        name="for_all_items">
                                                    <label class="form-check-label"
                                                        for="for_all_items">{{ trans('for_all items') }}</label>
                                                </div>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="" for="items">{{ trans('items') }}</label>
                                                <select class="custom-select  form-select advance-select" name="items"
                                                    id="order_id-items" multiple>

                                                    <option value="">{{ trans('select items') }}</option>
                                                    @foreach ($items ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}"
                                                            value="{{ $sItem->id }}">
                                                            {{ "$sItem->id : {$sItem->product?->name} : quantity $sItem->quantity : width $sItem->width : height $sItem->height" }}
                                                        </option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="col-lg-9 ml-lg-auto">
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="order-representativesDeleteModel"
                        aria-labelledby="order-representativesDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white" id="order-representativesDeleteModelLabel">
                                        {{ trans('Delete OrderRepresentative') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the OrderRepresentative') }} <span></span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{ trans('Close') }}</button>
                                    <button type="button"
                                        class="btn btn-danger items-final-delete">{{ trans('Delete') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="order-reportsModal" aria-hidden="true"
                        aria-labelledby="order-reportsModalLabel"
                        data-store="{{ route('dashboard.order-reports.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h1 class="modal-title text-white" id="order-reportsModalLabel">
                                        {{ trans('OrderReport') }}
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">

                                            <div class="form-group mb-3 col-md-4">
                                                <label class="required" for="user_id">{{ trans('user') }}</label>
                                                <select class="custom-select  form-select advance-select" name="user_id"
                                                    id="order_id-user_id">

                                                    <option value="">{{ trans('select user') }}</option>
                                                    @foreach ($users ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}"
                                                            value="{{ $sItem->id }}">{{ $sItem->fullname." - ".$sItem->phone }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-4">
                                                <label class="required"
                                                    for="report_reason_id">{{ trans('report reason') }}</label>
                                                <select class="custom-select  form-select advance-select"
                                                    name="report_reason_id" id="order_id-report_reason_id">

                                                    <option value="">{{ trans('select report reason') }}</option>
                                                    @foreach ($reportReasons ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}"
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class=""
                                                    for="desc_location">{{ trans('desc location') }}</label>
                                                <textarea type="number" name="desc_location" class="form-control "
                                                    placeholder="{{ trans('Enter desc location') }} "></textarea>

                                            </div>

                                            <div class="col-lg-9 ml-lg-auto">
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="order-reportsDeleteModel" aria-labelledby="order-reportsDeleteModelLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white" id="order-reportsDeleteModelLabel">
                                        {{ trans('Delete OrderReport') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the OrderReport') }} <span></span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{ trans('Close') }}</button>
                                    <button type="button"
                                        class="btn btn-danger items-final-delete">{{ trans('Delete') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic modal -->
                    <div class="modal fade text-left" id="updateQty" role="dialog" aria-labelledby="myModalLabel120"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white" id="myModalLabel120">
                                        {{ trans('view the history and update of the qty') }}</h5>
                                    <button class="btn btn-danger" type="button" class="close" data-bs-dismiss="modal"
                                        aria-bs-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="card row">
                                    <div class="card-body row">
                                        <div class="form-group col-12">
                                            <label for="new_qty">{{ trans('new qty') }}</label>
                                            <input type="text" name="new_qty" id="new_qty" class="form-control"
                                                placeholder="" aria-describedby="helpId">
                                        </div>
                                        <div class="form-group col-12 my-2">
                                            <button class="btn btn-primary"
                                                action="{{ route('dashboard.orders.item.edit', ['id' => $order->id, 'itemId' => '%s']) }}"
                                                id="updateQtyBtn">{{ trans('update qty') }}</button>
                                        </div>
                                        <div class="table-responsive" id="updates-table">
                                            <table class="table  align-middle">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th>{{ trans('from') }}</th>
                                                        <th>{{ trans('to') }}</th>
                                                        <th>{{ trans('updated by') }}</th>
                                                        <th>{{ trans('updated at') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-group-divider">


                                                </tbody>
                                                <tfoot>

                                                </tfoot>
                                            </table>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Basic modal -->
                    <div class="modal fade text-left" id="updateSize" role="dialog" aria-labelledby="myModalLabel120"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white" id="myModalLabel120">{{ trans('update size') }}
                                    </h5>
                                    <button class="btn btn-danger" type="button" class="close" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="card row">
                                    <div class="card-body row">
                                        <div class="form-group col-6">
                                            <label for="new_width">{{ trans('new width in meter') }}</label>
                                            <input type="text" name="new_width" id="new_width" class="form-control"
                                                placeholder="" aria-describedby="helpId">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="new_height">{{ trans('new height in meters') }}</label>
                                            <input type="text" name="new_height" id="new_height" class="form-control"
                                                placeholder="" aria-describedby="helpId">
                                        </div>

                                        <div class="form-group col-12 my-2">
                                            <button class="btn btn-primary" id="updateSizeBtn"
                                                action="{{ route('dashboard.orders.item.edit', ['id' => $order->id, 'itemId' => '%s']) }}">{{ trans('update size') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Basic modal -->
                    <div class="modal fade text-left" id="addItem" role="dialog" aria-labelledby="myModalLabel120"
                        aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable"
                            role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white" id="myModalLabel120">{{ trans('add') }}
                                    </h5>
                                    <button type="button" class="close btn btn-danger" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="card">
                                    <div class="card-body row" id="filter-products">
                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4 class="card-title">{{ trans('cart') }}</h4>
                                                    <div id="cart-items"></div>
                                                    <button type="submit" id="saveCart"
                                                        class="btn btn-primary mx-auto">{{ trans('save cart') }}</button>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-xl-9 col-lg-8 col-md-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4 class="card-title">
                                                        {{ trans('add product to order') }}</h4>
                                                </div>
                                                <div class="col-12 ">
                                                    <label for="search">{{ trans('search for the product') }}</label>
                                                    <input type="text" class="form-control" name="search"
                                                        id="search">
                                                </div>
                                                <div class="form-group mt-2 col-6 select-parent">
                                                    <label for="">{{ trans('choose a category') }}</label>
                                                    <select class="advance-select form-control" name="category_id"
                                                        aria-label="Default select example" required>
                                                        <option value="">
                                                            {{ trans('choose a category') }}</option>
                                                        @foreach ($categories ?? [] as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group mt-2  col-6 select-parent">
                                                    <label for="">{{ trans('choose sub category') }}</label>

                                                    <select class="advance-select form-control" name="sub_category_id"
                                                        aria-label="Default select example" required>
                                                        <option value="">
                                                            {{ trans('choose sub category') }}</option>
                                                    </select>
                                                </div>

                                            </div>
                                            <div id="search-results" class="row">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('notification::inc.notifyModal')
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('media::mediaCenter.modal')
    @include('orders::pages.orders.inc.assign-representatives-modal')
    @include('orders::pages.orders.inc.assign-operators-modal')
    @include('orders::pages.orders.inc.update-delivery-price-modal')
    @include('orders::pages.orders.inc.update-total-provider-invoice-modal')
    @include('orders::pages.orders.inc.change-status-modal')
    @include('orders::pages.orders.inc.cancel-modal')
    @include('orders::pages.orders.inc.issue-status-modal')
    @include('orders::pages.orders.inc.return-order-continue-modal')
    @include('orders::pages.orders.inc.applay-copoun-modal')
    @include('orders::pages.orders.inc.change-pay-type-modal')
@endsection
@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
    <style>
        thead th {
            font-size: 12px !important
        }

        /* Custom CSS to enhance borders between cells */
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
            border-bottom: 1px solid #dee2e6 !important;
            /* Ensure borders are visible */
        }

        .table thead th {
            font-size: 0.9rem;
            /* Smaller font size for the title */
        }

        .table tbody th,
        .table tbody td {
            font-size: 1rem;
            /* Default font size for values */
        }

        .table-bordered th[scope="row"] {
            width: 30%
        }

        .table-bordered tr {
            border: 1px solid #dee2e6 !important;
        }

        .table-bordered {
            border: 1px solid #dee2e6 !important;
        }

        #search-results {
            height: 70vh;
            overflow-x: hidden;
        }

        #cart-items {
            height: 70vh;
            overflow-x: hidden;
        }

        img.card-img-top {
            width: 150px;
            height: 100px;
            object-fit: cover
        }


        @media screen and (max-width: 480px) {
            img.card-img-top {
                width: 100px;
                height: 70px;
                object-fit: cover
            }

            .card .card-title {
                font-size: 1rem;
                margin-bottom: 1rem;
            }

            .input-group-prepend,
            .input-group-append {
                font-size: 10px;
            }

            .input-group-prepend button,
            .input-group-append button {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px;
            }

            .input-group-prepend input {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px !important;
            }

            #search-results {
                height: 240px !important;
                overflow-x: hidden;
            }

            #cart-items {
                height: 200px !important;
                overflow-x: hidden;
            }

            #addItem .card {
                padding: 0px 5px !important
            }

            #addItem .card-body {
                padding: 5px 0px !important
            }

            #addItem .card p {
                margin: 0px 5px !important
            }

            #saveCart {
                margin-top: 15px
            }

            .select-parent {
                margin: 5px 0px
            }
        }


        @media screen and (max-height: 700px) {
            img.card-img-top {
                width: 70px;
                height: 50px;
                object-fit: cover
            }

            .card>* {
                font-size: 0.8rem !important;
            }

            .input-group-prepend,
            .input-group-append {
                font-size: 10px;
            }

            .input-group-prepend button,
            .input-group-append button {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px;
            }

            .input-group-prepend input {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px !important;
            }

            #addItem .card {
                padding: 0px 5px !important
            }

            #addItem .card-body {
                padding: 5px 0px !important
            }

            #addItem .card p {
                margin: 0px 5px !important
            }

            #saveCart {
                margin-top: 15px
            }

            .select-parent {
                margin: 5px 0px
            }
        }

        .appointment-card {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .appointment-card.active {
            background: #28a745;
            color: white;
        }

        /* Add a visual mark for cards that are in contract */
        .in-contract::after {
            content: "In Contract";
            display: block;
            position: absolute;
            top: 30px;
            right: -15px;
            background: #28a745;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 2px 32px;
            border-radius: 4px;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            pointer-events: none;
            transform: rotate(45deg);
            width: fit-content;
            text-align: center;
        }

        .in-contract {
            position: relative;
            overflow: hidden;
        }
    </style>
@endpush
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    @isset($editMode)
        <script>
            $(document).ready(function() {
                $('#order_id-items').parent().hide();
                $('#for_all_items').change(function(e) {
                    e.preventDefault();
                    let checked = $(this).is(':checked');
                    if (checked) {
                        $('#order_id-items').parent().hide();
                    } else {
                        $('#order_id-items').parent().show();
                    }
                });
                $('#assignRepresentativeBtn').click(function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    id = JSON.stringify([id]);

                    $('#assignRepresentativesModal #for').val(id);
                    $('#assignRepresentativesModal').modal('show');
                })
                $('#assignOperatorBtn').click(function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    id = JSON.stringify([id]);

                    $('#assignOperatorsModal #for').val(id);
                    $('#assignOperatorsModal').modal('show');
                })
                $('#addCouponBtn').click(function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    id = JSON.stringify([id]);

                    $('#applyCouponModal #for').val(id);
                    $('#applyCouponModal').modal('show');
                })
                $('#updateDeliveryPriceBtn').click(function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    id = JSON.stringify([id]);

                    $('#updateDeliveryModal #for').val(id);
                    $('#updateDeliveryModal').modal('show');
                })
                $('#updateTotalProviderInvoiceBtn').click(function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    id = JSON.stringify([id]);

                    // Set current total_provider_invoice if exists
                    let currentTotalProviderInvoice = '{{ $order->total_provider_invoice ?? "" }}';
                    if (currentTotalProviderInvoice) {
                        $('#updateTotalProviderInvoiceModal #total_provider_invoice').val(currentTotalProviderInvoice);
                    }

                    $('#updateTotalProviderInvoiceModal #for').val(id);
                    $('#updateTotalProviderInvoiceModal').modal('show');
                })
                $('#changePayTypeBtn').click(function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    id = JSON.stringify([id]);

                    // Set current pay_type if exists
                    let currentPayType = '{{ $order->pay_type ?? "" }}';
                    if (currentPayType) {
                        $('#changePayTypeModal #order_id-pay_type').val(currentPayType);
                    }

                    $('#changePayTypeModal #for').val(id);
                    $('#changePayTypeModal').modal('show');
                })
                $('#changeStatusBtn').click(function(e) {
                    e.preventDefault();
                    $('#changeStatusModal').modal('show');
                })
                $('#cancelBtn').click(function(e) {
                    e.preventDefault();
                    $('#cancelModal').modal('show');
                })
                $('#issueStatusBtn').click(function(e) {
                    e.preventDefault();
                    $('#issueStatusModal').modal('show');
                })
                $('#returnOrderContinueBtn').click(function(e) {
                    e.preventDefault();
                    $('#returnOrderContinueModal').modal('show');
                })
            });
        </script>
        {{-- cart  items --}}
        <script>
            let hasSize = {!! json_encode($hasSize) !!};
            let allProducts = {!! json_encode($products) !!};
            let allSubCategories = {!! json_encode($subCategories) !!};
            var allUsers = [];
            var dateTimes = [];
            var opening = false;
            // program to generate random strings

            // declare all characters
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            function generateString(length) {
                let result = ' ';
                const charactersLength = characters.length;
                for (let i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }

                return result;
            }

            $(document).ready(function() {
                localStorage.setItem("cartItems", "[]");
                $('#order-representativesModal [name=representative_id] option').each(function(e) {
                    let user = {};
                    user.id = $(this).val();
                    user.name = $(this).text();
                    user.roles = $(this).data('roles');
                    allUsers.push(user);
                });

                $('#order-representativesModal [name=type]').change(function(e) {
                    e.preventDefault();
                    let type = $(this).val();


                    let selectedUser = $('#order-representativesModal [name=representative_id]').val()
                    $('#order-representativesModal [name=representative_id]').empty()

                    $.each(allUsers, function(index, user) {

                        if (type == "delivery" && user.roles && user.roles.includes('driver')) {
                            $('#order-representativesModal [name=representative_id]').append(new Option(
                                user.name, user.id));
                        } else if (type == "receiver" && user.roles && user.roles.includes('driver')) {
                            $('#order-representativesModal [name=representative_id]').append(new Option(
                                user.name, user.id));
                        } else if (type == "technical" && user.roles && user.roles.includes(
                                'technical')) {
                            $('#order-representativesModal [name=representative_id]').append(new Option(
                                user.name, user.id));
                        }
                    });
                    $('#order-representativesModal [name=representative_id]').val(selectedUser).change()

                });
                $('.edit-item ,.create-new-items').click(function(e) {
                    e.preventDefault();
                    let url = "{{ route('dashboard.orders.get-date-times') }}"
                    let ids = [];
                    $('#products-table tbody tr').each(function(index, element) {
                        let id = $(this).data('id');
                        if (id) {
                            ids.push(id);
                        }

                    });
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: {
                            ids: ids
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status) {
                                dateTimes = response.dateTimes;
                                $('#order_id-selected_date').empty()
                                $.each(dateTimes, function(key, value) {
                                    $('#order_id-selected_date').append(new Option(key,
                                        key));
                                });
                                $('#order_id-selected_date').val("").trigger('change')
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Code to run if the request fails
                            response = JSON.parse(jqXHR.responseText);
                            Swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-success",
                                }
                            })
                            $.each(response.errors, function(key, array) {
                                $.each(array, function(index, error) {
                                    toastr.error(error, key);
                                });
                            });
                        }
                    });


                });
                $('#order_id-selected_date').change(function() {
                    let selectedDate = $(this).val();
                    if (selectedDate) {
                        $('#order-representativesModal [name=date]').val(selectedDate)
                        let times = dateTimes[selectedDate];
                        $('#order_id-selected_time').empty();
                        $.each(times, function(key, value) {
                            $('#order_id-selected_time').append(new Option(value.value, value.key));
                        });
                        $('#order_id-selected_time').val("").trigger('change')
                    }

                });
                $('#order_id-selected_time').change(function() {
                    let selectedTime = $(this).val();
                    if (selectedTime) {
                        let [from, to] = selectedTime.split("-");
                        $('#order-representativesModal [name=time]').val(from)
                        $('#order-representativesModal [name=to_time]').val(to)
                    }

                });
                $('.notify-item').click(function(e) {
                    e.preventDefault();
                    $('#notifyModal').modal('show')
                    $('#notifyModal [name=for]').val('users')
                    let ids = [$(this).data('representative-id')];
                    ids = JSON.stringify(ids)
                    $('#notifyModal [name=for_data]').val(ids)
                })
            });
            $(document).on("click", ".btn#increase", function() {               
                let $input = $(this).closest(".input-group").find("input[name='quantity']");
                let currentValue = parseInt($input.val(), 10) || 0;
                let step = parseInt($input.attr("step"), 10) || 1;
                $input.val(currentValue + step).trigger('change');
            });

            $(document).on("click", ".btn#decrease", function() {
                let $input = $(this).closest(".input-group").find("input[name='quantity']");
                let currentValue = parseInt($input.val(), 10) || 0;
                let step = parseInt($input.attr("step"), 10) || 1;
                let min = parseInt($input.attr("min"), 10) || 0;
                $input.val(Math.max(currentValue - step, min)).trigger('change');
            });

            function calculateSize(row) {
                let height = parseFloat(row.find('.height').val()) || 0;
                let width = parseFloat(row.find('.width').val()) || 0;
                let size = height * width;
                row.find('.size').val(size);
            }

            $(document).on('input', '.height, .width', function() {
                let row = $(this).closest('.calculator');
                calculateSize(row);
            });



            function loadProducts() {
                $("#search-results").html("");

                products = allProducts.filter(function(product) {
                    let title = $('#filter-products [name=search]').val()
                    title = (title.length && product.name.includes(title))


                    let category_id = $('#filter-products [name=category_id]').val()
                    let category = product.category_id == category_id;

                    let sub_category_id = $('#filter-products [name=sub_category_id]').val()
                    let sub_category = true
                    if (sub_category_id.length) {
                        sub_category = (product.sub_category_id == sub_category_id) ? true : false
                    }

                    return (title || (category && sub_category))
                }).sort(function(a, b) {
                    // Sort descending: in_contract=1 first, then 0
                    return (b.in_contract || 0) - (a.in_contract || 0);
                });

                products.forEach(product => {
                    product = drawProduct(product);
                    $("#search-results").append(product);
                });

            }

            function loadSubCategories() {
                $('#filter-products [name=sub_category_id]').empty();
                $('#filter-products [name=sub_category_id]').append(
                    '<option value="">{{ trans('sub_category') }}</option>');

                subCategories = allSubCategories.filter(function(item) {
                    return (item.parent_id == $('#filter-products [name=category_id]').val())
                })


                if (subCategories.length) {
                    subCategories.forEach(item => {
                        let newOption = $('<option>', {
                            value: item.id,
                            text: item.name
                        });
                        $('#filter-products [name=sub_category_id]').append(newOption).trigger('change');
                    });
                }

            }

            function drawProduct(product) {
                let sizeHtml = "";

                if (hasSize.includes(product.category_id) || hasSize.includes(product.sub_category_id)) {
                    sizeHtml = `
                    <div class="row calculator my-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('Height') }}</label>
                            <input name="height" type="number" class="form-control height" placeholder="Enter height">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Width</label>
                            <input name="width" type="number" class="form-control width" placeholder="Enter width">
                        </div>

                    </div>
                    `;
                }
                let category = (product.category) ?
                    `<span class="badge bg-success">${product.category}</span>` :
                    "";
                let subCategory = (product.sub_category) ?
                    `<span class="badge bg-warning text-dark">${product.sub_category}</span>` : "";
                let classForContract = product.in_contract ? "in-contract" : "";
                return `
                <div class="card p-2 col-xl-3 col-lg-4 col-md-4 col-6 ${classForContract}">
                    <img class="card-img-top mx-auto "
                        src="${product.image}"
                        alt="Card image cap">
                    <div class="card-body row ">
                        <div class="col-12">
                            <h5 class="card-title">${product.name}</h5>
                            <p>${product.price} {{ trans('SAR') }} </p>
                            ${category}
                            ${subCategory}
                        </div>
                        <div class="col-12 mt-2">
                            <form  class="add-to-cart-form" action="#" method="post">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-secondary text-white" type="button" id="increase"
                                                >
                                                <i class="fas fa-plus-square"></i>
                                            </button>
                                        </div>
                                        <input type="number" id="quantity" class="text-center form-control"
                                            aria-live="polite" data-bs-step="counter" name="quantity"
                                            title="quantity" value="1" min="0" step="1"
                                            data-bs-round="0" aria-label="Quantity selector">
                                        <div class="input-group-append" id="button-addon4">
                                            <button class="btn btn-secondary text-white" type="button" id="decrease"
                                                >
                                                <i class="fas fa-minus-square"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <input type="number" id="item_id" name="item_id" value="${product.id}" hidden>
                                ${sizeHtml}
                                <button type="submit" class="btn btn-primary mx-auto">{{ trans('add') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            }

            function drawCartItems() {
                let cartItems = localStorage.getItem("cartItems");
                cartItems = JSON.parse(cartItems);
                $('#cart-items').html("");
                for (const property in cartItems) {
                    let cartItem = cartItems[property];
                    cartItem = drawCartItem(cartItem)
                    $('#cart-items').append(cartItem);

                }
            }

            function drawCartItem(cartItem) {
                let category = (cartItem.product.category) ?
                    `<span class="badge bg-success">${cartItem.product.category}</span>` :
                    "";
                let subCategory = (cartItem.product.sub_category) ?
                    `<span class="badge bg-warning text-dark">${cartItem.product.sub_category}</span>` : "";
                let id = cartItem.product.id;
                let cartItemId = cartItem.cartItemId;
                let name = cartItem.product.name;
                let price = cartItem.product.price;
                let image = cartItem.product.image;
                let qty = cartItem.qty;

                let size = "";
                let sizeHtml = "";

                if (hasSize.includes(cartItem.product.category_id) || hasSize.includes(cartItem.product.sub_category_id)) {
                    size = (cartItem.width && cartItem.height) ? (parseFloat(cartItem.width) * parseFloat(cartItem.height)) +
                        " M" : "";

                }

                let classForContract = cartItem.product.in_contract ? "in-contract" : "";

                return `
                        <div class="card p-2 col-12 ${classForContract}">
                            <img class="card-img-top mx-auto "
                                src="${image}"
                                alt="Card image cap">
                            <div class="card-body row ">
                                <div class="col-12">
                                    <h5 class="card-title">${name}</h5>
                                    <p>unit price ${price} {{ trans('SAR') }} </p>
                                    <p>size : ${size}</p>
                                    ${category}
                                    ${subCategory}
                                </div>
                                ${sizeHtml}
                                <div class="col-12 mt-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-secondary text-white" type="button" id="increase"
                                                        data-id="${cartItemId}"
                                                       >
                                                        <i class="fas fa-plus-square"></i>
                                                    </button>
                                                </div>

                                                <input value="${qty}" type="number" id="quantity" class="text-center form-control"
                                                    aria-live="polite" data-bs-step="counter" name="quantity"
                                                    title="quantity" value="1" min="0" step="1"
                                                    data-bs-round="0" aria-label="Quantity selector"
                                                    data-id="${cartItemId}"
                                                    >

                                                <div class="input-group-append" id="button-addon4">
                                                    <button class="btn btn-secondary text-white" type="button" id="decrease"
                                                        data-id="${cartItemId}"
                                                       >
                                                        <i class="fas fa-minus-square"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <button data-id="${cartItemId}" type="submit" class="btn btn-danger mx-auto cart-delete mt-1">{{ trans('delete') }}</button>
                                        </div>
                                </div>
                            </div>
                        </div>
                        `;
            }

            function convertFormToJsObject(form) {
                const formData = new FormData(form);
                const formObject = {};
                for (const [key, value] of formData.entries()) {
                    formObject[key] = value;
                }
                return formObject;
            }
            $(document).on('click', '.delete-cart-item', function(e) {
                let itemsId = $(this).data('id');
                let cartItems = localStorage.getItem('cartItems');
                cartItems = JSON.parse(cartItems)
                delete cartItems[itemsId]
                cartItems = JSON.stringify(Object.assign({}, cartItems));
                localStorage.setItem("cartItems", cartItems);
                drawCartItems()
            });

            function calculateSize(row) {
                let height = parseFloat(row.find('.height').val()) || 0;
                let width = parseFloat(row.find('.width').val()) || 0;
                let size = height * width;
                row.find('.size').val(size);
            }

            $(document).on('input', '.height, .width', function() {
                let row = $(this).closest('.calculator');
                calculateSize(row);
            });

            $(document).on('input', '#filter-products [name=search]', function() {
                let value = $(this).val();
                if (value.length) {
                    $('.select-parent').hide(500);
                    $('.select-parent select').val("").trigger('change');
                } else {
                    $('.select-parent').show(500);
                }
                loadProducts();
            });
            $(document).on('change', '#filter-products [name=sub_category_id]', function() {
                loadProducts();
            });
            $(document).on('change', '#filter-products [name=category_id]', function() {
                loadSubCategories();
                loadProducts();
            });

            $(document).on('submit', '.add-to-cart-form', function(e) {
                e.preventDefault();
                let cartItemId = null;
                let data = convertFormToJsObject($(this)[0]);
                let url = $(this).attr('action');
                let itemsId = $(this).find('#item_id').val();
                let qty = $(this).find('#quantity').val();
                let width = $(this).find('[name=width]').val();
                let height = $(this).find('[name=height]').val();
                let cartItems = localStorage.getItem('cartItems');
                if (cartItems != null) {
                    cartItems = JSON.parse(cartItems);
                } else {
                    cartItems = {};
                }

                let cartItem = null;
                if (typeof cartItems === "object") {
                    for (const property in cartItems) {
                        let item = cartItems[property];

                        if (item.product.id == itemsId && item.height == height && item.width == width) {
                            cartItem = item;
                        }
                    }
                }


                let product = allProducts.filter(function(product) {
                    return (product.id == itemsId);
                })[0];

                if (cartItem) {
                    cartItem.qty = (parseInt(cartItem.qty) + parseInt(qty));
                    cartItemId = cartItem.cartItemId;
                } else if (product) {
                    cartItemId = generateString(15);
                    cartItem = {};
                    cartItem.cartItemId = cartItemId;
                    cartItem.product = product
                    cartItem.qty = qty
                    cartItem.width = width
                    cartItem.height = height
                }


                if (cartItem) {
                    cartItems[cartItemId] = cartItem;
                    cartItems = JSON.stringify(Object.assign({}, cartItems));
                    localStorage.setItem("cartItems", cartItems);
                    drawCartItems()
                }


            });
            $(document).on('change', '#cart-items [name=quantity]', function(e) {
                e.preventDefault();
                let value           = $(this).val();
               
                
                if(value > 0){
                    let itemsId         = $(this).data('id');
                    let cartItems       = localStorage.getItem('cartItems');
                    cartItems           = JSON.parse(cartItems);
                    cartItem            = cartItems[itemsId];
                    cartItem.qty        = $(this).val();
                    cartItems[itemsId]  = cartItem;
                    cartItems           = JSON.stringify(Object.assign({}, cartItems));
                    localStorage.setItem("cartItems", cartItems);

                }

            });
            $(document).on('click', '.cart-delete', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let cartItems = localStorage.getItem("cartItems");
                cartItems = JSON.parse(cartItems);
                delete cartItems[id];
                cartItems = JSON.stringify(Object.assign({}, cartItems));
                localStorage.setItem("cartItems", cartItems);
                drawCartItems();
            });
            $(document).on('click', '#saveCart', function(e) {
                e.preventDefault();
                let items = [];
                let cartItems = localStorage.getItem("cartItems");
                cartItems = JSON.parse(cartItems);
                $('#cart-items').html("");
                for (const property in cartItems) {
                    let cartItem = cartItems[property];
                    let item = {};
                    item.id = cartItem.product.id
                    item.quantity = cartItem.qty
                    item.width = cartItem.width
                    item.height = cartItem.height
                    items.push(item)
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('dashboard.orders.edit', $order->id) }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        items: items
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#addItem").modal('hide');
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        })

                        if (response.remade) {
                            $('#remade').replaceWith(response.remade)
                        }

                        localStorage.setItem("cartItems", '[]');
                        drawCartItems();
                        $('#filter-products select,#filter-products input').val(null).trigger('change');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Code to run if the request fails
                        response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        })
                        $.each(response.errors, function(key, array) {
                            $.each(array, function(index, error) {
                                toastr.error(error, key);
                            });
                        });
                    }
                });

            });
            $(document).on("click", ".open-modal-add", function(e) {
                e.preventDefault()
                var orderId = $(this).data('id');
                $(".modal-body #order_id").val(orderId);
                $('#addItem').modal('show');
            });

            function convertFormToJsObject(form) {
                const formData = new FormData(form);
                const formObject = {};
                for (const [key, value] of formData.entries()) {
                    formObject[key] = value;
                }
                return formObject;
            }

            function formatDate(isoDate) {
                const date = new Date(isoDate);
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                    timeZoneName: 'short'
                };
                return date.toLocaleString('en-US', options);
            }

            //update order qty
            $(document).on('click', '.edit-quantity', function(e) {
                e.preventDefault();
                $('#updates-table tbody').html("");
                let id = $(this).data('id');
                let quantity = $(this).data('quantity');
                let updates = $(this).data('updates');
                $.each(updates, function(indexInArray, valueOfElement) {
                    $('#updates-table tbody').append(`
                        <tr >
                            <td>${valueOfElement.from}</td>
                            <td>${valueOfElement.to}</td>
                            <td>${valueOfElement.updater_email}</td>
                            <td>${formatDate(valueOfElement.created_at)}</td>
                        </tr>
                    `);
                });

                $('[name=new_qty]').val(quantity);
                $("#updateQty").modal('show');
                $('#updateQtyBtn').data('id', id);

            });
            $(document).on('click', '#updateQtyBtn', function(e) {
                e.preventDefault();
                let url = $('#updateQtyBtn').attr('action');
                let id = $('#updateQtyBtn').data('id');
                let width = $('.edit-size.btn[data-id="' + id + '"]').data("width");
                let height = $('.edit-size.btn[data-id="' + id + '"]').data("height");
                url = url.replace('%s', id);
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                        quantity: $('#new_qty').val(),
                        width: width,
                        height: height,
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#updateQty").modal('hide');
                        $("#updateSize").modal('hide');
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        }).then(function(result) {

                            if (response.remade) {
                                $('#remade').replaceWith(response.remade)
                            }
                        })

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Code to run if the request fails
                        response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        })
                        $.each(response.errors, function(key, array) {
                            $.each(array, function(index, error) {
                                toastr.error(error, key);
                            });
                        });
                    },
                });

            });
            $(document).on('click', '.edit-size', function(e) {
                e.preventDefault();
                $('#updates-table tbody').html("");
                let id = $(this).data('id');
                let width = $(this).data('width');
                let height = $(this).data('height');
                $('#updateSizeBtn').data('id', id);
                $('[name=new_width]').val(width);
                $('[name=new_height]').val(height);
                $("#updateSize").modal('show');
            });
            $(document).on('click', '.btn-delete-item', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                $('#order-items-final-delete').data('action', url)
                $("#order-itemsDeleteModel").modal('show');
            });

            $(document).on('click', '#updateSizeBtn', function(e) {
                e.preventDefault();
                let url = $('#updateSizeBtn').attr('action');
                let id = $('#updateSizeBtn').data('id');
                let quantity = $('.edit-quantity[data-id="' + id + '"]').data("quantity");
                let width = $('#new_width').val();
                let height = $('#new_height').val();
                url = url.replace('%s', id);
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                        size: $('#new_size').val(),
                        quantity: quantity,
                        width: width,
                        height: height,
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#updateQty").modal('hide');
                        $("#updateSize").modal('hide');
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        }).then(function(result) {

                            if (response.remade) {
                                $('#remade').replaceWith(response.remade)
                            }
                        })

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Code to run if the request fails
                        response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        })
                        $.each(response.errors, function(key, array) {
                            $.each(array, function(index, error) {
                                toastr.error(error, key);
                            });
                        });
                    },
                });

            });
            $(document).on('click', '#order-items-final-delete', function(e) {
                e.preventDefault();
                let url = $('#order-items-final-delete').data('action');

                $.ajax({
                    type: "Delete",
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#order-itemsDeleteModel").modal('hide');
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        }).then(function(result) {
                            if (response.remade) {
                                $('#remade').replaceWith(response.remade)
                            }
                        })

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Code to run if the request fails
                        $("#order-itemsDeleteModel").modal('hide');
                        response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        })
                        $.each(response.errors, function(key, array) {
                            $.each(array, function(index, error) {
                                toastr.error(error, key);
                            });
                        });
                    },
                });

            });
        </script>
    @endisset

@endpush
