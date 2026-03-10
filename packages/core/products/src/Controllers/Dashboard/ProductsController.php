<?php

namespace Core\Products\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Products\Requests\ProductsRequest;
use Core\Products\Requests\ImportProductsRequest;
use Core\Products\Exports\ProductsExport;
use Core\Products\Services\ProductsService;
use Core\Categories\Services\CategoriesService;
use Core\Info\Services\CitiesService;
use Core\Products\Models\Product;
use Core\Products\Requests\CreateProductsRequest;

class ProductsController extends Controller
{
    use ApiResponse;
    public function __construct(protected ProductsService $productsService,protected CategoriesService $categoriesService,protected CitiesService $citiesService){}

    public function index(){
        $title      = trans('Product index');
        $screen     = 'products-index';
        $total      = $this->productsService->totalCount();
        $trash      = $this->productsService->trashCount();
		$cities = $this->citiesService->selectable('id','name');
		$categories     = $this->categoriesService->selectable('id','name',[['parent_id',null]],true);
		$subCategories  = $this->categoriesService->selectable('id','name',[['parent_id','!=',null]],true);

        return view('products::pages.products.list', compact('title','screen','categories','subCategories','cities',"total","trash"));
    }
    public function create(Request $request){
        $item           =  null;
        $screen         = 'Product-create';
        $title          = trans("Product  create");
		$categories     = $this->categoriesService->selectable('id','name',[['parent_id',null]]);
		$subCategories  = $this->categoriesService->selectable('id','name',[['parent_id','!=',null]]);
		$cities = $this->citiesService->selectable('id','name');

        return view('products::pages.products.create', compact('item','title','screen','cities','categories','subCategories') );
    }
    public function store(CreateProductsRequest $request){

        try {
            DB::beginTransaction();
            $data = $request->except('version');
            if($request->type == 'clothes'){
                foreach ($request->version ?? [] as $versionData) {
                    $versionData = array_merge($data,$versionData);
                    $record      = $this->productsService->storeOrUpdate($versionData);
                }
            }else{
                $record      = $this->productsService->storeOrUpdate($data);
            }
            $record->deleteUrl  = route('dashboard.products.delete',$record->id);
            $record->updateUrl  = route('dashboard.products.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Products were  saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }

    }

    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->productsService->get($id) : null;
        $screen     = isset($item)  ? 'Product-edit'          : 'Product-create';
        $title      = isset($item)  ? trans("Product  edit")  : trans("Product  create");
		$categories = $this->categoriesService->selectable('id','name');
		$subCategories = $this->categoriesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');


        return view('products::pages.products.edit', compact('item','title','screen','categories','subCategories','cities') );
    }

    public function storeOrUpdate(ProductsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->productsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.products.delete',$record->id);
            $record->updateUrl  = route('dashboard.products.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Product saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Product index');
        $screen     = 'products-index';
        $item       = $this->productsService->get($id);
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('products::pages.products.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->productsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Product deleted'));
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
            $data             = $this->productsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            dd($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Product import');
        $screen     = 'Product-import';
        $url        = route('dashboard.products.import') ;
        $exportUrl  = route('dashboard.products.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.products.index') ;
        $cols       = ['translations.en.name'=>'name en','translations.ar.name'=>'name ar','translations.en.desc'=>'desc en','translations.ar.desc'=>'desc ar','image'=>'image','type'=>'type','sku'=>'sku','is_package'=>'is package','category_id'=>'category','sub_category_id'=>'sub category','price'=>'price','quantity'=>'quantity','status'=>'status'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportProductsRequest $request){
        try {
            DB::beginTransaction();
            $this->productsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Product saved'));
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
        $filename = $request->headersOnly ? 'products-template.xlsx' : 'products.xlsx';
        return Excel::download(new ProductsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->productsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->productsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Product restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function bestSales(Request $request){
        $screen     = 'best-sale-products-index';
        $title      = trans('best sales products');

        return view('products::pages.products.best_sales', compact('title','screen') );
    }

    public function salesExport(Request $request)
    {
        $filename = $request->headersOnly ? 'best-sale-products-template.xlsx' : 'best-sale-products.xlsx';
        return Excel::download(new ProductsExport($request->headersOnly,$request->cols), $filename);
    }

}
