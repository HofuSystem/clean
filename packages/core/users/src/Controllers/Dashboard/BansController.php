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
use Core\Users\Requests\BansRequest; 
use Core\Users\Requests\ImportBansRequest; 
use Core\Users\Exports\BansExport; 
use Core\Users\Services\BansService;
use Core\Users\Services\UsersService;

class BansController extends Controller
{
    use ApiResponse;
    public function __construct(protected BansService $bansService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('Ban index');
        $screen     = 'bans-index';
        $total      = $this->bansService->totalCount();
        $trash      = $this->bansService->trashCount();
		$admins = $this->usersService->selectable('id','fullname');

        return view('users::pages.bans.list', compact('title','screen','admins',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->bansService->get($id) : null;
        $screen     = isset($item)  ? 'Ban-edit'          : 'Ban-create';
        $title      = isset($item)  ? trans("Ban  edit")  : trans("Ban  create");
		$admins = $this->usersService->selectable('id','fullname');


        return view('users::pages.bans.edit', compact('item','title','screen','admins') );
    }

    public function storeOrUpdate(BansRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->bansService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.bans.delete',$record->id);
            $record->updateUrl  = route('dashboard.bans.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Ban saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Ban index');
        $screen     = 'bans-index';
        $item       = $this->bansService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.bans.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->bansService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Ban deleted'));
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
            $data             = $this->bansService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Ban import');
        $screen     = 'Ban-import';
        $url        = route('dashboard.bans.import') ;
        $exportUrl  = route('dashboard.bans.export',['headersOnly' => 1]) ;
        $cols       = ['level'=>'level','value'=>'value','admin_id'=>'admin','reason'=>'reason','from'=>'starts','to'=>'ends'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','cols'));
    }
    public function import(ImportBansRequest $request){
        try {
            DB::beginTransaction();
            $this->bansService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Ban saved'));
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
        $filename = $request->headersOnly ? 'bans-template.xlsx' : 'bans.xlsx';
        return Excel::download(new BansExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->bansService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->bansService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Ban restored'));
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
