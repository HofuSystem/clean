<?php

namespace Core\Workers\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Workers\Requests\WorkersRequest; 
use Core\Workers\Requests\ImportWorkersRequest; 
use Core\Workers\Exports\WorkersExport; 
use Core\Workers\Services\WorkersService;
use Core\Info\Services\NationalitiesService;
use Core\Info\Services\CitiesService;
use Core\Categories\Services\CategoriesService;
use Core\Users\Services\UsersService;

class WorkersController extends Controller
{
    use ApiResponse;
    public function __construct(protected WorkersService $workersService,protected NationalitiesService $nationalitiesService,protected CitiesService $citiesService,protected CategoriesService $categoriesService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('Worker index');
        $screen     = 'workers-index';
        $total      = $this->workersService->totalCount();
        $trash      = $this->workersService->trashCount();
		$nationalities = $this->nationalitiesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');
		$categories = $this->categoriesService->selectable('id','name');
		$leaders = $this->usersService->selectable('id','fullname',[],'technical');
		$workers = $this->workersService->selectable('id','name');

        return view('workers::pages.workers.list', compact('title','screen','nationalities','cities','categories','leaders','workers',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->workersService->get($id) : null;
        $screen     = isset($item)  ? 'Worker-edit'          : 'Worker-create';
        $title      = isset($item)  ? trans("Worker  edit")  : trans("Worker  create");
		$nationalities = $this->nationalitiesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');
		$categories = $this->categoriesService->selectable('id','name');
		$leaders = $this->usersService->selectable('id','fullname',[],'technical');
		$workers = $this->workersService->selectable('id','name');


        return view('workers::pages.workers.edit', compact('item','title','screen','nationalities','cities','categories','leaders','workers') );
    }

    public function storeOrUpdate(WorkersRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->workersService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.workers.delete',$record->id);
            $record->updateUrl  = route('dashboard.workers.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Worker saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Worker index');
        $screen     = 'workers-index';
        $item       = $this->workersService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('workers::pages.workers.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->workersService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Worker deleted'));
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
            $data             = $this->workersService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Worker import');
        $screen     = 'Worker-import';
        $url        = route('dashboard.workers.import') ;
        $exportUrl  = route('dashboard.workers.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.workers.index') ;
        $cols       = ['image'=>'image','name'=>'name','phone'=>'phone','email'=>'email','years_experience'=>'years experience','address'=>'address','birth_date'=>'birth date','hour_price'=>'hour price','gender'=>'gender','status'=>'status','identity'=>'identity','nationality_id'=>'nationality','city_id'=>'city','categories'=>'categories','leader_id'=>'leader'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportWorkersRequest $request){
        try {
            DB::beginTransaction();
            $this->workersService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Worker saved'));
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
        $filename = $request->headersOnly ? 'workers-template.xlsx' : 'workers.xlsx';
        return Excel::download(new WorkersExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->workersService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->workersService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Worker restored'));
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
