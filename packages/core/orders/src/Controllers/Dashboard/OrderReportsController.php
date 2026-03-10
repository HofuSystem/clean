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
use Core\Orders\Requests\OrderReportsRequest; 
use Core\Orders\Requests\ImportOrderReportsRequest; 
use Core\Orders\Exports\OrderReportsExport; 
use Core\Orders\Services\OrderReportsService;
use Core\Orders\Services\OrdersService;
use Core\Users\Services\UsersService;
use Core\Orders\Services\ReportReasonsService;

class OrderReportsController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderReportsService $orderReportsService,protected OrdersService $ordersService,protected UsersService $usersService,protected ReportReasonsService $reportReasonsService){}

    public function index(){
        $title      = trans('OrderReport index');
        $screen     = 'order-reports-index';
        $total      = $this->orderReportsService->totalCount();
        $trash      = $this->orderReportsService->trashCount();
		$orders = $this->ordersService->selectable('id','reference_id');
		$users = $this->usersService->selectable('id','fullname');
		$reportReasons = $this->reportReasonsService->selectable('id','name');

        return view('orders::pages.order-reports.list', compact('title','screen','orders','users','reportReasons',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderReportsService->get($id) : null;
        $screen     = isset($item)  ? 'OrderReport-edit'          : 'OrderReport-create';
        $title      = isset($item)  ? trans("OrderReport  edit")  : trans("OrderReport  create");
		$orders = $this->ordersService->selectable('id','reference_id');
		$users = $this->usersService->selectable('id','fullname');
		$reportReasons = $this->reportReasonsService->selectable('id','name');


        return view('orders::pages.order-reports.edit', compact('item','title','screen','orders','users','reportReasons') );
    }

    public function storeOrUpdate(OrderReportsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderReportsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-reports.delete',$record->id);
            $record->updateUrl  = route('dashboard.order-reports.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('OrderReport saved'),['entity'=>$record->itemData]);
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
        $title      = trans('OrderReport index');
        $screen     = 'order-reports-index';
        $item       = $this->orderReportsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.order-reports.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderReportsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderReport deleted'));
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
            $data             = $this->orderReportsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('OrderReport import');
        $screen     = 'OrderReport-import';
        $url        = route('dashboard.order-reports.import') ;
        $exportUrl  = route('dashboard.order-reports.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.order-reports.index') ;
        $cols       = ['order_id'=>'order','user_id'=>'user','report_reason_id'=>'report reason','desc_location'=>'desc location'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrderReportsRequest $request){
        try {
            DB::beginTransaction();
            $this->orderReportsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderReport saved'));
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
        $filename = $request->headersOnly ? 'order-reports-template.xlsx' : 'order-reports.xlsx';
        return Excel::download(new OrderReportsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->orderReportsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->orderReportsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderReport restored'));
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
