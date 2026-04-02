<?php

namespace Core\B2B\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\B2B\Requests\CompanyBranchesRequest;
use Core\B2B\Services\CompanyBranchesService;

class CompanyBranchesController extends Controller
{
    use ApiResponse;

    public function __construct(protected CompanyBranchesService $companyBranchesService)
    {
    }

    public function storeOrUpdate(CompanyBranchesRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->companyBranchesService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.company-branches.delete', $record->id);
            $record->updateUrl = route('dashboard.company-branches.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('branch saved'), ['entity' => $record->itemData]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            dd($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->companyBranchesService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('branch deleted'));
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
            $data = $this->companyBranchesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
