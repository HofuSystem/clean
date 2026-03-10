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
                        <li class="breadcrumb-item text-muted">@lang('cms')</li>
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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.cms-pages.index")}}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.cms-pages.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.cms-pages.create') }}"
                            data-mode="new"
                        @endisset>

                        @csrf
                        <div class="card-body row">

                           

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="name-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="name-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="name-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[en][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('en')?->name }} @endisset">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[ar][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('ar')?->name }} @endisset">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_parent" name="is_parent"
                                        @checked(isset($item) and $item->is_parent)>
                                    <label class="form-check-label" for="is_parent">{{ trans('is parent') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_multi_upload"
                                        name="is_multi_upload" @checked(isset($item) and $item->is_multi_upload)>
                                    <label class="form-check-label"
                                        for="is_multi_upload">{{ trans('is multi upload') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_point" name="have_point"
                                        @checked(isset($item) and $item->have_point)>
                                    <label class="form-check-label" for="have_point">{{ trans('have point') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_name" name="have_name"
                                        @checked(isset($item) and $item->have_name)>
                                    <label class="form-check-label" for="have_name">{{ trans('have name') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_description"
                                        name="have_description" @checked(isset($item) and $item->have_description)>
                                    <label class="form-check-label"
                                        for="have_description">{{ trans('have description') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_intro" name="have_intro"
                                        @checked(isset($item) and $item->have_intro)>
                                    <label class="form-check-label" for="have_intro">{{ trans('have intro') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_image" name="have_image"
                                        @checked(isset($item) and $item->have_image)>
                                    <label class="form-check-label" for="have_image">{{ trans('have image') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_tablet_image"
                                        name="have_tablet_image" @checked(isset($item) and $item->have_tablet_image)>
                                    <label class="form-check-label"
                                        for="have_tablet_image">{{ trans('have tablet image') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_mobile_image"
                                        name="have_mobile_image" @checked(isset($item) and $item->have_mobile_image)>
                                    <label class="form-check-label"
                                        for="have_mobile_image">{{ trans('have mobile image') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_icon" name="have_icon"
                                        @checked(isset($item) and $item->have_icon)>
                                    <label class="form-check-label" for="have_icon">{{ trans('have icon') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_video" name="have_video"
                                        @checked(isset($item) and $item->have_video)>
                                    <label class="form-check-label" for="have_video">{{ trans('have video') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-3 col-sm-6 ">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="have_link" name="have_link"
                                        @checked(isset($item) and $item->have_link)>
                                    <label class="form-check-label" for="have_link">{{ trans('have link') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <div class="mt-3 items-container" data-items-on       =   "cms_pages_id"
                                    data-items-name     =   "details" data-items-from     =   "cms-page-details">

                                    <h3 class="text-dark">{{ trans('details') }}</h3>
                                    <button class="btn-operation create-new-items"><i class="fas fa-plus"></i></button>
                                    <hr>
                                    <div class="table-responsive ">
                                        <table class="table table-striped table-hover text-center">
                                            <thead class="table-primary text-white text-capitalize h6">
                                                <tr>

                                                    <th scope="col" data-name="translations.en.name" data-type="text">
                                                        {{ trans('name ( en )') }}</th>
                                                    <th scope="col" data-name="translations.ar.name" data-type="text">
                                                        {{ trans('name ( ar )') }}</th>
                                              
                                                    <th scope="col" data-name="image" data-type="mediacenter">
                                                        {{ trans('image') }}</th>
                                                    <th scope="col" data-name="icon" data-type="mediacenter">
                                                        {{ trans('icon') }}</th>
                                                    <th scope="col" data-name="actions" data-type="actions">
                                                        {{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($item->details ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->translate('en')?->name }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->name }}</td>
                                                        <td>{!! \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($sItem->image) !!}</td>
                                                        <td>{!! \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($sItem->icon) !!}</td>
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
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="modal fade" id="cms-page-detailsModal" aria-hidden="true"
                        aria-labelledby="cms-page-detailsModalLabel"
                        data-store="{{ route('dashboard.cms-page-details.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="cms-page-detailsModalLabel">
                                        {{ trans('CmsPageDetail') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">

                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-name-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-name-en"
                                                            type="button" role="tab" aria-controls="items-name-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-name-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-name-ar"
                                                            type="button" role="tab" aria-controls="items-name-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-name-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-name-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[ar][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-description-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-description-en"
                                                            type="button" role="tab"
                                                            aria-controls="items-description-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-description-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-description-ar"
                                                            type="button" role="tab"
                                                            aria-controls="items-description-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-description-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="description">{{ trans('description') }}</label>
                                                            <textarea type="number" name="translations[en][description]" class="form-control "
                                                                placeholder="{{ trans('Enter description') }} "></textarea>

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-description-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="description">{{ trans('description') }}</label>
                                                            <textarea type="number" name="translations[ar][description]" class="form-control "
                                                                placeholder="{{ trans('Enter description') }} "></textarea>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-intro-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-intro-en"
                                                            type="button" role="tab" aria-controls="items-intro-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-intro-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-intro-ar"
                                                            type="button" role="tab" aria-controls="items-intro-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-intro-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="intro">{{ trans('intro') }}</label>
                                                            <textarea type="number" name="translations[en][intro]" class="form-control "
                                                                placeholder="{{ trans('Enter intro') }} "></textarea>

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-intro-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="intro">{{ trans('intro') }}</label>
                                                            <textarea type="number" name="translations[ar][intro]" class="form-control "
                                                                placeholder="{{ trans('Enter intro') }} "></textarea>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-point-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-point-en"
                                                            type="button" role="tab" aria-controls="items-point-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-point-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-point-ar"
                                                            type="button" role="tab" aria-controls="items-point-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-point-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="point">{{ trans('point') }}</label>
                                                            <textarea type="number" name="translations[en][point]" class="form-control "
                                                                placeholder="{{ trans('Enter point') }} "></textarea>

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-point-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="point">{{ trans('point') }}</label>
                                                            <textarea type="number" name="translations[ar][point]" class="form-control "
                                                                placeholder="{{ trans('Enter point') }} "></textarea>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="image">{{ trans('image') }}</label>
                                                <div class="media-center-group form-control" data-max="1"
                                                    data-type="image">
                                                    <input type="text" hidden="hidden" class="form-control"
                                                        name="image" value="">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i
                                                            class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class=""
                                                    for="tablet_image">{{ trans('tablet image') }}</label>
                                                <div class="media-center-group form-control" data-max="1"
                                                    data-type="image">
                                                    <input type="text" hidden="hidden" class="form-control"
                                                        name="tablet_image" value="">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i
                                                            class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class=""
                                                    for="mobile_image">{{ trans('mobile image') }}</label>
                                                <div class="media-center-group form-control" data-max="1"
                                                    data-type="image">
                                                    <input type="text" hidden="hidden" class="form-control"
                                                        name="mobile_image" value="">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i
                                                            class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="icon">{{ trans('icon') }}</label>
                                                <div class="media-center-group form-control" data-max="1"
                                                    data-type="image">
                                                    <input type="text" hidden="hidden" class="form-control"
                                                        name="icon" value="">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i
                                                            class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="video">{{ trans('video') }}</label>
                                                <input type="text" name="video" class="form-control "
                                                    placeholder="{{ trans('Enter video') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="link">{{ trans('link') }}</label>
                                                <input type="text" name="link" class="form-control "
                                                    placeholder="{{ trans('Enter link') }} " value="">

                                            </div>

                                            <div class="col-lg-9 ml-lg-auto">
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="cms-page-detailsDeleteModel" tabindex="-1"
                        aria-labelledby="cms-page-detailsDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cms-page-detailsDeleteModelLabel">
                                        {{ trans('Delete CmsPageDetail') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the CmsPageDetail') }} <span></span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{ trans('Close') }}</button>
                                    <button type="button"
                                        class="btn btn-danger items-final-delete">{{ trans('Delete') }}</button>
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
