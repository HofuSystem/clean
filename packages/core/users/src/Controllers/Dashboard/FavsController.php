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
use Core\Users\Requests\FavsRequest; 
use Core\Users\Requests\ImportFavsRequest; 
use Core\Users\Exports\FavsExport; 
use Core\Users\Services\FavsService;
use Core\Users\Services\UsersService;

class FavsController extends Controller
{
    use ApiResponse;
    public function __construct(protected FavsService $favsService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('Fav index');
        $screen     = 'favs-index';
        $total      = $this->favsService->totalCount();
        $trash      = $this->favsService->trashCount();
		$users = $this->usersService->selectable('id','fullname');

        return view('users::pages.favs.list', compact('title','screen','users',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->favsService->get($id) : null;
        $screen     = isset($item)  ? 'Fav-edit'          : 'Fav-create';
        $title      = isset($item)  ? trans("Fav  edit")  : trans("Fav  create");
		$users = $this->usersService->selectable('id','fullname');


        return view('users::pages.favs.edit', compact('item','title','screen','users') );
    }

    public function storeOrUpdate(FavsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->favsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.favs.delete',$record->id);
            $record->updateUrl  = route('dashboard.favs.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Fav saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Fav index');
        $screen     = 'favs-index';
        $item       = $this->favsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.favs.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->favsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Fav deleted'));
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
            $data             = $this->favsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Fav import');
        $screen     = 'Fav-import';
        $url        = route('dashboard.favs.import') ;
        $exportUrl  = route('dashboard.favs.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.favs.index') ;
        $cols       = ['favs_type'=>'favs type','favs_id'=>'favs','user_id'=>'user'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportFavsRequest $request){
        try {
            DB::beginTransaction();
            $this->favsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Fav saved'));
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
        $filename = $request->headersOnly ? 'favs-template.xlsx' : 'favs.xlsx';
        return Excel::download(new FavsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->favsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->favsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Fav restored'));
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
