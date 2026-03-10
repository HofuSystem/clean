<?php

namespace Core\Categories\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Categories\Requests\CartsRequest; 
use Core\Categories\Services\CartsService;
use Core\Coupons\Services\CouponsService;

class CartsController extends Controller
{
    use ApiResponse;
    public function __construct(protected CartsService $cartsService,protected CouponsService $couponsService){}
   
    public function index(){
        $title      = trans('Cart index');
        $screen     = 'carts-index';
        $users = $this->couponsService->selectable('id','fullname');

        return view('categories::pages.carts.list', compact('title','screen','users'));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->cartsService->get($id) : null;
        $screen     = isset($item)  ? 'Cart-edit'          : 'Cart-create';
        $title      = isset($item)  ? trans("Cart  edit")  : trans("Cart  create");
        $users = $this->couponsService->selectable('id','fullname');


        return view('categories::pages.carts.edit', compact('item','title','screen','users') );
    }

    public function storeOrUpdate(CartsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->cartsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.carts.delete',$record->id); 
            $record->updateUrl  = route('dashboard.carts.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Cart saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Cart index');
        $screen     = 'carts-index';
        $record     = $this->cartsService->get($id);;
        return view('categories::pages.carts.show', compact('title','screen','record'));
    }



   
    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->cartsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Cart deleted'));
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
            $data             = $this->cartsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    
   
}
