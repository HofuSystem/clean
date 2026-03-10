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
                            <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('wallet')</li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-dark">{{ $title }}</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->

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

                    <form class="form" method="POST" id="operation-form"
                        redirect-to="{{ route('dashboard.wallet-transactions.index') }}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.wallet-transactions.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.wallet-transactions.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="deposit" @selected(isset($item) and $item->type == 'deposit')>{{ trans('deposit') }}</option>
                                    <option value="withdraw" @selected(isset($item) and $item->type == 'withdraw')>{{ trans('withdraw') }}</option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="amount">{{ trans('amount') }}</label>
                                <input type="number" name="amount" class="form-control "
                                    placeholder="{{ trans('Enter amount') }} "
                                    value="{{ old('amount', $item->amount ?? null) }}">

                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label  for="expired_at">{{ trans('expired at') }}</label>
                                <input type="date" name="expired_at" class="form-control "
                                    placeholder="{{ trans('Enter expired at') }} "
                                    value="{{ old('expired_at', $item->expired_at ?? null) }}">
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="transaction_type">{{ trans('transaction_type') }}</label>
                                <select class="custom-select  form-select advance-select" name="transaction_type" id="transaction_type">
                                    <option value="">{{ trans('transaction type') }}</option>
                                    <option value="withdraw" @selected(isset($item) and $item->transaction_type == 'withdraw')>{{ trans('withdraw') }}</option>
                                    <option value="charge" @selected(isset($item) and $item->transaction_type == 'charge')>{{ trans('charge') }}</option>
                                    <option value="remaining_amount" @selected(isset($item) and $item->transaction_type == 'remaining_amount')>{{ trans('remaining_amount') }}</option>
                                    <option value="compensation_add" @selected(isset($item) and $item->transaction_type == 'compensation_add')>{{ trans('compensation_add') }}</option>
                                    <option value="promotional_add" @selected(isset($item) and $item->transaction_type == 'promotional_add')>{{ trans('promotional_add') }}</option>
                                    <option value="order_payment" @selected(isset($item) and $item->transaction_type == 'order_payment')>{{ trans('order_payment') }}</option>
                                    <option value="cashback" @selected(isset($item) and $item->transaction_type == 'cashback')>{{ trans('cashback') }}</option>
                                    <option value="manual_admin_deduction" @selected(isset($item) and $item->transaction_type == 'manual_admin_deduction')>{{ trans('manual_admin_deduction') }}</option>
                                    <option value="expiry_deduction" @selected(isset($item) and $item->transaction_type == 'expiry_deduction')>{{ trans('expiry_deduction') }}</option>
                                </select>

                            </div>

                           

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="user_id">{{ trans('user') }}</label>
                                <select class="custom-select  form-select advance-select" name="user_id" id="user_id">

                                    <option value="">{{ trans('select user') }}</option>
                                    @foreach ($users ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->user_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->fullname }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="order_id">{{ trans('order') }}</label>
                                <select class="custom-select  form-select advance-select" name="order_id"
                                    id="order_id">

                                    <option value="">{{ trans('select order') }}</option>
                                    @foreach ($orders ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->order_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->reference_id }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="package_id">{{ trans('package') }}</label>
                                <select class="custom-select  form-select advance-select" name="package_id"
                                    id="package_id">

                                    <option value="">{{ trans('select package') }}</option>
                                    @foreach ($packages ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->package_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->price }}</option>
                                    @endforeach

                                </select>

                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('media::mediaCenter.modal')
@endsection
@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
@endpush
