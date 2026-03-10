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
use Core\Pages\Requests\BusinessesRequest;
use Core\Pages\Requests\ImportBusinessesRequest;
use Core\Pages\Exports\BusinessesExport;
use Core\Pages\Services\BusinessesService;

class BusinessesController extends Controller
{
    use ApiResponse;
    public function __construct(protected BusinessesService $BusinessesService){}

    public function index(){
        $title      = trans('Businesses index');
        $screen     = 'Businesses-index';
        $total      = $this->BusinessesService->totalCount();
        $trash      = $this->BusinessesService->trashCount();
        return view('pages::pages.businesses.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->BusinessesService->get($id) : null;
        $screen     = isset($item)  ? 'Businesses-edit'          : 'Businesses-create';
        $title      = isset($item)  ? trans("Businesses  edit")  : trans("Businesses  create");


        return view('pages::pages.businesses.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(BusinessesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->BusinessesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.businesses.delete',$record->id);
            $record->updateUrl  = route('dashboard.businesses.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Businesses saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Businesses show');
        $screen     = 'Businesses-show';
        $item       = $this->BusinessesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.businesses.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->BusinessesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Businesses deleted'));
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
            $data             = $this->BusinessesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Businesses import');
        $screen     = 'Businesses-import';
        $url        = route('dashboard.businesses.import') ;
        $exportUrl  = route('dashboard.businesses.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.businesses.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','count'=>'count','is_active'=>'is active'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportBusinessesRequest $request){
        try {
            DB::beginTransaction();
            $this->BusinessesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Businesses saved'));
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
        $filename = $request->headersOnly ? 'Businesses-template.xlsx' : 'Businesses.xlsx';
        return Excel::download(new BusinessesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->BusinessesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->BusinessesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Businesses restored'));
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
