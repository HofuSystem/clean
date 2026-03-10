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
use Core\Categories\Requests\CategoryOffersRequest; 
use Core\Categories\Requests\ImportCategoryOffersRequest; 
use Core\Categories\Exports\CategoryOffersExport; 
use Core\Categories\Services\CategoryOffersService;

class CategoryOffersController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategoryOffersService $categoryOffersService){}

    public function index(){
        $title      = trans('Offer index');
        $screen     = 'category-offers-index';
        $total      = $this->categoryOffersService->totalCount();
        $trash      = $this->categoryOffersService->trashCount();

        return view('categories::pages.category-offers.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->categoryOffersService->get($id) : null;
        $screen     = isset($item)  ? 'CategoryOffer-edit'          : 'CategoryOffer-create';
        $title      = isset($item)  ? trans("Offer edit")  : trans("Offer create");


        return view('categories::pages.category-offers.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(CategoryOffersRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->categoryOffersService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.category-offers.delete',$record->id);
            $record->updateUrl  = route('dashboard.category-offers.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Offer saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Offer index');
        $screen     = 'category-offers-index';
        $item       = $this->categoryOffersService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('categories::pages.category-offers.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->categoryOffersService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Offer deleted'));
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
            $data             = $this->categoryOffersService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Offerimport');
        $screen     = 'CategoryOffer-import';
        $url        = route('dashboard.category-offers.import') ;
        $exportUrl  = route('dashboard.category-offers.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.category-offers.index') ;
        $cols       = ['translations.en.name'=>'name en','translations.ar.name'=>'name ar','translations.en.desc'=>'desc en','translations.ar.desc'=>'desc ar','price'=>'price','sale_price'=>'sale price','image'=>'image','hours_num'=>'hours num','workers_num'=>'workers num','status'=>'status','type'=>'type'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportCategoryOffersRequest $request){
        try {
            DB::beginTransaction();
            $this->categoryOffersService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Offer saved'));
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
        $filename = $request->headersOnly ? 'category-offers-template.xlsx' : 'category-offers.xlsx';
        return Excel::download(new CategoryOffersExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->categoryOffersService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->categoryOffersService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('CategoryOffer restored'));
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
