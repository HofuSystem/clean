<?php

namespace Core\CMS\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\CMS\Requests\CmsPageDetailsRequest;
use Core\CMS\Requests\ImportCmsPageDetailsRequest;
use Core\CMS\Exports\CmsPageDetailsExport;
use Core\CMS\Services\CmsPageDetailsService;
use Core\CMS\Services\CmsPagesService;

class CmsPageDetailsController extends Controller
{
    use ApiResponse;
    public function __construct(protected CmsPageDetailsService $cmsPageDetailsService,protected CmsPagesService $cmsPagesService){}

    public function index($slug){
        $title      = trans($slug .' index');
        $screen     = $slug .'-index';
        $total      = $this->cmsPageDetailsService->totalCount();
        $trash      = $this->cmsPageDetailsService->trashCount();
		$cmsPages   = $this->cmsPagesService->selectable('id','name');
        $pageData   = $this->cmsPagesService->getBySlug($slug);

        return view('cms::pages.cms-page-details.list', compact('title','screen','slug','pageData','cmsPages',"total","trash"));
    }


    public function createOrEdit(Request $request,$slug,$id = null){
        $item       = isset($id)    ? $this->cmsPageDetailsService->get($id) : null;
        $screen     = isset($item)  ? 'CmPageDetail-edit'          : 'CmsPageDetail-create';
        $title      = isset($item)  ? trans($slug ." edit")  : trans($slug ." create");
		$cmsPages = $this->cmsPagesService->selectable('id','name');
        $pageData   = $this->cmsPagesService->getBySlug($slug);

        return view('cms::pages.cms-page-details.edit', compact('item','title','slug','screen','cmsPages','pageData') );
    }

    public function storeOrUpdate(CmsPageDetailsRequest $request,$slug, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->cmsPageDetailsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.cms-page-details.delete',['slug'=>$slug,'id'=>$record->id]);
            $record->updateUrl  = route('dashboard.cms-page-details.edit',['slug'=>$slug,'id'=>$record->id]);
            DB::commit();
            return $this->returnData(trans('CmsPageDetail saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }


    public function show($slug,$id){
        $title      = trans($slug .' index');
        $screen     = 'cms-page-details-index';
        $item       = $this->cmsPageDetailsService->get($id);
        $comments   = $item->comments()->where('parent_id',null)->get();
        $pageData   = $this->cmsPagesService->getBySlug($slug);

        return view('cms::pages.cms-page-details.show', compact('title','screen','slug','item','comments','pageData'));
    }


    public function delete(Request $request,$slug,$id){
        try {
            DB::beginTransaction();
            $record             = $this->cmsPageDetailsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans($slug .'deleted'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function dataTable(Request $request,$slug){
        try {
            $data             = $this->cmsPageDetailsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request,$slug){
        $title      = trans($slug .' import');
        $screen     = $slug .'-import';
        $url        = route('dashboard.cms-page-details.import') ;
        $exportUrl  = route('dashboard.cms-page-details.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.cms-page-details.index') ;
        $cols       = ['translations.en.name'=>'name en','translations.ar.name'=>'name ar','translations.en.description'=>'description en','translations.ar.description'=>'description ar','translations.en.intro'=>'intro en','translations.ar.intro'=>'intro ar','translations.en.point'=>'point en','translations.ar.point'=>'point ar','image'=>'image','tablet_image'=>'tablet image','mobile_image'=>'mobile image','icon'=>'icon','video'=>'video','link'=>'link','cms_pages_id'=>'cms pages'];
        return view('settings::views.import', compact('title','screen','url','slug','exportUrl','backUrl','cols'));
    }
    public function import(ImportCmsPageDetailsRequest $request,$slug){
        try {
            DB::beginTransaction();
            $this->cmsPageDetailsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans( $slug .' saved'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function export(Request $request,$slug)
    {
        $filename = $request->headersOnly ? $slug .'-template.xlsx' : $slug .'.xlsx';
        return Excel::download(new CmsPageDetailsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$slug,$id){
        try {
            DB::beginTransaction();
            $comment = $this->cmsPageDetailsService->comment($id,$request->content,$request->parent_id);
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
