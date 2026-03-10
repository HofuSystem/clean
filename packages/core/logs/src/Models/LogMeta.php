<?php

namespace Core\Logs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LogMeta extends Model
{
    protected $table = 'logs_sys_meta';
    protected $fillable = ['key','value','parent_id','created_by','updated_by'];
    protected $guarded = [];
    use HasFactory;
}
