<?php

namespace Core\Notification\Models;

use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationToken extends Model
{
    protected $table = 'notification_tokens';

    protected $fillable = [
        'topic',
        'token',
        'status',
        'title',
        'notification_id',
        'user_id',
        'device_id',
        'installation_id',
        'platform',
        'fcm_message_id',
        'error_code',
        'error_message',
        'attempts',
        'queued_at',
        'accepted_at',
        'failed_at',
        'received_at',
        'opened_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'queued_at' => 'datetime',
        'accepted_at' => 'datetime',
        'failed_at' => 'datetime',
        'received_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
