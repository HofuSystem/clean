<?php

namespace Core\Orders\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Categories\Services\CategoriesService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Coupons\Services\CouponsService;
use Core\Info\Services\CitiesService;
use Core\Info\Services\DistrictsService;
use Core\Orders\Requests\CartsRequest; 
use Core\Orders\Requests\ImportCartsRequest; 
use Core\Orders\Exports\CartsExport; 
use Core\Orders\Services\CartsService;
use Core\Orders\Services\OrderItemsService;
use Core\Orders\Services\OrdersService;
use Core\Orders\Services\ReportReasonsService;
use Core\Products\Services\ProductsService;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Services\UsersService;

class CartsController extends Controller
{
    use ApiResponse;
    public function __construct(protected CitiesService $citiesService,protected DistrictsService $districtsService,protected OrdersService $ordersService,protected CategoriesService $categoriesService ,protected CouponsService $couponsService,protected ProductsService $productsService,protected OrderItemsService $orderItemsService,protected ReportReasonsService $reportReasonsService,protected CartsService $cartsService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('Cart index');
        $screen     = 'carts-index';
        $total      = $this->cartsService->totalCount();
        $trash      = $this->cartsService->trashCount();
		$users      = $this->usersService->selectable('id','fullname');
        $cities     = $this->citiesService->selectable('id','name');
        return view('orders::pages.carts.list', compact('title','screen','users','cities',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->cartsService->get($id) : null;
        $screen     = isset($item)  ? 'Cart-edit'          : 'Cart-create';
        $title      = isset($item)  ? trans("Cart  edit")  : trans("Cart  create");
		$users = $this->usersService->selectable('id','fullname');


        return view('orders::pages.carts.edit', compact('item','title','screen','users') );
    }
    public function createOrder(Request $request,$id){
        $screen         =  'create-order-form-cart';         
        $title          =  trans("create order form cart");
        $cart           =  $this->cartsService->get($id);
        $cartItems      =  $this->cartsService->getOrderItems($cart);
        // dd($cartItems)
        $clients        =  $this->usersService->selectable('id','fullname',['wallet']);
		$coupons        =  $this->couponsService->findMatching(applying:'manual')->get();
		$orders         =  $this->ordersService->selectable('id','reference_id');
        $products       =  $this->productsService->getProductsCard();
        $categories     =  $this->categoriesService->selectable('id','name',[['parent_id' ,null]],true);
        $subCategories  =  $this->categoriesService->getSelect('id','name',[['parent_id','!=' ,null]],true);
        $cities         =  $this->citiesService->selectable('id','name');
        $districts      =  $this->districtsService->selectable('id','name');
        $hasSize        =  [55];
        return view('orders::pages.orders.create', compact('title','screen','cart','cartItems','hasSize','cities','districts','clients','coupons','subCategories','categories','products') );
    
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
        $item       = $this->cartsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.carts.show', compact('title','screen','item','comments'));
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
    public function importView(Request $request){
        $title      = trans('Cart import');
        $screen     = 'Cart-import';
        $url        = route('dashboard.carts.import') ;
        $exportUrl  = route('dashboard.carts.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.carts.index') ;
        $cols       = ['user_id'=>'user','phone'=>'phone','data'=>'data'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCartsRequest $request){
        try {
            DB::beginTransaction();
            $this->cartsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Cart saved'));
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
        $filename = $request->headersOnly ? 'carts-template.xlsx' : 'carts.xlsx';
        return Excel::download(new CartsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->cartsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->cartsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Cart restored'));
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
