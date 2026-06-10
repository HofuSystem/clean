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
                        <li class="breadcrumb-item text-muted">@lang('products')</li>
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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{route("dashboard.products.index")}}" data-id="{{ $item->id ?? null }}"
                        @if ($item) action="{{ route('dashboard.products.edit', $item->id) }}"
                            data-mode="edit"
                        @else
                            action="{{ route('dashboard.products.create') }}"
                            data-mode="new" @endif>

                        @csrf
                        <div class="card-body row">
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="clothes" @selected(isset($item) and $item->type == 'clothes')>{{ trans('clothes') }}</option>
                                    <option value="sales" @selected(isset($item) and $item->type == 'sales')>{{ trans('sales') }}</option>
                                    <option value="services" @selected(isset($item) and $item->type == 'services')>{{ trans('services') }}</option>

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-12" id="wash-type-div">
                                <label class="required" for="wash_type">{{ trans('wash type') }}</label>
                                <select class="custom-select  form-select advance-select" name="wash_type" id="wash_type">
                                    <option value="">{{ trans('select wash type') }}</option>
                                    <option value="lab" @selected(isset($item) and $item->wash_type == 'lab')>{{ trans('lab') }}</option>
                                    <option value="washer" @selected(isset($item) and $item->wash_type == 'washer')>{{ trans('washer') }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="display_as">{{ trans('display as') }}</label>
                                <select class="custom-select  form-select advance-select" name="display_as" id="display_as">
                                    <option value="">{{ trans('select display as') }}</option>
                                    <option value="main" @selected(isset($item) and $item->display_as == 'main')>{{ trans('main') }}</option>
                                    <option value="addon" @selected(isset($item) and $item->display_as == 'addon')>{{ trans('addon') }}</option>
                                </select>
                            </div>
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
                            <div class="col-12 mt-5" id="desc-div">
                                <ul class="nav nav-tabs" id="languageTabs" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active " id="desc-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#desc-en" type="button" role="tab" aria-controls="desc-en"
                                            aria-selected=" true">{{ trans('English') }}</button>
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
                                            <label class="" for="desc">{{ trans('desc') }}</label>
                                            <textarea type="number" name="translations[en][desc]" class="form-control "
                                                placeholder="{{ trans('Enter desc') }} ">
                                                    @isset($item)
                                                    {{ $item?->translate('en')?->desc }}
                                                    @endisset
                                                </textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="desc-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="desc">{{ trans('desc') }}</label>
                                            <textarea type="number" name="translations[ar][desc]" class="form-control "
                                                placeholder="{{ trans('Enter desc') }} ">
                                                @isset($item)
                                                {{ $item?->translate('ar')?->desc }}
                                                @endisset
                                            </textarea>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12" id="images-div">
                                <label class="" for="image">{{ trans('image') }}</label>
                                <div class="media-center-group form-control" data-max="10" data-type="gallery">
                                    <input type="text" hidden="hidden" class="form-control" name="image"
                                        value="{{ old('image', $item->image ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-12" id="sku-div">
                                <label class="required" for="sku">{{ trans('sku') }}</label>
                                <input type="text" name="sku" class="form-control "
                                    placeholder="{{ trans('Enter sku') }} "
                                    value="@isset($item){{ $item->sku }}@endisset">

                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_package" name="is_package"
                                        @checked(isset($item) and $item->is_package)>
                                    <label class="form-check-label" for="is_package">{{ trans('is package') }}</label>
                                </div>

                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="category_id">{{ trans('category') }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id"
                                    id="category_id">

                                    <option value="">{{ trans('select category') }}</option>
                                    @foreach ($categories ?? [] as $sItem)
                                        <option data-type="{{ $sItem->type }}" data-id="{{ $sItem->id }}" @selected(isset($item) and $item->category_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-6" id="sub-div">
                                <label class="" for="sub_category_id">{{ trans('sub category') }}</label>
                                <select class="custom-select  form-select advance-select" name="sub_category_id"
                                    id="sub_category_id">

                                    <option value="">{{ trans('select sub category') }}</option>
                                    @foreach ($subCategories ?? [] as $sItem)
                                        <option  data-parent-id="{{ $sItem->parent_id }}" data-id="{{ $sItem->id }}" @selected(isset($item) and $item->sub_category_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label class="required" for="price">{{ trans('price') }}</label>
                                <input type="number" name="price" class="form-control "
                                    placeholder="{{ trans('Enter price') }} "
                                    value="{{ old('price', $item->price ?? null) }}" step="any">

                            </div>
                            <div class="form-group mb-3 col-md-6">
                                  <label class="required" for="cost">{{ trans('cost') }}</label>
                                <input type="number" name="cost" class="form-control "
                                    placeholder="{{ trans('Enter cost') }} "
                                    value="{{ old('cost', $item->cost ?? null) }}" step="any">
                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <div class="mt-3 items-container" data-items-on       =   "priceable_id"
                                    data-items-name     =   "prices" data-items-from     =   "prices">

                                    <h3 class="text-dark">{{ trans('prices') }}</h3>
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

                                                @foreach ($item->prices ?? [] as $sItem)
                                                    <tr data-id="{{ $sItem->id }}"
                                                        data-data="{{ json_encode($sItem->itemData) }}">

                                                        <td>{{ $sItem?->city?->name }}</td>
                                                        <td>{{ $sItem->price }}</td>
                                                        <td>{{ $sItem->cost }}</td>
                                                        <td class="options">{!! $sItem->itemsActions !!}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group mb-3 col-md-12" id="quantity-div">
                                <label class="required" for="quantity">{{ trans('quantity') }}</label>
                                <input type="number" name="quantity" class="form-control "
                                    placeholder="{{ trans('Enter quantity') }} "
                                    value="{{ old('quantity', $item->quantity ?? null) }}">

                            </div>
                            <div class="form-group mb-3 col-md-12">
                                <label class="required" for="status">{{ trans('status') }}</label>
                                <select class="custom-select  form-select advance-select" name="status" id="status">

                                    <option value="">{{ trans('select status') }}</option>
                                    <option value="active" @selected(isset($item) and $item->status == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>{{ trans('not-active') }}
                                    </option>

                                </select>

                            </div>

                            <div class="form-group mb-3 col-md-12" id="product-settings-management-div" style="{{ (isset($item) && $item->type == 'sales') ? '' : 'display: none;' }}">
                                <div class="mt-4 p-4 border rounded bg-light">
                                    <h3 class="text-dark mb-3"><i class="fas fa-cog text-primary me-2"></i>{{ trans('Product Settings Management') }}</h3>
                                    
                                    @if (isset($item))
                                        <!-- Add setting form -->
                                        <div class="row mb-4 bg-white p-3 rounded border">
                                            <div class="col-md-6 form-group">
                                                <label class="form-label font-weight-bold mb-2" for="global-setting-select">{{ trans('Select Global Setting') }}</label>
                                                <select id="global-setting-select" class="form-select form-control select2">
                                                    <option value="">{{ trans('Select Setting') }}</option>
                                                    @foreach ($globalSettings as $gSetting)
                                                        <option value="{{ $gSetting->id }}" data-options="{{ json_encode($gSetting->productSettings->map(fn($opt) => ['id' => $opt->id, 'name' => $opt->name])) }}">
                                                            {{ $gSetting->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6 form-group mt-3 mt-md-0" id="options-selection-container" style="display: none;">
                                                <label class="form-label font-weight-bold mb-2">{{ trans('Select Options') }}</label>
                                                <div id="options-checkboxes" class="d-flex flex-wrap gap-3 p-3 border rounded bg-light">
                                                    <!-- Dynamic checkboxes will be inserted here -->
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-3 text-end">
                                                <button type="button" id="btn-associate-setting" class="btn btn-primary btn-sm" style="display: none;">
                                                    <i class="fas fa-save me-1"></i> {{ trans('Save Setting & Options') }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Associated settings table -->
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover text-center align-middle border">
                                                <thead class="table-primary text-white text-capitalize">
                                                    <tr>
                                                        <th>{{ trans('Setting Name') }}</th>
                                                        <th>{{ trans('Selected Options') }}</th>
                                                        <th>{{ trans('Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     @php
                                                         $associatedSettings = $item->productSettings()
                                                             ->whereNull('parent_id')
                                                             ->active()
                                                             ->with([
                                                                 'translations',
                                                                 'productSettings' => function ($q) use ($item) {
                                                                     $q->active()
                                                                         ->whereHas('products', function ($pq) use ($item) {
                                                                             $pq->where('products.id', $item->id);
                                                                         })
                                                                         ->with('translations');
                                                                 }
                                                             ])
                                                             ->get();
                                                     @endphp
                                                    @forelse ($associatedSettings as $assocSetting)
                                                        <tr data-setting-id="{{ $assocSetting->id }}">
                                                            <td class="font-weight-bold">{{ $assocSetting->name }}</td>
                                                            <td>
                                                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                                                    @forelse ($assocSetting->productSettings as $opt)
                                                                        <span class="badge bg-success text-white p-2 m-1">
                                                                            {{ $opt->name }} 
                                                                            @if($opt->addon_price > 0)
                                                                                (+{{ $opt->addon_price }} {{ trans('SAR') }})
                                                                            @endif
                                                                        </span>
                                                                    @empty
                                                                        <span class="text-muted">{{ trans('No options selected') }}</span>
                                                                    @endforelse
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-icon btn-danger remove-associated-setting" 
                                                                        data-url="{{ route('dashboard.products.delete-setting', ['id' => $item->id, 'setting_id' => $assocSetting->id]) }}"
                                                                        title="{{ trans('Delete') }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-muted py-3">{{ trans('No settings associated with this product yet') }}</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning py-3 text-center">
                                            <i class="fas fa-exclamation-triangle me-2"></i> {{ trans('Please save the product first to manage its settings') }}
                                        </div>
                                    @endif
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

                                            <div class="form-group mb-3 col-md-6 hidden" style="display: none;">
                                                <label class="required"
                                                    for="priceable_type">{{ trans('priceable') }}</label>
                                                <input type="text" name="priceable_type" class="form-control "
                                                    placeholder="{{ trans('Enter priceable') }} " value="Core\Products\Models\Product">

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
                                        <span></span>
                                    </h5>
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

                    <div class="modal fade" id="product-settingsModal" aria-hidden="true"
                        aria-labelledby="product-settingsModalLabel"
                        data-store="{{ route('dashboard.product-settings.create') }}">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="product-settingsModalLabel">
                                        {{ trans('options moadl') }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="modal-form items-modal-form">
                                        <div class="row">

                                            <div class="col-12 mt-5">
                                                <ul class="nav nav-tabs" id="settingLanguageTabs" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active " id="items-product-settings-name-en-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-product-settings-name-en"
                                                            type="button" role="tab" aria-controls="items-product-settings-name-en"
                                                            aria-selected="true">{{ trans('English') }}</button>
                                                    </li>

                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  " id="items-product-settings-name-ar-tab"
                                                            data-bs-toggle="tab" data-bs-target="#items-product-settings-name-ar"
                                                            type="button" role="tab" aria-controls="items-product-settings-name-ar"
                                                            aria-selected="false">{{ trans('العربية') }}</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content mt-3" id="settingLanguageTabsContent">
                                                    <div class="tab-pane fade show active" id="items-product-settings-name-en"
                                                        role="tabpanel" aria-labelledby="items-product-settings-name-en-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required" for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[en][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">
                                                        </div>

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="" for="description">{{ trans('description') }}</label>
                                                            <textarea name="translations[en][description]" class="form-control" rows="3"
                                                                placeholder="{{ trans('Enter description') }}"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade " id="items-product-settings-name-ar" role="tabpanel"
                                                        aria-labelledby="items-product-settings-name-ar-tab">

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="required" for="name">{{ trans('name') }}</label>
                                                            <input type="text" name="translations[ar][name]"
                                                                class="form-control "
                                                                placeholder="{{ trans('Enter name') }} " value="">
                                                        </div>

                                                        <div class="form-group mb-3 col-md-12">
                                                            <label class="" for="description">{{ trans('description') }}</label>
                                                            <textarea name="translations[ar][description]" class="form-control" rows="3"
                                                                placeholder="{{ trans('Enter description') }}"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required" for="addon_price">{{ trans('addon price') }}</label>
                                                <input type="number" name="addon_price" class="form-control "
                                                    placeholder="{{ trans('Enter addon price') }} " value="">
                                            </div>

                                            <div class="form-group mb-3 col-md-6">
                                                <label class="required" for="cost">{{ trans('cost') }}</label>
                                                <input type="number" name="cost" class="form-control "
                                                    placeholder="{{ trans('Enter cost') }} " value="">
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="discount_percent">{{ trans('discount percent') }}</label>
                                                <input type="number" step="0.01" name="discount_percent" class="form-control "
                                                    placeholder="{{ trans('Enter discount percent') }} " value="">
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="required" for="status">{{ trans('status') }}</label>
                                                <select class="custom-select form-select advance-select" name="status"
                                                    id="parent_id-status">
                                                    <option value="">{{ trans('select status') }}</option>
                                                    <option value="active">{{ trans('active') }}</option>
                                                    <option value="not-active">{{ trans('not-active') }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="color">{{ trans('color') }}</label>
                                                <div class="input-group">
                                                    <input type="text" name="color" class="form-control color-text-input"
                                                        placeholder="{{ trans('e.g., #000000') }}" value="">
                                                    <input type="color" class="form-control form-control-color color-picker-input"
                                                        style="max-width: 60px; padding: 2px;" value="#000000">
                                                </div>
                                            </div>

                                            <div class="form-group mb-3 col-md-12">
                                                <label class="" for="icon">{{ trans('icon') }}</label>
                                                <div class="media-center-group form-control" data-max="1" data-type="image">
                                                    <input type="text" hidden="hidden" class="form-control" name="icon"
                                                        value="">
                                                    <button type="button" class="btn btn-secondary media-center-load"
                                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                                    <div class="input-gallery"></div>
                                                </div>
                                            </div>

                                            <div class="col-lg-9 ml-lg-auto">
                                                <button type="submit" class="btn btn-primary font-weight-bold mr-2">{{
                                                    trans('save') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="product-settingsDeleteModel" tabindex="-1"
                        aria-labelledby="product-settingsDeleteModelLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="product-settingsDeleteModelLabel">
                                        {{ trans('Delete ProductSetting') }} <span></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ trans('Are you sure you want to delete the ProductSetting') }} <span></span>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                                        trans('Close') }}</button>
                                    <button type="button" class="btn btn-danger items-final-delete">{{ trans('Delete')
                                        }}</button>
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
        $(document).ready(function () {
            // Store all subcategory options when the document is ready
            var allSubCategories    = $('#sub_category_id option').clone();
            var allCategories       = $('#category_id option').clone();
            // When the category changes
            $('#type').change(function() {
                $('#desc-div,#sub-div,#quantity-div,#sku-div,#wash-type-div,#product-settings-management-div').hide();

                var type            = $(this).val();
                var $Category       = $('#category_id');
                var $CategoryValue  = $('#category_id').val();
                if(type =="clothes"){
                    $('#images-div,#sub-div,#sku-div,#wash-type-div').show();
                }else  if(type =="sales"){
                    $('#desc-div,#sub-div,#product-settings-management-div').show();

                }else  if(type =="services"){
                    $('#wash-type-div').show();

                }
                // Clear the current options
                $Category.empty();

                // Add only the options that belong to the selected category
                allCategories.each(function() {
                    if ($(this).data('type') == type) {
                        $Category.append($(this).clone()); // Add matching options
                    }

                });

                // Trigger Select2 to update the dropdown
                $Category.val($CategoryValue).trigger('change');

            });
            $('#category_id').change(function() {
                var category_id         = $(this).val();
                var $subCategory        = $('#sub_category_id');
                var $subCategoryValue   = $('#sub_category_id').val();
                // Clear the current options
                $subCategory.empty();

                // Add only the options that belong to the selected category
                allSubCategories.each(function() {
                    if ($(this).data('parent-id') == category_id) {
                        $subCategory.append($(this).clone()); // Add matching options
                    }

                });

                // Trigger Select2 to update the dropdown
                $subCategory.val($subCategoryValue).trigger('change');

            });
            $('#type').change()

            // Product settings management logic
            const associatedIds = @json(isset($item) ? $item->productSettings->pluck('id')->toArray() : []);
            const settingSelect = document.getElementById('global-setting-select');
            const optionsContainer = document.getElementById('options-selection-container');
            const optionsCheckboxes = document.getElementById('options-checkboxes');
            const btnAssociate = document.getElementById('btn-associate-setting');

            if (settingSelect) {
                $(settingSelect).on('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (!selectedOption || !selectedOption.value) {
                        optionsContainer.style.display = 'none';
                        btnAssociate.style.display = 'none';
                        return;
                    }

                    const optionsData = JSON.parse(selectedOption.getAttribute('data-options') || '[]');
                    optionsCheckboxes.innerHTML = '';

                    if (optionsData.length === 0) {
                        optionsCheckboxes.innerHTML = '<span class="text-muted">{{ trans("No options available for this setting") }}</span>';
                    } else {
                        optionsData.forEach(opt => {
                            const isChecked = associatedIds.includes(opt.id) ? 'checked' : '';
                            const wrapper = document.createElement('div');
                            wrapper.className = 'form-check form-check-inline mx-2 my-1';
                            wrapper.innerHTML = `
                                <input class="form-check-input option-checkbox" type="checkbox" name="options[]" value="${opt.id}" id="opt-${opt.id}" ${isChecked}>
                                <label class="form-check-label" for="opt-${opt.id}">
                                    ${opt.name}
                                </label>
                            `;
                            optionsCheckboxes.appendChild(wrapper);
                        });
                    }

                    optionsContainer.style.display = 'block';
                    btnAssociate.style.display = 'inline-block';
                });
            }

            if (btnAssociate) {
                btnAssociate.addEventListener('click', function() {
                    const productId = "{{ $item->id ?? '' }}";
                    if (!productId) return;

                    const settingId = settingSelect.value;
                    const checkedCheckboxes = document.querySelectorAll('.option-checkbox:checked');
                    const optionIds = Array.from(checkedCheckboxes).map(cb => cb.value);

                    // Send AJAX request
                    fetch(`{{ route('dashboard.products.associate-settings', ['id' => $item->id ?? 0]) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            setting_id: settingId,
                            option_ids: optionIds
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status || data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: data.message || 'Saved',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Something went wrong'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving'
                        });
                    });
                });
            }

            // Delete handler
            document.querySelectorAll('.remove-associated-setting').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    Swal.fire({
                        title: '{{ trans("Are you sure?") }}',
                        text: '{{ trans("You will remove this setting and all its selected options from the product.") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: '{{ trans("Yes, delete it!") }}',
                        cancelButtonText: '{{ trans("Cancel") }}'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(url, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status || data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: data.message || 'Deleted',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'Something went wrong'
                                    });
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while deleting'
                                });
                            });
                        }
                    });
                });
            });
        });
    </script>
@endpush
