<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Core\Orders\Models\OrderSchedule;
use Core\Orders\Services\OrderSchedulesService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Core\Orders\Requests\OrderSchedulesRequest;

class ScheduleController extends Controller
{
    use ApiResponse;

    public function __construct(protected OrderSchedulesService $orderSchedulesService) {}

    public function index()
    {
        $user = Auth::user();

        // Get day schedules (weekly recurring)
        $daySchedules = OrderSchedule::where('client_id', $user->id)
            ->where('type', 'day')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get date schedules (specific dates)
        $dateSchedules = OrderSchedule::where('client_id', $user->id)
            ->where('type', 'date')
            ->orderBy('receiver_date', 'asc')
            ->get();
        
        // Get user addresses for the date schedule modal
        $addresses = \Core\Users\Models\Address::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();
        
        return view('client.schedule', compact('daySchedules', 'dateSchedules', 'addresses'));
    }

    public function store(OrderSchedulesRequest $request)
    {
        try {
            $data = $request->all();
            $data['client_id'] = Auth::id();
            $data['note'] = $data['service_type'] ?? null;
            $data['note'] .= " - " . $data['notes'] ?? null;
            
            // For day schedules, set dates to null
            if ($data['type'] === 'day') {
                $data['receiver_date'] = null;
                $data['delivery_date'] = null;
            }
            
            // For date schedules, set days to null
            if ($data['type'] === 'date') {
                $data['receiver_day'] = null;
                $data['delivery_day'] = null;
            }

            $this->orderSchedulesService->storeOrUpdate($data);
            
            return redirect()->route('client.schedule.index')
                ->with('success', trans('client.schedule_created_success'));
                
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.schedule_creation_failed')])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = OrderSchedule::where('client_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();
            
            $schedule->delete();
            
            return redirect()->route('client.schedule.index')
                ->with('success', trans('client.schedule_deleted_success'));
                
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.schedule_deletion_failed')]);
        }
    }
}
