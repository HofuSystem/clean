<?php

namespace Core\Workers\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Workers\Requests\WorkerDaysRequest; 
use Core\Workers\Requests\ImportWorkerDaysRequest; 
use Core\Workers\Exports\WorkerDaysExport; 
use Core\Workers\Services\WorkerDaysService;
use Core\Workers\Services\WorkersService;

class WorkerDaysController extends Controller
{
    use ApiResponse;
    public function __construct(protected WorkerDaysService $workerDaysService,protected WorkersService $workersService){}

    public function index(){
        $title      = trans('WorkerDay index');
        $screen     = 'worker-days-index';
        $total      = $this->workerDaysService->totalCount();
        $trash      = $this->workerDaysService->trashCount();
		$workers = $this->workersService->selectable('id','name');

        return view('workers::pages.worker-days.list', compact('title','screen','workers',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->workerDaysService->get($id) : null;
        $screen     = isset($item)  ? 'WorkerDay-edit'          : 'WorkerDay-create';
        $title      = isset($item)  ? trans("WorkerDay  edit")  : trans("WorkerDay  create");
		$workers = $this->workersService->selectable('id','name');


        return view('workers::pages.worker-days.edit', compact('item','title','screen','workers') );
    }

    public function storeOrUpdate(WorkerDaysRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->workerDaysService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.worker-days.delete',$record->id);
            $record->updateUrl  = route('dashboard.worker-days.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('WorkerDay saved'),['entity'=>$record->itemData]);
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
        $title      = trans('WorkerDay index');
        $screen     = 'worker-days-index';
        $item       = $this->workerDaysService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('workers::pages.worker-days.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->workerDaysService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('WorkerDay deleted'));
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
            $data             = $this->workerDaysService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('WorkerDay import');
        $screen     = 'WorkerDay-import';
        $url        = route('dashboard.worker-days.import') ;
        $exportUrl  = route('dashboard.worker-days.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.worker-days.index') ;
        $cols       = ['worker_id'=>'worker','date'=>'date','type'=>'type'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportWorkerDaysRequest $request){
        try {
            DB::beginTransaction();
            $this->workerDaysService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('WorkerDay saved'));
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
        $filename = $request->headersOnly ? 'worker-days-template.xlsx' : 'worker-days.xlsx';
        return Excel::download(new WorkerDaysExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->workerDaysService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->workerDaysService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('WorkerDay restored'));
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
