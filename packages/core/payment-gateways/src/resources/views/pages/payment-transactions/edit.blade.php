
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
                        <li class="breadcrumb-item text-muted">@lang("payment-gateways")</li>
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
                            action="{{ route("dashboard.payment-transactions.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.payment-transactions.create") }}"
                            data-mode="new"
                        @endif
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="transaction_id">{{ trans("transaction id") }}</label>
                                <input type="text" name="transaction_id" class="form-control "
                                    placeholder="{{ trans("Enter transaction id") }} " value="@if(isset($item)){{ $item->transaction_id }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="for">{{ trans("for") }}</label>
                                <input type="text" name="for" class="form-control "
                                    placeholder="{{ trans("Enter for") }} " value="@if(isset($item)){{ $item->for }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="status">{{ trans("status") }}</label>
                                <input type="text" name="status" class="form-control "
                                    placeholder="{{ trans("Enter status") }} " value="@if(isset($item)){{ $item->status }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="request_data">{{ trans("request data") }}</label>
                <textarea type="number" name="request_data" class="form-control "
                    placeholder="{{ trans("Enter request data") }} " >@if(isset($item)){{ $item->request_data }}@endif</textarea>
                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="payment_method">{{ trans("payment method") }}</label>
                                <input type="text" name="payment_method" class="form-control "
                                    placeholder="{{ trans("Enter payment method") }} " value="@if(isset($item)){{ $item->payment_method }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="payment_data">{{ trans("payment data") }}</label>
                <textarea type="number" name="payment_data" class="form-control "
                    placeholder="{{ trans("Enter payment data") }} " >@if(isset($item)){{ $item->payment_data }}@endif</textarea>
                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="payment_response">{{ trans("payment response") }}</label>
                <textarea type="number" name="payment_response" class="form-control "
                    placeholder="{{ trans("Enter payment response") }} " >@if(isset($item)){{ $item->payment_response }}@endif</textarea>
                    
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