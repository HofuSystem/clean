<?php

namespace Core\Orders\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Categories\Models\Category;
use Core\Categories\Services\CategoriesService;
use Core\Orders\Requests\ChangeStatusRequest;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Coupons\Models\Coupon;
use Core\Orders\Requests\OrdersRequest;
use Core\Orders\Requests\ImportOrdersRequest;
use Core\Orders\Exports\OrdersExport;
use Core\Orders\Services\OrdersService;
use Core\Users\Services\UsersService;
use Core\Users\Models\User;
use Core\Coupons\Services\CouponsService;
use Core\Info\Services\CitiesService;
use Core\Info\Services\DistrictsService;
use Core\Orders\Helpers\OrderHelper;
use Core\Orders\Models\Order;
use Core\Orders\Requests\OrdersApplyCouponRequest;
use Core\Orders\Requests\OrdersChangePayTypeRequest;
use Core\Orders\Requests\OrdersAssignOperatorsRequest;
use Core\Orders\Requests\OrdersAssignRepresentativesRequest;
use Core\Orders\Requests\UpdateDeliveryPriceRequest;
use Core\Orders\Requests\UpdateCostRequest;
use Core\Orders\Requests\UpdateItemRequest;
use Core\Orders\Requests\UpdateOrderRequest;
use Core\Orders\Requests\UpdateB2bFinancialNoteRequest;
use Core\Products\Services\ProductsService;
use Core\Orders\Services\OrderItemsService;
use Core\Orders\Services\ReportReasonsService;
use Core\Products\Models\Product;
use Core\B2B\Models\Contract;
use RuntimeException;

class OrdersController extends Controller
{
    use ApiResponse;
    public function __construct(protected CitiesService $citiesService,protected DistrictsService $districtsService,protected OrdersService $ordersService,protected CategoriesService $categoriesService ,protected UsersService $usersService,protected CouponsService $couponsService,protected ProductsService $productsService,protected OrderItemsService $orderItemsService,protected ReportReasonsService $reportReasonsService){}

