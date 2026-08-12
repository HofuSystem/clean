<?php

namespace Core\Info\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Info\Models\CoverageNotification;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoverageNotificationsController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $title  = trans('Coverage Requests');
        $screen = 'coverage-notifications-index';
        $total  = CoverageNotification::count();

        return view('info::pages.coverage_notifications.list', compact('title', 'screen', 'total'));
    }

    public function dataTable(Request $request)
    {
        try {
            $draw = $request->input('draw');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $query = CoverageNotification::with(['user', 'city.translations', 'district.translations']);

            $recordsTotal = $query->count();
            $recordsFiltered = $recordsTotal;

            $records = $query->orderBy('created_at', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            $data = [];
            foreach ($records as $record) {
                // Find matching address for user & district/city
                $addressQuery = \Core\Users\Models\Address::where('user_id', $record->user_id);
                if ($record->district_id) {
                    $addressQuery->where('district_id', $record->district_id);
                } elseif ($record->city_id) {
                    $addressQuery->where('city_id', $record->city_id);
                }
                $address = $addressQuery->first();

                $actions = '<div class="d-flex justify-content-center">';
                if (auth('web')->user()->can('dashboard.coverage-notifications.delete')) {
                    $actions .= '<button class="btn-operation delete-item mx-1" data-href="' . route('dashboard.coverage-notifications.delete', ['id' => $record->id]) . '"><i class="fas fa-trash"></i></button>';
                }
                $actions .= '</div>';

                $data[] = [
                    'id'            => $record->id,
                    'user'          => $record->user?->fullname ?? '---------------------',
                    'phone'         => $record->user?->phone ?? '---------------------',
                    'city'          => $record->city?->name ?? '---------------------',
                    'district'      => $record->district?->name ?? '---------------------',
                    'address'       => $address ? ($address->location . ($address->description ? ' (' . $address->description . ')' : '')) : '---------------------',
                    'type'          => $record->type == 'expansion' ? trans('Expansion') : trans('Resume'),
                    'status'        => $record->status == 'pending' ? '<span class="badge bg-warning text-dark">' . trans('Pending') . '</span>' : '<span class="badge bg-success">' . trans('Notified') . '</span>',
                    'created_at'    => $record->created_at->format('Y-m-d H:i'),
                    'actions'       => $actions,
                ];
            }

            return $this->returnData(trans('data found'), [
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $record = CoverageNotification::findOrFail($id);
            $record->delete();
            DB::commit();
            return $this->returnSuccessMessage(trans('Record deleted successfully'));
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
