<?php

namespace Core\B2B\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\B2B\Requests\B2BFinancialsRequest;
use Core\B2B\Services\B2BFinancialsService;

class B2BFinancialsController extends Controller
{
    use ApiResponse;

    public function __construct(protected B2BFinancialsService $financialsService)
    {
    }

    public function storeOrUpdate(B2BFinancialsRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->financialsService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.b2b-financials.delete', $record->id);
            $record->updateUrl = route('dashboard.b2b-financials.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('financial record saved'), ['entity' => $record->itemData]);
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
            $this->financialsService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('financial record deleted'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $data = $this->financialsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
