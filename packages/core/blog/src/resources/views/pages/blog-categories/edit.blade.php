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
                        <li class="breadcrumb-item text-muted">@lang("blog")</li>
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
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.blog-categories.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.blog-categories.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.blog-categories.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                        <div class="col-12 mt-5">
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                            
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link active " id="title-en-tab" data-bs-toggle="tab" data-bs-target="#title-en" type="button" role="tab" aria-controls="title-en" aria-selected=" true">{{trans("English")}}</button>
                    			</li>
                    
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link  " id="title-ar-tab" data-bs-toggle="tab" data-bs-target="#title-ar" type="button" role="tab" aria-controls="title-ar" aria-selected=" false">{{trans("العربية")}}</button>
                    			</li>
                    
                            </ul>
                            <div class="tab-content mt-3" id="languageTabsContent">
                            <div class="tab-pane fade show active" id="title-en" role="tabpanel" aria-labelledby="en-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="title">{{ trans("title") }}</label>
                                <input type="text" name="translations[en][title]" class="form-control "
                                    placeholder="{{ trans("Enter title") }} " value="@isset($item) {{ $item?->translate('en')?->title }} @endisset">
                                    
                            </div>

                        </div><div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="title">{{ trans("title") }}</label>
                                <input type="text" name="translations[ar][title]" class="form-control "
                                    placeholder="{{ trans("Enter title") }} " value="@isset($item) {{ $item?->translate('ar')?->title }} @endisset">
                                    
                            </div>

                        </div>
                            </div>
                        </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="parent_id">{{ trans("parent") }}</label>
                                <select class="custom-select  form-select advance-select" name="parent_id" id="parent_id"  >
                                    
                                    <option   value="" >{{trans("select parent")}}</option>
                                    @foreach($parents ?? [] as $sItem)
                                        <option data-id="{{$sItem->id }}" @selected(isset($item)  and $item->parent_id == $sItem->id) value="{{$sItem->id }}" >{{$sItem->title}}</option>
                                    @endforeach
            
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="status">{{ trans("status") }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status"  >
                                    
                <option  value="" >{{trans("select status")}}</option>
            <option value="active" @selected(isset($item) and $item->status == "active" ) >{{trans("active")}}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == "not-active" ) >{{trans("not-active")}}</option>
                                    
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
