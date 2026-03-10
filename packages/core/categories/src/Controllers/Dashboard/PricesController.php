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
use Core\Categories\Requests\PricesRequest; 
use Core\Categories\Requests\ImportPricesRequest; 
use Core\Categories\Exports\PricesExport; 
use Core\Categories\Services\PricesService;
use Core\Info\Services\CitiesService;

class PricesController extends Controller
{
    use ApiResponse;
    public function __construct(protected PricesService $pricesService,protected CitiesService $citiesService){}

    public function index(){
        $title      = trans('prices index');
        $screen     = 'prices-index';
        $total      = $this->pricesService->totalCount();
        $trash      = $this->pricesService->trashCount();
		$cities = $this->citiesService->selectable('id','name');

        return view('categories::pages.prices.list', compact('title','screen','cities',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->pricesService->get($id) : null;
        $screen     = isset($item)  ? 'prices-edit'          : 'prices-create';
        $title      = isset($item)  ? trans("prices  edit")  : trans("prices  create");
		$cities = $this->citiesService->selectable('id','name');


        return view('categories::pages.prices.edit', compact('item','title','screen','cities') );
    }

    public function storeOrUpdate(PricesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->pricesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.prices.delete',$record->id);
            $record->updateUrl  = route('dashboard.prices.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('prices saved'),['entity'=>$record->itemData]);
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
        $title      = trans('prices index');
        $screen     = 'prices-index';
        $item       = $this->pricesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('categories::pages.prices.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->pricesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('prices deleted'));
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
            $data             = $this->pricesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('prices import');
        $screen     = 'prices-import';
        $url        = route('dashboard.prices.import') ;
        $exportUrl  = route('dashboard.prices.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.prices.index') ;
        $cols       = ['priceable_type'=>'priceable','priceable_id'=>'priceable id','city_id'=>'city','price'=>'price'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportPricesRequest $request){
        try {
            DB::beginTransaction();
            $this->pricesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('prices saved'));
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
        $filename = $request->headersOnly ? 'prices-template.xlsx' : 'prices.xlsx';
        return Excel::download(new PricesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->pricesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->pricesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Price restored'));
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
