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
                        action="{{ route('dashboard.products.create') }}" data-mode="new">
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
                                            <textarea type="text" name="translations[en][desc]" class="form-control "
                                                placeholder="{{ trans('Enter desc') }} "></textarea>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="desc-ar" role="tabpanel" aria-labelledby="ar-tab">

                                        <div class="form-group mb-3 col-md-12">
                                            <label class="" for="desc">{{ trans('desc') }}</label>
                                            <textarea type="text" name="translations[ar][desc]" class="form-control "
                                                placeholder="{{ trans('Enter desc') }} "></textarea>

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
                            <div class="form-group mb-3 col-md-12">
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
                            <div class="table-responsive b-2 items-container"  id="vars-div"
                                    data-items-name     =   "version"
                                    data-items-on       =   "id"
                                    data-items-from     =   "products"
                                    >
                                <table class="table text-center table-bordered">
                                    <thead class="bg-primary text-white p-2">
                                        <tr>
                                            <th scope="col">{{ trans('sub category') }}</th>
                                            <th scope="col">{{ trans('sku') }}</th>
                                            <th scope="col">{{ trans('price') }}</th>
                                            <th scope="col">{{ trans('cost') }}</th>
                                            <th scope="col">{{ trans('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <select class="custom-select  form-select advance-select"
                                                    name="sub_category_id" id="sub_category_id">

                                                    <option value="">{{ trans('select sub category') }}</option>
                                                    @foreach ($subCategories ?? [] as $sItem)
                                                        <option data-parent-id="{{ $sItem->parent_id }}" data-id="{{ $sItem->id }}"
                                                            value="{{ $sItem->id }}">{{ $sItem->name }}</option>
                                                    @endforeach

                                                </select>

                                            </td>
                                            <td>
                                                <input type="text" name="sku" class="form-control"
                                                    placeholder="{{ trans('Enter sku') }} ">
                                            </td>
                                            <td>
                                                <input type="number" name="price" class="form-control"
                                                    placeholder="{{ trans('Enter price') }} " step="any" min="0">
                                            </td>
                                            <td>
                                                <input type="number" name="cost" class="form-control "
                                                    placeholder="{{ trans('Enter cost') }} " step="any" min="0">
                                            </td>
                                            <td>
                                                <button id="addToRow" class="btn btn-dark ">{{ trans('Add') }}</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="form-group mb-3 col-md-12" id="quantity-div">
                                <label class="required" for="quantity">{{ trans('quantity') }}</label>
                                <input type="number" name="quantity" class="form-control "
                                    placeholder="{{ trans('Enter quantity') }} "
                                    value="{{ old('quantity', $item->quantity ?? null) }}">

                            </div>
                            <div class="form-group mb-3 col-md-12" id="price-div">
                                <label class="required" for="price">{{ trans('price') }}</label>
                                <input type="number" name="price" class="form-control "
                                    placeholder="{{ trans('Enter price') }} " step="any" min="0"
                                    value="{{ old('price', $item->price ?? null) }}">
                            </div>
                            <div class="form-group mb-3 col-md-12" id="cost-div">
                                <label class="required" for="cost">{{ trans('cost') }}</label>
                                <input type="number" name="cost" class="form-control "
                                    placeholder="{{ trans('Enter cost') }} " step="any" min="0"
                                    value="{{ old('cost', $item->cost ?? null) }}">
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
            $('#sub_category_id,#category_id').empty();
            // When the category changes
            $('#type').change(function() {
                $('#desc-div,#vars-div,#quantity-div,#price-div,#cost-div').hide();

                var type        = $(this).val();
                var $Category   = $('#category_id');
                if(type =="clothes"){
                    $('#images-div,#vars-div').show();
                }else  if(type =="sales"){
                    $('#desc-div,#price-div,#cost-div,#quantity-div').show();

                }else  if(type =="services"){
                    $('#price-div,#cost-div').show();

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
                $Category.trigger('change');

            });
            $('#category_id').change(function() {
                var category_id = $(this).val();
                var $subCategory = $('#sub_category_id');
                // Clear the current options
                $subCategory.empty();

                // Add only the options that belong to the selected category
                allSubCategories.each(function() {
                    if ($(this).data('parent-id') == category_id) {
                        $subCategory.append($(this).clone()); // Add matching options
                    }

                });

                // Trigger Select2 to update the dropdown
                $subCategory.trigger('change');

            });
            $(document).on("click", "#addToRow", function (e) {
                e.preventDefault()
                let subCategory     = $("tfoot select[name='sub_category_id']").val();
                let subCategoryText = $("tfoot select[name='sub_category_id'] option:selected").text();
                let sku             = $("tfoot input[name='sku']").val();
                let price           = $("tfoot input[name='price']").val();
                let cost            = $("tfoot input[name='cost']").val();
                let data            = {sub_category_id:subCategory,sku:sku,price:price,cost:cost}
                if (sku && price) {
                    let newRow = `<tr data-data='${JSON.stringify(data)}'>
                        <td>${subCategoryText}</td>
                        <td>${sku}</td>
                        <td>${price}</td>
                        <td>${cost}</td>
                        <td>
                            <button class="btn btn-danger btn-delete">Delete</button>
                        </td>
                    </tr>`;
                    
                    $("tbody").append(newRow);
                    
                    // Clear input fields
                    $("tfoot input[name='sku']").val("");
                    $("tfoot input[name='price']").val("");
                    $("tfoot input[name='cost']").val("");
                } else {
                    toastr.error("{{ trans('Please enter SKU and price and cost.') }}");
                }
            });
            
            // Function to delete a row
            $(document).on("click", ".btn-delete", function () {
                $(this).closest("tr").remove();
            });
            $('#type').change();
        });
    </script>
@endpush
