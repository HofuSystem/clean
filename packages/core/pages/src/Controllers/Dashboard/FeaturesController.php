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
use Core\Pages\Requests\FeaturesRequest;
use Core\Pages\Requests\ImportFeaturesRequest;
use Core\Pages\Exports\FeaturesExport;
use Core\Pages\Services\FeaturesService;

class FeaturesController extends Controller
{
    use ApiResponse;
    public function __construct(protected FeaturesService $featuresService){}

    public function index(){
        $title      = trans('features index');
        $screen     = 'features-index';
        $total      = $this->featuresService->totalCount();
        $trash      = $this->featuresService->trashCount();

        return view('pages::pages.features.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->featuresService->get($id) : null;
        $screen     = isset($item)  ? 'features-edit'          : 'features-create';
        $title      = isset($item)  ? trans("features  edit")  : trans("features  create");


        return view('pages::pages.features.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(FeaturesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->featuresService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.features.delete',$record->id);
            $record->updateUrl  = route('dashboard.features.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('features saved'),['entity'=>$record->itemData]);
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
        $title      = trans('features show');
        $screen     = 'features-show';
        $item       = $this->featuresService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.features.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->featuresService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('features deleted'));
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
            $data             = $this->featuresService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('features import');
        $screen     = 'features-import';
        $url        = route('dashboard.features.import') ;
        $exportUrl  = route('dashboard.features.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.features.index') ;
        $cols       = ['translations.en.title'=>'title en','translations.ar.title'=>'title ar','translations.en.description'=>'description en','translations.ar.description'=>'description ar','icon'=>'icon','is_active'=>'is active'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportFeaturesRequest $request){
        try {
            DB::beginTransaction();
            $this->featuresService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('features saved'));
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
        $filename = $request->headersOnly ? 'features-template.xlsx' : 'features.xlsx';
        return Excel::download(new FeaturesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->featuresService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->featuresService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Feature restored'));
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
