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
use Core\Users\Requests\PermissionsRequest; 
use Core\Users\Requests\ImportPermissionsRequest; 
use Core\Users\Exports\PermissionsExport; 
use Core\Users\Services\PermissionsService;

class PermissionsController extends Controller
{
    use ApiResponse;
    public function __construct(protected PermissionsService $permissionsService){}

    public function index(){
        $title      = trans('Permission index');
        $screen     = 'permissions-index';
        $total      = $this->permissionsService->totalCount();
        $trash      = $this->permissionsService->trashCount();

        return view('users::pages.permissions.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->permissionsService->get($id) : null;
        $screen     = isset($item)  ? 'Permission-edit'          : 'Permission-create';
        $title      = isset($item)  ? trans("Permission  edit")  : trans("Permission  create");


        return view('users::pages.permissions.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(PermissionsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->permissionsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.permissions.delete',$record->id);
            $record->updateUrl  = route('dashboard.permissions.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Permission saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Permission index');
        $screen     = 'permissions-index';
        $item       = $this->permissionsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.permissions.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->permissionsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Permission deleted'));
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
            $data             = $this->permissionsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Permission import');
        $screen     = 'Permission-import';
        $url        = route('dashboard.permissions.import') ;
        $exportUrl  = route('dashboard.permissions.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.permissions.index') ;
        $cols       = ['id'=>'id','translations.en.title'=>'title en','translations.ar.title'=>'title ar','name'=>'name'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportPermissionsRequest $request){
        try {
            DB::beginTransaction();
            $this->permissionsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Permission saved'));
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
        $filename = $request->headersOnly ? 'permissions-template.xlsx' : 'permissions.xlsx';
        return Excel::download(new PermissionsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->permissionsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->permissionsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Permission restored'));
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
