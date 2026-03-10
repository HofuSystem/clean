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
                            <a href="{{ route('dashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang('notification')</li>
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
                        redirect-to="{{ route('dashboard.notifications.index') }}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.notifications.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.notifications.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="types">{{ trans('types') }}</label>
                                <select class="custom-select  form-select advance-select" name="types" id="types"
                                    multiple>

                                    <option value="">{{ trans('select types') }}</option>
                                    <option value="apps" @selected(isset($item) and str_contains($item->types, 'apps'))>{{ trans('apps') }}</option>
                                    <option value="email" @selected(isset($item) and str_contains($item->types, 'email'))>{{ trans('email') }}</option>
                                    <option value="sms" @selected(isset($item) and str_contains($item->types, 'sms'))>{{ trans('sms') }}</option>
                                    <option value="whats_app" @selected(isset($item) and str_contains($item->types, 'whats_app'))>{{ trans('whats app') }}
                                    </option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="for">{{ trans('for') }}</label>
                                <select class="custom-select  form-select advance-select" name="for" id="for">
                                    <option value="">{{ trans('select for') }}</option>
                                    <option value="users" @selected(isset($item) and $item->for == 'users')>{{ trans('users') }}</option>
                                    <option value="email" @selected(isset($item) and $item->for == 'email')>{{ trans('email') }}</option>
                                    <option value="phone" @selected(isset($item) and $item->for == 'phone')>{{ trans('phone') }}</option>
                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-12" id="selected-users">
                                <label class="" for="selected_users">{{ trans('selected users') }}</label>
                                <select class="custom-select  form-select advance-select" name="selected_users"
                                    id="selected_users" multiple>

                                    <option value="">{{ trans('select sender') }}</option>
                                    @foreach ($senders ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and in_array($sItem->id, $item->for_data_array))
                                            value="{{ $sItem->id }}">{{ $sItem->phone . ' : ' . $sItem->fullname }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="for_data">{{ trans('for data') }}</label>
                                <textarea name="for_data" class="form-control " placeholder="{{ trans('Enter for data') }} ">
@isset($item)
{{ $item->for_data }}
@endisset
</textarea>

                            </div>
                            <div id="users-filters" class="row">
                                <div class="form-group mb-3 col-md-6">
                                    <label class="required" for="register_from">{{ trans('register from') }}</label>
                                    <input type="date" name="register_from" class="form-control "
                                        placeholder="{{ trans('Enter register_from') }} "
                                        value="@isset($item){{ $item->register_from }}@endisset">

                                </div>
                                <div class="form-group mb-3 col-md-6">
                                    <label class="required" for="register_to">{{ trans('register to') }}</label>
                                    <input type="date" name="register_to" class="form-control "
                                        placeholder="{{ trans('Enter register_to') }} "
                                        value="@isset($item){{ $item->register_to }}@endisset">
                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_from">{{ trans('orders from') }}</label>
                                    <input type="date" name="orders_from" class="form-control "
                                        placeholder="{{ trans('Enter orders_from') }} "
                                        value="@isset($item){{ $item->orders_from }}@endisset">

                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_to">{{ trans('orders to') }}</label>
                                    <input type="date" name="orders_to" class="form-control "
                                        placeholder="{{ trans('Enter orders_to') }} "
                                        value="@isset($item){{ $item->orders_to }}@endisset">
                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_min">{{ trans('orders min') }}</label>
                                    <input type="text" name="orders_min" class="form-control "
                                        placeholder="{{ trans('Enter orders_min') }} "
                                        value="@isset($item){{ $item->orders_min }}@endisset">

                                </div>
                                <div class="form-group mb-3 col-md-3">
                                    <label class="required" for="orders_max">{{ trans('orders max') }}</label>
                                    <input type="text" name="orders_max" class="form-control "
                                        placeholder="{{ trans('Enter orders_max') }} "
                                        value="@isset($item){{ $item->orders_max }}@endisset">
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="title">{{ trans('title') }}</label>
                                <input type="text" name="title" class="form-control "
                                    placeholder="{{ trans('Enter title') }} "
                                    value="@isset($item){{ $item->title }} @else  {{ config('app.name') }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="body">{{ trans('body') }}</label>
                                <textarea name="body" class="form-control " placeholder="{{ trans('Enter body') }} ">
@isset($item)
{{ $item->body }}
@endisset
</textarea>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="media">{{ trans('media') }}</label>
                                <div class="media-center-group form-control" data-max="5" data-type="gallery">
                                    <input type="text" hidden="hidden" class="form-control" name="media"
                                        value="{{ old('media', $item->media ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>



                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('send') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <!--end::Card-->
                <!--begin::Card-->
                <div class="card mt-3">
                    <!--begin::Card header-->
                    <div class="card-header row">
                        <h3 class="card-title font-weight-bold for">@lang('selected users')</h3>


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

                                    <div class="p-1 row" data-kt-user-table-filter="form" id="filter-form">

                                        <div class="col-md-6 mb-1">
                                            <label for="filter_fullname"> @lang('full name') </label>
                                            <input type="text" name="filter_fullname" id="filter_fullname" class="form-control filter-input"
                                                placeholder="@lang('search for name') " value="">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="filter_phone"> @lang('phone') </label>
                                            <input type="text" name="filter_phone" id="filter_phone" class="form-control filter-input"
                                                placeholder="@lang('search for phone') " value="">
                                        </div>
                                      
                                        <!--begin::Actions-->
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="button" id="filter-reset-btn"
                                                class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                data-kt-menu-dismiss="true"
                                                data-kt-user-table-filter="reset">@lang('Reset')</button>
                                            <button type="button" id="filter-apply-btn" class="btn btn-primary fw-bold px-6"
                                                data-kt-menu-dismiss="true"
                                                data-kt-user-table-filter="filter">@lang('Apply')</button>
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0 table-responsive table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle text-center table-row-dashed fs-6 gy-5"
                            id="view-datatable-notification-users"
                            @isset($item)
                                data-load="{{ route('dashboard.notifications.getSentToUsers', $item->id) }}"
                            @else
                                data-load="{{ route('dashboard.notifications.getusers') }}"
                            @endisset>
                            <!--begin::Table head-->
                            <thead class="table-primary">
                                <!--begin::Table row-->
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="text-center p-3" data-name="id">@lang('id')</th>
                                    <th class="text-center p-3" data-name="fullname">@lang('full name')</th>
                                    <th class="text-center p-3" data-name="email">@lang('email')</th>
                                    <th class="text-center p-3" data-name="phone">@lang('phone')</th>
                                    @isset($item)
                                        <th class="text-center p-3" data-name="sent_status">@lang('sent status')</th>
                                        <th class="text-center p-3" data-name="sent_response">@lang('sent response')</th>
                                    @endisset
                                    <th class="text-center p-3" data-name="orders_count">@lang('orders count')</th>
                                    <th class="text-center p-3" data-name="gender">@lang('gender')</th>
                                    <th class="text-center p-3" data-name="city">@lang('city')</th>
                                    <th class="text-center p-3" data-name="district">@lang('district')</th>
                                    <th class="text-center p-3" data-name="created_at">@lang('register date')</th>
                                    <th class="text-center p-3" data-name="showActions">@lang('Actions')</th>
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
        $('#for').change(function(e) {
            e.preventDefault();
            let value = $(this).val();
            if (value == 'users') {
                $('#selected-users').show()
                $('#users-filters').show()
                $('[name=for_data]').parent().hide()
                $('[name=for_data]').val(null)
            } else {
                $('#selected-users').hide()
                $('#users-filters').hide()
                $('[name=for_data]').parent().show()
                $('[name=for_data]').val(null)
            }
        });
        $('#for').trigger('change')
        $('#selected_users').change(function(e) {
            e.preventDefault();
            let value = $(this).val();
            $('[name=for_data]').val(JSON.stringify(value))
        });
        var cols = [];
        //if any change happed in the form related to users, reload the table
        let url =
            @if (isset($item))
                '{{ route('dashboard.notifications.getSentToUsers', $item->id) }}'
            @else
                '{{ route('dashboard.notifications.getusers') }}'
            @endif ;
        let formData = getFormData($('#operation-form'));
        cols = [];
        $('table#view-datatable-notification-users thead th').each(function(index, element) {
            let data = $(this).data('name');
            let name = (index == 0) ? data : $(this).text();
            let col = {
                'name': name,
                'data': data
            };
            if ($(this).attr('orderable')) {
                col.orderable = ($(this).attr('orderable') == true);
            }
            cols.push(col);
        });
        let DataTable = $('#view-datatable-notification-users').DataTable({
            dom: "<'d-flex justify-content-between align-items-center mb-2'<'dt-buttons'B><'dt-length'l>>" +
                "frtip",
            lengthMenu: [
                [25, 50, 100, 300, 500, -1],
                [25, 50, 100, 300, 500, trans('all')]
            ],
            pageLength: 25, // default selected value
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            orderable: false,
            searchDelay: 500,
            searching: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: {
                url: url,
                type: 'POST',
                data: function(data) {
                    $.extend(data, formData);
                    return data;
                },
                error: function(xhr, error, thrown) {
                    // Handle AJAX errors
                    console.log(xhr, error, thrown);

                    // alert('noq An error occurred: ' + error);
                }

            },
            columns: cols,

        });
        
        // Handle filter Apply button
        $('#filter-apply-btn').on('click', function(e) {
            e.preventDefault();
            // Get filter values
            let filterData = {
                filter_fullname: $('#filter_fullname').val(),
                filter_phone: $('#filter_phone').val()
            };
            // Merge with existing form data
            $.extend(formData, filterData);
            // Reload DataTable with filters
            DataTable.draw();
        });
        
        // Handle filter Reset button
        $('#filter-reset-btn').on('click', function(e) {
            e.preventDefault();
            // Clear filter inputs
            $('#filter_fullname').val('');
            $('#filter_phone').val('');
            // Remove filter data from formData
            delete formData.filter_fullname;
            delete formData.filter_phone;
            // Reload DataTable without filters
            DataTable.draw();
        });
        
        @if (!isset($item))
            $('#operation-form select , #operation-form input, #operation-form textarea').change(function(e) {
                e.preventDefault();
                formData = getFormData($('#operation-form'));
                DataTable.draw();
            });
        @endisset
    </script>
@endpush
