<?php

namespace Core\Pages\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Pages\Requests\WorkStepsRequest;
use Core\Pages\Requests\ImportWorkStepsRequest;
use Core\Pages\Exports\WorkStepsExport;
use Core\Pages\Services\WorkStepsService;

class WorkStepsController extends Controller
{
    use ApiResponse;
    public function __construct(protected WorkStepsService $workStepsService){}

    public function index(){
        $title      = trans('Work Steps index');
        $screen     = 'work-steps-index';
        $total      = $this->workStepsService->totalCount();
        $trash      = $this->workStepsService->trashCount();

        return view('pages::pages.work-steps.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->workStepsService->get($id) : null;
        $screen     = isset($item)  ? 'work-steps-edit'          : 'work-steps-create';
        $title      = isset($item)  ? trans("Work Steps edit")  : trans("Work Steps create");


        return view('pages::pages.work-steps.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(WorkStepsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->workStepsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.work-steps.delete',$record->id);
            $record->updateUrl  = route('dashboard.work-steps.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Work Step saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Work Steps show');
        $screen     = 'work-steps-show';
        $item       = $this->workStepsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.work-steps.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->workStepsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Work Step deleted'));
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
            $data             = $this->workStepsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Work Steps import');
        $screen     = 'work-steps-import';
        $url        = route('dashboard.work-steps.import') ;
        $exportUrl  = route('dashboard.work-steps.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.work-steps.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','translations.en.description'=>'description en','translations.ar.description'=>'description ar','icon'=>'icon','order'=>'order'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportWorkStepsRequest $request){
        try {
            DB::beginTransaction();
            $this->workStepsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Work Steps saved'));
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
        $filename = $request->headersOnly ? 'work-steps-template.xlsx' : 'work-steps.xlsx';
        return Excel::download(new WorkStepsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->workStepsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->workStepsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Work Step restored'));
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

