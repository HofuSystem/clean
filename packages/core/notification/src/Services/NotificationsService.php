<?php

namespace Core\Notification\Services;

use Core\Comments\Services\CommentingService;
use Core\Notification\Models\Notification;
use Core\Notification\DataResources\NotificationsResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class NotificationsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return Notification::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, [
            'types', 'for', 'for_data', 'purpose', 'processing_status', 'title', 'body', 'media',
            'sender_id', 'translations', 'register_from', 'register_to', 'orders_from', 'orders_to',
            'orders_min', 'orders_max', 'idempotency_key'
        ]),ARRAY_FILTER_USE_KEY);

        if(isset($recordData['for_data']) and !is_string($recordData['for_data'])){
            $recordData['for_data'] = json_encode($recordData['for_data']);
        }
        if(isset($recordData['types']) and !is_string($recordData['types'])){
            $recordData['types'] = json_encode($recordData['types']);
        }
        if(!isset($recordData['for_data'])){
            $recordData['for_data'] = json_encode([]);
        }

        if (!$id && !empty($recordData['idempotency_key']) && Notification::hasTableColumn('idempotency_key')) {
            $existing = Notification::query()
                ->where('idempotency_key', $recordData['idempotency_key'])
                ->first();
            if ($existing) {
                return $existing;
            }
        } else {
            unset($recordData['idempotency_key']);
        }

        if (!$id) {
            $recentDuplicateQuery = Notification::query()
                ->where('created_at', '>=', now()->subMinutes(10))
                ->where('sender_id', auth()->id())
                ->where('title', $recordData['title'] ?? '')
                ->where('body', $recordData['body'] ?? '')
                ->where('types', $recordData['types'] ?? '[]')
                ->where('for', $recordData['for'] ?? '')
                ->where('for_data', $recordData['for_data']);

            foreach (['purpose', 'media', 'register_from', 'register_to', 'orders_from', 'orders_to', 'orders_min', 'orders_max'] as $field) {
                $recentDuplicateQuery->where($field, $recordData[$field] ?? null);
            }

            $recentDuplicate = $recentDuplicateQuery->latest('id')->first();
            if ($recentDuplicate) {
                return $recentDuplicate;
            }
        }

        try {
            $record = Notification::updateOrCreate(['id' => $id],$recordData);
        } catch (QueryException $e) {
            if (!$id && !empty($recordData['idempotency_key'])) {
                $existing = Notification::query()
                    ->where('idempotency_key', $recordData['idempotency_key'])
                    ->first();
                if ($existing) {
                    return $existing;
                }
            }
            throw $e;
        }

        return $record;
    }

    public function get(int $id){
        return Notification::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record = Notification::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){
        $recordsTotal       = Notification::count();
        $recordsFiltered    = Notification::search()->count();

        $desiredColumns = [
            'id', 'types', 'channel', 'for', 'purpose', 'processing_status', 'title', 'body', 'media',
            'sender_id', 'created_at', 'targeted_users_count', 'eligible_users_count',
            'eligible_devices_count', 'accepted_by_fcm_count', 'permanent_failed_count',
            'transient_failed_count', 'received_count', 'opened_count'
        ];

        // Safe column filtering in case migrations are pending in production
        $existingColumns = Notification::getTableColumns();
        $selectedColumns = !empty($existingColumns)
            ? array_values(array_intersect($desiredColumns, $existingColumns))
            : array_values(array_diff($desiredColumns, ['channel']));

        $records = Notification::select($selectedColumns)
        ->with(['sender'])
        ->withCount([
            'users as sent_count' => function ($query) {
                $query->where('users_notifications.notifications_type', Notification::class)
                      ->where('users_notifications.status', 'sent');
            },
            'users as pending_count' => function ($query) {
                $query->where('users_notifications.notifications_type', Notification::class)
                      ->where('users_notifications.status', 'pending');
            }
        ])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => NotificationsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Notification::find($value['id'])->update([$orderBy=>$value['order']]);
        }
    }
    public function import(array $items){
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item,$item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int $id,string $content,int | null $parent_id){
       return $this->commentingService->comment(
         Notification::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Notification::count();
    }
    public function trashCount(){
        return Notification::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Notification::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }

    public function resendPending(int $id){
        $notification = Notification::findOrFail($id);
        $pendingUsers = $notification->users()
            ->wherePivot('status', 'pending')
            ->where('users_notifications.notifications_type', Notification::class)
            ->get();
        if ($pendingUsers->count() > 0) {
            \Core\Notification\Helpers\NotificationsManger::getInstance()->resendToUsers($notification, $pendingUsers);
        }
        $this->completeLegacyRetryIfSettled($notification);
        return $pendingUsers->count();
    }

    protected function completeLegacyRetryIfSettled(Notification $notification): void
    {
        if (config('notification.direct_fcm_enabled', false)
            || $notification->processing_status !== 'processing') {
            return;
        }

        $hasPendingUsers = DB::table('users_notifications')
            ->where('notifications_type', Notification::class)
            ->where('notifications_id', $notification->id)
            ->where('status', 'pending')
            ->exists();

        if (!$hasPendingUsers) {
            $notification->update([
                'processing_status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    public function resendUser(int $id, int $userId){
        $notification = Notification::findOrFail($id);
        $user = $notification->users()
            ->wherePivot('status', 'pending')
            ->where('users.id', $userId)
            ->where('users_notifications.notifications_type', Notification::class)
            ->get();
        if ($user->count() > 0) {
            \Core\Notification\Helpers\NotificationsManger::getInstance()->resendToUsers($notification, $user);
        }
        $this->completeLegacyRetryIfSettled($notification);
        return $user->count();
    }
}
