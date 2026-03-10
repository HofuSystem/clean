<?php

namespace Core\Pages\Models;

use Illuminate\Database\Eloquent\Model;

class TestimonialTranslation extends Model
{
    protected $fillable  = ['name', 'title', 'body', 'location'];
    public $timestamps   = false;
}

