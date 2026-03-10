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
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.order-representatives.index")}}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.order-representatives.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.order-representatives.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="order_id">{{ trans('order') }}</label>
                                <select class="custom-select  form-select advance-select" name="order_id" id="order_id">

                                    <option value="">{{ trans('select order') }}</option>
                                    @foreach ($orders ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->order_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->reference_id }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="required" for="representative_id">{{ trans('representative') }}</label>
                                <select class="custom-select  form-select advance-select" name="representative_id"
                                    id="representative_id">

                                    <option value="">{{ trans('select representative') }}</option>
                                    @foreach ($representatives ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->representative_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->fullname }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="delivery" @selected(isset($item) and $item->type == 'delivery')>{{ trans('delivery') }}</option>
                                    <option value="technical" @selected(isset($item) and $item->type == 'technical')>{{ trans('technical') }}
                                    </option>
                                    <option value="receiver" @selected(isset($item) and $item->type == 'receiver')>{{ trans('receiver') }}</option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="" for="date">{{ trans('date') }}</label>
                                <input type="date" name="date" class="form-control "
                                    placeholder="{{ trans('Enter date') }} "
                                    value="@isset($item){{ $item->date }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="" for="time">{{ trans('time') }}</label>
                                <input type="time" name="time" class="form-control "
                                    placeholder="{{ trans('Enter time') }} "
                                    value="@isset($item){{ $item->time }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-4">
                                <label class="" for="to_time">{{ trans('to time') }}</label>
                                <input type="time" name="to_time" class="form-control "
                                    placeholder="{{ trans('Enter to time') }} "
                                    value="@isset($item){{ $item->to_time }}@endisset">

                            </div>

                            <div class="map-container">
                                <h4>{{ trans('use the map to find the location') }}</h4>
                                <div class="row">

                                    <div class="form-group mb-3 col-md-6">
                                        <label class="" for="lat">{{ trans('lat') }}</label>
                                        <input type="text" id="lat" name="lat" class="form-control lat"
                                        placeholder="{{ trans('Enter lat') }} "
                                        value="@isset($item){{ $item->lat }}@endisset">
                                        
                                    </div>
                                    
                                    <div class="form-group mb-3 col-md-6">
                                        <label class="" for="lng">{{ trans('lng') }}</label>
                                        <input type="text" id="lng" name="lng" class="form-control lng"
                                        placeholder="{{ trans('Enter lng') }} "
                                        value="@isset($item){{ $item->lng }}@endisset">
                                        
                                    </div>
                                </div>

                                <input type="text" class="search-box" placeholder="{{ trans("Enter an address") }}...">
                                <div class="suggestions"></div>
                                <div class="map"></div>
                            </div>
                        
                          
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="location">{{ trans('location') }}</label>
                                <input type="text" name="location" class="form-control "
                                    placeholder="{{ trans('Enter location') }} "
                                    value="@isset($item){{ $item->location }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="has_problem" name="has_problem"
                                        @checked(isset($item) and $item->has_problem)>
                                    <label class="form-check-label" for="has_problem">{{ trans('has problem') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="for_all_items"
                                        name="for_all_items" @checked(isset($item) and $item->for_all_items)>
                                    <label class="form-check-label"
                                        for="for_all_items">{{ trans('for_all items') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="items">{{ trans('items') }}</label>
                                <select class="custom-select  form-select advance-select" name="items" id="items"
                                    multiple>

                                    <option value="">{{ trans('select items') }}</option>
                                    @foreach ($items ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->items->where('id', $sItem->id)->first())
                                            value="{{ $sItem->id }}">{{ $sItem->order?->reference_id ." : ".$sItem->product?->name." :quantity  ".$sItem->quantity ." height  : ".$sItem->height." width  : ".$sItem->width  }}</option>
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />

@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>

@endpush
