
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
                            action="{{ route("dashboard.order-schedules.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.order-schedules.create") }}"
                            data-mode="new"
                        @endif
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="client_id">{{ trans("client") }}</label>
                                <select class="custom-select  form-select advance-select" name="client_id" id="client_id"  >
                                    
                                    <option   value="" >{{trans("select")." ".trans("client")}}</option>
                                    @foreach($clients ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->client_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->fullname}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="type">{{ trans("type") }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type"  >
                                    <option value="day" @selected(isset($item) and $item->type == "day" ) >{{trans("day")}}</option>
                                    <option value="date" @selected(isset($item) and $item->type == "date" ) >{{trans("date")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="receiver_day">{{ trans("receiver day") }}</label>
                                <select class="custom-select  form-select advance-select" name="receiver_day" id="receiver_day"  >
                                    <option value="sunday" @selected(isset($item) and $item->receiver_day == "sunday" ) >{{trans("sunday")}}</option>
                                    <option value="monday" @selected(isset($item) and $item->receiver_day == "monday" ) >{{trans("monday")}}</option>
                                    <option value="tuesday" @selected(isset($item) and $item->receiver_day == "tuesday" ) >{{trans("tuesday")}}</option>
                                    <option value="wednesday" @selected(isset($item) and $item->receiver_day == "wednesday" ) >{{trans("wednesday")}}</option>
                                    <option value="thursday" @selected(isset($item) and $item->receiver_day == "thursday" ) >{{trans("thursday")}}</option>
                                    <option value="friday" @selected(isset($item) and $item->receiver_day == "friday" ) >{{trans("friday")}}</option>
                                    <option value="saturday" @selected(isset($item) and $item->receiver_day == "saturday" ) >{{trans("saturday")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="receiver_date">{{ trans("receiver date") }}</label>
                                <input type="date" name="receiver_date" class="form-control "
                                     " value="@if(isset($item)){{ $item->receiver_date }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="receiver_time">{{ trans("receiver time") }}</label>
                                <input type="time" name="receiver_time" class="form-control "
                                     value="@if(isset($item)){{ $item->receiver_time }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="receiver_to_time">{{ trans("receiver to time") }}</label>
                                <input type="time" name="receiver_to_time" class="form-control "
                                     value="@if(isset($item)){{ $item->receiver_to_time }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="delivery_day">{{ trans("delivery day") }}</label>
                                <select class="custom-select  form-select advance-select" name="delivery_day" id="delivery_day"  >
                                    <option value="sunday" @selected(isset($item) and $item->delivery_day == "sunday" ) >{{trans("sunday")}}</option>
                                    <option value="monday" @selected(isset($item) and $item->delivery_day == "monday" ) >{{trans("monday")}}</option>
                                    <option value="tuesday" @selected(isset($item) and $item->delivery_day == "tuesday" ) >{{trans("tuesday")}}</option>
                                    <option value="wednesday" @selected(isset($item) and $item->delivery_day == "wednesday" ) >{{trans("wednesday")}}</option>
                                    <option value="thursday" @selected(isset($item) and $item->delivery_day == "thursday" ) >{{trans("thursday")}}</option>
                                    <option value="friday" @selected(isset($item) and $item->delivery_day == "friday" ) >{{trans("friday")}}</option>
                                    <option value="saturday" @selected(isset($item) and $item->delivery_day == "saturday" ) >{{trans("saturday")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="delivery_date">{{ trans("delivery date") }}</label>
                                <input type="date" name="delivery_date" class="form-control "
                                     " value="@if(isset($item)){{ $item->delivery_date }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="delivery_time">{{ trans("delivery time") }}</label>
                                <input type="time" name="delivery_time" class="form-control "
                                     value="@if(isset($item)){{ $item->delivery_time }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="delivery_to_time">{{ trans("delivery to time") }}</label>
                                <input type="time" name="delivery_to_time" class="form-control "
                                     value="@if(isset($item)){{ $item->delivery_to_time }}@endif">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="receiver_address_id">{{ trans("receiver address") }}</label>
                                <select class="custom-select  form-select advance-select" name="receiver_address_id" id="receiver_address_id"  >
                                    
                                    <option   value="" >{{trans("select")." ".trans("receiver address")}}</option>
                                    @foreach($receiverAddresses ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->receiver_address_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="delivery_address_id">{{ trans("delivery address") }}</label>
                                <select class="custom-select  form-select advance-select" name="delivery_address_id" id="delivery_address_id"  >
                                    
                                    <option   value="" >{{trans("select")." ".trans("delivery address")}}</option>
                                    @foreach($deliveryAddresses ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->delivery_address_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="note">{{ trans("note") }}</label>
                <textarea type="number" name="note" class="form-control "
                    placeholder="{{ trans("Enter note") }} " >@if(isset($item)){{ $item->note }}@endif</textarea>
                    
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