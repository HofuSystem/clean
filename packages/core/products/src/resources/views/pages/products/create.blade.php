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

                    <form class="form" method="POST" id="operation-form"  redirect-to="{{ request('type') == 'sales' ? route('dashboard.products.sales') : route('dashboard.products.index') }}" data-id="{{ $item->id ?? null }}"
                        action="{{ route('dashboard.products.create') }}" data-mode="new">
                        @csrf
                        <div class="card-body row g-4 product-form-grid">
                            <div class="form-group col-lg-4 col-md-6" id="type-div">
                                <label class="required" for="type">{{ trans('type') }}</label>
                                <select class="custom-select  form-select advance-select" name="type" id="type">

                                    <option value="">{{ trans('select type') }}</option>
                                    <option value="clothes" @selected((isset($item) and $item->type == 'clothes') || (!isset($item) && request('type') != 'sales'))>{{ trans('clothes') }}</option>
                                    <option value="sales" @selected((isset($item) and $item->type == 'sales') || request('type') == 'sales')>{{ trans('sales') }}</option>
                                    <option value="services" @selected(isset($item) and $item->type == 'services')>{{ trans('services') }}</option>

                                </select>

                            </div>
                            <div class="form-group col-lg-4 col-md-6" id="wash-type-div">
                                <label class="required" for="wash_type">{{ trans('wash type') }}</label>
                                <select class="custom-select  form-select advance-select" name="wash_type" id="wash_type">
                                    <option value="">{{ trans('select wash type') }}</option>
                                    <option value="lab" @selected(isset($item) and $item->wash_type == 'lab')>{{ trans('lab') }}</option>
                                    <option value="washer" @selected(isset($item) and $item->wash_type == 'washer')>{{ trans('washer') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-4 col-md-6" id="display-as-div">
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
                                        <button class="nav-link {{ app()->getLocale() === 'en' ? 'active' : '' }}" id="name-en-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-en" type="button" role="tab" aria-controls="name-en"
                                            aria-selected="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}">{{ trans('English') }}</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ app()->getLocale() === 'ar' ? 'active' : '' }}" id="name-ar-tab" data-bs-toggle="tab"
                                            data-bs-target="#name-ar" type="button" role="tab" aria-controls="name-ar"
                                            aria-selected="{{ app()->getLocale() === 'ar' ? 'true' : 'false' }}">{{ trans('Arabic') }}</button>
                                    </li>

                                </ul>
                                <div class="tab-content mt-3" id="languageTabsContent">
                                    <div class="tab-pane fade {{ app()->getLocale() === 'en' ? 'show active' : '' }}" id="name-en" role="tabpanel"
                                        aria-labelledby="en-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[en][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('en')?->name }} @endisset">

                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label for="desc_en">{{ trans('desc') }}</label>
                                            <textarea name="translations[en][desc]" id="desc_en" class="form-control" rows="4"
                                                placeholder="{{ trans('Enter desc') }}">{{ old('translations.en.desc') }}</textarea>
                                        </div>

                                    </div>
                                    <div class="tab-pane fade {{ app()->getLocale() === 'ar' ? 'show active' : '' }}" id="name-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="required" for="name">{{ trans('name') }}</label>
                                            <input type="text" name="translations[ar][name]" class="form-control "
                                                placeholder="{{ trans('Enter name') }} "
                                                value="@isset($item) {{ $item?->translate('ar')?->name }} @endisset">

                                        </div>

                                        <div class="form-group mb-3 col-md-12">
                                            <label for="desc_ar">{{ trans('desc') }}</label>
                                            <textarea name="translations[ar][desc]" id="desc_ar" class="form-control" rows="4"
                                                placeholder="{{ trans('Enter desc') }}">{{ old('translations.ar.desc') }}</textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-8 col-md-12 product-media-panel" id="images-div">
                                <label class="" for="image">{{ trans('image') }}</label>
                                <div class="media-center-group form-control" data-max="10" data-type="gallery">
                                    <input type="text" hidden="hidden" class="form-control" name="image"
                                        value="{{ old('image', $item->image ?? null) }}">
                                    <button type="button" class="btn btn-secondary media-center-load"
                                        style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                                    <div class="input-gallery"></div>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-12" id="sku-div">
                                <label for="sku">{{ trans('sku') }}</label>
                                <input type="text" name="sku" class="form-control"
                                    placeholder="{{ trans('Enter sku') }}"
                                    value="{{ old('sku') }}">
                            </div>
                            <div class="form-group col-lg-4 col-md-6 product-compact-field" id="category-field">
                                <label class="required" for="category_id">{{ trans('category') }}</label>
                                <select class="custom-select  form-select advance-select" name="category_id"
                                    id="category_id">

                                    <option value="">{{ trans('select category') }}</option>
                                    @foreach ($categories ?? [] as $sItem)
                                        <option data-type="{{ $sItem->type }}" data-is-package="{{ $sItem->is_package ? 1 : 0 }}" data-id="{{ $sItem->id }}" @selected(isset($item) and $item->category_id == $sItem->id)
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group col-lg-4 col-md-6 product-compact-field" id="sub-div" style="display: none;">
                                <label class="" for="sub_category_id">{{ trans('sub category') }}</label>
                                <select class="custom-select form-select advance-select sub-category-select" name="sub_category_id"
                                    id="sub_category_id">

                                    <option value="">{{ trans('select sub category') }}</option>
                                    @foreach ($subCategories ?? [] as $sItem)
                                        <option data-parent-id="{{ $sItem->parent_id }}" data-id="{{ $sItem->id }}"
                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="form-group col-lg-4 col-md-6 product-compact-field" id="product-status-field">
                                <label class="required" for="status">{{ trans('status') }}</label>
                                <select class="custom-select form-select advance-select" name="status" id="status">
                                    <option value="">{{ trans('select status') }}</option>
                                    <option value="active" @selected(isset($item) and $item->status == 'active')>{{ trans('active') }}</option>
                                    <option value="not-active" @selected(isset($item) and $item->status == 'not-active')>{{ trans('not-active') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-3 col-md-6" id="quantity-div">
                                <label class="required" for="quantity">{{ trans('quantity') }}</label>
                                <input type="number" name="quantity" class="form-control "
                                    placeholder="{{ trans('Enter quantity') }} "
                                    value="{{ old('quantity', $item->quantity ?? null) }}">

                            </div>
                            <div class="form-group col-lg-3 col-md-6" id="price-div">
                                <label class="required" for="price">{{ trans('price') }}</label>
                                <input type="number" name="price" class="form-control "
                                    placeholder="{{ trans('Enter price') }} " step="any" min="0"
                                    value="{{ old('price', $item->price ?? null) }}">
                            </div>
                            <div class="form-group col-lg-3 col-md-6" id="points-div">
                                <label class="required" for="points">{{ trans('points') }}</label>
                                <input type="number" name="points" class="form-control "
                                    placeholder="{{ trans('Enter points') }} " step="any" min="0"
                                    value="{{ old('points', $item->points ?? null) }}">
                            </div>
                            <div class="form-group col-lg-3 col-md-6" id="cost-div">
                                <label class="required" for="cost">{{ trans('cost') }}</label>
                                <input type="number" name="cost" class="form-control "
                                    placeholder="{{ trans('Enter cost') }} " step="any" min="0"
                                    value="{{ old('cost', $item->cost ?? null) }}">
                            </div>
                            @include('products::pages.products._city_prices')
                            @include('products::pages.products._additional_products')
                            @include('products::pages.products._variants')
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
        .product-form-grid > [class*="col-"] { min-width: 0; }
        .product-form-grid { --bs-gutter-x: 1.25rem; --bs-gutter-y: 1.25rem; }
        .product-form-grid .form-control,
        .product-form-grid .form-select,
        .product-form-grid .select2-selection { border-radius: 12px !important; min-height: 44px; border-color: #dfe3e8; }
        .product-form-grid .form-select { padding-top: .65rem; padding-bottom: .65rem; line-height: 1.5; }
        .product-form-grid .select2-container .select2-selection--single { height: 44px !important; }
        .product-form-grid .select2-container .select2-selection--single .select2-selection__rendered { line-height: 42px !important; padding-top: 0 !important; padding-bottom: 0 !important; }
        .product-form-grid .select2-container .select2-selection--single .select2-selection__arrow { height: 42px !important; top: 1px !important; }
        .product-media-panel,
        .product-compact-field { background: transparent; border: 0; border-radius: 0; padding: 6px; margin: 6px; }
        @media (min-width: 992px) {
            .product-media-panel.col-lg-8 { flex: 0 0 calc(66.666667% - 12px); width: calc(66.666667% - 12px); }
            .product-media-panel.col-lg-6 { flex: 0 0 calc(50% - 12px); width: calc(50% - 12px); }
            .product-compact-field.col-lg-4 { flex: 0 0 calc(33.333333% - 12px); width: calc(33.333333% - 12px); }
            .product-compact-field.col-lg-3 { flex: 0 0 calc(25% - 12px); width: calc(25% - 12px); }
            .product-compact-field.col-lg-6 { flex: 0 0 calc(50% - 12px); width: calc(50% - 12px); }
            .product-compact-field.col-lg-12 { flex: 0 0 calc(100% - 12px); width: calc(100% - 12px); }
        }
        @media (min-width: 768px) and (max-width: 991.98px) {
            .product-media-panel.col-md-12 { flex: 0 0 calc(100% - 12px); width: calc(100% - 12px); }
            .product-compact-field.col-md-6 { flex: 0 0 calc(50% - 12px); width: calc(50% - 12px); }
        }
        .product-media-panel .media-center-group { min-height: 108px; background: #fff; }
        .product-switch-card { min-height: 76px; display: flex; align-items: center; gap: 12px; margin: 0; }
        .product-switch-card .form-check-input { width: 2.75rem; height: 1.5rem; margin: 0; flex: 0 0 auto; }
        .product-switch-card .form-check-label { font-weight: 600; line-height: 1.6; }
        .product-form-section { box-shadow: none; border-color: #e4e6ef !important; }
        .product-table-wrap { border: 1px solid #e4e6ef; border-radius: 16px; overflow: hidden; padding: 0; box-shadow: 0 6px 24px rgba(30, 41, 59, .04); }
        .product-inner-table thead th { background: #4f7fba !important; color: #fff !important; font-weight: 600; white-space: nowrap; vertical-align: middle; }
        .product-inner-table td { min-width: 135px; vertical-align: middle; }
        .product-inner-table th:last-child, .product-inner-table td:last-child { min-width: 90px; }
        .product-form-grid .nav-tabs { margin-bottom: 1rem; }
        @media (max-width: 767.98px) {
            .card-body.product-form-grid { padding: 1rem; }
            .product-inner-table { min-width: 760px; }
            .product-media-panel, .product-compact-field { border-radius: 14px; padding: 14px; }
            .product-media-panel, .product-compact-field { flex: 0 0 calc(100% - 12px); width: calc(100% - 12px); }
        }

         .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
            border-bottom: 1px solid #dee2e6 !important;
            /* Ensure borders are visible */
        }

        .table thead th {
            font-size: 0.9rem;
            /* Smaller font size for the title */
        }

        .table tbody th,
        .table tbody td {
            font-size: 1rem;
            /* Default font size for values */
        }

        .table-bordered th[scope="row"] {
            width: 30%
        }

        .table-bordered tr {
            border: 1px solid #dee2e6 !important;
        }

        .table-bordered {
            border: 1px solid #dee2e6 !important;
        }
    </style>
@endpush
@push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.5/jquery.tablednd.js"></script>
    <script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
    <script>
        $(document).ready(function() {
            //when category change change hied the sub category that doeds not belong to this category
            // Store all subcategory options when the document is ready
            var allSubCategories = $('#sub_category_id option').clone();
            var allCategories = $('#category_id option').clone();
            var categoryPlaceholder = allCategories.filter('[value=""]').first().clone();
            var subCategoryPlaceholder = allSubCategories.filter('[value=""]').first().clone();
            $('.sub-category-select,#category_id').empty();
            function applyPackageOrTypeVisibility() {
                var type = $('#type').val();
                var isPackage = Number($('#category_id option:selected').data('is-package')) === 1;
                var isWashingBatch = type === 'clothes' && !isPackage;

                $('#sku-div,#images-div').show();
                $('#wash-type-div,#display-as-div,#quantity-div,#sub-div,#product-settings-management-div').hide();
                $('#sku-div').toggle(isPackage || type !== 'clothes');
                $('#price-div,#points-div,#cost-div').toggle(!isPackage && !isWashingBatch);

                var hasSubCategories = $('#sub_category_id option[value!=""]').length > 0;
                var showSubCategory = hasSubCategories && !isPackage && !isWashingBatch;
                $('#sub-div').toggle(showSubCategory);
                $('#sub-div label').toggleClass('required', showSubCategory);
                if (!showSubCategory) {
                    $('#sub_category_id').val(null).trigger('change.select2');
                }

                $('#product-variants-section').toggle(isPackage);
                $('#city-prices-section').toggle(!isPackage && !isWashingBatch);
                $('#additional-products-section').toggle(isWashingBatch);
                if (isPackage || isWashingBatch) {
                    $('#city-prices-table tbody').empty();
                }
                if (!isWashingBatch) {
                    $('#additional-products-table tbody').empty();
                }
                if (isWashingBatch && $('#category_id').val() && $('#additional-products-table tbody tr').length === 0 && window.addAdditionalProductRow) {
                    window.addAdditionalProductRow();
                }
                if (isPackage && $('#product-variants-table tbody tr').length === 0 && window.addProductVariantRow) {
                    window.addProductVariantRow();
                }
                if (!isPackage) {
                    $('#product-variants-table tbody').empty();
                }

                if (type === 'clothes') {
                    $('#wash-type-div').show();
                } else if (type === 'sales') {
                    $('#display-as-div,#quantity-div,#product-settings-management-div').show();
                }

                syncProductHeaderLayout();
                syncVisibleFieldWidths();
            }

            function syncProductHeaderLayout() {
                $('#images-div').removeClass('col-lg-6').addClass('col-lg-8');
                $('#sku-div').removeClass('col-lg-3').addClass('col-lg-4');
            }

            function syncVisibleFieldWidths() {
                var type = $('#type').val();
                var $topFields = $('#type-div,#wash-type-div,#display-as-div');
                $topFields.removeClass('col-lg-4 col-lg-6 col-lg-12');
                if (type === 'clothes') {
                    $('#type-div,#wash-type-div').addClass('col-lg-6');
                } else if (type === 'sales') {
                    $('#type-div,#display-as-div').addClass('col-lg-6');
                } else {
                    $('#type-div').addClass('col-lg-12');
                }

                var $classification = $('#category-field,#sub-div,#product-status-field');
                $classification.removeClass('col-lg-4 col-lg-6 col-lg-12');
                var $visibleClassification = $classification.filter(':visible');
                $visibleClassification.addClass($visibleClassification.length === 3 ? 'col-lg-4' : ($visibleClassification.length === 2 ? 'col-lg-6' : 'col-lg-12'));

                var $financialFields = $('#quantity-div,#price-div,#points-div,#cost-div');
                $financialFields.removeClass('col-lg-3 col-lg-4 col-lg-6 col-lg-12');
                var $visibleFinancialFields = $financialFields.filter(':visible');
                $visibleFinancialFields.addClass($visibleFinancialFields.length === 4 ? 'col-lg-3' : 'col-lg-4');
            }

            // When the category changes
            $('#type').change(function() {
                var type        = $(this).val();
                var $Category   = $('#category_id');

                if (type == "sales") {
                    $('#operation-form').attr('redirect-to', "{{ route('dashboard.products.sales') }}");
                } else {
                    $('#operation-form').attr('redirect-to', "{{ route('dashboard.products.index') }}");
                }

                // Clear the current options
                $Category.empty();
                $Category.append(categoryPlaceholder.clone());

                // Add only the options that belong to the selected category
                allCategories.each(function() {
                    if ($(this).data('type') == type) {
                        $Category.append($(this).clone()); // Add matching options
                    }
                });

                applyPackageOrTypeVisibility();

                // Trigger Select2 to update the dropdown
                $Category.trigger('change');
            });

            $('#category_id').change(function() {
                var category_id = $(this).val();
                var $subCategory = $('#sub_category_id');
                $('#additional-products-table tbody').empty();
                // Clear the current options
                $subCategory.empty();
                $subCategory.append(subCategoryPlaceholder.clone());

                // Add only the options that belong to the selected category
                allSubCategories.each(function() {
                    if ($(this).data('parent-id') == category_id) {
                        $subCategory.append($(this).clone()); // Add matching options
                    }
                });

                applyPackageOrTypeVisibility();

                // Trigger Select2 to update the dropdown
                $subCategory.trigger('change');
            });
            // Auto copy price to points
            $(document).on('input', 'input[name="price"]', function() {
                let priceVal = $(this).val();
                let pointsInput = $(this).closest('.card-body, tfoot').find('input[name="points"]');
                if (pointsInput.length) {
                    pointsInput.val(priceVal);
                }
            });

            $('#type').change();
        });
    </script>
@endpush
