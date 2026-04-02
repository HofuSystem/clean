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
                        @if (isset($item)) action="{{ route('dashboard.'.$type.'.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.'.$type.'.create') }}"
                            data-mode="new" @endif>

                        @csrf
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6" id="categoryTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab"
                                        data-bs-target="#general" type="button" role="tab" aria-controls="general"
                                        aria-selected="true">{{ trans('General Information') }}</button>
                                </li>
                                @if ($type == 'categories')
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="app-features-tab" data-bs-toggle="tab"
                                            data-bs-target="#app-features" type="button" role="tab"
                                            aria-controls="app-features"
                                            aria-selected="false">{{ trans('App Features') }}</button>
                                    </li>
                                @endif
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo"
                                        type="button" role="tab" aria-controls="seo"
                                        aria-selected="false">{{ trans('SEO') }}</button>
                                </li>
                                @if (in_array($type, ['categories', 'services', 'sub-services']))
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="items-tab" data-bs-toggle="tab"
                                            data-bs-target="#items" type="button" role="tab"
                                            aria-controls="items" aria-selected="false">{{ trans('Items') }}</button>
                                    </li>
                                @endif
                            </ul>

                            <div class="tab-content" id="categoryTabContent">
                                <!-- General Tab -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel"
                                    aria-labelledby="general-tab">
                                    <div class="row">
                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="delivery_price">{{ trans('slug') }}</label>
                                            <input type="text" name="slug" class="form-control "
                                                placeholder="{{ trans('Enter slug') }} "
                                                value="{{ old('slug', $item->slug ?? null) }}">
                                        </div>

                                        <div class="col-12 mt-5">
                                            <h5>{{ trans('Name') }}</h5>
                                            <ul class="nav nav-tabs" id="nameLanguageTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="name-en-tab"
                                                        data-bs-toggle="tab" data-bs-target="#pane-name-en" type="button"
                                                        role="tab" aria-controls="pane-name-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="name-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#pane-name-ar" type="button" role="tab"
                                                        aria-controls="pane-name-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content mt-3" id="nameLanguageTabsContent">
                                                <div class="tab-pane fade show active" id="pane-name-en" role="tabpanel"
                                                    aria-labelledby="name-en-tab">
                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class="required"
                                                            for="name">{{ trans('name') }}</label>
                                                        <input type="text" name="translations[en][name]"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter name') }} "
                                                            value="@isset($item) {{ $item?->translate('en')?->name }} @endisset">
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade " id="pane-name-ar" role="tabpanel"
                                                    aria-labelledby="name-ar-tab">
                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class="required"
                                                            for="name">{{ trans('name') }}</label>
                                                        <input type="text" name="translations[ar][name]"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter name') }} "
                                                            value="@isset($item) {{ $item?->translate('ar')?->name }} @endisset">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (in_array($type, ['services', 'sub-services']))
                                            <div class="col-12 mt-5">
                                                <h5>{{ trans('Intro') }}</h5>
                                                <ul class="nav nav-tabs" id="introLanguageTabs" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="intro-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#pane-intro-en"
                                                            type="button" role="tab" aria-controls="pane-intro-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="intro-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#pane-intro-ar"
                                                            type="button" role="tab" aria-controls="pane-intro-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content mt-3" id="introLanguageTabsContent">
                                                    <div class="tab-pane fade show active" id="pane-intro-en"
                                                        role="tabpanel" aria-labelledby="intro-en-tab">
                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="intro">{{ trans('intro') }}</label>
                                                            <textarea name="translations[en][intro]" class="form-control" placeholder="{{ trans('Enter intro') }} ">@isset($item){{ $item?->translate('en')?->intro }}@endisset</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade " id="pane-intro-ar" role="tabpanel"
                                                        aria-labelledby="intro-ar-tab">
                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="intro">{{ trans('intro') }}</label>
                                                            <textarea name="translations[ar][intro]" class="form-control " placeholder="{{ trans('Enter intro') }} ">@isset($item) {{ $item?->translate('ar')?->intro }}@endisset</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-12 mt-5">
                                            <h5>{{ trans('Description') }}</h5>
                                            <ul class="nav nav-tabs" id="descLanguageTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="desc-en-tab"
                                                        data-bs-toggle="tab" data-bs-target="#pane-desc-en" type="button"
                                                        role="tab" aria-controls="pane-desc-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="desc-ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#pane-desc-ar" type="button" role="tab"
                                                        aria-controls="pane-desc-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content mt-3" id="descLanguageTabsContent">
                                                <div class="tab-pane fade show active" id="pane-desc-en" role="tabpanel"
                                                    aria-labelledby="desc-en-tab">
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
                                                <div class="tab-pane fade " id="pane-desc-ar" role="tabpanel"
                                                    aria-labelledby="desc-ar-tab">
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
                                            <label class="required" for="type">{{ trans('type') }}</label>
                                            <select class="custom-select  form-select advance-select" name="type"
                                                id="type">
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
                                                <input class="form-check-input" type="checkbox" id="is_package"
                                                    name="is_package" @checked(isset($item) and $item->is_package)>
                                                <label class="form-check-label"
                                                    for="is_package">{{ trans('is package') }}</label>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="status">{{ trans('status') }}</label>
                                            <select class="custom-select  form-select advance-select" name="status"
                                                id="status">
                                                <option value="">{{ trans('select status') }}</option>
                                                <option value="active" @selected(isset($item) and $item->status == 'active')>
                                                    {{ trans('active') }}</option>
                                                <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>
                                                    {{ trans('not active') }}
                                                </option>
                                            </select>
                                        </div>

                                        @if (in_array($type, ['sub-categories', 'sub-services']))
                                            <div class="form-group mb-3 col-md-12">
                                                @if ($type == 'sub-categories')
                                                    <label class=""
                                                        for="parent_id">{{ trans('Parent Category') }}</label>
                                                @else
                                                    <label class=""
                                                        for="parent_id">{{ trans('Parent service') }}</label>
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
                                                <input class="form-check-input" type="checkbox" id="for_all_cities"
                                                    name="for_all_cities" @checked(isset($item) && $item->for_all_cities)>
                                                <label class="form-check-label"
                                                    for="for_all_cities">{{ trans('For All Cities') }}</label>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-md-12" id="cities-select-group">
                                            <label class="" for="cities">{{ trans('cities') }}</label>
                                            <select class="custom-select  form-select advance-select" name="cities"
                                                id="cities" multiple>
                                                <option value="">{{ trans('select cities') }}</option>
                                                @foreach ($cities ?? [] as $sItem)
                                                    <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->cities->where('id', $sItem->id)->first())
                                                        value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <!-- SEO Tab -->
                                <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                    <div class="row">
                                        <div class="col-12 mt-5">
                                            <h5>{{ trans('Meta Title') }}</h5>
                                            <ul class="nav nav-tabs" id="metaTitleLanguageTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="meta_title-en-tab"
                                                        data-bs-toggle="tab" data-bs-target="#pane-meta_title-en"
                                                        type="button" role="tab" aria-controls="pane-meta_title-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="meta_title-ar-tab"
                                                        data-bs-toggle="tab" data-bs-target="#pane-meta_title-ar"
                                                        type="button" role="tab" aria-controls="pane-meta_title-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content mt-3" id="metaTitleLanguageTabsContent">
                                                <div class="tab-pane fade show active" id="pane-meta_title-en"
                                                    role="tabpanel" aria-labelledby="meta_title-en-tab">
                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class=""
                                                            for="meta_title">{{ trans('meta_title') }}</label>
                                                        <input type="text" name="translations[en][meta_title]"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter meta_title') }} "
                                                            value="@isset($item){{ $item?->translate('en')?->meta_title }}@endisset" />
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade " id="pane-meta_title-ar" role="tabpanel"
                                                    aria-labelledby="meta_title-ar-tab">
                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class=""
                                                            for="meta_title">{{ trans('meta_title') }}</label>
                                                        <input type="text" name="translations[ar][meta_title]"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter meta_title') }} "
                                                            value="@isset($item){{ $item?->translate('ar')?->meta_title }}@endisset" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-5">
                                            <h5>{{ trans('Meta Description') }}</h5>
                                            <ul class="nav nav-tabs" id="metaDescLanguageTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active " id="meta_description-en-tab"
                                                        data-bs-toggle="tab" data-bs-target="#pane-meta_description-en"
                                                        type="button" role="tab"
                                                        aria-controls="pane-meta_description-en"
                                                        aria-selected=" true">{{ trans('English') }}</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link  " id="meta_description-ar-tab"
                                                        data-bs-toggle="tab" data-bs-target="#pane-meta_description-ar"
                                                        type="button" role="tab"
                                                        aria-controls="pane-meta_description-ar"
                                                        aria-selected=" false">{{ trans('العربية') }}</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content mt-3" id="metaDescLanguageTabsContent">
                                                <div class="tab-pane fade show active" id="pane-meta_description-en"
                                                    role="tabpanel" aria-labelledby="meta_description-en-tab">
                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class=""
                                                            for="meta_description">{{ trans('meta_description') }}</label>
                                                        <input type="text" name="translations[en][meta_description]"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter meta_description') }} "
                                                            value="@isset($item){{ $item?->translate('en')?->meta_description }}@endisset" />
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade " id="pane-meta_description-ar" role="tabpanel"
                                                    aria-labelledby="meta_description-ar-tab">
                                                    <div class="form-group mb-3 col-md-12">
                                                        <label class=""
                                                            for="meta_description">{{ trans('meta_description') }}</label>
                                                        <input type="text" name="translations[ar][meta_description]"
                                                            class="form-control "
                                                            placeholder="{{ trans('Enter meta_description') }} "
                                                            value="@isset($item){{ $item?->translate('ar')?->meta_description }}@endisset" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($type == 'categories')
                                    <!-- App Features Tab -->
                                    <div class="tab-pane fade" id="app-features" role="tabpanel"
                                        aria-labelledby="app-features-tab">
                                        <div class="row">
                                            <div class="form-group mb-3 col-md-12">
                                                <div class="mt-3 items-container" data-items-on="category_id"
                                                    data-items-name="app_features"
                                                    data-items-from="category-app-features">
                                                    <h3 class="text-dark">{{ trans('App Features') }}</h3>
                                                    <button type="button" class="btn-operation create-new-items"><i
                                                            class="fas fa-plus"></i></button>
                                                    <hr>
                                                    <div class="table-responsive ">
                                                        <table class="table table-striped table-hover text-center">
                                                            <thead class="table-primary text-white text-capitalize h6">
                                                                <tr>
                                                                    <th scope="col" data-name="section"
                                                                        data-type="select">
                                                                        {{ trans('Section') }}</th>
                                                                    <th scope="col" data-name="translations.en.title"
                                                                        data-type="text">
                                                                        {{ trans('Title (en)') }}</th>
                                                                    <th scope="col" data-name="translations.ar.title"
                                                                        data-type="text">
                                                                        {{ trans('Title (ar)') }}</th>
                                                                    <th scope="col" data-name="image"
                                                                        data-type="mediacenter">
                                                                        {{ trans('Image') }}</th>
                                                                    <th scope="col" data-name="value"
                                                                        data-type="text">
                                                                        {{ trans('Value') }}</th>
                                                                    <th scope="col" data-name="actions"
                                                                        data-type="actions">
                                                                        {{ trans('Actions') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($item->appFeatures ?? [] as $sItem)
                                                                    <tr data-id="{{ $sItem->id }}"
                                                                        data-data="{{ json_encode($sItem->itemData) }}">
                                                                        <td>{{ trans($sItem->section) }}</td>
                                                                        <td>{{ $sItem?->translate('en')?->title }}</td>
                                                                        <td>{{ $sItem?->translate('ar')?->title }}</td>
                                                                        <td>{!! \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($sItem->image) !!}</td>
                                                                        <td>{{ $sItem->value }}</td>
                                                                        <td class="options">
                                                                            {!! $sItem->itemsActions !!}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Items Tab -->
                                @if (in_array($type, ['categories', 'services', 'sub-services']))
                                    <div class="tab-pane fade" id="items" role="tabpanel"
                                        aria-labelledby="items-tab">
                                        <div class="row">
                                            @if (in_array($type, ['categories', 'services']))
                                                <div class="form-group mb-3 col-md-12">
                                                    <div class="mt-3 items-container" data-items-on="parent_id"
                                                        data-items-name="sub_categories"
                                                        data-items-from="categories">
                                                        <h3 class="text-dark">{{ trans('sub ' . $type) }}</h3>
                                                        <button type="button" class="btn-operation create-new-items"><i
                                                                class="fas fa-plus"></i></button>
                                                        <hr>
                                                        <div class="table-responsive ">
                                                            <table
                                                                class="table table-striped table-hover text-center">
                                                                <thead
                                                                    class="table-primary text-white text-capitalize h6">
                                                                    <tr>
                                                                        <th scope="col"
                                                                            data-name="translations.en.name"
                                                                            data-type="text">
                                                                            {{ trans('name ( en )') }}</th>
                                                                        <th scope="col"
                                                                            data-name="translations.ar.name"
                                                                            data-type="text">
                                                                            {{ trans('name ( ar )') }}</th>
                                                                        <th scope="col" data-name="name"
                                                                            data-type="text">
                                                                            {{ trans('name') }}</th>
                                                                        <th scope="col" data-name="image"
                                                                            data-type="mediacenter">
                                                                            {{ trans('image') }}</th>
                                                                        <th scope="col" data-name="sort"
                                                                            data-type="number">
                                                                            {{ trans('sort') }}</th>
                                                                        <th scope="col" data-name="status"
                                                                            data-type="select">
                                                                            {{ trans('status') }}</th>
                                                                        <th scope="col" data-name="actions"
                                                                            data-type="actions">
                                                                            {{ trans('actions') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($item->subCategories ?? [] as $sItem)
                                                                        <tr data-id="{{ $sItem->id }}"
                                                                            data-data="{{ json_encode($sItem->itemData) }}">
                                                                            <td>{{ $sItem?->translate('en')?->name }}
                                                                            </td>
                                                                            <td>{{ $sItem?->translate('ar')?->name }}
                                                                            </td>
                                                                            <td>{{ $sItem->name }}</td>
                                                                            <td>{!! \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesHtml($sItem->image) !!}</td>
                                                                            <td>{{ $sItem->sort }}</td>
                                                                            <td>{{ $sItem->status }}</td>
                                                                            <td class="options">
                                                                                {!! $sItem->itemsActions !!}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if (in_array($type, ['services', 'sub-services']))
                                                <div class="form-group mb-3 col-md-12">
                                                    <div class="mt-3 items-container" data-items-on="category_id"
                                                        data-items-name="category_types"
                                                        data-items-from="category-types">
                                                        <h3 class="text-dark">{{ trans('sub-services Types') }}
                                                        </h3>
                                                        <button type="button" class="btn-operation create-new-items"><i
                                                                class="fas fa-plus"></i></button>
                                                        <hr>
                                                        <div class="table-responsive ">
                                                            <table
                                                                class="table table-striped table-hover text-center">
                                                                <thead
                                                                    class="table-primary text-white text-capitalize h6">
                                                                    <tr>
                                                                        <th scope="col"
                                                                            data-name="translations.en.name"
                                                                            data-type="text">
                                                                            {{ trans('name ( en )') }}</th>
                                                                        <th scope="col"
                                                                            data-name="translations.ar.name"
                                                                            data-type="text">
                                                                            {{ trans('name ( ar )') }}</th>
                                                                        <th scope="col" data-name="name"
                                                                            data-type="text">
                                                                            {{ trans('name') }}</th>
                                                                        <th scope="col" data-name="hour_price"
                                                                            data-type="number">
                                                                            {{ trans('hour price') }}</th>
                                                                        <th scope="col" data-name="status"
                                                                            data-type="select">
                                                                            {{ trans('status') }}</th>
                                                                        <th scope="col" data-name="actions"
                                                                            data-type="actions">
                                                                            {{ trans('actions') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($item->categoryTypes ?? [] as $sItem)
                                                                        <tr data-id="{{ $sItem->id }}"
                                                                            data-data="{{ json_encode($sItem->itemData) }}">
                                                                            <td>{{ $sItem?->translate('en')?->name }}
                                                                            </td>
                                                                            <td>{{ $sItem?->translate('ar')?->name }}
                                                                            </td>
                                                                            <td>{{ $sItem->name }}</td>
                                                                            <td>{{ $sItem->hour_price }}</td>
                                                                            <td>{{ $sItem->status }}</td>
                                                                            <td class="options">
                                                                                {!! $sItem->itemsActions !!}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if (in_array($type, ['services', 'sub-services']))
                                                <div class="form-group mb-3 col-md-12">
                                                    <div class="mt-3 items-container" data-items-on="category_id"
                                                        data-items-name="category_settings"
                                                        data-items-from="category-settings">
                                                        <h3 class="text-dark">{{ trans('sub-services settings') }}
                                                        </h3>
                                                        <button type="button" class="btn-operation create-new-items"><i
                                                                class="fas fa-plus"></i></button>
                                                        <hr>
                                                        <div class="table-responsive ">
                                                            <table
                                                                class="table table-striped table-hover text-center">
                                                                <thead
                                                                    class="table-primary text-white text-capitalize h6">
                                                                    <tr>
                                                                        <th scope="col"
                                                                            data-name="translations.en.name"
                                                                            data-type="text">
                                                                            {{ trans('name ( en )') }}</th>
                                                                        <th scope="col"
                                                                            data-name="translations.ar.name"
                                                                            data-type="text">
                                                                            {{ trans('name ( ar )') }}</th>
                                                                        <th scope="col" data-name="name"
                                                                            data-type="text">
                                                                            {{ trans('name') }}</th>
                                                                        <th scope="col" data-name="addon_price"
                                                                            data-type="number">
                                                                            {{ trans('addon price') }}</th>
                                                                        <th scope="col" data-name="status"
                                                                            data-type="select">
                                                                            {{ trans('status') }}</th>
                                                                        <th scope="col" data-name="actions"
                                                                            data-type="actions">
                                                                            {{ trans('actions') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($item?->categorySettings?->where('parent_id', null) ?? [] as $sItem)
                                                                        <tr data-id="{{ $sItem->id }}"
                                                                            data-data="{{ json_encode($sItem->itemData) }}">
                                                                            <td>{{ $sItem?->translate('en')?->name }}
                                                                            </td>
                                                                            <td>{{ $sItem?->translate('ar')?->name }}
                                                                            </td>
                                                                            <td>{{ $sItem->name }}</td>
                                                                            <td>{{ $sItem->addon_price }}</td>
                                                                            <td>{{ $sItem->status }}</td>
                                                                            <td class="options">
                                                                                {!! $sItem->itemsActions !!}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
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
                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="slug">{{ trans('slug') }}</label>
                                                <input type="text" name="slug" class="form-control "
                                                    placeholder="{{ trans('Enter slug') }} "
                                                    value="">
                
                                            </div>
                

                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="modal-nameLanguageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-name-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-items-name-en"
                                                            type="button" role="tab" aria-controls="modal-pane-items-name-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-name-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-items-name-ar"
                                                            type="button" role="tab" aria-controls="modal-pane-items-name-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="modal-nameLanguageTabsContent">
                                                    <div class="tab-pane fade show active" id="modal-pane-items-name-en"
                                                        role="tabpanel" aria-labelledby="items-name-en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="modal-pane-items-name-ar" role="tabpanel"
                                                        aria-labelledby="items-name-ar-tab">

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
                                            @if (in_array($type,['services','sub-services']))
                                                <div class="col-12 mt-5">
                                                    <ul class="nav nav-tabs" id="modal-introLanguageTabs" role="tablist">

                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active " id="items-intro-en-tab"
                                                                data-bs-toggle="tab" data-bs-target="#modal-pane-items-intro-en"
                                                                type="button" role="tab" aria-controls="modal-pane-items-intro-en"
                                                                aria-selected=" true">{{ trans('English') }}</button>
                                                        </li>

                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link  " id="items-intro-ar-tab"
                                                                data-bs-toggle="tab" data-bs-target="#modal-pane-items-intro-ar"
                                                                type="button" role="tab" aria-controls="modal-pane-items-intro-ar"
                                                                aria-selected=" false">{{ trans('العربية') }}</button>
                                                        </li>

                                                    </ul>
                                                    <div class="tab-content mt-3" id="modal-introLanguageTabsContent">
                                                        <div class="tab-pane fade show active" id="modal-pane-items-intro-en"
                                                            role="tabpanel" aria-labelledby="items-intro-en-tab">

                                                            <div class="form-group mb-3 col-md-12">
                                                                <label class=""
                                                                    for="intro">{{ trans('intro') }}</label>
                                                                <textarea type="number" name="translations[en][intro]" class="form-control "
                                                                    placeholder="{{ trans('Enter intro') }} "></textarea>

                                                            </div>

                                                        </div>
                                                        <div class="tab-pane fade " id="modal-pane-items-intro-ar" role="tabpanel"
                                                            aria-labelledby="items-intro-ar-tab">

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
                                                <ul class="nav nav-tabs" id="modal-descLanguageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-desc-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-items-desc-en"
                                                            type="button" role="tab" aria-controls="modal-pane-items-desc-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-desc-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-items-desc-ar"
                                                            type="button" role="tab" aria-controls="modal-pane-items-desc-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="modal-descLanguageTabsContent">
                                                    <div class="tab-pane fade show active" id="modal-pane-items-desc-en"
                                                        role="tabpanel" aria-labelledby="items-desc-en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <div class="editor-container">
                                                                <div id="categories-desc-en"
                                                                    name="translations[en][desc]"></div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="modal-pane-items-desc-ar" role="tabpanel"
                                                        aria-labelledby="items-desc-ar-tab">

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
                                                <ul class="nav nav-tabs" id="modal-types-nameLanguageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-categorytype-name-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-categorytype-name-en"
                                                            type="button" role="tab" aria-controls="modal-pane-categorytype-name-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-categorytype-name-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-categorytype-name-ar"
                                                            type="button" role="tab" aria-controls="modal-pane-categorytype-name-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="modal-types-nameLanguageTabsContent">
                                                    <div class="tab-pane fade show active" id="modal-pane-categorytype-name-en"
                                                        role="tabpanel" aria-labelledby="items-categorytype-name-en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="modal-pane-categorytype-name-ar" role="tabpanel"
                                                        aria-labelledby="items-categorytype-name-ar-tab">

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
                                                    <ul class="nav nav-tabs" id="modal-types-introLanguageTabs" role="tablist">

                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active " id="items-categorytype-intro-en-tab"
                                                                data-bs-toggle="tab" data-bs-target="#modal-pane-categorytype-intro-en"
                                                                type="button" role="tab" aria-controls="modal-pane-categorytype-intro-en"
                                                                aria-selected=" true">{{ trans('English') }}</button>
                                                        </li>

                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link  " id="items-categorytype-intro-ar-tab"
                                                                data-bs-toggle="tab" data-bs-target="#modal-pane-categorytype-intro-ar"
                                                                type="button" role="tab" aria-controls="modal-pane-categorytype-intro-ar"
                                                                aria-selected=" false">{{ trans('العربية') }}</button>
                                                        </li>

                                                    </ul>
                                                    <div class="tab-content mt-3" id="modal-types-introLanguageTabsContent">
                                                        <div class="tab-pane fade show active" id="modal-pane-categorytype-intro-en"
                                                            role="tabpanel" aria-labelledby="items-categorytype-intro-en-tab">

                                                            <div class="form-group mb-3 col-md-12">
                                                                <label class=""
                                                                    for="intro">{{ trans('intro') }}</label>
                                                                <textarea type="number" name="translations[en][intro]" class="form-control "
                                                                    placeholder="{{ trans('Enter intro') }} "></textarea>

                                                            </div>

                                                        </div>
                                                        <div class="tab-pane fade " id="modal-pane-categorytype-intro-ar" role="tabpanel"
                                                            aria-labelledby="items-categorytype-intro-ar-tab">

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
                                                <ul class="nav nav-tabs" id="modal-types-descLanguageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-categorytype-desc-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-categorytype-desc-en"
                                                            type="button" role="tab" aria-controls="modal-pane-categorytype-desc-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-categorytype-desc-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-categorytype-desc-ar"
                                                            type="button" role="tab" aria-controls="modal-pane-categorytype-desc-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="modal-types-descLanguageTabsContent">
                                                    <div class="tab-pane fade show active" id="modal-pane-categorytype-desc-en"
                                                        role="tabpanel" aria-labelledby="items-categorytype-desc-en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class=""
                                                                for="desc">{{ trans('desc') }}</label>
                                                            <textarea type="number" name="translations[en][desc]" class="form-control "
                                                                placeholder="{{ trans('Enter desc') }} "></textarea>

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="modal-pane-categorytype-desc-ar" role="tabpanel"
                                                        aria-labelledby="items-categorytype-desc-ar-tab">

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
                                                <ul class="nav nav-tabs" id="settings-nameLanguageTabs" role="tablist">

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-category-settings-name-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-settings-name-en"
                                                            type="button" role="tab" aria-controls="modal-pane-settings-name-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-category-settings-name-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#modal-pane-settings-name-ar"
                                                            type="button" role="tab" aria-controls="modal-pane-settings-name-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>

                                                </ul>
                                                <div class="tab-content mt-3" id="settings-nameLanguageTabsContent">
                                                    <div class="tab-pane fade show active" id="modal-pane-settings-name-en"
                                                        role="tabpanel" aria-labelledby="items-category-settings-name-en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade " id="modal-pane-settings-name-ar" role="tabpanel"
                                                        aria-labelledby="items-category-settings-name-ar-tab">

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
                    <div class="modal fade" id="category-app-featuresModal" aria-hidden="true"
                        aria-labelledby="category-app-featuresModalLabel"
                        data-store="{{ route('dashboard.category-app-features.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="category-app-featuresModalLabel">
                                        {{ trans('App Features') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">
                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="section">{{ trans('Section') }}</label>
                                                <select class="custom-select form-select advance-select" name="section"
                                                    id="app-feature-section">
                                                    <option value="">{{ trans('Select Section') }}</option>
                                                    <option value="mainFeature">{{ trans('mainFeature') }}</option>
                                                    <option value="reviewsCount">{{ trans('reviewsCount') }}</option>
                                                    <option value="reviewsRate">{{ trans('reviewsRate') }}</option>
                                                    <option value="intro">{{ trans('intro') }}</option>
                                                    <option value="secFeaures">{{ trans('secFeaures') }}</option>
                                                    <option value="whyus">{{ trans('whyus') }}</option>
                                                    <option value="included">{{ trans('included') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="app-features-titleLanguageTabs"
                                                    role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active "
                                                            id="items-app-features-title-en-tab" data-bs-toggle="tab"
                                                            data-bs-target="#modal-pane-app-features-title-en"
                                                            type="button" role="tab"
                                                            aria-controls="modal-pane-app-features-title-en"
                                                            aria-selected=" true">{{ trans('English') }}</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  "
                                                            id="items-app-features-title-ar-tab" data-bs-toggle="tab"
                                                            data-bs-target="#modal-pane-app-features-title-ar"
                                                            type="button" role="tab"
                                                            aria-controls="modal-pane-app-features-title-ar"
                                                            aria-selected=" false">{{ trans('العربية') }}</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content mt-3" id="app-features-titleLanguageTabsContent">
                                                    <div class="tab-pane fade show active"
                                                        id="modal-pane-app-features-title-en" role="tabpanel"
                                                        aria-labelledby="items-app-features-title-en-tab">
                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="title">{{ trans('Title') }}</label>
                                                            <input type="text" name="translations[en][title]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter title') }} "
                                                                value="">
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade " id="modal-pane-app-features-title-ar"
                                                        role="tabpanel"
                                                        aria-labelledby="items-app-features-title-ar-tab">
                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required"
                                                                for="title">{{ trans('Title') }}</label>
                                                            <input type="text" name="translations[ar][title]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter title') }} "
                                                                value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="image">{{ trans('Image') }}</label>
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
                                                <label class="" for="value">{{ trans('Value') }}</label>
                                                <input step="0.1" type="number" name="value" id="app-feature-value" class="form-control"
                                                    placeholder="{{ trans('Enter numeric value') }}">
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
                    <div class="modal fade" id="category-app-featuresDeleteModel" tabindex="-1"
                        aria-labelledby="category-app-featuresDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="category-app-featuresDeleteModelLabel">
                                        {{ trans('Delete App Feature') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the App Feature') }} <span></span>?
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
