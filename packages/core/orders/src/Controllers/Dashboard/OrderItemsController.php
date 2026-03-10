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
use Core\Orders\Requests\OrderItemsRequest; 
use Core\Orders\Requests\ImportOrderItemsRequest; 
use Core\Orders\Exports\OrderItemsExport; 
use Core\Orders\Services\OrderItemsService;
use Core\Orders\Services\OrdersService;
use Core\Products\Services\ProductsService;

class OrderItemsController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderItemsService $orderItemsService,protected OrdersService $ordersService,protected ProductsService $productsService){}

    public function index(){
        $title      = trans('OrderItem index');
        $screen     = 'order-items-index';
        $total      = $this->orderItemsService->totalCount();
        $trash      = $this->orderItemsService->trashCount();
		$orders = $this->ordersService->selectable('id','reference_id');
		$products = $this->productsService->selectable('id','name');
		$items = $this->orderItemsService->selectable('id','product_id');

        return view('orders::pages.order-items.list', compact('title','screen','orders','products','items',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderItemsService->get($id) : null;
        $screen     = isset($item)  ? 'OrderItem-edit'          : 'OrderItem-create';
        $title      = isset($item)  ? trans("OrderItem  edit")  : trans("OrderItem  create");
		$orders = $this->ordersService->selectable('id','reference_id');
		$products = $this->productsService->selectable('id','name');
		$items = $this->orderItemsService->selectable('id','product_id');


        return view('orders::pages.order-items.edit', compact('item','title','screen','orders','products','items') );
    }

    public function storeOrUpdate(OrderItemsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderItemsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-items.delete',$record->id);
            $record->updateUrl  = route('dashboard.order-items.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('OrderItem saved'),['entity'=>$record->itemData]);
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
        $title      = trans('OrderItem index');
        $screen     = 'order-items-index';
        $item       = $this->orderItemsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.order-items.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderItemsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderItem deleted'));
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
            $data             = $this->orderItemsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            dd($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('OrderItem import');
        $screen     = 'OrderItem-import';
        $url        = route('dashboard.order-items.import') ;
        $exportUrl  = route('dashboard.order-items.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.order-items.index') ;
        $cols       = ['order_id'=>'order','product_id'=>'product','product_data'=>'product data','product_price'=>'product price','quantity'=>'quantity','width'=>'width','height'=>'height','add_by_admin'=>'add by admin','update_by_admin'=>'update by admin','is_picked'=>'is picked','is_delivered'=>'is delivered'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrderItemsRequest $request){
        try {
            DB::beginTransaction();
            $this->orderItemsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderItem saved'));
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
        $filename = $request->headersOnly ? 'order-items-template.xlsx' : 'order-items.xlsx';
        return Excel::download(new OrderItemsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->orderItemsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->orderItemsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderItem restored'));
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
