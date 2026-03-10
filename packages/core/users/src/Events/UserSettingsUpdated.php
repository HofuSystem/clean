<?php

namespace Core\Users\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use JohnDoe\BlogPackage\Models\Post;

class UserSettingsUpdated
{
    use Dispatchable, SerializesModels;
    public $user_id;

    public function __construct($user_id)
    {
        $this->user_id= $user_id;

    }
}
