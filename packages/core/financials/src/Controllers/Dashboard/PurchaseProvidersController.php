<?php

namespace Core\Financials\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Financials\Services\PurchaseProvidersService;
use Core\Info\Services\CitiesService;
use Core\Info\Services\DistrictsService;

use Core\Financials\Requests\PurchaseProvidersRequest;
use Core\Financials\Requests\ImportPurchaseProvidersRequest;
use Core\Financials\Exports\PurchaseProvidersExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseProvidersController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PurchaseProvidersService $purchaseProvidersService,
        protected CitiesService $citiesService,
        protected DistrictsService $districtsService
    ){}

    public function index(){
        $title      = trans('Purchase providers index');
        $screen     = 'purchase-providers-index';
        $counts     = $this->purchaseProvidersService->getCounts();
        $total      = $counts['total'];
        $trash      = $counts['trash'];
        
        $cities = $this->citiesService->selectable('id','name');
        $districts = $this->districtsService->selectable('id','name');

        return view('financials::pages.purchase-providers.list', compact('title','screen','cities','districts',"total","trash"));
    }

    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->purchaseProvidersService->get($id) : null;
        $screen     = isset($item)  ? 'purchase-providers-edit'          : 'purchase-providers-create';
        $title      = isset($item)  ? trans("Purchase provider edit")  : trans("Purchase provider create");
        
        $cities = $this->citiesService->selectable('id','name');
        $districts = $this->districtsService->selectable('id','name');

        return view('financials::pages.purchase-providers.edit', compact('item','title','screen','cities','districts') );
    }

    public function storeOrUpdate(PurchaseProvidersRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->purchaseProvidersService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.purchase-providers.delete',$record->id);
            $record->updateUrl  = route('dashboard.purchase-providers.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Purchase provider saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Purchase provider index');
        $screen     = 'purchase-providers-index';
        $item       = $this->purchaseProvidersService->get($id);
        return view('financials::pages.purchase-providers.show', compact('title','screen','item'));
    }

    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->purchaseProvidersService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase provider deleted'));
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
            $data             = $this->purchaseProvidersService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function restore(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->purchaseProvidersService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase provider restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function importView(Request $request){
        $title      = trans('Purchase provider import');
        $screen     = 'purchase-provider-import';
        $url        = route('dashboard.purchase-providers.import') ;
        $exportUrl  = route('dashboard.purchase-providers.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.purchase-providers.index') ;
        $cols       = [
            'name' => 'name',
            'commercial_registration' => 'commercial registration',
            'tax_number' => 'tax number',
            'street_name' => 'street name',
            'building_no' => 'building no',
            'city_id' => 'city',
            'district_id' => 'district',
            'postal_code' => 'postal code'
        ];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }

    public function import(ImportPurchaseProvidersRequest $request){
        try {
            DB::beginTransaction();
            $this->purchaseProvidersService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Purchase provider saved'));
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
        $filename = $request->headersOnly ? 'purchase-providers-template.xlsx' : 'purchase-providers.xlsx';
        return Excel::download(new PurchaseProvidersExport($request->headersOnly,$request->cols), $filename);
    }
}