    public function index(){
        $title           = trans('Order index');
        $screen          = 'orders-index';
        $total           = $this->ordersService->totalCount();
        $trash           = $this->ordersService->trashCount();
        $types           = DB::table('orders')->whereNotNull('type')->distinct()->pluck('type');
        $statuses        = DB::table('orders')->whereNotNull('status')->distinct()->pluck('status');

        $operators = DB::table('users')
            ->select('users.id', 'users.fullname')
            ->selectRaw("'[\"operator\"]' as roles_json")
            ->whereNull('users.deleted_at')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', 'Core\\Users\\Models\\User')
                    ->where('roles.name', 'operator');
            })
            ->get();

        $representatives = DB::table('users')
            ->select('users.id', 'users.fullname', 'users.phone')
            ->selectRaw("(SELECT CONCAT('[', GROUP_CONCAT(DISTINCT CONCAT('\"', roles.name, '\"')), ']') FROM model_has_roles JOIN roles ON roles.id = model_has_roles.role_id WHERE model_has_roles.model_id = users.id AND model_has_roles.model_type = 'Core\\\\Users\\\\Models\\\\User') as roles_json")
            ->whereNull('users.deleted_at')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', 'Core\\Users\\Models\\User')
                    ->whereIn('roles.name', ['driver', 'technical']);
            })
            ->get();

        $cities = DB::table('cities')
            ->join('city_translations', function ($join) {
                $join->on('cities.id', '=', 'city_translations.city_id')
                    ->where('city_translations.locale', '=', app()->getLocale());
            })
            ->select('cities.id', 'city_translations.name')
            ->get();

        return view('orders::pages.orders.list', compact('title','screen','operators','cities','representatives',"types","statuses","total","trash"));
    }
    public function create(Request $request ){

        $screen             = 'Order-create';
        $title              = trans("Order  create");
        $clients            = DB::table('users')->select('id', 'fullname', 'wallet')->whereNull('deleted_at')->get();
        $coupons            = $this->couponsService->findMatching(applying:'manual')->get();
        $products           = $this->productsService->getProductsCard();
        $categories         = $this->categoriesService->selectable('id','name',[['parent_id' ,null]],true);
        $subCategories      = $this->categoriesService->getSelect('id','name',[['parent_id','!=' ,null]],true);
        $cities             = DB::table('cities')
            ->join('city_translations', function ($join) {
                $join->on('cities.id', '=', 'city_translations.city_id')
                    ->where('city_translations.locale', '=', app()->getLocale());
            })
            ->select('cities.id', 'city_translations.name')
            ->get();
        $districts          = DB::table('districts')
            ->join('district_translations', function ($join) {
                $join->on('districts.id', '=', 'district_translations.district_id')
                    ->where('district_translations.locale', '=', app()->getLocale());
            })
            ->select('districts.id', 'district_translations.name')
            ->get();
        $hasSize            = [55];
        return view('orders::pages.orders.create', compact('title','screen','hasSize','cities','districts','clients','coupons','subCategories','categories','products') );
    }
    public function edit(Request $request,$id ){
        $order                  = $this->ordersService->get($id) ;
        $screen                 = $order->type;
        $title                  = trans($order->type)  ;
        $users                  = DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', 'Core\\Users\\Models\\User');
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereIn('roles.name', ['technical', 'driver'])
            ->whereNull('users.deleted_at')
            ->select('users.id', 'users.fullname', 'users.phone', DB::raw('GROUP_CONCAT(roles.name) as role_names'))
            ->groupBy('users.id', 'users.fullname', 'users.phone')
            ->get()
            ->map(function ($u) {
                $roles = explode(',', $u->role_names ?? '');
                $u->roles = collect($roles)->map(fn($r) => (object)['name' => $r]);
                return $u;
            });
        $operators              = DB::table('users')
            ->select('users.id', 'users.fullname', 'users.phone')
            ->whereNull('users.deleted_at')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', 'Core\\Users\\Models\\User')
                    ->where('roles.name', 'operator');
            })
            ->get();
        $orderItems             = $order->items;
        $comments               = $order->comments->whereNull('parent_id');
        $contract               = $order->company_id ? Contract::forCompany($order->company_id)->currentActive()->first() : null;
        $products               = $this->productsService->getProductsCard($order->type,$order->client,$order->company,$order->b2b_type);
        $categories             = DB::table('categories')
            ->join('category_translations', function ($join) {
                $join->on('categories.id', '=', 'category_translations.category_id')
                    ->where('category_translations.locale', '=', app()->getLocale());
            })
            ->select('categories.id', 'category_translations.name')
            ->whereNull('categories.deleted_at')
            ->whereNull('categories.parent_id')
            ->where('categories.type', OrderHelper::getOrderType($order->type))
            ->get();
        $subCategories          = DB::table('categories')
            ->join('category_translations', function ($join) {
                $join->on('categories.id', '=', 'category_translations.category_id')
                    ->where('category_translations.locale', '=', app()->getLocale());
            })
            ->select('categories.id', 'category_translations.name', 'categories.parent_id')
            ->whereNull('categories.deleted_at')
            ->whereNotNull('categories.parent_id')
            ->where('categories.type', OrderHelper::getOrderType($order->type))
            ->get();
        $categoryNames          = DB::table('category_translations')
            ->where('locale', app()->getLocale())
            ->pluck('name', 'category_id');
        $items                  = $order->items;
        $reportReasons          = DB::table('report_reasons')
            ->join('report_reason_translations', function ($join) {
                $join->on('report_reasons.id', '=', 'report_reason_translations.report_reason_id')
                    ->where('report_reason_translations.locale', '=', app()->getLocale());
            })
            ->select('report_reasons.id', 'report_reason_translations.name')
            ->get();
        $coupons                = DB::table('coupons')
            ->where('status','active')
            ->get()
            ->keyBy('id')
            ->map(function($item){
                $title =  $item->code.", ".trans('type').": ".trans("coupons.".$item->type).", ".trans('value').": ".$item->value;
                if($item->type == 'percentage'){
                    $title .= "%";
                }else{
                    $title .= " ".trans('SAR');
                }
                return $title;
            });
        $editMode               = true;
        $hasSize                = [55];
        $allowedRepresentatives =  [];
        $customerOrdersCount    = $order->client_id ? DB::table('orders')->where('client_id', $order->client_id)->whereNull('deleted_at')->count() : 0;
        $customerTire           = OrderHelper::getCustomerTier($customerOrdersCount);
        $hasDeliveryRep         = $order->orderRepresentatives
            ->where('type', 'delivery')
            ->filter(fn($r) => !empty($r->representative_id))
            ->isNotEmpty();

        if(in_array(OrderHelper::getOrderType($order->type),['maid','host','services'])){
            $allowedRepresentatives[] = 'technical';
        }else if($order->status == 'pending'){
            $allowedRepresentatives[] = 'receiver';
        }else if(
            $order->status == 'order_has_been_delivered_to_admin'
            or
            ($order->status != "receiving_driver_accepted" and !$hasDeliveryRep)
        ){
            $allowedRepresentatives[] = 'delivery';
        }
        if($order->type == 'sales'){
            $allowedRepresentatives = ['delivery'];
        }
        $orderHistories         = DB::table('order_histories')
            ->where('order_id', $order->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($h) {
                $h->old_value = $h->old_value ? json_decode($h->old_value, true) : null;
                $h->new_value = $h->new_value ? json_decode($h->new_value, true) : null;
                $h->changed_by = $h->changed_by ? json_decode($h->changed_by, true) : null;
                $h->created_at = $h->created_at ? \Carbon\Carbon::parse($h->created_at) : null;
                return $h;
            });
        return view('orders::pages.orders.edit', compact('order','title','screen','customerOrdersCount','customerTire','editMode','users','operators','allowedRepresentatives','orderItems','subCategories','categories','categoryNames','comments','products','items','reportReasons','hasSize','coupons','orderHistories') );
    }
    public function changeStatus(ChangeStatusRequest $request,$id){
        try {
            DB::beginTransaction();
            $this->ordersService->changeStatus($id,$request->status,$request->notes);

            DB::commit();
            return $this->returnSuccessMessage(trans('Order status was updated'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function returnOrderContinue(Request $request,$id){
        try {
            DB::beginTransaction();
            $this->ordersService->returnOrderContinue($id);

            DB::commit();
            return $this->returnSuccessMessage(trans('order was returned to continue'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function getDateTimes(Request $request){
        try {
            $dataTimes = $this->ordersService->getDateTimes($request->ids ?? []);
            return $this->returnData(trans('Order status was updated'),['dateTimes' => $dataTimes]);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function store(OrdersRequest $request){
        try {

            DB::beginTransaction();
            $order = $this->ordersService->storeOrder(
                $request->type,
                $request->client_id,
                $request->only(['type','client_id','coupon_id','pay_type','desc','city_id','district_id']),
                $request->items,
                $request->coupon_id,
                $request->wallet_used,
            );
            DB::commit();
            return $this->returnData(trans('Order saved'),['url'=>route('dashboard.orders.edit',$order->id)]);
        }catch(RuntimeException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),[],[],422);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function assignRepresentatives(OrdersAssignRepresentativesRequest $request){
        try {
            DB::beginTransaction();
            $for    = json_decode($request->for);
            $this->ordersService->assignRepresentatives(
                $request->type,
                $request->representative_id,
                $for
            );
            DB::commit();
            return $this->returnSuccessMessage(trans('Order Representatives'));
        }catch(RuntimeException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),[],[],422);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function assignOperators(OrdersAssignOperatorsRequest $request){
        try {
            DB::beginTransaction();
            $for    = json_decode($request->for);
            $this->ordersService->assignOperators(
                $request->operator_id,
                $for
            );
            DB::commit();
            return $this->returnSuccessMessage(trans('Order operators'));
        }catch(RuntimeException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),[],[],422);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function applyCoupon(OrdersApplyCouponRequest $request){
        try {
            DB::beginTransaction();
            $for    = json_decode($request->for);
            $this->ordersService->applyCoupon(
                $request->coupon_id,
                $for
            );
            DB::commit();
            return $this->returnSuccessMessage(trans('apply Coupon success'));
        }catch(RuntimeException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),[],[],422);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function updateDeliveryPrice(UpdateDeliveryPriceRequest $request){
        try {
            DB::beginTransaction();
            $for    = json_decode($request->for);
            $this->ordersService->updateDeliveryPrice(
                $for,
                $request->price,
            );
            DB::commit();
            return $this->returnSuccessMessage(trans('Order operators'));
        }catch(RuntimeException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),[],[],422);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function changePayType(OrdersChangePayTypeRequest $request){
        try {
            DB::beginTransaction();
            $for    = json_decode($request->for);
            $this->ordersService->changePayType(
                $request->pay_type,
                $for
            );
            DB::commit();
            return $this->returnSuccessMessage(trans('Payment type changed successfully'));
        }catch(RuntimeException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),[],[],422);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function update(UpdateOrderRequest $request, $id = null){
        try {

            DB::beginTransaction();
            $this->ordersService->update($id,$request->items);
            $order         =   Order::findOrFail($id);
            $orderItems    =   $order->items()->withTrashed()->get();
            $products      =   Product::where('status', 'active')->get();
            $categories    =   Category::where('status', 'active')->doesntHave('parent')->get();
            $editMode      = true;
            $hasSize       = [55];
            DB::commit();
            return $this->returnData(trans('order updated'),[
                'remade' => view('orders::pages.orders.inc.remade-part', compact(['order','editMode','hasSize','orderItems','products','categories']))->render(),
            ]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function updateItem(UpdateItemRequest $request,$orderId,$itemId)
    {
        try {
            DB::beginTransaction();
            $this->ordersService->updateItem($orderId,$itemId,$request->quantity,$request->height,$request->width,$request->wash_type);
            $order         =   Order::findOrFail($orderId);
            $orderItems    =   $order->items()->withTrashed()->get();
            $products      =   Product::where('status', 'active')->get();
            $categories    =   Category::where('status', 'active')->doesntHave('parent')->get();
            $editMode      =   true;
            $hasSize       =   [55];
            DB::commit();
            $remade = view('orders::pages.orders.inc.remade-part', compact(['order','editMode','hasSize','orderItems','products','categories']))->render();
            return $this->returnData(trans('order item is updated'),[
                'remade' => $remade,
            ]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }

    }
    public function destroyItem(Request $request,$orderId,$itemId)
    {
        try {
            DB::beginTransaction();
            $this->ordersService->destroyItem($itemId);
            $order         =   Order::findOrFail($orderId);
            $orderItems    =   $order->items()->withTrashed()->get();
            $products      =   Product::where('status', 'active')->get();
            $categories    =   Category::where('status', 'active')->doesntHave('parent')->get();
            $editMode      = true;
            $hasSize       = [55];

            DB::commit();
            return $this->returnData(trans('order item was deleted'),[
                'remade' => view('orders::pages.orders.inc.remade-part', compact(['order','hasSize','editMode','orderItems','products','categories']))->render(),
            ]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }

    }
    public function updateCost(UpdateCostRequest $request)
    {
        try {
            DB::beginTransaction();
            $for    = json_decode($request->for);
            $this->ordersService->updateCost($for, $request->washer_cost, $request->lab_cost);
            DB::commit();
            return $this->returnSuccessMessage(trans('Cost updated successfully'));
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
        $title                      = trans('Order show');
        $screen                     = 'orders-show';
        $order                      = $this->ordersService->get($id);
        $comments                   = $order->comments()->where('parent_id',null)->get();
        $orderItems                 = $order->items;
        $items                      = $order->items;
        $customerOrdersCount        = $order->client?->orders()?->count();
        $customerTire               = OrderHelper::getCustomerTier($customerOrdersCount);
        $allowedRepresentatives     =[];
        $orderHistories             = $order->histories;
        return view('orders::pages.orders.edit', compact('order','title','screen','customerOrdersCount','customerTire','allowedRepresentatives','comments','orderItems','items','orderHistories') );
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->ordersService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Order deleted'));
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
            $data             = $this->ordersService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Order import');
        $screen     = 'Order-import';
        $url        = route('dashboard.orders.import') ;
        $exportUrl  = route('dashboard.orders.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.orders.index') ;
        $cols       = ['reference_id'=>'reference_id','type'=>'type','status'=>'status','client_id'=>'client','pay_type'=>'pay type','transaction_id'=>'transaction id','order_status_times'=>'order status times','days_per_week'=>'days per week','days_per_week_names'=>'days per week names','days_per_month_dates'=>'days per month names','note'=>'note','coupon_id'=>'coupon','coupon_data'=>'coupon data','order_price'=>'order price ','delivery_price'=>'delivery price ','total_price'=>'total price','paid'=>'paid','is_admin_accepted'=>'is admin accepted','admin_cancel_reason'=>'admin cancel reason','wallet_used'=>'wallet used','wallet_amount_used'=>'wallet amount used'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrdersRequest $request){
        try {
            DB::beginTransaction();
            $this->ordersService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Order saved'));
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
        $filename = $request->headersOnly ? 'orders-template.xlsx' : 'orders.xlsx';
        return Excel::download(new OrdersExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->ordersService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->ordersService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Order restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function invoice(Request $request,$id){
        $title      = trans('Order invoice');
        $screen     = 'orders-invoice';
        $order       = $this->ordersService->get($id);;
        return view('orders::pages.orders.invoice', compact('title','screen','order'));
    }

    public function updateB2bFinancialNote(UpdateB2bFinancialNoteRequest $request){
        try {
            DB::beginTransaction();
            $for    = json_decode($request->for);
            $this->ordersService->updateB2bFinancialNote($for,$request->b2b_financial_note);
            DB::commit();
            return $this->returnSuccessMessage(trans('B2B Financial Note updated'));
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
