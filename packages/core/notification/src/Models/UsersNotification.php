<?php

namespace Core\Notification\Models;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use Core\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UsersNotification extends Model {
    
	protected $table             = 'users_notifications';
    protected $guarded           = [];
    
    public function notification()
    {
        return $this->morphTo('notifications');
    }


 

}
