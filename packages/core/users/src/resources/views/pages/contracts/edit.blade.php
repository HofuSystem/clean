
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
                        <li class="breadcrumb-item text-muted">@lang("users")</li>
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
                            action="{{ route("dashboard.contracts.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.contracts.create") }}"
                            data-mode="new"
                        @endif
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="title">{{ trans("title") }}</label>
                                <input type="text" name="title" class="form-control "
                                    placeholder="{{ trans("Enter title") }} " value="@if(isset($item)){{ $item->title }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="months_count">{{ trans("months count") }}</label>
                                <input type="number" name="months_count" class="form-control " step="any" min="0"
                                    placeholder="{{ trans("Enter months count") }} " value="{{ old("months_count" , $item->months_count ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="month_fees">{{ trans("month fees") }}</label>
                                <input type="number" name="month_fees" class="form-control " step="any" min="0"
                                    placeholder="{{ trans("Enter month fees") }} " value="{{ old("month_fees" , $item->month_fees ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="max_allowed_over_price">{{ trans("max allowed over price") }}</label>
                                <input type="number" name="max_allowed_over_price" class="form-control " step="0.01" min="0"
                                    placeholder="{{ trans("Enter max allowed over price") }} " value="{{ old("max_allowed_over_price" , $item->max_allowed_over_price ?? null) }}">
                                <small class="form-text text-muted">{{ trans("Leave empty for no limit") }}</small>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="unlimited_days">{{ trans("unlimited days") }}</label>
                                <select class="custom-select form-select" name="unlimited_days" id="unlimited_days">
                                    <option value="0" @selected(old("unlimited_days" , $item->unlimited_days ?? 0) == 0)>{{ trans("No") }}</option>
                                    <option value="1" @selected(old("unlimited_days" , $item->unlimited_days ?? 0) == 1)>{{ trans("Yes") }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="number_of_days">{{ trans("number of days") }}</label>
                                <input type="number" name="number_of_days" class="form-control " step="any" min="0" 
                                    placeholder="{{ trans("Enter number of days") }} " value="{{ old("number_of_days" , $item->number_of_days ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="contract">{{ trans("contract") }}</label>
                                <div class="media-center-group form-control" data-max="3" data-type="media">
                                    <input type="text" hidden="hidden" class="form-control" name="contract" value="{{ old("contract" , $item->contract ?? null) }}">
                                    <button  type="button" class="btn btn-secondary media-center-load" style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="start_date">{{ trans("start date") }}</label>
                                <input type="date" name="start_date" class="form-control "
                                @if(isset($item) and $item->start_date)value="{{ $item->start_date->format('Y-m-d') }}"@endif>
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="end_date">{{ trans("end date") }}</label>
                                <input type="date" name="end_date" class="form-control "
                                @if(isset($item) and $item->end_date)value="{{ $item->end_date->format('Y-m-d') }}"@endif>
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="client_id">{{ trans("client") }}</label>
                                <select class="custom-select  form-select advance-select" name="client_id" id="client_id"  >
                                    
                                    <option   value="" >{{trans("select")." ".trans("client")}}</option>
                                    @foreach($clients ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->client_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->fullname}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="commercial_registration">{{ trans("commercial_registration") }}</label>
                                <input type="text" name="commercial_registration" class="form-control "
                                    placeholder="{{ trans("Enter commercial registration") }} " value="{{ old("commercial_registration" , $item->commercial_registration ?? null) }}">
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="tax_number">{{ trans("tax_number") }}</label>
                                <input type="text" name="tax_number" class="form-control "
                                    placeholder="{{ trans("Enter tax number") }} " value="{{ old("tax_number" , $item->tax_number ?? null) }}">
                            </div>

                            <div class="form-group mb-3 col-md-12">
                            <div class="mt-3 items-container"
                                data-items-on       =   "contract_id"
                                data-items-name     =   "contractPrices"
                                data-items-from     =   "contracts-prices">

                                <h3>{{ trans("contract prices") }}</h3>
                                <button class="btn btn-success create-new-items"><i class="fas fa-plus"></i></button>
                                <hr>
                                <div class="table-responsive ">
                                    <table class="table table-striped table-hover text-center" >
                                        <thead class="table-primary">
                                            <tr>
                                            
                                        <th  scope="col" data-name="product_id" data-type="select">{{ trans("product") }}</th>
                                            <th  scope="col" data-name="price" data-type="number">{{ trans("price") }}</th>
                                            <th  scope="col" data-name="actions" data-type="actions">{{ trans("actions") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        @foreach ($item->contractPrices ?? [] as $sItem)
                                            <tr data-id="{{ $sItem->id }}" data-data="{{ json_encode($sItem->itemData) }}" >
                                            
                                            <td>{{ $sItem?->product?->category?->name . ' -> ' . $sItem?->product?->subCategory?->name . ' -> ' . $sItem?->product?->name }}</td>
                                            <td>{{ $sItem->price }}</td>
                                            <td class="options">{!! $sItem->itemsActions !!}</td>
                                            </tr>
                                        @endforeach
        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
        
                            </div>

                            <div class="form-group mb-3 col-md-12">
                            <div class="mt-3 items-container"
                                data-items-on       =   "contract_id"
                                data-items-name     =   "contractCustomerPrices"
                                data-items-from     =   "contracts-customer-prices">

                                <h3>{{ trans("contract customer prices") }}</h3>
                                <button class="btn btn-success create-new-items"><i class="fas fa-plus"></i></button>
                                <hr>
                                <div class="table-responsive ">
                                    <table class="table table-striped table-hover text-center" >
                                        <thead class="table-primary">
                                            <tr>
                                            
                                        <th  scope="col" data-name="product_id" data-type="select">{{ trans("product") }}</th>
                                            <th  scope="col" data-name="over_price" data-type="number">{{ trans("over price") }}</th>
                                            <th  scope="col" data-name="actions" data-type="actions">{{ trans("actions") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        @foreach ($item->contractCustomerPrices ?? [] as $sItem)
                                            <tr data-id="{{ $sItem->id }}" data-data="{{ json_encode($sItem->itemData) }}" >
                                            
                                            <td>{{ $sItem?->product?->category?->name . ' -> ' . $sItem?->product?->subCategory?->name . ' -> ' . $sItem?->product?->name }}</td>
                                            <td>{{ $sItem->over_price }}</td>
                                            <td class="options">{!! $sItem->itemsActions !!}</td>
                                            </tr>
                                        @endforeach
        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
        
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
        
                <div class="modal fade" id="contracts-pricesModal" aria-hidden="true" aria-labelledby="contracts-pricesModalLabel" data-store="{{route("dashboard.contracts-prices.create")}}" >
                    <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h1 class="modal-title fs-5" id="contracts-pricesModalLabel">{{ trans("contracts prices") }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form  class="modal-form items-modal-form" >
                                <div class="row">
                                    
                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="product_id">{{ trans("product") }}</label>
                                <select class="custom-select  form-select advance-select" name="product_id" id="contract_id-product_id"  >
                                    
                                    <option   value="" >{{trans("select")." ".trans("product")}}</option>
                                    @foreach($products ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}"  value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="price">{{ trans("price") }}</label>
                                <input type="number" name="price" class="form-control " step="any" min="0"
                                    placeholder="{{ trans("Enter price") }} " value="" required>
                                    
                            </div>

                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans("Submit") }}</button>
                                </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    </div>
                </div>
                <div class="modal fade" id="contracts-pricesDeleteModel" tabindex="-1" aria-labelledby="contracts-pricesDeleteModelLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="contracts-pricesDeleteModelLabel">{{ trans("Delete contracts prices") }} <span></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{ trans("Are you sure you want to delete the contracts prices") }} <span></span>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans("Close") }}</button>
                                <button type="button" class="btn btn-danger items-final-delete">{{ trans("Delete") }}</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="contracts-customer-pricesModal" aria-hidden="true" aria-labelledby="contracts-customer-pricesModalLabel" data-store="{{route("dashboard.contracts-customer-prices.create")}}" >
                    <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h1 class="modal-title fs-5" id="contracts-customer-pricesModalLabel">{{ trans("contracts customer prices") }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form  class="modal-form items-modal-form" >
                                <div class="row">
                                    
                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="product_id">{{ trans("product") }}</label>
                                <select class="custom-select  form-select advance-select" name="product_id" id="contract_id-product_id-customer"  >
                                    
                                    <option   value="" >{{trans("select")." ".trans("product")}}</option>
                                    @foreach($products ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}"  value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="over_price">{{ trans("over price") }}</label>
                                <input type="number" name="over_price" class="form-control " step="any" min="0"
                                    placeholder="{{ trans("Enter over price") }} " value="">
                                    
                            </div>

                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans("Submit") }}</button>
                                </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    </div>
                </div>
                <div class="modal fade" id="contracts-customer-pricesDeleteModel" tabindex="-1" aria-labelledby="contracts-customer-pricesDeleteModelLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="contracts-customer-pricesDeleteModelLabel">{{ trans("Delete contracts customer prices") }} <span></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{ trans("Are you sure you want to delete the contracts customer prices") }} <span></span>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans("Close") }}</button>
                                <button type="button" class="btn btn-danger items-final-delete">{{ trans("Delete") }}</button>
                            </div>
                        </div>
                    </div>
                </div>

        

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
    <script>
        $(document).ready(function() {
            // Function to toggle number_of_days field visibility
            function toggleNumberOfDays() {
                var unlimitedDays = $('#unlimited_days').val();
                var numberOfDaysField = $('input[name="number_of_days"]').closest('.form-group');
                
                if (unlimitedDays == '1') {
                    // Hide number of days field when unlimited is Yes
                    numberOfDaysField.hide();
                    $('input[name="number_of_days"]').val(''); // Clear the value
                } else {
                    // Show number of days field when unlimited is No
                    numberOfDaysField.show();
                }
            }
            
            // Run on page load
            toggleNumberOfDays();
            
            // Run when unlimited_days value changes
            $('#unlimited_days').on('change', function() {
                toggleNumberOfDays();
            });
        });
    </script>
@endpush