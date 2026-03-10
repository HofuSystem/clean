<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Admin\Requests\RoutesRecordsRequest; 
use Core\Admin\Requests\ImportRoutesRecordsRequest; 
use Core\Admin\Exports\RoutesRecordsExport; 
use Core\Admin\Services\RoutesRecordsService;
use Core\Users\Services\UsersService;

class RoutesRecordsController extends Controller
{
    use ApiResponse;
    public function __construct(protected RoutesRecordsService $routesRecordsService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('routes records index');
        $screen     = 'routes-records-index';
        $total      = $this->routesRecordsService->totalCount();
        $trash      = $this->routesRecordsService->trashCount();
		$users      = $this->usersService->selectable('id','fullname',['phone']);
		$ipAddresses = $this->usersService->selectable('id','fullname');

        return view('admin::pages.routes-records.list', compact('title','screen','users','ipAddresses',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->routesRecordsService->get($id) : null;
        $screen     = isset($item)  ? 'routes records-edit'          : 'routes records-create';
        $title      = isset($item)  ? trans("routes records  edit")  : trans("routes records  create");
		$users = $this->usersService->selectable('id','fullname');
		$ipAddresses = $this->usersService->selectable('id','fullname');


        return view('admin::pages.routes-records.edit', compact('item','title','screen','users','ipAddresses') );
    }

    public function storeOrUpdate(RoutesRecordsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->routesRecordsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.routes-records.delete',$record->id);
            $record->updateUrl  = route('dashboard.routes-records.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('routes records saved'),['entity'=>$record->itemData]);
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
        $title      = trans('routes records index');
        $screen     = 'routes-records-index';
        $item       = $this->routesRecordsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('admin::pages.routes-records.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->routesRecordsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('routes records deleted'));
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
            $data             = $this->routesRecordsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('routes records import');
        $screen     = 'routes records-import';
        $url        = route('dashboard.routes-records.import') ;
        $exportUrl  = route('dashboard.routes-records.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.routes-records.index') ;
        $cols       = ['end_point'=>'end point','attributes'=>'attributes','user_id'=>'user','ip_address'=>'ip_address'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportRoutesRecordsRequest $request){
        try {
            DB::beginTransaction();
            $this->routesRecordsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('routes records saved'));
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
        $filename = $request->headersOnly ? 'routes-records-template.xlsx' : 'routes-records.xlsx';
        return Excel::download(new RoutesRecordsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->routesRecordsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->routesRecordsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('routes records restored'));
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
