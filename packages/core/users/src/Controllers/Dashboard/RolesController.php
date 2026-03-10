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
use Core\Users\Requests\RolesRequest;
use Core\Users\Requests\ImportRolesRequest;
use Core\Users\Exports\RolesExport;
use Core\Users\Services\RolesService;
use Core\Users\Services\PermissionsService;

class RolesController extends Controller
{
    use ApiResponse;
    public function __construct(protected RolesService $rolesService,protected PermissionsService $permissionsService){}

    public function index(){
        $title          = trans('Role index');
        $screen         = 'roles-index';
        $total          = $this->rolesService->totalCount();
        $trash          = $this->rolesService->trashCount();
		$permissions    = $this->permissionsService->selectable('id','title');

        return view('users::pages.roles.list', compact('title','screen','permissions',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item           = isset($id)    ? $this->rolesService->get($id) : null;
        $screen         = isset($item)  ? 'Role-edit'          : 'Role-create';
        $title          = isset($item)  ? trans("Role  edit")  : trans("Role  create");
		$permissions    = $this->permissionsService->selectable('id','title')->map(function($item){
            $item->tab = substr($item->name,10);
            return $item;
        });

        $rolePermissions    = [];
        if($item){
            $rolePermissions = $item->permissions->keyBy('name')->toArray();
        }

        $tabs               = [
            'dashboard-permission'          => ['index','analysis' ,'nav-bar','translation','fixed-costs'],
            'company'                       => ['company','contracts','contracts-prices'],
            'users'                         => ['roles','permissions','users','profiles','user-edit-requests','devices','address','bans'],
            'categories-and-services'       => ['categories','sub-categories','services','sub-services',"category-date-times","category-offers","sliders",'prices','category-settings','category-types','additional-features','category-sub-settings'],
            'products'                      => ['products'],
            'coupons'                       => ['coupons'],
            'orders'                        => ['carts','order','report-reasons','delivery-prices'],
            'wallet'                        => ['wallet'],
            'points'                        => ['points'],
            'notifications'                 => ['notifications','banner-notifications'],
            'info'                          => ['countries','cities','districts','nationalities'],
            'favs'                          => ['favs'],
            'workers'                       => ['worker'],
            'blogs'                         => ['blogs' ,'blog-categories'],
            'settings'                      => ['cms-pages','cms-page-detail','settings'],
            'seo'                           => ['pages','sections','features','contact-requests','counters','reasons','businesses'],

        ];


        return view('users::pages.roles.edit', compact('item','title','screen','permissions','tabs','rolePermissions') );
    }

    public function storeOrUpdate(RolesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->rolesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.roles.delete',$record->id);
            $record->updateUrl  = route('dashboard.roles.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Role saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Role index');
        $screen     = 'roles-index';
        $item       = $this->rolesService->get($id);;

        $rolePermissions = $item->permissions->map(function($item){
            $item->tab = substr($item->name,10);
            return $item;
        });
        ;
        $tabs               = [
            'dashboard-permission'          => ['index','analysis' ,'nav-bar','translation'],
            'users'                         => ['roles','permissions','users','profiles','user-edit-requests','devices','address','bans'],
            'categories-and-services'       => ['categories','sub-categories','services','sub-services',"category-date-times","category-offers","sliders",'prices'],
            'products'                      => ['products'],
            'coupons'                       => ['coupons'],
            'orders'                        => ['carts','order','report-reasons','delivery-prices'],
            'wallet'                        => ['wallet'],
            'points'                        => ['points'],
            'notifications'                 => ['notifications','banner-notifications'],
            'info'                          => ['countries','cities','districts','nationalities'],
            'favs'                          => ['favs'],
            'workers'                       => ['worker'],
            'blogs'                         => ['blogs' ,'blog-categories'],
            'settings'                      => ['cms-pages','settings'],
        ];
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.roles.show', compact('title','screen','item','rolePermissions','tabs','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->rolesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Role deleted'));
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
            $data             = $this->rolesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Role import');
        $screen     = 'Role-import';
        $url        = route('dashboard.roles.import') ;
        $exportUrl  = route('dashboard.roles.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.roles.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','name'=>'name','permissions'=>'permissions'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportRolesRequest $request){
        try {
            DB::beginTransaction();
            $this->rolesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Role saved'));
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
        $filename = $request->headersOnly ? 'roles-template.xlsx' : 'roles.xlsx';
        return Excel::download(new RolesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->rolesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->rolesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Role restored'));
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
