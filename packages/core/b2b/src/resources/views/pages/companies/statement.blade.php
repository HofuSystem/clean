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
$currency = trans('SAR');
@endphp

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    {{-- ── Toolbar ── --}}
    <div class="toolbar my-3" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack flex-wrap gap-2">

            {{-- Breadcrumb --}}
            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-2">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-4 my-1">{{ $title }}</h1>
                <span class="h-20px border-gray-200 border-start mx-3"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted"><a href=""
                            class="text-muted text-hover-primary">@lang('Home')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.companies.index') }}"
                            class="text-muted text-hover-primary">@lang('companies')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-dark">{{ $company->fullname }}</li>
                </ul>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">

                {{-- Company Switcher --}}
                <div class="me-3">
                    <select class="form-select form-select-sm form-select-solid"
                        onchange="window.location.href=this.value">
                        @foreach($companies as $comp)
                        <option value="{{ route('dashboard.company-statement.show', $comp->id) }}" {{ $comp->id ==
                            $company->id ? 'selected' : '' }}>
                            {{ $comp->fullname }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Export PDF --}}
                <button class="btn btn-sm btn-light-danger" id="btn-export-pdf" onclick="window.print()">
                    <i class="fas fa-file-pdf me-1"></i> @lang('PDF')
                </button>

                {{-- Export XLSX --}}
                <a href="{{ route('dashboard.company-statement.export-xlsx', $company->id) }}"
                    class="btn btn-sm btn-light-success">
                    <i class="fas fa-file-excel me-1"></i> @lang('XLSX')
                </a>
            </div>
        </div>
    </div>

    {{-- ── Sub-Toolbar for Actions ── --}}
    <div class="container-fluid mb-5">
        <div class="d-flex justify-content-end gap-3">
            {{-- Add Owed --}}
            <button class="btn btn-danger" id="btn-owed" onclick="openForm('owed')">
                <i class="fas fa-plus-circle me-1"></i> @lang('Add Owed (Credit)')
            </button>

            {{-- Add Paid --}}
            <button class="btn btn-success" id="btn-paid" onclick="openForm('paid')">
                <i class="fas fa-plus-circle me-1"></i> @lang('Add Paid (Debit)')
            </button>
        </div>
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            {{-- ── Slide-in Form Panel ── --}}
            <div id="action-panel" class="card mb-5 d-none">
                <div class="card-body position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                        onclick="closeForm()"></button>

                    {{-- OWED FORM --}}
                    <div id="owed-form-wrapper" class="d-none">
                        <h5 class="fw-bold text-danger mb-4">
                            <i class="fas fa-file-invoice-dollar me-2 text-danger"></i> @lang('Add Owed (Credit)')
                        </h5>
                        <form id="owed-form" class="row g-3">
                            @csrf

                            <input type="hidden" name="company_id" value="{{ $company->id }}">
                            <input type="hidden" name="type" value="owed">
                            <div class="col-md-3">
                                <label class="form-label required">@lang('Date')</label>
                                <input type="date" name="collection_date" class="form-control" required
                                    value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">@lang('Reference')</label>
                                <input type="text" name="reference_id" class="form-control bg-ranger border-danger"
                                    value="{{ $nextOwedRefrence }}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label required">@lang('Amount (SAR)')</label>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Associated Invoice (Reference)')</label>
                                <input type="text" name="note" class="form-control" placeholder="e.g. INV-2026...">
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-danger px-8">
                                    <i class="fas fa-save me-2"></i> @lang('Save and Out')
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- PAID FORM --}}
                    <div id="paid-form-wrapper" class="d-none">
                        <h5 class="fw-bold text-success mb-4">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i> @lang('Add Bank Transfer')
                        </h5>
                        <form id="paid-form" class="row g-3">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $company->id }}">
                            <input type="hidden" name="type" value="paid">
                            <div class="col-md-3">
                                <label class="form-label required">@lang('Date')</label>
                                <input type="date" name="collection_date" class="form-control" required
                                    value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">@lang('Reference')</label>
                                <input type="text" name="reference_id" class="form-control" placeholder="">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label required">@lang('Amount (SAR)')</label>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">@lang('Bank Name / Notes')</label>
                                <input type="text" name="note" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">@lang('Attach Document (PDF)')</label>
                                <input type="file" name="attachment" class="form-control" accept=".pdf">
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-8">
                                    <i class="fas fa-save me-2"></i> @lang('Save and Insert')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── Statement Document ── --}}
            <div id="statement-doc" class="card shadow-sm" style="max-width:900px;margin:0 auto 60px;">
                <div class="card-body p-0">
                    <div class="statement-paper p-5"
                        style="font-family:'Tajawal','Segoe UI',sans-serif; direction:rtl;">

                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                            <div style="text-align:right;">
                                <h2 class="fw-bold mb-0" style="font-size:1.5rem;">@lang('Customer Account Statement')
                                </h2>
                                <p class="text-muted mb-0"
                                    style="letter-spacing:3px;font-size:.7rem;text-transform:uppercase;">STATEMENT OF
                                    ACCOUNT</p>
                                <p class="mb-0 mt-2 fw-bold" style="font-size:1rem;">{{ $sellerNameAr }}</p>
                                <p class="mb-1 text-muted" style="font-size:0.85rem;">{{ $sellerName }}</p>
                                <p class="mb-0 text-muted" style="font-size:.8rem;">@lang('VAT Number:'): {{ $sellerVat }}</p>
                                <p class="mb-0 text-muted" style="font-size:.8rem;">@lang('Commercial Registration:'): {{ $sellerCrn }}</p>
                                <div class="mb-0 text-muted" style="font-size:.7rem;">
                                    <div>@lang('Building No'): {{ $sellerBuilding }}</div>
                                    <div>@lang('Street'): {{ $sellerStreet }}</div>
                                    @if($sellerDistrict) <div>@lang('District'): {{ $sellerDistrict }}</div> @endif
                                    @if($sellerCity) <div>@lang('City'): {{ $sellerCity }}</div> @endif
                                    @if($sellerPostal) <div>@lang('Postal Code'): {{ $sellerPostal }}</div> @endif
                                    @if($sellerAdditional) <div>@lang('Additional Number'): {{ $sellerAdditional }}</div> @endif
                                </div>
                            </div>
                            @if($sellerLogo)
                            <img src="{{ asset($sellerLogo) }}" alt="{{ $sellerName }}"
                                style="height:70px;object-fit:contain;">
                            @else
                            <div
                                style="width:80px;height:80px;background:#00AEEF;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <span style="color:#fff;font-weight:700;font-size:1rem;">CS</span>
                            </div>
                            @endif
                        </div>

                        {{-- Meta info row --}}
                        <div class="d-flex justify-content-between align-items-start mb-4 gap-4">

                            {{-- Issue info box --}}
                            <div class="border rounded p-3" style="min-width:200px;font-size:.82rem;line-height:1.9;">
                                <div class="d-flex justify-content-between gap-4">
                                    <span class="text-muted">@lang('Issue Date:'):</span>
                                    <strong>{{ now()->format('Y-m-d') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between gap-4">
                                    <span class="text-muted">@lang('Currency:'):</span>
                                    <strong>{{ $currency }}</strong>
                                </div>

                            </div>

                            {{-- Billed to box --}}
                            <div class="border rounded p-3 text-end"
                                style="min-width:260px;font-size:.82rem;line-height:1.9;background:#f9f9f9;">
                                <p class="text-muted mb-1"
                                    style="font-size:.65rem;letter-spacing:2px;text-transform:uppercase;">@lang('BILLED TO / Customer')</p>
                                <p class="fw-bold mb-1" style="font-size:1.1rem;">{{ $company->name_ar ?: $company->fullname }}</p>
                                <p class="mb-1 text-muted" style="font-size:0.85rem;">{{ $company->name_en }}</p>
                                @if($company->tax_number)
                                <p class="mb-0 text-muted">@lang('VAT Number:'): {{ $company->tax_number }}</p>
                                @endif
                                @if($company->commercial_registration)
                                <p class="mb-0 text-muted">@lang('Reference Register:'): {{ $company->commercial_registration }}</p>
                                @endif
                                @if($contract)
                                <p class="mb-0 text-muted">@lang('Reference Contract:'): {{ $contract->title }}</p>
                                @endif
                                <div class="mb-0 text-muted" style="font-size:.75rem;">
                                    <div>@lang('Building No'): {{ $company->building_no }}</div>
                                    <div>@lang('Street'): {{ $company->street_name }}</div>
                                    @if($company->district) <div>@lang('District'): {{ $company->district->name }}</div> @endif
                                    @if($company->city) <div>@lang('City'): {{ $company->city->name }}</div> @endif
                                    @if($company->postal_code) <div>@lang('Postal Code'): {{ $company->postal_code }}</div> @endif
                                    @if($company->additional_number) <div>@lang('Additional Number'): {{ $company->additional_number }}</div> @endif
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" style="font-size:.83rem;">
                                <thead style="background:#f3f3f3;">
                                    <tr>
                                        <th class="text-dark py-2">التاريخ <br> Date</th>
                                        <th class="text-dark py-2">المرجع <br> Reference</th>
                                        <th class="text-dark py-2">الوصف <br> Description</th>
                                        <th class="py-2 text-danger">المدين <br> Debit</th>
                                        <th class="py-2 text-success">الدائن <br> Credit</th>
                                        <th class="text-dark py-2">الرصيد <br> Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $row)
                                    <tr>
                                        <td>{{ $row['date_rendered'] }}</td>
                                        <td>{{ $row['reference_id'] }}</td>
                                        <td class="text-end">
                                            @if(isset($row['url']))
                                            <a href="{{ $row['url'] }}" target="_blank" class="text-primary fw-bold">
                                                {{ $row['note'] ?? trans($row['type']) }}
                                            </a>
                                            @else
                                            {{ $row['note'] ?? trans($row['type']) }}
                                            @endif

                                            @if($row['attachment'])
                                            <a href="{{ asset('storage/' . $row['attachment']) }}" target="_blank"
                                                class="ms-2 text-primary" title="@lang('View Attachment')">
                                                <i class="fas fa-paperclip"></i>
                                            </a>
                                            @endif

                                            @if($row['type'] === 'invoice')
                                            <a href="{{ route('dashboard.electronic-invoices.show', $row['id']) }}"
                                                target="_blank" class="ms-2 text-dark" title="@lang('Print Invoice')">
                                                فاتورة ضريبية عن طلب تسليم
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @elseif($row['type'] === 'owed')
                                            <a href="{{ route('dashboard.company-statement.print-credit-note', ['companyId' => $company->id, 'financialId' => $row['id']]) }}"
                                                target="_blank" class="ms-2 text-dark"
                                                title="@lang('Print Credit Note')">
                                                إشعار دائن ضريبي
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td class="text-danger fw-semibold">
                                            {{ $row['debit'] ? number_format($row['debit'], 2) : '-' }}
                                        </td>
                                        <td class="text-success fw-semibold">
                                            {{ $row['credit'] ? number_format($row['credit'], 2) : '-' }}
                                        </td>
                                        <td
                                            class="{{ $row['balance'] > 0 ? 'text-danger' : ($row['balance'] < 0 ? 'text-success' : 'text-muted') }} fw-bold">
                                            {{ number_format(abs($row['balance']), 2) }}
                                            <br>
                                            <small style="font-size:.65rem;">
                                                @if($row['balance'] > 0) @lang('(Debit)') @elseif($row['balance'] < 0)
                                                    @lang('(Credit)') @else — @endif </small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-muted py-4">@lang('No financial records found')</td>
                                    </tr>
                                    @endforelse

                                    {{-- Totals Row --}}
                                    <tr style="background:#f9f9f9;font-weight:bold;">
                                        <td colspan="3" class="text-start">(Totals) المجاميع</td>
                                        <td class="text-danger">{{ number_format($totalsOwed, 2) }}</td>
                                        <td class="text-success">{{ number_format($totalsPaid, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Final Balance --}}
                        <div class="d-flex justify-content-between align-items-start mt-3">
                            <div class="border rounded p-3 text-center" style="min-width:200px;">
                                <p class="mb-1 fw-bold" style="font-size:1.4rem;"
                                    class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format(abs($balance), 2) }}
                                </p>
                                @if($balance > 0)
                                <span class="badge bg-danger">@lang('Required from you')</span>
                                @elseif($balance < 0) <span class="badge bg-success">@lang('Required for you')</span>
                                    @else
                                    <span class="badge bg-secondary">@lang('Settled')</span>
                                    @endif
                                    <p class="mt-1 mb-0 fw-semibold text-muted" style="font-size:.75rem;">@lang('Final
                                        Outstanding Balance')</p>
                            </div>
                            <div class="d-flex gap-5 pt-3" style="font-size:.8rem;text-align:center;">
                                <div style="min-width:140px;border-top:1px solid #aaa;padding-top:5px;">
                                    @lang('Company Stamp')
                                </div>
                                <div style="min-width:140px;border-top:1px solid #aaa;padding-top:5px;">
                                    @lang('Authorized Sign')
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="text-center mt-5 pt-3 border-top" style="font-size:.72rem;color:#999;">
                            <p class="mb-0">@lang('An approved document automatically extracted from the financial
                                system.')</p>
                            <p class="mb-0">@lang('Powered by Hofu System - With technology we create mastery')</p>
                        </div>

                    </div>{{-- /statement-paper --}}
                </div>
            </div>{{-- /statement-doc --}}

        </div>
    </div>
</div>

{{-- Alert placeholder (print hidden) --}}
<div id="form-alert" class="alert d-none mx-4" role="alert"></div>

@endsection

@push('css')
<style>
    /* ── Panel animation ── */
    #action-panel {
        transition: all .3s ease;
        border-left: 4px solid transparent;
    }

    #action-panel.owed {
        border-left-color: #f1416c !important;
    }

    #action-panel.paid {
        border-left-color: #50cd89 !important;
    }

    /* ── Statement paper look ── */
    .statement-paper {
        background: #fff;
    }

    /* ── Print styles ── */
    @media print {

        #kt_toolbar,
        #action-panel,
        .btn,
        nav,
        aside,
        #kt_aside,
        #kt_header,
        #kt_footer {
            display: none !important;
        }

        #statement-doc {
            box-shadow: none !important;
            max-width: 100% !important;
            margin: 0 !important;
        }

        .statement-paper {
            padding: 10mm !important;
        }

        body {
            background: #fff !important;
        }
    }
