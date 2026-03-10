<?php

namespace Core\Settings\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use JohnDoe\BlogPackage\Models\Post;

class FormSubmitted
{
    use Dispatchable, SerializesModels;
    public $data = [];
    public $form_id ;

    public function __construct($form_id,$data)
    {
        $this->form_id = $form_id;
        $this->data = $data;

    }
}
