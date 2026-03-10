
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
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"></h1>
                    <!--end::Title-->
                    <!--begin::Separator-->
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <!--end::Separator-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                         <!--begin::Item-->
                         <li class="breadcrumb-item text-muted">
                            <a href=""
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("orders")</li>
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


                    <form class="form" method="POST" id="operation-form" data-id="{{$item->id ?? null}}"
                        @if(isset($item))
                            action="{{ route("dashboard.order-transactions.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.order-transactions.create") }}"
                            data-mode="new"
                        @endif
                        >

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="order_id">{{ trans("order") }}</label>
                                <select class="custom-select  form-select advance-select" name="order_id" id="order_id"  >

                                    <option   value="" >{{trans("select")." ".trans("order")}}</option>
                                    @foreach($orders ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->order_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->phone}}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="type">{{ trans("type") }}</label>
                                <select class="custom-select form-select advance-select" name="type" id="type" required>
                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="cash" @selected(isset($item) and $item->type == 'cash')>{{ trans('cash') }}</option>
                                    <option value="online" @selected(isset($item) and $item->type == 'online')>{{ trans('online') }}</option>
                                    <option value="wallet" @selected(isset($item) and $item->type == 'wallet')>{{ trans('wallet') }}</option>
                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="online_payment_method">{{ trans("online payment method") }}</label>
                                <input type="text" name="online_payment_method" class="form-control "
                                    placeholder="{{ trans("Enter online payment method") }} " value="@if(isset($item)){{ $item->online_payment_method }}@endif">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="amount">{{ trans("amount") }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control "
                                    placeholder="{{ trans("Enter amount") }} " value="{{ old("amount" , $item->amount ?? null) }}" required>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="transaction_id">{{ trans("transaction id") }}</label>
                                <input type="text" name="transaction_id" class="form-control "
                                    placeholder="{{ trans("Enter transaction id") }} " value="@if(isset($item)){{ $item->transaction_id }}@endif">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="notes">{{ trans("notes") }}</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="{{ trans("Enter notes") }}">@if(isset($item)){{ $item->notes }}@endif</textarea>

                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans("Submit") }}</button>
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