</style>
@endpush

@push('js')
<script>
    function openForm(type) {
        const panel = document.getElementById('action-panel');
        const owedW = document.getElementById('owed-form-wrapper');
        const paidW = document.getElementById('paid-form-wrapper');

        panel.classList.remove('d-none', 'owed', 'paid');
        owedW.classList.add('d-none');
        paidW.classList.add('d-none');

        if (type === 'owed') {
            owedW.classList.remove('d-none');
            panel.classList.add('owed');
        } else {
            paidW.classList.remove('d-none');
            panel.classList.add('paid');
        }

        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function closeForm() {
        document.getElementById('action-panel').classList.add('d-none');
    }

    function handleFormSubmit(formId, url) {
        const form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const alertBox = document.getElementById('form-alert');

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status) {
                        alertBox.className = 'alert alert-success mx-4';
                        alertBox.textContent = data.message || 'تم الحفظ بنجاح';
                        alertBox.classList.remove('d-none');
                        form.reset();
                        closeForm();
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        const errors = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message || 'حدث خطأ');
                        alertBox.className = 'alert alert-danger mx-4';
                        alertBox.textContent = errors;
                        alertBox.classList.remove('d-none');
                    }
                })
                .catch(() => {
                    alertBox.className = 'alert alert-danger mx-4';
                    alertBox.textContent = 'حدث خطأ في الاتصال بالخادم.';
                    alertBox.classList.remove('d-none');
                });
        });
    }

    handleFormSubmit('owed-form', "{{ route('dashboard.company-statement.add-owed', $company->id) }}");
    handleFormSubmit('paid-form', "{{ route('dashboard.company-statement.add-paid', $company->id) }}");
</script>
@endpush