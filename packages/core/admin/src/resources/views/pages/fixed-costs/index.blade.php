@extends('admin::layouts.dashboard')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y mx-auto">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ trans('Fixed Costs Management') }}</h5>
                            <a href="{{ route('dashboard.fixed-costs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ trans('Add New Fixed Cost') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ trans('Name') }}</th>
                                        <th>{{ trans('Description') }}</th>
                                        <th>{{ trans('Amount') }}</th>
                                        <th>{{ trans('Frequency') }}</th>
                                        <th>{{ trans('Monthly Amount') }}</th>
                                        <th>{{ trans('Date') }}</th>
                                        <th>{{ trans('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fixedCosts as $fixedCost)
                                        <tr>
                                            <td>{{ $fixedCost->name }}</td>
                                            <td>{{ Str::limit($fixedCost->description, 50) }}</td>
                                            <td class="text-end">{{ number_format($fixedCost->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($fixedCost->frequency) }}</span>
                                            </td>
                                                                                        <td class="text-end fw-bold">{{ number_format($fixedCost->monthly_amount, 2) }}</td>
                                            <td>{{ $fixedCost->date->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dashboard.fixed-costs.show', $fixedCost) }}"
                                                       class="btn btn-sm btn-info" title="{{ trans('View') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('dashboard.fixed-costs.edit', $fixedCost) }}"
                                                       class="btn btn-sm btn-warning" title="{{ trans('Edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('dashboard.fixed-costs.destroy', $fixedCost) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('{{ trans('Are you sure you want to delete this fixed cost?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('Delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">{{ trans('No fixed costs found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($fixedCosts->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $fixedCosts->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
@endpush
@push('js')
<script>
</script>
@endpush