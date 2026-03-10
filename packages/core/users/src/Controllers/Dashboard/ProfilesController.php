<?php

namespace Core\Users\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Users\Requests\ProfilesRequest; 
use Core\Users\Requests\ImportProfilesRequest; 
use Core\Users\Exports\ProfilesExport; 
use Core\Users\Services\ProfilesService;
use Core\Info\Services\CountriesService;
use Core\Info\Services\CitiesService;
use Core\Info\Services\DistrictsService;
use Core\Users\Services\UsersService;

class ProfilesController extends Controller
{
    use ApiResponse;
    public function __construct(protected ProfilesService $profilesService,protected CountriesService $countriesService,protected CitiesService $citiesService,protected DistrictsService $districtsService,protected UsersService $usersService){}

    public function index(){
        $title      = trans('Profile index');
        $screen     = 'profiles-index';
        $total      = $this->profilesService->totalCount();
        $trash      = $this->profilesService->trashCount();
		$countries = $this->countriesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');
		$districts = $this->districtsService->selectable('id','name');
		$users = $this->usersService->selectable('id','fullname');

        return view('users::pages.profiles.list', compact('title','screen','countries','cities','districts','users',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->profilesService->get($id) : null;
        $screen     = isset($item)  ? 'Profile-edit'          : 'Profile-create';
        $title      = isset($item)  ? trans("Profile  edit")  : trans("Profile  create");
		$countries = $this->countriesService->selectable('id','name');
		$cities = $this->citiesService->selectable('id','name');
		$districts = $this->districtsService->selectable('id','name');
		$users = $this->usersService->selectable('id','fullname');


        return view('users::pages.profiles.edit', compact('item','title','screen','countries','cities','districts','users') );
    }

    public function storeOrUpdate(ProfilesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->profilesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.profiles.delete',$record->id);
            $record->updateUrl  = route('dashboard.profiles.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Profile saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Profile index');
        $screen     = 'profiles-index';
        $item       = $this->profilesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.profiles.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->profilesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Profile deleted'));
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
            $data             = $this->profilesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Profile import');
        $screen     = 'Profile-import';
        $url        = route('dashboard.profiles.import') ;
        $exportUrl  = route('dashboard.profiles.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.profiles.index') ;
        $cols       = ['country_id'=>'country','city_id'=>'city','district_id'=>'district','other_city_name'=>'other city name','user_id'=>'user','bio'=>'bio','lat'=>'lat','lng'=>'lng'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportProfilesRequest $request){
        try {
            DB::beginTransaction();
            $this->profilesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Profile saved'));
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
        $filename = $request->headersOnly ? 'profiles-template.xlsx' : 'profiles.xlsx';
        return Excel::download(new ProfilesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->profilesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->profilesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Profile restored'));
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
