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
use Core\Pages\Requests\PagesRequest;
use Core\Pages\Requests\ImportPagesRequest;
use Core\Pages\Exports\PagesExport;
use Core\Pages\Services\PagesService;
use Core\Settings\Helpers\ToolHelper;

class PagesController extends Controller
{
    use ApiResponse;
    public function __construct(protected PagesService $pagesService){}

    public function index(){
        $title      = trans('pages index');
        $screen     = 'pages-index';
        $total      = $this->pagesService->totalCount();
        $trash      = $this->pagesService->trashCount();
		$pages = $this->pagesService->selectable(['id']);
        return view('pages::pages.pages.list', compact('title','screen','pages',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->pagesService->get($id) : null;
        $screen     = isset($item)  ? 'pages-edit'          : 'pages-create';
        $title      = isset($item)  ? trans("pages  edit")  : trans("pages  create");
		$pages = $this->pagesService->selectable(['id']);
        $sectionsData = ToolHelper::sectionsData();


        return view('pages::pages.pages.edit', compact('item','title','screen','pages','sectionsData') );
    }

    public function storeOrUpdate(PagesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->pagesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.pages.delete',$record->id);
            $record->updateUrl  = route('dashboard.pages.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('pages saved'),['entity'=>$record->itemData]);
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
        $title      = trans('pages show');
        $screen     = 'pages-show';
        $item       = $this->pagesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.pages.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->pagesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('pages deleted'));
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
            $data             = $this->pagesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('pages import');
        $screen     = 'pages-import';
        $url        = route('dashboard.pages.import') ;
        $exportUrl  = route('dashboard.pages.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.pages.index') ;
        $cols       = ['slug'=>'slug','translations.en.title'=>'title en','translations.ar.title'=>'title ar','translations.en.description'=>'description en','translations.ar.description'=>'description ar','image'=>'image','is_active'=>'is active'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportPagesRequest $request){
        try {
            DB::beginTransaction();
            $this->pagesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('pages saved'));
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
        $filename = $request->headersOnly ? 'pages-template.xlsx' : 'pages.xlsx';
        return Excel::download(new PagesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->pagesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->pagesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Page restored'));
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
