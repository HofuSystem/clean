<?php

namespace Core\CMS\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\CMS\DataResources\CmsPageDetailsResource;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\CMS\Requests\ImportCmsPagesRequest;
use Core\CMS\Exports\CmsPagesExport;
use Core\CMS\Models\CmsPage;
use Core\CMS\Models\CmsPageDetail;
use Core\CMS\Requests\CmsPageDetailsRequest;
use Core\CMS\Services\CmsPagesService;
use Illuminate\Support\Arr;

class CmsPagesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CmsPagesService $cmsPagesService){}


    public function index(string $slug = null )
    {
        $screen     = $slug.'-index';
        $title      = trans($slug .' '. 'index');
        $pageData = getCmsDetails($slug);
        if($pageData)
        {
           if($pageData->is_parent)
           {
              $cmsDetails = CmsPageDetail::where('cms_pages_id',$pageData->id)->get();
              return view('cms::pages.cms-pages.list', ['cmsDetails' => CmsPageDetailsResource::collection($cmsDetails),'pageData'=>$pageData,'screen'=>$screen,'title'=>$title,'slug'=>$slug]);
           }else{
            $cmsDetails =  CmsPageDetail::where('cms_pages_id',$pageData->id)->first() ;
            if($cmsDetails)
            {
                return redirect()->route('dashboard.cms-pages.show',[$slug,$cmsDetails->id]);
            }else{
                $data = [] ;
                $data['cms_pages_id'] =$pageData->id ;
                $cmsDetails =  CmsPageDetail::create($data) ;
                return redirect()->route('dashboard.cms-pages.show',[$slug,$cmsDetails->id]);

            }
           }
        }
    }
    public function create(string $slug = null )
    {
        $pageData = getCmsDetails($slug);
        if($pageData)
        {
              return view('cms::pages.cms-pages.create', ['pageData'=>$pageData]);
        }
    }

    public function store(string $slug = null,CmsPageDetailsRequest $request   )
    {
        $pageData = getCmsDetails($slug);
        $data = $request->validated();
        $data['cms_pages_id'] =$pageData->id ;
        //CmsPageDetail::create($data);
        CmsPageDetail::create(Arr::except($data,['image','tablet_image','mobile_image','icon']));
        if($pageData->is_parent)
        {
            return redirect()->route('dashboard.cms.page',$slug)->with(['successful_message' => __('message.success_message.edit_successfuly')]);
        }
    }

    public function show(string $slug = null ,  $id = null)
    {
        $screen     = $slug.'-show';
        $title      = trans($slug .' '. 'show');
        $item = getCmsDetails($slug);
        $comments   = $item->comments()->where('parent_id',null)->get();

        if($item)
        {
            $cmsDetails =  CmsPageDetail::where('id',$id)->first() ;
              return view('cms::pages.cms-pages.show', ['item'=>$item ,'cmsDetails'=>  CmsPageDetailsResource::make($cmsDetails),'screen'=>$screen,'title'=>$title,'comments'=>$comments]);
        }
    }

    public function update(string $slug = null ,  $id = null,CmsPageDetailsRequest $request   )
    {
        $pageData = getCmsDetails($slug);
        //dd($request->validated(),$pageData,$id);
        //CmsPageDetail::where('id',$id)->first()->update($data);
        CmsPageDetail::find($id)->update(Arr::except($request->validated(),['image','tablet_image','mobile_image','icon']));

        if($pageData->is_parent)
        {
            return redirect()->route('dashboard.cms.page',$slug)->with(['successful_message' => __('message.success_message.edit_successfuly')]);
        }else{
            return redirect()->route('dashboard.cms.page.show',[$slug,$id])->with(['successful_message' => __('message.success_message.edit_successfuly')]);
        }

    }
    public function destroy(string $slug = null ,   $id = null)
    {
        CmsPageDetail::where('id',$id)->delete();

        return redirect()->route('dashboard.cms.page',$slug)->with(['successful_message' => __('message.success_message.delete_successfuly'),]);
    }
    /* public function index(string $slug = null){
        $title      = trans('CmsPage index');
        $screen     = 'cms-pages-index';
        $total      = $this->cmsPagesService->totalCount();
        $trash      = $this->cmsPagesService->trashCount();
		$cmsPages = $this->cmsPagesService->selectable('id','name');

        return view('cms::pages.cms-pages.list', compact('title','screen','cmsPages',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->cmsPagesService->get($id) : null;
        $screen     = isset($item)  ? 'CmsPage-edit'          : 'CmsPage-create';
        $title      = isset($item)  ? trans("CmsPage  edit")  : trans("CmsPage  create");
		$cmsPages = $this->cmsPagesService->selectable('id','name');


        return view('cms::pages.cms-pages.edit', compact('item','title','screen','cmsPages') );
    }

    public function storeOrUpdate(CmsPagesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->cmsPagesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.cms-pages.delete',[$record->id,$record->slug]);
            $record->updateUrl  = route('dashboard.cms-pages.edit',[$record->id,$record->slug]);
            DB::commit();
            return $this->returnData(trans('CmsPage saved'),['entity'=>$record->itemData]);
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
        $title      = trans('CmsPage index');
        $screen     = 'cms-pages-index';
        $item       = $this->cmsPagesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('cms::pages.cms-pages.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->cmsPagesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('CmsPage deleted'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    } */

    public function dataTable(Request $request){
        try {
            $data             = $this->cmsPagesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('CmsPage import');
        $screen     = 'CmsPage-import';
        $url        = route('dashboard.cms-pages.import') ;
        $exportUrl  = route('dashboard.cms-pages.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.cms-pages.index') ;
        $cols       = ['slug'=>'slug','translations.en.name'=>'name en','translations.ar.name'=>'name ar','is_parent'=>'is parent','is_multi_upload'=>'is multi upload','have_point'=>'have point','have_name'=>'have name','have_description'=>'have description','have_intro'=>'have intro','have_image'=>'have image','have_tablet_image'=>'have tablet image','have_mobile_image'=>'have mobile image','have_icon'=>'have icon','have_video'=>'have video','have_link'=>'have link'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCmsPagesRequest $request){
        try {
            DB::beginTransaction();
            $this->cmsPagesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('CmsPage saved'));
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
        $filename = $request->headersOnly ? 'cms-pages-template.xlsx' : 'cms-pages.xlsx';
        return Excel::download(new CmsPagesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->cmsPagesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->cmsPagesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('CmsPage restored'));
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
