<?php

namespace Core\B2B\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPermissionTranslation extends Model
{
    public $timestamps  = false;
    protected $fillable = ['name', 'description'];
}
