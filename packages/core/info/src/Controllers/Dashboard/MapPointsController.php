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
use Core\Info\Requests\MapPointsRequest; 
use Core\Info\Requests\ImportMapPointsRequest; 
use Core\Info\Exports\MapPointsExport; 
use Core\Info\Services\MapPointsService;
use Core\Info\Services\DistrictsService;

class MapPointsController extends Controller
{
    use ApiResponse;
    public function __construct(protected MapPointsService $mapPointsService,protected DistrictsService $districtsService){}

    public function index(){
        $title      = trans('mappoints index');
        $screen     = 'map-points-index';
        $total      = $this->mapPointsService->totalCount();
        $trash      = $this->mapPointsService->trashCount();
		$districts = $this->districtsService->selectable('id','name');

        return view('info::pages.map-points.list', compact('title','screen','districts',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->mapPointsService->get($id) : null;
        $screen     = isset($item)  ? 'mappoints-edit'          : 'mappoints-create';
        $title      = isset($item)  ? trans("mappoints  edit")  : trans("mappoints  create");
		$districts = $this->districtsService->selectable('id','name');


        return view('info::pages.map-points.edit', compact('item','title','screen','districts') );
    }

    public function storeOrUpdate(MapPointsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->mapPointsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.map-points.delete',$record->id);
            $record->updateUrl  = route('dashboard.map-points.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('mappoints saved'),['entity'=>$record->itemData]);
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
        $title      = trans('mappoints index');
        $screen     = 'map-points-index';
        $item       = $this->mapPointsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('info::pages.map-points.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->mapPointsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('mappoints deleted'));
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
            $data             = $this->mapPointsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('mappoints import');
        $screen     = 'mappoints-import';
        $url        = route('dashboard.map-points.import') ;
        $exportUrl  = route('dashboard.map-points.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.map-points.index') ;
        $cols       = ['lat'=>'lat','lng'=>'lng','district_id'=>'district'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportMapPointsRequest $request){
        try {
            DB::beginTransaction();
            $this->mapPointsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('mappoints saved'));
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
        $filename = $request->headersOnly ? 'map-points-template.xlsx' : 'map-points.xlsx';
        return Excel::download(new MapPointsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->mapPointsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->mapPointsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('MapPoint restored'));
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
