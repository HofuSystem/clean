<?php

namespace Core\Orders\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Orders\Models\Cart;
use Core\Orders\Services\CartFollowUpsService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartFollowUpsController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CartFollowUpsService $cartFollowUpsService,
        protected UsersService         $usersService
    ) {}

    /**
     * List all follow-ups (filterable).
     */
    public function index(Request $request)
    {
        $title   = trans('Follow Ups');
        $screen  = 'cart-follow-ups-index';

        $userIds = DB::table('cart_follow_ups')->whereNotNull('user_id')->distinct()->pluck('user_id');
        $users   = !empty($userIds) ? DB::table('users')->select('id', 'fullname', 'phone')->whereIn('id', $userIds)->get() : collect();

        $adminIds = DB::table('cart_follow_ups')->whereNotNull('admin_id')->distinct()->pluck('admin_id');
        $admins  = !empty($adminIds) ? DB::table('users')->select('id', 'fullname', 'phone')->whereIn('id', $adminIds)->get() : collect();

        $total   = $this->cartFollowUpsService->totalCount();
        $statuses = ['pending', 'sale', 'no_answer', 'not_interested'];

        return view('orders::pages.cart-follow-ups.list',
            compact('title', 'screen', 'users', 'admins', 'total', 'statuses'));
    }

    /**
     * DataTable AJAX.
     */
    public function dataTable(Request $request)
    {
        try {
            $data = $this->cartFollowUpsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    /**
     * Store a new follow-up for a cart (AJAX call from cart show page).
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'cart_id' => 'required|exists:carts,id',
                'phone'   => 'nullable|string|max:20',
                'notes'   => 'nullable|string|max:500',
            ]);

            $followUp = $this->cartFollowUpsService->create($request->all());
            DB::commit();
            return $this->returnData(trans('Follow up created'), ['follow_up' => $followUp]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    /**
     * Expose the configured follow-up hours diff (for display in cart show).
     */
    public function getSettings()
    {
        return $this->returnData('ok', [
            'hours_diff' => $this->cartFollowUpsService->getFollowUpHoursDiff(),
        ]);
    }

    /**
     * Update status of a follow up.
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'status' => 'required|string|in:pending,sale,no_answer,not_interested',
                'notes'  => 'nullable|string|max:500',
            ]);

            $followUp = $this->cartFollowUpsService->updateStatus($id, $request->status, $request->notes);
            DB::commit();
            return $this->returnData(trans('Status updated successfully'), ['follow_up' => $followUp]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    /**
     * Analysis / Stats dashboard for follow-ups.
     */
    public function analysis(Request $request)
    {
        $title = trans('Follow Ups Analysis');
        $screen = 'cart-follow-ups-analysis';
        $stats = $this->cartFollowUpsService->getAnalysisData($request->from_date, $request->to_date);

        return view('orders::pages.cart-follow-ups.analysis', compact('title', 'screen', 'stats'));
    }
}
