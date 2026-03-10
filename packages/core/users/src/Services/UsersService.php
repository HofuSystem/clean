<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\User;
use Core\Users\DataResources\UsersResource;
use Core\Orders\Services\OrdersService;
use Core\Wallet\Services\WalletTransactionsService;
use Spatie\Permission\Models\Role;

class UsersService
{
    public function __construct(protected CommentingService $commentingService,protected RolesService $rolesService,protected OrdersService $ordersService,protected WalletTransactionsService $walletTransactionsService,protected PointsService $pointsService,protected AddressesService $addressesService){}

    public function selectable(string $key,string $value,array $selected =[],$role = null,$with = []){
        $selected[] = 'id';
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return User::select($selected)
        ->with($with)
        ->underMyControl()
        ->when(isset($role),function($roleQuery) use ($role){
            $roleQuery->whereHas('roles',function($roleQuery) use ($role){
                $roleQuery->when(is_array($role),function($roleQuery)use($role){
                    $roleQuery->whereIn('name',$role);
                })->when(is_string($role),function($roleQuery)use($role){
                    $roleQuery->where('name',$role);
                });
            });
        })->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['image','fullname','email','email_verified_at','phone','phone_verified_at','is_active','is_allow_notify','date_of_birth','identity_number','wallet','points_balance','gender','rate_avg','referral_code','verified_code','last_login_at','translations']),ARRAY_FILTER_USE_KEY);
        $record     = User::updateOrCreate(['id' => $id],$recordData);
        $roles      = Role::whereIn('id',$data['roles'])->get(); 
        $record->syncRoles($roles);
        $technicals = $data['technicals'] ?? [];
        User::where('operator_id',$record->id)->update(['operator_id' => null]);
        if(isset($technicals) && count($technicals) > 0){
            User::whereIn('id',$technicals)->update(['operator_id' => $record->id]);
        }

        
        if(!isset($id)){
            //saving on create the related ordersItems
            $ordersItems            = $data['orders'] ?? [];
            foreach ($ordersItems as $index => $itemValues) {
                $itemValues['client_id'] = $record->id;
                $this->ordersService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
            //saving on create the related walletTransactionsItems
            $walletTransactionsItems            = $data['walletTransactions'] ?? [];
            foreach ($walletTransactionsItems as $index => $itemValues) {
                $itemValues['user_id'] = $record->id;
                $this->walletTransactionsService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
            //saving on create the related pointsItems
            $pointsItems            = $data['points'] ?? [];
            foreach ($pointsItems as $index => $itemValues) {
                $itemValues['user_id'] = $record->id;
                $this->pointsService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
            //saving on create the related addressesItems
            $addressesItems            = $data['addresses'] ?? [];
            foreach ($addressesItems as $index => $itemValues) {
                $itemValues['user_id'] = $record->id;
                $this->addressesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }
           
        return $record;
    }
    public function updatePassword($id,$password){
        $user = $this->get($id);
        $user->update(['password' => $password]);
        // Delete all tokens (for API authentication)
        $user->tokens()->delete();

        return $user;
    }

    public function get(int $id){
        return  User::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = User::underMyControl()->findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = User::underMyControl()->count();
        $recordsFiltered    = User::underMyControl()->search()->count();
        $records            = User::underMyControl()
            ->select([
                'users.id',
                'users.image',
                'users.fullname',
                'users.email',
                'users.phone',
                'users.is_active',
                'users.is_allow_notify',
                'users.date_of_birth',
                'users.gender',
                'users.created_at',
                \DB::raw('(SELECT MAX(created_at) FROM orders WHERE orders.client_id = users.id) as latest_order_at')
            ])
            ->with(['roles'])
            ->withCount(['orders as orders_count' => function ($query) {
                $query->whereIn('status', ['finished', 'delivered']);
            }])
        
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => UsersResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            User::find($value['id'])->update([$orderBy=>$value['order']]);
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
         User::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return User::underMyControl()->count();
    }
    public function trashCount(){
        return User::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = User::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
