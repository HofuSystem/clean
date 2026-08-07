@extends('admin::layouts.dashboard')

@section('content')
    <!--begin::Content-->
    <div class="container-fluid flex-grow-1 container-p-y">

        <!--begin::Toolbar-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack mb-3">
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
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.index') }}" class="text-muted text-hover-primary">@lang('Home')</a>
                    </li>
                    <li class="breadcrumb-item text-muted">@lang("Financials")</li>
                    <li class="breadcrumb-item text-dark">{{ $title }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Toolbar-->

        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Card-->
            <div class="card shadow-sm border-0">
                <!--begin::Card header-->
                <div class="card-header row align-items-center py-3">
                    <div class="card-title col-md-6 mb-2 mb-md-0">
                        <h5 class="card-title mb-0 text-dark fw-bolder">{{ trans('Fixed Costs Management') }}</h5>
                    </div>
                    <div class="card-toolbar col-md-6 d-flex justify-content-end align-items-center gap-3">
                        <div class="border border-dashed border-primary text-primary rounded px-3 py-1">
                            <span class="fw-bolder fs-5">{{ $fixedCosts->total() }}</span>
                            <i class="fas fa-calculator ms-1"></i>
                            <small class="fw-bold">@lang('total')</small>
                        </div>
                        <a href="{{ route('dashboard.fixed-costs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> {{ trans('Add New Fixed Cost') }}
                        </a>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center p-3 mb-4">
                            <i class="fas fa-check-circle me-2 fs-4"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table align-middle text-center table-row-dashed fs-6 gy-5 table-hover">
                            <thead class="table-primary">
                                <tr class="text-center text-white fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="text-center">{{ trans('Name') }}</th>
                                    <th class="text-center">{{ trans('Description') }}</th>
                                    <th class="text-center">{{ trans('Amount') }}</th>
                                    <th class="text-center">{{ trans('Frequency') }}</th>
                                    <th class="text-center">{{ trans('Monthly Amount') }}</th>
                                    <th class="text-center">{{ trans('Date') }}</th>
                                    <th class="text-center">{{ trans('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-bold">
                                @forelse($fixedCosts as $fixedCost)
                                    <tr>
                                        <td class="fw-bolder text-dark">{{ $fixedCost->name }}</td>
                                        <td>{{ Str::limit($fixedCost->description, 50) ?: '---' }}</td>
                                        <td class="fw-bold text-primary">{{ number_format($fixedCost->amount, 2) }} {{ trans('SAR') }}</td>
                                        <td>
                                            <span class="badge bg-label-info px-3 py-2 fs-7">{{ trans(ucfirst($fixedCost->frequency)) }}</span>
                                        </td>
                                        <td class="fw-bold text-success">{{ number_format($fixedCost->monthly_amount, 2) }} {{ trans('SAR') }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $fixedCost->date->format('Y-m-d') }}</span></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('dashboard.fixed-costs.show', $fixedCost) }}"
                                                   class="btn btn-icon btn-sm btn-light-info" title="{{ trans('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('dashboard.fixed-costs.edit', $fixedCost) }}"
                                                   class="btn btn-icon btn-sm btn-light-warning" title="{{ trans('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('dashboard.fixed-costs.destroy', $fixedCost) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ trans('Are you sure you want to delete this fixed cost?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm btn-light-danger" title="{{ trans('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted fs-6">{{ trans('No fixed costs found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($fixedCosts->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $fixedCosts->links() }}
                        </div>
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Content-->
@endsection
@push('css')
@endpush
@push('js')
@endpush