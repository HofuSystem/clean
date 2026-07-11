<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Financials\Services\PurchasesService;
use Core\Financials\Services\PurchaseItemsService;
use Core\Financials\Services\PurchaseProvidersService;

use Core\Financials\Requests\PurchasesRequest;
use Core\Financials\Requests\ImportPurchasesRequest;
use Core\Financials\Exports\PurchasesExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchasesController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PurchasesService $purchasesService,
        protected PurchaseItemsService $purchaseItemsService,
        protected PurchaseProvidersService $purchaseProvidersService
    ){}

    public function index(){
        $title      = trans('Purchases index');
        $screen     = 'purchases-index';
        $total      = $this->purchasesService->totalCount();
        $trash      = $this->purchasesService->trashCount();
        
        $items = $this->purchaseItemsService->selectable('id','name');
        $providers = $this->purchaseProvidersService->selectable('id','name');

        return view('financials::pages.purchases.list', compact('title','screen','items','providers',"total","trash"));
    }

    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->purchasesService->get($id) : null;
        $screen     = isset($item)  ? 'purchases-edit'          : 'purchases-create';
        $title      = isset($item)  ? trans("Purchase edit")  : trans("Purchase create");
        
        $items = $this->purchaseItemsService->selectable('id','name');
        $providers = $this->purchaseProvidersService->selectable('id','name');

        return view('financials::pages.purchases.edit', compact('item','title','screen','items','providers') );
    }

    public function storeOrUpdate(PurchasesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->purchasesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.purchases.delete',$record->id);
            $record->updateUrl  = route('dashboard.purchases.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Purchase saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function show($id){
        $title      = trans('Purchase index');
        $screen     = 'purchases-index';
        $item       = $this->purchasesService->get($id);
        return view('financials::pages.purchases.show', compact('title','screen','item'));
    }

    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->purchasesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase deleted'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function dataTable(Request $request){
        try {
            $data             = $this->purchasesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function restore(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->purchasesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function importView(Request $request){
        $title      = trans('Purchase import');
        $screen     = 'purchase-import';
        $url        = route('dashboard.purchases.import') ;
        $exportUrl  = route('dashboard.purchases.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.purchases.index') ;
        $cols       = [
            'item_id' => 'item',
            'provider_id' => 'provider',
            'value_before_tax' => 'value before tax',
            'tax_value' => 'tax value',
            'value_after_tax' => 'value after tax',
            'notes' => 'notes'
        ];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }

    public function import(ImportPurchasesRequest $request){
        try {
            DB::beginTransaction();
            $this->purchasesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase saved'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function export(Request $request)
    {
        $filename = $request->headersOnly ? 'purchases-template.xlsx' : 'purchases.xlsx';
        return Excel::download(new PurchasesExport($request->headersOnly,$request->cols), $filename);
    }
}
