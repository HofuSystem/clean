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
use Core\Blog\Requests\BlogCategoriesRequest; 
use Core\Blog\Requests\ImportBlogCategoriesRequest; 
use Core\Blog\Exports\BlogCategoriesExport; 
use Core\Blog\Services\BlogCategoriesService;

class BlogCategoriesController extends Controller
{
    use ApiResponse;
    public function __construct(protected BlogCategoriesService $blogCategoriesService){}

    public function index(){
        $title      = trans('Blog Categories index');
        $screen     = 'blog-categories-index';
        $total      = $this->blogCategoriesService->totalCount();
        $trash      = $this->blogCategoriesService->trashCount();
		$parents = $this->blogCategoriesService->selectable('id','title');

        return view('blog::pages.blog-categories.list', compact('title','screen','parents',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->blogCategoriesService->get($id) : null;
        $screen     = isset($item)  ? 'Blog Categories-edit'          : 'Blog Categories-create';
        $title      = isset($item)  ? trans("Blog Categories  edit")  : trans("Blog Categories  create");
		$parents = $this->blogCategoriesService->selectable('id','title');


        return view('blog::pages.blog-categories.edit', compact('item','title','screen','parents') );
    }

    public function storeOrUpdate(BlogCategoriesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->blogCategoriesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.blog-categories.delete',$record->id);
            $record->updateUrl  = route('dashboard.blog-categories.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Blog Categories saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Blog Categories index');
        $screen     = 'blog-categories-index';
        $item       = $this->blogCategoriesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('blog::pages.blog-categories.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->blogCategoriesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Blog Categories deleted'));
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
            $data             = $this->blogCategoriesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Blog Categories import');
        $screen     = 'Blog Categories-import';
        $url        = route('dashboard.blog-categories.import') ;
        $exportUrl  = route('dashboard.blog-categories.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.blog-categories.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','parent_id'=>'parent','status'=>'status'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportBlogCategoriesRequest $request){
        try {
            DB::beginTransaction();
            $this->blogCategoriesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Blog Categories saved'));
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
        $filename = $request->headersOnly ? 'blog-categories-template.xlsx' : 'blog-categories.xlsx';
        return Excel::download(new BlogCategoriesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->blogCategoriesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->blogCategoriesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Blog Categories restored'));
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
