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
                <div class="card page-editor-card">


                    <form class="form page-editor-form" method="POST" id="operation-form" data-id="{{ $item->id ?? null }}"
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

                            <div class="col-12 row mt-3">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="title-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-en" type="button" role="tab"
                                            aria-controls="title-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="title-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#title-ar" type="button" role="tab"
                                            aria-controls="title-ar" aria-selected=" false">العربية</button>
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
                            <div class="col-12 row mt-3">
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
                                            aria-selected=" false">العربية</button>
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

                            <div class="col-12 mt-3">
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

                            <div class="col-12 mt-3">
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

                                                    <th scope="col" data-name="translations.ar.title"
                                                        data-type="text">{{ app()->getLocale() === 'ar' ? 'العنوان (العربية)' : trans('title') . ' (Arabic)' }}</th>
                                                    <th scope="col" data-name="translations.ar.small_title"
                                                        data-type="text">{{ app()->getLocale() === 'ar' ? 'العنوان الفرعي (العربية)' : trans('small title') . ' (Arabic)' }}</th>
                                                    <th scope="col" data-name="translations.ar.description"
                                                        data-type="content">{{ app()->getLocale() === 'ar' ? 'الوصف (العربية)' : trans('description') . ' (Arabic)' }}</th>
                                                    <th scope="col" data-name="translations.en.title"
                                                        data-type="text">{{ app()->getLocale() === 'ar' ? 'العنوان (الإنجليزية)' : trans('title') . ' (English)' }}</th>
                                                    <th scope="col" data-name="translations.en.small_title"
                                                        data-type="text">{{ app()->getLocale() === 'ar' ? 'العنوان الفرعي (الإنجليزية)' : trans('small title') . ' (English)' }}</th>
                                                    <th scope="col" data-name="translations.en.description"
                                                        data-type="content">{{ app()->getLocale() === 'ar' ? 'الوصف (الإنجليزية)' : trans('description') . ' (English)' }}</th>
                                                    <th scope="col" data-name="images" data-type="mediacenter">
                                                        {{ trans('images') }}</th>
                                                    <th scope="col" data-name="template" data-type="select">
                                                        {{ app()->getLocale() === 'ar' ? 'القالب' : trans('template') }}</th>
                                                    <th scope="col" data-name="actions" data-type="actions">
                                                        {{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($item->sections ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->translate('ar')?->title }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->small_title }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->description }}</td>
                                                        <td>{{ $sItem?->translate('en')?->title }}</td>
                                                        <td>{{ $sItem?->translate('en')?->small_title }}</td>
                                                        <td>{{ $sItem?->translate('en')?->description }}</td>
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

                                            <div class="col-12 row mt-3" id="title-div">
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
                                                            aria-selected=" false">العربية</button>
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
                                            <div class="col-12 row mt-3" id="small_title-div">
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
                                                            aria-selected=" false">العربية</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-small_title-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                                <label class=""
                                                                for="small_title">{{ app()->getLocale() === 'ar' ? 'العنوان الفرعي' : trans('small title') }}</label>
                                                            <input type="text" name="translations[en][small_title]"
                                                                class="form-control "
                                                                placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل العنوان الفرعي' : trans('Enter small title') }} "
                                                                value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-small_title-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                                <label class=""
                                                                for="small_title">{{ app()->getLocale() === 'ar' ? 'العنوان الفرعي' : trans('small title') }}</label>
                                                            <input type="text" name="translations[ar][small_title]"
                                                                class="form-control "
                                                                placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل العنوان الفرعي' : trans('Enter small title') }} "
                                                                value="">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 row mt-3" id="description-div">
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
                                                            aria-selected=" false">العربية</button>
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
                                                <label class="" for="template">{{ app()->getLocale() === 'ar' ? 'القالب' : trans('template') }}</label>
                                                <select class="custom-select  form-select advance-select" name="template"
                                                    id="page_id-template">
                                                    <option value="">{{ trans('Select template') }}</option>
                                                    <option value="hero">{{ trans('hero') }}</option>
                                                    <option value="order-modes">{{ app()->getLocale() === 'ar' ? 'طرق الطلب' : 'Order methods' }}</option>
                                                    <option value="pricing">{{ app()->getLocale() === 'ar' ? 'الأسعار' : 'Pricing' }}</option>
                                                    <option value="services">{{ trans('services') }}</option>
                                                    <option value="faq">{{ trans('faq') }}</option>
                                                    <option value="coverage">{{ app()->getLocale() === 'ar' ? 'التغطية' : 'Coverage' }}</option>
                                                    <option value="testimonials">{{ trans('testimonials') }}</option>
                                                    <option value="why-us">{{ trans('why-us') }}</option>
                                                    <option value="app-features">{{ trans('app-features') }}</option>
                                                    <option value="gifts-hero">{{ app()->getLocale() === 'ar' ? 'واجهة الورود والهدايا' : 'Gifts hero' }}</option>
                                                    <option value="gifts-collections">{{ app()->getLocale() === 'ar' ? 'تشكيلة الورود والهدايا' : 'Gifts collections' }}</option>
                                                    <option value="gifts-benefits">{{ app()->getLocale() === 'ar' ? 'مزايا الإهداء' : 'Gifts benefits' }}</option>
                                                    <option value="gifts-occasions">{{ app()->getLocale() === 'ar' ? 'المناسبات' : 'Gifts occasions' }}</option>
                                                    <option value="gifts-steps">{{ app()->getLocale() === 'ar' ? 'خطوات الطلب' : 'Gifts ordering steps' }}</option>
                                                    <option value="gifts-faq">{{ app()->getLocale() === 'ar' ? 'الأسئلة الشائعة للهدايا' : 'Gifts FAQ' }}</option>
                                                    <option value="gifts-cta">{{ app()->getLocale() === 'ar' ? 'دعوة طلب الهدايا' : 'Gifts call to action' }}</option>
                                                    <option value="b2b">{{ trans('b2b') }}</option>
                                                    <option value="blogs">{{ trans('blogs') }}</option>
                                                    <option value="contact">{{ trans('contact') }}</option>
                                                </select>

                                            </div>

                                            <div class="col-lg-9 ml-lg-auto">
                                                <button type="submit"
                                                    class="btn btn-primary font-weight-bold mr-2">{{ trans('Submit') }}</button>
                                            </div>

                                            <div class="col-12" id="entities-div"></div>
                                            <div class="col-12 mt-4 testimonial-manager-panel" id="testimonials-manager-panel" style="display:none">
                                                <div class="testimonial-manager-header">
                                                    <div>
                                                        <h3>{{ app()->getLocale() === 'ar' ? 'بطاقات آراء العملاء' : 'Customer review cards' }}</h3>
                                                        <p>{{ app()->getLocale() === 'ar' ? 'عدّل عنوان ووصف القسم أعلاه، وأدِر بطاقات آراء العملاء من هذا الجدول.' : 'Edit the section heading and description above, and manage the customer review cards in this table.' }}</p>
                                                    </div>
                                                    <a href="{{ route('dashboard.testimonials.create') }}" class="btn btn-primary" target="_blank" rel="noopener">
                                                        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة بطاقة' : 'Add a card' }}
                                                    </a>
                                                </div>
                                                <div class="table-responsive testimonial-manager-table-wrap">
                                                    <table class="table align-middle table-hover testimonial-manager-table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                                                                <th>{{ app()->getLocale() === 'ar' ? 'التصنيف' : 'Category' }}</th>
                                                                <th>{{ app()->getLocale() === 'ar' ? 'محتوى البطاقة' : 'Review' }}</th>
                                                                <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                                                <th>{{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($testimonials as $testimonial)
                                                                @php
                                                                    $testimonialAr = $testimonial->translate('ar');
                                                                    $testimonialEn = $testimonial->translate('en');
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>
                                                                        <div class="testimonial-manager-person">
                                                                            @if(filled($testimonial->avatar))
                                                                                <img src="{{ $testimonial->image_url }}" alt="{{ $testimonialAr?->name ?: $testimonialEn?->name }}" loading="lazy">
                                                                            @endif
                                                                            <div>
                                                                                <strong>{{ $testimonialAr?->name ?: $testimonialEn?->name }}</strong>
                                                                                @if($testimonialEn?->name && $testimonialAr?->name)<small>{{ $testimonialEn->name }}</small>@endif
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        {{ $testimonialAr?->title ?: $testimonialAr?->location ?: $testimonialEn?->title ?: $testimonialEn?->location }}
                                                                    </td>
                                                                    <td class="testimonial-manager-review">
                                                                        <span>{{ $testimonialAr?->body ?: $testimonialEn?->body }}</span>
                                                                        @if(filled($testimonialEn?->body))<small>{{ $testimonialEn->body }}</small>@endif
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge {{ $testimonial->is_active ? 'badge-light-success' : 'badge-light-secondary' }}">
                                                                            {{ app()->getLocale() === 'ar' ? ($testimonial->is_active ? 'نشط' : 'متوقف') : ($testimonial->is_active ? 'Active' : 'Inactive') }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('dashboard.testimonials.edit', $testimonial->id) }}" class="btn btn-sm btn-light-primary" target="_blank" rel="noopener">
                                                                            {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                                                                        </a>
                                                                        <button type="button" class="btn btn-sm btn-light-danger testimonial-delete-button" data-url="{{ route('dashboard.testimonials.delete', $testimonial->id) }}">
                                                                            {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-muted py-5">{{ app()->getLocale() === 'ar' ? 'لا توجد بطاقات آراء بعد.' : 'There are no review cards yet.' }}</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
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
    <style>
        .page-editor-card { border: 0; border-radius: 16px; box-shadow: 0 8px 28px rgba(31, 51, 100, .07); }
        .page-editor-form .card-body { padding: clamp(16px, 2.4vw, 30px); row-gap: 8px; }
        .page-editor-form .form-group { margin-bottom: 14px !important; }
        .page-editor-form .nav-tabs { border-bottom-color: #e7ebf2; }
        .page-editor-form .tab-content { padding: 4px 0 0; }
        .page-editor-form textarea.form-control { height: 112px !important; min-height: 88px; resize: vertical; }
        .page-editor-form .editor-container { height: 180px; border: 1px solid #e1e6ef; border-radius: 10px; overflow-y: auto; margin-bottom: 8px; }
        .page-editor-form .editor-container .ql-editor { min-height: 130px; }
        .page-editor-form .card-footer { position: sticky; bottom: 0; z-index: 3; background: rgba(255,255,255,.96); border-top: 1px solid #edf0f5; padding: 12px 24px; }
        .page-editor-form .card-footer .col-lg-9 { width: 100%; text-align: end; }
        .testimonial-manager-panel { border: 1px solid #e7ebf2; border-radius: 14px; background: #f8faff; padding: 18px; }
        .testimonial-manager-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 14px; }
        .testimonial-manager-header h3 { margin: 0 0 4px; color: #1f3364; font-size: 18px; font-weight: 700; }
        .testimonial-manager-header p { margin: 0; color: #727b8c; font-size: 13px; }
        .testimonial-manager-table-wrap { max-height: 360px; overflow: auto; border: 1px solid #e7ebf2; border-radius: 10px; background: #fff; }
        .testimonial-manager-table { min-width: 850px; margin: 0; }
        .testimonial-manager-table thead th { position: sticky; top: 0; z-index: 1; background: #f0f4fa; color: #4c5870; white-space: nowrap; }
        .testimonial-manager-table td { max-width: 210px; color: #4f596d; font-size: 13px; }
        .testimonial-manager-table td small { display: block; margin-top: 4px; color: #8891a1; font-weight: 400; }
        .testimonial-manager-person { display: flex; align-items: center; gap: 10px; min-width: 150px; }
        .testimonial-manager-person img { width: 42px; height: 42px; flex: 0 0 42px; border-radius: 50%; object-fit: cover; border: 1px solid #e7ebf2; background: #f0f4fa; }
        .testimonial-manager-review span, .testimonial-manager-review small { display: -webkit-box; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
        @media (max-width: 767px) { .page-editor-form .card-body { padding: 14px; } .page-editor-form .nav-tabs .nav-link { padding: 8px 12px; } }
        @media (max-width: 767px) { .testimonial-manager-header { align-items: flex-start; flex-direction: column; } }
    </style>
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
            var sectionsData = {!! json_encode($sectionsData) !!};
            $('.page-editor-form .nav-tabs').each(function() {
                var tabs = $(this);
                var arabicButton = tabs.find('[data-bs-target$="-ar"]').first();
                var englishButton = tabs.find('[data-bs-target$="-en"]').first();
                if (!arabicButton.length || !englishButton.length) return;

                arabicButton.closest('li').prependTo(tabs);
                arabicButton.text('العربية').addClass('active').attr('aria-selected', 'true');
                englishButton.text('English').removeClass('active').attr('aria-selected', 'false');

                var arabicPane = $(arabicButton.attr('data-bs-target'));
                var englishPane = $(englishButton.attr('data-bs-target'));
                arabicPane.addClass('show active');
                englishPane.removeClass('show active');
            });

            $(document).on('click', '.testimonial-delete-button', function() {
                var button = $(this);
                var confirmMessage = @json(app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف بطاقة الرأي؟' : 'Delete this review card?');

                if (!window.confirm(confirmMessage)) return;

                button.prop('disabled', true);
                $.ajax({
                    url: button.data('url'),
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() { window.location.reload(); },
                    error: function() {
                        button.prop('disabled', false);
                        window.alert(@json(app()->getLocale() === 'ar' ? 'تعذر حذف بطاقة الرأي. حاول مرة أخرى.' : 'Could not delete this review card. Please try again.'));
                    }
                });
            });

            $('#page_id-template').change(function() {
                var template = $(this).val();
                $('#title-div').hide();
                $('#small_title-div').hide();
                $('#description-div').hide();
                $('#image-div').hide();
                $('#video-div').hide();
                $('#entities-div').empty();
                $('#testimonials-manager-panel').toggle(template === 'testimonials');
                if (!sectionsData[template]) return;
                let has = sectionsData[template].has;
                has.forEach(element => {
                    $('#' + element + '-div').show();
                });
                let entities = sectionsData[template].entity;
                let entitiesHtml = '';
                entities.forEach(element => {
                    if(element.route && element.title_key){
                        entitiesHtml += `<a class="btn btn-sm btn-outline-primary m-2" href="${element.route}" target="_blank" rel="noopener">${element.title_key}</a>`;
                    }
                });
                $('#entities-div').html(entitiesHtml);
            });
        });
    </script>
@endpush
