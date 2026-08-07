@extends('admin::layouts.dashboard')
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar my-3" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.companies.index') }}"
                            class="text-muted text-hover-primary">@lang('companies')</a>
                    </li>
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <ul
                        class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold wizard-nav">
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 active" data-bs-toggle="tab"
                                href="#kt_company_step_1">@lang('1- Company Data')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 @if(!isset($item)) disabled @endif"
                                data-bs-toggle="tab" href="#kt_company_step_2">@lang('2- Branches')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 @if(!isset($item)) disabled @endif"
                                data-bs-toggle="tab" href="#kt_company_step_3">@lang('3- Company Employees')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 @if(!isset($item)) disabled @endif"
                                data-bs-toggle="tab" href="#kt_company_step_4">@lang('4- Contract')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 @if(!isset($contract)) disabled @endif"
                                data-bs-toggle="tab" href="#kt_company_step_5">@lang('5- Product Prices')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 @if(!isset($contract)) disabled @endif"
                                data-bs-toggle="tab" href="#kt_company_step_6">@lang('6- Customer Prices')</a>
                        </li>
                       
                    </ul>
                </div>

                <div class="tab-content">
                    <!--– Step 1: Company Data –-->
                    <div class="tab-pane fade show active" id="kt_company_step_1" role="tabpanel">
                        <form class="form" method="POST" id="operation-form" data-id="{{ $item->id ?? null }}"
                            enctype="multipart/form-data" @if(isset($item))
                            action="{{ route('dashboard.companies.edit', $item->id) }}" data-mode="edit" @else
                            action="{{ route('dashboard.companies.create') }}" data-mode="new" @endif>
                            @csrf
                            <div class="card-body row">
                                <div class="form-group mb-3 col-md-12 text-center">
                                    <label for="avatar">{{ trans('avatar') }}</label>
                                    <div class="media-center-group form-control" data-max="1" data-type="image">
                                        <input type="text" hidden class="form-control" name="avatar"
                                            value="{{ old('avatar', $item->avatar ?? null) }}">
                                        <button type="button" class="btn btn-secondary media-center-load"
                                            style="margin-top:10px;">
                                            <i class="fa fa-image"></i>
                                        </button>
                                        <div class="input-gallery"></div>
                                    </div>
                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="required" for="fullname">{{ trans('company name') }}</label>
                                    <input type="text" name="fullname" class="form-control"
                                        placeholder="{{ trans('Enter company name') }}"
                                        value="{{ old('fullname', $item->fullname ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="" for="name_ar">{{ trans('name in arabic') }}</label>
                                    <input type="text" name="name_ar" class="form-control"
                                        placeholder="{{ trans('Enter name in arabic') }}"
                                        value="{{ old('name_ar', $item->name_ar ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label class="" for="name_en">{{ trans('name in english') }}</label>
                                    <input type="text" name="name_en" class="form-control"
                                        placeholder="{{ trans('Enter name in english') }}"
                                        value="{{ old('name_en', $item->name_en ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label for="line_of_business">{{ trans('line of business') }}</label>
                                    <input type="text" name="line_of_business" class="form-control"
                                        placeholder="{{ trans('Enter line of business') }}"
                                        value="{{ old('line_of_business', $item->line_of_business ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-4">
                                    <label for="email">{{ trans('email') }}</label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="{{ trans('Enter email') }}"
                                        value="{{ old('email', $item->email ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="phone">{{ trans('phone') }}</label>
                                    <input type="text" name="phone" class="form-control"
                                        placeholder="{{ trans('Enter phone') }}"
                                        value="{{ old('phone', $item->phone ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="bank_account_number">{{ trans('bank account number') }}</label>
                                    <input type="text" name="bank_account_number" class="form-control"
                                        placeholder="{{ trans('Enter bank account number') }}"
                                        value="{{ old('bank_account_number', $item->bank_account_number ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="iban">{{ trans('iban') }}</label>
                                    <input type="text" name="iban" class="form-control"
                                        placeholder="{{ trans('Enter IBAN') }}"
                                        value="{{ old('iban', $item->iban ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="commercial_registration">{{ trans('commercial registration') }}</label>
                                    <input type="text" name="commercial_registration" class="form-control"
                                        placeholder="{{ trans('Enter commercial registration') }}"
                                        value="{{ old('commercial_registration', $item->commercial_registration ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="tax_number">{{ trans('tax number') }}</label>
                                    <input type="text" name="tax_number" class="form-control"
                                        placeholder="{{ trans('Enter tax number') }}"
                                        value="{{ old('tax_number', $item->tax_number ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="street_name">{{ trans('street name') }}</label>
                                    <input type="text" name="street_name" class="form-control"
                                        placeholder="{{ trans('Enter street name') }}"
                                        value="{{ old('street_name', $item->street_name ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="building_no">{{ trans('building no') }}</label>
                                    <input type="text" name="building_no" class="form-control"
                                        placeholder="{{ trans('Enter building no') }}"
                                        value="{{ old('building_no', $item->building_no ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="city_id">{{ trans('city') }}</label>
                                    <select name="city_id" id="city_id" class="form-select select2">
                                        <option value="">{{ trans('Select City') }}</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" @selected(old('city_id', $item->city_id ?? null) == $city->id)>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="district_id">{{ trans('district') }}</label>
                                    <select name="district_id" id="district_id" class="form-select select2">
                                        <option value="">{{ trans('Select District') }}</option>
                                        @if(isset($item) && $item->city_id)
                                            @foreach(\Core\Info\Models\District::where('city_id', $item->city_id)->get() as $district)
                                                <option value="{{ $district->id }}" @selected(old('district_id', $item->district_id ?? null) == $district->id)>{{ $district->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="postal_code">{{ trans('postal code') }}</label>
                                    <input type="text" name="postal_code" class="form-control"
                                        placeholder="{{ trans('Enter postal code') }}"
                                        value="{{ old('postal_code', $item->postal_code ?? null) }}">
                                </div>

                                <div class="form-group mb-3 col-md-3">
                                    <label for="additional_number">{{ trans('additional number') }}</label>
                                    <input type="text" name="additional_number" class="form-control"
                                        placeholder="{{ trans('Enter additional number') }}"
                                        value="{{ old('additional_number', $item->additional_number ?? null) }}">
                                </div>



                                <div class="form-group mb-3 col-md-3">
                                    <label for="owner_id">{{ trans('owner') }}</label>
                                    <select class="custom-select form-select ajax-select"
                                        data-url="{{ route('dashboard.companies.search-users') }}" name="owner_id"
                                        id="owner_id">
                                        @if(isset($item) && $item->owner)
                                        <option value="{{ $item->owner->id }}" selected>{{ $item->owner->fullname }} :
                                            {{ $item->owner->phone }}</option>
                                        @else
                                        <option value="">{{ trans('select') . ' ' . trans('owner') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary font-weight-bold">
                                    @if(isset($item)) {{ trans('Update Company Data') }} @else {{ trans('Create Company
                                    & Continue') }} @endif
                                </button>
                                @if(isset($item))
                                <button type="button"
                                    class="btn btn-light-primary font-weight-bold ms-2 next-step">@lang('Next') <i
                                        class="fas fa-arrow-right"></i></button>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!--– Step 2: Branches –-->
                    <div class="tab-pane fade" id="kt_company_step_2" role="tabpanel">
                        <div class="card-body">
                            @if(isset($item))
                            <div class="items-container" data-items-on="company_id" data-items-name="branches"
                                data-items-from="company-branches">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h3>{{ trans('branches') }}</h3>
                                    <button class="btn btn-primary create-new-items"><i class="fas fa-plus"></i>
                                        @lang('Add Branch')</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover text-center">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col" data-name="name">{{ trans('branch name') }}</th>
                                                <th scope="col" data-name="location">{{ trans('location') }}</th>
                                                <th scope="col" data-name="is_default" data-type="checkbox">{{
                                                    trans('default') }}</th>
                                                <th scope="col" data-name="is_active" data-type="checkbox">{{
                                                    trans('active') }}</th>
                                                <th scope="col" data-name="actions" data-type="actions">{{
                                                    trans('actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item->branches ?? [] as $branch)
                                            <tr data-id="{{ $branch->id }}"
                                                data-data="{{ json_encode($branch->itemData) }}">
                                                <td>{{ $branch->name }}</td>
                                                <td>{{ $branch->location }}</td>
                                                <td>
                                                    @if($branch->is_default)
                                                    <span class="p-1 rounded bg-success text-white">@lang('yes')</span>
                                                    @else
                                                    <span class="p-1 rounded bg-danger text-white">@lang('no')</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($branch->is_active)
                                                    <span class="p-1 rounded bg-success text-white">@lang('yes')</span>
                                                    @else
                                                    <span class="p-1 rounded bg-danger text-white">@lang('no')</span>
                                                    @endif
                                                </td>
                                                <td class="options">{!! $branch->itemsActions !!}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-light-primary font-weight-bold prev-step"><i
                                    class="fas fa-arrow-left"></i> @lang('Previous')</button>
                            <button type="button" class="btn btn-light-primary font-weight-bold next-step">@lang('Next')
                                <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!--– Step 3: Company Employees –-->
                    <div class="tab-pane fade" id="kt_company_step_3" role="tabpanel">
                        <div class="card-body">
                            @if(isset($item))
                            <div class="items-container" data-items-on="company_id" data-items-name="employees"
                                data-items-from="company-employees">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h3>{{ trans('Company Employees') }}</h3>
                                    <button class="btn btn-primary create-new-items"><i class="fas fa-plus"></i>
                                        @lang('Add Employee')</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover text-center">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col" data-name="user">{{ trans('employee') }}</th>
                                                <th scope="col" data-name="permission">{{ trans('permission') }}</th>
                                                <th scope="col" data-name="actions" data-type="actions">{{
                                                    trans('actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employees as $employee)
                                            <tr data-id="{{ $employee->id }}"
                                                data-data="{{ json_encode($employee->itemData) }}">
                                                <td data-name="user_id" data-type="select">{{ $employee->user?->fullname
                                                    }}</td>
                                                <td data-name="permission_id" data-type="select">{{
                                                    $employee->permission?->name }}</td>
                                                <td class="options">{!! $employee->itemsActions !!}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-light-primary font-weight-bold prev-step"><i
                                    class="fas fa-arrow-left"></i> @lang('Previous')</button>
                            <button type="button" class="btn btn-light-primary font-weight-bold next-step">@lang('Next')
                                <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!--– Step 4: Contract –-->
                    <div class="tab-pane fade" id="kt_company_step_4" role="tabpanel">
                        <div class="card-body">
                            @if(isset($item))
                            <form class="form" method="POST" id="contract-form"
                                action="{{ $contract ? route('dashboard.contracts.edit', $contract->id) : route('dashboard.contracts.create') }}"
                                data-mode="{{ $contract ? 'edit' : 'new' }}">
                                @csrf
                                @if($contract) @method('PUT') @endif
                                <input type="hidden" name="company_id" value="{{ $item->id }}">

                                <div class="row">
                                    <div class="form-group mb-3 col-md-6">
                                        <label class="required" for="title">{{ trans('contract title') }}</label>
                                        <input type="text" name="title" class="form-control"
                                            value="{{ $contract->title ?? '' }}" required>
                                    </div>
                                    <div class="form-group mb-3 col-md-3">
                                        <label for="months_count" class="required">{{ trans('months count') }}</label>
                                        <input type="number" name="months_count" class="form-control"
                                            value="{{ $contract->months_count ?? '' }}" required>
                                    </div>
                                    <div class="form-group mb-3 col-md-3">
                                        <label for="month_fees" class="required">{{ trans('month fees') }}</label>
                                        <input type="number" name="month_fees" class="form-control"
                                            value="{{ $contract->month_fees ?? '' }}" required>
                                    </div>
                                    <div class="form-group mb-3 col-md-3">
                                        <label for="start_date">{{ trans('start date') }}</label>
                                        <input type="date" name="start_date" class="form-control"
                                            value="{{ isset($contract->start_date) ? $contract->start_date->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="form-group mb-3 col-md-3">
                                        <label for="end_date">{{ trans('end date') }}</label>
                                        <input type="date" name="end_date" class="form-control"
                                            value="{{ isset($contract->end_date) ? $contract->end_date->format('Y-m-d') : '' }}">
                                     </div>

                                </div>

                                <div class="card-footer d-flex justify-content-between">
                                    <button type="button" class="btn btn-light-primary font-weight-bold prev-step"><i
                                            class="fas fa-arrow-left"></i> @lang('Previous')</button>
                                    <div>
                                        <button type="submit" class="btn btn-primary font-weight-bold">{{ trans('Save Contract Details') }}</button>
                                        @if($contract)
                                        <button type="button"
                                            class="btn btn-light-primary font-weight-bold next-step">@lang('Next') <i
                                                class="fas fa-arrow-right"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>

                    <!--– Step 5: Product Prices –-->
                    <div class="tab-pane fade" id="kt_company_step_5" role="tabpanel">
                        <div class="card-body">
                            @if(isset($contract))
                            <div class="items-container" data-items-on="contract_id" data-items-name="contracts-prices"
                                data-items-from="contracts-prices">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h3>{{ trans('Product Prices') }}</h3>
                                    <button class="btn btn-primary create-new-items"><i class="fas fa-plus"></i>
                                        @lang('Add Product Price')</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover text-center">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col" data-name="product_id" data-type="select">{{
                                                    trans('Product') }}</th>
                                                <th scope="col" data-name="price">{{ trans('Price') }}</th>
                                                <th scope="col" data-name="actions" data-type="actions">{{
                                                    trans('actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($contract->contractPrices ?? [] as $cp)
                                            <tr data-id="{{ $cp->id }}" data-data="{{ json_encode($cp->itemData) }}">
                                                <td> {{ $cp->product?->category?->name }} ➡️ {{
                                                    $cp->product?->subCategory?->name }} ➡️ {{ $cp->product?->name }}
                                                </td>
                                                <td>{{ $cp->price }}</td>
                                                <td class="options">{!! $cp->itemsActions !!}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-light-primary font-weight-bold prev-step"><i
                                    class="fas fa-arrow-left"></i> @lang('Previous')</button>
                            <button type="button" class="btn btn-light-primary font-weight-bold next-step">@lang('Next')
                                <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!--– Step 6: Customer Prices –-->
                    <div class="tab-pane fade" id="kt_company_step_6" role="tabpanel">
                        <div class="card-body">
                            @if(isset($contract))
                            <div class="items-container" data-items-on="contract_id"
                                data-items-name="contracts-customer-prices" data-items-from="contracts-customer-prices">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h3>{{ trans('Customer Over Prices') }}</h3>
                                    <button class="btn btn-primary create-new-items"><i class="fas fa-plus"></i>
                                        @lang('Add Over Price')</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover text-center">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col" data-name="product_id" data-type="select">{{
                                                    trans('Product') }}</th>
                                                <th scope="col" data-name="over_price">{{ trans('Over Price') }}</th>
                                                <th scope="col" data-name="actions" data-type="actions">{{
                                                    trans('actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($contract->contractCustomerPrices ?? [] as $ccp)
                                            <tr data-id="{{ $ccp->id }}" data-data="{{ json_encode($ccp->itemData) }}">
                                                <td>{{ $ccp->product?->category?->name }} ➡️ {{
                                                    $ccp->product?->subCategory?->name }} ➡️ {{ $ccp->product?->name }}
                                                </td>
                                                <td>{{ $ccp->over_price }}</td>
                                                <td class="options">{!! $ccp->itemsActions !!}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-start">
                            <button type="button" class="btn btn-light-primary font-weight-bold prev-step"><i
                                    class="fas fa-arrow-left"></i> @lang('Previous')</button>
                        </div>
                    </div>

                  
                </div>
            </div>
        </div>
    </div>
</div>

<!--– Modals –-->
@if(isset($item))
<!--– Branches Modal –-->
<div class="modal fade" id="company-branchesModal" aria-hidden="true" aria-labelledby="company-branchesModalLabel"
    data-store="{{ route('dashboard.company-branches.create') }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="company-branchesModalLabel">{{ trans('branches') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form items-modal-form">
                    <input type="hidden" name="company_id" value="{{ $item->id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group mb-3 col-md-12">
                                    <label class="required" for="name">{{ trans('branch name') }}</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3 col-md-12">
                                    <label for="location">{{ trans('location') }}</label>
                                    <input type="text" name="location" id="branch_location_text" class="form-control">
                                </div>
                                <div class="form-group mb-3 col-md-6">
                                    <label for="lat">{{ trans('Latitude') }}</label>
                                    <input type="text" name="lat" id="branch_lat" class="form-control">
                                </div>
                                <div class="form-group mb-3 col-md-6">
                                    <label for="lng">{{ trans('Longitude') }}</label>
                                    <input type="text" name="lng" id="branch_lng" class="form-control">
                                </div>
                                <div class="form-group mb-3 col-md-6">
                                    <label for="city_id">{{ trans('city') }}</label>
                                    <select class="form-select advance-select" name="city_id">
                                        <option value="">{{ trans('select city') }}</option>
                                        @foreach($cities ?? [] as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3 col-md-6">
                                    <label for="district_id">{{ trans('district') }}</label>
                                    <select class="form-select advance-select" name="district_id">
                                        <option value="">{{ trans('select district') }}</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3 col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_default" value="1"
                                            id="is_default">
                                        <label class="form-check-label" for="is_default">{{ trans('is default')
                                            }}</label>
                                    </div>
                                </div>
                                <div class="form-group mb-3 col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="is_active">
                                        <label class="form-check-label" for="is_active">{{ trans('is active') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 position-relative">
                            <label>@lang('Select Location on Map')</label>
                            <div class="mb-2">
                                <div class="input-group">
                                    <input type="text" id="branch_map_search" class="form-control"
                                        placeholder="@lang('Search for a location...')">
                                    <button class="btn btn-secondary" type="button" id="btn_map_search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div id="branch_map_suggestions" class="suggestions-list" style="display: none;"></div>
                            </div>
                            <div id="branch_map" style="height: 360px; border: 1px solid #ccc; border-radius: 5px;">
                            </div>
                            <p class="text-muted small mt-2">@lang('Click on the map or drag the marker to set
                                location.')</p>
                        </div>
                        <div class="col-md-12 mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary font-weight-bold">{{ trans('Submit')
                                }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--– Employees Modal –-->
<div class="modal fade" id="company-employeesModal" aria-hidden="true" aria-labelledby="company-employeesModalLabel"
    data-store="{{ route('dashboard.company-employees.create') }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="company-employeesModalLabel">{{ trans('Company Employees') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form items-modal-form">
                    <input type="hidden" name="company_id" value="{{ $item->id }}">
                    <div class="row">
                        <div class="form-group mb-3 col-md-6">
                            <label class="required" for="user_id">{{ trans('Employee') }}</label>
                            <select class="form-select ajax-select"
                                data-url="{{ route('dashboard.companies.search-users') }}" name="user_id"
                                id="employee_user_id" required>
                                <option value="">@lang('Select User')</option>
                            </select>
                        </div>
                        <div class="form-group mb-3 col-md-6">
                            <label class="required" for="permission_id">{{ trans('Permission') }}</label>
                            <select class="form-select" name="permission_id" required>
                                <option value="">@lang('Select Permission')</option>
                                @foreach($permissions ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3 col-md-12">
                            <label for="branch_id">{{ trans('Branch (Optional)') }}</label>
                            <select class="form-select" name="branch_id">
                                <option value="">@lang('All Branches')</option>
                                @foreach($item->branches ?? [] as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary font-weight-bold">{{ trans('Submit')
                                }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(isset($contract))
<!--– Product Prices Modal –-->
<div class="modal fade" id="contracts-pricesModal" aria-hidden="true" aria-labelledby="contracts-pricesModalLabel"
    data-store="{{ route('dashboard.contracts-prices.create') }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="contracts-pricesModalLabel">{{ trans('Product Prices') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form items-modal-form">
                    <div class="row">
                        <input type="hidden" name="extra_id" value="{{ $contract->id }}">
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="product_id">{{ trans('Product') }}</label>
                            <select class="form-select advance-select" name="product_id" required>
                                <option value="">@lang('Select Product')</option>
                                @foreach($products ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->category?->name }} ➡️ {{ $p->subCategory?->name }}
                                    ➡️ {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="price">{{ trans("Price") }}</label>
                            <input type="number" name="price" class="form-control" required step="0.01">
                        </div>
                        <div class="col-md-12 mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary font-weight-bold">{{ trans('Submit')
                                }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--– Customer Prices Modal –-->
<div class="modal fade" id="contracts-customer-pricesModal" aria-hidden="true"
    aria-labelledby="contracts-customer-pricesModalLabel"
    data-store="{{ route('dashboard.contracts-customer-prices.create') }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="contracts-customer-pricesModalLabel">{{ trans('Customer Over Prices')
                    }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form items-modal-form">
                    <div class="row">
                        <input type="hidden" name="extra_id" value="{{ $contract->id }}">
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="product_id">{{ trans('Product') }}</label>
                            <select class="form-select advance-select" name="product_id" required>
                                <option value="">@lang('Select Product')</option>
                                @foreach($products ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->category?->name }} ➡️ {{ $p->subCategory?->name }}
                                    ➡️ {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="over_price">{{ trans("Over Price") }}</label>
                            <input type="number" name="over_price" class="form-control" required step="0.01">
                        </div>
                        <div class="col-md-12 mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary font-weight-bold">{{ trans('Submit')
                                }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!--– Delete Modals –-->
<div class="modal fade" id="company-branchesDeleteModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-body"><button type="button" class="btn-close mb-5" data-bs-dismiss="modal"></button>
                <h1 class="mb-3">@lang('Are you sure?')</h1>
                <p>@lang('You want to delete this branch?')</p><button type="button"
                    class="btn btn-danger items-final-delete">@lang('Delete')</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="company-employeesDeleteModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-body"><button type="button" class="btn-close mb-5" data-bs-dismiss="modal"></button>
                <h1 class="mb-3">@lang('Are you sure?')</h1>
                <p>@lang('You want to remove this employee?')</p><button type="button"
                    class="btn btn-danger items-final-delete">@lang('Remove')</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="contracts-pricesDeleteModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-body"><button type="button" class="btn-close mb-5" data-bs-dismiss="modal"></button>
                <h1 class="mb-3">@lang('Are you sure?')</h1>
                <p>@lang('You want to delete this price?')</p><button type="button"
                    class="btn btn-danger items-final-delete">@lang('Delete')</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="contracts-customer-pricesDeleteModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-body"><button type="button" class="btn-close mb-5" data-bs-dismiss="modal"></button>
                <h1 class="mb-3">@lang('Are you sure?')</h1>
                <p>@lang('You want to delete this over price?')</p><button type="button"
                    class="btn btn-danger items-final-delete">@lang('Delete')</button>
            </div>
        </div>
    </div>
</div>

<!--– Financials Modal –-->
<div class="modal fade" id="financialsModal" aria-hidden="true" aria-labelledby="financialsModalLabel"
    data-store="{{ route('dashboard.financials.create') }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="financialsModalLabel">{{ trans('Financial Record') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form items-modal-form">
                    <input type="hidden" name="company_id" value="{{ $item->id }}">
                    <div class="row">
                        <div class="form-group mb-3 col-md-6">
                            <label for="reference_id">{{ trans('Financial Reference ID') }}</label>
                            <input type="text" name="reference_id" class="form-control" readonly
                                placeholder="{{ trans('Auto-generated') }}">
                        </div>
                        <div class="form-group mb-3 col-md-6">
                            <label class="required" for="amount">{{ trans('Amount') }}</label>
                            <input type="number" name="amount" class="form-control" required step="0.01">
                        </div>
                        <div class="form-group mb-3 col-md-6">
                            <label for="collection_date">{{ trans('Collection Date') }}</label>
                            <input type="date" name="collection_date" class="form-control">
                        </div>
                        <div class="form-group mb-3 col-md-12">
                            <label for="note">{{ trans('Note') }}</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary font-weight-bold">{{ trans('Submit')
                                }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="financialsDeleteModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-body"><button type="button" class="btn-close mb-5" data-bs-dismiss="modal"></button>
                <h1 class="mb-3">@lang('Are you sure?')</h1>
                <p>@lang('You want to delete this financial record?')</p><button type="button"
                    class="btn btn-danger items-final-delete">@lang('Delete')</button>
            </div>
        </div>
    </div>
</div>
@endif

@include('media::mediaCenter.modal')
@endsection

@push('css')
<link href="{{ asset('control') }}/js/custom/crud/form.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .nav-line-tabs .nav-item .nav-link.disabled {
        color: #ccc !important;
        cursor: not-allowed;
    }

    .select2-container {
        width: 100% !important;
    }

    .suggestions-list {
        position: absolute;
        z-index: 1000;
        width: 100%;
        background: white;
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        top: 40px;
    }

    .suggestions-list div {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }

    .suggestions-list div:hover {
        background-color: #f8f9fa;
        color: #009ef7;
    }
</style>
@endpush
@push('js')
<script src="{{ asset('control') }}/js/custom/crud/form.js"></script>
<script>
    $(document).on('show.bs.modal', '#contracts-pricesModal, #contracts-customer-pricesModal', function () {
        let contractId = '{{ $contract->id ?? "" }}';
        if (contractId) {
            $(this).find('input[name="contract_id"]').val(contractId);
        }
    });

    $(document).ready(function () {
        $('.ajax-select').each(function () {
            var element = $(this);
            element.select2({
                allowClear: true,
                placeholder: "@lang('Search user...')",
                dropdownParent: element.closest('.modal').length ? element.closest('.modal') : null,
                ajax: {
                    url: element.data('url'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });
        });
    });
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // – Wizard Navigation –
    $(document).on('click', '.next-step', function () {
        let nextTab = $('.wizard-nav .nav-link.active').parent().next('li').find('a');
        if (nextTab.length > 0 && !nextTab.hasClass('disabled')) {
            nextTab.tab('show');
        }
    });

    $(document).on('click', '.prev-step', function () {
        let prevTab = $('.wizard-nav .nav-link.active').parent().prev('li').find('a');
        if (prevTab.length > 0) {
            prevTab.tab('show');
        }
    });

    // – Map Integration –
    let map, marker;
    $('#company-branchesModal').on('shown.bs.modal', function () {
        let lat = $('#branch_lat').val() || 24.7136;
        let lng = $('#branch_lng').val() || 46.6753;

        if (!map) {
            map = L.map('branch_map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                $('#branch_lat').val(e.latlng.lat.toFixed(6));
                $('#branch_lng').val(e.latlng.lng.toFixed(6));
                updateAddress(e.latlng.lat, e.latlng.lng);
            });

            marker.on('dragend', function (e) {
                let pos = marker.getLatLng();
                $('#branch_lat').val(pos.lat.toFixed(6));
                $('#branch_lng').val(pos.lng.toFixed(6));
                updateAddress(pos.lat, pos.lng);
            });
        } else {
            map.invalidateSize();
            map.setView([lat, lng], 13);
            marker.setLatLng([lat, lng]);
        }
    });

    function updateAddress(lat, lng) {
        $.getJSON(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, function (data) {
            if (data && data.display_name) {
                $('#branch_location_text').val(data.display_name);
            }
        });
    }

    // – Map Search Logic –
    function executeMapSearch() {
        let query = $('#branch_map_search').val();
        let suggestions = $('#branch_map_suggestions');

        if (query.length < 3) {
            suggestions.hide();
            return;
        }

        $.getJSON(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`, function (data) {
            suggestions.empty().show();
            if (data.length > 0) {
                $.each(data, function (i, item) {
                    suggestions.append(`<div data-lat="${item.lat}" data-lng="${item.lon}">${item.display_name}</div>`);
                });
            } else {
                suggestions.append('<div class="text-muted p-2">No results found</div>');
            }
        });
    }

    let searchTimeout;
    $(document).on('input', '#branch_map_search', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(executeMapSearch, 500);
    });

    $(document).on('click', '#btn_map_search', function () {
        executeMapSearch();
    });

    $(document).on('keypress', '#branch_map_search', function (e) {
        if (e.which == 13) { // Enter key
            e.preventDefault();
            executeMapSearch();
        }
    });

    $(document).on('click', '#branch_map_suggestions div', function () {
        let lat = $(this).data('lat');
        let lng = $(this).data('lng');
        let name = $(this).text();

        if (lat && lng) {
            let latlng = [parseFloat(lat), parseFloat(lng)];
            map.setView(latlng, 15);
            marker.setLatLng(latlng);
            $('#branch_lat').val(latlng[0].toFixed(6));
            $('#branch_lng').val(latlng[1].toFixed(6));
            $('#branch_location_text').val(name);
            $('#branch_map_search').val(name);
        }
        $('#branch_map_suggestions').hide();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#branch_map_search, #branch_map_suggestions').length) {
            $('#branch_map_suggestions').hide();
        }
    });

    // – Dynamic Districts for Branches –
    $(document).on('change', '#city_id', function () {
        let cityId = $(this).val();
        let districtSelect = $('#district_id');

        districtSelect.empty().append('<option value="">{{ trans("Select District") }}</option>');

        if (cityId) {
            $.ajax({
                url: "{{ route('dashboard.districts.getByCity', ':id') }}".replace(':id', cityId),
                type: 'GET',
                success: function (response) {
                    if (response.data) {
                        $.each(response.data, function (index, district) {
                            districtSelect.append('<option value="' + district.id + '">' + district.name + '</option>');
                        });
                        districtSelect.trigger('change');
                    }
                }
            });
        }
    });

    $(document).on('change', '#company-branchesModal select[name="city_id"]', function () {
        let cityId = $(this).val();
        let districtSelect = $('#company-branchesModal select[name="district_id"]');

        let modal = $(this).closest('.modal');
        let targetDistrictId = null;

        if (modal.data('mode') === 'edit') {
            let activeTr = $('.items-container.active tr.active');
            if (activeTr.length) {
                let data = activeTr.data('data');
                if (data && data.city_id == cityId) {
                    targetDistrictId = data.district_id;
                }
            }
        }

        districtSelect.empty().append('<option value="">{{ trans("select district") }}</option>');

        if (cityId) {
            $.ajax({
                url: "{{ route('dashboard.districts.getByCity', ':id') }}".replace(':id', cityId),
                type: 'GET',
                success: function (response) {
                    if (response.data) {
                        $.each(response.data, function (index, district) {
                            let selected = (targetDistrictId && district.id == targetDistrictId) ? 'selected' : '';
                            districtSelect.append('<option value="' + district.id + '" ' + selected + '>' + district.name + '</option>');
                        });
                        if (districtSelect.hasClass('select2-hidden-accessible')) {
                            districtSelect.trigger('change');
                        }
                    }
                }
            });
        }
    });

    // Initialize existing select2
    $('.advance-select').select2();

    // – Contract AJAX –
    $(document).on('submit', '#contract-form', function (e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let data = new FormData(this);
        let mode = form.data('mode');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    text: response.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "@lang('Ok, got it!')",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                }).then(function () {
                    location.reload();
                });
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                let message = xhr.responseJSON.message;
                let errorMessages = '';
                if (errors) {
                    $.each(errors, function (key, value) {
                        errorMessages += value + '<br>';
                    });
                }
                Swal.fire({
                    html: errorMessages || message || "@lang('System Error')",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "@lang('Ok, got it!')",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            }
        });
    });
</script>
@endpush