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
use Core\Info\Requests\CitiesRequest; 
use Core\Info\Requests\ImportCitiesRequest; 
use Core\Info\Exports\CitiesExport; 
use Core\Info\Services\CitiesService;
use Core\Info\Services\CountriesService;

class CitiesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CitiesService $citiesService,protected CountriesService $countriesService){}

    public function index(){
        $title      = trans('City index');
        $screen     = 'cities-index';
        $total      = $this->citiesService->totalCount();
        $trash      = $this->citiesService->trashCount();
		$countries = $this->countriesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');

        return view('info::pages.cities.list', compact('title','screen','countries','cities',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->citiesService->get($id) : null;
        $screen     = isset($item)  ? 'City-edit'          : 'City-create';
        $title      = isset($item)  ? trans("City  edit")  : trans("City  create");
		$countries = $this->countriesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');


        return view('info::pages.cities.edit', compact('item','title','screen','countries','cities') );
    }

    public function storeOrUpdate(CitiesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->citiesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.cities.delete',$record->id);
            $record->updateUrl  = route('dashboard.cities.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('City saved'),['entity'=>$record->itemData]);
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
        $title      = trans('City index');
        $screen     = 'cities-index';
        $item       = $this->citiesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('info::pages.cities.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->citiesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('City deleted'));
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
            $data             = $this->citiesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('City import');
        $screen     = 'City-import';
        $url        = route('dashboard.cities.import') ;
        $exportUrl  = route('dashboard.cities.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.cities.index') ;
        $cols       = ['slug'=>'slug','translations.en.name'=>'name en','translations.ar.name'=>'name ar','lat'=>'lat','lng'=>'lng','postal_code'=>'postal code','image'=>'image','delivery_price'=>'delivery price','status'=>'status','country_id'=>'country'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCitiesRequest $request){
        try {
            DB::beginTransaction();
            $this->citiesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('City saved'));
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
        $filename = $request->headersOnly ? 'cities-template.xlsx' : 'cities.xlsx';
        return Excel::download(new CitiesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->citiesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->citiesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('City restored'));
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
