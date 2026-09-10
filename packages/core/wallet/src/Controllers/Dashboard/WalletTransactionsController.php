<?php

namespace Core\Wallet\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Wallet\Requests\WalletTransactionsRequest; 
use Core\Wallet\Requests\ImportWalletTransactionsRequest; 
use Core\Wallet\Exports\WalletTransactionsExport; 
use Core\Wallet\Services\WalletTransactionsService;
use Core\Users\Services\UsersService;
use Core\Wallet\Services\WalletPackagesService;
use Core\Orders\Services\OrdersService;

class WalletTransactionsController extends Controller
{
    use ApiResponse;
    public function __construct(
        protected WalletTransactionsService $walletTransactionsService,
        protected UsersService $usersService,
        protected WalletPackagesService $walletPackagesService,
        protected OrdersService $ordersService
    ){}

    public function index(Request $request){
        $title      = trans('Wallet Transactions Summary');
        $screen     = 'wallet-transactions-index';
        $total      = $this->walletTransactionsService->totalCount();
        $trash      = $this->walletTransactionsService->trashCount();
        $period     = $request->get('period', 'all');
        $fromDate   = $request->get('from_created_at');
        $toDate     = $request->get('to_created_at');
        $stats      = $this->walletTransactionsService->getSummaryStats($period, $fromDate, $toDate);

        return view('wallet::pages.wallet-transactions.list', compact('title','screen',"total","trash","stats","period"));
    }

    public function stats(Request $request){
        $period   = $request->get('period', 'all');
        $fromDate = $request->get('from_date') ?: $request->get('from_created_at');
        $toDate   = $request->get('to_date') ?: $request->get('to_created_at');
        $stats    = $this->walletTransactionsService->getSummaryStats($period, $fromDate, $toDate);
        return response()->json($stats);
    }

    public function searchUsers(Request $request)
    {
        $q = trim($request->get('q', ''));
        $users = DB::table('users')
            ->whereNull('deleted_at')
            ->when($q, function($query) use ($q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('fullname', 'LIKE', "%{$q}%")
                        ->orWhere('phone', 'LIKE', "%{$q}%")
                        ->orWhere('email', 'LIKE', "%{$q}%");
                });
            })
            ->select('id', 'fullname', 'phone', 'email', 'wallet')
            ->orderBy('id', 'desc')
            ->limit(40)
            ->get()
            ->map(function($u) {
                $display = $u->fullname ?: 'بدون اسم';
                if ($u->phone) {
                    $display .= ' (' . $u->phone . ')';
                } elseif ($u->email) {
                    $display .= ' (' . $u->email . ')';
                }
                return [
                    'id' => $u->id,
                    'text' => $display,
                    'wallet' => (float) $u->wallet
                ];
            });

        return response()->json(['results' => $users]);
    }

    public function userBalance($id)
    {
        $wallet = (float) DB::table('users')->where('id', $id)->value('wallet');
        return response()->json(['wallet' => $wallet]);
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->walletTransactionsService->get($id) : null;
        $screen     = isset($item)  ? 'WalletTransaction-edit'          : 'WalletTransaction-create';
        $title      = isset($item)  ? trans("WalletTransaction  edit")  : trans("WalletTransaction  create");

        $users      = DB::table('users')->select('id', 'fullname')->whereNull('deleted_at')->get();
        $addedBies  = $users;
        $packages   = DB::table('wallet_packages')->select('id', 'price')->whereNull('deleted_at')->get();
        $orders     = DB::table('orders')->select('id', 'reference_id')->whereNull('deleted_at')->latest('id')->take(500)->get();
        if (isset($item) && $item->order_id && !$orders->contains('id', $item->order_id)) {
            $currentOrder = DB::table('orders')->select('id', 'reference_id')->where('id', $item->order_id)->first();
            if ($currentOrder) {
                $orders->push($currentOrder);
            }
        }

        return view('wallet::pages.wallet-transactions.edit', compact('item','title','screen','users','addedBies','packages','orders'   ) );
    }

    public function storeOrUpdate(WalletTransactionsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->walletTransactionsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.wallet-transactions.delete',$record->id);
            $record->updateUrl  = route('dashboard.wallet-transactions.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('WalletTransaction saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }


    public function show($id){
        $title      = trans('WalletTransaction index');
        $screen     = 'wallet-transactions-index';
        $item       = $this->walletTransactionsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('wallet::pages.wallet-transactions.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->walletTransactionsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('WalletTransaction deleted'));
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
            $data             = $this->walletTransactionsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('WalletTransaction import');
        $screen     = 'WalletTransaction-import';
        $url        = route('dashboard.wallet-transactions.import') ;
        $exportUrl  = route('dashboard.wallet-transactions.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.wallet-transactions.index') ;
        $cols       = ['type'=>'type','amount'=>'amount','wallet_before'=>'wallet before','wallet_after'=>'wallet after','status'=>'status','transaction_id'=>'transaction id','bank_name'=>'bank name','account_number'=>'account number','iban_number'=>'iban number','user_id'=>'user','added_by_id'=>'added by','package_id'=>'package'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportWalletTransactionsRequest $request){
        try {
            DB::beginTransaction();
            $this->walletTransactionsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('WalletTransaction saved'));
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
        $filename = $request->headersOnly ? 'wallet-transactions-template.xlsx' : 'wallet-transactions.xlsx';
        return Excel::download(new WalletTransactionsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->walletTransactionsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->walletTransactionsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('WalletTransaction restored'));
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
