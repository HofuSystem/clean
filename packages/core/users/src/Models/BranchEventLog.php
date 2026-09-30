<?php

namespace Core\Users\Models;

use Illuminate\Database\Eloquent\Model;

class BranchEventLog extends Model
{
    protected $table = 'branch_event_logs';
    
    protected $guarded = [];

    protected $casts = [
        'is_attributed' => 'boolean',
        'event_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
