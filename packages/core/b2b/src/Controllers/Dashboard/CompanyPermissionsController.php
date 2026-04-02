<?php

namespace Core\B2B\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\B2B\Requests\CompanyPermissionsRequest;
use Core\B2B\Services\CompanyPermissionsService;
use Core\B2B\Models\CompanyPermission;

class CompanyPermissionsController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CompanyPermissionsService $permissionsService
    ) {}

    public function index()
    {
        $title  = trans('company permissions index');
        $screen = 'company-permissions-index';
        $total  = CompanyPermission::count(); // I should add this to service if needed, but for now simple
        $trash  = CompanyPermission::onlyTrashed()->count();

        return view('b2b::pages.company_permissions.list', compact('title', 'screen', 'total', 'trash'));
    }

    public function createOrEdit(Request $request, $id = null)
    {
        $item   = isset($id) ? $this->permissionsService->get($id) : null;
        $screen = isset($item) ? 'company-permissions-edit'   : 'company-permissions-create';
        $title  = isset($item) ? trans('company permissions edit') : trans('company permissions create');

        return view('b2b::pages.company_permissions.edit', compact('item', 'title', 'screen'));
    }

    public function storeOrUpdate(CompanyPermissionsRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record            = $this->permissionsService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.company-permissions.delete', $record->id);
            $record->updateUrl = route('dashboard.company-permissions.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('company permissions saved'), ['entity' => $record->itemData]);
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
            $this->permissionsService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('company permissions deleted'));
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
            $data = $this->permissionsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
