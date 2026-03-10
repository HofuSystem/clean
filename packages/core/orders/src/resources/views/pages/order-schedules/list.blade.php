@extends('admin::layouts.dashboard')
@section('content')
    <!--end::Header-->

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
                        <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
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
        <!--begin::Container-->
        <div  class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header row">
                    <!--begin::Card title-->
                    <div class="card-title col-md-6 ">
                        <!--begin::cols-->
                        <div class="form-group d-flex justify-content-center">
                            <label class="text-dark fw-bold" for="visible_cols"> @lang('visible cols')</label>
                            <select class="form-control mx-3" data-control="select2" name="visible_cols"
                                id="visible_cols" multiple></select>
                        </div>
                        <!--end::cols-->
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar col-md-6">
                        <!--begin::Toolbar-->

                        <div  data-kt-user-table-toolbar="base">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <div class="d-flex">
                                     
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-success text-success rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.order-schedules.index') }}">
                                            <div class="fw-bolder fs-5 text-success" > 
                                                {{ $total }} 
                                                <i class="fas fa-list-alt"></i>
                                                @lang('total')
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                        <!--begin::Stat-->
                                        <div class="border border-dashed border-danger  text-danger rounded mx-1 p-2">
                                            <a href="{{ route('dashboard.order-schedules.index',['trash' => 1]) }}">
                                            <div class="fw-bolder fs-5 text-danger" >
                                                {{ $trash }}
                                                <i class="fas fa-trash-alt"></i>
                                                @lang('Trash')
                                            </div>
                                            </a>
                                        </div>
                                        <!--end::Stat-->
                                    </div>
                                </div>
                                <div class="d-flex">

                                    @can('dashboard.order-schedules.export')
                                        <a href="{{ route('dashboard.order-schedules.export') }}" id="export" type="button"
                                            class="btn-operation ">
                                            <i class="fas fa-upload"></i>
                                            <span>
                                                @lang('Export Report')
                                            </span>
                                        </a>
                                    @endcan
                                    @can('dashboard.order-schedules.import')
                                        <a href="{{ route('dashboard.order-schedules.import') }}"
                                            class="btn-operation">
                                            <i class="fas fa-file-import"></i>
                                            <span>
                                                @lang('import list')
                                            </span>
                                        </a>
                                    @endcan
                                    <!--begin::Add -->
                                    @can('dashboard.order-schedules.create')
                                        <a href="{{ route('dashboard.order-schedules.create') }}"
                                            class="btn-operation ">
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

                                    
                                        <div class="col-6 mb-1">
                                            <label for="client_id">@lang("client")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="client_id" id="client_id">
                                                
                                                <option  value="" > @lang("select clients")</option>
                                                @foreach($clients as $item)
                                                    <option value="{{$item->id }}" @selected($item->id  == request("client_id")) >@lang($item->fullname)</option>
                                                @endforeach
            
                                            </select>
                                        </div>
                                    
                                        <div class="col-6 mb-1">
                                            <label for="type">@lang("type")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="type" id="type">
                                                <option value=""> @lang("select type")</option><option value="day" @selected("day" == request("type")) >{{trans("day")}}</option><option value="date" @selected("date" == request("type")) >{{trans("date")}}</option>
                                            </select>
                                        </div>
                                    
                                        <div class="col-6 mb-1">
                                            <label for="receiver_day">@lang("receiver day")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="receiver_day" id="receiver_day">
                                                <option value=""> @lang("select receiver_day")</option><option value="sunday" @selected("sunday" == request("receiver_day")) >{{trans("sunday")}}</option><option value="monday" @selected("monday" == request("receiver_day")) >{{trans("monday")}}</option><option value="tuesday" @selected("tuesday" == request("receiver_day")) >{{trans("tuesday")}}</option><option value="wednesday" @selected("wednesday" == request("receiver_day")) >{{trans("wednesday")}}</option><option value="thursday" @selected("thursday" == request("receiver_day")) >{{trans("thursday")}}</option><option value="friday" @selected("friday" == request("receiver_day")) >{{trans("friday")}}</option><option value="saturday" @selected("saturday" == request("receiver_day")) >{{trans("saturday")}}</option>
                                            </select>
                                        </div>
                                    
                                        <div class="col-6 mb-1">
                                        <label for="receiver_date"> @lang("receiver date from") </label>
                                        <input type="date" name="from_receiver_date" class="form-control filter-input"
                                            placeholder="@lang("search for receiver date") " value="{{ request("receiver_date") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="receiver_date"> @lang("receiver date to") </label>
                                            <input type="date" name="to_receiver_date" class="form-control filter-input"
                                                placeholder="@lang("search for receiver date") " value="{{ request("receiver_date") }}">
                                        </div>
                                        <div class="col-6 mb-1">
                                        <label for="receiver_time"> @lang("receiver time from") </label>
                                        <input type="time" name="from_receiver_time" class="form-control filter-input"
                                            placeholder="@lang("search for receiver time") " value="{{ request("receiver_time") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="receiver_time"> @lang("receiver time to") </label>
                                            <input type="time" name="to_receiver_time" class="form-control filter-input"
                                                placeholder="@lang("search for receiver time") " value="{{ request("receiver_time") }}">
                                        </div>
                                        <div class="col-6 mb-1">
                                        <label for="receiver_to_time"> @lang("receiver to time from") </label>
                                        <input type="time" name="from_receiver_to_time" class="form-control filter-input"
                                            placeholder="@lang("search for receiver to time") " value="{{ request("receiver_to_time") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="receiver_to_time"> @lang("receiver to time to") </label>
                                            <input type="time" name="to_receiver_to_time" class="form-control filter-input"
                                                placeholder="@lang("search for receiver to time") " value="{{ request("receiver_to_time") }}">
                                        </div>
                                        <div class="col-6 mb-1">
                                            <label for="delivery_day">@lang("delivery day")</label>
                                            <select class="custom-select filter-input form-select advance-select" name="delivery_day" id="delivery_day">
                                                <option value=""> @lang("select delivery_day")</option><option value="sunday" @selected("sunday" == request("delivery_day")) >{{trans("sunday")}}</option><option value="monday" @selected("monday" == request("delivery_day")) >{{trans("monday")}}</option><option value="tuesday" @selected("tuesday" == request("delivery_day")) >{{trans("tuesday")}}</option><option value="wednesday" @selected("wednesday" == request("delivery_day")) >{{trans("wednesday")}}</option><option value="thursday" @selected("thursday" == request("delivery_day")) >{{trans("thursday")}}</option><option value="friday" @selected("friday" == request("delivery_day")) >{{trans("friday")}}</option><option value="saturday" @selected("saturday" == request("delivery_day")) >{{trans("saturday")}}</option>
                                            </select>
                                        </div>
                                    
                                        <div class="col-6 mb-1">
                                        <label for="delivery_date"> @lang("delivery date from") </label>
                                        <input type="date" name="from_delivery_date" class="form-control filter-input"
                                            placeholder="@lang("search for delivery date") " value="{{ request("delivery_date") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="delivery_date"> @lang("delivery date to") </label>
                                            <input type="date" name="to_delivery_date" class="form-control filter-input"
                                                placeholder="@lang("search for delivery date") " value="{{ request("delivery_date") }}">
                                        </div>
                                        <div class="col-6 mb-1">
                                        <label for="delivery_time"> @lang("delivery time from") </label>
                                        <input type="time" name="from_delivery_time" class="form-control filter-input"
                                            placeholder="@lang("search for delivery time") " value="{{ request("delivery_time") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="delivery_time"> @lang("delivery time to") </label>
                                            <input type="time" name="to_delivery_time" class="form-control filter-input"
                                                placeholder="@lang("search for delivery time") " value="{{ request("delivery_time") }}">
                                        </div>
                                        <div class="col-6 mb-1">
                                        <label for="delivery_to_time"> @lang("delivery to time from") </label>
                                        <input type="time" name="from_delivery_to_time" class="form-control filter-input"
                                            placeholder="@lang("search for delivery to time") " value="{{ request("delivery_to_time") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="delivery_to_time"> @lang("delivery to time to") </label>
                                            <input type="time" name="to_delivery_to_time" class="form-control filter-input"
                                                placeholder="@lang("search for delivery to time") " value="{{ request("delivery_to_time") }}">
                                        </div>
                                        <div class="col-6 mb-1">
                                        <label for="created_at"> @lang("Create Date from") </label>
                                        <input type="datetime-local" name="from_created_at" class="form-control filter-input"
                                            placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="created_at"> @lang("Create Date to") </label>
                                            <input type="datetime-local" name="to_created_at" class="form-control filter-input"
                                                placeholder="@lang("search for Create Date") " value="{{ request("created_at") }}">
                                        </div>
                                        <div class="col-6 mb-1">
                                        <label for="updated_at"> @lang("Last update Date from") </label>
                                        <input type="datetime-local" name="from_updated_at" class="form-control filter-input"
                                            placeholder="@lang("search for Last update Date") " value="{{ request("updated_at") }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                            <label for="updated_at"> @lang("Last update Date to") </label>
                                            <input type="datetime-local" name="to_updated_at" class="form-control filter-input"
                                                placeholder="@lang("search for Last update Date") " value="{{ request("updated_at") }}">
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
                        data-load="{{ route('dashboard.order-schedules.index',['trash' => request()->trash]) }}">
                        <!--begin::Table head-->
                        <thead class="table-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                
                                    <th class="w-10px pe-2" data-name="select_switch">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#view-datatable .form-check-input" value="1">
                                        </div>
                                    </th>
                                    <th class="text-center p-0" data-name="id">@lang("id")</th>

                                    
                                    <th class="text-center p-0" data-name="client_id">@lang("client")</th>
                                    <th class="text-center p-0" data-name="type">@lang("type")</th>
                                    <th class="text-center p-0" data-name="receiver_day">@lang("receiver day")</th>
                                    <th class="text-center p-0" data-name="receiver_date">@lang("receiver date")</th>
                                    <th class="text-center p-0" data-name="receiver_time">@lang("receiver time")</th>
                                    <th class="text-center p-0" data-name="receiver_to_time">@lang("receiver to time")</th>
                                    <th class="text-center p-0" data-name="delivery_day">@lang("delivery day")</th>
                                    <th class="text-center p-0" data-name="delivery_date">@lang("delivery date")</th>
                                    <th class="text-center p-0" data-name="delivery_time">@lang("delivery time")</th>
                                    <th class="text-center p-0" data-name="delivery_to_time">@lang("delivery to time")</th>
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
@endsection
@push('css')

@endpush
@push('js')
<script>
    var deleteUrl = "{{ route('dashboard.order-schedules.delete', ['id'=>'%s','trash'=>request()->trash]) }}"
</script>
@endpush
