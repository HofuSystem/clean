<?php

namespace Core\Coupons\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Coupons\Requests\GiftsRequest;
use Core\Coupons\Services\GiftsService;

class GiftsController extends Controller
{
    use ApiResponse;

    public function __construct(protected GiftsService $giftsService) {}

    public function index()
    {
        $title  = trans('Gifts index');
        $screen = 'gifts-index';
        $total  = $this->giftsService->totalCount();
        $trash  = $this->giftsService->trashCount();

        return view('coupons::pages.gifts.list', compact('title', 'screen', 'total', 'trash'));
    }

    public function createOrEdit(Request $request, $id = null)
    {
        $item   = isset($id) ? $this->giftsService->get($id) : null;
        $screen = isset($item) ? 'Gift-edit' : 'Gift-create';
        $title  = isset($item) ? trans("Gift edit") : trans("Gift create");

        return view('coupons::pages.gifts.edit', compact('item', 'title', 'screen'));
    }

    public function storeOrUpdate(GiftsRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->giftsService->storeOrUpdate($request->all(), $id);
            DB::commit();
            return $this->returnData(trans('Gift saved'), ['entity' => $record->itemData]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->giftsService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Gift deleted'));
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $data = $this->giftsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function restore(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->giftsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Gift restored'));
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
