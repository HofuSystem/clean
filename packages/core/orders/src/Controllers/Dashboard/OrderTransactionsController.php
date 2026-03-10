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
use Core\Orders\Requests\OrderTransactionsRequest;
use Core\Orders\Requests\ImportOrderTransactionsRequest;
use Core\Orders\Exports\OrderTransactionsExport;
use Core\Orders\Services\OrderTransactionsService;
use Core\Orders\Services\OrdersService;
use Core\Coupons\Services\CouponsService;
use Core\Wallet\Services\WalletTransactionsService;

class OrderTransactionsController extends Controller
{
    use ApiResponse;
    public function __construct(protected OrderTransactionsService $orderTransactionsService,protected OrdersService $ordersService,protected CouponsService $couponsService,protected WalletTransactionsService $walletTransactionsService){}

    public function index(){
        $title      = trans('order transactions index');
        $screen     = 'order-transactions-index';
        $total      = $this->orderTransactionsService->totalCount();
        $trash      = $this->orderTransactionsService->trashCount();
		$orders = $this->ordersService->selectable('id','reference_id');
		$points = $this->couponsService->selectable('id','code');
		$walletTransactions = $this->walletTransactionsService->selectable('id','type');

        return view('orders::pages.order-transactions.list', compact('title','screen','orders','points','walletTransactions',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->orderTransactionsService->get($id) : null;
        $screen     = isset($item)  ? 'order transactions-edit'          : 'order transactions-create';
        $title      = isset($item)  ? trans("order transactions  edit")  : trans("order transactions  create");
		$orders = $this->ordersService->selectable('id','reference_id');
		$points = $this->couponsService->selectable('id','code');
		$walletTransactions = $this->walletTransactionsService->selectable('id','type');


        return view('orders::pages.order-transactions.edit', compact('item','title','screen','orders','points','walletTransactions') );
    }

    public function storeOrUpdate(OrderTransactionsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->orderTransactionsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.order-transactions.delete',$record->id);
            $record->updateUrl  = route('dashboard.order-transactions.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('order transactions saved'),['entity'=>$record->itemData]);
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
        $title      = trans('order transactions show');
        $screen     = 'order-transactions-show';
        $item       = $this->orderTransactionsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.order-transactions.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->orderTransactionsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('order transactions deleted'));
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
            $data             = $this->orderTransactionsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('order transactions import');
        $screen     = 'order transactions-import';
        $url        = route('dashboard.order-transactions.import') ;
        $exportUrl  = route('dashboard.order-transactions.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.order-transactions.index') ;
        $cols       = ['order_id'=>'order','type'=>'type','online_payment_method'=>'online payment method','amount'=>'amount','transaction_id'=>'transaction id','point_id'=>'point','wallet_transaction_id'=>'wallet Transactions'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportOrderTransactionsRequest $request){
        try {
            DB::beginTransaction();
            $this->orderTransactionsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('order transactions saved'));
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
        $filename = $request->headersOnly ? 'order-transactions-template.xlsx' : 'order-transactions.xlsx';
        return Excel::download(new OrderTransactionsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->orderTransactionsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->orderTransactionsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('OrderTransaction restored'));
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
