<?php

namespace Core\Orders\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Orders\Requests\OrderTypesOfDatasRequest; 
use Core\Orders\Requests\ImportOrderTypesOfDatasRequest; 
use Core\Orders\Exports\OrderTypesOfDatasExport; 
use Core\Orders\Services\OrderTypesOfDatasService;
use Core\Orders\Services\OrdersService;

class OrderTypesOfDatasController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderTypesOfDatasService $orderTypesOfDatasService,protected OrdersService $ordersService){}

    public function index(){
        $title      = trans('OrderTypesOfData index');
        $screen     = 'order-types-of-datas-index';
        $total      = $this->orderTypesOfDatasService->totalCount();
        $trash      = $this->orderTypesOfDatasService->trashCount();
		$orders = $this->ordersService->selectable('id','reference_id');

        return view('orders::pages.order-types-of-datas.list', compact('title','screen','orders',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderTypesOfDatasService->get($id) : null;
        $screen     = isset($item)  ? 'OrderTypesOfData-edit'          : 'OrderTypesOfData-create';
        $title      = isset($item)  ? trans("OrderTypesOfData  edit")  : trans("OrderTypesOfData  create");
		$orders = $this->ordersService->selectable('id','reference_id');


        return view('orders::pages.order-types-of-datas.edit', compact('item','title','screen','orders') );
    }

    public function storeOrUpdate(OrderTypesOfDatasRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderTypesOfDatasService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-types-of-datas.delete',$record->id);
            $record->updateUrl  = route('dashboard.order-types-of-datas.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('OrderTypesOfData saved'),['entity'=>$record->itemData]);
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
        $title      = trans('OrderTypesOfData index');
        $screen     = 'order-types-of-datas-index';
        $item       = $this->orderTypesOfDatasService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.order-types-of-datas.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderTypesOfDatasService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderTypesOfData deleted'));
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
            $data             = $this->orderTypesOfDatasService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('OrderTypesOfData import');
        $screen     = 'OrderTypesOfData-import';
        $url        = route('dashboard.order-types-of-datas.import') ;
        $exportUrl  = route('dashboard.order-types-of-datas.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.order-types-of-datas.index') ;
        $cols       = ['order_id'=>'order','key'=>'key','value'=>'value'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrderTypesOfDatasRequest $request){
        try {
            DB::beginTransaction();
            $this->orderTypesOfDatasService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderTypesOfData saved'));
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
        $filename = $request->headersOnly ? 'order-types-of-datas-template.xlsx' : 'order-types-of-datas.xlsx';
        return Excel::download(new OrderTypesOfDatasExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->orderTypesOfDatasService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->orderTypesOfDatasService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderTypesOfData restored'));
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
