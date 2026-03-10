@extends('admin::layouts.dashboard')


@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> {{ trans('Activity Log') }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.activity-log.model-history') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-search"></i> {{ trans('Model History') }}
                        </a>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info text-white p-2 rounded-circle mb-2"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ trans('Total Activities') }}</span>
                                    <span class="info-box-number">{{ number_format($stats['total_activities']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success text-white p-2 rounded-circle mb-2"><i class="fas fa-calendar-day"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ trans('Today') }}</span>
                                    <span class="info-box-number">{{ number_format($stats['today_activities']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning text-white p-2 rounded-circle mb-2"><i class="fas fa-calendar-week"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ trans('This Week') }}</span>
                                    <span class="info-box-number">{{ number_format($stats['this_week_activities']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary text-white p-2 rounded-circle mb-2"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ trans('This Month') }}</span>
                                    <span class="info-box-number">{{ number_format($stats['this_month_activities']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('dashboard.activity-log.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('Search') }}</label>
                                    <input type="text" name="search" class="form-control" 
                                           value="{{ $search }}" 
                                           placeholder="{{ trans('Search in description, log name, or user...') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ trans('Model Type') }}</label>
                                    <select name="subject_type" class="form-control">
                                        <option value="">{{ trans('All Types') }}</option>
                                        @foreach($subjectTypes as $type)
                                            <option value="{{ $type['value'] }}" 
                                                    {{ $subject_type == $type['value'] ? 'selected' : '' }}>
                                                {{ $type['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ trans('Event') }}</label>
                                    <select name="event" class="form-control">
                                        <option value="">{{ trans('All Events') }}</option>
                                        @foreach($events as $eventOption)
                                            <option value="{{ $eventOption }}" 
                                                    {{ $event == $eventOption ? 'selected' : '' }}>
                                                {{ ucfirst($eventOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ trans('User') }}</label>
                                    <select name="causer_id" class="form-control">
                                        <option value="">{{ trans('All Users') }}</option>
                                        @foreach($causers as $causer)
                                            <option value="{{ $causer['id'] }}" 
                                                    {{ $causer_id == $causer['id'] ? 'selected' : '' }}>
                                                {{ $causer['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>{{ trans('From Date') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $date_from }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>{{ trans('To Date') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $date_to }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('dashboard.activity-log.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Activities Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>{{ trans('ID') }}</th>
                                    <th>{{ trans('Description') }}</th>
                                    <th>{{ trans('Model') }}</th>
                                    <th>{{ trans('Event') }}</th>
                                    <th>{{ trans('User') }}</th>
                                    <th>{{ trans('Date') }}</th>
                                    <th>{{ trans('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->id }}</td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $activity->description }}">
                                                {{ $activity->description }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($activity->subject)
                                                <span class="badge bg-info">
                                                    {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ trans('Deleted') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity->event)
                                                <span class="badge badge-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'updated' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($activity->event) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ trans('N/A') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity->causer)
                                                <div>
                                                    <strong>{{ $activity->causer->fullname ?? $activity->causer->name ?? $activity->causer->email }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $activity->causer->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">{{ trans('System') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                                <br>
                                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('dashboard.activity-log.show', $activity->id) }}" 
                                               class="btn btn-sm btn-info" title="{{ trans('View Details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5>{{ trans('No activities found') }}</h5>
                                                <p class="text-muted">{{ trans('Try adjusting your search criteria') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $activities->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
        var deleteUrl = "{{ route('dashboard.activity-log.delete', '%s') }}"

</script>
@endpush
@push('css')
<style>
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 14px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
