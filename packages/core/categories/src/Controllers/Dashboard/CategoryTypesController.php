<?php

namespace Core\Categories\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Categories\Requests\CategoryTypesRequest; 
use Core\Categories\Requests\ImportCategoryTypesRequest; 
use Core\Categories\Exports\CategoryTypesExport; 
use Core\Categories\Services\CategoryTypesService;
use Core\Categories\Services\CategoriesService;

class CategoryTypesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategoryTypesService $categoryTypesService,protected CategoriesService $categoriesService){}

    public function index(){
        $title      = trans('Services Type index');
        $screen     = 'category-types-index';
        $total      = $this->categoryTypesService->totalCount();
        $trash      = $this->categoryTypesService->trashCount();
		$categories = $this->categoriesService->selectable('id','name');

        return view('categories::pages.category-types.list', compact('title','screen','categories',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->categoryTypesService->get($id) : null;
        $screen     = isset($item)  ? 'CategoryType-edit'          : 'CategoryType-create';
        $title      = isset($item)  ? trans("Services Type edit")  : trans("Services Type create");
		$categories = $this->categoriesService->selectable('id','name');


        return view('categories::pages.category-types.edit', compact('item','title','screen','categories') );
    }

    public function storeOrUpdate(CategoryTypesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->categoryTypesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.category-types.delete',$record->id);
            $record->updateUrl  = route('dashboard.category-types.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Services Type saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Services Type index');
        $screen     = 'category-types-index';
        $item       = $this->categoryTypesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('categories::pages.category-types.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->categoryTypesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Services Type deleted'));
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
            $data             = $this->categoryTypesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Services Type import');
        $screen     = 'CategoryType-import';
        $url        = route('dashboard.category-types.import') ;
        $exportUrl  = route('dashboard.category-types.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.category-types.index') ;
        $cols       = ['slug'=>'slug','translations.en.name'=>'name en','translations.ar.name'=>'name ar','translations.en.intro'=>'intro en','translations.ar.intro'=>'intro ar','translations.en.desc'=>'desc en','translations.ar.desc'=>'desc ar','category_id'=>'category','hour_price'=>'hour price','status'=>'status'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCategoryTypesRequest $request){
        try {
            DB::beginTransaction();
            $this->categoryTypesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Services Type saved'));
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
        $filename = $request->headersOnly ? 'category-types-template.xlsx' : 'category-types.xlsx';
        return Excel::download(new CategoryTypesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->categoryTypesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->categoryTypesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('CategoryType restored'));
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
