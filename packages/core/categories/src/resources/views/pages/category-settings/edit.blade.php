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
                        <li class="breadcrumb-item text-muted">@lang('services')</li>
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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.category-settings.index")}}" data-id="{{ $item->id ?? null }}"
                        @isset($item)
                            action="{{ route('dashboard.category-settings.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.category-settings.create') }}"
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
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="category_id">{{ trans('service') }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id"
                                    id="category_id">

                                    <option value="">{{ trans('select service') }}</option>
                                    @foreach ($categories ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->category_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="addon_price">{{ trans('addon price') }}</label>
                                <input type="number" name="addon_price" class="form-control "
                                    placeholder="{{ trans('Enter addon price') }} "
                                    value="{{ old('addon_price', $item->addon_price ?? null) }}">

                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="cost">{{ trans('cost') }}</label>
                                <input type="number" name="cost" class="form-control "
                                    placeholder="{{ trans('Enter cost') }} "
                                    value="{{ old('cost', $item->cost ?? null) }}">

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <div class="mt-3 items-container" data-items-on       =   "priceable_id"
                                    data-items-name     =   "addon_prices" data-items-from     =   "prices">

                                    <h3 class="text-dark">{{ trans('addon prices') }}</h3>
                                    <button class="btn-operation create-new-items"><i class="fas fa-plus"></i></button>
                                    <hr>
                                    <div class="table-responsive ">
                                        <table class="table table-striped table-hover text-center">
                                            <thead class="table-primary text-white text-capitalize h6">
                                                <tr>

                                                    <th scope="col" data-name="city_id" data-type="select">
                                                        {{ trans('city') }}</th>
                                                    <th scope="col" data-name="price" data-type="number">
                                                        {{ trans('price') }}</th>
                                                    <th scope="col" data-name="cost" data-type="number">
                                                        {{ trans('cost') }}</th>
                                                    <th scope="col" data-name="actions" data-type="actions">
                                                        {{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($item->addonPrices ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->city?->name }}</td>
                                                        <td>{{ $sItem->price }}</td>
                                                        <td class="options">{!! $sItem->itemsActions !!}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="" for="parent_id">{{ trans('parent') }}</label>
                                <select class="custom-select  form-select advance-select" name="parent_id"
                                    id="parent_id">

                                    <option value="">{{ trans('select parent') }}</option>
                                    @foreach ($parents ?? [] as $sItem)
                                        <option data-id="{{ $sItem->id }}" @selected(isset($item) and $item->parent_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="status">{{ trans('status') }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status">

                                    <option value="">{{ trans('select status') }}</option>
                                    <option value="active" @selected(isset($item) and $item->status == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>{{ trans('not-active') }}
                                    </option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12">
                                <div class="mt-3 items-container" data-items-on       =   "parent_id"
                                    data-items-name     =   "category_settings"
                                    data-items-from     =   "category-settings">

                                    <h3 class="text-dark">{{ trans('service sub settings') }}</h3>
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
                                                    <th scope="col" data-name="cost" data-type="number">
                                                        {{ trans('cost') }}</th>
                                                    <th scope="col" data-name="status" data-type="select">
                                                        {{ trans('status') }}</th>
                                                    <th scope="col" data-name="actions" data-type="actions">
                                                        {{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($item->categorySettings ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->translate('en')?->name }}</td>
                                                        <td>{{ $sItem?->translate('ar')?->name }}</td>
                                                        <td>{{ $sItem->name }}</td>
                                                        <td>{{ $sItem->addon_price }}</td>
                                                        <td>{{ $sItem->cost }}</td>
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
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="modal fade" id="pricesModal" aria-hidden="true" aria-labelledby="pricesModalLabel"
                        data-store="{{ route('dashboard.prices.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="pricesModalLabel">{{ trans('prices') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">

                                            <div class="form-group mb-3 col-md-6" style="display: none;">
                                                <label class="required"
                                                    for="priceable_type">{{ trans('priceable') }}</label>
                                                <input type="text" name="priceable_type" class="form-control "
                                                    placeholder="{{ trans('Enter priceable') }} "
                                                    value="Core\Categories\Models\CategorySetting">

                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="city_id">{{ trans('city') }}</label>
                                                <select class="custom-select  form-select advance-select" name="city_id"
                                                    id="priceable_id-city_id">

                                                    <option value="">{{ trans('select city') }}</option>
                                                    @foreach ($cities ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}"
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required" for="price">{{ trans('price') }}</label>
                                                <input type="number" name="price" class="form-control "
                                                    placeholder="{{ trans('Enter price') }} " value="">

                                            </div>
                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required" for="cost">{{ trans('cost') }}</label>
                                                <input type="number" name="cost" class="form-control "
                                                    placeholder="{{ trans('Enter cost') }} " value="">

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
                    <div class="modal fade" id="pricesDeleteModel" tabindex="-1"
                        aria-labelledby="pricesDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="pricesDeleteModelLabel">{{ trans('Delete prices') }}
                                        <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the prices') }} <span></span>?
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
                                        {{ trans('CategorySetting') }}</h1>
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
                                                <label class="required" for="category_id">{{ trans('service') }}</label>
                                                <select class="custom-select  form-select advance-select"
                                                    name="category_id" id="parent_id-category_id">

                                                    <option value="">{{ trans('select service') }}</option>
                                                    @foreach ($categories ?? [] as $sItem)
                                                        <option data-id="{{ $sItem->id }}"
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required"
                                                    for="addon_price">{{ trans('addon price') }}</label>
                                                <input type="number" name="addon_price" class="form-control "
                                                    placeholder="{{ trans('Enter addon price') }} " value="">

                                            </div>
                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required"
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
@endpush
