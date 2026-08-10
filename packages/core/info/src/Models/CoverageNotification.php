<?php

namespace Core\Info\Models;

use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class CoverageNotification extends Model
{
    protected $table = 'coverage_notifications';

    protected $fillable = [
        'user_id',
        'city_id',
        'district_id',
        'type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
}
