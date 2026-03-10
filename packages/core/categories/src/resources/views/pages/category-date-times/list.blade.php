@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y ">

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
                    <li class="breadcrumb-item text-muted">@lang("categories")</li>
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
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols" id="visible_cols"
                                multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->

                        <div data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <div class="d-flex">
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                            <div class="fw-bolder fs-5">
                                                {{ $total }}
                                                <i class="fas fa-list-alt"></i>
                                                @lang('total')
                                            </div>
                                        </div>
                                        <!--end::Stat-->
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-danger  text-danger rounded mx-1 p-2">
                                            <div class="fw-bolder fs-5">
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt"></i>
                                                @lang('Trash')
                                            </div>
                                        </div>
                                        <!--end::Stat-->
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap">

                                 

                                    <!--begin::Add -->
                                    @can('dashboard.category-date-times.create')
                                        <a href="{{ route('dashboard.category-date-times.create') }}" class="btn-operation ">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>
                                                @lang('create new')
                                            </span>
                                        </a>
                                    @endcan
                                </div>

                            </div>
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="border border-warning border-dashed rounded text-warning  p-2 mx-1">
                                <span class="me-2" data-kt-user-table-select="selected_count"></span>@lang('Selected')
                            </div>
                            <button type="button" class="btn btn-primary"
                                data-kt-user-table-select="delete_selected">@lang('Delete Selected')</button>
                        </div>
                        <!--end::Group actions-->
                    </div>

                    <!--end::Card toolbar-->
                </div>

                <!--begin::Content-->
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

                                <div class="p-1 row" data-kt-user-table-filter="form">


                                    <div class="col-md-6 mb-1">
                                        <label for="type">@lang("type")</label>
                                        <select class="custom-select filter-input form-select advance-select" name="type"
                                            id="type">

                                            <option value=""> @lang("select categories")</option>
                                            <option value="clothes" @selected('clothes' == request("type"))>@lang('clothes')
                                            </option>
                                            <option value="sales" @selected('sales' == request("type"))>@lang('sales')
                                            </option>
                                            <option value="services" @selected('services' == request("type"))>
                                                @lang('services')</option>
                                            <option value="maid" @selected('maid' == request("type"))>@lang('maid')</option>
                                            <option value="maid" @selected('host' == request("type"))>@lang('host')</option>


                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="category_id">@lang("category")</label>
                                        <select class="custom-select filter-input form-select advance-select"
                                            name="category_id" id="category_id">

                                            <option value=""> @lang("select categories")</option>
                                            @foreach($categories as $item)
                                                <option value="{{$item->id }}" @selected($item->id == request("category_id"))>
                                                    @lang($item->name)</option>
                                            @endforeach

                                        </select>
                                    </div>

                                


                                    <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang("Create Date from") </label>
                                        <input type="datetime-local" name="from_created_at"
                                            class="form-control filter-input" placeholder="@lang("search for Create Date") "
                                            value="{{ request("created_at") }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="created_at"> @lang("Create Date to") </label>
                                        <input type="datetime-local" name="to_created_at" class="form-control filter-input"
                                            placeholder="@lang("search for Create Date") "
                                            value="{{ request("created_at") }}">
                                    </div>

                                    <!--begin::Actions-->
                                    <div class=" d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="reset">@lang('Reset')</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-menu-dismiss="true"
                                            data-kt-user-table-filter="filter">@lang('Apply')</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Content-->

                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle text-center table-row-dashed fs-6 gy-5" id="view-datatable"
                        data-load="{{ route('dashboard.category-date-times.index',['trash' => request()->trash]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                <th class="w-10px pe-2" data-name="select_switch">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#view-datatable .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-center p-0" data-name="date">@lang("date")</th>
                                <th class="text-center p-0" data-name="type">@lang("type")</th>
                                <th class="text-center p-0" data-name="city_id">@lang("city")</th>
                                <th class="text-center p-0" data-name="category_id">@lang("category/service")</th>
                                <th class="text-center p-0" data-name="count">@lang("count")</th>
                                <th class="text-center p-0" data-name="actions">@lang("Actions")</th>

                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">

                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
        <!--end::Post-->
    </div>
    <!--end::Content-->

    <!-- Duplicate DateTime Modal -->
    <div class="modal fade" id="duplicateDateTimeModal" tabindex="-1" aria-labelledby="duplicateDateTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateDateTimeModalLabel">@lang('Duplicate Date Time')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="duplicateDateTimeForm">
                    @csrf
                    <input type="hidden" id="duplicate_type" name="type">
                    <input type="hidden" id="duplicate_date" name="date">
                    <input type="hidden" id="duplicate_category_id" name="category_id">
                    <input type="hidden" id="duplicate_city_id" name="city_id">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="from_date" class="form-label required">@lang('From Date')</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="to_date" class="form-label required">@lang('To Date')</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" required>
                        </div>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> @lang('This will duplicate the selected date time schedule to all dates in the specified range.')
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-copy"></i> @lang('Duplicate')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('css')

@endpush
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.category-date-times.delete', ['type'=>'%type','date'=>'%date','category_id'=>'%category_id','city_id'=>'%city_id','trash'=>request()->trash]) }}"
    $(document).ready(function() {
        // Handle duplicate button click
        $(document).on('click', '.duplicate-datetime-btn', function(e) {
            e.preventDefault();
            
            const type = $(this).data('type');
            const date = $(this).data('date');
            const categoryId = $(this).data('category-id');
            const cityId = $(this).data('city-id');
            
            // Set hidden fields
            $('#duplicate_type').val(type);
            $('#duplicate_date').val(date);
            $('#duplicate_category_id').val(categoryId);
            $('#duplicate_city_id').val(cityId);
            
            // Set from_date to the current date
            $('#from_date').val(date);
            
            // Show modal
            $('#duplicateDateTimeModal').modal('show');
        });
        
        // Handle form submission
        $('#duplicateDateTimeForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> @lang("Processing...")');
            
            $.ajax({
                url: '{{ route("dashboard.category-date-times.duplicate") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#duplicateDateTimeModal').modal('hide');
                    $('#duplicateDateTimeForm')[0].reset();
                    
                    // Show success message in toastr
                    toastr.success(response.message || '@lang("Date times duplicated successfully")', '@lang("Success")', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 3000
                    });
                    
                    // Reload datatable using the table ID
                    if ($.fn.DataTable.isDataTable('#view-datatable')) {
                        $('#view-datatable').DataTable().ajax.reload(null, false);
                    } else {
                        // If DataTable is not initialized yet, just reload the page
                        location.reload();
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    if (xhr.status === 422 && xhr.responseJSON) {
                        // Show validation errors in toastr
                        if (xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    toastr.error(message, '@lang("Validation Error")', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                });
                            });
                        } else if (xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message, '@lang("Error")', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 5000
                            });
                        }
                    } else {
                        // Show generic error
                        let errorMessage = '@lang("An error occurred while duplicating")';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        toastr.error(errorMessage, '@lang("Error")', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 5000
                        });
                    }
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush