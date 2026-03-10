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
use Core\Orders\Requests\OrderInvoicesRequest; 
use Core\Orders\Requests\ImportOrderInvoicesRequest; 
use Core\Orders\Exports\OrderInvoicesExport; 
use Core\Orders\Services\OrderInvoicesService;
use Core\Orders\Services\OrdersService;
use Core\Users\Services\UsersService;

class OrderInvoicesController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderInvoicesService $orderInvoicesService,protected OrdersService $ordersService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('OrderInvoice index');
        $screen     = 'order-invoices-index';
        $total      = $this->orderInvoicesService->totalCount();
        $trash      = $this->orderInvoicesService->trashCount();
		$orders = $this->ordersService->selectable('id','reference_id');
		$users = $this->usersService->selectable('id','fullname');

        return view('orders::pages.order-invoices.list', compact('title','screen','orders','users',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderInvoicesService->get($id) : null;
        $screen     = isset($item)  ? 'OrderInvoice-edit'          : 'OrderInvoice-create';
        $title      = isset($item)  ? trans("OrderInvoice  edit")  : trans("OrderInvoice  create");
		$orders = $this->ordersService->selectable('id','reference_id');
		$users = $this->usersService->selectable('id','fullname');


        return view('orders::pages.order-invoices.edit', compact('item','title','screen','orders','users') );
    }

    public function storeOrUpdate(OrderInvoicesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderInvoicesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-invoices.delete',$record->id);
            $record->updateUrl  = route('dashboard.order-invoices.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('OrderInvoice saved'),['entity'=>$record->itemData]);
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
        $title      = trans('OrderInvoice index');
        $screen     = 'order-invoices-index';
        $item       = $this->orderInvoicesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.order-invoices.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderInvoicesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderInvoice deleted'));
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
            $data             = $this->orderInvoicesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('OrderInvoice import');
        $screen     = 'OrderInvoice-import';
        $url        = route('dashboard.order-invoices.import') ;
        $exportUrl  = route('dashboard.order-invoices.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.order-invoices.index') ;
        $cols       = ['invoice_num'=>'invoice num','data'=>'data','order_id'=>'order','user_id'=>'user'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrderInvoicesRequest $request){
        try {
            DB::beginTransaction();
            $this->orderInvoicesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderInvoice saved'));
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
        $filename = $request->headersOnly ? 'order-invoices-template.xlsx' : 'order-invoices.xlsx';
        return Excel::download(new OrderInvoicesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->orderInvoicesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->orderInvoicesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderInvoice restored'));
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
