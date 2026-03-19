<?php

namespace Core\B2B\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Core\Orders\Models\OrderSchedule;
use Core\Orders\Services\OrderSchedulesService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Core\Orders\Requests\OrderSchedulesRequest;
use Core\B2B\Helpers\B2BHelper;

class ScheduleController extends Controller
{
    use ApiResponse;

    public function __construct(protected OrderSchedulesService $orderSchedulesService)
    {
    }

    public function index()
    {
        B2BHelper::checkPermission('manage-scheduling-addresses');
        $context = B2BHelper::getCreationContext();
        $companyId = $context['company_id'];
        $branchId = $context['branch_id'];

        // Get day schedules (weekly recurring)
        $daySchedules = OrderSchedule::where('company_id', $companyId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('type', 'day')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get date schedules (specific dates)
        $dateSchedules = OrderSchedule::where('company_id', $companyId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('type', 'date')
            ->orderBy('receiver_date', 'asc')
            ->get();

        $title = trans('client.schedule');
        $description = trans('client.schedule_description');

        return view('b2b::web.pages.schedule', compact('daySchedules', 'dateSchedules', 'title', 'description'));
    }

    public function store(OrderSchedulesRequest $request)
    {
        B2BHelper::checkPermission('manage-scheduling-addresses');
        try {
            $data = $request->all();
            $companyId = B2BHelper::getB2BCompanyId();
            
            $data['company_id'] = $companyId;
            

            $data['note'] = $data['note'] ?? '';

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

            return $this->returnSuccessMessage(trans('client.schedule_created_success'));
        }
        catch (\Exception $e) {
            report($e);
            dd($e);
            return $this->returnErrorMessage(trans('client.schedule_creation_failed'));
        }
    }

    public function destroy($id)
    {
        B2BHelper::checkPermission('manage-scheduling-addresses');
        try {
            $context = B2BHelper::getCreationContext();
            $companyId = $context['company_id'];
            
            $schedule = OrderSchedule::where('company_id', $companyId)
                ->where('id', $id)
                ->firstOrFail();

            $schedule->delete();


            return $this->returnSuccessMessage(trans('client.schedule_deleted_success'));

        }
        catch (\Exception $e) {
            report($e);
            return $this->returnErrorMessage(trans('client.schedule_deletion_failed'));
        }
    }
}