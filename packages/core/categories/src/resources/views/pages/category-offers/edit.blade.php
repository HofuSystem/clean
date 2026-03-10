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
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.category-offers.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.category-offers.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.category-offers.create") }}"
                            data-mode="new"
                        @endisset
                        >

                        @csrf
                        <div class="card-body row">
                        
                        <div class="col-12 mt-5">
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                            
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link active " id="name-en-tab" data-bs-toggle="tab" data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en" aria-selected=" true">{{trans("English")}}</button>
                    			</li>
                    
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link  " id="name-ar-tab" data-bs-toggle="tab" data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar" aria-selected=" false">{{trans("العربية")}}</button>
                    			</li>
                    
                            </ul>
                            <div class="tab-content mt-3" id="languageTabsContent">
                            <div class="tab-pane fade show active" id="name-en" role="tabpanel" aria-labelledby="en-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="name">{{ trans("name") }}</label>
                                <input type="text" name="translations[en][name]" class="form-control "
                                    placeholder="{{ trans("Enter name") }} " value="@isset($item) {{ $item?->translate('en')?->name }} @endisset">
                                    
                            </div>

                        </div><div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="name">{{ trans("name") }}</label>
                                <input type="text" name="translations[ar][name]" class="form-control "
                                    placeholder="{{ trans("Enter name") }} " value="@isset($item) {{ $item?->translate('ar')?->name }} @endisset">
                                    
                            </div>

                        </div>
                            </div>
                        </div>
                        <div class="col-12 mt-5">
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                            
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link active " id="desc-en-tab" data-bs-toggle="tab" data-bs-target="#desc-en" type="button" role="tab" aria-controls="desc-en" aria-selected=" true">{{trans("English")}}</button>
                    			</li>
                    
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link  " id="desc-ar-tab" data-bs-toggle="tab" data-bs-target="#desc-ar" type="button" role="tab" aria-controls="desc-ar" aria-selected=" false">{{trans("العربية")}}</button>
                    			</li>
                    
                            </ul>
                            <div class="tab-content mt-3" id="languageTabsContent">
                            <div class="tab-pane fade show active" id="desc-en" role="tabpanel" aria-labelledby="en-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="desc">{{ trans("desc") }}</label>
                <textarea type="number" name="translations[en][desc]" class="form-control "
                    placeholder="{{ trans("Enter desc") }} " >@isset($item) {{ $item?->translate('en')?->desc }} @endisset</textarea>
                    
                            </div>

                        </div><div class="tab-pane fade " id="desc-ar" role="tabpanel" aria-labelledby="ar-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="desc">{{ trans("desc") }}</label>
                <textarea type="number" name="translations[ar][desc]" class="form-control "
                    placeholder="{{ trans("Enter desc") }} " >@isset($item) {{ $item?->translate('ar')?->desc }} @endisset</textarea>
                    
                            </div>

                        </div>
                            </div>
                        </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="price">{{ trans("price") }}</label>
                                <input type="number" name="price" class="form-control "
                                    placeholder="{{ trans("Enter price") }} " value="{{ old("price" , $item->price ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="sale_price">{{ trans("sale price") }}</label>
                                <input type="number" name="sale_price" class="form-control "
                                    placeholder="{{ trans("Enter sale price") }} " value="{{ old("sale_price" , $item->sale_price ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="image">{{ trans("image") }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="image" value="{{ old("image" , $item->image ?? null) }}">
                                    <button  type="button" class="btn btn-secondary media-center-load" style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="hours_num">{{ trans("hours num") }}</label>
                                <input type="number" name="hours_num" class="form-control "
                                    placeholder="{{ trans("Enter hours num") }} " value="{{ old("hours_num" , $item->hours_num ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="workers_num">{{ trans("workers num") }}</label>
                                <input type="number" name="workers_num" class="form-control "
                                    placeholder="{{ trans("Enter workers num") }} " value="{{ old("workers_num" , $item->workers_num ?? null) }}">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="status">{{ trans("status") }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status"  >
                                    
                <option  value="" >{{trans("select status")}}</option>
            <option value="active" @selected(isset($item) and $item->status == "active" ) >{{trans("active")}}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == "not-active" ) >{{trans("not active")}}</option>
                                    
                                </select>
                                            
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="type">{{ trans("type") }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type"  >
                                    
                <option  value="" >{{trans("select type")}}</option>
            <option value="services" @selected(isset($item) and $item->type == "services" ) >{{trans("services")}}</option>
                                    <option value="clothes" @selected(isset($item) and $item->type == "clothes" ) >{{trans("clothes")}}</option>
                                    <option value="sales" @selected(isset($item) and $item->type == "sales" ) >{{trans("sales")}}</option>
                                    <option value="maid" @selected(isset($item) and $item->type == "maid" ) >{{trans("maid")}}</option>
                                    <option value="host" @selected(isset($item) and $item->type == "host" ) >{{trans("host")}}</option>
                                    
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
