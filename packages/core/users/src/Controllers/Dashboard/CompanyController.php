<?php

namespace Core\Users\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Users\Requests\AddressesRequest; 
use Core\Users\Requests\ImportAddressesRequest; 
use Core\Users\Exports\AddressesExport; 
use Core\Users\Services\AddressesService;
use Core\Info\Services\CitiesService;
use Core\Info\Services\DistrictsService;
use Core\Users\Services\UsersService;
use Core\Users\Services\RolesService;
use Core\Coupons\Services\CouponsService;
use Core\Orders\Models\Order;
use Core\Orders\Services\OrdersService;
use Core\Wallet\Services\WalletPackagesService;
use Core\Users\Models\Role;

class CompanyController extends Controller
{
    use ApiResponse;
    public function __construct(
        protected AddressesService $addressesService,
        protected CitiesService $citiesService,
        protected DistrictsService $districtsService,
        protected UsersService $usersService,
        protected RolesService $rolesService,
        protected CouponsService $couponsService,
        protected WalletPackagesService $walletPackagesService,
        protected OrdersService $ordersService
        ){}

    public function addresses(){
        $title      = trans('Address index');
        $screen     = 'addresses-index';
        $total      = $this->addressesService->totalCount();
        $trash      = $this->addressesService->trashCount();
		$cities = $this->citiesService->selectable('id','name');
		$districts = $this->districtsService->selectable('id','name');
		$users = $this->usersService->selectable('id','fullname');
        $forCompany = true;
        return view('users::pages.addresses.list', compact('title','forCompany','screen','cities','districts','users',"total","trash"));
    }
    public function orders(){
        $title           = trans('Order index');
        $screen          = 'orders-index';
        $total           = $this->ordersService->totalCount();
        $trash           = $this->ordersService->trashCount();
        $types           = Order::groupBy('type')->select('type')->get()->pluck('type');
        $statuses        = Order::groupBy('status')->select('status')->get()->pluck('status');
        $operators       = $this->usersService->selectable('id','fullname',['wallet'],"operator");
        $representatives = $this->usersService->selectable('id','fullname',['wallet'],["driver","technical"]);
        $cities          = $this->citiesService->selectable('id','name');
        $forCompany = true;     
        return view('orders::pages.orders.list', compact('title','forCompany','screen','operators','cities','representatives',"types","statuses","total","trash"));
    }





    public function accounts(){
        $title      = trans('company index');
        $screen     = 'company-index';
        $total      = $this->usersService->totalCount();
        $trash      = $this->usersService->trashCount();
		$roles      = $this->rolesService->selectable('id','name');
		$clients    = $this->usersService->selectable('id','fullname');
		$coupons    = $this->couponsService->selectable('id','code');
		$users      = $this->usersService->selectable('id','fullname');
		$addedBies  = $this->usersService->selectable('id','fullname');
		$packages   = $this->walletPackagesService->selectable('id','price');
		$cities     = $this->citiesService->selectable('id','name');
		$districts  = $this->districtsService->selectable('id','name');
        $forCompany = true;
        // Get total users and total users in trash by role using the User and Role models directly, merged into one collection
        $rolesWithUserCounts = Role::withCount([
            'users as users_count' => function($query) {
            $query->whereNull('deleted_at');
            },
            'users as users_trash_count' => function($query) {
            $query->withoutGlobalScopes()->whereNotNull('deleted_at');
            }
        ])->get(['id', 'name', 'users_count', 'users_trash_count']);

        return view('users::pages.users.list', compact('title','forCompany','screen','roles','clients','coupons','users','addedBies','packages','cities','districts',"total","trash",'rolesWithUserCounts'));
    }




        
}
