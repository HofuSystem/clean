<?php

namespace Core\Pages\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['point', 'us_text', 'them_text'];
}

