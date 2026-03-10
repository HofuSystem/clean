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
use Core\Pages\Requests\SectionsRequest;
use Core\Pages\Requests\ImportSectionsRequest;
use Core\Pages\Exports\SectionsExport;
use Core\Pages\Services\SectionsService;
use Core\Pages\Services\PagesService;

class SectionsController extends Controller
{
    use ApiResponse;
    public function __construct(protected SectionsService $sectionsService,protected PagesService $pagesService){}

    public function index(){
        $title      = trans('sections index');
        $screen     = 'sections-index';
        $total      = $this->sectionsService->totalCount();
        $trash      = $this->sectionsService->trashCount();
		$pages = $this->pagesService->selectable(['id']);

        return view('pages::pages.sections.list', compact('title','screen','pages',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->sectionsService->get($id) : null;
        $screen     = isset($item)  ? 'sections-edit'          : 'sections-create';
        $title      = isset($item)  ? trans("sections  edit")  : trans("sections  create");
		$pages = $this->pagesService->selectable(['id']);


        return view('pages::pages.sections.edit', compact('item','title','screen','pages') );
    }

    public function storeOrUpdate(SectionsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->sectionsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.sections.delete',$record->id);
            $record->updateUrl  = route('dashboard.sections.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('sections saved'),['entity'=>$record->itemData]);
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
        $title      = trans('sections show');
        $screen     = 'sections-show';
        $item       = $this->sectionsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.sections.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->sectionsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('sections deleted'));
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
            $data             = $this->sectionsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('sections import');
        $screen     = 'sections-import';
        $url        = route('dashboard.sections.import') ;
        $exportUrl  = route('dashboard.sections.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.sections.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','translations.en.small_title'=>'small title en','translations.ar.small_title'=>'small title ar','translations.en.description'=>'description en','translations.ar.description'=>'description ar','images'=>'images','video'=>'video','template'=>'template','page_id'=>'page'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportSectionsRequest $request){
        try {
            DB::beginTransaction();
            $this->sectionsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('sections saved'));
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
        $filename = $request->headersOnly ? 'sections-template.xlsx' : 'sections.xlsx';
        return Excel::download(new SectionsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->sectionsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->sectionsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Section restored'));
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
