<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Financials\Services\PurchaseItemsService;

use Core\Financials\Requests\PurchaseItemsRequest;
use Core\Financials\Requests\ImportPurchaseItemsRequest;
use Core\Financials\Exports\PurchaseItemsExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseItemsController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PurchaseItemsService $purchaseItemsService
    ){}

    public function index(){
        $title      = trans('Purchase items index');
        $screen     = 'purchase-items-index';
        $total      = $this->purchaseItemsService->totalCount();
        $trash      = $this->purchaseItemsService->trashCount();

        return view('financials::pages.purchase-items.list', compact('title','screen',"total","trash"));
    }

    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->purchaseItemsService->get($id) : null;
        $screen     = isset($item)  ? 'purchase-items-edit'          : 'purchase-items-create';
        $title      = isset($item)  ? trans("Purchase item edit")  : trans("Purchase item create");

        return view('financials::pages.purchase-items.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(PurchaseItemsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->purchaseItemsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.purchase-items.delete',$record->id);
            $record->updateUrl  = route('dashboard.purchase-items.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Purchase item saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Purchase item index');
        $screen     = 'purchase-items-index';
        $item       = $this->purchaseItemsService->get($id);
        return view('financials::pages.purchase-items.show', compact('title','screen','item'));
    }

    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->purchaseItemsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase item deleted'));
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
            $data             = $this->purchaseItemsService->dataTable($request->draw);
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
            $record             = $this->purchaseItemsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase item restored'));
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
        $title      = trans('Purchase item import');
        $screen     = 'purchase-item-import';
        $url        = route('dashboard.purchase-items.import') ;
        $exportUrl  = route('dashboard.purchase-items.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.purchase-items.index') ;
        $cols       = ['name'=>'name'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }

    public function import(ImportPurchaseItemsRequest $request){
        try {
            DB::beginTransaction();
            $this->purchaseItemsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase item saved'));
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
        $filename = $request->headersOnly ? 'purchase-items-template.xlsx' : 'purchase-items.xlsx';
        return Excel::download(new PurchaseItemsExport($request->headersOnly,$request->cols), $filename);
    }
}
