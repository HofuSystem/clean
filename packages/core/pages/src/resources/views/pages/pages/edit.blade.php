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
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"></h1>
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
                        <li class="breadcrumb-item text-muted">@lang('pages')</li>
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


                    <form class="form" method="POST" id="operation-form" data-id="{{ $item->id ?? null }}"
                        @if (isset($item)) action="{{ route('dashboard.pages.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.pages.create') }}"
                            data-mode="new" @endif>

                        @csrf
                        <div class="card-body row">

                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="slug">{{ trans('slug') }}</label>
                                <input type="text" name="slug" class="form-control "
                                    placeholder="{{ trans('Enter slug') }} "
                                    value="@if (isset($item)) {{ $item->slug }} @endif">

                            </div>

                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-en" type="button" role="tab"
                                            aria-controls="title-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-ar" type="button" role="tab"
                                            aria-controls="title-ar" aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[en][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@if (isset($item)) {{ $item?->translate('en')?->title }} @endif">

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="title">{{ trans('title') }}</label>
                                            <input type="text" name="translations[ar][title]" class="form-control "
                                                placeholder="{{ trans('Enter title') }} "
                                                value="@if (isset($item)) {{ $item?->translate('ar')?->title }} @endif">

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12 row mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="description-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-en" type="button" role="tab"
                                            aria-controls="description-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="description-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-ar" type="button" role="tab"
                                            aria-controls="description-ar"
                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="description-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="description">{{ trans('description') }}</label>
                                            <textarea type="number" name="translations[en][description]" class="form-control "
                                                placeholder="{{ trans('Enter description') }} ">
@if (isset($item))
{{ $item?->translate('en')?->description }}
@endif
</textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="description-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="description">{{ trans('description') }}</label>
                                            <textarea type="number" name="translations[ar][description]" class="form-control "
                                                placeholder="{{ trans('Enter description') }} ">@if (isset($item)){{ $item?->translate('ar')?->description }}@endif</textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="image">{{ trans('image') }}</label>
                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                    <input type="text" hidden="hidden" class="form-control" name="image"
                                        value="{{ old('image', $item->image ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        @checked(isset($item) and $item->is_active)>
                                    <label class="form-check-label" for="is_active">{{ trans('is active') }}</label>
                                </div>

                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="meta_title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#meta_title-en" type="button" role="tab"
                                            aria-controls="meta_title-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="meta_title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#meta_title-ar" type="button" role="tab"
                                            aria-controls="meta_title-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="meta_title-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="meta_title">{{ trans('meta_title') }}</label>
                                            <input type="text" name="translations[en][meta_title]"
                                                class="form-control " placeholder="{{ trans('Enter meta_title') }} "
                                                value="@isset($item){{ $item?->translate('en')?->meta_title }}@endisset" />

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="meta_title-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="meta_title">{{ trans('meta_title') }}</label>
                                            <input type="text" name="translations[ar][meta_title]"
                                                class="form-control " placeholder="{{ trans('Enter meta_title') }} "
                                                value="@isset($item){{ $item?->translate('ar')?->meta_title }}@endisset" />

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="meta_description-en-tab"
                                            data-bs-toggle="tab" data-bs-target="#meta_description-en" type="button"
                                            role="tab" aria-controls="meta_description-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="meta_description-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#meta_description-ar" type="button" role="tab"
                                            aria-controls="meta_description-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="meta_description-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="meta_description">{{ trans('meta_description') }}</label>
                                            <input type="text" name="translations[en][meta_description]"
                                                class="form-control " placeholder="{{ trans('Enter meta_description') }} "
                                                value="@isset($item){{ $item?->translate('en')?->meta_description }}@endisset" />

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="meta_description-ar" role="tabpanel"
                                        aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class=""
                                                for="meta_description">{{ trans('meta_description') }}</label>
                                            <input type="text" name="translations[ar][meta_description]"
                                                class="form-control " placeholder="{{ trans('Enter meta_description') }} "
                                                value="@isset($item){{ $item?->translate('ar')?->meta_description }}@endisset" />

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <div class="mt-3 items-container" data-items-on       =   "page_id"
                                    data-items-name     =   "sections" data-items-from     =   "sections">

                                    <h3>{{ trans('sections') }}</h3>
                                    <button class="btn btn-operation create-new-items"><i
                                            class="fas fa-plus"></i></button>
                                    <hr>
                                    <div class="table-responsive ">
                                        <table class="table table-striped table-hover text-center">
                                            <thead class="table-primary">
                                                <tr>

                                                    <th scope="col" data-name="translations.en.title"
                                                        data-type="text">{{ trans('title') }}( en )</th>
                                                    <th scope="col" data-name="translations.ar.title"
                                                        data-type="text">{{ trans('title') }}( ar )</th>
                                                    <th scope="col" data-name="translations.en.small_title"
                                                        data-type="text">{{ trans('small title') }}( en )</th>
                                                    <th scope="col" data-name="translations.ar.small_title"
                                                        data-type="text">{{ trans('small title') }}( ar )</th>
                                                    <th scope="col" data-name="translations.en.description"
                                                        data-type="content">{{ trans('description') }}( en )</th>
                                                    <th scope="col" data-name="translations.ar.description"
                                                        data-type="content">{{ trans('description') }}( ar )</th>
                                                    <th scope="col" data-name="images" data-type="mediacenter">
                                                        {{ trans('images') }}</th>
                                                    <th scope="col" data-name="template" data-type="select">
                                                        {{ trans('template') }}</th>
                                                    <th scope="col" data-name="actions" data-type="actions">
                                                        {{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($item->sections ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->translate('en')?->title }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->title }}</td>
                                                        <td>{{ $sItem?->translate('en')?->small_title }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->small_title }}</td>
                                                        <td>{{ $sItem?->translate('en')?->description }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->description }}</td>
                                                        <td>{!! \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($sItem->images) !!}</td>
                                                        <td>{{ $sItem->template }}</td>
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
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="modal fade" id="sectionsModal" aria-hidden="true" aria-labelledby="sectionsModalLabel"
                        data-store="{{ route('dashboard.sections.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="sectionsModalLabel">{{ trans('sections') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">

                                            <div class="col-12 row mt-5" id="title-div">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-title-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-title-en"
                                                            type="button" role="tab" aria-controls="items-title-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-title-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-title-ar"
                                                            type="button" role="tab" aria-controls="items-title-ar"
                                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-title-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="title">{{ trans('title') }}</label>
                                                            <input type="text" name="translations[en][title]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter title') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-title-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="title">{{ trans('title') }}</label>
                                                            <input type="text" name="translations[ar][title]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter title') }} " value="">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 row mt-5" id="small_title-div">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-small_title-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-small_title-en"
                                                            type="button" role="tab"
                                                            aria-controls="items-small_title-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-small_title-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-small_title-ar"
                                                            type="button" role="tab"
                                                            aria-controls="items-small_title-ar"
                                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-small_title-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="small_title">{{ trans('small title') }}</label>
                                                            <input type="text" name="translations[en][small_title]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter small title') }} "
                                                                value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-small_title-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="small_title">{{ trans('small title') }}</label>
                                                            <input type="text" name="translations[ar][small_title]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter small title') }} "
                                                                value="">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 row mt-5" id="description-div">
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
                                                            aria-selected=" false">{{ trans('arabic') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-description-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="description">{{ trans('description') }}</label>
                                                            <div class="editor-container">
                                                                <div id="sections-description"
                                                                    name="translations[en][description]"></div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-description-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="description">{{ trans('description') }}</label>
                                                            <div class="editor-container">
                                                                <div id="sections-description"
                                                                    name="translations[ar][description]"></div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3 col-md-12" id="image-div">
                                                <label class="" for="images">{{ trans('images') }}</label>
                                                <div class="media-center-group form-control" data-max="3"
                                                    data-type="gallery">
                                                    <input type="text" hidden="hidden" class="form-control"
                                                        name="images" value="">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i
                                                            class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12" id="video-div">
                                                <label class="" for="video">{{ trans('video') }}</label>
                                                <input type="text" name="video" class="form-control "
                                                    placeholder="{{ trans('Enter video') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="template">{{ trans('template') }}</label>
                                                <select class="custom-select  form-select advance-select" name="template"
                                                    id="page_id-template">
                                                    <option value="">{{ trans('Select template') }}</option>
                                                    <option value="hero">{{ trans('hero') }}</option>
                                                    <option value="services">{{ trans('services') }}</option>
                                                    <option value="b2b">{{ trans('b2b') }}</option>
                                                    <option value="blogs">{{ trans('blogs') }}</option>
                                                    <option value="faq">{{ trans('faq') }}</option>
                                                    <option value="contact">{{ trans('contact') }}</option>
                                                    <option value="testimonials">{{ trans('testimonials') }}</option>
                                                    <option value="why-us">{{ trans('why-us') }}</option>
                                                    <option value="app-features">{{ trans('app-features') }}</option>
                                                </select>

                                            </div>

                                            <div class="col-lg-9 ml-lg-auto">
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold mr-2">{{ trans('Submit') }}</button>
                                            </div>

                                            <div class="col-12" id="entities-div">
                                               
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="sectionsDeleteModel" tabindex="-1"
                        aria-labelledby="sectionsDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="sectionsDeleteModelLabel">{{ trans('Delete sections') }}
                                        <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the sections') }} <span></span>?
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
    <script>
        $(document).ready(function() {
            var sectionsData = {!! json_encode($sectionsData) !!};
            $('#page_id-template').change(function() {
                var template = $(this).val();
                $('#title-div').hide();
                $('#small_title-div').hide();
                $('#description-div').hide();
                $('#image-div').hide();
                $('#video-div').hide();
                $('#entities-div').empty();
                let has = sectionsData[template].has;
                has.forEach(element => {
                    $('#' + element + '-div').show();
                });
                let entities = sectionsData[template].entity;
                let entitiesHtml = '<hr>';
                entities.forEach(element => {
                    if(element.route && element.title_key){
                        entitiesHtml += `<a class="btn btn-success m-2" href="${element.route}" target="_blank">${element.title_key} + </a>`;
                    }
                });
                $('#entities-div').html(entitiesHtml);
            });
        });
    </script>
@endpush
