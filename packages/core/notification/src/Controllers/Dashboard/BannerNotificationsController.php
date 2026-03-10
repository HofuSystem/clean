<?php

namespace Core\Notification\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Notification\Requests\BannerNotificationsRequest; 
use Core\Notification\Requests\ImportBannerNotificationsRequest; 
use Core\Notification\Exports\BannerNotificationsExport; 
use Core\Notification\Services\BannerNotificationsService;

class BannerNotificationsController extends Controller
{
    use ApiResponse;
    public function __construct(protected BannerNotificationsService $bannerNotificationsService){}

    public function index(){
        $title      = trans('banner_notifications index');
        $screen     = 'banner-notifications-index';
        $total      = $this->bannerNotificationsService->totalCount();
        $trash      = $this->bannerNotificationsService->trashCount();

        return view('notification::pages.banner-notifications.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->bannerNotificationsService->get($id) : null;
        $screen     = isset($item)  ? 'banner_notifications-edit'          : 'banner_notifications-create';
        $title      = isset($item)  ? trans("banner_notifications  edit")  : trans("banner_notifications  create");


        return view('notification::pages.banner-notifications.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(BannerNotificationsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->bannerNotificationsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.banner-notifications.delete',$record->id);
            $record->updateUrl  = route('dashboard.banner-notifications.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('banner_notifications saved'),['entity'=>$record->itemData]);
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
        $title      = trans('banner_notifications index');
        $screen     = 'banner-notifications-index';
        $item       = $this->bannerNotificationsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('notification::pages.banner-notifications.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->bannerNotificationsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('banner_notifications deleted'));
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
            $data             = $this->bannerNotificationsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('banner_notifications import');
        $screen     = 'banner_notifications-import';
        $url        = route('dashboard.banner-notifications.import') ;
        $exportUrl  = route('dashboard.banner-notifications.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.banner-notifications.index') ;
        $cols       = ['image'=>'image','publish_date'=>'publish date','expired_date'=>'expired date','next_vision_hour'=>'next vision hour','status'=>'status'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportBannerNotificationsRequest $request){
        try {
            DB::beginTransaction();
            $this->bannerNotificationsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('banner_notifications saved'));
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
        $filename = $request->headersOnly ? 'banner-notifications-template.xlsx' : 'banner-notifications.xlsx';
        return Excel::download(new BannerNotificationsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->bannerNotificationsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->bannerNotificationsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('BannerNotification restored'));
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
