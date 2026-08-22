<?php

namespace Core\Wallet\Services;

use Core\Comments\Services\CommentingService;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Core\Wallet\DataResources\Api\WalletTransactionResource;
use Core\Wallet\Models\WalletPackage;
use Core\Wallet\Models\WalletTransaction;
use Core\Wallet\DataResources\WalletTransactionsResource;

class WalletTransactionsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function getTestAccountIds(): array
    {
        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if (is_string($testAccounts)) {
            $testAccounts = json_decode($testAccounts, true) ?? [];
        }
        return is_array($testAccounts) ? array_filter($testAccounts) : [];
    }

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return WalletTransaction::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['type','amount','wallet_before','wallet_after','status','transaction_id','bank_name','account_number','iban_number','transaction_type','user_id','added_by_id','package_id','translations','expired_at','order_id']),ARRAY_FILTER_USE_KEY);
        if(!isset($recordData['status'])){
            $recordData['status'] = 'accepted';
        }
        $record     = WalletTransaction::updateOrCreate(['id' => $id],$recordData);
        return $record;
    }

    public function get(int $id){
        return  WalletTransaction::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = WalletTransaction::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){
        $testAccounts = $this->getTestAccountIds();

        $recordsTotalQuery = WalletTransaction::query();
        if (!empty($testAccounts)) {
            $recordsTotalQuery->whereNotIn('user_id', $testAccounts);
        }
        $recordsTotal       = $recordsTotalQuery->count();
        $recordsFiltered    = WalletTransaction::search()->count();
        $records            = WalletTransaction::select(['id','type','amount','wallet_before','wallet_after','status','transaction_id','bank_name','account_number','iban_number','user_id','added_by_id','package_id','created_at','expired_at','order_id','transaction_type','notes'])
        ->with(['user:id,fullname,phone,email','addedBy:id,fullname,email','package:id,price,value','order:id,reference_id','orderTransaction.order:id,reference_id'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => WalletTransactionsResource::collection($records)
        ];
    }

    public function getSummaryStats($period = 'all', $fromDate = null, $toDate = null)
    {
        $testAccounts = $this->getTestAccountIds();

        // 1. Total Current Balances of all active non-test clients (not bound by date)
        $usersQuery = \Illuminate\Support\Facades\DB::table('users')
            ->whereNull('deleted_at');
        if (!empty($testAccounts)) {
            $usersQuery->whereNotIn('id', $testAccounts);
        }
        $totalClientsBalance = (float) $usersQuery->sum('wallet');

        // Date constraints based on period
        $query = \Illuminate\Support\Facades\DB::table('wallet_transactions')
            ->whereNull('deleted_at');

        // Exclude testing accounts
        if (!empty($testAccounts)) {
            $query->whereNotIn('user_id', $testAccounts);
        }

        if ($period === 'custom' && ($fromDate || $toDate)) {
            if ($fromDate) {
                $query->whereDate('created_at', '>=', \Carbon\Carbon::parse($fromDate));
            }
            if ($toDate) {
                $query->whereDate('created_at', '<=', \Carbon\Carbon::parse($toDate));
            }
        } elseif ($period === 'today') {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($period === 'this_month') {
            $query->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()]);
        } elseif ($period === 'last_month') {
            $query->whereBetween('created_at', [\Carbon\Carbon::now()->subMonth()->startOfMonth(), \Carbon\Carbon::now()->subMonth()->endOfMonth()]);
        } elseif ($period === 'last_3_months') {
            $query->where('created_at', '>=', \Carbon\Carbon::now()->subMonths(3)->startOfDay());
        } elseif ($period === 'this_year') {
            $query->whereBetween('created_at', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()]);
        }

        // Consolidated aggregate query in 1 single shot!
        $stats = (clone $query)->selectRaw("
            COALESCE(SUM(CASE WHEN (transaction_type IN ('charge', 'deposit', 'remaining_amount') OR package_id IS NOT NULL OR (type = 'deposit' AND (transaction_type IS NULL OR transaction_type NOT IN ('compensation_add', 'promotional_add', 'cashback', 'reward')))) THEN amount ELSE 0 END), 0) as total_recharge,
            COUNT(DISTINCT CASE WHEN (transaction_type IN ('charge', 'deposit', 'remaining_amount') OR package_id IS NOT NULL OR (type = 'deposit' AND (transaction_type IS NULL OR transaction_type NOT IN ('compensation_add', 'promotional_add', 'cashback', 'reward')))) THEN user_id ELSE NULL END) as recharge_users_count,

            COALESCE(SUM(CASE WHEN (transaction_type = 'order_payment' OR (type = 'withdraw' AND (transaction_type IS NULL OR transaction_type NOT IN ('expiry_deduction', 'manual_admin_deduction')))) THEN amount ELSE 0 END), 0) as total_paid_from_wallet,
            COUNT(CASE WHEN (transaction_type = 'order_payment' OR (type = 'withdraw' AND (transaction_type IS NULL OR transaction_type NOT IN ('expiry_deduction', 'manual_admin_deduction')))) THEN 1 ELSE NULL END) as paid_orders_count,

            COALESCE(SUM(CASE WHEN transaction_type IN ('promotional_add', 'cashback', 'reward') THEN amount ELSE 0 END), 0) as total_promotional,
            COUNT(DISTINCT CASE WHEN transaction_type IN ('promotional_add', 'cashback', 'reward') THEN user_id ELSE NULL END) as promo_users_count,

            COALESCE(SUM(CASE WHEN transaction_type = 'compensation_add' THEN amount ELSE 0 END), 0) as total_compensations,
            COUNT(CASE WHEN transaction_type = 'compensation_add' THEN 1 ELSE NULL END) as compensations_count
        ")->first();

        $totalPromotional = (float) ($stats->total_promotional ?? 0);
        $promoUsersCount = (int) ($stats->promo_users_count ?? 0);
        $avgPromotional = $promoUsersCount > 0 ? ($totalPromotional / $promoUsersCount) : 0;

        return [
            'total_clients_balance'   => $totalClientsBalance,
            'total_recharge'          => (float) ($stats->total_recharge ?? 0),
            'recharge_users_count'    => (int) ($stats->recharge_users_count ?? 0),
            'total_paid_from_wallet'  => (float) ($stats->total_paid_from_wallet ?? 0),
            'paid_orders_count'       => (int) ($stats->paid_orders_count ?? 0),
            'total_promotional'       => $totalPromotional,
            'avg_promotional_per_user'=> $avgPromotional,
            'total_compensations'     => (float) ($stats->total_compensations ?? 0),
            'compensations_count'     => (int) ($stats->compensations_count ?? 0),
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            WalletTransaction::find($value['id'])->update([$orderBy=>$value['order']]);
        }
    }
    public function import(array $items){
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item,$item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int $id,string $content,int | null $parent_id){
       return $this->commentingService->comment(
         WalletTransaction::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        $testAccounts = $this->getTestAccountIds();
        $query = WalletTransaction::query();
        if (!empty($testAccounts)) {
            $query->whereNotIn('user_id', $testAccounts);
        }
        return $query->count();
    }
    public function trashCount(){
        $testAccounts = $this->getTestAccountIds();
        $query = WalletTransaction::onlyTrashed();
        if (!empty($testAccounts)) {
            $query->whereNotIn('user_id', $testAccounts);
        }
        return $query->count();
    }
    public function restore(int $id){
        $record = WalletTransaction::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    public function history($type = null)   {
        $user = auth('api')->user();
        $transactions = WalletTransaction::with('order')
        ->where('user_id',$user->id)
        ->when($type and in_array($type,['deposit','withdraw']),function($query) use ($type){
            $query->where('type',$type);
        })
        ->when($type and !in_array($type,['deposit','withdraw']),function($query) use ($type){
            $query->where('transaction_type',$type);
        })
        ->latest()->get();
        $data ['data'] = WalletTransactionResource::collection($transactions);
        $data ['wallet'] = $user->wallet;
        return $data;
     }

    public function charge(array $data,$user)
    {
        if(isset($data['check_id'])){
            $package = WalletPackage::find($data['check_id']);
            if($package){
                $data['amount'] = $package->value;
            }
        }
        $amount                 = $data['amount'];
        $wallet                 = $user->wallet;
        $before_wallet_charge   = ['wallet_before' => $wallet, 'wallet_after' => ($wallet + $amount) , 'transaction_type' => 'charge' , 'added_by_id' => $user->id , 'status' => 'accepted' ];
        $transaction            = $user->walletTransactions()->create($data + $before_wallet_charge);
        $transaction            = WalletTransactionResource::make($transaction);
        return $transaction;
    }

    public function withdraw(array $data)
    {
        $user = auth('api')->user();
        $amount = $data['amount'];
        $wallet = $user->wallet;
        $before_wallet_charge = ['wallet_before' => $wallet, 'wallet_after' => ($wallet - $amount) , 'transaction_type' => 'withdraw' , 'added_by_id' => $user->id , 'status' => 'pending'];
        $transaction = $user->walletTransactions()->create($data + $before_wallet_charge);
        $transaction = WalletTransactionResource::make($transaction);
        $user->update(['wallet' => $transaction->wallet_after]);
        return $transaction;
    }
    public static function updateUserWallet($userId){
        $depositWallet = WalletTransaction::where('user_id', $userId)->where('type', 'deposit')->sum('amount');
        $withdrawWallet = WalletTransaction::where('user_id', $userId)->where('type', 'withdraw')->sum('amount');
        $user = User::find($userId);
        $currentWallet = $depositWallet - $withdrawWallet;
        if ($user) {
            $user->update(['wallet' => $currentWallet]);
        }
        return $currentWallet;
    }
}
