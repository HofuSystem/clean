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
use Core\Categories\Requests\CategoriesRequest;
use Core\Categories\Requests\ImportCategoriesRequest;
use Core\Categories\Exports\CategoriesExport;
use Core\Categories\Models\Category;
use Core\Categories\Requests\CategoryDateTimesCreateRequest;
use Core\Categories\Services\CategoriesService;
use Core\Info\Services\CitiesService;
use Core\Categories\Services\CategorySettingsService;

class CategoriesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategoriesService $categoriesService,protected CitiesService $citiesService,protected CategorySettingsService $categorySettingsService){}

    public function index(Request $request){
        $type           = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);
        if($type == 'categories'){
            $types   = ['clothes','sales','services'];
        }else if($type == 'sub-categories'){
            $types   = ['clothes','sales','services'];
        }else if($type == 'services'){
            $types   = ['maid','host'];
        }else if($type == 'sub-services'){
            $types   = ['maid','host'];
        }
        $title          = trans($type.' index');
        $screen         = $type.'-index';
        $total          = $this->categoriesService->totalCount($type);
        $trash          = $this->categoriesService->trashCount($type);
		$parents        = $this->categoriesService->selectable('id','name');
		$cities         = $this->citiesService->selectable('id','name');
		$categories     = $this->categoriesService->selectable('id','name');

        return view('categories::pages.categories.list', compact('title','screen','type','types','parents','cities','categories',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $type           = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);
        if($type == 'categories'){
            $types   = ['clothes','sales','services'];
        }else if($type == 'sub-categories'){
            $types   = ['clothes','sales','services'];
        }else if($type == 'services'){
            $types   = ['maid','host'];
        }else if($type == 'sub-services'){
            $types   = ['maid','host'];
        }
        $item       = isset($id)    ? $this->categoriesService->get($id) : null;
        $screen     = isset($item)  ? $type.'-edit'          : $type.'-create';
        $title      = isset($item)  ? trans($type." edit")  : trans($type." create");
		$parents = $this->categoriesService->selectable('id','name',['parent_id' => null],false,$types);
		$cities = $this->citiesService->selectable('id','name');

		$categories = $this->categoriesService->selectable('id','name');


        return view('categories::pages.categories.edit', compact('item','title','type','types','screen','parents','cities','categories') );
    }

    public function duplicate(Request $request,$id){
        $type       = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);
        if($type == 'categories'){
            $types   = ['clothes','sales','services'];
        }else if($type == 'sub-categories'){
            $types   = ['clothes','sales','services'];
        }else if($type == 'services'){
            $types   = ['maid','host'];
        }else if($type == 'sub-services'){
            $types   = ['maid','host'];
        }
        $item       = $this->categoriesService->get($id);
        $title      = trans($type.' duplicate');
        $screen     = $type.'-duplicate';
        $parents = $this->categoriesService->selectable('id','name',['parent_id' => null],false,$types);
		$cities = $this->citiesService->selectable('id','name');
		$categories = $this->categoriesService->selectable('id','name');

        return view('categories::pages.categories.duplicate', compact('item','title','type','types','screen','parents','cities','categories'));
    }

    public function storeOrUpdate(CategoriesRequest $request, $id = null){
        try {
            $type           = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);

            DB::beginTransaction();
            $record             = $this->categoriesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.'.$type.'.delete',$record->id);
            $record->updateUrl  = route('dashboard.'.$type.'.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Category saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function duplicateAction(CategoriesRequest $request, $id){
        try {
            $type           = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);

            DB::beginTransaction();
            $record             = $this->categoriesService->duplicateAction($request->all(),$id);
            $record->deleteUrl  = route('dashboard.'.$type.'.delete',$record->id);
            $record->updateUrl  = route('dashboard.'.$type.'.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Category saved'),['entity'=>$record->itemData]);
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
        $type        = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);
        $title      = trans($type.' index');
        $screen     = $type.'-index';
        $item       = $this->categoriesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('categories::pages.categories.show', compact('title','screen','type','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->categoriesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Category deleted'));
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
            $type           = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);
            $data             = $this->categoriesService->dataTable($request->draw,$type);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $type           = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);

        $title      = trans($type.' import');
        $screen     = $type.'-import';
        $url        = route('dashboard.'.$type.'.import') ;
        $exportUrl  = route('dashboard.'.$type.'.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.'.$type.'.index') ;
        $cols       = ['slug'=>'slug','translations.en.name'=>'name en','translations.ar.name'=>'name ar','image'=>'image','translations.en.intro'=>'intro en','translations.ar.intro'=>'intro ar','translations.en.desc'=>'desc en','translations.ar.desc'=>'desc ar','type'=>'type','delivery_price'=>'delivery price','sort'=>'sort','is_package'=>'is package','status'=>'status','parent_id'=>'parent','city_id'=>'city'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCategoriesRequest $request){
        try {
            DB::beginTransaction();
            $this->categoriesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Category saved'));
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
        $type           = in_array($request->segment(2) , ['categories','sub-categories','services','sub-services']) ?  $request->segment(2) :  $request->segment(3);
        $filename = $request->headersOnly ? $type.'-template.xlsx' : $type.'.xlsx';
        return Excel::download(new CategoriesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->categoriesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->categoriesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Category restored'));
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
