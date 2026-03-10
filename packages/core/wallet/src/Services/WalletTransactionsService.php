<?php

namespace Core\Wallet\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\User;
use Core\Wallet\DataResources\Api\WalletTransactionResource;
use Core\Wallet\Models\WalletPackage;
use Core\Wallet\Models\WalletTransaction;
use Core\Wallet\DataResources\WalletTransactionsResource;

class WalletTransactionsService
{
    public function __construct(protected CommentingService $commentingService){}

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

        $recordsTotal       = WalletTransaction::count();
        $recordsFiltered    = WalletTransaction::search()->count();
        $records            = WalletTransaction::select(['id','type','amount','wallet_before','wallet_after','status','transaction_id','bank_name','account_number','iban_number','user_id','added_by_id','package_id','created_at','expired_at','order_id','transaction_type'])
        ->with(['user','addedBy','package','order'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => WalletTransactionsResource::collection($records)
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
        return WalletTransaction::count();
    }
    public function trashCount(){
        return WalletTransaction::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = WalletTransaction::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    public function history($type = null)   {
        $user = auth('api')->user();
        $transactions = WalletTransaction::where('user_id',$user->id)
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
