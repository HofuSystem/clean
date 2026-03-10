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
                        <li class="breadcrumb-item text-muted">@lang('categories')</li>
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
                        action="{{ route('dashboard.category-date-times.create') }}">
                        @csrf
                        <div class="card-body row">
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="clothes" @selected(isset($item) and $item->type == 'clothes')>{{ trans('clothes') }}</option>
                                    <option value="sales" @selected(isset($item) and $item->type == 'sales')>{{ trans('sales') }}</option>
                                    <option value="services" @selected(isset($item) and $item->type == 'services')>{{ trans('services') }}</option>
                                    <option value="maid" @selected(isset($item) and $item->type == 'maid')>{{ trans('maid') }}</option>
                                    <option value="host" @selected(isset($item) and $item->type == 'host')>{{ trans('host') }}</option>

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label for="category_id">{{ trans('category') }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id"
                                    id="category_id">

                                    <option value="">{{ trans('select category') }}</option>
                                    @foreach ($categories ?? [] as $sItem)
                                        <option data-type="{{ $sItem->type }}" data-id="{{ $sItem->id }}" @selected(isset($item) and $item->category_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label  for="city_id">{{ trans("city") }}</label>
                                <select class="custom-select  form-select advance-select" name="city_id" id="city_id"  >
                                    
                                    <option   value="" >{{trans("select cities")}}</option>
                                    @foreach($cities ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>
                            <div class="form-group col-md-6">
                                <label for="dateFrom">{{ trans('Date From') }}</label>
                                <input name="date_from" type="date" class="form-control" id="dateFrom" >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="dateTo">{{ trans('Date To') }}</label>
                                <input name="date_to" type="date" class="form-control" id="dateTo" >
                            </div>
                            <div class="form-group">
                                <label for="weekends">{{ trans('Select weekends') }}</label>
                                <select multiple class="form-control advance-select" id="weekends">
                                    <option value="monday">{{ trans("Monday") }}</option>
                                    <option value="tuesday">{{ trans("Tuesday") }}</option>
                                    <option value="wednesday">{{ trans("Wednesday") }}</option>
                                    <option value="thursday">{{ trans("Thursday") }}</option>
                                    <option value="friday">{{ trans("Friday") }}</option>
                                    <option value="saturday">{{ trans("Saturday") }}</option>
                                    <option value="sunday">{{ trans("Sunday") }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="allDays">{{ trans('select of dates off') }}</label>
                                <select name="off_dates"  multiple class="form-control advance-select" id="allDays"></select>
                            </div>
                            <div class="container">
                                <table class="table mt-4 text-center" id="scheduleTable">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>{{ trans('From Time') }}</th>
                                            <th>{{ trans('To Time') }}</th>
                                            <th>{{ trans('Receiver Count') }}</th>
                                            <th>{{ trans('Delivery Count') }}</th>
                                            <th>{{ trans('Order Count') }}</th>
                                            <th>{{ trans('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Rows will be added here dynamically -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th> <input type="time" class="form-control" id="fromTime" ></th>
                                            <th> <input type="time" class="form-control" id="toTime" ></th>
                                            <th> <input type="number" class="form-control" id="receiverCount" ></th>
                                            <th> <input type="number" class="form-control" id="deliveryCount" ></th>
                                            <th colspan="2"> <button id="addToTable" class="btn btn-primary">{{ trans('Add to Table') }}</button></th>
                                        </tr>
                                    </tfoot>
                                </table>
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
    {{-- <link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" /> --}}
@endpush
@push('js')
    {{-- <script src="{{ asset('control') }}/js/custom/crud/form.js"></script> --}}
    <script>
        $(document).ready(function() {
            var allCategories = $('#category_id option').clone();
            $('#type').change(function() {
                var type        = $(this).val();
                var $Category   = $('#category_id');
              
                // Clear the current options
                $Category.empty();

                // Add only the options that belong to the selected category
                allCategories.each(function() {
                    if ($(this).data('type') == type) {
                        $Category.append($(this).clone()); // Add matching options
                    }

                });

                // Trigger Select2 to update the dropdown
                $Category.trigger('change');

            });
            // Function to populate the 'All Days' select based on the date range
            $('#dateFrom, #dateTo').change(function() {
                var dateFrom = new Date($('#dateFrom').val());
                var dateTo = new Date($('#dateTo').val());
                var allDays = $('#allDays');
                allDays.empty();

                while (dateFrom <= dateTo) {
                    var dateString = dateFrom.toISOString().split('T')[0];
                    allDays.append(new Option(dateString, dateString));
                    dateFrom.setDate(dateFrom.getDate() + 1);
                }
            });

            // Function to add a new row to the table
            $('#addToTable').click(function(event) {
                event.preventDefault();
                var fromTime        = $('#fromTime').val();
                var toTime          = $('#toTime').val();
                var receiverCount   = $('#receiverCount').val();
                var deliveryCount   = $('#deliveryCount').val();
                

                if (fromTime && toTime && receiverCount && deliveryCount) {
                    var newRow = `
                    <tr>
                        <td> <input name="from_time[]" type="time" class="form-control"  value="${fromTime}" ></td>
                        <td> <input name="to_time[]" type="time" class="form-control"  value="${toTime}" ></td>
                        <td> <input name="receiver_count[]" type="number" class="form-control receiver-count"  value="${receiverCount}" > </td>
                        <td> <input name="delivery_count[]" type="number" class="form-control delivery-count"  value="${deliveryCount}" > </td>
                        <td> <input disabled name="order_count[]" type="number" class="form-control order-count"  value="${(parseInt(receiverCount)+parseInt(deliveryCount))}" > </td>
                        <td><button  class="btn btn-danger btn-sm delete-row">Delete</button></td>
                    </tr>`;
                    $('#scheduleTable tbody').append(newRow);

                    // Clear the form inputs
                    $('#fromTime').val('');
                    $('#toTime').val('');
                    $('#receiverCount').val('');
                    $('#deliveryCount').val('');
                }else{
                    toastr.error("Please fill all fields");
                }
            });

            // Function to delete a row from the table
            $(document).on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
            });
            // Function update order count
            $(document).on('change', '.receiver-count, .delivery-count', function() {
                var receiverCount = $(this).closest('tr').find('.receiver-count').val();
                var deliveryCount = $(this).closest('tr').find('.delivery-count').val();
                var orderCount = $(this).closest('tr').find('.order-count');
                orderCount.val(parseInt(receiverCount) + parseInt(deliveryCount));
            });
           
            $('.advance-select').each(function(index, element) {
                let name = $(this).attr('name') + "...";
                if ($(this).parents('.modal').length) {
                    let id = $(this).parents('.modal').first().attr('id');
                    $(this).select2({
                        allowClear: true,
                        placeholder: name,
                        dropdownParent: $('#' + id),
                        placeholder: "Select an option",
                    }).trigger('change');
                } else {
                    $(this).select2({
                        allowClear: true,
                        placeholder: name,
                        placeholder: "Select an option",

                    }).trigger('change');
                }
            });
            $('#operation-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Collect form data
                var formData = {
                    _token      : $('input[name="_token"]').val(), // CSRF token
                    type        : $('select[name="type"]').val(), // Date From
                    category_id : $('select[name="category_id"]').val(), // Date From
                    city_id     : $('select[name="city_id"]').val(), // Date From
                    date_from   : $('input[name="date_from"]').val(), // Date From
                    date_to     : $('input[name="date_to"]').val(), // Date To
                    weekends    : $('#weekends').val(), // Selected Weekdays
                    off_dates   : $('#allDays').val(), // Selected Off Dates
                    times       : [] // Array to store time and order count data
                };

                // Collect data from the table rows
                $('#scheduleTable tbody tr').each(function() {
                    var row = {
                        from_time       : $(this).find('input[name="from_time[]"]').val(),
                        to_time         : $(this).find('input[name="to_time[]"]').val(),
                        receiver_count  : $(this).find('input[name="receiver_count[]"]').val(),
                        delivery_count  : $(this).find('input[name="delivery_count[]"]').val()
                    };
                    formData.times.push(row); // Add row data to the times array
                });

           
                // You can now send the formData object to your server using AJAX
                $.ajax({
                    url: $('#operation-form').attr('action'), // Form action URL
                    method: 'POST', // HTTP method
                    data: formData, // Data to send
                    success: function(response) {
                        toastr.success(response.message);
                        $('input[name="_token"]').val(null).trigger('change'), // CSRF token
                        $('input[name="city_id"]').val(null).trigger('change'), // Date From
                        $('input[name="date_from"]').val(null).trigger('change'), // Date From
                        $('input[name="date_to"]').val(null).trigger('change'), // Date To
                        $('#weekends').val(null).trigger('change'), // Selected Weekdays
                        $('#allDays').val(null).trigger('change')
                        $('#scheduleTable tbody').html(""); 
                    },
                    error: function(xhr, status, error) {
                        // Code to run if the request fails
                        response = JSON.parse(xhr.responseText); 
                        toastr.error(response.message);
                        $.each(response.errors, function (key, array) { 
                            $.each(array, function (index, error) { 
                            toastr.error(error,key);
                            });
                        });
                    }
                });
            });
        });
    </script>
@endpush
