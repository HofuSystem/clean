<?php

namespace Core\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\GlobalModelObserver;

#[ObservedBy([GlobalModelObserver::class])]

class OrderHistory extends Model
{
    protected $table = 'order_histories';
    
    protected $fillable = [
        'order_id',
        'action_type',
        'notes',
        'changed_by',
        'old_value',
        'new_value',
    ];
    
    protected $casts = [
        'changed_by' => 'array',
        'old_value' => 'array',
        'new_value' => 'array',
    ];
    
    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    
    // Helper method to get formatted changed_by info
    public function getChangedByNameAttribute()
    {
        return $this->changed_by['name'] ?? trans('System');
    }
    
    public function getChangedByEmailAttribute()
    {
        return $this->changed_by['email'] ?? null;
    }
    
    public function getChangedByPhoneAttribute()
    {
        return $this->changed_by['phone'] ?? null;
    }
    
    public function getChangedByUserIdAttribute()
    {
        return $this->changed_by['user_id'] ?? null;
    }
}

