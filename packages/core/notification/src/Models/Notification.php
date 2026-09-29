<?php

namespace Core\Notification\Models;

use Core\Notification\Helpers\NotificationChannelResolver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Notification\Observers\NotificationObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Orders\Models\Order;

#[ObservedBy([NotificationObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Notification extends CoreModel {

	protected $table             = 'notifications';
	protected $fillable          = [
        'types',
        'for',
        'for_data',
        'purpose',
        'channel',
        'processing_status',
        'started_at',
        'completed_at',
        'targeted_users_count',
        'eligible_users_count',
        'eligible_devices_count',
        'accepted_by_fcm_count',
        'permanent_failed_count',
        'transient_failed_count',
        'received_count',
        'opened_count',
        'payload',
        'title',
        'body',
        'media',
        'sender_id',
        'order_id',
        'register_from',
        'register_to',
        'orders_from',
        'orders_to',
        'orders_min',
        'orders_max',
        'creator_id',
        'updater_id'
    ];
    protected $guarded           = [];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'targeted_users_count' => 'integer',
        'eligible_users_count' => 'integer',
        'eligible_devices_count' => 'integer',
        'accepted_by_fcm_count' => 'integer',
        'permanent_failed_count' => 'integer',
        'transient_failed_count' => 'integer',
        'received_count' => 'integer',
        'opened_count' => 'integer',
    ];

    /**
     * Cache table columns to avoid repeated schema queries and guard against pending migrations.
     *
     * @var array<string>|null
     */
    protected static ?array $tableColumns = null;

    /**
     * Get all column names for the notifications table.
     *
     * @return array<string>
     */
    public static function getTableColumns(): array
    {
        if (static::$tableColumns === null) {
            try {
                static::$tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing((new static)->getTable());
            } catch (\Throwable $e) {
                static::$tableColumns = [];
            }
        }

        return static::$tableColumns;
    }

    /**
     * Check if a column exists on the notifications table.
     *
     * @param string $column
     * @return bool
     */
    public static function hasTableColumn(string $column): bool
    {
        $columns = static::getTableColumns();
        return empty($columns) ? false : in_array($column, $columns, true);
    }

    //start Scopes
    function scopeSearch($query){

        //filter select on  types
        if((request()->has("filters.types")) and !empty(request("filters.types"))){
            $type = request("filters.types");
            $channelMap = [
                'apps' => 'app_fcm',
                'app_fcm' => 'app_fcm',
                'fcm' => 'app_fcm',
                'whats_app' => 'whatsapp',
                'whatsapp' => 'whatsapp',
                'sms' => 'sms',
                'email' => 'email',
                'in_app' => 'in_app',
            ];
            $mappedChannel = $channelMap[$type] ?? null;

            $query->where(function($q) use ($type, $mappedChannel) {
                $q->where('types', $type)
                  ->orWhere('types', 'LIKE', '%"' . $type . '"%')
                  ->orWhere('types', 'LIKE', '%' . $type . '%');
                if ($mappedChannel && static::hasTableColumn('channel')) {
                    $q->orWhere('channel', $mappedChannel);
                }
            });
        }

        //filter select on  channel
        if((request()->has("filters.channel")) and !empty(request("filters.channel"))){
            $channel = request("filters.channel");
            $hasChannelCol = static::hasTableColumn('channel');
            $query->where(function($sub) use ($channel, $hasChannelCol) {
                $applyLegacy = function($s) use ($channel) {
                    if ($channel === 'app_fcm') {
                        $s->where('types', 'LIKE', '%apps%')
                          ->orWhere('types', 'LIKE', '%app_fcm%')
                          ->orWhere('types', 'LIKE', '%fcm%')
                          ->orWhere('types', 'LIKE', '%push%');
                    } elseif ($channel === 'whatsapp') {
                        $s->where('types', 'LIKE', '%whats_app%')
                          ->orWhere('types', 'LIKE', '%whatsapp%');
                    } elseif ($channel === 'sms') {
                        $s->where('types', 'LIKE', '%sms%');
                    } elseif ($channel === 'email') {
                        $s->where('types', 'LIKE', '%email%')
                          ->orWhere('types', 'LIKE', '%mail%');
                    } elseif ($channel === 'in_app') {
                        $s->where('types', 'LIKE', '%in_app%')
                          ->orWhere('types', 'LIKE', '%inapp%');
                    } elseif ($channel === 'legacy_unknown') {
                        $s->whereNull('types')
                          ->orWhere('types', '')
                          ->orWhere('types', '[]')
                          ->orWhere('types', 'null');
                    }
                };

                if ($hasChannelCol) {
                    $sub->where('channel', $channel)
                        ->orWhere(function($s) use ($applyLegacy) {
                            $s->whereNull('channel')->where(function($t) use ($applyLegacy) {
                                $applyLegacy($t);
                            });
                        });
                } else {
                    $sub->where(function($t) use ($applyLegacy) {
                        $applyLegacy($t);
                    });
                }
            });
        }

        //filter select on  for
        if((request()->has("filters.for")) and !empty(request("filters.for"))){
            $query->where("for",request("filters.for"));
        }

        //filter select on purpose
        if((request()->has("filters.purpose")) and !empty(request("filters.purpose"))){
            $purpose = request("filters.purpose");
            $hasPurposeCol = static::hasTableColumn('purpose');
            $hasOrderCol = static::hasTableColumn('order_id');
            $query->where(function($sub) use ($purpose, $hasPurposeCol, $hasOrderCol) {
                $applyLegacy = function($s) use ($purpose, $hasOrderCol) {
                    if ($purpose === 'authentication') {
                        $s->where(function($t) {
                            $t->where('title', 'LIKE', '%verify%')
                              ->orWhere('title', 'LIKE', '%رمز التحقق%')
                              ->orWhere('title', 'LIKE', '%كود التحقق%')
                              ->orWhere('title', 'LIKE', '%otp%')
                              ->orWhere('body', 'LIKE', '%verify message%')
                              ->orWhere('body', 'LIKE', '%رمز التحقق%')
                              ->orWhere('body', 'LIKE', '%كود التحقق%')
                              ->orWhere('body', 'LIKE', '%رمز التأكيد%')
                              ->orWhere('body', 'LIKE', '%كود الدخول%')
                              ->orWhere('body', 'LIKE', '%otp%');
                        });
                    } elseif ($purpose === 'transactional') {
                        $s->where(function($t) use ($hasOrderCol) {
                            if ($hasOrderCol) {
                                $t->whereNotNull('order_id')
                                  ->orWhere('title', 'LIKE', '%طلب%')
                                  ->orWhere('title', 'LIKE', '%سائق%')
                                  ->orWhere('title', 'LIKE', '%مندوب%')
                                  ->orWhere('title', 'LIKE', '%توصيل%')
                                  ->orWhere('body', 'LIKE', '%طلب%')
                                  ->orWhere('body', 'LIKE', '%سائق%')
                                  ->orWhere('body', 'LIKE', '%مندوب%')
                                  ->orWhere('body', 'LIKE', '%توصيل%');
                            } else {
                                $t->where('title', 'LIKE', '%طلب%')
                                  ->orWhere('title', 'LIKE', '%سائق%')
                                  ->orWhere('title', 'LIKE', '%مندوب%')
                                  ->orWhere('title', 'LIKE', '%توصيل%')
                                  ->orWhere('body', 'LIKE', '%طلب%')
                                  ->orWhere('body', 'LIKE', '%سائق%')
                                  ->orWhere('body', 'LIKE', '%مندوب%')
                                  ->orWhere('body', 'LIKE', '%توصيل%');
                            }
                        });
                    } elseif ($purpose === 'marketing') {
                        $s->where(function($t) use ($hasOrderCol) {
                            if ($hasOrderCol) {
                                $t->whereNull('order_id');
                            }
                            $t->where(function($m) {
                                $m->where('title', 'LIKE', '%خصم%')
                                  ->orWhere('title', 'LIKE', '%عرض%')
                                  ->orWhere('title', 'LIKE', '%عروض%')
                                  ->orWhere('title', 'LIKE', '%كوبون%')
                                  ->orWhere('body', 'LIKE', '%خصم%')
                                  ->orWhere('body', 'LIKE', '%عرض%')
                                  ->orWhere('body', 'LIKE', '%عروض%')
                                  ->orWhere('body', 'LIKE', '%كوبون%')
                                  ->orWhere('body', 'LIKE', '%سلة متروكة%');
                            });
                        });
                    } elseif ($purpose === 'system') {
                        $s->where(function($t) {
                            $t->where('title', 'LIKE', '%صيانة%')
                              ->orWhere('title', 'LIKE', '%تحديث النظام%')
                              ->orWhere('body', 'LIKE', '%صيانة%')
                              ->orWhere('body', 'LIKE', '%تحديث النظام%');
                        });
                    } elseif ($purpose === 'legacy_unknown') {
                        $s->where(function($t) use ($hasOrderCol) {
                            if ($hasOrderCol) {
                                $t->whereNull('order_id');
                            }
                            $t->where(function($b) {
                                $b->whereNull('title')->orWhere('title', '');
                            })->where(function($b) {
                                $b->whereNull('body')->orWhere('body', '');
                            });
                        });
                    }
                };

                if ($hasPurposeCol) {
                    $sub->where('purpose', $purpose)
                        ->orWhere(function($s) use ($applyLegacy) {
                            $s->whereNull('purpose')->where(function($t) use ($applyLegacy) {
                                $applyLegacy($t);
                            });
                        });
                } else {
                    $sub->where(function($t) use ($applyLegacy) {
                        $applyLegacy($t);
                    });
                }
            });
        }

        //filter select on processing_status
        if((request()->has("filters.processing_status")) and !empty(request("filters.processing_status"))){
            if (static::hasTableColumn('processing_status')) {
                $query->where("processing_status",request("filters.processing_status"));
            }
        }

        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->where("title","LIKE","%".request("filters.title")."%");
        }

        //filter text on  body
        if((request()->has("filters.body")) and !empty(request("filters.body"))){
            $query->where("body","LIKE","%".request("filters.body")."%");
        }

        //filter select on  sender
        if((request()->has("filters.sender_id")) and !empty(request("filters.sender_id"))){
            $query->whereRelation("sender","id",request("filters.sender_id"));
        }

        //filter date on  created_at
        if((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))){
            $query->whereDate("created_at",">=",Carbon::parse(request("filters.from_created_at")));
        }

        if((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))){
            $query->whereDate("created_at","<=",Carbon::parse(request("filters.to_created_at")));
        }

        //filter date on  updated_at
        if((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))){
            $query->whereDate("updated_at",">=",Carbon::parse(request("filters.from_updated_at")));
        }

        if((request()->has("for_user")) and !empty(request("for_user"))){
            $query->whereHas('users',function($userQuery){
                $userQuery->where('users.id',request("for_user"));
            });
        }
        if((request()->has("filters.phone")) and !empty(request("filters.phone"))){
            $query->whereHas('users',function($userQuery){
                $userQuery->where('users.phone','LIKE','%'.request("filters.phone").'%');
            });
        }
        if((request()->has("filters.reference_id")) and !empty(request("filters.reference_id"))){
            $query->whereHas('order',function($orderQuery){
                $orderQuery->where('orders.reference_id','LIKE','%'.request("filters.reference_id").'%');
            });
        }
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereHas('order.client.profile',function($profileQuery){
                $profileQuery->where('profiles.city_id',request("filters.city_id"));
            });
        }
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
        

    }

    //end Scopes

    //start relations

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    public function sender(){
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
    public function users()
    {
        return $this->morphToMany(User::class, 'notifications', 'users_notifications');
    }

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class, 'notification_id', 'id');
    }

    //end relations

    //start Attributes

    public function getForDataArrayAttribute(){
        return json_decode($this->for_data,true) ?? [];
    }
    public function getActionsAttribute(){
      return $this->getActions('notifications');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('notifications');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('notifications');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('notifications');
    }

    public function getChannelAttribute(): string
    {
        return $this->getChannel();
    }

    public function getResolvedPurposeAttribute(): string
    {
        return $this->getPurpose();
    }

    public function getChannel(): string
    {
        return NotificationChannelResolver::resolveChannel($this);
    }

    public function getChannels(): array
    {
        return NotificationChannelResolver::resolveChannels($this);
    }

        public function getResolvedPurpose(): string
    {
        return $this->getPurpose();
    }

    public function getPurpose(): string
    {
        return NotificationChannelResolver::resolvePurpose($this);
    }

    public function getTransportType(): string
    {
        $deliv = $this->getDeliveryChannel();
        return NotificationChannelResolver::resolveTransportType($this->getChannel(), $deliv);
    }

    public function isFcm(): bool
    {
        return NotificationChannelResolver::isFcm($this);
    }

    public function isWhatsApp(): bool
    {
        return in_array(NotificationChannelResolver::CHANNEL_WHATSAPP, $this->getChannels(), true);
    }

    public function isSms(): bool
    {
        return in_array(NotificationChannelResolver::CHANNEL_SMS, $this->getChannels(), true);
    }

    public function isEmail(): bool
    {
        return in_array(NotificationChannelResolver::CHANNEL_EMAIL, $this->getChannels(), true);
    }

    public function getDeliveryChannel(): string
    {
        $payload = is_array($this->payload) ? $this->payload : json_decode($this->payload ?? '{}', true);

        // 1. Immutable snapshot explicitly saved at campaign creation takes highest priority
        if (!empty($payload['delivery_channel'])) {
            if ($payload['delivery_channel'] === 'direct_fcm') {
                return 'direct_fcm';
            }
            if (in_array($payload['delivery_channel'], ['legacy_topic', 'legacy'], true)) {
                return 'legacy_topic';
            }
            return (string) $payload['delivery_channel'];
        }

        // If channel is non-FCM (WhatsApp, SMS, Email, In-App):
        $channel = $this->getChannel();
        if ($channel !== NotificationChannelResolver::CHANNEL_APP_FCM && $channel !== NotificationChannelResolver::CHANNEL_LEGACY_UNKNOWN) {
            return $channel;
        }

        // 2. Historical campaigns without snapshot:
        // Do NOT classify as direct_fcm merely because notification_tokens rows exist!
        // Historical campaigns (like 1120558) created notification_tokens rows during legacy topic subscription.
        // Direct FCM is only determined if there is unambiguous direct evidence:
        // E.g., accepted_by_fcm_count > 0, OR notification_tokens having topic='direct'.
        // If it was legacy topic (e.g. topic != 'direct', status='success', etc.), or unprovable, treat safely as 'legacy_topic'.
        if ($this->accepted_by_fcm_count > 0) {
            return 'direct_fcm';
        }

        if ($this->relationLoaded('notificationTokens')
            ? $this->notificationTokens->where('topic', 'direct')->count() > 0
            : $this->notificationTokens()->where('topic', 'direct')->exists()) {
            return 'direct_fcm';
        }

        return 'legacy_topic';
    }

    public function getDeliveryMetricLabel(): string
    {
        $channel = $this->getDeliveryChannel();
        if ($channel === 'direct_fcm') {
            return trans('قُبل من FCM');
        }
        if ($channel === 'whatsapp') {
            return trans('بوابة WhatsApp');
        }
        if ($channel === 'sms') {
            return trans('بوابة SMS');
        }
        if ($channel === 'email') {
            return trans('خادم Email');
        }

        return 'Legacy Topic Subscription';
    }

    public function getDeliveryMetricValue(): string
    {
        $channel = $this->getDeliveryChannel();
        if ($channel === 'direct_fcm') {
            return (string) ($this->accepted_by_fcm_count ?? 0);
        }
        if (in_array($channel, ['whatsapp', 'sms', 'email'], true)) {
            $sent = $this->sent_count ?? $this->users()->wherePivot('status', 'sent')->count();
            return $sent > 0 ? (string) $sent : '—';
        }

        $sentCount = $this->sent_count ?? $this->users()->wherePivot('status', 'sent')->count();
        return $sentCount > 0 ? (string) $sentCount : '—';
    }
    //end Attributes

}
