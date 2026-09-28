<?php

namespace Core\Notification\Models;

use Core\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UsersNotification extends Model {
    
	protected $table             = 'users_notifications';
    public $timestamps           = false;
    protected $guarded           = [];

    protected $casts = [
        'read_at' => 'datetime',
        'accepted_devices_count' => 'integer',
        'failed_devices_count' => 'integer',
    ];
    
    public function notification()
    {
        return $this->morphTo('notifications');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
