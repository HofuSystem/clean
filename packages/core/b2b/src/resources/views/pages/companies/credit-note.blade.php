@extends('admin::layouts.dashboard')

@section('content')
@php
$sellerName = \Core\Settings\Services\SettingsService::getDataBaseSetting('name_en') ?: 'CleanStation';
$sellerNameAr = \Core\Settings\Services\SettingsService::getDataBaseSetting('name_ar') ?: 'كلين ستيشن';
$sellerVat = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_tax_number') ?: '—';
$sellerCrn = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_commercial_registration') ?: '—';
$sellerStreet = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_street_name') ?: '';
$sellerBuilding = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_building_no') ?: '';
$sellerDistrictId = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_district');
$sellerCityId = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_city');
$sellerPostal = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_postal_code') ?: '';
$sellerAdditional = \Core\Settings\Services\SettingsService::getDataBaseSetting('tax_additional_number') ?: '';
$sellerCity = $sellerCityId ? \Core\Info\Models\City::find($sellerCityId)?->name : '';
$sellerDistrict = $sellerDistrictId ? \Core\Info\Models\District::find($sellerDistrictId)?->name : '';
$sellerLogo = \Core\Settings\Services\SettingsService::getDataBaseSetting('logo');
@endphp

<div class="container py-8 d-print-none">
    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('dashboard.companies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> @lang('Back to Statement')
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-2"></i> @lang('Print Credit Note')
        </button>
    </div>
</div>

<div class="credit-note-paper card shadow-sm mx-auto"
    style="max-width: 850px; font-family: 'Tajawal', sans-serif; direction: rtl;">
    <div class="card-body p-10">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-danger fw-bolder mb-0" style="font-size: 2.2rem;">إشعار دائن ضريبي</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tax Credit Note</p>
            <div class="mt-2 text-muted" style="font-size: 0.85rem;">
                <span>التاريخ: {{ $financial->collection_date ? $financial->collection_date->format('Y-m-d') : $financial->created_at->format('Y-m-d') }}</span>
                <span class="mx-3">|</span>
                <span>المرجع: {{ $financial->note ?: '—' }}</span>
            </div>
        </div>

        {{-- Meta Data --}}
        <div class="row mb-8 fs-6">
            <div class="col-6">
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">رقم المستند:</span>
                    <span>{{ $financial->reference_id }}</span>
                </div>
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">العميل:</span>
                    <span>{{ $financial->company->name_ar ?: $financial->company->fullname }}</span>
                </div>
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">الرقم الضريبي للعميل:</span>
                    <span>{{ $financial->company->tax_number ?: '—' }}</span>
                </div>
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">العنوان:</span>
                    <div style="font-size: 0.8rem;">
                        <div>@lang('Building No'): {{ $financial->company->building_no }}</div>
                        <div>@lang('Street'): {{ $financial->company->street_name }}</div>
                        @if($financial->company->district) <div>@lang('District'): {{ $financial->company->district->name }}</div> @endif
                        @if($financial->company->city) <div>@lang('City'): {{ $financial->company->city->name }}</div> @endif
                        @if($financial->company->postal_code) <div>@lang('Postal Code'): {{ $financial->company->postal_code }}</div> @endif
                        @if($financial->company->additional_number) <div>@lang('Additional Number'): {{ $financial->company->additional_number }}</div> @endif
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">البائع:</span>
                    <span>{{ $sellerNameAr }}</span>
                </div>
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">الرقم الضريبي للبائع:</span>
                    <span>{{ $sellerVat }}</span>
                </div>
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">السجل التجاري:</span>
                    <span>{{ $sellerCrn }}</span>
                </div>
                <div class="d-flex mb-2">
                    <span class="fw-bold me-2">عنوان البائع:</span>
                    <div style="font-size: 0.8rem;">
                        <div>@lang('Building No'): {{ $sellerBuilding }}</div>
                        <div>@lang('Street'): {{ $sellerStreet }}</div>
                        @if($sellerDistrict) <div>@lang('District'): {{ $sellerDistrict }}</div> @endif
                        @if($sellerCity) <div>@lang('City'): {{ $sellerCity }}</div> @endif
                        @if($sellerPostal) <div>@lang('Postal Code'): {{ $sellerPostal }}</div> @endif
                        @if($sellerAdditional) <div>@lang('Additional Number'): {{ $sellerAdditional }}</div> @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive mb-8">
            <table class="table table-row-bordered table-row-gray-300 align-middle">
                <thead class="bg-light">
                    <tr class="fw-bold fs-7 text-gray-800 text-uppercase">
                        <th class="ps-4">الوصف</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">السعر</th>
                        <th class="text-end pe-4">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-800">تعويض/تعديل مالي يخص الفاتورة المذكورة</span>
                                <span class="text-muted fs-8">Code: CN-ADJ</span>
                            </div>
                        </td>
                        <td class="text-center">1</td>
                        <td class="text-center text-danger">-{{ number_format($financial->amount, 2) }}</td>
                        <td class="text-end pe-4 text-danger fw-bold">-{{ number_format($financial->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="d-flex justify-content-end mb-10">
            <div class="w-100 w-md-250px fs-6">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">المجموع قبل الضريبة</span>
                    <span class="fw-bold text-gray-800">-{{ number_format($financial->amount / 1.15, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <span class="text-muted">ضريبة القيمة المضافة (15%)</span>
                    <span class="fw-bold text-gray-800">-{{ number_format($financial->amount - ($financial->amount /
                        1.15), 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <h3 class="fw-bolder">الإجمالي النهائي</h3>
                    <h3 class="text-danger fw-bolder">-{{ number_format($financial->amount, 2) }} ر.س</h3>
                </div>
            </div>
        </div>

        {{-- QR Code Placeholder --}}
        <div class="mt-20 border rounded p-10 bg-light text-center">
            @if(isset($qrCode))
            <img src="{{ $qrCode }}" alt="QR Code" style="width: 150px; height: 150px;">
            @else
            <p class="text-muted mb-0" style="font-size: 0.7rem;">QR Code (ZATCA Phase 2 Official)</p>
            @endif
        </div>

    </div>
</div>

<style>
    @media print {
        @page {
            size: auto;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0 !important;
            background: #fff !important;
        }

        .credit-note-paper {
            box-shadow: none !important;
            margin: 0 !important;
            max-width: 100% !important;
            border: none !important;
        }

        .container,
        .d-print-none,
        #kt_header,
        #kt_aside,
        #kt_footer,
        .toolbar {
            display: none !important;
        }

        .card-body {
            padding: 40px !important;
        }
    }
</style>
@endsection