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
use Core\Pages\Requests\CountersRequest;
use Core\Pages\Requests\ImportCountersRequest;
use Core\Pages\Exports\CountersExport;
use Core\Pages\Services\CountersService;

class CountersController extends Controller
{
    use ApiResponse;
    public function __construct(protected CountersService $countersService){}

    public function index(){
        $title      = trans('counters index');
        $screen     = 'counters-index';
        $total      = $this->countersService->totalCount();
        $trash      = $this->countersService->trashCount();

        return view('pages::pages.counters.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->countersService->get($id) : null;
        $screen     = isset($item)  ? 'counters-edit'          : 'counters-create';
        $title      = isset($item)  ? trans("counters  edit")  : trans("counters  create");


        return view('pages::pages.counters.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(CountersRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->countersService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.counters.delete',$record->id);
            $record->updateUrl  = route('dashboard.counters.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('counters saved'),['entity'=>$record->itemData]);
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
        $title      = trans('counters show');
        $screen     = 'counters-show';
        $item       = $this->countersService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.counters.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->countersService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('counters deleted'));
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
            $data             = $this->countersService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('counters import');
        $screen     = 'counters-import';
        $url        = route('dashboard.counters.import') ;
        $exportUrl  = route('dashboard.counters.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.counters.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','count'=>'count','is_active'=>'is active'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCountersRequest $request){
        try {
            DB::beginTransaction();
            $this->countersService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('counters saved'));
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
        $filename = $request->headersOnly ? 'counters-template.xlsx' : 'counters.xlsx';
        return Excel::download(new CountersExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->countersService->comment($id,$request->content,$request->parent_id);
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

}
