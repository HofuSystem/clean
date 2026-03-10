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
                            <a href=""
                                class="text-muted text-hover-primary">@lang('Home')</a>
                        </li>
                        <!--end::Item-->
                        
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">@lang("workers")</li>
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
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.workers.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.workers.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.workers.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="image">{{ trans("image") }}</label>
                                <div class="media-center-group form-control" data-max="" data-type="">
                                    <input type="text" hidden="hidden" class="form-control" name="image" value="{{ old("image" , $item->image ?? null) }}">
                                    <button  type="button" class="btn btn-secondary media-center-load" style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="name">{{ trans("name") }}</label>
                                <input type="text" name="name" class="form-control "
                                    placeholder="{{ trans("Enter name") }} " value="@isset($item){{ $item->name }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="phone">{{ trans("phone") }}</label>
                                <input type="text" name="phone" class="form-control "
                                    placeholder="{{ trans("Enter phone") }} " value="@isset($item){{ $item->phone }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="email">{{ trans("email") }}</label>
                                <input type="email" name="email" class="form-control "
                                    placeholder="{{ trans("Enter email") }} " value="{{ old("email" , $item->email ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="years_experience">{{ trans("years experience") }}</label>
                                <input type="number" name="years_experience" class="form-control "
                                    placeholder="{{ trans("Enter years experience") }} " value="{{ old("years_experience" , $item->years_experience ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="address">{{ trans("address") }}</label>
                                <input type="text" name="address" class="form-control "
                                    placeholder="{{ trans("Enter address") }} " value="@isset($item){{ $item->address }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="birth_date">{{ trans("birth date") }}</label>
                                <input type="date" name="birth_date" class="form-control "
                                    placeholder="{{ trans("Enter birth date") }} " value="@isset($item){{ $item->birth_date }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="hour_price">{{ trans("hour price") }}</label>
                                <input type="number" name="hour_price" class="form-control "
                                    placeholder="{{ trans("Enter hour price") }} " value="{{ old("hour_price" , $item->hour_price ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="gender">{{ trans("gender") }}</label>
                                <select class="custom-select  form-select advance-select" name="gender" id="gender"  >
                                    
                <option  value="" >{{trans("select gender")}}</option>
            <option value="male" @selected(isset($item) and $item->gender == "male" ) >{{trans("male")}}</option>
                                    <option value="female" @selected(isset($item) and $item->gender == "female" ) >{{trans("female")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="status">{{ trans("status") }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status"  >
                                    
                <option  value="" >{{trans("select status")}}</option>
            <option value="active" @selected(isset($item) and $item->status == "active" ) >{{trans("active")}}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == "not-active" ) >{{trans("not-active")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="identity">{{ trans("identity") }}</label>
                                <select class="custom-select  form-select advance-select" name="identity" id="identity"  >
                                    
                <option  value="" >{{trans("select identity")}}</option>
            <option value="passport" @selected(isset($item) and $item->identity == "passport" ) >{{trans("passport")}}</option>
                                    <option value="id" @selected(isset($item) and $item->identity == "id" ) >{{trans("id")}}</option>
                                    <option value="driver-licence" @selected(isset($item) and $item->identity == "driver-licence" ) >{{trans("driver-licence")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="nationality_id">{{ trans("nationality") }}</label>
                                <select class="custom-select  form-select advance-select" name="nationality_id" id="nationality_id"  >
                                    
                                    <option   value="" >{{trans("select nationality")}}</option>
                                    @foreach($nationalities ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->nationality_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="city_id">{{ trans("city") }}</label>
                                <select class="custom-select  form-select advance-select" name="city_id" id="city_id"  >
                                    
                                    <option   value="" >{{trans("select city")}}</option>
                                    @foreach($cities ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->city_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="categories">{{ trans("categories") }}</label>
                                <select class="custom-select  form-select advance-select" name="categories" id="categories" multiple >
                                    
                                    <option   value="" >{{trans("select categories")}}</option>
                                    @foreach($categories ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item) and $item->categories->where('id', $sItem->id)->first()) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="leader_id">{{ trans("leader_id") }}</label>
                                <select class="custom-select  form-select advance-select" name="leader_id" id="leader_id"  >
                                    
                                    <option   value="" >{{trans("select leader_id")}}</option>
                                    @foreach($leaders ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item) and $item->leader_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->fullname}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                            <div class="mt-3 items-container"
                                data-items-on       =   "worker_id"
                                data-items-name     =   "workdays"
                                data-items-from     =   "worker-days">

                                <h3 class="text-dark">{{ trans("work days") }}</h3>
                                <button class="btn-operation create-new-items"><i class="fas fa-plus"></i></button>
                                <hr>
                                <div class="table-responsive ">
                                    <table class="table table-striped table-hover text-center" >
                                        <thead class="table-primary text-white text-capitalize h6">
                                            <tr>
                                            
                                            <th  scope="col" data-name="date" data-type="date">{{ trans("date") }}</th>
                                            <th  scope="col" data-name="type" data-type="select">{{ trans("type") }}</th>
                                            <th  scope="col" data-name="actions" data-type="actions">{{ trans("actions") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        @foreach ($item->workdays ?? [] as $sItem)
                                            <tr data-id="{{ $sItem->id }}" data-data="{{ json_encode($sItem->itemData) }}" >
                                            
                                            <td>{{ $sItem->date }}</td>
                                            <td>{{ $sItem->type }}</td>
                                            <td class="options">{!! $sItem->itemsActions !!}</td>
                                            </tr>
                                        @endforeach
        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
        
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
        
                <div class="modal fade" id="worker-daysModal" aria-hidden="true" aria-labelledby="worker-daysModalLabel" data-store="{{route("dashboard.worker-days.create")}}" >
                    <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h1 class="modal-title fs-5" id="worker-daysModalLabel">{{ trans("WorkerDay") }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form  class="modal-form items-modal-form" >
                                <div class="row">
                                    
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="date">{{ trans("date") }}</label>
                                <input type="date" name="date" class="form-control "
                                    placeholder="{{ trans("Enter date") }} " value="">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="type">{{ trans("type") }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="worker_id-type"  >
                                    
                <option  value="" >{{trans("select type")}}</option>
            <option value="absence" @selected(isset($item) and $item->type == "absence" ) >{{trans("absence")}}</option>
                                    <option value="attendees" @selected(isset($item) and $item->type == "attendees" ) >{{trans("attendees")}}</option>
                                    
                                </select>
                                            
                            </div>

                                <div class="col-lg-9 ml-lg-auto">
                                    <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    </div>
                </div>
                <div class="modal fade" id="worker-daysDeleteModel" tabindex="-1" aria-labelledby="worker-daysDeleteModelLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="worker-daysDeleteModelLabel">{{ trans("Delete WorkerDay") }} <span></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{ trans("Are you sure you want to delete the WorkerDay") }} <span></span>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans("Close") }}</button>
                                <button type="button" class="btn btn-danger items-final-delete">{{ trans("Delete") }}</button>
                            </div>
                        </div>
                    </div>
                </div>

        
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
