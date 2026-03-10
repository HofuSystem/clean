<?php

namespace Core\Blog\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Blog\Requests\BlogsRequest; 
use Core\Blog\Requests\ImportBlogsRequest; 
use Core\Blog\Exports\BlogsExport; 
use Core\Blog\Services\BlogsService;
use Core\Blog\Services\BlogCategoriesService;

class BlogsController extends Controller
{
    use ApiResponse;
    public function __construct(protected BlogsService $blogsService,protected BlogCategoriesService $blogCategoriesService){}

    public function index(){
        $title      = trans('blog index');
        $screen     = 'blogs-index';
        $total      = $this->blogsService->totalCount();
        $trash      = $this->blogsService->trashCount();
		$categories = $this->blogCategoriesService->selectable('id','title');

        return view('blog::pages.blogs.list', compact('title','screen','categories',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->blogsService->get($id) : null;
        $screen     = isset($item)  ? 'blog-edit'          : 'blog-create';
        $title      = isset($item)  ? trans("blog  edit")  : trans("blog  create");
		$categories = $this->blogCategoriesService->selectable('id','title');


        return view('blog::pages.blogs.edit', compact('item','title','screen','categories') );
    }

    public function storeOrUpdate(BlogsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->blogsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.blogs.delete',$record->id);
            $record->updateUrl  = route('dashboard.blogs.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('blog saved'),['entity'=>$record->itemData]);
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
        $title      = trans('blog index');
        $screen     = 'blogs-index';
        $item       = $this->blogsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('blog::pages.blogs.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->blogsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('blog deleted'));
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
            $data             = $this->blogsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('blog import');
        $screen     = 'blog-import';
        $url        = route('dashboard.blogs.import') ;
        $exportUrl  = route('dashboard.blogs.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.blogs.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','image'=>'image','gallery'=>'gallary','translations.en.content'=>'content en','translations.ar.content'=>'content ar','category_id'=>'category','status'=>'status','published_at'=>'published_at','translations.en.meta'=>'meta en','translations.ar.meta'=>'meta ar'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportBlogsRequest $request){
        try {
            DB::beginTransaction();
            $this->blogsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('blog saved'));
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
        $filename = $request->headersOnly ? 'blogs-template.xlsx' : 'blogs.xlsx';
        return Excel::download(new BlogsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->blogsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->blogsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Blog restored'));
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
