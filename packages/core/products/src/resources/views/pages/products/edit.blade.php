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
                $('#desc-div,#sub-div,#quantity-div,#sku-div').hide();

                var type            = $(this).val();
                var $Category       = $('#category_id');
                var $CategoryValue  = $('#category_id').val();
                if(type =="clothes"){
                    $('#images-div,#sub-div,#sku-div').show();
                }else  if(type =="sales"){
                    $('#desc-div,#quantity-div').show();

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
        });
    </script>
@endpush
