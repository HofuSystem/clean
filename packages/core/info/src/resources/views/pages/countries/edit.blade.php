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
            <!--end::Container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid">
                <!--begin::Card-->
                <div class="card">
                    
                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.countries.index")}}" data-id="{{$item->id ?? null}}"
                        @isset($item)
                            action="{{ route("dashboard.countries.edit",$item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route("dashboard.countries.create") }}"
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
                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="phonecode">{{ trans("phonecode") }}</label>
                                <input type="text" name="phonecode" class="form-control "
                                    placeholder="{{ trans("Enter phonecode") }} " value="@isset($item){{ $item->phonecode }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="short_name">{{ trans("short name") }}</label>
                                <input type="text" name="short_name" class="form-control "
                                    placeholder="{{ trans("Enter short name") }} " value="@isset($item){{ $item->short_name }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="flag">{{ trans("flag") }}</label>
                                <input type="text" name="flag" class="form-control "
                                    placeholder="{{ trans("Enter flag") }} " value="@isset($item){{ $item->flag }}@endisset">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-12">
                            <div class="mt-3 items-container"
                                data-items-on       =   "country_id"
                                data-items-name     =   "cities"
                                data-items-from     =   "cities">

                                <h3 class="text-dark">{{ trans("cities") }}</h3>
                                <button class="btn-operation create-new-items"><i class="fas fa-plus"></i></button>
                                <hr>
                                <div class="table-responsive ">
                                    <table class="table table-striped table-hover text-center" >
                                        <thead class="table-primary text-white text-capitalize h6">
                                            <tr>
                                            
                                            <th  scope="col" data-name="translations.en.name" data-type="text" >{{ trans("name ( en )") }}</th>
                                            <th  scope="col" data-name="translations.ar.name" data-type="text" >{{ trans("name ( ar )") }}</th>
                                            <th  scope="col" data-name="name" data-type="text">{{ trans("name") }}</th>
                                            <th  scope="col" data-name="postal_code" data-type="text">{{ trans("postal code") }}</th>
                                            <th  scope="col" data-name="image" data-type="mediacenter">{{ trans("image") }}</th>
                                            <th  scope="col" data-name="status" data-type="select">{{ trans("status") }}</th>
                                            <th  scope="col" data-name="actions" data-type="actions">{{ trans("actions") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        @foreach ($item->cities ?? [] as $sItem)
                                            <tr data-id="{{ $sItem->id }}" data-data="{{ json_encode($sItem->itemData) }}" >
                                            
                                            <td>{{ $sItem?->translate("en")?->name }}</td>
                                            <td>{{ $sItem?->translate("ar")?->name }}</td>
                                            <td>{{ $sItem->name }}</td>
                                            <td>{{ $sItem->postal_code }}</td>
                                            <td>{!! \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($sItem->image) !!}</td>
                                            <td>{{ $sItem->status }}</td>
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
        
                <div class="modal fade" id="citiesModal" aria-hidden="true" aria-labelledby="citiesModalLabel" data-store="{{route("dashboard.cities.create")}}" >
                    <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h1 class="modal-title fs-5" id="citiesModalLabel">{{ trans("City") }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form  class="modal-form items-modal-form" >
                                <div class="row">
                                    
                         

                        <div class="col-12 mt-5">
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                            
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link active " id="items-name-en-tab" data-bs-toggle="tab" data-bs-target="#items-name-en" type="button" role="tab" aria-controls="items-name-en" aria-selected=" true">{{trans("English")}}</button>
                    			</li>
                    
                    			<li class="nav-item" role="presentation">
                    				<button class="nav-link  " id="items-name-ar-tab" data-bs-toggle="tab" data-bs-target="#items-name-ar" type="button" role="tab" aria-controls="items-name-ar" aria-selected=" false">{{trans("العربية")}}</button>
                    			</li>
                    
                            </ul>
                            <div class="tab-content mt-3" id="languageTabsContent">
                            <div class="tab-pane fade show active" id="items-name-en" role="tabpanel" aria-labelledby="en-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="name">{{ trans("name") }}</label>
                                <input type="text" name="translations[en][name]" class="form-control "
                                    placeholder="{{ trans("Enter name") }} " value="">
                                    
                            </div>

                        </div><div class="tab-pane fade " id="items-name-ar" role="tabpanel" aria-labelledby="ar-tab">
                            
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="name">{{ trans("name") }}</label>
                                <input type="text" name="translations[ar][name]" class="form-control "
                                    placeholder="{{ trans("Enter name") }} " value="">
                                    
                            </div>

                        </div>
                            </div>
                        </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="lat">{{ trans("lat") }}</label>
                                <input type="number" name="lat" class="form-control "
                                    placeholder="{{ trans("Enter lat") }} " value="">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="lng">{{ trans("lng") }}</label>
                                <input type="number" name="lng" class="form-control "
                                    placeholder="{{ trans("Enter lng") }} " value="">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="postal_code">{{ trans("postal code") }}</label>
                                <input type="text" name="postal_code" class="form-control "
                                    placeholder="{{ trans("Enter postal code") }} " value="">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="image">{{ trans("image") }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="image" value="">
                                    <button  type="button" class="btn btn-secondary media-center-load" style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="delivery_price">{{ trans("delivery price") }}</label>
                                <input type="number" name="delivery_price" class="form-control "
                                    placeholder="{{ trans("Enter delivery price") }} " value="">
                                    
                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="status">{{ trans("status") }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="country_id-status"  >
                                    
                <option  value="" >{{trans("select status")}}</option>
            <option value="active" @selected(isset($item) and $item->status == "active" ) >{{trans("active")}}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == "not-active" ) >{{trans("not-active")}}</option>
                                    
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
                <div class="modal fade" id="citiesDeleteModel" tabindex="-1" aria-labelledby="citiesDeleteModelLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="citiesDeleteModelLabel">{{ trans("Delete City") }} <span></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{ trans("Are you sure you want to delete the City") }} <span></span>?
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
