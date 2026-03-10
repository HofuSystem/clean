<?php

namespace Core\Users\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Users\Requests\ContractsPricesRequest; 
use Core\Users\Requests\ImportContractsPricesRequest; 
use Core\Users\Exports\ContractsPricesExport; 
use Core\Users\Services\ContractsPricesService;
use Core\Users\Services\ContractsService;
use Core\Products\Services\ProductsService;

class ContractsPricesController extends Controller
{
    use ApiResponse;
    public function __construct(protected ContractsPricesService $contractsPricesService,protected ContractsService $contractsService,protected ProductsService $productsService){}

    public function index(){
        $title      = trans('contracts prices index');
        $screen     = 'contracts-prices-index';
        $total      = $this->contractsPricesService->totalCount();
        $trash      = $this->contractsPricesService->trashCount();
		$contracts = $this->contractsService->selectable(['id','title']);
		$products = $this->productsService->selectable('id','name',['category_id','sub_category_id'],['category','subCategory'])->map(function($item){
			$item->name = $item->category?->name . ' -> ' . $item->subCategory?->name . ' -> ' . $item->name;
			return $item;
		});

        return view('users::pages.contracts-prices.list', compact('title','screen','contracts','products',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->contractsPricesService->get($id) : null;
        $screen     = isset($item)  ? 'contracts prices-edit'          : 'contracts prices-create';
        $title      = isset($item)  ? trans("contracts prices  edit")  : trans("contracts prices  create");
		$contracts = $this->contractsService->selectable(['id','title']);
		$products = $this->productsService->selectable('id','name',['category_id','sub_category_id'],['category','subCategory'])->map(function($item){
			$item->name = $item->category?->name . ' -> ' . $item->subCategory?->name . ' -> ' . $item->name;
			return $item;
		});


        return view('users::pages.contracts-prices.edit', compact('item','title','screen','contracts','products') );
    }

    public function storeOrUpdate(ContractsPricesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->contractsPricesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.contracts-prices.delete',$record->id);
            $record->updateUrl  = route('dashboard.contracts-prices.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('contracts prices saved'),['entity'=>$record->itemData]);
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
        $title      = trans('contracts prices show');
        $screen     = 'contracts-prices-show';
        $item       = $this->contractsPricesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.contracts-prices.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->contractsPricesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('contracts prices deleted'));
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
            $data             = $this->contractsPricesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('contracts prices import');
        $screen     = 'contracts prices-import';
        $url        = route('dashboard.contracts-prices.import') ;
        $exportUrl  = route('dashboard.contracts-prices.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.contracts-prices.index') ;
        $cols       = ['contract_id'=>'contract','product_id'=>'product','price'=>'price'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportContractsPricesRequest $request){
        try {
            DB::beginTransaction();
            $this->contractsPricesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('contracts prices saved'));
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
        $filename = $request->headersOnly ? 'contracts-prices-template.xlsx' : 'contracts-prices.xlsx';
        return Excel::download(new ContractsPricesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->contractsPricesService->comment($id,$request->content,$request->parent_id);
            DB::commit();
            return $this->returnData(trans('comment created'),['comment'=>new CommentResource($comment)]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function restore(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->contractsPricesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('ContractsPrice restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
}
