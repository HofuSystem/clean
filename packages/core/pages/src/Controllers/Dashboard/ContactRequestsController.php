<?php

namespace Core\Pages\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Pages\Requests\ContactRequestsRequest;
use Core\Pages\Requests\ImportContactRequestsRequest;
use Core\Pages\Exports\ContactRequestsExport;
use Core\Pages\Services\ContactRequestsService;

class ContactRequestsController extends Controller
{
    use ApiResponse;
    public function __construct(protected ContactRequestsService $contactRequestsService){}

    public function index(){
        $title      = trans('contact requests index');
        $screen     = 'contact-requests-index';
        $total      = $this->contactRequestsService->totalCount();
        $trash      = $this->contactRequestsService->trashCount();

        return view('pages::pages.contact-requests.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->contactRequestsService->get($id) : null;
        $screen     = isset($item)  ? 'contact requests-edit'          : 'contact requests-create';
        $title      = isset($item)  ? trans("contact requests  edit")  : trans("contact requests  create");


        return view('pages::pages.contact-requests.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(ContactRequestsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->contactRequestsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.contact-requests.delete',$record->id);
            $record->updateUrl  = route('dashboard.contact-requests.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('contact requests saved'),['entity'=>$record->itemData]);
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
        $title      = trans('contact requests show');
        $screen     = 'contact-requests-show';
        $item       = $this->contactRequestsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.contact-requests.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->contactRequestsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('contact requests deleted'));
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
            $data             = $this->contactRequestsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('contact requests import');
        $screen     = 'contact requests-import';
        $url        = route('dashboard.contact-requests.import') ;
        $exportUrl  = route('dashboard.contact-requests.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.contact-requests.index') ;
        $cols       = ['name'=>'name','phone'=>'phone','email'=>'email','service_id'=>'service','date'=>'date','time'=>'time','notes'=>'notes'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportContactRequestsRequest $request){
        try {
            DB::beginTransaction();
            $this->contactRequestsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('contact requests saved'));
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
        $filename = $request->headersOnly ? 'contact-requests-template.xlsx' : 'contact-requests.xlsx';
        return Excel::download(new ContactRequestsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->contactRequestsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->contactRequestsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('ContactRequest restored'));
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
