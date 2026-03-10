<?php

namespace Core\Wallet\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\PaymentGateways\Models\PaymentTransaction;
use Core\PaymentGateways\Services\MyFatoorahService;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\User;
use Core\Users\Services\UsersService;
use Core\Wallet\Models\WalletTransaction;
use Core\Wallet\Requests\Api\ChargeWalletRequest;
use Core\Wallet\Requests\Api\WithdrawRequest;
use Core\Wallet\Services\WalletPackagesService;
use Core\Wallet\Services\WalletTransactionsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
class WalletController extends Controller
{
    use ApiResponse;

    public function __construct(protected WalletTransactionsService $walletTransactionsService,protected UsersService $usersService,protected WalletPackagesService $walletPackagesService,protected MyFatoorahService $myfatoorahService){}
    public function history(Request $request){
        try {
            $record = $this->walletTransactionsService->history($request->type);
            return $this->returnData(trans('wallet history'),$record);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),['status' =>'fail'],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],['status' =>'fail'],422);
        }
    }

    public function packages()
    {
        try {
            $record = $this->walletPackagesService->packages();

            return $this->returnData(trans('wallet packages'),$record);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),['status' =>'fail'],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],['status' =>'fail'],422);
        }

    }

    public function charge(ChargeWalletRequest $request)
    {
        try {
            //TODO:add package_id
            DB::beginTransaction();
            $record = $this->walletTransactionsService->charge($request->validated(),auth('api')->user());
            DB::commit();
            return $this->returnData(trans('wallet charge successfully'),['data'=>$record]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),['status' =>'fail'],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],['status' =>'fail'],422);
        }
    }
    public function chargeV2(ChargeWalletRequest $request)
    {
        try {
            //TODO:add package_id
            DB::beginTransaction();
            $previousTransaction = WalletTransaction::where('user_id', auth('api')->user()->id)
                ->where('type', 'charge')->count();
            $transactionNumber = $previousTransaction + 1;
            $data = [
                'payment_url' => $this->myfatoorahService->createTransaction($request->amount, null, $request->all(), auth('api')->user()->id, 'wallet_charge','charge-user-'.auth('api')->user()->id.'-number-'.$transactionNumber.'-'),
            ];
            DB::commit();
            return $this->returnData(trans('wallet charge successfully'),['data'=>$data]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),['status' =>'fail'],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],['status' =>'fail'],422);
        }
    }


    public function withdraw(WithdrawRequest $request)
    {
        try {
            $user = User::find(379);
            //$user = auth()->user();
            if($request->validated()['amount'] > $user->wallet){
                return $this->returnErrorMessage(trans('There is not enough balance'),[],['status' =>'fail'],422);
            }
            DB::beginTransaction();
            $record = $this->walletTransactionsService->withdraw($request->validated());
            DB::commit();
            return $this->returnData(trans('wallet charge successfully'),$record);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),['status' =>'fail'],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],['status' =>'fail'],422);
        }
    }
}
