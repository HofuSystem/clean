<?php

namespace Core\B2B\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\B2B\Requests\CompanyEmployeesRequest;
use Core\B2B\Services\CompanyEmployeesService;
use Core\B2B\Models\CompanyEmployee;
use Core\B2B\Models\Company;
use Core\B2B\Models\CompanyPermission;
use Core\Users\Models\User;

class CompanyEmployeesController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CompanyEmployeesService $employeesService
    ) {}

    public function index()
    {
        $title  = trans('company employees index');
        $screen = 'company-employees-index';
        $total  = CompanyEmployee::count();
        $trash  = CompanyEmployee::onlyTrashed()->count();

        return view('b2b::pages.company_employees.list', compact('title', 'screen', 'total', 'trash'));
    }

    public function createOrEdit(Request $request, $id = null)
    {
        $item   = isset($id) ? $this->employeesService->get($id) : null;
        $screen = isset($item) ? 'company-employees-edit'   : 'company-employees-create';
        $title  = isset($item) ? trans('company employees edit') : trans('company employees create');

        $users       = User::select(['id', 'fullname'])->get();
        $companies   = Company::select(['id', 'fullname'])->get();
        $permissions = CompanyPermission::all();

        return view('b2b::pages.company_employees.edit', compact('item', 'title', 'screen', 'users', 'companies', 'permissions'));
    }

    public function storeOrUpdate(CompanyEmployeesRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record            = $this->employeesService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.company-employees.delete', $record->id);
            $record->updateUrl = route('dashboard.company-employees.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('company employee saved'), ['entity' => $record->itemData]);
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
            $this->employeesService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('company employee deleted'));
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
            $data = $this->employeesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
