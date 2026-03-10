<?php

namespace Core\Orders\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Orders\Requests\ReportReasonsRequest; 
use Core\Orders\Requests\ImportReportReasonsRequest; 
use Core\Orders\Exports\ReportReasonsExport; 
use Core\Orders\Services\ReportReasonsService;

class ReportReasonsController extends Controller
{
    use ApiResponse;
    public function __construct(protected ReportReasonsService $reportReasonsService){}

    public function index(){
        $title      = trans('ReportReason index');
        $screen     = 'report-reasons-index';
        $total      = $this->reportReasonsService->totalCount();
        $trash      = $this->reportReasonsService->trashCount();

        return view('orders::pages.report-reasons.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->reportReasonsService->get($id) : null;
        $screen     = isset($item)  ? 'ReportReason-edit'          : 'ReportReason-create';
        $title      = isset($item)  ? trans("ReportReason  edit")  : trans("ReportReason  create");


        return view('orders::pages.report-reasons.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(ReportReasonsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->reportReasonsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.report-reasons.delete',$record->id);
            $record->updateUrl  = route('dashboard.report-reasons.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('ReportReason saved'),['entity'=>$record->itemData]);
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
        $title      = trans('ReportReason index');
        $screen     = 'report-reasons-index';
        $item       = $this->reportReasonsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.report-reasons.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->reportReasonsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('ReportReason deleted'));
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
            $data             = $this->reportReasonsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('ReportReason import');
        $screen     = 'ReportReason-import';
        $url        = route('dashboard.report-reasons.import') ;
        $exportUrl  = route('dashboard.report-reasons.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.report-reasons.index') ;
        $cols       = ['translations.en.name'=>'name en','translations.ar.name'=>'name ar','translations.en.desc'=>'desc en','translations.ar.desc'=>'desc ar','ordering'=>'ordering'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportReportReasonsRequest $request){
        try {
            DB::beginTransaction();
            $this->reportReasonsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('ReportReason saved'));
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
        $filename = $request->headersOnly ? 'report-reasons-template.xlsx' : 'report-reasons.xlsx';
        return Excel::download(new ReportReasonsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->reportReasonsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->reportReasonsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('ReportReason restored'));
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
