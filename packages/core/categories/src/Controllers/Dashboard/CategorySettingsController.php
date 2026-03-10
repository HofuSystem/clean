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
use Core\Categories\Requests\CategorySettingsRequest; 
use Core\Categories\Requests\ImportCategorySettingsRequest; 
use Core\Categories\Exports\CategorySettingsExport; 
use Core\Categories\Services\CategorySettingsService;
use Core\Categories\Services\CategoriesService;
use Core\Info\Services\CitiesService;

class CategorySettingsController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategorySettingsService $categorySettingsService,protected CategoriesService $categoriesService,protected CitiesService $citiesService){}

    public function index(Request $request){
        $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);
        $title      = trans($type.' index');
        $screen     = $type.'-index';
        $total      = $this->categorySettingsService->totalCount($type);
        $trash      = $this->categorySettingsService->trashCount($type);
		$categories = $this->categoriesService->selectable('id','name');
		$cities     = $this->citiesService->selectable('id','name');
		$parents    = $this->categorySettingsService->selectable('id','name',true);

        return view('categories::pages.category-settings.list', compact('title','screen','type','categories','cities','parents',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);
        $item       = isset($id)    ? $this->categorySettingsService->get($id) : null;
        $screen     = isset($item)  ? 'CategorySetting-edit'          : 'CategorySetting-create';
        $title      = isset($item)  ? trans("CategorySetting  edit")  : trans("CategorySetting  create");
		$categories = $this->categoriesService->selectable('id','name');
		$cities     = $this->citiesService->selectable('id','name');
		$parents    = $this->categorySettingsService->selectable('id','name');


        return view('categories::pages.category-settings.edit', compact('item','title','screen','type','categories','cities','parents') );
    }

    public function storeOrUpdate(CategorySettingsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);
            $record             = $this->categorySettingsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.'.$type.'.delete',$record->id);
            $record->updateUrl  = route('dashboard.'.$type.'.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('CategorySetting saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }


    public function show(Request $request,$id){
        $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);
        $title      = trans($request.' index');
        $screen     = $request.'-index';
        $item       = $this->categorySettingsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('categories::pages.category-settings.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);

            $record             = $this->categorySettingsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans($type.' deleted'));
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
            $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);
            $data       = $this->categorySettingsService->dataTable($request->draw,$type);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);

        $title      = trans($type.' import');
        $screen     = $type.'-import';
        $url        = route('dashboard.'.$type.'.import') ;
        $exportUrl  = route('dashboard.'.$type.'.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.'.$type.'.index') ;
        $cols       = ['slug'=>'slug','translations.en.name'=>'name en','translations.ar.name'=>'name ar','category_id'=>'category','addon_price'=>'addon price','parent_id'=>'parent','status'=>'status'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCategorySettingsRequest $request){
        try {
            $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);

            DB::beginTransaction();
            $this->categorySettingsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans($type.' saved'));
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
        $type       = in_array($request->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  $request->segment(2) :  $request->segment(3);
        $filename = $request->headersOnly ? $type.'-template.xlsx' : $type.'.xlsx';
        return Excel::download(new CategorySettingsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->categorySettingsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->categorySettingsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('CategorySetting restored'));
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
