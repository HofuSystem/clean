<?php

namespace Core\Info\Models;

use Illuminate\Database\Eloquent\Model;
use Core\Settings\Models\CoreModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MapPoint extends CoreModel
{
    use SoftDeletes;
    protected $table             = 'map_points';

    protected $fillable          = [ 'lat', 'lng','district_id'];

}
