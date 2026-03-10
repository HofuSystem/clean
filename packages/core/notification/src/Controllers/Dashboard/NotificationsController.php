<?php

namespace Core\Notification\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\UsersResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Info\Services\CitiesService;
use Core\Notification\Requests\NotificationsRequest; 
use Core\Notification\Requests\ImportNotificationsRequest; 
use Core\Notification\Exports\NotificationsExport;
use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Services\NotificationsService;
use Core\Users\Models\User;
use Core\Users\Services\UsersService;

class NotificationsController extends Controller
{
    use ApiResponse;
    public function __construct(
    protected NotificationsService $notificationsService,
    protected NotificationsManger $notificationsManger,
    protected UsersService $usersService,
    protected CitiesService $citiesService
    ){}

    public function index(){
        $title      = trans('Notification index');
        $screen     = 'notifications-index';
        $total      = $this->notificationsService->totalCount();
        $trash      = $this->notificationsService->trashCount();
		$senders    = $this->usersService->selectable('id','fullname');
        $cities     = $this->citiesService->selectable('id','name');
        return view('notification::pages.notifications.list', compact('title','screen','senders','cities',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->notificationsService->get($id) : null;
        $screen     = isset($item)  ? 'Notification-edit'          : 'Notification-create';
        $title      = isset($item)  ? trans("Notification  edit")  : trans("Notification  create");
		$senders    = $this->usersService->selectable('id','fullname',['phone']);


        return view('notification::pages.notifications.edit', compact('item','title','screen','senders') );
    }

    public function storeOrUpdate(NotificationsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->notificationsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.notifications.delete',$record->id);
            $record->updateUrl  = route('dashboard.notifications.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Notification saved'),['entity'=>$record->itemData]);
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
        $title      = trans('Notification index');
        $screen     = 'notifications-index';
        $item       = $this->notificationsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('notification::pages.notifications.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->notificationsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Notification deleted'));
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
            $data             = $this->notificationsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Notification import');
        $screen     = 'Notification-import';
        $url        = route('dashboard.notifications.import') ;
        $exportUrl  = route('dashboard.notifications.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.notifications.index') ;
        $cols       = ['types'=>'types','for'=>'for','for_data'=>'for data','title'=>'title','body'=>'body','media'=>'media','sender_id'=>'sender'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportNotificationsRequest $request){
        try {
            DB::beginTransaction();
            $this->notificationsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Notification saved'));
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
        $filename = $request->headersOnly ? 'notifications-template.xlsx' : 'notifications.xlsx';
        return Excel::download(new NotificationsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->notificationsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->notificationsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Notification restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function getUsers(Request $request){
        $recordsTotal       = User::count();
        $users              = $this->notificationsManger->getNotificationUserQuery($request->for,$request->for_data,$request->register_from,$request->register_to,$request->orders_from,$request->orders_to,$request->orders_min,$request->orders_max)
        ->when($request->filter_fullname, function ($query) use ($request) {
            $query->where('fullname', 'like', '%' . $request->filter_fullname . '%');
        })
        ->when($request->filter_phone, function ($query) use ($request) {
            $query->where('phone', 'like', '%' . $request->filter_phone . '%');
        })
        ->withCount('orders');
        $recordsFiltered    = $users->count();
        $users              = $users->dataTable()->get();
        
        $users              = UsersResource::collection($users);
        return $this->returnData(trans('users fetched'),   [
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => $users
        ]);
    }
    public function getSentToUsers(Request $request,$id){
        $item           = $this->notificationsService->get($id);
        $users          = $item->users()->withPivot('status as sent_status','response as sent_response');
        $users          = $users->when($request->filter_fullname, function ($query) use ($request) {
            $query->where('fullname', 'like', '%' . $request->filter_fullname . '%');
        })
        ->when($request->filter_phone, function ($query) use ($request) {
            $query->where('phone', 'like', '%' . $request->filter_phone . '%');
        });
        $recordsTotal   = $users->count();
        $recordsFiltered = $users->count();
        $users          = $users->dataTable()->get();
        $users          = UsersResource::collection($users);
        return $this->returnData(trans('users fetched'),   [
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => $users
        ]);
    }
}
