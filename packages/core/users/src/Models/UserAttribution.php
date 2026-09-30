<?php

namespace Core\Users\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttribution extends Model
{
    protected $table = 'user_attributions';
    
    protected $guarded = [];

    protected $casts = [
        'is_attributed' => 'boolean',
        'first_seen_at' => 'datetime',
        'branch_raw_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
