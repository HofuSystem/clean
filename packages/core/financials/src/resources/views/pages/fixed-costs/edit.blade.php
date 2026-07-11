@extends('admin::layouts.dashboard')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ trans('Edit Fixed Cost') }}: {{ $fixedCost->name }}</h5>
                            <a href="{{ route('dashboard.fixed-costs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ trans('Back to List') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('dashboard.fixed-costs.update', $fixedCost) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ trans('Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', $fixedCost->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">{{ trans('Amount') }} <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror"
                                               id="amount" name="amount" value="{{ old('amount', $fixedCost->amount) }}" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="frequency" class="form-label">{{ trans('Frequency') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                                            <option value="">{{ trans('Select Frequency') }}</option>
                                            <option value="monthly" {{ old('frequency', $fixedCost->frequency) == 'monthly' ? 'selected' : '' }}>{{ trans('Monthly') }}</option>
                                            <option value="quarterly" {{ old('frequency', $fixedCost->frequency) == 'quarterly' ? 'selected' : '' }}>{{ trans('Quarterly') }}</option>
                                            <option value="yearly" {{ old('frequency', $fixedCost->frequency) == 'yearly' ? 'selected' : '' }}>{{ trans('Yearly') }}</option>
                                        </select>
                                        @error('frequency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">{{ trans('Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror"
                                               id="date" name="date" value="{{ old('date', $fixedCost->date->format('Y-m-d')) }}" required>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ trans('Description') }}</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3">{{ old('description', $fixedCost->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('dashboard.fixed-costs.index') }}" class="btn btn-secondary">
                                    {{ trans('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ trans('Update Fixed Cost') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
