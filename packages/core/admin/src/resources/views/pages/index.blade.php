@extends('admin::layouts.dashboard')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <div class="card border border-dark">
                <div class="card-body py-1 px-0">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all"
                                aria-selected="true">{{ trans('all') }}</button>
                        </li>
                        @foreach ($pendingTypes as $type)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link " id="pills-{{ $type }}-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-{{ $type }}" type="button" role="tab"
                                    aria-controls="pills-{{ $type }}"
                                    aria-selected="true">{{ trans($type) }}</button>
                            </li>
                        @endforeach
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="pills-testing-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-testing" type="button" role="tab" aria-controls="pills-testing"
                                aria-selected="true">{{ trans('testing') }}</button>
                        </li>
                    </ul>
                    <div class="tab-content p-0" id="pills-tabContent">

                        <div class="tab-pane fade " id="pills-testing" role="tabpanel" aria-labelledby="pills-testing-tab"
                            tabindex="0">
                            <div class="mt-1">
                                <h3 class="text-dark">{{ trans('testing orders') }}</h3>
                                <div class="table-responsive">
                                    <!--begin::Table-->

                                    <table class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                        data-load="{{ route('dashboard.orders.index', ['filters' => ['status' => 'pending', 'testAccount' => 'testAccount']]) }}">
                                        <!--begin::Table head-->
                                        <thead class="table-primary">
                                            <!--begin::Table row-->
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                                <th class="orderable text-center" data-name="created_at">
                                                    @lang('added at')</th>
                                                <th class="orderable text-center" data-name="reference_id">
                                                    @lang('order id')</th>
                                                <th class="orderable text-center" data-name="type">
                                                    @lang('type')</th>
                                                <th class="orderable text-center" data-name="client_id">
                                                    @lang('client')</th>
                                                <th class="orderable text-center" data-name="phone">
                                                    @lang('phone')</th>

                                                <th class="orderable text-center" data-name="total_price">
                                                    @lang('total price')
                                                </th>
                                                <th class="orderable text-center" data-name="pay_type">
                                                    @lang('payment method')</th>
                                                <th class="orderable text-center" data-name="city_id">
                                                    @lang('city')</th>
                                                <th class="orderable text-center" data-name="district_id">
                                                    @lang('district')</th>

                                                <th class=" text-center py-2" data-name="actions">
                                                    @lang('actions')</th>

                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="pills-all" role="tabpanel"
                            aria-labelledby="pills-all-tab" tabindex="0">
                            <div class="mt-1">
                                <h3 class="text-dark">{{ trans('all orders') }}</h3>
                                <div class="table-responsive">
                                    <!--begin::Table-->

                                    <table class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                        data-load="{{ route('dashboard.orders.index', ['filters' => ['status' => 'pending']]) }}">
                                        <!--begin::Table head-->
                                        <thead class="table-primary">
                                            <!--begin::Table row-->
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                                <th class="orderable text-center" data-name="created_at">
                                                    @lang('added at')</th>
                                                <th class="orderable text-center" data-name="reference_id">
                                                    @lang('order id')</th>
                                                <th class="orderable text-center" data-name="type">
                                                    @lang('type')</th>
                                                <th class="orderable text-center" data-name="client_id">
                                                    @lang('client')</th>
                                                <th class="orderable text-center" data-name="phone">
                                                    @lang('phone')</th>
                                                    <th class=" text-center py-2" data-name="receiver_date">
                                                        @lang('receiver date')</th>
                                                    <th class=" text-center py-2" data-name="receiver_time">
                                                        @lang('receiver time')</th>

                                                    <th class=" text-center py-2" data-name="delivery_date">
                                                        @lang('delivery date')</th>
                                                    <th class=" text-center py-2" data-name="delivery_time">
                                                        @lang('delivery time')</th>
                                                <th class="orderable text-center" data-name="total_price">
                                                    @lang('total price')
                                                </th>
                                                <th class="orderable text-center" data-name="pay_type">
                                                    @lang('payment method')</th>
                                                <th class="orderable text-center" data-name="city_id">
                                                    @lang('city')</th>
                                                <th class="orderable text-center" data-name="district_id">
                                                    @lang('district')</th>

                                                <th class=" text-center py-2" data-name="actions">
                                                    @lang('actions')</th>

                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>
                        @foreach ($pendingTypes as $type)
                            <div class="tab-pane fade " id="pills-{{ $type }}" role="tabpanel"
                                aria-labelledby="pills-{{ $type }}-tab" tabindex="0">
                                <div class="mt-1">
                                    <h3 class="text-dark">{{ trans($type . ' orders') }}</h3>
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table
                                            class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                            data-load="{{ route('dashboard.orders.index', ['filters' => ['status' => 'pending', 'type' => $type]]) }}">
                                            <!--begin::Table head-->
                                            <thead class="table-primary">
                                                <!--begin::Table row-->
                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                                    <th class="orderable text-center" data-name="created_at">
                                                        @lang('added at')</th>
                                                    <th class="orderable text-center" data-name="reference_id">
                                                        @lang('order id')</th>
                                                    <th class="orderable text-center" data-name="client_id">
                                                        @lang('client')</th>
                                                    <th class="orderable text-center" data-name="phone">
                                                        @lang('phone')</th>
                                                    <th class=" text-center py-2" data-name="receiver_date">
                                                        @lang('receiver date')</th>
                                                    <th class=" text-center py-2" data-name="receiver_time">
                                                        @lang('receiver time')</th>

                                                    <th class=" text-center py-2" data-name="delivery_date">
                                                        @lang('delivery date')</th>
                                                    <th class=" text-center py-2" data-name="delivery_time">
                                                        @lang('delivery time')</th>
                                                    <th class="orderable text-center" data-name="total_price">
                                                        @lang('total price')
                                                    </th>
                                                    <th class="orderable text-center" data-name="pay_type">
                                                        @lang('payment method')</th>
                                                    <th class="orderable text-center" data-name="city_id">
                                                        @lang('city')</th>
                                                    <th class="orderable text-center" data-name="district_id">
                                                        @lang('district')</th>

                                                    <th class=" text-center py-2" data-name="actions">
                                                        @lang('actions')</th>

                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="text-gray-600 fw-bold">

                                            </tbody>
                                            <!--end::Table body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            <div class="card border border-dark py-2 mt-3">
                <div class="card-body py-1 px-0">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-clothes-received-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-clothes-received" type="button" role="tab"
                                aria-controls="pills-clothes-received"
                                aria-selected="true">{{ trans('col.received') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="pills-clothes-canceled-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-clothes-canceled" type="button" role="tab"
                                aria-controls="pills-clothes-canceled"
                                aria-selected="true">{{ trans('col.canceled') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-delivered-orders-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-delivered-orders" type="button" role="tab"
                                aria-controls="pills-delivered-orders"
                                aria-selected="false">{{ trans('col.delivered') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-payment-orders-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-payment-orders" type="button" role="tab"
                                aria-controls="pills-payment-orders"
                                aria-selected="false">{{ trans('col.payment') }}</button>
                        </li>


                    </ul>
                    <div class="tab-content p-0" id="pills-tabContent">

                        <div class="tab-pane fade show active" id="pills-clothes-received" role="tabpanel"
                            aria-labelledby="pills-clothes-received-tab" tabindex="0">
                            <div class="mt-1">
                                <h3 class="text-dark">{{ trans('Received orders') }}</h3>
                                <div class="table-responsive">

                                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        <i class="fas fa-filter"></i>
                                        {{ trans('open filters of data') }}
                                    </button>
                                    <div class="row collapse" id="collapseOne" class="accordion-collapse collapse">
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('operator')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="operator_id" id="operator_id">
                                                <option value=""> @lang('select operator')</option>
                                                @foreach ($operators as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('operator_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('representative')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="representative_id" id="representative_id">
                                                <option value=""> @lang('select representative')</option>
                                                @foreach ($representatives as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('representative_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="reference_id"> @lang('Order Number') </label>
                                            <input type="text" name="reference_id" class="form-control filter-input"
                                                placeholder="@lang('search for order number') " value="{{ request('reference_id') }}">
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="type">@lang('type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="type" id="type">
                                                <option value=""> @lang('select type')</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}" @selected($type == request('type'))>
                                                        {{ trans($type) }}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-1">
                                            <label for="pay_type">@lang('pay type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="pay_type" id="pay_type">
                                                <option value=""> @lang('select pay_type')</option>
                                                <option value="cash" @selected('cash' == request('pay_type'))>{{ trans('cash') }}
                                                </option>
                                                <option value="card" @selected('card' == request('pay_type'))>{{ trans('card') }}
                                                </option>
                                                <option value="wallet" @selected('wallet' == request('pay_type'))>{{ trans('wallet') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="phone"> @lang('phone') </label>
                                            <input type="text" name="phone" class="form-control filter-input"
                                                placeholder="@lang('search for client phone') " value="{{ request('phone') }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="city_id">@lang('city')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="city_id" id="city_id">
                                                <option value=""> @lang('select city')</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}" @selected($city->id == request('city_id'))>
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!--begin::Table-->
                                    <table class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                        data-load="{{ route('dashboard.orders.index', ['filters' => ['status' => 'received']]) }}">
                                        <!--begin::Table head-->
                                        <thead class="table-primary">
                                            <!--begin::Table row-->
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="orderable text-center" data-name="created_at">
                                                    @lang('added at')</th>

                                                <th class="orderable text-center" data-name="reference_id">
                                                    @lang('order id')</th>
                                                <th class="orderable text-center" data-name="type">
                                                    @lang('type')</th>
                                                <th class="orderable text-center" data-name="client_id">
                                                    @lang('client')</th>
                                                <th class="orderable text-center" data-name="phone">
                                                    @lang('phone')</th>

                                                <th class="orderable text-center" data-name="total_price">
                                                    @lang('total price')
                                                </th>
                                                <th class="orderable text-center" data-name="pay_type">
                                                    @lang('payment method')</th>
                                                <th class="orderable text-center" data-name="status">
                                                    @lang('status')</th>
                                                <th class="orderable text-center" data-name="city_id">
                                                    @lang('city')</th>
                                                <th class="orderable text-center" data-name="district_id">
                                                    @lang('district')</th>
                                                <th class="orderable text-center" data-name="delivery_date">
                                                    @lang('delivery_date')</th>
                                                <th class="orderable text-center" data-name="delivery_time">
                                                    @lang('delivery_time')</th>



                                                <th class=" text-center py-2" data-name="actions">
                                                    @lang('actions')</th>

                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="pills-clothes-canceled" role="tabpanel"
                            aria-labelledby="pills-clothes-canceled-tab" tabindex="0">
                            <div class="mt-1">
                                <h3 class="text-dark">{{ trans('canceled clothes orders') }}</h3>
                                <div class="table-responsive">
                                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="fas fa-filter"></i>
                                        {{ trans('open filters of data') }}
                                    </button>
                                    <!--begin::Table-->
                                    <div class="row collapse" id="collapseTwo" class="accordion-collapse collapse">
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('operator')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="operator_id" id="operator_id">
                                                <option value=""> @lang('select operator')</option>
                                                @foreach ($operators as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('operator_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('representative')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="representative_id" id="representative_id">
                                                <option value=""> @lang('select representative')</option>
                                                @foreach ($representatives as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('representative_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="reference_id"> @lang('Order Number') </label>
                                            <input type="text" name="reference_id" class="form-control filter-input"
                                                placeholder="@lang('search for order number') " value="{{ request('reference_id') }}">
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="type">@lang('type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="type" id="type">
                                                <option value=""> @lang('select type')</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}" @selected($type == request('type'))>
                                                        {{ trans($type) }}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-1">
                                            <label for="pay_type">@lang('pay type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="pay_type" id="pay_type">
                                                <option value=""> @lang('select pay_type')</option>
                                                <option value="cash" @selected('cash' == request('pay_type'))>{{ trans('cash') }}
                                                </option>
                                                <option value="card" @selected('card' == request('pay_type'))>{{ trans('card') }}
                                                </option>
                                                <option value="wallet" @selected('wallet' == request('pay_type'))>{{ trans('wallet') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="phone"> @lang('phone') </label>
                                            <input type="text" name="phone" class="form-control filter-input"
                                                placeholder="@lang('search for client phone') " value="{{ request('phone') }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="city_id">@lang('city')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="city_id" id="city_id">
                                                <option value=""> @lang('select city')</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}" @selected($city->id == request('city_id'))>
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <table class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                        data-load="{{ route('dashboard.orders.index', ['filters' => ['status' => ['canceled', 'rejected']]]) }}">
                                        <!--begin::Table head-->
                                        <thead class="table-primary">
                                            <!--begin::Table row-->
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                                <th class="orderable text-center" data-name="created_at">
                                                    @lang('added at')</th>
                                                <th class="orderable text-center" data-name="reference_id">
                                                    @lang('order id')</th>
                                                <th class="orderable text-center" data-name="client_id">
                                                    @lang('client')</th>
                                                <th class="orderable text-center" data-name="phone">
                                                    @lang('phone')</th>
                                                <th class=" text-center py-2" data-name="receiver_date">
                                                    @lang('receiver date')</th>
                                                <th class=" text-center py-2" data-name="receiver_time">
                                                    @lang('receiver time')</th>

                                                <th class=" text-center py-2" data-name="delivery_date">
                                                    @lang('delivery date')</th>
                                                <th class=" text-center py-2" data-name="delivery_time">
                                                    @lang('delivery time')</th>
                                                <th class="orderable text-center" data-name="total_price">
                                                    @lang('total price')
                                                </th>
                                                <th class="orderable text-center" data-name="pay_type">
                                                    @lang('payment method')</th>
                                                <th class="orderable text-center" data-name="city_id">
                                                    @lang('city')</th>
                                                <th class="orderable text-center" data-name="district_id">
                                                    @lang('district')</th>

                                                <th class=" text-center py-2" data-name="actions">
                                                    @lang('actions')</th>

                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-delivered-orders" role="tabpanel"
                            aria-labelledby="pills-delivered-orders-tab" tabindex="0">
                            <div class="mt-1">
                                <h3 class="text-dark">{{ trans('delivered orders') }}</h3>
                                <div class="table-responsive">
                                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <i class="fas fa-filter"></i>
                                        {{ trans('open filters of data') }}
                                    </button>
                                    <div class="row collapse" id="collapseThree" class="accordion-collapse collapse">
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('operator')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="operator_id" id="operator_id">
                                                <option value=""> @lang('select operator')</option>
                                                @foreach ($operators as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('operator_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('representative')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="representative_id" id="representative_id">
                                                <option value=""> @lang('select representative')</option>
                                                @foreach ($representatives as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('representative_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="reference_id"> @lang('Order Number') </label>
                                            <input type="text" name="reference_id" class="form-control filter-input"
                                                placeholder="@lang('search for order number') " value="{{ request('reference_id') }}">
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="type">@lang('type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="type" id="type">
                                                <option value=""> @lang('select type')</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}" @selected($type == request('type'))>
                                                        {{ trans($type) }}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-1">
                                            <label for="pay_type">@lang('pay type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="pay_type" id="pay_type">
                                                <option value=""> @lang('select pay_type')</option>
                                                <option value="cash" @selected('cash' == request('pay_type'))>{{ trans('cash') }}
                                                </option>
                                                <option value="card" @selected('card' == request('pay_type'))>{{ trans('card') }}
                                                </option>
                                                <option value="wallet" @selected('wallet' == request('pay_type'))>{{ trans('wallet') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="phone"> @lang('phone') </label>
                                            <input type="text" name="phone" class="form-control filter-input"
                                                placeholder="@lang('search for client phone') " value="{{ request('phone') }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="city_id">@lang('city')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="city_id" id="city_id">
                                                <option value=""> @lang('select city')</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}" @selected($city->id == request('city_id'))>
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--begin::Table-->
                                    <table class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                        data-load="{{ route('dashboard.orders.index', ['filters' => ['status' => 'finished']]) }}">
                                        <!--begin::Table head-->
                                        <thead class="table-primary">
                                            <!--begin::Table row-->
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                                <th class="orderable text-center" data-name="created_at">
                                                    @lang('added at')</th>
                                                <th class="orderable text-center" data-name="reference_id">
                                                    @lang('order id')</th>
                                                <th class="orderable text-center" data-name="client_id">
                                                    @lang('client')</th>
                                                <th class="orderable text-center" data-name="phone">
                                                    @lang('phone')</th>
                                                <th class=" text-center py-2" data-name="receiver_date">
                                                    @lang('receiver date')</th>
                                                <th class=" text-center py-2" data-name="receiver_time">
                                                    @lang('receiver time')</th>
                                                <th class="orderable text-center" data-name="status">
                                                    @lang('status')</th>
                                                <th class="orderable text-center" data-name="total_price">
                                                    @lang('total price')
                                                </th>
                                                <th class="orderable text-center" data-name="pay_type">
                                                    @lang('payment method')</th>
                                                <th class="orderable text-center" data-name="city_id">
                                                    @lang('city')</th>
                                                <th class="orderable text-center" data-name="district_id">
                                                    @lang('district')</th>

                                                <th class=" text-center py-2" data-name="actions">
                                                    @lang('actions')</th>

                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-payment-orders" role="tabpanel"
                            aria-labelledby="pills-payment-orders-tab" tabindex="0">
                            <div class="mt-1">
                                <h3 class="text-dark">{{ trans('payment orders') }}</h3>
                                <div class="table-responsive">
                                    <button class="btn btn-primary mb-1" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        <i class="fas fa-filter"></i>
                                        {{ trans('open filters of data') }}
                                    </button>
                                    <div class="row collapse" id="collapseFour" class="accordion-collapse collapse">
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('operator')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="operator_id" id="operator_id">
                                                <option value=""> @lang('select operator')</option>
                                                @foreach ($operators as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('operator_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="operator_id">@lang('representative')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="representative_id" id="representative_id">
                                                <option value=""> @lang('select representative')</option>
                                                @foreach ($representatives as $user)
                                                    <option value="{{ $user->id }}" @selected($user->id == request('representative_id'))>
                                                        {{ $user->fullname }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="reference_id"> @lang('Order Number') </label>
                                            <input type="text" name="reference_id" class="form-control filter-input"
                                                placeholder="@lang('search for order number') " value="{{ request('reference_id') }}">
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <label for="type">@lang('type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="type" id="type">
                                                <option value=""> @lang('select type')</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}" @selected($type == request('type'))>
                                                        {{ trans($type) }}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-1">
                                            <label for="pay_type">@lang('pay type')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="pay_type" id="pay_type">
                                                <option value=""> @lang('select pay_type')</option>
                                                <option value="cash" @selected('cash' == request('pay_type'))>{{ trans('cash') }}
                                                </option>
                                                <option value="card" @selected('card' == request('pay_type'))>{{ trans('card') }}
                                                </option>
                                                <option value="wallet" @selected('wallet' == request('pay_type'))>{{ trans('wallet') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="phone"> @lang('phone') </label>
                                            <input type="text" name="phone" class="form-control filter-input"
                                                placeholder="@lang('search for client phone') " value="{{ request('phone') }}">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <label for="city_id">@lang('city')</label>
                                            <select class="custom-select filter-input form-select advance-select"
                                                name="city_id" id="city_id">
                                                <option value=""> @lang('select city')</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}" @selected($city->id == request('city_id'))>
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--begin::Table-->
                                    <table class="view-dataTable table align-middle text-center table-row-dashed fs-6 gy-5"
                                        data-load="{{ route('dashboard.orders.index', ['filters' => ['cancel_testing' => 'cancel_testing', 'status' => ['pending_payment', 'failed_payment']]]) }}">
                                        <!--begin::Table head-->
                                        <thead class="table-primary">
                                            <!--begin::Table row-->
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">

                                                <th class="orderable text-center" data-name="created_at">
                                                    @lang('added at')</th>
                                                <th class="orderable text-center" data-name="reference_id">
                                                    @lang('order id')</th>
                                                <th class="orderable text-center" data-name="client_id">
                                                    @lang('client')</th>
                                                <th class="orderable text-center" data-name="phone">
                                                    @lang('phone')</th>
                                                <th class=" text-center py-2" data-name="receiver_date">
                                                    @lang('receiver date')</th>
                                                <th class=" text-center py-2" data-name="receiver_time">
                                                    @lang('receiver time')</th>
                                                <th class="orderable text-center" data-name="status">
                                                    @lang('status')</th>
                                                <th class="orderable text-center" data-name="total_price">
                                                    @lang('total price')
                                                </th>
                                                <th class="orderable text-center" data-name="pay_type">
                                                    @lang('payment method')</th>
                                                <th class="orderable text-center" data-name="city_id">
                                                    @lang('city')</th>
                                                <th class="orderable text-center" data-name="district_id">
                                                    @lang('district')</th>

                                                <th class=" text-center py-2" data-name="actions">
                                                    @lang('actions')</th>

                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
<!-- Content -->


<!-- / Content -->
