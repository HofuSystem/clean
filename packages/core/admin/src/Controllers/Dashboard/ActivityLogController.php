<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $title = trans('Activity Log');
        $screen = 'activity-log';
        
        // Get search parameters
        $search = $request->get('search');
        $subject_type = $request->get('subject_type');
        $event = $request->get('event');
        $causer_id = $request->get('causer_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        
        // Build query
        $query = Activity::with(['subject', 'causer'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('log_name', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('subject', function($subjectQuery) use ($search) {
                      $subjectQuery->where('id', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('causer', function($causerQuery) use ($search) {
                      $causerQuery->where('fullname', 'LIKE', '%' . $search . '%')
                                 ->orWhere('email', 'LIKE', '%' . $search . '%');
                  });
            });
        }
        
        if ($subject_type) {
            $query->where('subject_type', $subject_type);
        }
        
        if ($event) {
            $query->where('event', $event);
        }
        
        if ($causer_id) {
            $query->where('causer_id', $causer_id);
        }
        
        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }
        
        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }
        
        // Get paginated results
        $activities = $query->paginate(20)->withQueryString();
        
        // Get filter options
        $subjectTypes = Activity::select('subject_type')
            ->whereNotNull('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->filter()
            ->map(function($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });
        
        $events = Activity::select('event')
            ->whereNotNull('event')
            ->distinct()
            ->pluck('event')
            ->filter();
        
        $causers = Activity::with('causer')
            ->whereNotNull('causer_id')
            ->get()
            ->pluck('causer')
            ->filter()
            ->unique('id')
            ->map(function($causer) {
                return [
                    'id' => $causer->id,
                    'name' => $causer->fullname ?? $causer->name ?? $causer->email
                ];
            });
        
        // Get statistics
        $stats = [
            'total_activities' => Activity::count(),
            'today_activities' => Activity::whereDate('created_at', today())->count(),
            'this_week_activities' => Activity::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_activities' => Activity::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
        
        return view('admin::pages.activity-log.index', compact(
            'title',
            'screen',
            'activities',
            'subjectTypes',
            'events',
            'causers',
            'stats',
            'search',
            'subject_type',
            'event',
            'causer_id',
            'date_from',
            'date_to'
        ));
    }
    
    public function show($id)
    {
        $activity = Activity::with(['subject', 'causer'])->findOrFail($id);
        $title = trans('Activity Details');
        $screen = 'activity-log-details';
        
        return view('admin::pages.activity-log.show', compact('activity', 'title', 'screen'));
    }
    
    public function modelHistory(Request $request)
    {
        $title = trans('Model History');
        $screen = 'model-history';
        $subject_type = $request->get('subject_type');
        $subject_id = $request->get('subject_id');
        
        if (!$subject_type || !$subject_id) {
            return redirect()->back()->with('error', 'Subject type and ID are required');
        }
        
        // Get the model instance
        $model = $subject_type::find($subject_id);
        if (!$model) {
            return redirect()->back()->with('error', 'Model not found');
        }
        
        // Get all activities for this model
        $activities = Activity::where('subject_type', $subject_type)
            ->where('subject_id', $subject_id)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin::pages.activity-log.model-history', compact(
            'title',
            'screen',
            'activities',
            'model',
            'subject_type',
            'subject_id'
        ));
    }
}
