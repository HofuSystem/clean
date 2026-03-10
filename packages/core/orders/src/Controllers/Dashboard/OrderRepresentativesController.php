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
use Core\Orders\Requests\OrderRepresentativesRequest;
use Core\Orders\Requests\ImportOrderRepresentativesRequest;
use Core\Orders\Exports\OrderRepresentativesExport;
use Core\Orders\Models\OrderRepresentative;
use Core\Orders\Services\OrderRepresentativesService;
use Core\Orders\Services\OrdersService;
use Core\Users\Services\UsersService;
use Core\Orders\Services\OrderItemsService;
use Core\Users\Models\User;

class OrderRepresentativesController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderRepresentativesService $orderRepresentativesService, protected OrdersService $ordersService, protected UsersService $usersService, protected OrderItemsService $orderItemsService)
    {
    }

    public function index()
    {
        $title = trans('OrderRepresentative index');
        $screen = 'order-representatives-index';
        $total = $this->orderRepresentativesService->totalCount();
        $trash = $this->orderRepresentativesService->trashCount();
        $orders = $this->ordersService->selectable('id', 'reference_id');
        $representatives = $this->usersService->selectable('id', 'fullname');
        $items = $this->orderItemsService->selectable('id', 'product_id');

        return view('orders::pages.order-representatives.list', compact('title', 'screen', 'orders', 'representatives', 'items', "total", "trash"));
    }
    public function analysis(Request $request)
    {
        $title              = trans('OrderRepresentative analysis');
        $screen             = 'order-representatives-analysis';
        $allRepresentatives = User::whereHas('roles',function($roleQuery){
            $roleQuery->whereIn('name', ['technical', 'driver']);
        })->get();
        //update anylsis
        $representatives    = User::withCount(['representativeOrders as total_orders' => function ($query) use ($request) {
            $query->analysis($request->city_id,null,null,['delivered', 'finished'])
            ->testAccounts(false)
            ->with('orderRepresentatives')
            ->analysisRepresentatives(['technical', 'delivery'],$request->from ?? now(),$request->to ?? now());
        }])
        ->with(['representativeOrders'=>function($query)use($request){
            $query->analysis($request->city_id,null,null,['delivered', 'finished'])
            ->testAccounts(false)
            ->with('orderRepresentatives')
            ->analysisRepresentatives(['technical', 'delivery'],$request->from ?? now(),$request->to ?? now());
        }])
        ->whereHas('representativeOrders',function($orderQuery)use($request){
            $orderQuery->analysis($request->city_id,$request->from,$request->to,['delivered', 'finished'])
            ->testAccounts(false)
            ->with('orderRepresentatives')
            ->analysisRepresentatives(['technical', 'delivery'],$request->from ?? now(),$request->to ?? now());
        })
        ->when(isset($request->representative_id), function ($query) use ($request) {
            $query->where('users.id', $request->representative_id);
        })->get();

        return view('orders::pages.order-representatives.analysis', compact('title', 'screen',  'representatives','allRepresentatives'));
    }
    public function collectiveAnalysis(Request $request)
    {
        $title              = trans('OrderRepresentative collective Analysis');
        $screen             = 'order-representatives-collective-analysis';
        $allRepresentatives = User::whereHas('roles',function($roleQuery){
            $roleQuery->whereIn('name', ['technical', 'driver']);
        })->get();
        //update anylsis
        $representatives    = User::withCount(['representativeOrders as total_orders_count' => function ($query) use ($request) {
            $query->analysis($request->city_id,null,null,['deleivered', 'finished'])
            ->analysisRepresentatives(['technical', 'delivery'],$request->from ?? now(),$request->to ?? now());
        }])
        ->with(['representativeOrders'=>function($query)use($request){
            $query->analysis($request->city_id,null,null,['deleivered', 'finished'])
            ->analysisRepresentatives(['technical', 'delivery'],$request->from ?? now(),$request->to ?? now());
        }])
        ->whereHas('representativeOrders',function($orderQuery)use($request){
            $orderQuery->analysis($request->city_id,$request->from,$request->to,['delivered', 'finished'])
            ->analysisRepresentatives(['technical', 'delivery'],$request->from ?? now(),$request->to ?? now());
        })
        ->when(isset($request->representative_id), function ($query) use ($request) {
            $query->where('users.id', $request->representative_id);
        })->get()->map(function ($representative) {
            //total orders before any discount
            $representative->total_orders_before_discount = $representative->representativeOrders->sum('order_price');
            //total orders discount
            $representative->total_orders_discount = $representative->representativeOrders->sum('total_coupon');
            //total orders delivary
            $representative->total_orders_delivery = $representative->representativeOrders->sum('delivery_price');
            //total orders visa 
            $representative->total_orders_visa = $representative->representativeOrders->where('pay_type','card')->sum(function($order){ return ($order->paid - $order->wallet_amount_used - $order->points_amount_used);});
            //total orders Wallet 
            $representative->total_orders_wallet = $representative->representativeOrders->sum('wallet_amount_used');
            //total order points
            $representative->total_orders_points = $representative->representativeOrders->sum('points_amount_used');
            //total remaining
            $representative->total_orders_remaining = $representative->representativeOrders->sum(function($order){
                return $order->total_price - $order->paid ;
            });
            return $representative;
        });

        return view('orders::pages.order-representatives.collectiveAnalysis', compact('title', 'screen',  'representatives','allRepresentatives'));
    }


    public function createOrEdit(Request $request, $id = null)
    {
        $item = isset($id) ? $this->orderRepresentativesService->get($id) : null;
        $screen = isset($item) ? 'OrderRepresentative-edit' : 'OrderRepresentative-create';
        $title = isset($item) ? trans("OrderRepresentative  edit") : trans("OrderRepresentative  create");
        $orders = $this->ordersService->selectable('id', 'reference_id');
        $representatives = $this->usersService->selectable('id', 'fullname');
        $items = $this->orderItemsService->selectable('id', 'product_id');


        return view('orders::pages.order-representatives.edit', compact('item', 'title', 'screen', 'orders', 'representatives', 'items'));
    }

    public function storeOrUpdate(OrderRepresentativesRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->orderRepresentativesService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.order-representatives.delete', $record->id);
            $record->updateUrl = route('dashboard.order-representatives.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('OrderRepresentative saved'), ['entity' => $record->itemData]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }


    public function show($id)
    {
        $title = trans('OrderRepresentative index');
        $screen = 'order-representatives-index';
        $item = $this->orderRepresentativesService->get($id);
        ;
        $comments = $item->comments()->where('parent_id', null)->get();
        return view('orders::pages.order-representatives.show', compact('title', 'screen', 'item', 'comments'));
    }


    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $record = $this->orderRepresentativesService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderRepresentative deleted'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $data = $this->orderRepresentativesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function importView(Request $request)
    {
        $title = trans('OrderRepresentative import');
        $screen = 'OrderRepresentative-import';
        $url = route('dashboard.order-representatives.import');
        $exportUrl = route('dashboard.order-representatives.export', ['headersOnly' => 1]);
        $backUrl = route('dashboard.order-representatives.index');
        $cols = ['order_id' => 'order', 'representative_id' => 'representative', 'type' => 'type', 'date' => 'date', 'time' => 'time', 'to_time' => 'to time', 'lat' => 'lat', 'lng' => 'lng', 'location' => 'location', 'has_problem' => 'has problem', 'for_all_items' => 'for_all items', 'items' => 'items'];
        return view('settings::views.import', compact('title', 'screen', 'url', 'exportUrl', 'backUrl', 'cols'));
    }
    public function import(ImportOrderRepresentativesRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->orderRepresentativesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderRepresentative saved'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function export(Request $request)
    {
        $filename = $request->headersOnly ? 'order-representatives-template.xlsx' : 'order-representatives.xlsx';
        return Excel::download(new OrderRepresentativesExport($request->headersOnly, $request->cols), $filename);
    }
    public function comment(CommentRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $comment = $this->orderRepresentativesService->comment($id, $request->content, $request->parent_id);
            DB::commit();
            return $this->returnData(trans('comment created'), ['comment' => new CommentResource($comment)]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
