<?php

namespace Core\B2B\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\B2B\Models\CompanyEmployee;
use Core\B2B\Models\B2BFinancial;
use Core\B2B\Models\Contract;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\B2B\Requests\CompaniesRequest;
use Core\B2B\Services\CompaniesService;
use Core\Users\Services\UsersService;
use Core\B2B\Services\CompanyPermissionsService;
use Core\B2B\Models\CompanyPermission;

use Core\Info\Services\CitiesService;
use Core\Info\Services\DistrictsService;
use Core\Products\Services\ProductsService;

class CompaniesController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CompaniesService $companiesService,
        protected UsersService $usersService,
        protected CitiesService $citiesService,
        protected DistrictsService $districtsService,
        protected CompanyPermissionsService $permissionsService,
        protected ProductsService $productsService
    ) {
    }

    public function index()
    {
        $title = trans('companies index');
        $screen = 'companies-index';
        $total = $this->companiesService->totalCount();
        $trash = $this->companiesService->trashCount();
        $owners = $this->usersService->selectable('id', 'fullname', []);

        return view('b2b::pages.companies.list', compact('title', 'screen', 'total', 'trash', 'owners'));
    }

    public function searchUsers(Request $request)
    {
        $q = $request->input('q');
        $users = \Core\Users\Models\User::where('fullname', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(50)
            ->get(['id', 'fullname', 'phone']);

        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'text' => $user->fullname . ' : ' . $user->phone
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function createOrEdit(Request $request, $id = null)
    {
        $item = isset($id) ? $this->companiesService->get($id) : null;
        $screen = isset($item) ? 'companies-edit' : 'companies-create';
        $title = isset($item) ? trans('companies edit') : trans('companies create');
        $cities = $this->citiesService->selectable('id', 'name');
        $users = [];
        $permissions = CompanyPermission::with('translations')->get();
        $contract = $item?->contracts()->currentActive()
            ->with([
                'contractPrices.product.category.translations',
                'contractPrices.product.subCategory.translations',
                'contractCustomerPrices.product.category.translations',
                'contractCustomerPrices.product.subCategory.translations'
            ])
            ->first();
        $employees = CompanyEmployee::where('company_id', $item?->id)
            ->with(['user', 'permission', 'branch'])
            ->get();
        $products = $this->productsService->selectable('id', 'name', [
            'category_id',
            'sub_category_id'
        ], [
            'translations',
            'category.translations',
            'subCategory.translations'
        ]);
        $financials = B2BFinancial::where('company_id', $item?->id)->get();

        return view('b2b::pages.companies.edit', compact('item', 'title', 'screen', 'cities', 'users', 'permissions', 'contract', 'employees', 'products', 'financials'));
    }

    public function storeOrUpdate(CompaniesRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->companiesService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.companies.delete', $record->id);
            $record->updateUrl = route('dashboard.companies.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('companies saved'), ['entity' => $record->itemData]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function show($id)
    {
        $title = trans('companies show');
        $screen = 'companies-show';
        $item = $this->companiesService->get($id);

        return view('b2b::pages.companies.show', compact('title', 'screen', 'item'));
    }

    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->companiesService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('companies deleted'));
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
            $data = $this->companiesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function restore(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->companiesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Company restored'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
