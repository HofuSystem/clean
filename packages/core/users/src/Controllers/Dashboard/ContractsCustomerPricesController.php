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
use Core\Users\Requests\ContractsCustomerPricesRequest; 
use Core\Users\Requests\ImportContractsCustomerPricesRequest; 
use Core\Users\Exports\ContractsCustomerPricesExport; 
use Core\Users\Services\ContractsCustomerPricesService;
use Core\Users\Services\ContractsService;
use Core\Products\Services\ProductsService;

class ContractsCustomerPricesController extends Controller
{
    use ApiResponse;
    public function __construct(protected ContractsCustomerPricesService $contractsCustomerPricesService,protected ContractsService $contractsService,protected ProductsService $productsService){}

    public function index(){
        $title      = trans('contracts customer prices index');
        $screen     = 'contracts-customer-prices-index';
        $total      = $this->contractsCustomerPricesService->totalCount();
        $trash      = $this->contractsCustomerPricesService->trashCount();
		$contracts = $this->contractsService->selectable(['id','title']);
		$products = $this->productsService->selectable('id','name',['category_id','sub_category_id'],['category','subCategory'])->map(function($item){
			$item->name = $item->category?->name . ' -> ' . $item->subCategory?->name . ' -> ' . $item->name;
			return $item;
		});

        return view('users::pages.contracts-customer-prices.list', compact('title','screen','contracts','products',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->contractsCustomerPricesService->get($id) : null;
        $screen     = isset($item)  ? 'contracts-customer-prices-edit'          : 'contracts-customer-prices-create';
        $title      = isset($item)  ? trans("contracts customer prices  edit")  : trans("contracts customer prices  create");
		$contracts = $this->contractsService->selectable(['id','title']);
		$products = $this->productsService->selectable('id','name',['category_id','sub_category_id'],['category','subCategory'])->map(function($item){
			$item->name = $item->category?->name . ' -> ' . $item->subCategory?->name . ' -> ' . $item->name;
			return $item;
		});


        return view('users::pages.contracts-customer-prices.edit', compact('item','title','screen','contracts','products') );
    }

    public function storeOrUpdate(ContractsCustomerPricesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->contractsCustomerPricesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.contracts-customer-prices.delete',$record->id);
            $record->updateUrl  = route('dashboard.contracts-customer-prices.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('contracts customer prices saved'),['entity'=>$record->itemData]);
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
        $title      = trans('contracts customer prices show');
        $screen     = 'contracts-customer-prices-show';
        $item       = $this->contractsCustomerPricesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.contracts-customer-prices.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->contractsCustomerPricesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('contracts customer prices deleted'));
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
            $data             = $this->contractsCustomerPricesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('contracts customer prices import');
        $screen     = 'contracts-customer-prices-import';
        $url        = route('dashboard.contracts-customer-prices.import') ;
        $exportUrl  = route('dashboard.contracts-customer-prices.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.contracts-customer-prices.index') ;
        $cols       = ['contract_id'=>'contract','product_id'=>'product','over_price'=>'over price'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportContractsCustomerPricesRequest $request){
        try {
            DB::beginTransaction();
            $this->contractsCustomerPricesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('contracts customer prices saved'));
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
        $filename = $request->headersOnly ? 'contracts-customer-prices-template.xlsx' : 'contracts-customer-prices.xlsx';
        return Excel::download(new ContractsCustomerPricesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->contractsCustomerPricesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->contractsCustomerPricesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('ContractsCustomerPrice restored'));
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

