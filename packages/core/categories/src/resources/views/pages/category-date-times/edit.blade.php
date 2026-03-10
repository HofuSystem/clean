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

                    <form class="form" method="POST" id="update-date-time-form"
                        action="{{ route('dashboard.category-date-times.edit',['type'=>$type,'date'=>$date,'category_id'=>$categoryId,'city_id'=>$cityId]) }}">
                        @csrf
                        <div class="card-body row">
                            <div class="form-group mb-3 col-md-12">
                                <label for="date">{{ trans('date') }}</label>
                                <input name="date" type="date" class="form-control" id="date" value="{{ $date }}">
                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="clothes" @selected(isset($type) and $type == 'clothes')>{{ trans('clothes') }}</option>
                                    <option value="sales" @selected(isset($type) and $type == 'sales')>{{ trans('sales') }}</option>
                                    <option value="services" @selected(isset($type) and $type == 'services')>{{ trans('services') }}</option>
                                    <option value="maid" @selected(isset($type) and $type == 'maid')>{{ trans('maid') }}</option>
                                    <option value="host" @selected(isset($type) and $type == 'host')>{{ trans('host') }}</option>

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label for="category_id">{{ trans('category') }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id"
                                    id="category_id" data-old-value="{{ $categoryId }}">

                                    <option value="">{{ trans('select category') }}</option>
                                    @foreach ($categories ?? [] as $sItem)
                                        <option data-type="{{ $sItem->type }}" data-id="{{ $sItem->id }}" @selected(isset($categoryId) and $categoryId == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label  for="city_id">{{ trans("city") }}</label>
                                <select class="custom-select  form-select advance-select" name="city_id" id="city_id"  >
                                    
                                    <option   value="" >{{trans("select cities")}}</option>
                                    @foreach($cities ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" value="{{$sItem->id }}" @selected(isset($cityId) and $cityId == $sItem->id) >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
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
                                        @foreach($dateTimes ?? [] as $sItem)
                                            <tr>
                                                <td> <input name="from[]" type="time" class="form-control"  value="{{ $sItem->from }}" ></td>
                                                <td> <input name="to[]" type="time" class="form-control"  value="{{ $sItem->to }}" ></td>
                                                <td> <input name="receiver_count[]" type="number" class="form-control receiver-count"  value="{{ $sItem->receiver_count }}" > </td>
                                                <td> <input name="delivery_count[]" type="number" class="form-control delivery-count"  value="{{ $sItem->delivery_count }}" > </td>
                                                <td> <input disabled name="order_count[]" type="number" class="form-control order-count"  value="{{ $sItem->delivery_count + $sItem->receiver_count }}" > </td>
                                                <td><button  class="btn btn-danger btn-sm delete-row">Delete</button></td>
                                            </tr>
                                        @endforeach
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
                // Keep the old value if editing
                if ($Category.data('old-value')) {
                    $Category.val($Category.data('old-value'));
                }else{
                    $Category.val(null);
                }
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
                        <td> <input name="from[]" type="time" class="form-control"  value="${fromTime}" ></td>
                        <td> <input name="to[]" type="time" class="form-control"  value="${toTime}" ></td>
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
            $('#update-date-time-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Collect form data
                var formData = {
                    _token          : $('input[name="_token"]').val(), // CSRF token
                    new_type        : $('select[name="type"]').val(), // Date From
                    new_category_id : $('select[name="category_id"]').val(), // Date From
                    new_city_id     : $('select[name="city_id"]').val(), // Date From
                    new_date        : $('input[name="date"]').val(), // New Date
                    times           : [] // Array to store time and order count data
                };

                // Collect data from the table rows
                $('#scheduleTable tbody tr').each(function() {
                    var row = {
                        from            : $(this).find('input[name="from[]"]').val(),
                        to              : $(this).find('input[name="to[]"]').val(),
                        order_count     : $(this).find('input[name="order_count[]"]').val(),
                        receiver_count  : $(this).find('input[name="receiver_count[]"]').val(),
                        delivery_count  : $(this).find('input[name="delivery_count[]"]').val()
                    };
                    formData.times.push(row); // Add row data to the times array
                });

           
                // You can now send the formData object to your server using AJAX
                $.ajax({
                    url: $('#update-date-time-form').attr('action'), // Form action URL
                    method: 'POST', // HTTP method
                    data: formData, // Data to send
                    success: function(response) {
                        window.location.href = "{{ route('dashboard.category-date-times.index') }}";
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
