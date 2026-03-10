<?php

namespace Core\Pages\Models;

use Illuminate\Database\Eloquent\Model;

class WorkStepTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'description'];
}

