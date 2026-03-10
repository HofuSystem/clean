<?php

namespace Core\Orders\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Orders\Requests\DeliveryPricesRequest; 
use Core\Orders\Requests\ImportDeliveryPricesRequest; 
use Core\Orders\Exports\DeliveryPricesExport; 
use Core\Orders\Services\DeliveryPricesService;
use Core\Categories\Services\CategoriesService;
use Core\Info\Services\CitiesService;
use Core\Info\Services\DistrictsService;
use Core\Settings\Services\SettingsService;

class DeliveryPricesController extends Controller
{
    use ApiResponse;
    public function __construct(protected DeliveryPricesService $deliveryPricesService,protected CategoriesService $categoriesService,protected CitiesService $citiesService,protected DistrictsService $districtsService){}

    public function index(){
        $title              = trans('delivery prices index');
        $screen             = 'delivery-prices-index';
        $total              = $this->deliveryPricesService->totalCount();
        $trash              = $this->deliveryPricesService->trashCount();
		$categories         = $this->categoriesService->selectable('id','name');
		$cities             = $this->citiesService->selectable('id','name');
		$districts          = $this->districtsService->selectable('id','name');
        $deliveryCharge     = SettingsService::getDataBaseSetting('delivery_charge');
        $freeDeliveryMin    = SettingsService::getDataBaseSetting('free_delivery_min');

        return view('orders::pages.delivery-prices.list', compact('title','screen','deliveryCharge','freeDeliveryMin','categories','cities','districts',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->deliveryPricesService->get($id) : null;
        $screen     = isset($item)  ? 'delivery prices-edit'          : 'delivery prices-create';
        $title      = isset($item)  ? trans("delivery prices  edit")  : trans("delivery prices  create");
		$categories = $this->categoriesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');
		$districts = $this->districtsService->selectable('id','name');


        return view('orders::pages.delivery-prices.edit', compact('item','title','screen','categories','cities','districts') );
    }

    public function storeOrUpdate(DeliveryPricesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->deliveryPricesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.delivery-prices.delete',$record->id);
            $record->updateUrl  = route('dashboard.delivery-prices.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('delivery prices saved'),['entity'=>$record->itemData]);
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
        $title      = trans('delivery prices index');
        $screen     = 'delivery-prices-index';
        $item       = $this->deliveryPricesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('orders::pages.delivery-prices.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->deliveryPricesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('delivery prices deleted'));
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
            $data             = $this->deliveryPricesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('delivery prices import');
        $screen     = 'delivery prices-import';
        $url        = route('dashboard.delivery-prices.import') ;
        $exportUrl  = route('dashboard.delivery-prices.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.delivery-prices.index') ;
        $cols       = ['category_id'=>'category','city'=>'city','district_id'=>'district','price'=>'price'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportDeliveryPricesRequest $request){
        try {
            DB::beginTransaction();
            $this->deliveryPricesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('delivery prices saved'));
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
        $filename = $request->headersOnly ? 'delivery-prices-template.xlsx' : 'delivery-prices.xlsx';
        return Excel::download(new DeliveryPricesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->deliveryPricesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->deliveryPricesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('DeliveryPrice restored'));
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
