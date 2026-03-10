<?php
namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class GlobalModelObserver
{
    /**
     * Get trace information from the call stack
     */
    protected function getTraceInfo(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
        $traceInfo = [];
        
        // Skip the first few frames (this method, activity log methods, etc.)
        // Start from frame 4 to get the actual caller
        $relevantFrames = array_slice($trace, 4, 10);
        
        foreach ($relevantFrames as $index => $frame) {
            $file = $frame['file'] ?? 'Unknown';
            $line = $frame['line'] ?? 0;
            $function = $frame['function'] ?? 'Unknown';
            $class = $frame['class'] ?? null;
            $type = $frame['type'] ?? '';
            
            // Skip vendor files and framework files to focus on application code
            if (strpos($file, '/vendor/') !== false || strpos($file, '/framework/') !== false) {
                continue;
            }
            
            // Get relative path from project root
            $relativePath = str_replace(base_path() . '/', '', $file);
            
            $traceInfo[] = [
                'frame' => $index + 1,
                'file' => $relativePath,
                'line' => $line,
                'function' => $function,
                'class' => $class ? class_basename($class) : null,
                'full_method' => $class ? $class . $type . $function : $function,
            ];
            
            // Limit to first 5 relevant frames
            if (count($traceInfo) >= 5) {
                break;
            }
        }
        
        return [
            'trace' => $traceInfo,
            'caller' => $traceInfo[0] ?? null,
        ];
    }

    public function created(Model $model)
    {
        $traceInfo = $this->getTraceInfo();
        $properties = [
            'attributes' => $model->getAttributes(),
        ];
        
        activity()
            ->performedOn($model)
            ->withProperties($properties)
            ->tap(function (Activity $activity) use ($traceInfo) {
                $activity->trace = $traceInfo;
            })
            ->log('Model created');
    }

    public function updated(Model $model)
    {
        $traceInfo = $this->getTraceInfo();
        $properties = [
            'old' => $model->getOriginal(),
            'new' => $model->getChanges(),
        ];
        
        activity()
            ->performedOn($model)
            ->withProperties($properties)
            ->tap(function (Activity $activity) use ($traceInfo) {
                $activity->trace = $traceInfo;
            })
            ->log('Model updated');
    }

    public function deleted(Model $model)
    {
        $traceInfo = $this->getTraceInfo();
        $properties = [
            'attributes' => $model->getAttributes(),
        ];
        
        activity()
            ->performedOn($model)
            ->withProperties($properties)
            ->tap(function (Activity $activity) use ($traceInfo) {
                $activity->trace = $traceInfo;
            })
            ->log('Model deleted');
    }

    public function restored(Model $model)
    {
        $traceInfo = $this->getTraceInfo();
        $properties = [
            'attributes' => $model->getAttributes(),
        ];
        
        activity()
            ->performedOn($model)
            ->withProperties($properties)
            ->tap(function (Activity $activity) use ($traceInfo) {
                $activity->trace = $traceInfo;
            })
            ->log('Model restored');
    }

    public function forceDeleted(Model $model)
    {
        $traceInfo = $this->getTraceInfo();
        $properties = [
            'attributes' => $model->getAttributes(),
        ];
        
        activity()
            ->performedOn($model)
            ->withProperties($properties)
            ->tap(function (Activity $activity) use ($traceInfo) {
                $activity->trace = $traceInfo;
            })
            ->log('Model force deleted');
    }
}