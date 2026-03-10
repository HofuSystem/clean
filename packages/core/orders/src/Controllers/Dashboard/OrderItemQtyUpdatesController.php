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
use Core\Orders\Requests\OrderItemQtyUpdatesRequest; 
use Core\Orders\Requests\ImportOrderItemQtyUpdatesRequest; 
use Core\Orders\Exports\OrderItemQtyUpdatesExport; 
use Core\Orders\Services\OrderItemQtyUpdatesService;
use Core\Orders\Services\OrderItemsService;

class OrderItemQtyUpdatesController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderItemQtyUpdatesService $orderItemQtyUpdatesService,protected OrderItemsService $orderItemsService){}

    public function index(){
        $title      = trans('OrderItemQtyUpdate index');
        $screen     = 'order-item-qty-updates-index';
        $total      = $this->orderItemQtyUpdatesService->totalCount();
        $trash      = $this->orderItemQtyUpdatesService->trashCount();
		$items = $this->orderItemsService->selectable('id','product_id');

        return view('orders::pages.order-item-qty-updates.list', compact('title','screen','items',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderItemQtyUpdatesService->get($id) : null;
        $screen     = isset($item)  ? 'OrderItemQtyUpdate-edit'          : 'OrderItemQtyUpdate-create';
        $title      = isset($item)  ? trans("OrderItemQtyUpdate  edit")  : trans("OrderItemQtyUpdate  create");
		$items = $this->orderItemsService->selectable('id','product_id');


        return view('orders::pages.order-item-qty-updates.edit', compact('item','title','screen','items') );
    }

    public function storeOrUpdate(OrderItemQtyUpdatesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderItemQtyUpdatesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-item-qty-updates.delete',$record->id);
            $record->updateUrl  = route('dashboard.order-item-qty-updates.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('OrderItemQtyUpdate saved'),['entity'=>$record->itemData]);
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
        $title      = trans('OrderItemQtyUpdate index');
        $screen     = 'order-item-qty-updates-index';
        $item       = $this->orderItemQtyUpdatesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.order-item-qty-updates.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderItemQtyUpdatesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderItemQtyUpdate deleted'));
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
            $data             = $this->orderItemQtyUpdatesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('OrderItemQtyUpdate import');
        $screen     = 'OrderItemQtyUpdate-import';
        $url        = route('dashboard.order-item-qty-updates.import') ;
        $exportUrl  = route('dashboard.order-item-qty-updates.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.order-item-qty-updates.index') ;
        $cols       = ['item_id'=>'item','from'=>'from','to'=>'to','updater_email'=>'updater email'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrderItemQtyUpdatesRequest $request){
        try {
            DB::beginTransaction();
            $this->orderItemQtyUpdatesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderItemQtyUpdate saved'));
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
        $filename = $request->headersOnly ? 'order-item-qty-updates-template.xlsx' : 'order-item-qty-updates.xlsx';
        return Excel::download(new OrderItemQtyUpdatesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->orderItemQtyUpdatesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->orderItemQtyUpdatesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderItemQtyUpdate restored'));
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
