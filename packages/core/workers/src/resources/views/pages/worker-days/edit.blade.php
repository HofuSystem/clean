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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.worker-days.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.worker-days.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.worker-days.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="worker_id">{{ trans("worker") }}</label>
                                <select class="custom-select  form-select advance-select" name="worker_id" id="worker_id"  >

                                    <option   value="" >{{trans("select worker")}}</option>
                                    @foreach($workers ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->worker_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->name}}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="date">{{ trans("date") }}</label>
                                <input type="date" name="date" class="form-control "
                                    placeholder="{{ trans("Enter date") }} " value="@isset($item){{ $item->date }}@endisset">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="type">{{ trans("type") }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type"  >

                <option  value="" >{{trans("select type")}}</option>
            <option value="absence" @selected(isset($item) and $item->type == "absence" ) >{{trans("absence")}}</option>
                                    <option value="attendees" @selected(isset($item) and $item->type == "attendees" ) >{{trans("attendees")}}</option>

                                </select>

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
