@extends('admin::layouts.dashboard')
@section('content')
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
                    <li class="breadcrumb-item text-muted">@lang("info")</li>
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
                    <div class="card-title col-md-6 mt-3">
                        <div class="fw-bolder fs-5 text-dark">
                            <span class="badge badge-success p-2">{{ $total }}</span> @lang('Total Subscriptions')
                        </div>
                    </div>
                    
                    <div class="card-toolbar col-md-6 mt-3">
                        <!--begin::Toolbar-->
                        <div data-kt-user-table-toolbar="base">
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary"
                                data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                        </div>
                        <!--end::Group actions-->
                    </div>
                </div>

                <!--begin::Filters-->
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

                                <div class="p-4 row" data-kt-user-table-filter="form">
                                    <div class="col-md-3 mb-3">
                                        <label for="city_id">@lang("City")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="filters[city_id]" id="city_id">
                                            <option value="">@lang("select city")</option>
                                            @foreach($cities as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="district_id">@lang("District")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="filters[district_id]" id="district_id">
                                            <option value="">@lang("select district")</option>
                                            @foreach($districts as $d)
                                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="type">@lang("Type")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="filters[type]" id="type">
                                            <option value="">@lang("select type")</option>
                                            <option value="expansion">@lang("Expansion")</option>
                                            <option value="resume">@lang("Resume")</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="status">@lang("Status")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="filters[status]" id="status">
                                            <option value="">@lang("select status")</option>
                                            <option value="pending">@lang("Pending")</option>
                                            <option value="notified">@lang("Notified")</option>
                                        </select>
                                    </div>
                                    
                                    <!--begin::Actions-->
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-user-table-filter="reset">@lang('Reset')</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-user-table-filter="filter">@lang('Apply')</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Filters-->

                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive mt-3">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.coverage-notifications.index') }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-3" data-name="id">@lang("id")</th>
                                <th class="text-center p-3" data-name="user">@lang("User")</th>
                                <th class="text-center p-3" data-name="phone">@lang("Phone")</th>
                                <th class="text-center p-3" data-name="city">@lang("City")</th>
                                <th class="text-center p-3" data-name="district">@lang("District")</th>
                                <th class="text-center p-3" data-name="address">@lang("User Address")</th>
                                <th class="text-center p-3" data-name="type">@lang("Type")</th>
                                <th class="text-center p-3" data-name="status">@lang("Status")</th>
                                <th class="text-center p-3" data-name="created_at">@lang("Date")</th>
                                <th class="text-center p-3" data-name="actions">@lang("Actions")</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
@endsection
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.coverage-notifications.delete', ['id'=>'%s']) }}"
</script>
@endpush
