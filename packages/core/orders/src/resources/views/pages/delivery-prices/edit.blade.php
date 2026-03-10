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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.delivery-prices.index")}}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.delivery-prices.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.delivery-prices.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="category_id">{{ trans('category') }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id"
                                    id="category_id">

                                    <option value="">{{ trans('select category') }}</option>
                                    @foreach ($categories ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->category_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="city_id">{{ trans('city') }}</label>
                                <select class="custom-select  form-select advance-select" name="city_id" id="city_id">

                                    <option value="">{{ trans('select city') }}</option>
                                    @foreach ($cities ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->city_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="district_id">{{ trans('district') }}</label>
                                <select class="custom-select  form-select advance-select" name="district_id"
                                    id="district_id">

                                    <option value="">{{ trans('select district') }}</option>
                                    @foreach ($districts ?? [] as $sItem)
                                        <option data-city-id="{{ $sItem->city_id }}" data-id="{{ $sItem->id }}"
                                            @selected(isset($item) and $item->district_id == $sItem->id) value="{{ $sItem->id }}">{{ $sItem->name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="price">{{ trans('price') }}</label>
                                <input type="number" name="price" class="form-control "
                                    placeholder="{{ trans('Enter price') }} "
                                    value="{{ old('price', $item->price ?? null) }}">

                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="free_delivery">{{ trans('free delivery') }}</label>
                                <input type="number" name="free_delivery" class="form-control "
                                    placeholder="{{ trans('Enter free delivery') }} "
                                    value="{{ old('free_delivery', $item->free_delivery ?? null) }}">
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
    <script>
        $(document).ready(function() {
            const citySelect = $("#city_id");
            const districtSelect = $("#district_id");
            const allDistricts = districtSelect.find("option").clone(); // Store all districts

            citySelect.on("change", function() {
                const selectedCityId = $(this).val();

                // Clear current district options
                districtSelect.html('<option value="">{{ trans('Select District') }}</option>');

                // Filter districts based on selected city
                allDistricts.each(function() {                    
                    if ($(this).data("city-id") == selectedCityId) {
                        districtSelect.append($(this).clone());
                    }
                });
            });
            $("#city_id").change()
        });
    </script>
@endpush
