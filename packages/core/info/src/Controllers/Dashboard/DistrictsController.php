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
use Core\Info\Requests\DistrictsRequest; 
use Core\Info\Requests\ImportDistrictsRequest; 
use Core\Info\Exports\DistrictsExport;
use Core\Info\Models\District;
use Core\Info\Services\DistrictsService;
use Core\Info\Services\CitiesService;

class DistrictsController extends Controller
{
    use ApiResponse;
    public function __construct(protected DistrictsService $districtsService,protected CitiesService $citiesService){}

    public function index(){
        $title      = trans('District index');
        $screen     = 'districts-index';
        $total      = $this->districtsService->totalCount();
        $trash      = $this->districtsService->trashCount();
		$cities = $this->citiesService->selectable('id','name');
		$districts = $this->districtsService->selectable('id','name');

        return view('info::pages.districts.list', compact('title','screen','cities','districts',"total","trash"));
    }
    public function mapPoints(){
        $title      = trans('District Map Points');
        $screen     = 'mapPoints-index';
        $districts  = District::with('mapPoints')->has('mapPoints')->get()->map(function($district){
            return [
                'name' => $district->name,
                'points' => $district->mapPoints->map(function($point){
                    return [$point->lat, $point->lng];
                })
            ];
        })->toJson();
        return view('info::pages.districts.map-points', compact('title','screen','districts'));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->districtsService->get($id) : null;
        $screen     = isset($item)  ? 'District-edit'          : 'District-create';
        $title      = isset($item)  ? trans("District  edit")  : trans("District  create");
		$cities     = $this->citiesService->selectable('id','name');
		$districts  = $this->districtsService->selectable('id','name');
        $points     = [];
        if(isset($item)){
            $points = $item->mapPoints->map(function($point){
                return [
                  $point->lat,
                  $point->lng
                ];
            });
        }
        return view('info::pages.districts.edit', compact('item','title','screen','cities','districts','points') );
    }

    public function storeOrUpdate(DistrictsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->districtsService->storeOrUpdate($request->all(),$id,$request->coordinates);
            $record->deleteUrl  = route('dashboard.districts.delete',$record->id);
            $record->updateUrl  = route('dashboard.districts.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('District saved'),['entity'=>$record->itemData]);
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
        $title      = trans('District index');
        $screen     = 'districts-index';
        $item       = $this->districtsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('info::pages.districts.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->districtsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('District deleted'));
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
            $data             = $this->districtsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('District import');
        $screen     = 'District-import';
        $url        = route('dashboard.districts.import') ;
        $exportUrl  = route('dashboard.districts.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.districts.index') ;
        $cols       = ['slug'=>'slug','translations.en.name'=>'name en','translations.ar.name'=>'name ar','lat'=>'lat','lng'=>'lng','postal_code'=>'postal code','city_id'=>'city'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportDistrictsRequest $request){
        try {
            DB::beginTransaction();
            $this->districtsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('District saved'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function export(Request $request)
    {
        $filename = $request->headersOnly ? 'districts-template.xlsx' : 'districts.xlsx';
        return Excel::download(new DistrictsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->districtsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->districtsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('District restored'));
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
