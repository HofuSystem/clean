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
                        <li class="breadcrumb-item text-muted">@lang('orders')</li>
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
                        <div class="card-body ">
                            <div class="row" id="basic-table">
                                <div class="col-12">

                                    <div class="row match-height">
                                        <div class="col-xl-6 col-md-6 col-sm-12 profile-card-1">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="card-body text-center">
                                                        <label class="col-form-label">{{ trans('order details') }}</label>


                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('coupons') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <select class="form-control advance-select"
                                                                    id="coupon_id" name="coupon_id">
                                                                    <option value="">{{ trans('coupon') }}</option>
                                                                    @foreach ($coupons as $coupon)
                                                                        <option value="{{ $coupon->id }}">
                                                                            {{ $coupon->code }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('type') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <select class="form-control advance-select"
                                                                    id="type" name="type">
                                                                    <option value="clothes">{{ trans('clothes') }}</option>
                                                                    <option value="sales">{{ trans('sales') }}</option>
                                                                    <option value="services">{{ trans('services') }}
                                                                    </option>
                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('total price') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <input id="total_price" name="total_price" type="text"
                                                                    value="" class="form-control" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('payment method') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <select class="form-control advance-select"
                                                                    id="pay_type" name="pay_type">
                                                                    <option value="cash">{{ trans('cash') }}</option>
                                                                    <option value="card">{{ trans('card') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('tax') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <input id="tax_persent" name="tax_persent" type="text"
                                                                    value="" class="form-control">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('customer notes') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <textarea name="desc" class="form-control" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-12 profile-card-1">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="card-body text-center">
                                                        <label class="col-form-label"> {{ trans('client details') }}
                                                        </label>
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('client name') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <select class="form-control advance-select"
                                                                    id="client_id" name="client_id">
                                                                    <option value="">{{ trans('client') }}</option>
                                                                    @foreach ($clients as $client)
                                                                        <option @selected(isset($cart) and $cart->user_id == $client->id) data-wallet="{{ $client->wallet }}" value="{{ $client->id }}"> {{ $client->fullname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('city') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <select class="form-control advance-select"
                                                                    id="city_id" name="city_id">
                                                                    <option value="">{{ trans('city') }}</option>
                                                                    @foreach ($cities as $city)
                                                                        <option @selected(isset($cart) and $cart?->user?->profile?->city_id == $city->id) value="{{ $city->id }}">
                                                                            {{ $city->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('district') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <select class="form-control advance-select"
                                                                    id="district_id" name="district_id">

                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="form-group row form-check form-switch">
                                                            <label class="form-check-label text-start" for="wallet_used">
                                                                {{ trans('is wallet used') }}
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="wallet_used" name="wallet_used">
                                                            </label>


                                                        </div>

                                                        <div class="form-group row" id="wallet-amount">
                                                            <label
                                                                class="col-form-label col-md-3">{{ trans('wallet balance') }}</label>
                                                            <div class="col-md-9 position-relative has-icon-left">
                                                                <input id="wallet_balacance" name="wallet_balacance"
                                                                    type="text" value="" class="form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="m-1 p-2 w-100 btn btn-primary"
                                            id="create-order">{{ trans('add new') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="basic-table">
                                <div class="col-12">
                                    <div class="card-header pb-0 px-2">
                                        <h6 class="card-title">{{ trans('products') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="products-table">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th>{{ trans('product name') }}</th>
                                                        <th>{{ trans('SKU') }}</th>
                                                        <th>{{ trans('category') }}</th>
                                                        <th>{{ trans('sub category') }}</th>
                                                        <th>{{ trans('price') }}</th>
                                                        <th>{{ trans('quantity') }}</th>
                                                        <th>{{ trans('size') }}</th>
                                                        <th>{{ trans('total') }}</th>
                                                        <th>{{ trans('delete') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                 
                                                        <th colspan="4"></th>
                                                        <th>{{ trans('total quantity') }}</th>
                                                        <th><span id="total-qty"></span> </th>
                                                        <th>{{ trans('total price') }}</th>
                                                        <th> <span id="total-price">0.00</span> {{ trans('SAR') }}</th>
                                                     
                                                        <th>
                                                            {!! trans('add') !!}
                                                            <a data-toggle="modal" class="btn-operation open-modal-add"
                                                                title="{!! trans('add') !!}" href="#addItem">
                                                                <i class="fas fa-plus-square "></i>
                                                            </a>
                                                        </th>

                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <!-- Basic modal -->
                            <div class="modal fade text-left" id="addItem"  role="dialog"
                                aria-labelledby="myModalLabel120" aria-hidden="true">
                                <div class="modal-dialog  modal-fullscreen modal-dialog-centered modal-dialog-scrollable"
                                    role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary ">
                                            <h5 class="modal-title text-white" id="myModalLabel120">
                                                {{ trans('add product to order') }}
                                            </h5>
                                            <button type="button" class="btn btn-danger close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="card row">
                                            <div class="card-body row" id="filter-products">
                                                <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h4 class="card-title">{{ trans('cart') }}</h4>
                                                            <div id="cart-items"></div>
                                                            <button type="submit" id="saveCart"
                                                                class="btn btn-primary mx-auto">{{ trans('save cart') }}</button>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-xl-9 col-lg-8 col-md-6 mb-2">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h4 class="card-title">{{ trans('add') }}</h4>
                                                        </div>
                                                        <div class="col-12 ">
                                                            <label
                                                                for="search">{{ trans('search for the product') }}</label>
                                                            <input type="text" class="form-control" name="search"
                                                                id="search">
                                                        </div>
                                                        <div class="form-group mt-2 col-6 select-parent">
                                                            <label for="">{{ trans('choose a category') }}</label>
                                                            <select class="advance-select form-control"
                                                                name="category_id" aria-label="Default select example"
                                                                required>
                                                                <option value="">{{ trans('choose a category') }}
                                                                </option>
                                                                @foreach ($categories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group mt-2  col-6 select-parent">
                                                            <label for="">{{ trans('sub category') }}</label>

                                                            <select class="advance-select form-control"
                                                                name="sub_category_id" aria-label="Default select example"
                                                                required >
                                                                <option value="">{{ trans('sub category') }}
                                                                </option>
                                                            </select>
                                                        </div>

                                                    </div>
                                                    <div id="search-results" class="row">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Basic modal -->
                            <div class="modal fade text-left" id="updateQty"  role="dialog"
                                aria-labelledby="myModalLabel120" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
                                    role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title text-white" id="myModalLabel120">
                                                {{ trans('view the history and update of the qty') }}</h5>
                                            <button type="button" class="btn btn-danger close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="card row">
                                            <div class="card-body row">
                                                <div class="form-group col-12">
                                                    <label for="new_qty">{{ trans('new_qty') }}</label>
                                                    <input type="text" name="new_qty" id="new_qty"
                                                        class="form-control" placeholder="" aria-describedby="helpId">
                                                </div>
                                                <div class="form-group col-12">
                                                    <button class="btn btn-primary"
                                                        id="updateQtyBtn">{{ trans('update_qty') }}</button>
                                                </div>


                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Basic modal -->
                            <div class="modal fade text-left" id="updateSize"  role="dialog"
                                aria-labelledby="myModalLabel120" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
                                    role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="myModalLabel120">{{ trans('update size') }}
                                            </h5>
                                            <button type="button" class="btn btn-danger close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="card row">
                                            <div class="card-body row">
                                                <div class="form-group col-6">
                                                    <label for="new_width">{{ trans('new width in meter') }}</label>
                                                    <input type="text" name="new_width" id="new_width"
                                                        class="form-control" placeholder="" aria-describedby="helpId">
                                                </div>
                                                <div class="form-group col-6">
                                                    <label for="new_height">{{ trans('new height in meters') }}</label>
                                                    <input type="text" name="new_height" id="new_height"
                                                        class="form-control" placeholder="" aria-describedby="helpId">
                                                </div>
                                             
                                                <div class="form-group col-12">
                                                    <button class="btn btn-primary"
                                                        id="updateSizeBtn">{{ trans('update size') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.orders.index') }}" class="btn btn-secondary text-white"><i
                                    class="fas fa-arrow-left"></i> {{ trans('Back') }}</a>
                        </div>
                    </div>


                
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
    <link href="{{ asset('control') }}/js/custom/crud/show.css" rel="stylesheet" type="text/css" />
    <style>
        thead th{
    font-size:12px !important
}
        #search-results {
            height: 70vh;
            overflow-x: hidden;
        }

        #cart-items {
            height: 70vh;
            overflow-x: hidden;
        }

        img.card-img-top {
            width: 150px;
            height: 100px;
            object-fit: cover
        }


        @media screen and (max-width: 480px) {
            img.card-img-top {
                width: 100px;
                height: 70px;
                object-fit: cover
            }

            .card .card-title {
                font-size: 1rem;
                margin-bottom: 1rem;
            }

            .input-group-prepend,
            .input-group-append {
                font-size: 10px;
            }

            .input-group-prepend button,
            .input-group-append button {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px;
            }

            .input-group-prepend input {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px !important;
            }

            #search-results {
                height: 240px;
                overflow-x: hidden;
            }

            #cart-items {
                height: 200px;
                overflow-x: hidden;
            }

            #addItem .card {
                padding: 0px 5px !important
            }

            #addItem .card-body {
                padding: 5px 0px !important
            }

            #addItem .card p {
                margin: 0px 5px !important
            }

            #saveCart {
                margin-top: 15px
            }

            .select-parent {
                margin: 5px 0px
            }
        }


        @media screen and (max-height: 700px) {
            img.card-img-top {
                width: 70px;
                height: 50px;
                object-fit: cover
            }

            .card>* {
                font-size: 0.8rem !important;
            }

            .input-group-prepend,
            .input-group-append {
                font-size: 10px;
            }

            .input-group-prepend button,
            .input-group-append button {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px;
            }

            .input-group-prepend input {
                font-size: 10px;
                margin: 0;
                padding: 5px 10px !important;
            }

            #search-results {
                height: 150px;
                overflow-x: hidden;
            }

            #cart-items {
                height: 140px;
                overflow-x: hidden;
            }

            #addItem .card {
                padding: 0px 5px !important
            }

            #addItem .card-body {
                padding: 5px 0px !important
            }

            #addItem .card p {
                margin: 0px 5px !important
            }

            #saveCart {
                margin-top: 15px
            }

            .select-parent {
                margin: 5px 0px
            }
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
    <script>
        $(document).ready(function() {
            $('.advance-select').each(function (index, element) {
                let id = $(this).attr('id');
                if ($(this).parents('.modal').length) {
                let parentId = $(this).parents('.modal').first().attr('id');
                $(this).select2({
                    width: '100%',
                    height: '40px',
                    placeholder: 'select..',
                    dropdownParent: $('#' + parentId)
                });
                } else {

                $(this).select2({
                    width: '100%',
                    height: '40px',
                    placeholder: 'select..'
                });
                }
            });
            
            let allProducts         = {!! json_encode($products) !!};
            let allSubCategories    = {!! json_encode($subCategories) !!};
            let allDistricts        = {!! json_encode($districts) !!};
            let hasSize             = {!! json_encode($hasSize) !!};
            // program to generate random strings

            // declare all characters
            const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            function generateString(length) {
                let result = ' ';
                const charactersLength = characters.length;
                for ( let i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }

                return result;
            }
            @isset($cartItems)
                localStorage.setItem('cartItems',JSON.stringify({!! json_encode($cartItems) !!}))
            @endisset
            console.log(cartItems);
            
            var cartItems           = localStorage.getItem('cartItems');
            
            if (cartItems) {
                darwTable();
                drawCartItems();
            }
            else {
                localStorage.setItem("cartItems", null);
            }
            $('#wallet-amount').hide();
            $('[name="wallet_used"]').change(function(e) {
                e.preventDefault();
                if ($(this).is(":checked")) {
                    $('#wallet-amount').show();
                } else {
                    $('#wallet-amount').hide();

                }
            });
            $(document).on("click", ".open-modal-add", function(e) {
                e.preventDefault()
                var orderId = $(this).data('id');
                drawCartItems();
                $(".modal-body #order_id").val(orderId);
                $('#addItem').modal('show');
            });
            $(document).on("click", ".btn#increase", function () {
                let $input = $(this).closest(".input-group").find("input[name='quantity']");
                let currentValue = parseInt($input.val(), 10) || 0;
                let step = parseInt($input.attr("step"), 10) || 1;
                $input.val(currentValue + step).trigger('change');
            });

            $(document).on("click", ".btn#decrease", function () {
                let $input = $(this).closest(".input-group").find("input[name='quantity']");
                let currentValue = parseInt($input.val(), 10) || 0;
                let step = parseInt($input.attr("step"), 10) || 1;
                let min = parseInt($input.attr("min"), 10) || 0;
                $input.val(Math.max(currentValue - step, min)).trigger('change');
            });

            function loadProducts() {
                $("#search-results").html("");

                products = allProducts.filter(function(product) {
                    let title = $('#filter-products [name=search]').val()
                    title = (title.length && product.name.includes(title))


                    let category_id = $('#filter-products [name=category_id]').val()
                    let category = product.category_id == category_id;

                    let sub_category_id = $('#filter-products [name=sub_category_id]').val()
                    let sub_category = true
                    if (sub_category_id.length) {
                        sub_category = (product.sub_category_id == sub_category_id) ? true : false
                    }

                    return (title || (category && sub_category))
                })

                products.forEach(product => {
                    product = drawProduct(product);
                    $("#search-results").append(product);
                });

            }

            function loadSubCategories() {
                $('#filter-products [name=sub_category_id]').empty();
                $('#filter-products [name=sub_category_id]').append(
                    '<option value="">{{ trans('sub_category') }}</option>');
                
                subCategories = allSubCategories.filter(function(item) {                    
                    return (item.parent_id == $('#filter-products [name=category_id]').val())
                })
                

                if (subCategories.length) {
                    subCategories.forEach(item => {
                        let newOption = $('<option>', {
                            value: item.id ,
                            text: item.name 
                        });
                        $('#filter-products [name=sub_category_id]').append(newOption).trigger('change');
                    });
                }

            }

            function drawProduct(product) {
                if(product.prices){
                    let cityId = $('#city_id').val();
                    let price = product.prices.find(price => price.city_id == cityId);
                    if(price){
                        product.price = price.price;
                    }
                }
                let sizeHtml ="";
                
                if(hasSize.includes(product.category_id) || hasSize.includes(product.sub_category_id)){
                    sizeHtml    = `
                    <div class="row calculator my-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ trans("Height") }}</label>
                            <input name="height" type="number" class="form-control height" placeholder="Enter height" value="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Width</label>
                            <input name="width" type="number" class="form-control width" placeholder="Enter width">
                        </div>
                     
                    </div>
                    `;
                }
                let category = (product.category) ?
                    `<span class="badge bg-success">${product.category}</span>` :
                    "";
                let subCategory = (product.sub_category) ?
                    `<span class="badge bg-warning text-dark">${product.sub_category}</span>` : "";
                return `
                <div class="card p-2 col-xl-3 col-lg-4 col-md-4 col-6">
                    <img class="card-img-top mx-auto"
                        src="${product.image}"
                        alt="Card image cap">
                    <div class="card-body row ">
                        <div class="col-12">
                            <h5 class="card-title">${product.name}</h5>
                            <p>${product.price} {{ trans('SAR') }}</p>
                            ${category}
                            ${subCategory}
                        </div>
                        <div class="col-12 mt-2">
                            <form  class="add-to-cart-form" action="#" method="post">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-secondary text-white" type="button" id="increase"
                                                >
                                                <i class="fas fa-plus-square"></i>
                                            </button>
                                        </div>
                                        <input type="number" id="quantity" class="text-center form-control"
                                            aria-live="polite" data-bs-step="counter" name="quantity"
                                            title="quantity" value="1" min="0" step="1"
                                            data-bs-round="0" aria-label="Quantity selector">
                                        <div class="input-group-append" id="button-addon4">
                                            <button class="btn btn-secondary text-white" type="button" id="decrease"
                                                >
                                                <i class="fas fa-minus-square"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <input type="number" id="item_id" name="item_id" value="${product.id}" hidden>
                                ${sizeHtml}
                                <button type="submit" class="btn btn-primary mx-auto">{{ trans('add') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            }

            function drawCartItems() {
                let cartItems   = localStorage.getItem("cartItems");                
                cartItems       = JSON.parse(cartItems);
                $('#cart-items').html("");
                for (const property in cartItems) {
                    let cartItem    = cartItems[property];
                    cartItem        = drawCartItem(cartItem)
                    $('#cart-items').append(cartItem);

                }
            }

            function drawCartItem(cartItem) {
                let category = (cartItem.product.category) ?
                    `<span class="badge bg-success">${cartItem.product.category}</span>` :
                    "";
                let subCategory = (cartItem.product.sub_category) ?
                    `<span class="badge bg-warning text-dark">${cartItem.product.sub_category}</span>` : "";
                let id          = cartItem.product.id;
                let cartItemId  = cartItem.cartItemId;
                let name        = cartItem.product.name;
                let price       = cartItem.product.price;
                let image       = cartItem.product.image;
                let qty         = cartItem.qty;
                let originalProduct = allProducts.find(product => product.id == id);   
                let cityId          = $('#city_id').val();
                let originalPrice = originalProduct.prices.find(price => price.city_id == cityId);
                if(originalPrice && originalPrice.price){
                    price = originalPrice.price;
                }else{
                    price = originalProduct.price;
                }
                
                let size     = "";
                let sizeHtml = "";
                
                if(hasSize.includes(cartItem.product.category_id) || hasSize.includes(cartItem.product.sub_category_id)){
                    size        = ` ${cartItem.height} X ${cartItem.width} = `+(parseFloat(cartItem.width) * parseFloat(cartItem.height) ) + " M" ;
          
                }

                return `
                        <div class="card p-2 col-12 cart-item">
                            <img class="card-img-top mx-auto"
                                src="${image}"
                                alt="Card image cap">
                            <div class="card-body row ">
                                <div class="col-12">
                                    <h5 class="card-title">${name}</h5>
                                    <p>unit price ${price} {{ trans('SAR') }}</p>
                                    <p>size : ${size}</p>
                                    ${category}
                                    ${subCategory}
                                </div>
                                ${sizeHtml}                                
                                <div class="col-12 mt-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-secondary text-white" type="button" id="increase"
                                                        data-id="${cartItemId}"
                                                       >
                                                        <i class="fas fa-plus-square"></i>
                                                    </button>
                                                </div>

                                                <input value="${qty}" type="number" id="quantity" class="text-center form-control"
                                                    aria-live="polite" data-bs-step="counter" name="quantity"
                                                    title="quantity" value="1" min="0" step="1"
                                                    data-bs-round="0" aria-label="Quantity selector"
                                                    data-id="${cartItemId}"
                                                    >

                                                <div class="input-group-append" id="button-addon4">
                                                    <button class="btn btn-secondary text-white" type="button" id="decrease"
                                                        data-id="${cartItemId}"
                                                       >
                                                        <i class="fas fa-minus-square"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <button data-id="${cartItemId}" type="submit" class="btn btn-danger mx-auto cart-delete mt-1">{{ trans('delete') }}</button>
                                        </div>
                                </div>
                            </div>
                        </div>
                        `;
            }

            function convertFormToJsObject(form) {
                const formData = new FormData(form);
                const formObject = {};
                for (const [key, value] of formData.entries()) {
                    formObject[key] = value;
                }
                return formObject;
            }
            $(document).on('click','.delete-cart-item',function (e) {
                let itemsId     = $(this).data('id');
                
                let cartItems   = localStorage.getItem('cartItems');
                cartItems       = JSON.parse(cartItems)
                delete cartItems[itemsId]
                cartItems       = JSON.stringify(Object.assign({}, cartItems));
                localStorage.setItem("cartItems", cartItems);
                drawCartItems()
                darwTable()
            });
       
            function calculateSize(row) {
                let height = parseFloat(row.find('.height').val()) || 0;
                let width = parseFloat(row.find('.width').val()) || 0;
                let size = height * width;
                row.find('.size').val(size);
            }

            $(document).on('input', '.height, .width', function(){
                let row = $(this).closest('.calculator');
                calculateSize(row);
            });

            $(document).on('input', '#filter-products [name=search]', function() {
                let value = $(this).val();
                if (value.length) {
                    $('.select-parent').hide(500);
                    $('.select-parent select').val("").trigger('change');
                } else {
                    $('.select-parent').show(500);
                }
                loadProducts();
            });
            $(document).on('change', '#filter-products [name=sub_category_id]', function() {
                loadProducts();
            });
            $(document).on('change', '#filter-products [name=category_id]', function() {
                loadSubCategories();
                loadProducts();
            });
            $(document).on('submit', '.add-to-cart-form', function(e) {
                e.preventDefault();
                let cartItemId  = null;
                let data        = convertFormToJsObject($(this)[0]);
                let url         = $(this).attr('action');
                let itemsId     = $(this).find('#item_id').val();
                let qty         = $(this).find('#quantity').val();
                let width       = $(this).find('[name=width]').val();
                let height      = $(this).find('[name=height]').val();
                
                let cartItems   = localStorage.getItem('cartItems');
                
                if(cartItems != null){
                    cartItems   = JSON.parse(cartItems);
                }
                if(cartItems == null){
                    cartItems   = {};
                }
                
                let cartItem    = null;
                if (typeof cartItems === "object" ){
                    for (const property in cartItems) {
                        let item = cartItems[property];
                        if(item.product.id == itemsId && item.width == width && item.height == height){
                            cartItem = item;
                        }
                    }
                }
               

                let product = allProducts.filter(function(product) {
                    return (product.id == itemsId);
                })[0];
                
                if (cartItem) {
                    cartItem.qty    = (parseInt(cartItem.qty) + parseInt(qty));
                    cartItemId      = cartItem.cartItemId;
                } else if (product) {
                    cartItemId          = generateString(15);
                    cartItem            = {};
                    cartItem.cartItemId = cartItemId;
                    cartItem.product    = product
                    cartItem.qty        = qty
                    cartItem.width      = width
                    cartItem.height     = height
                }
                
                if (cartItem) {
                    cartItems[cartItemId]   = cartItem;
                    cartItems               = JSON.stringify(Object.assign({}, cartItems));
                    localStorage.setItem("cartItems", cartItems);
                    drawCartItems()
                }


            });
            $(document).on('change', '.cart-item [name=quantity]', function(e) {
                e.preventDefault();
                let value           = $(this).val();
                
                if(value > 0){
                    let itemsId         = $(this).data('id');
                    let cartItems       = localStorage.getItem('cartItems');
                    cartItems           = JSON.parse(cartItems);
                    cartItem            = cartItems[itemsId];
                    cartItem.qty        = $(this).val();
                    cartItems[itemsId]  = cartItem;
                    cartItems           = JSON.stringify(Object.assign({}, cartItems));
                    localStorage.setItem("cartItems", cartItems);

                }


            });
            $(document).on('click', '.cart-delete', function(e) {
                e.preventDefault();
                let id          =  $(this).data('id');
                let cartItems   = localStorage.getItem("cartItems");
                cartItems       = JSON.parse(cartItems);
                delete cartItems[id];
                cartItems   = JSON.stringify(Object.assign({}, cartItems));
                localStorage.setItem("cartItems", cartItems);
                drawCartItems();
            });
            $(document).on('click', '#saveCart', function(e) {
                e.preventDefault();
                darwTable()


            });

            function darwTable() {
                let cartItems = localStorage.getItem("cartItems");
                cartItems = JSON.parse(cartItems);
                $('#products-table tbody').html("");
                let totatlPrice = 0;
                let totatlQty = 0;
                for (const property in cartItems) {
                    let cartItem    = cartItems[property];
                    let size        = (cartItem.width  && cartItem.height) ? (parseFloat(cartItem.width) * parseFloat(cartItem.height)) : 1; 
                    totatlPrice     += (parseFloat(cartItem.qty) *size* parseFloat(cartItem.product.price));
                    totatlQty       += parseInt(cartItem.qty);
                    cartItem        = drawTableRaw(cartItem)
                    $('#products-table tbody').append(cartItem);
                    $("#addItem").modal('hide');
                    $('#filter-products select,#filter-products input').val(null).trigger('change');
                }
                $('#total-price').html(totatlPrice)
                $('#total_price').val(totatlPrice)
                $('#total-qty').html(totatlQty)
            }

            function drawTableRaw(cartItem) {
                let width       = cartItem.width;
                let height      = cartItem.height;
                let size        = (width  && height) ? (parseFloat(width) * parseFloat(height)) : 1; 
                let sizeHtml    = "";
                if(hasSize.includes(cartItem.product.category_id) || hasSize.includes(cartItem.product.sub_category_id)){
                    sizeHtml    = `
                   <span class="edit-size btn" 
                        data-id="${cartItem.cartItemId}"
                        data-width="${cartItem.width}"
                        data-height="${cartItem.height}">
                        ${cartItem.width} X ${cartItem.height} =   ${parseFloat(cartItem.width) * parseFloat(cartItem.height)}M
                        <i class="fas fa-edit text-success"></i>
                    </span>
                    `;
                }
                let originalProduct = allProducts.find(product => product.id == cartItem.product.id);   
                let cityId          = $('#city_id').val();
                let originalPrice = originalProduct.prices.find(price => price.city_id == cityId);
                if(originalPrice && originalPrice.price){
                    cartItem.product.price = originalPrice.price;
                }else{
                    cartItem.product.price = originalProduct.price;
                }

                return `
            <tr>
                <td>${cartItem.product.name}</td>
                <td>${cartItem.product.sku}</td>
                <td>${cartItem.product.category}</td>
                <td>${cartItem.product.sub_category}</td>
                <td>${cartItem.product.price}</td>
                <td>
                    <span class="edit-quantity text-success"
                        data-id="${cartItem.cartItemId}"
                        data-quantity="${cartItem.qty}">
                        ${cartItem.qty}
                        <i class="fas fa-edit"></i>
                    </span>
                </td>
                <td>
                ${sizeHtml}
                </td>
                <td>${(parseFloat(cartItem.qty) * parseFloat(size) * parseFloat(cartItem.product.price))}</td>
                <td>
                    <a  data-id="${cartItem.cartItemId}"
                        class="delete-cart-item btn-operation" title="{!! trans('delete') !!}">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>

        `;
            }
            //cart controle 
            $('#city_id').change(function(e) {
                e.preventDefault();
                drawCartItems();
                darwTable();
                $('#district_id').empty();
                $('#district_id').append(
                    '<option value="">{{ trans('district') }}</option>');

                districts = allDistricts.filter(function(item) {
                    return (item.city_id == $('#city_id').val())
                })

                if (districts.length) {
                    districts.forEach(item => {
                        $('#district_id').append('<option value="' + item
                            .id + '">' + item.name + '</option>');
                    });
                }

            });
            $('#client_id').change(function(e) {
                e.preventDefault();
                let wallet = $(this).find('option:selected').data('wallet');
                $('#wallet_balacance').val(wallet)
            });
            //update order qty
            $(document).on('click', '.edit-quantity', function(e) {
                e.preventDefault();
                $('#updates-table tbody').html("");
                let id = $(this).data('id');
                $('#updateQtyBtn').data('id', id);
                let quantity = $(this).data('quantity');
                $('[name=new_qty]').val(quantity);
                $("#updateQty").modal('show');
            });
            $(document).on('click', '#updateQtyBtn', function(e) {
                e.preventDefault();
                let itemsId = $(this).data('id');
                let cartItems = localStorage.getItem('cartItems');
                cartItems = JSON.parse(cartItems);
                cartItem = cartItems[itemsId];
                cartItem.qty = $('#new_qty').val();
                cartItems[itemsId] = cartItem;
                cartItems = JSON.stringify(Object.assign({}, cartItems));
                localStorage.setItem("cartItems", cartItems);
                $("#updateQty").modal('hide');
                drawCartItems()
                darwTable()

            });
          
            //update order qty
            $(document).on('click', '.edit-size', function(e) {
                e.preventDefault();
                $('#updates-table tbody').html("");
                let id = $(this).data('id');
                $('#updateSizeBtn').data('id', id);

                let height = $(this).data('height');
                let width = $(this).data('width');
                $('[name=new_height]').val(height);
                $('[name=new_width]').val(width);
                $("#updateSize").modal('show');
            });
            $(document).on('keyup', '#new_height,#new_width', function(e) {
                e.preventDefault();
                let size = $('[name=new_size]').val();
                let width = parseFloat($('#new_width').val())
                let height = parseFloat($('#new_height').val())
                if (width && height) {
                    size = width * height;
                    $('[name=new_size]').val(size)
                }
            });

            $(document).on('click', '#updateSizeBtn', function(e) {
                e.preventDefault();
                let itemsId         =  $(this).data('id');
                let cartItems       = localStorage.getItem('cartItems');
                cartItems           = JSON.parse(cartItems);
                cartItem            = cartItems[itemsId];
                cartItem.width      = $('#new_width').val();
                cartItem.height     = $('#new_height').val();
                cartItems[itemsId]  = cartItem;
                cartItems           = JSON.stringify(Object.assign({}, cartItems));
                localStorage.setItem("cartItems", cartItems);
                $("#updateSize").modal('hide');
                drawCartItems()
                darwTable()

            });
            $('#create-order').click(function(e) {
                e.preventDefault();
                let data = {};
                let items = [];
                let cartItems = localStorage.getItem("cartItems");
                cartItems = JSON.parse(cartItems);

                for (const property in cartItems) {
                    let cartItem = cartItems[property];
                    let item = {
                        product_id  : cartItem.product.id,
                        quantity    : cartItem.qty,
                        width       : cartItem.width,
                        height      : cartItem.height,
                    }
                    items.push(item)
                }

                data._token = "{{ csrf_token() }}";
                data.items = items;
                data.coupon_id = $('#coupon_id').val();
                data.type = $('#type').val();
                data.pay_type = $('#pay_type').val();
                data.desc = $('[name="desc"]').val();
                data.client_id = $('#client_id').val();
                data.city_id = $('#city_id').val();
                data.district_id = $('#district_id').val();
                data.wallet_used = $('#wallet_used').is(":checked") ? 1 : 0;
                $.ajax({
                    type: "POST",
                    url: "{{ route('dashboard.orders.create') }}",
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function(result) {
                            localStorage.setItem("cartItems",null);
                            document.location.href = response.url;
                        })
                    },
                    error: function(response) {
                        response = response.responseJSON;
                        toastr.error(response.message);
                        errors = response.errors;
                        for (const key in errors) {
                            if (Object.prototype.hasOwnProperty.call(errors, key)) {
                                const errorsMessages = errors[key];
                                $.each(errorsMessages, function(indexInArray, errorsMessage) {
                                    toastr.error(errorsMessage, key);

                                });
                            }
                        }

                    }
                });


            });

            $('#city_id').trigger('change')
            $('#client_id').trigger('change')
            @if (isset($cart) and $cart?->user?->profile?->district_id)
                $('#district_id').val('{{ $cart?->user?->profile?->district_id }}').change()
            @endif

        });
    </script>
@endpush
