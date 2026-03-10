<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Admin\Requests\OrderInvociesRequest; 
use Core\Admin\Services\OrderInvociesService;
use Core\Admin\Services\ProfilesService;
use Core\Coupons\Services\CouponsService;

class OrderInvociesController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderInvociesService $orderInvociesService,protected ProfilesService $profilesService,protected CouponsService $couponsService){}
   
    public function index(){
        $title      = trans('OrderInvocy index');
        $screen     = 'order-invocies-index';
        $orders = $this->profilesService->selectable('id','reference_id');
$users = $this->couponsService->selectable('id','fullname');

        return view('admin::pages.order-invocies.list', compact('title','screen','orders','users'));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderInvociesService->get($id) : null;
        $screen     = isset($item)  ? 'OrderInvocy-edit'          : 'OrderInvocy-create';
        $title      = isset($item)  ? trans("OrderInvocy  edit")  : trans("OrderInvocy  create");
        $orders = $this->profilesService->selectable('id','reference_id');
$users = $this->couponsService->selectable('id','fullname');


        return view('admin::pages.order-invocies.edit', compact('item','title','screen','orders','users') );
    }

    public function storeOrUpdate(OrderInvociesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderInvociesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-invocies.delete',$record->id); 
            $record->updateUrl  = route('dashboard.order-invocies.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('OrderInvocy saved'),['entity'=>$record->itemData]);
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
        $title      = trans('OrderInvocy index');
        $screen     = 'order-invocies-index';
        $record     = $this->orderInvociesService->get($id);;
        return view('admin::pages.order-invocies.show', compact('title','screen','record'));
    }



   
    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderInvociesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderInvocy deleted'));
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
            $data             = $this->orderInvociesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    
   
}
