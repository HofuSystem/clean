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
use Core\Pages\Requests\ComparisonsRequest;
use Core\Pages\Requests\ImportComparisonsRequest;
use Core\Pages\Exports\ComparisonsExport;
use Core\Pages\Services\ComparisonsService;

class ComparisonsController extends Controller
{
    use ApiResponse;
    public function __construct(protected ComparisonsService $comparisonsService){}

    public function index(){
        $title      = trans('Comparisons index');
        $screen     = 'comparisons-index';
        $total      = $this->comparisonsService->totalCount();
        $trash      = $this->comparisonsService->trashCount();

        return view('pages::pages.comparisons.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->comparisonsService->get($id) : null;
        $screen     = isset($item)  ? 'comparisons-edit'          : 'comparisons-create';
        $title      = isset($item)  ? trans("Comparisons edit")  : trans("Comparisons create");


        return view('pages::pages.comparisons.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(ComparisonsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->comparisonsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.comparisons.delete',$record->id);
            $record->updateUrl  = route('dashboard.comparisons.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Comparison saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Comparisons show');
        $screen     = 'comparisons-show';
        $item       = $this->comparisonsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.comparisons.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->comparisonsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Comparison deleted'));
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
            $data             = $this->comparisonsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Comparisons import');
        $screen     = 'comparisons-import';
        $url        = route('dashboard.comparisons.import') ;
        $exportUrl  = route('dashboard.comparisons.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.comparisons.index') ;
        $cols       = ['translations.en.point'=>'point en','translations.ar.point'=>'point ar','translations.en.us_text'=>'us text en','translations.ar.us_text'=>'us text ar','translations.en.them_text'=>'them text en','translations.ar.them_text'=>'them text ar','order'=>'order'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportComparisonsRequest $request){
        try {
            DB::beginTransaction();
            $this->comparisonsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Comparisons saved'));
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
        $filename = $request->headersOnly ? 'comparisons-template.xlsx' : 'comparisons.xlsx';
        return Excel::download(new ComparisonsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->comparisonsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->comparisonsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Comparison restored'));
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

