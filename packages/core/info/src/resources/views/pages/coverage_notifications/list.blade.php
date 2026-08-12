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
                </div>

                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive mt-3">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.coverage-notifications.index') }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
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
