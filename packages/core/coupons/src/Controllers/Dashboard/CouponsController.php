<?php

namespace Core\Coupons\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Coupons\Requests\CouponsRequest; 
use Core\Coupons\Requests\ImportCouponsRequest; 
use Core\Coupons\Exports\CouponsExport; 
use Core\Coupons\Services\CouponsService;
use Core\Products\Services\ProductsService;
use Core\Categories\Services\CategoriesService;
use Core\Users\Services\UsersService;
use Core\Users\Services\RolesService;

class CouponsController extends Controller
{
    use ApiResponse;
    public function __construct(protected CouponsService $couponsService,protected ProductsService $productsService,protected CategoriesService $categoriesService,protected UsersService $usersService,protected RolesService $rolesService){}

    public function index(){
        $title      = trans('Coupon index');
        $screen     = 'coupons-index';
        $total      = $this->couponsService->totalCount();
        $trash      = $this->couponsService->trashCount();
		$products = $this->productsService->selectable('id','name');
		$categories = $this->categoriesService->selectable('id','name');
		$users = $this->usersService->selectable('id','fullname');
		$roles = $this->rolesService->selectable('id','name');

        return view('coupons::pages.coupons.list', compact('title','screen','products','categories','users','roles',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->couponsService->get($id) : null;
        $screen     = isset($item)  ? 'Coupon-edit'          : 'Coupon-create';
        $title      = isset($item)  ? trans("Coupon  edit")  : trans("Coupon  create");
		$products = $this->productsService->selectable('id','name');
		$categories = $this->categoriesService->selectable('id','name');
		$users = $this->usersService->selectable('id','fullname');
		$roles = $this->rolesService->selectable('id','name');


        return view('coupons::pages.coupons.edit', compact('item','title','screen','products','categories','users','roles') );
    }

    public function storeOrUpdate(CouponsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $data               = $request->all();
            $data['products']   = array_filter($data['products'] ?? []);
            $data['categories'] = array_filter($data['categories'] ?? []);
            $data['users']      = array_filter($data['users'] ?? []);
            $data['roles']      = array_filter($data['roles'] ?? []);
            $record             = $this->couponsService->storeOrUpdate($data,$id);
            $record->deleteUrl  = route('dashboard.coupons.delete',$record->id);
            $record->updateUrl  = route('dashboard.coupons.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Coupon saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Coupon index');
        $screen     = 'coupons-index';
        $item       = $this->couponsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('coupons::pages.coupons.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->couponsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Coupon deleted'));
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
            $data             = $this->couponsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Coupon import');
        $screen     = 'Coupon-import';
        $url        = route('dashboard.coupons.import') ;
        $exportUrl  = route('dashboard.coupons.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.coupons.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','status'=>'status','applying'=>'applying','code'=>'code','max_use'=>'max use','max_use_per_user'=>'max use per user','payment_method'=>'payment method','start_at'=>'start at','end_at'=>'end at','order_type'=>'order Type','all_products'=>'all products','products'=>'products','categories'=>'categories','all_users'=>'all users','users'=>'users','roles'=>'roles','order_minimum'=>'order minimum','order_maximum'=>'order maximum','type'=>'type','value'=>'value','max_value'=>'max value'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCouponsRequest $request){
        try {
            DB::beginTransaction();
            $this->couponsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Coupon saved'));
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
        $filename = $request->headersOnly ? 'coupons-template.xlsx' : 'coupons.xlsx';
        return Excel::download(new CouponsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->couponsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->couponsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Coupon restored'));
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
