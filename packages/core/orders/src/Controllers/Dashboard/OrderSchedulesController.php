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
use Core\Orders\Requests\OrderSchedulesRequest; 
use Core\Orders\Requests\ImportOrderSchedulesRequest; 
use Core\Orders\Exports\OrderSchedulesExport; 
use Core\Orders\Services\OrderSchedulesService;
use Core\Users\Services\UsersService;
use Core\Users\Services\AddressesService;

class OrderSchedulesController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderSchedulesService $orderSchedulesService,protected UsersService $usersService,protected AddressesService $addressesService){}

    public function index(){
        $title      = trans('order schedules index');
        $screen     = 'order-schedules-index';
        $total      = $this->orderSchedulesService->totalCount();
        $trash      = $this->orderSchedulesService->trashCount();
		$clients = $this->usersService->selectable('id','fullname',[],'company');
		$receiverAddresses = $this->addressesService->selectable('id','location');
		$deliveryAddresses = $this->addressesService->selectable('id','location');

        return view('orders::pages.order-schedules.list', compact('title','screen','clients','receiverAddresses','deliveryAddresses',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderSchedulesService->get($id) : null;
        $screen     = isset($item)  ? 'order schedules-edit'          : 'order schedules-create';
        $title      = isset($item)  ? trans("order schedules  edit")  : trans("order schedules  create");
		$clients = $this->usersService->selectable('id','fullname',[],'company');
		$receiverAddresses = $this->addressesService->selectable('id','location');
		$deliveryAddresses = $this->addressesService->selectable('id','location');


        return view('orders::pages.order-schedules.edit', compact('item','title','screen','clients','receiverAddresses','deliveryAddresses') );
    }

    public function storeOrUpdate(OrderSchedulesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderSchedulesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-schedules.delete',$record->id);
            $record->updateUrl  = route('dashboard.order-schedules.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('order schedules saved'),['entity'=>$record->itemData]);
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
        $title      = trans('order schedules show');
        $screen     = 'order-schedules-show';
        $item       = $this->orderSchedulesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.order-schedules.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderSchedulesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('order schedules deleted'));
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
            $data             = $this->orderSchedulesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('order schedules import');
        $screen     = 'order schedules-import';
        $url        = route('dashboard.order-schedules.import') ;
        $exportUrl  = route('dashboard.order-schedules.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.order-schedules.index') ;
        $cols       = ['client_id'=>'client','type'=>'type','receiver_day'=>'receiver day','receiver_date'=>'receiver date','receiver_time'=>'receiver time','receiver_to_time'=>'receiver to time','delivery_day'=>'delivery day','delivery_date'=>'delivery date','delivery_time'=>'delivery time','delivery_to_time'=>'delivery to time','receiver_address_id'=>'receiver address','delivery_address_id'=>'delivery address','note'=>'note'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrderSchedulesRequest $request){
        try {
            DB::beginTransaction();
            $this->orderSchedulesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('order schedules saved'));
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
        $filename = $request->headersOnly ? 'order-schedules-template.xlsx' : 'order-schedules.xlsx';
        return Excel::download(new OrderSchedulesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->orderSchedulesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->orderSchedulesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderSchedule restored'));
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
