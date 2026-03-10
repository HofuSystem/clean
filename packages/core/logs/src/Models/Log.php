<?php

namespace Core\Logs\Models;
use Core\Cache\Services\CacheMangerService;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $table='logs_sys';
    protected $guarded=[];

    //Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    public function scopeSearch($query)
    {
        if (request()->has('search'))
            return $query->where('name', 'LIKE', '%' . request('search') . '%');
    }
   

    /**
     * The "boot" method for the  model.
     */
    protected static function boot()
    {
        parent::boot();

        // Add event listeners
        static::creating(function ($model) {
        });

        static::created(function ($model) {
        });

        static::updating(function ($model) {
            // Logic for "updating"
        });

        static::updated(function ($model) {
        });

        static::deleting(function ($model) {
            // Logic for "deleting"
        });

        static::deleted(function ($model) {
            CacheMangerService::clearEntityRecord('logs-sys',$model);
        });

        static::saving(function ($model) {
            // Logic for "saving"
        });

        static::saved(function ($model) {
            CacheMangerService::clearEntityRecord('logs-sys',$model);
        });

        static::restoring(function ($model) {
            // Logic for "restoring"
        });

        static::restored(function ($model) {
            CacheMangerService::clearEntityRecord('logs-sys',$model);
        });
    }

}