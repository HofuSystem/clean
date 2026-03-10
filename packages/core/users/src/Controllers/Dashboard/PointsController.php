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
use Core\Users\Requests\PointsRequest; 
use Core\Users\Requests\ImportPointsRequest; 
use Core\Users\Exports\PointsExport; 
use Core\Users\Services\PointsService;
use Core\Users\Services\UsersService;

class PointsController extends Controller
{
    use ApiResponse;
    public function __construct(protected PointsService $pointsService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('points index');
        $screen     = 'points-index';
        $total      = $this->pointsService->totalCount();
        $trash      = $this->pointsService->trashCount();
		$users      = $this->usersService->selectable('id','fullname');

        return view('users::pages.points.list', compact('title','screen','users',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->pointsService->get($id) : null;
        $screen     = isset($item)  ? 'points-edit'          : 'points-create';
        $title      = isset($item)  ? trans("points  edit")  : trans("points  create");
		$users = $this->usersService->selectable('id','fullname');


        return view('users::pages.points.edit', compact('item','title','screen','users') );
    }

    public function storeOrUpdate(PointsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->pointsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.points.delete',$record->id);
            $record->updateUrl  = route('dashboard.points.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('points saved'),['entity'=>$record->itemData]);
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
        $title      = trans('points index');
        $screen     = 'points-index';
        $item       = $this->pointsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.points.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->pointsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('points deleted'));
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
            $data             = $this->pointsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('points import');
        $screen     = 'points-import';
        $url        = route('dashboard.points.import') ;
        $exportUrl  = route('dashboard.points.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.points.index') ;
        $cols       = ['title'=>'title','amount'=>'amount','operation'=>'operation','expire_at'=>'expire at','user_id'=>'user'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportPointsRequest $request){
        try {
            DB::beginTransaction();
            $this->pointsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('points saved'));
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
        $filename = $request->headersOnly ? 'points-template.xlsx' : 'points.xlsx';
        return Excel::download(new PointsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->pointsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->pointsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Point restored'));
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
