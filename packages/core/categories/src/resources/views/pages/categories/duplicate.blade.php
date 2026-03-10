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
                        <li class="breadcrumb-item text-muted">@lang($type)</li>
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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.$type.index")}}" data-id="{{ $item->id ?? null }}"
                            action="{{ route('dashboard.'.$type.'.duplicate', $item->id) }}"
                            data-mode="new">

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
                            @if (in_array($type,['services','sub-services']))
                                <div class="col-12 mt-5">
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active " id="intro-en-tab" data-bs-toggle="tab"
                                                data-bs-target="#intro-en" type="button" role="tab"
                                                aria-controls="intro-en"
                                                aria-selected=" true">{{ trans('English') }}</button>
                                        </li>

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link  " id="intro-ar-tab" data-bs-toggle="tab"
                                                data-bs-target="#intro-ar" type="button" role="tab"
                                                aria-controls="intro-ar"
                                                aria-selected=" false">{{ trans('العربية') }}</button>
                                        </li>

                                    </ul>
                                    <div class="tab-content mt-3" id="languageTabsContent">
                                        <div class="tab-pane fade show active" id="intro-en" role="tabpanel"
                                            aria-labelledby="en-tab">

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="intro">{{ trans('intro') }}</label>
                                                <textarea type="number" name="translations[en][intro]" class="form-control" placeholder="{{ trans('Enter intro') }} ">@isset($item){{ $item?->translate('en')?->intro }}@endisset</textarea>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade " id="intro-ar" role="tabpanel" aria-labelledby="ar-tab">

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="intro">{{ trans('intro') }}</label>
                                                <textarea type="number" name="translations[ar][intro]" class="form-control " placeholder="{{ trans('Enter intro') }} ">@isset($item) {{ $item?->translate('ar')?->intro }}@endisset</textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @endif

                            <div class="col-12 mt-5">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="desc-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#desc-en" type="button" role="tab"
                                            aria-controls="desc-en" aria-selected=" true">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  " id="desc-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#desc-ar" type="button" role="tab"
                                            aria-controls="desc-ar"
                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade show active" id="desc-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('description') }}</label>

                                            <div class="editor-container">
                                                <div id="desc-en" name="translations[en][desc]">
                                                    @isset($item)
                                                        {!! $item?->translate('en')?->desc !!}
                                                    @endisset
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="desc-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label>{{ trans('description') }}</label>
                                            <div class="editor-container">
                                                <div id="desc-ar" name="translations[ar][desc]">
                                                    @isset($item)
                                                        {!! $item?->translate('ar')?->desc !!}
                                                    @endisset
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    @foreach ($types as $sType)
                                        <option value="{{ $sType }}" @selected(isset($item) and $item->type == $sType)>
                                            {{ trans($sType) }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="delivery_price">{{ trans('delivery price') }}</label>
                                <input type="number" name="delivery_price" class="form-control "
                                    placeholder="{{ trans('Enter delivery price') }} "
                                    value="{{ old('delivery_price', $item->delivery_price ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="sort">{{ trans('sort') }}</label>
                                <input type="number" name="sort" class="form-control "
                                    placeholder="{{ trans('Enter sort') }} "
                                    value="{{ old('sort', $item->sort ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_package" name="is_package"
                                        @checked(isset($item) and $item->is_package)>
                                    <label class="form-check-label" for="is_package">{{ trans('is package') }}</label>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <label class="" for="status">{{ trans('status') }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status">

                                    <option value="">{{ trans('select status') }}</option>
                                    <option value="active" @selected(isset($item) and $item->status == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>{{ trans('not active') }}
                                    </option>

                                </select>

                            </div>
                            @if (in_array($type,['sub-categories','sub-services']))
                                <div class="form-group mb-3 col-md-12">
                                    @if ($type == 'sub-categories')
                                        <label class="" for="parent_id">{{ trans('Parent Category') }}</label>
                                    @else
                                        <label class="" for="parent_id">{{ trans('Parent service') }}</label>
                                    @endif
                                    <select class="custom-select  form-select advance-select" name="parent_id"
                                        id="parent_id">

                                        <option value="">{{ trans('select parent') }}</option>
                                        @foreach ($parents ?? [] as $sItem)
                                            <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->parent_id == $sItem->id)
                                                value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                            @endif
                            <div class="form-group mb-3 col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="for_all_cities" name="for_all_cities"
                                        @checked(isset($item) && $item->for_all_cities)>
                                    <label class="form-check-label" for="for_all_cities">{{ trans('For All Cities') }}</label>
                                </div>
                            </div>


                            <div class="form-group mb-3 col-md-12" id="cities-select-group">
                                <label class="" for="cities">{{ trans('cities') }}</label>
                                <select class="custom-select  form-select advance-select" name="cities" id="cities" multiple>

                                    <option value="">{{ trans('select cities') }}</option>
                                    @foreach ($cities ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->cities->where('id',$sItem->id)->first())
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            @if (in_array($type,['categories','services']))

                                <div class="form-group mb-3 col-md-12">
                                    <div class="mt-3 items-container" data-items-on       =   "parent_id"
                                        data-items-name     =   "sub_categories" data-items-from     =   "categories">

                                        <h3 class="text-dark">{{ trans('sub '.$type) }}</h3>
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
                                                        <th scope="col" data-name="name" data-type="text">
                                                            {{ trans('name') }}</th>
                                                        <th scope="col" data-name="image" data-type="mediacenter">
                                                            {{ trans('image') }}</th>
                                                        <th scope="col" data-name="sort" data-type="number">
                                                            {{ trans('sort') }}</th>
                                                        <th scope="col" data-name="status" data-type="select">
                                                            {{ trans('status') }}</th>
                                                        <th scope="col" data-name="actions" data-type="actions">
                                                            {{ trans('actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach ($item->subCategories ?? [] as $sItem)
                                                        <tr data-id="{{ $sItem->id }}"
                                                            data-data="{{ json_encode($sItem->itemData) }}">

                                                            <td>{{ $sItem?->translate('en')?->name }}</td>
                                                            <td>{{ $sItem?->translate('ar')?->name }}</td>
                                                            <td>{{ $sItem->name }}</td>
                                                            <td>{!! \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($sItem->image) !!}</td>
                                                            <td>{{ $sItem->sort }}</td>
                                                            <td>{{ $sItem->status }}</td>
                                                            <td class="options">{!! $sItem->itemsActions !!}</td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            @endif
                            @if (in_array($type,['services','sub-services']))
                            <div class="form-group mb-3 col-md-12">
                                <div class="mt-3 items-container" data-items-on       =   "category_id"
                                    data-items-name     =   "category_types" data-items-from     =   "category-types">

                                    <h3 class="text-dark">{{ trans('sub-services Types') }}</h3>
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
                                                    <th scope="col" data-name="name" data-type="text">
                                                        {{ trans('name') }}</th>
                                                    <th scope="col" data-name="hour_price" data-type="number">
                                                        {{ trans('hour price') }}</th>
                                                    <th scope="col" data-name="status" data-type="select">
                                                        {{ trans('status') }}</th>
                                                    <th scope="col" data-name="actions" data-type="actions">
                                                        {{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($item->categoryTypes ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->translate('en')?->name }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->name }}</td>
                                                        <td>{{ $sItem->name }}</td>
                                                        <td>{{ $sItem->hour_price }}</td>
                                                        <td>{{ $sItem->status }}</td>
                                                        <td class="options">{!! $sItem->itemsActions !!}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            @endif
                            @if (in_array($type,['services','sub-services']))
                            <div class="form-group mb-3 col-md-12">
                                <div class="mt-3 items-container"
                                    data-items-on       =   "category_id"
                                    data-items-name     =   "category_settings"
                                    data-items-from     =   "category-settings">

                                    <h3 class="text-dark">{{ trans('sub-services settings') }}</h3>
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
                                                    <th scope="col" data-name="name" data-type="text">
                                                        {{ trans('name') }}</th>
                                                    <th scope="col" data-name="addon_price" data-type="number">
                                                        {{ trans('addon price') }}</th>
                                                    <th scope="col" data-name="status" data-type="select">
                                                        {{ trans('status') }}</th>
                                                    <th scope="col" data-name="actions" data-type="actions">
                                                        {{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($item?->categorySettings?->where('parent_id',null) ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->translate('en')?->name }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->name }}</td>
                                                        <td>{{ $sItem->name }}</td>
                                                        <td>{{ $sItem->addon_price }}</td>
                                                        <td>{{ $sItem->status }}</td>
                                                        <td class="options">{!! $sItem->itemsActions !!}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                            @endif
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

                    <div class="modal fade" id="categoriesModal" aria-hidden="true"
                        aria-labelledby="categoriesModalLabel" data-store="{{ route('dashboard.categories.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="categoriesModalLabel">{{ trans('sub '.$type) }}</h1>
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
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-name-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[ar][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="image">{{ trans('image') }}</label>
                                                <div class="media-center-group form-control" data-max=""
                                                    data-type="">
                                                    <input type="text" hidden="hidden" class="form-control"
                                                        name="image" value="">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i
                                                            class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>
                                            @if (in_array($type,['services','sub-services']))
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

                                            @endif

                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-desc-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-desc-en"
                                                            type="button" role="tab" aria-controls="items-desc-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-desc-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-desc-ar"
                                                            type="button" role="tab" aria-controls="items-desc-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-desc-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <div class="editor-container">
                                                                <div id="categories-desc-en"
                                                                    name="translations[en][desc]"></div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-desc-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <div class="editor-container">
                                                                <div id="categories-desc-ar"
                                                                    name="translations[ar][desc]"></div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="type">{{ trans('type') }}</label>
                                                <select class="custom-select  form-select advance-select" name="type"
                                                    id="parent_id-type">
                                                    <option value="">{{ trans('select type') }}</option>
                                                    @foreach ($types as $sType)
                                                        <option value="{{ $sType }}" @selected(isset($item) and $item->type == $sType)>
                                                            {{ trans($sType) }}</option>

                                                    @endforeach


                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class=""
                                                    for="delivery_price">{{ trans('delivery price') }}</label>
                                                <input type="number" name="delivery_price" class="form-control "
                                                    placeholder="{{ trans('Enter delivery price') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="sort">{{ trans('sort') }}</label>
                                                <input type="number" name="sort" class="form-control "
                                                    placeholder="{{ trans('Enter sort') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_package"
                                                        name="is_package">
                                                    <label class="form-check-label"
                                                        for="is_package">{{ trans('is package') }}</label>
                                                </div>

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="status">{{ trans('status') }}</label>
                                                <select class="custom-select  form-select advance-select" name="status"
                                                    id="parent_id-status">

                                                    <option value="">{{ trans('select status') }}</option>
                                                    <option value="active" @selected(isset($item) and $item->status == 'active')>
                                                        {{ trans('active') }}</option>
                                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>
                                                        {{ trans('not active') }}</option>

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="cities">{{ trans('cities') }}</label>
                                                <select class="custom-select  form-select advance-select" name="cities"
                                                    id="parent_id-cities" multiple>

                                                    <option value="">{{ trans('select cities') }}</option>
                                                    @foreach ($cities ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}"
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

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
                    <div class="modal fade" id="categoriesDeleteModel" tabindex="-1"
                        aria-labelledby="categoriesDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="categoriesDeleteModelLabel">
                                        {{ trans('Delete Category') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the Category') }} <span></span>?
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


                    <div class="modal fade" id="category-typesModal" aria-hidden="true"
                        aria-labelledby="category-typesModalLabel"
                        data-store="{{ route('dashboard.category-types.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="category-typesModalLabel">
                                        {{ trans($type.' Type') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">



                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-categorytype-name-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-categorytype-name-en"
                                                            type="button" role="tab" aria-controls="items-categorytype-name-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-categorytype-name-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-categorytype-name-ar"
                                                            type="button" role="tab" aria-controls="items-categorytype-name-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-categorytype-name-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-categorytype-name-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[ar][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            @if (in_array($type,['services','sub-services']))
                                                <div class="col-12 mt-5">
                                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active " id="items-categorytype-intro-en-tab"
                                                                data-bs-toggle="tab" data-bs-target="#items-categorytype-intro-en"
                                                                type="button" role="tab" aria-controls="items-categorytype-intro-en"
                                                                aria-selected=" true">{{ trans('English') }}</button>
                                                        </li>

                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link  " id="items-categorytype-intro-ar-tab"
                                                                data-bs-toggle="tab" data-bs-target="#items-categorytype-intro-ar"
                                                                type="button" role="tab" aria-controls="items-categorytype-intro-ar"
                                                                aria-selected=" false">{{ trans('العربية') }}</button>
                                                        </li>

                                                    </ul>
                                                    <div class="tab-content mt-3" id="languageTabsContent">
                                                        <div class="tab-pane fade show active" id="items-categorytype-intro-en"
                                                            role="tabpanel" aria-labelledby="en-tab">

                                                            <div class="form-group mb-3 col-md-12">
                                                                <label class=""
                                                                    for="intro">{{ trans('intro') }}</label>
                                                                <textarea type="number" name="translations[en][intro]" class="form-control "
                                                                    placeholder="{{ trans('Enter intro') }} "></textarea>

                                                            </div>

                                                        </div>
                                                        <div class="tab-pane fade " id="items-categorytype-intro-ar" role="tabpanel"
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

                                            @endif
                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-categorytype-desc-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-categorytype-desc-en"
                                                            type="button" role="tab" aria-controls="items-categorytype-desc-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-categorytype-desc-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-categorytype-desc-ar"
                                                            type="button" role="tab" aria-controls="items-categorytype-desc-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-categorytype-desc-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="desc">{{ trans('desc') }}</label>
                                                            <textarea type="number" name="translations[en][desc]" class="form-control "
                                                                placeholder="{{ trans('Enter desc') }} "></textarea>

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-categorytype-desc-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="desc">{{ trans('desc') }}</label>
                                                            <textarea type="number" name="translations[ar][desc]" class="form-control "
                                                                placeholder="{{ trans('Enter desc') }} "></textarea>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required"
                                                    for="hour_price">{{ trans('hour price') }}</label>
                                                <input type="number" name="hour_price" class="form-control "
                                                    placeholder="{{ trans('Enter hour price') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="status">{{ trans('status') }}</label>
                                                <select class="custom-select  form-select advance-select" name="status"
                                                    id="category_id-status">

                                                    <option value="">{{ trans('select status') }}</option>
                                                    <option value="active" @selected(isset($item) and $item->status == 'active')>
                                                        {{ trans('active') }}</option>
                                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>
                                                        {{ trans('not-active') }}</option>

                                                </select>

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
                    <div class="modal fade" id="category-typesDeleteModel" tabindex="-1"
                        aria-labelledby="category-typesDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="category-typesDeleteModelLabel">
                                        {{ trans('Delete CategoryType') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the CategoryType') }} <span></span>?
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


                    <div class="modal fade" id="category-settingsModal" aria-hidden="true"
                        aria-labelledby="category-settingsModalLabel"
                        data-store="{{ route('dashboard.category-settings.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="category-settingsModalLabel">
                                        {{ trans($type.' Settings') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">
                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-category-settings-name-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-category-settings-name-en"
                                                            type="button" role="tab" aria-controls="items-category-settings-name-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-category-settings-name-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-category-settings-name-ar"
                                                            type="button" role="tab" aria-controls="items-category-settings-name-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="languageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-category-settings-name-en"
                                                        role="tabpanel" aria-labelledby="en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="items-category-settings-name-ar" role="tabpanel"
                                                        aria-labelledby="ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[ar][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group mb-3 col-md-6">
                                                <label class=""
                                                    for="addon_price">{{ trans('addon price') }}</label>
                                                <input type="number" name="addon_price" class="form-control "
                                                    placeholder="{{ trans('Enter addon price') }} " value="">

                                            </div>
                                            <div class="form-group mb-3 col-md-6">
                                                <label class=""
                                                    for="cost">{{ trans('cost') }}</label>
                                                <input type="number" name="cost" class="form-control "
                                                    placeholder="{{ trans('Enter cost') }} " value="">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="status">{{ trans('status') }}</label>
                                                <select class="custom-select  form-select advance-select" name="status"
                                                    id="parent_id-status">

                                                    <option value="">{{ trans('select status') }}</option>
                                                    <option value="active" @selected(isset($item) and $item->status == 'active')>
                                                        {{ trans('active') }}</option>
                                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>
                                                        {{ trans('not-active') }}</option>

                                                </select>

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
                    <div class="modal fade" id="category-settingsDeleteModel" tabindex="-1"
                        aria-labelledby="category-settingsDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="category-settingsDeleteModelLabel">
                                        {{ trans('Delete CategorySetting') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the CategorySetting') }} <span></span>?
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
        document.addEventListener('DOMContentLoaded', function () {
            const forAllCitiesSwitch = document.getElementById('for_all_cities');
            const citiesSelectGroup = document.getElementById('cities-select-group');
            function toggleCitiesSelectGroup() {
            if (forAllCitiesSwitch.checked) {
                citiesSelectGroup.style.display = 'none';
            } else {
                citiesSelectGroup.style.display = '';
            }
            }
            if (forAllCitiesSwitch && citiesSelectGroup) {
            forAllCitiesSwitch.addEventListener('change', toggleCitiesSelectGroup);
            // Trigger on page load
            toggleCitiesSelectGroup();
            }
        });
    </script>
@endpush
