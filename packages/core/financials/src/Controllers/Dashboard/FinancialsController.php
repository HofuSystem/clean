<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Financials\Requests\FinancialsRequest;
use Core\Financials\Services\FinancialsService;
use Core\B2B\Models\Company;

class FinancialsController extends Controller
{
    use ApiResponse;

    public function __construct(protected FinancialsService $financialsService)
    {
    }

    public function index()
    {
        $title = trans('Financials');
        $screen = 'financials-index';
        $total = $this->financialsService->totalCount();
        $trash = $this->financialsService->trashCount();
        $companies = Company::underMyControl()->get(['id', 'fullname']);

        return view('financials::pages.financials.list', compact('title', 'screen', 'companies', 'total', 'trash'));
    }

    public function createOrEdit(Request $request, $id = null)
    {
        $item = isset($id) ? $this->financialsService->get($id) : null;
        $screen = isset($item) ? 'financials-edit' : 'financials-create';
        $title = isset($item) ? trans("Financial Edit") : trans("Financial Create");
        $companies = Company::underMyControl()->get(['id', 'fullname']);

        return view('financials::pages.financials.edit', compact('item', 'title', 'screen', 'companies'));
    }

    public function show($id)
    {
        $title = trans('Financial details');
        $screen = 'financials-index';
        $item = $this->financialsService->get($id);
        return view('financials::pages.financials.show', compact('title', 'screen', 'item'));
    }

    public function storeOrUpdate(FinancialsRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->financialsService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.financials.delete', $record->id);
            $record->updateUrl = route('dashboard.financials.edit', $record->id);
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

    public function restore(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->financialsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('financial record restored'));
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
