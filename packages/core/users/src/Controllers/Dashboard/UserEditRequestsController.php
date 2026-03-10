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
use Core\Users\Requests\UserEditRequestsRequest; 
use Core\Users\Requests\ImportUserEditRequestsRequest; 
use Core\Users\Exports\UserEditRequestsExport; 
use Core\Users\Services\UserEditRequestsService;
use Core\Users\Services\UsersService;

class UserEditRequestsController extends Controller
{
    use ApiResponse;
    public function __construct(protected UserEditRequestsService $userEditRequestsService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('UserEditRequest index');
        $screen     = 'user-edit-requests-index';
        $total      = $this->userEditRequestsService->totalCount();
        $trash      = $this->userEditRequestsService->trashCount();
		$users = $this->usersService->selectable('id','fullname');

        return view('users::pages.user-edit-requests.list', compact('title','screen','users',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->userEditRequestsService->get($id) : null;
        $screen     = isset($item)  ? 'UserEditRequest-edit'          : 'UserEditRequest-create';
        $title      = isset($item)  ? trans("UserEditRequest  edit")  : trans("UserEditRequest  create");
		$users = $this->usersService->selectable('id','fullname');


        return view('users::pages.user-edit-requests.edit', compact('item','title','screen','users') );
    }

    public function storeOrUpdate(UserEditRequestsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->userEditRequestsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.user-edit-requests.delete',$record->id);
            $record->updateUrl  = route('dashboard.user-edit-requests.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('UserEditRequest saved'),['entity'=>$record->itemData]);
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
        $title      = trans('UserEditRequest index');
        $screen     = 'user-edit-requests-index';
        $item       = $this->userEditRequestsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.user-edit-requests.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->userEditRequestsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('UserEditRequest deleted'));
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
            $data             = $this->userEditRequestsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('UserEditRequest import');
        $screen     = 'UserEditRequest-import';
        $url        = route('dashboard.user-edit-requests.import') ;
        $exportUrl  = route('dashboard.user-edit-requests.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.user-edit-requests.index') ;
        $cols       = ['fullname'=>'full name','email'=>'email','phone'=>'phone','user_id'=>'user'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportUserEditRequestsRequest $request){
        try {
            DB::beginTransaction();
            $this->userEditRequestsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('UserEditRequest saved'));
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
        $filename = $request->headersOnly ? 'user-edit-requests-template.xlsx' : 'user-edit-requests.xlsx';
        return Excel::download(new UserEditRequestsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->userEditRequestsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->userEditRequestsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('UserEditRequest restored'));
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
