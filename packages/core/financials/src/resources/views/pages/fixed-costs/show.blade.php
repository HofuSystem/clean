@extends('admin::layouts.dashboard')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ trans('Fixed Cost Details') }}</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('dashboard.fixed-costs.edit', $fixedCost) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> {{ trans('Edit') }}
                                </a>
                                <a href="{{ route('dashboard.fixed-costs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ trans('Back to List') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="w-25">{{ trans('Name') }}:</th>
                                        <td>{{ $fixedCost->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('Description') }}:</th>
                                        <td>{{ $fixedCost->description ?: trans('No description provided') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('Amount') }}:</th>
                                        <td class="fw-bold text-primary">{{ number_format($fixedCost->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('Frequency') }}:</th>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($fixedCost->frequency) }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="w-25">{{ trans('Monthly Amount') }}:</th>
                                        <td class="fw-bold text-success">{{ number_format($fixedCost->monthly_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('Date') }}:</th>
                                        <td>{{ $fixedCost->date->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">{{ trans('Cost Breakdown') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-primary">{{ number_format($fixedCost->amount, 2) }}</h4>
                                                    <small class="text-muted">{{ trans('Original Amount') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-info">{{ ucfirst($fixedCost->frequency) }}</h4>
                                                    <small class="text-muted">{{ trans('Frequency') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-success">{{ number_format($fixedCost->monthly_amount, 2) }}</h4>
                                                    <small class="text-muted">{{ trans('Monthly Equivalent') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">{{ trans('Created') }}: {{ $fixedCost->created_at->format('Y-m-d H:i:s') }}</small>
                                        @if($fixedCost->updated_at != $fixedCost->created_at)
                                            <br><small class="text-muted">{{ trans('Last Updated') }}: {{ $fixedCost->updated_at->format('Y-m-d H:i:s') }}</small>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('dashboard.fixed-costs.destroy', $fixedCost) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ trans('Are you sure you want to delete this fixed cost?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> {{ trans('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
