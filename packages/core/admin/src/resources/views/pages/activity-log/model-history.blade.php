@extends('admin::layouts.dashboard')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> {{ trans('Model History') }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.activity-log.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ trans('Back to Activity Log') }}
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Model Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> {{ trans('Model Information') }}</h5>
                                <p class="mb-0">
                                    <strong>{{ trans('Model Type') }}:</strong> 
                                    <span class="badge badge-primary">{{ class_basename($subject_type) }}</span>
                                    <strong>{{ trans('Model ID') }}:</strong> 
                                    <span class="badge badge-secondary">{{ $subject_id }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('dashboard.activity-log.model-history') }}" class="mb-4">
                        <input type="hidden" name="subject_type" value="{{ $subject_type }}">
                        <input type="hidden" name="subject_id" value="{{ $subject_id }}">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('Search') }}</label>
                                    <input type="text" name="search" class="form-control" 
                                           value="{{ request('search') }}" 
                                           placeholder="{{ trans('Search in description...') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ trans('Event') }}</label>
                                    <select name="event" class="form-control">
                                        <option value="">{{ trans('All Events') }}</option>
                                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>{{ trans('Created') }}</option>
                                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>{{ trans('Updated') }}</option>
                                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>{{ trans('Deleted') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ trans('From Date') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ trans('To Date') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('dashboard.activity-log.model-history', ['subject_type' => $subject_type, 'subject_id' => $subject_id]) }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Activities Timeline -->
                    <div class="timeline">
                        @forelse($activities as $activity)
                            <div class="time-label">
                                <span class="bg-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'updated' ? 'warning' : 'danger') }}">
                                    {{ $activity->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <div>
                                <i class="fas fa-{{ $activity->event == 'created' ? 'plus' : ($activity->event == 'updated' ? 'edit' : 'trash') }} bg-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'updated' ? 'warning' : 'danger') }}"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $activity->created_at->format('H:i:s') }}
                                    </span>
                                    <h3 class="timeline-header">
                                        <strong>{{ $activity->description }}</strong>
                                        @if($activity->event)
                                            <span class="badge badge-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'updated' ? 'warning' : 'danger') }} ml-2">
                                                {{ ucfirst($activity->event) }}
                                            </span>
                                        @endif
                                    </h3>
                                    
                                    <div class="timeline-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>{{ trans('User') }}:</h6>
                                                <p>
                                                    @if($activity->causer)
                                                        <strong>{{ $activity->causer->fullname ?? $activity->causer->name ?? $activity->causer->email }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $activity->causer->email }}</small>
                                                    @else
                                                        <span class="text-muted">{{ trans('System') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>{{ trans('Time') }}:</h6>
                                                <p>
                                                    {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                                    <br>
                                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        @if($activity->properties && count($activity->properties) > 0)
                                            <div class="mt-3">
                                                <h6>{{ trans('Changes') }}:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ trans('Property') }}</th>
                                                                <th>{{ trans('Old Value') }}</th>
                                                                <th>{{ trans('New Value') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($activity->properties as $key => $value)
                                                                <tr>
                                                                    <td><strong>{{ $key }}</strong></td>
                                                                    <td>
                                                                        @if(isset($value['old']))
                                                                            @if(is_array($value['old']) || is_object($value['old']))
                                                                                <pre class="mb-0" style="font-size: 0.7em;">{{ json_encode($value['old'], JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                <span class="text-muted">{{ $value['old'] }}</span>
                                                                            @endif
                                                                        @else
                                                                            <span class="text-muted">{{ trans('N/A') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if(isset($value['attributes']))
                                                                            @if(is_array($value['attributes']) || is_object($value['attributes']))
                                                                                <pre class="mb-0" style="font-size: 0.7em;">{{ json_encode($value['attributes'], JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                <span class="text-success">{{ $value['attributes'] }}</span>
                                                                            @endif
                                                                        @else
                                                                            @if(is_array($value) || is_object($value))
                                                                                <pre class="mb-0" style="font-size: 0.7em;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                <span class="text-success">{{ $value }}</span>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="timeline-footer">
                                        <a href="{{ route('dashboard.activity-log.show', $activity->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> {{ trans('View Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="time-label">
                                <span class="bg-secondary">{{ trans('No Activities') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-inbox bg-secondary"></i>
                                <div class="timeline-item">
                                    <div class="timeline-body text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5>{{ trans('No activities found') }}</h5>
                                        <p class="text-muted">{{ trans('This model has no recorded activities yet.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 40px;
    width: 2px;
    margin-left: -1.5px;
    background-color: #e9ecef;
}

.timeline > li {
    position: relative;
    margin-bottom: 50px;
    min-height: 50px;
}

.timeline > li:before,
.timeline > li:after {
    content: " ";
    display: table;
}

.timeline > li:after {
    clear: both;
}

.timeline > li .timeline-panel {
    position: relative;
    width: calc(100% - 90px);
    float: right;
    border: 1px solid #d2d6de;
    background: #fff;
    border-radius: 0.25rem;
    padding: 20px;
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
}

.timeline > li .timeline-badge {
    color: #fff;
    width: 50px;
    height: 50px;
    line-height: 50px;
    font-size: 1.4em;
    text-align: center;
    position: absolute;
    top: 16px;
    left: 15px;
    margin-right: -25px;
    background-color: #6c757d;
    z-index: 100;
    border-radius: 50%;
}

.timeline > li.timeline-inverted > .timeline-panel {
    float: right;
}

.timeline > li.timeline-inverted > .timeline-panel:before {
    border-left-width: 0;
    border-right-width: 15px;
    left: -15px;
    right: auto;
}

.timeline > li.timeline-inverted > .timeline-panel:after {
    border-left-width: 0;
    border-right-width: 14px;
    left: -14px;
    right: auto;
}

.timeline-badge.primary {
    background-color: #007bff !important;
}

.timeline-badge.success {
    background-color: #28a745 !important;
}

.timeline-badge.warning {
    background-color: #ffc107 !important;
}

.timeline-badge.danger {
    background-color: #dc3545 !important;
}

.timeline-badge.info {
    background-color: #17a2b8 !important;
}

.timeline-title {
    margin-top: 0;
    color: inherit;
}

.timeline-body > p,
.timeline-body > ul {
    margin-bottom: 0;
}

.timeline-body > p + p {
    margin-top: 5px;
}

.timeline-item {
    background: #fff;
    border: 1px solid #d2d6de;
    border-radius: 0.25rem;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
}

.timeline-header {
    margin: 0 0 10px 0;
    color: #495057;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 10px;
}

.timeline-body {
    padding: 10px 0;
}

.timeline-footer {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.time-label {
    position: relative;
    display: block;
    margin: 0 0 20px 0;
}

.time-label > span {
    display: inline-block;
    padding: 5px 10px;
    color: #fff;
    border-radius: 0.25rem;
    font-weight: 600;
}

.time {
    color: #999;
    font-size: 0.875rem;
}

.timeline > li > i {
    position: absolute;
    left: 15px;
    top: 0;
    background: #adb5bd;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    text-align: center;
    line-height: 30px;
    font-size: 15px;
    color: #fff;
}

.timeline > li > .timeline-item {
    margin-left: 60px;
    background: #fff;
    color: #666;
    padding: 0;
    position: relative;
    border-radius: 0.25rem;
    border: 1px solid #d2d6de;
}

.timeline > li > .timeline-item > .time {
    color: #999;
    float: right;
    font-size: 0.875rem;
    padding: 10px;
}

.timeline > li > .timeline-item > .timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #eee;
    padding: 10px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline > li > .timeline-item > .timeline-body,
.timeline > li > .timeline-item > .timeline-footer {
    padding: 10px;
}

.timeline > li > .timeline-item > .timeline-footer > a {
    color: #337ab7;
}
</style>
@endpush
