<?php

namespace Core\Info\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Info\Requests\CountriesRequest; 
use Core\Info\Requests\ImportCountriesRequest; 
use Core\Info\Exports\CountriesExport; 
use Core\Info\Services\CountriesService;

class CountriesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CountriesService $countriesService){}

    public function index(){
        $title      = trans('Country index');
        $screen     = 'countries-index';
        $total      = $this->countriesService->totalCount();
        $trash      = $this->countriesService->trashCount();
		$countries = $this->countriesService->selectable('id','name');

        return view('info::pages.countries.list', compact('title','screen','countries',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->countriesService->get($id) : null;
        $screen     = isset($item)  ? 'Country-edit'          : 'Country-create';
        $title      = isset($item)  ? trans("Country  edit")  : trans("Country  create");
		$countries = $this->countriesService->selectable('id','name');


        return view('info::pages.countries.edit', compact('item','title','screen','countries') );
    }

    public function storeOrUpdate(CountriesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->countriesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.countries.delete',$record->id);
            $record->updateUrl  = route('dashboard.countries.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Country saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Country index');
        $screen     = 'countries-index';
        $item       = $this->countriesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('info::pages.countries.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->countriesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Country deleted'));
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
            $data             = $this->countriesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Country import');
        $screen     = 'Country-import';
        $url        = route('dashboard.countries.import') ;
        $exportUrl  = route('dashboard.countries.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.countries.index') ;
        $cols       = ['slug'=>'slug','translations.en.name'=>'name en','translations.ar.name'=>'name ar','phonecode'=>'phonecode','short_name'=>'short name','flag'=>'flag'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCountriesRequest $request){
        try {
            DB::beginTransaction();
            $this->countriesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Country saved'));
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
        $filename = $request->headersOnly ? 'countries-template.xlsx' : 'countries.xlsx';
        return Excel::download(new CountriesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->countriesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->countriesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Country restored'));
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
