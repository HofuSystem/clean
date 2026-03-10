@extends('admin::layouts.dashboard')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> {{ trans('Activity Details') }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.activity-log.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ trans('Back to List') }}
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ trans('Basic Information') }}</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ trans('ID') }}:</strong></td>
                                            <td>{{ $activity->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('Description') }}:</strong></td>
                                            <td>{{ $activity->description }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('Log Name') }}:</strong></td>
                                            <td>
                                                @if($activity->log_name)
                                                    <span class="badge badge-info">{{ $activity->log_name }}</span>
                                                @else
                                                    <span class="text-muted">{{ trans('N/A') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('Event') }}:</strong></td>
                                            <td>
                                                @if($activity->event)
                                                    <span class="badge badge-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'updated' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($activity->event) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ trans('N/A') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('Created At') }}:</strong></td>
                                            <td>
                                                {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                                <br>
                                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ trans('Subject Information') }}</h4>
                                </div>
                                <div class="card-body">
                                    @if($activity->subject)
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ trans('Model Type') }}:</strong></td>
                                                <td>
                                                    <span class="badge badge-primary">{{ class_basename($activity->subject_type) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ trans('Model ID') }}:</strong></td>
                                                <td>{{ $activity->subject_id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ trans('Model Status') }}:</strong></td>
                                                <td>
                                                    <span class="badge badge-success">{{ trans('Active') }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                      
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ trans('The subject model has been deleted or is no longer available.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ trans('User Information') }}</h4>
                                </div>
                                <div class="card-body">
                                    @if($activity->causer)
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ trans('User ID') }}:</strong></td>
                                                <td>{{ $activity->causer_id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ trans('Name') }}:</strong></td>
                                                <td>{{ $activity->causer->fullname ?? $activity->causer->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ trans('Email') }}:</strong></td>
                                                <td>{{ $activity->causer->email ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ trans('Type') }}:</strong></td>
                                                <td>
                                                    <span class="badge badge-info">{{ class_basename($activity->causer_type) }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            {{ trans('This activity was performed by the system or the user has been deleted.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ trans('Properties') }}</h4>
                                </div>
                                <div class="card-body">
                                    @if($activity->properties && count($activity->properties) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('Property') }}</th>
                                                        <th>{{ trans('Value') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activity->properties as $key => $value)
                                                        @if($key !== 'trace')
                                                            <tr>
                                                                <td><strong>{{ $key }}</strong></td>
                                                                <td>
                                                                    @if(is_array($value) || is_object($value))
                                                                        <pre class="mb-0" style="font-size: 0.8em;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            {{ trans('No properties available for this activity.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($activity->trace)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-code-branch"></i> {{ trans('Trace Information') }}
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @php
                                        // Handle trace - it might be an array (from cast) or JSON string
                                        $trace = is_array($activity->trace) ? $activity->trace : (is_string($activity->trace) ? json_decode($activity->trace, true) : []);
                                        $caller = $trace['caller'] ?? null;
                                        $traceFrames = $trace['trace'] ?? [];
                                    @endphp
                                    
                                    @if($caller)
                                        <div class="alert alert-primary">
                                            <h5><i class="fas fa-map-marker-alt"></i> {{ trans('Called From') }}:</h5>
                                            <p class="mb-1">
                                                <strong>{{ trans('Function') }}:</strong> 
                                                <code>{{ $caller['full_method'] ?? ($caller['class'] ? $caller['class'] . '::' . $caller['function'] : $caller['function']) }}</code>
                                            </p>
                                            <p class="mb-1">
                                                <strong>{{ trans('File') }}:</strong> 
                                                <code>{{ $caller['file'] ?? 'Unknown' }}</code>
                                            </p>
                                            <p class="mb-0">
                                                <strong>{{ trans('Line') }}:</strong> 
                                                <code>{{ $caller['line'] ?? 'Unknown' }}</code>
                                            </p>
                                        </div>
                                    @endif
                                    
                                    @if(count($traceFrames) > 0)
                                        <h5><i class="fas fa-list"></i> {{ trans('Stack Trace') }}:</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th style="width: 60px;">#</th>
                                                        <th>{{ trans('File') }}</th>
                                                        <th>{{ trans('Line') }}</th>
                                                        <th>{{ trans('Function') }}</th>
                                                        <th>{{ trans('Class') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($traceFrames as $frame)
                                                        <tr>
                                                            <td><span class="badge badge-secondary">{{ $frame['frame'] ?? '' }}</span></td>
                                                            <td>
                                                                <code style="font-size: 0.85em;">{{ $frame['file'] ?? 'Unknown' }}</code>
                                                            </td>
                                                            <td>
                                                                <span class="badge text-info">{{ $frame['line'] ?? 'N/A' }}</span>
                                                            </td>
                                                            <td>
                                                                <code>{{ $frame['function'] ?? 'Unknown' }}</code>
                                                            </td>
                                                            <td>
                                                                @if($frame['class'] ?? null)
                                                                    <span class="badge text-primary">{{ $frame['class'] }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
