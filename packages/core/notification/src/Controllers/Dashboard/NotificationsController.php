<?php

namespace Core\Notification\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\UsersResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Info\Services\CitiesService;
use Core\Settings\Helpers\ToolHelper;
use Core\Notification\Requests\NotificationsRequest; 
use Core\Notification\Requests\ImportNotificationsRequest; 
use Core\Notification\Exports\NotificationsExport;
use Core\Notification\Helpers\NotificationsManger;
use Core\Notification\Helpers\NotificationDataNormalizer;
use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Models\UsersNotification;
use Core\Notification\Services\NotificationsService;
use Core\Notification\Services\RecipientEligibilityService;
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

        $senderIds  = DB::table('notifications')->whereNotNull('sender_id')->distinct()->pluck('sender_id');
        $senders    = !empty($senderIds) ? DB::table('users')->select('id', 'fullname')->whereIn('id', $senderIds)->get() : collect();
        $cities     = DB::table('cities')
            ->join('city_translations', function ($join) {
                $join->on('cities.id', '=', 'city_translations.city_id')
                    ->where('city_translations.locale', '=', app()->getLocale());
            })
            ->select('cities.id', 'city_translations.name')
            ->get();

        return view('notification::pages.notifications.list', compact('title','screen','senders','cities',"total","trash"));
    }

    public function createOrEdit(Request $request, $id = null){
        $item       = isset($id)    ? $this->notificationsService->get($id) : null;
        if ($item) {
            $this->syncCompletedCampaignStatuses($item);
        }
        $screen     = isset($item)  ? 'Notification-edit'          : 'Notification-create';
        $title      = isset($item)  ? trans("Notification  edit")  : trans("Notification  create");
        $selectedIds = isset($item) ? (ToolHelper::isJson($item->for_data) ? json_decode($item->for_data, true) : []) : [];
        $senders    = isset($item) ? \Core\Users\Models\User::whereIn('id', (array)$selectedIds)->get(['id', 'fullname', 'phone']) : collect();

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
        $item = $this->notificationsService->get($id);
        $title = trans('Campaign Details') . ' #' . $item->id;
        $screen = 'notifications-show';
        $comments = $item->comments()->where('parent_id', null)->get();

        $deliveryChannel = $item->getDeliveryChannel();
        $isFcmChannel = $item->isFcm();
        $resolvedChannel = $item->getChannel();
        
        $isDirect = ($deliveryChannel === 'direct_fcm');
        $deliveryEventsEnabled = config('notification.delivery_events_enabled', true);

        // Targeted and eligible counts
        $totalTargeted = $item->targeted_users_count ?: ($item->for === 'all' ? User::count() : $item->users()->count());
        $eligibleUsers = $item->eligible_users_count ?: 0;
        $eligibleDevices = $item->eligible_devices_count ?: 0;

        // Channel-specific delivery metrics
        $acceptedByFcm = $isDirect ? ($item->accepted_by_fcm_count ?? 0) : '—';
        
        $hasSavedLegacyCount = false;
        $legacyTopicCount = null;
        if (!$isDirect) {
            $payload = is_array($item->payload) ? $item->payload : json_decode($item->payload ?? '{}', true);
            $rawCount = $payload['sent_count'] ?? $item->sent_count;
            if ($rawCount !== null && is_numeric($rawCount) && (int) $rawCount > 0) {
                $hasSavedLegacyCount = true;
                $legacyTopicCount = (int) $rawCount;
            }
        }

        $permFailed = $item->permanent_failed_count ?? 0;
        $transFailed = $item->transient_failed_count ?? 0;

        // Engagement metrics: display "غير متاح" when not measurable
        if (!$isDirect || !$deliveryEventsEnabled) {
            $receivedDisplay = 'غير متاح';
            $openedDisplay = 'غير متاح';
            $openRateDisplay = 'غير متاح';
            $deliveryRateDisplay = 'غير متاح';
        } else {
            $received = (int)($item->received_count ?? 0);
            $opened = (int)($item->opened_count ?? 0);
            $receivedDisplay = number_format($received);
            $openedDisplay = number_format($opened);
            $deliveryRateDisplay = ($isDirect && is_numeric($acceptedByFcm) && $acceptedByFcm > 0)
                ? round(($received / $acceptedByFcm) * 100, 1) . '%'
                : '0%';
            $openRateDisplay = ($received > 0)
                ? round(($opened / $received) * 100, 1) . '%'
                : '0%';
        }

        // Exact counts for tabs
        $usersCount = UsersNotification::where('notifications_type', Notification::class)
            ->where('notifications_id', $item->id)
            ->count();
        $devicesCount = NotificationToken::where('notification_id', $item->id)->count();
        $skippedTokensCount = NotificationToken::where('notification_id', $item->id)
            ->where('status', 'skipped_by_feature_flag')
            ->count();

        return view('notification::pages.notifications.show', compact(
            'title', 'screen', 'item', 'comments', 'isDirect', 'deliveryChannel',
            'isFcmChannel', 'resolvedChannel',
            'totalTargeted', 'eligibleUsers', 'eligibleDevices', 'acceptedByFcm',
            'hasSavedLegacyCount', 'legacyTopicCount', 'permFailed', 'transFailed', 'receivedDisplay',
            'openedDisplay', 'deliveryRateDisplay', 'openRateDisplay',
            'usersCount', 'devicesCount', 'skippedTokensCount'
        ));
    }

    public function queueMonitor(Request $request){
        $title = trans('Notification Queue Monitor');
        $screen = 'notifications-queue-monitor';

        $notifications = Notification::query()
            ->select([
                'id', 'title', 'purpose', 'processing_status',
                'started_at', 'completed_at', 'created_at',
                'targeted_users_count', 'eligible_devices_count',
                'accepted_by_fcm_count', 'permanent_failed_count', 'transient_failed_count',
                'payload'
            ])
            ->withCount([
                'notificationTokens as total_tokens_count',
                'notificationTokens as queued_count' => function ($q) {
                    $q->where('status', 'queued');
                },
                'notificationTokens as processing_count' => function ($q) {
                    $q->where('status', 'processing');
                },
                'notificationTokens as skipped_count' => function ($q) {
                    $q->where('status', 'skipped_by_feature_flag');
                },
            ])
            ->orderByDesc('id')
            ->paginate(15);

        $oldestPending = NotificationToken::whereIn('status', ['queued', 'processing'])
            ->min('queued_at');
        $oldestPending = $oldestPending ? \Carbon\Carbon::parse($oldestPending) : null;

        $lastErrorToken = NotificationToken::whereIn('status', ['transient_failed', 'permanent_failed'])
            ->whereNotNull('error_code')
            ->orderByDesc('id')
            ->first(['notification_id', 'error_code', 'error_message', 'created_at']);

        return view('notification::pages.notifications.queue-monitor', compact(
            'title', 'screen', 'notifications', 'oldestPending', 'lastErrorToken'
        ));
    }

    public function previewAudience(Request $request){
        try {
            $result = app(RecipientEligibilityService::class)->previewAudience($request->all());
            return $this->returnData(trans('Audience preview generated'), ['data' => $result]);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 500);
        }
    }

    public function getEligibilityUsers(Request $request, $id){
        $notification = Notification::findOrFail($id);

        $query = UsersNotification::query()
            ->with('user:id,fullname,phone')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $notification->id);

        $recordsTotal = (clone $query)->count();

        if ($request->filled('filter_status')) {
            $query->where('eligibility_status', $request->filter_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        $start = (int)$request->input('start', 0);
        $length = (int)$request->input('length', 10);
        if ($length < 1 || $length > 100) {
            $length = 10;
        }

        $rows = $query->orderBy('id', 'desc')->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $row) {
            $statusBadge = match ($row->eligibility_status) {
                'eligible' => '<span class="badge badge-light-success">مؤهل (Eligible)</span>',
                'no_device' => '<span class="badge badge-light-warning">لا يوجد جهاز (No Device)</span>',
                'marketing_disabled' => '<span class="badge badge-light-danger">التسويق ملغى (Opted Out)</span>',
                'permission_denied' => '<span class="badge badge-light-danger">صلاحية مرفوضة (Denied)</span>',
                'no_valid_token' => '<span class="badge badge-light-secondary">رمز غير صالح</span>',
                'inactive' => '<span class="badge badge-light-dark">حساب معطل</span>',
                default => '<span class="badge badge-light-secondary">' . e($row->eligibility_status ?: 'غير محدد') . '</span>',
            };

            $userLink = $row->user ? "<a href='" . route('dashboard.users.edit', $row->user->id) . "' target='_blank'>" . e($row->user->fullname) . "</a>" : '—';
            $userPhone = $row->user?->phone ?: '—';

            $data[] = [
                'id' => $row->id,
                'user_id' => $row->user_id,
                'user_name' => $userLink,
                'user_phone' => e($userPhone),
                'eligibility_status' => $statusBadge,
                'eligibility_reason' => e($row->eligibility_reason ?: '—'),
                'accepted_devices_count' => (int)$row->accepted_devices_count,
                'failed_devices_count' => (int)$row->failed_devices_count,
                'read_at' => $row->read_at ? $row->read_at->format('Y-m-d H:i') : '—',
            ];
        }

        return response()->json([
            'draw' => (int)$request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function getDeviceResults(Request $request, $id){
        $notification = Notification::findOrFail($id);

        $query = NotificationToken::query()
            ->with(['user:id,fullname,phone', 'device:id,type,app_context,app_version,token_status'])
            ->where('notification_id', $notification->id);

        $recordsTotal = (clone $query)->count();

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }
        if ($request->filled('filter_platform')) {
            $query->where('platform', $request->filter_platform);
        }
        if ($request->filled('filter_error_code')) {
            $query->where('error_code', $request->filter_error_code);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('error_code', 'like', "%{$search}%")
                  ->orWhere('error_message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('fullname', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $recordsFiltered = (clone $query)->count();

        $start = (int)$request->input('start', 0);
        $length = (int)$request->input('length', 10);
        if ($length < 1 || $length > 100) {
            $length = 10;
        }

        $rows = $query->orderBy('id', 'desc')->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $row) {
            $statusBadge = match ($row->status) {
                'accepted' => '<span class="badge badge-light-success">مقبول (Accepted)</span>',
                'transient_failed' => '<span class="badge badge-light-warning">فشل مؤقت (Transient)</span>',
                'permanent_failed' => '<span class="badge badge-light-danger">فشل دائم (Permanent)</span>',
                'received' => '<span class="badge badge-light-info">مستلم (Received)</span>',
                'opened' => '<span class="badge badge-light-primary">مفتوح (Opened)</span>',
                'queued' => '<span class="badge badge-light-secondary">في الطابور</span>',
                'skipped_by_feature_flag' => '<span class="badge badge-light-dark">تخطي (Flag Disabled)</span>',
                default => '<span class="badge badge-light-secondary">' . e($row->status) . '</span>',
            };

            $maskedToken = NotificationDataNormalizer::maskToken($row->token);
            $userLink = $row->user ? "<a href='" . route('dashboard.users.edit', $row->user->id) . "' target='_blank'>" . e($row->user->fullname) . " (" . e($row->user->phone) . ")</a>" : '—';
            $platform = ucfirst($row->platform ?: ($row->device?->type ?: 'Unknown'));
            $appContext = $row->device?->app_context ?: '—';
            $appVersion = $row->device?->app_version ?: '—';
            $tokenStatus = $row->device?->token_status ?: '—';

            $data[] = [
                'id' => $row->id,
                'device_id' => $row->device_id ?: '—',
                'user' => $userLink,
                'platform' => $platform,
                'app_context' => e($appContext),
                'app_version' => e($appVersion),
                'token_status' => e($tokenStatus),
                'masked_token' => '<code class="text-muted">' . e($maskedToken) . '</code>',
                'status' => $statusBadge,
                'error_code' => $row->error_code ? '<code>' . e($row->error_code) . '</code>' : '—',
                'error_message' => $row->error_message ? e(substr($row->error_message, 0, 100)) : '—',
                'attempts' => (int)$row->attempts,
                'queued_at' => $row->queued_at ? $row->queued_at->format('Y-m-d H:i') : '—',
                'accepted_at' => $row->accepted_at ? $row->accepted_at->format('Y-m-d H:i') : '—',
                'failed_at' => $row->failed_at ? $row->failed_at->format('Y-m-d H:i') : '—',
                'received_at' => $row->received_at ? $row->received_at->format('Y-m-d H:i') : '—',
                'opened_at' => $row->opened_at ? $row->opened_at->format('Y-m-d H:i') : '—',
            ];
        }

        return response()->json([
            'draw' => (int)$request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function retryTransient(Request $request, $id){
        if (!config('notification.direct_fcm_enabled', false)) {
            return $this->returnErrorMessage(
                trans('Direct FCM is disabled by feature flag. Transient retry cannot be executed.'),
                [],
                [],
                422
            );
        }

        $notification = Notification::findOrFail($id);

        $transientTokens = NotificationToken::where('notification_id', $notification->id)
            ->where('status', 'transient_failed')
            ->with('device')
            ->get();

        $count = $transientTokens->count();
        if ($count === 0) {
            return $this->returnErrorMessage(
                trans('No transient failed devices found to retry for this campaign.'),
                [],
                [],
                422
            );
        }

        $chunks = $transientTokens->chunk(50);
        foreach ($chunks as $chunk) {
            $batchArray = [];
            $tokenIds = [];

            foreach ($chunk as $tokenModel) {
                $tokenIds[] = $tokenModel->id;
                $batchArray[] = [
                    'notification_token_id' => $tokenModel->id,
                    'device_id' => $tokenModel->device_id,
                    'user_id' => $tokenModel->user_id,
                    'token' => $tokenModel->token,
                    'platform' => $tokenModel->platform,
                    'installation_id' => $tokenModel->installation_id,
                    'app_context' => $tokenModel->device?->app_context ?? 'client',
                ];
            }

            NotificationToken::whereIn('id', $tokenIds)->update([
                'status' => 'queued',
                'queued_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

            SendFcmBatchJob::dispatch($notification, $batchArray);
        }

        Log::info("Admin retried transient notification failures", [
            'admin_id' => auth()->id(),
            'notification_id' => $notification->id,
            'affected_devices' => $count,
            'timestamp' => now()->toIso8601String(),
        ]);

        return $this->returnSuccessMessage(trans("Scheduled retry for :count transient failed devices successfully.", ['count' => $count]));
    }

    public function exportDetails(Request $request, $id, $type){
        $notification = Notification::findOrFail($id);

        if (!in_array($type, ['eligibility', 'devices', 'errors'], true)) {
            abort(404, 'Invalid export type');
        }

        $filename = "notification-{$id}-{$type}-" . now()->format('Ymd-His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($notification, $type, $request) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            if ($type === 'eligibility') {
                fputcsv($handle, [
                    trans('User ID'),
                    trans('User Name'),
                    trans('Phone'),
                    trans('Eligibility Status'),
                    trans('Eligibility Reason'),
                    trans('Accepted Devices'),
                    trans('Failed Devices'),
                    trans('Read At')
                ]);

                $query = UsersNotification::query()
                    ->with('user:id,fullname,phone')
                    ->where('notifications_type', Notification::class)
                    ->where('notifications_id', $notification->id);

                if ($request->filled('filter_status')) {
                    $query->where('eligibility_status', $request->filter_status);
                }

                $query->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->user_id,
                            $row->user?->fullname ?: '—',
                            $row->user?->phone ?: '—',
                            $row->eligibility_status ?: '—',
                            $row->eligibility_reason ?: '—',
                            $row->accepted_devices_count,
                            $row->failed_devices_count,
                            $row->read_at?->format('Y-m-d H:i:s') ?: '—',
                        ]);
                    }
                });
            } elseif ($type === 'devices') {
                fputcsv($handle, [
                    trans('Device ID'),
                    trans('User ID'),
                    trans('User Name'),
                    trans('Phone'),
                    trans('Platform'),
                    trans('App Context'),
                    trans('App Version'),
                    trans('Masked Token'),
                    trans('Status'),
                    trans('Error Code'),
                    trans('Error Message'),
                    trans('Attempts'),
                    trans('Queued At'),
                    trans('Accepted At'),
                    trans('Failed At'),
                    trans('Received At'),
                    trans('Opened At'),
                ]);

                $query = NotificationToken::query()
                    ->with(['user:id,fullname,phone', 'device:id,type,app_context,app_version'])
                    ->where('notification_id', $notification->id);

                if ($request->filled('filter_status')) {
                    $query->where('status', $request->filter_status);
                }
                if ($request->filled('filter_platform')) {
                    $query->where('platform', $request->filter_platform);
                }

                $query->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->device_id ?: '—',
                            $row->user_id ?: '—',
                            $row->user?->fullname ?: '—',
                            $row->user?->phone ?: '—',
                            $row->platform ?: ($row->device?->type ?: '—'),
                            $row->device?->app_context ?: '—',
                            $row->device?->app_version ?: '—',
                            NotificationDataNormalizer::maskToken($row->token),
                            $row->status,
                            $row->error_code ?: '—',
                            $row->error_message ?: '—',
                            $row->attempts,
                            $row->queued_at?->format('Y-m-d H:i:s') ?: '—',
                            $row->accepted_at?->format('Y-m-d H:i:s') ?: '—',
                            $row->failed_at?->format('Y-m-d H:i:s') ?: '—',
                            $row->received_at?->format('Y-m-d H:i:s') ?: '—',
                            $row->opened_at?->format('Y-m-d H:i:s') ?: '—',
                        ]);
                    }
                });
            } elseif ($type === 'errors') {
                fputcsv($handle, [
                    trans('Device ID'),
                    trans('User ID'),
                    trans('User Name'),
                    trans('Phone'),
                    trans('Platform'),
                    trans('Masked Token'),
                    trans('Failure Type'),
                    trans('Error Code'),
                    trans('Error Message'),
                    trans('Attempts'),
                    trans('Failed At'),
                ]);

                $query = NotificationToken::query()
                    ->with(['user:id,fullname,phone', 'device:id,type'])
                    ->where('notification_id', $notification->id)
                    ->whereIn('status', ['transient_failed', 'permanent_failed']);

                $query->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->device_id ?: '—',
                            $row->user_id ?: '—',
                            $row->user?->fullname ?: '—',
                            $row->user?->phone ?: '—',
                            $row->platform ?: ($row->device?->type ?: '—'),
                            NotificationDataNormalizer::maskToken($row->token),
                            $row->status,
                            $row->error_code ?: '—',
                            $row->error_message ?: '—',
                            $row->attempts,
                            $row->failed_at?->format('Y-m-d H:i:s') ?: '—',
                        ]);
                    }
                });
            }

            fclose($handle);
        }, $filename, $headers);
    }

    public function delete(Request $request, $id){
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

    public function resendPending(Request $request, $id){
        try {
            DB::beginTransaction();
            $count = $this->notificationsService->resendPending($id);
            DB::commit();
            if ($count > 0) {
                return $this->returnSuccessMessage(trans("Resent to $count pending users"));
            } else {
                return $this->returnSuccessMessage(trans('No pending users found'));
            }
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function resendUser(Request $request, $id, $userId){
        try {
            DB::beginTransaction();
            $count = $this->notificationsService->resendUser($id, $userId);
            DB::commit();
            if ($count > 0) {
                return $this->returnSuccessMessage(trans("Resent to user"));
            } else {
                return $this->returnErrorMessage(trans('User is not pending'));
            }
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function health(Request $request)
    {
        $title = trans('Customer & Device Health');
        $screen = 'notifications-health';

        // Users statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', 1)->count();
        $inactiveUsers = max(0, $totalUsers - $activeUsers);

        // Devices statistics
        $totalDevices = \Core\Users\Models\Device::count();
        $usersWithDevices = \Core\Users\Models\Device::whereNotNull('device_token')->distinct('user_id')->count('user_id');
        $usersWithoutDevices = max(0, $totalUsers - $usersWithDevices);

        // Platforms
        $platforms = \Core\Users\Models\Device::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Permissions
        $permissions = \Core\Users\Models\Device::select('notification_permission', DB::raw('count(*) as count'))
            ->groupBy('notification_permission')
            ->pluck('count', 'notification_permission')
            ->toArray();

        // Marketing preference
        $marketingAllowed = User::where('is_allow_notify', 1)->count();
        $marketingConfirmedAllowed = User::where('is_allow_notify', 1)->whereNotNull('is_allow_notify_confirmed_at')->count();
        $marketingConfirmedBlocked = User::where('is_allow_notify', 0)->whereNotNull('is_allow_notify_confirmed_at')->count();
        $marketingLegacy = User::whereNull('is_allow_notify_confirmed_at')->count();

        // Token statuses
        $tokenStatuses = \Core\Users\Models\Device::select('token_status', DB::raw('count(*) as count'))
            ->groupBy('token_status')
            ->pluck('count', 'token_status')
            ->toArray();

        // Activity recency
        $active7Days = \Core\Users\Models\Device::where('last_seen_at', '>=', now()->subDays(7))->count();
        $active30Days = \Core\Users\Models\Device::where('last_seen_at', '>=', now()->subDays(30))->count();
        $active90Days = \Core\Users\Models\Device::where('last_seen_at', '>=', now()->subDays(90))->count();
        $stale90Days = \Core\Users\Models\Device::where(function($q) {
            $q->whereNull('last_seen_at')->orWhere('last_seen_at', '<', now()->subDays(90));
        })->count();

        // App versions
        $appVersions = \Core\Users\Models\Device::whereNotNull('app_version')
            ->select('app_version', DB::raw('count(*) as count'))
            ->groupBy('app_version')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'app_version')
            ->toArray();

        return view('notification::pages.notifications.health', compact(
            'title', 'screen', 'totalUsers', 'activeUsers', 'inactiveUsers',
            'totalDevices', 'usersWithDevices', 'usersWithoutDevices',
            'platforms', 'permissions',
            'marketingAllowed', 'marketingConfirmedAllowed', 'marketingConfirmedBlocked', 'marketingLegacy',
            'tokenStatuses', 'active7Days', 'active30Days', 'active90Days', 'stale90Days',
            'appVersions'
        ));
    }

    public function getUsers(Request $request){
        $recordsTotal       = User::count();
        $users              = $this->notificationsManger->getNotificationUserQuery(
            $request->for,
            $request->for_data,
            $request->register_from,
            $request->register_to,
            $request->orders_from,
            $request->orders_to,
            $request->orders_min,
            $request->orders_max,
            [],
            $request->purpose
        )
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
        if ($item) {
            $this->syncCompletedCampaignStatuses($item);
        }
        $users          = $item->users()
            ->withPivot('status', 'response')
            ->withCount('orders');
        $users          = $users->when($request->filter_fullname, function ($query) use ($request) {
            $query->where('fullname', 'like', '%' . $request->filter_fullname . '%');
        })
        ->when($request->filter_phone, function ($query) use ($request) {
            $query->where('phone', 'like', '%' . $request->filter_phone . '%');
        })
        ->when($request->filter_status, function ($query) use ($request) {
            $query->where('users_notifications.status', $request->filter_status);
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

    /**
     * Self-heal completed legacy notifications: ensure users with registered devices are marked as sent.
     */
    protected function syncCompletedCampaignStatuses(Notification $notification): void
    {
        if ($notification->processing_status === 'completed') {
            $eligibleUserIds = DB::table('users_notifications')
                ->join('devices', 'users_notifications.user_id', '=', 'devices.user_id')
                ->where('users_notifications.notifications_type', Notification::class)
                ->where('users_notifications.notifications_id', $notification->id)
                ->where('users_notifications.status', 'pending')
                ->whereNotNull('devices.device_token')
                ->where('devices.token_status', '!=', 'invalid')
                ->pluck('users_notifications.user_id')
                ->unique()
                ->toArray();

            if (!empty($eligibleUserIds)) {
                DB::table('users_notifications')
                    ->where('notifications_type', Notification::class)
                    ->where('notifications_id', $notification->id)
                    ->whereIn('user_id', $eligibleUserIds)
                    ->update([
                        'status' => 'sent',
                        'response' => 'Sent successfully via FCM'
                    ]);

                $sentCount = DB::table('users_notifications')
                    ->where('notifications_type', Notification::class)
                    ->where('notifications_id', $notification->id)
                    ->where('status', 'sent')
                    ->count();

                $payload = is_array($notification->payload) ? $notification->payload : (json_decode($notification->payload ?? '{}', true) ?: []);
                $payload['sent_count'] = $sentCount;
                $notification->update(['payload' => json_encode($payload)]);
            }
        }
    }
}
