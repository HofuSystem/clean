<?php

namespace Core\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactorCode extends Model
{
    use HasFactory;
    protected $table='factor_auth_codes';
    protected $guarded=[];
}
