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
use Core\Pages\Requests\ReasonsRequest;
use Core\Pages\Requests\ImportReasonsRequest;
use Core\Pages\Exports\ReasonsExport;
use Core\Pages\Services\ReasonsService;

class ReasonsController extends Controller
{
    use ApiResponse;
    public function __construct(protected ReasonsService $reasonsService){}

    public function index(){
        $title      = trans('reasons index');
        $screen     = 'reasons-index';
        $total      = $this->reasonsService->totalCount();
        $trash      = $this->reasonsService->trashCount();

        return view('pages::pages.reasons.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->reasonsService->get($id) : null;
        $screen     = isset($item)  ? 'reasons-edit'          : 'reasons-create';
        $title      = isset($item)  ? trans("reasons  edit")  : trans("reasons  create");


        return view('pages::pages.reasons.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(ReasonsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->reasonsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.reasons.delete',$record->id);
            $record->updateUrl  = route('dashboard.reasons.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('reasons saved'),['entity'=>$record->itemData]);
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
        $title      = trans('reasons show');
        $screen     = 'reasons-show';
        $item       = $this->reasonsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.reasons.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->reasonsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('reasons deleted'));
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
            $data             = $this->reasonsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('reasons import');
        $screen     = 'reasons-import';
        $url        = route('dashboard.reasons.import') ;
        $exportUrl  = route('dashboard.reasons.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.reasons.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','count'=>'count','is_active'=>'is active'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportReasonsRequest $request){
        try {
            DB::beginTransaction();
            $this->reasonsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('reasons saved'));
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
        $filename = $request->headersOnly ? 'reasons-template.xlsx' : 'reasons.xlsx';
        return Excel::download(new ReasonsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->reasonsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->reasonsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Reason restored'));
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
