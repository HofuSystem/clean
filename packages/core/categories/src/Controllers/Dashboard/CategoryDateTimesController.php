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
use Core\Categories\Requests\CategoryDateTimesRequest; 
use Core\Categories\Requests\ImportCategoryDateTimesRequest; 
use Core\Categories\Exports\CategoryDateTimesExport;
use Core\Categories\Requests\CategoryDateTimesCreateRequest;
use Core\Categories\Requests\CategoryDateTimesDuplicateRequest;
use Core\Categories\Services\CategoryDateTimesService;
use Core\Categories\Services\CategoriesService;
use Core\Info\Services\CitiesService;

class CategoryDateTimesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CategoryDateTimesService $categoryDateTimesService,protected CategoriesService $categoriesService,protected CitiesService $citiesService){}

    public function index(){
        $title      = trans('delivery date time index');
        $screen     = 'delivery-date-time-index';
        $total      = $this->categoryDateTimesService->totalCount();
        $trash      = $this->categoryDateTimesService->trashCount();
		$categories = $this->categoriesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');

        return view('categories::pages.category-date-times.list', compact('title','screen','categories','cities',"total","trash"));
    }


    public function create(Request $request,$id = null){
        $screen     =   'CategoryDateTime-create';
        $title      =   trans("delivery date time  create");
		$categories = $this->categoriesService->selectable('id','name');
		$cities     = $this->citiesService->selectable('id','name');


        return view('categories::pages.category-date-times.create', compact('title','screen','categories','cities') );
    }
    public function store(CategoryDateTimesCreateRequest $request){
        try {
            DB::beginTransaction();
            $this->categoryDateTimesService->createDateTimes($request->type,$request->category_id,$request->city_id,$request->date_from,$request->date_to,$request->weekends,$request->off_dates,$request->times);
            DB::commit();
            return $this->returnSuccessMessage(trans('Category date time saved'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function edit(Request $request, $type, $date)
    {
        $screen     = 'CategoryDateTime-edit';
        $title      = trans("delivery date time  edit");
        $dateTimes  = $this->categoryDateTimesService->getForEdit($type, $date,$request->category_id,$request->city_id);
        if($dateTimes->isEmpty()){
            abort(404);
        }
        $type       = $dateTimes->first()->type;
        $date       = $dateTimes->first()->date;
        $categoryId = $dateTimes->first()->category_id;
        $cityId     = $dateTimes->first()->city_id;

        $categories = $this->categoriesService->selectable('id','name');
        $cities     = $this->citiesService->selectable('id','name');
        return view('categories::pages.category-date-times.edit', compact('dateTimes','title','screen','categories','cities','type','date','categoryId','cityId'));
    }

  

    public function update(CategoryDateTimesRequest $request,  $type, $date)
    {
        try {
            DB::beginTransaction();
            $this->categoryDateTimesService->update($type, $date,$request->category_id,$request->city_id,$request->times,$request->new_type,$request->new_category_id,$request->new_city_id,$request->new_date);
            DB::commit();
            return $this->returnSuccessMessage(trans('delivery date time saved'));
        } catch(ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function delete(Request $request,$type,$date){
        try {
            DB::beginTransaction();
            $this->categoryDateTimesService->delete($type,$date,$request->category_id,$request->city_id);
            DB::commit();
            return $this->returnSuccessMessage(trans('delivery date time deleted'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function duplicate(CategoryDateTimesDuplicateRequest $request){
        try {
            DB::beginTransaction();
            $count = $this->categoryDateTimesService->duplicate(
                $request->type, 
                $request->date, 
                $request->category_id, 
                $request->city_id, 
                $request->from_date, 
                $request->to_date
            );
            DB::commit();
            return $this->returnSuccessMessage(trans('Date times duplicated successfully') . " ({$count} " . trans('dates') . ")");
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
            $data             = $this->categoryDateTimesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
  
    
}
