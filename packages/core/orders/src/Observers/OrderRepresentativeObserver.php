<?php

namespace Core\Orders\Observers;

use Core\Notification\Models\Notification;
use Core\Orders\Models\OrderRepresentative;
use Core\Settings\Services\SettingsService;
use Core\Orders\Services\OrderHistoryService;
use Core\Users\Models\User;

class OrderRepresentativeObserver
{
    /**
     * Handle the OrderRepresentative "creating" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function creating(OrderRepresentative $orderRepresentative)
    {
    
    }
    /**
     * Handle the OrderRepresentative "created" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function created(OrderRepresentative $orderRepresentative)
    {
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_representatives_using');
        $order             = $orderRepresentative->order;
        if(isset($order)){           
            if(isset($orderRepresentative->representative) and isset($notifyTypes) and !empty($notifyTypes)){
                $title          = 'طلب جديد';
                $message        = ' لديك طلب جديد من مرسل من الاداره تفقده رقم'.$orderRepresentative->order?->reference_id;
                $senderData = [
                    'id'        => auth()->user()->id,
                    'fullname'  => auth()->user()->fullname,
                    'phone'     => (string)auth()->user()->phone,
                    'image'     => (string)auth()->user()->avatarUrl,
                ];
                $data = [
                    'key'               => "order",
                    'key_id'            => $orderRepresentative->order?->id,
                    'status'            => $orderRepresentative->order?->status,
                    'title'             => $title,
                    'body'              => $message,
                    'order_id'          => $orderRepresentative->order?->id,
                    'order_driver_type' => $orderRepresentative->order?->status == 'pending'||$orderRepresentative->order?->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
                    'sender_data'       => $senderData,
                ];
               
                Notification::create([
                    'types'     => json_encode($notifyTypes), 
                    'for'       => 'users', 
                    'for_data'  => json_encode([$orderRepresentative->representative->id]),
                    'payload'   => json_encode($data), 
                    'title'     => $title, 
                    'body'      => $message, 
                    'sender_id' => auth()->user()->id,
                    'order_id'  => $orderRepresentative->order?->id,
                ]);
    
            }
        }
    }

    /**
     * Handle the OrderRepresentative "updating" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function updating(OrderRepresentative $orderRepresentative)
    {

    }
    /**
     * Handle the OrderRepresentative "updated" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function updated(OrderRepresentative $orderRepresentative)
    {
        // Check if the user_id has been changed
        $notifyTypes       = SettingsService::getDataBaseSetting('notify_representatives_using');
        $order              = $orderRepresentative->order;    
        if(isset($order)){
             // Check for representative change
             $orderHistoryService = app(OrderHistoryService::class);
             if ($orderRepresentative->isDirty('representative_id')) {
                $oldRepId = $orderRepresentative->getOriginal('representative_id');
                $newRepId = $orderRepresentative->representative_id;
                $newRep = User::find($newRepId);
                $oldRep = User::find($oldRepId);
                $orderHistoryService->logRepresentativeChange(
                    $orderRepresentative->order_id,
                    $orderRepresentative->type,
                    $orderRepresentative->getOriginal('representative_id'),
                    $orderRepresentative->representative_id,
                    $oldRep?->fullname ?? $oldRep?->email ?? null,
                    $newRep?->fullname ?? $newRep?->email ?? null
                );
            }
            
            // Check for date/time change
            if ($orderRepresentative->isDirty('date') || $orderRepresentative->isDirty('time') || $orderRepresentative->isDirty('to_time')) {
                $orderHistoryService->logRepresentativeDateTimeChange(
                    $orderRepresentative->order_id,
                    $orderRepresentative->type,
                    $orderRepresentative->getOriginal('date'),
                    $orderRepresentative->date,
                    $orderRepresentative->getOriginal('time') . ($orderRepresentative->getOriginal('to_time') ? ' - ' . $orderRepresentative->getOriginal('to_time') : ''),
                    $orderRepresentative->time . ($orderRepresentative->to_time ? ' - ' . $orderRepresentative->to_time : '')
                );
            }
            
            // Check for location change
            if ($orderRepresentative->isDirty('location') || $orderRepresentative->isDirty('lat') || $orderRepresentative->isDirty('lng')) {
                $orderHistoryService->logRepresentativeLocationChange(
                    $orderRepresentative->order_id,
                    $orderRepresentative->type,
                    $orderRepresentative->getOriginal('location') ?? "({$orderRepresentative->getOriginal('lat')}, {$orderRepresentative->getOriginal('lng')})",
                    $orderRepresentative->location ?? "({$orderRepresentative->lat}, {$orderRepresentative->lng})"
                );
            }
            if ($orderRepresentative->isDirty('representative_id')) {
                if(isset($orderRepresentative->representative) and isset($orderRepresentative->order) and isset($notifyTypes) and !empty($notifyTypes)){
                    $title          = 'طلب جديد';
                    $message        = ' لديك طلب جديد من مرسل من الاداره تفقده رقم'.$orderRepresentative->order?->reference_id;
                    $senderData = [
                        'id'        => auth()->user()->id,
                        'fullname'  => auth()->user()->fullname,
                        'phone'     => (string)auth()->user()->phone,
                        'image'     => (string)auth()->user()->avatarUrl,
                    ];
                    $data = [
                        'key'               => "order",
                        'key_id'            => $orderRepresentative->order?->id,
                        'status'            => $orderRepresentative->order?->status,
                        'title'             => $title,
                        'body'              => $message,
                        'order_id'          => $orderRepresentative->order?->id,
                        'order_driver_type' => $orderRepresentative->order?->status == 'pending'||$orderRepresentative->order?->status == 'receiving_driver_accepted' ? 'receipt' : 'delivery',
                        'sender_data'       => $senderData,
                    ];
                    Notification::create([
                        'types'     => json_encode($notifyTypes), 
                        'for'       => 'users', 
                        'for_data'  => json_encode([$orderRepresentative->representative->id]),
                        'payload'   => json_encode($data), 
                        'title'     => $title, 
                        'body'      => $message, 
                        'sender_id' => auth()->user()->id,
                        'order_id'  => $orderRepresentative->order?->id,
                    ]);
        
                }
            }

        }
    }
    /**
     * Handle the OrderRepresentative "saving" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function saving(OrderRepresentative $orderRepresentative)
    {

    }
    /**
     * Handle the OrderRepresentative "saved" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function saved(OrderRepresentative $orderRepresentative)
    {
        $order  = $orderRepresentative->order;  
        if (isset($order) and $orderRepresentative->isDirty('representative_id')) {
            $operatorId = $orderRepresentative?->representative?->leader_id;
            $orderRepresentative->order?->updateQuietly(['operator_id' => $operatorId]);
            if($orderRepresentative->type == 'receiver'){
                $order->update(['order_status_times' => ['select_receiving_driver' => [date("Y-m-d H:i"),auth()->user()->email]]]);
            }elseif($orderRepresentative->type == 'delivery'){
                $order->update(['order_status_times' => ['select_delivery_driver' => [date("Y-m-d H:i"),auth()->user()->email]]]);
            }elseif($orderRepresentative->type == 'technical'){
                $order->update(['order_status_times' => ['select_technical' => [date("Y-m-d H:i"),auth()->user()->email]]]);
            }
        }
    }

    /**
     * Handle the OrderRepresentative "deleted" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function deleted(OrderRepresentative $orderRepresentative)
    {

    }

    /**
     * Handle the OrderRepresentative "restored" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function restored(OrderRepresentative $orderRepresentative)
    {
        //
    }

    /**
     * Handle the OrderRepresentative "force deleted" event.
     *
     * @param  \Core\Orders\Models\OrderRepresentative  $orderRepresentative
     * @return void
     */
    public function forceDeleted(OrderRepresentative $orderRepresentative)
    {
        //
    }
}
