<?php

namespace Core\Users\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserSecSettingsUpdated
{
    use Dispatchable, SerializesModels;
    public $user_id;

    public function __construct($user_id)
    {
        $this->user_id= $user_id;
    }
}
