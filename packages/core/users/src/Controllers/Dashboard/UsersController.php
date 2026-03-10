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
use Core\Users\Requests\UsersRequest; 
use Core\Users\Requests\ImportUsersRequest; 
use Core\Users\Exports\UsersExport; 
use Core\Users\Services\UsersService;
use Core\Users\Services\RolesService;
use Core\Coupons\Services\CouponsService;
use Core\Wallet\Services\WalletPackagesService;
use Core\Info\Services\CitiesService;
use Core\Info\Services\CountriesService;
use Core\Info\Services\DistrictsService;
use Core\Orders\Helpers\OrderHelper;
use Core\Users\Models\Profile;
use Core\Users\Models\Role;
use Core\Users\Requests\ProfilesRequest;
use Core\Users\Requests\UsersUpdatePasswordRequest;
use Core\Users\Services\ProfilesService;

class UsersController extends Controller
{
    use ApiResponse;
    public function __construct(protected UsersService $usersService,protected ProfilesService $profilesService,protected RolesService $rolesService,protected CouponsService $couponsService,protected WalletPackagesService $walletPackagesService,protected CountriesService $countriesService,protected CitiesService $citiesService,protected DistrictsService $districtsService){}

    public function index(){
        $title      = trans('User index');
        $screen     = 'users-index';
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
        // Get total users and total users in trash by role using the User and Role models directly, merged into one collection
        $rolesWithUserCounts = Role::withCount([
            'users as users_count' => function($query) {
            $query->whereNull('deleted_at');
            },
            'users as users_trash_count' => function($query) {
            $query->withoutGlobalScopes()->whereNotNull('deleted_at');
            }
        ])->get(['id', 'name', 'users_count', 'users_trash_count']);

        return view('users::pages.users.list', compact('title','screen','roles','clients','coupons','users','addedBies','packages','cities','districts',"total","trash",'rolesWithUserCounts'));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->usersService->get($id) : null;
        $screen     = isset($item)  ? 'User-edit'          : 'User-create';
        $title      = isset($item)  ? trans("User  edit")  : trans("User  create");
		$roles      = $this->rolesService->selectable('id','name');
		$clients    = $this->usersService->selectable('id','fullname');
		$coupons    = $this->couponsService->selectable('id','code');
		$users      = $this->usersService->selectable('id','fullname');
		$technicals = $this->usersService->selectable('id','fullname',[],'technical');
		$addedBies  = $this->usersService->selectable('id','fullname');
		$packages   = $this->walletPackagesService->selectable('id','price');
        $countries  = $this->countriesService->selectable('id','name');
		$cities     = $this->citiesService->selectable('id','name');
		$districts  = $this->districtsService->selectable('id','name');

        return view('users::pages.users.edit', compact('item','title','screen','roles','technicals','clients','coupons','users','addedBies','packages','countries','cities','districts') );
    }

    public function storeOrUpdate(UsersRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->usersService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.users.delete',$record->id);
            $record->updateUrl  = route('dashboard.users.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('User saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function updatePassword(UsersUpdatePasswordRequest $request, $id){
        try {
            DB::beginTransaction();
            $record             = $this->usersService->updatePassword($id,$request->password);
            
            DB::commit();
            return $this->returnSuccessMessage(trans('password was updated'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function updateProfile(ProfilesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $id                 = Profile::where('user_id',$request->user_id)->first()?->id;
            $record             = $this->profilesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.profiles.delete',$record->id);
            $record->updateUrl  = route('dashboard.profiles.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('User saved'),['entity'=>$record->itemData]);
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
        $title              = trans('User index');
        $screen             = 'users-index';
        $item               = $this->usersService->get($id);;
        $comments           = $item->comments()->where('parent_id',null)->get();
        $totalSpent         = $item->orders()->sum('order_price');
        $totalOrders        = $item->orders()->count();
        $firstOrderDate     = $item->orders()->first()?->created_at;
        $lastOrderDate      = $item->orders()->latest()->first()?->created_at;
        $customerTire       = OrderHelper::getCustomerTier($totalOrders);
        return view('users::pages.users.show', compact('title','screen','item','totalSpent','customerTire','totalOrders','firstOrderDate','lastOrderDate','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->usersService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('User deleted'));
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
            $data             = $this->usersService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
                report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('User import');
        $screen     = 'User-import';
        $url        = route('dashboard.users.import') ;
        $exportUrl  = route('dashboard.users.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.users.index') ;
        $cols       = ['image'=>'avatar','fullname'=>'full name','email'=>'email','email_verified_at'=>'email verified at','phone'=>'phone','phone_verified_at'=>'phone verified at','roles'=>'roles','is_active'=>'is active','is_allow_notify'=>'is allow notify','date_of_birth'=>'date of birth','identity_number'=>'identity number','wallet'=>'wallet','points_balance'=>'points balance','gender'=>'gender','rate_avg'=>'rate avg','referral_code'=>'referral code','verified_code'=>'verified code','last_login_at'=>'last login at'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportUsersRequest $request){
        try {
            DB::beginTransaction();
            $this->usersService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('User saved'));
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
        $filename = $request->headersOnly ? 'users-template.xlsx' : 'users.xlsx';
        return Excel::download(new UsersExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->usersService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->usersService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('User restored'));
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    
}
