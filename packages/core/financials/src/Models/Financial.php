<?php

namespace Core\Financials\Models;

use Core\Settings\Models\CoreModel;
use App\Observers\GlobalModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

use Core\B2B\Models\Company;

#[ObservedBy([GlobalModelObserver::class])]
class Financial extends CoreModel
{
    protected $table    = 'financials';
    protected $fillable = ['company_id', 'user_id', 'reference_id', 'amount', 'type', 'collection_date', 'note', 'attachment', 'creator_id', 'updater_id'];
    protected $guarded  = [];

    protected $casts = [
        'collection_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->reference_id)) {
                $unique = false;
                while (!$unique) {
                    $ref = 'Fin-' . str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                    if (!static::where('reference_id', $ref)->exists()) {
                        $model->reference_id = $ref;
                        $unique = true;
                    }
                }
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\Core\Users\Models\User::class, 'user_id', 'id');
    }

    public function scopeSearch($query)
    {
        if (request()->has('filters.reference_id') && !empty(request('filters.reference_id'))) {
            $query->where('reference_id', 'LIKE', '%' . request('filters.reference_id') . '%');
        }
        if (request()->has('filters.company_id') && !empty(request('filters.company_id'))) {
            $query->where('company_id', request('filters.company_id'));
        }
        if (request()->has('filters.user_id') && !empty(request('filters.user_id'))) {
            $query->where('user_id', request('filters.user_id'));
        }
    }

    // ─── Actions Attributes ───────────────────────────────────────────────────

    public function getActionsAttribute()
    {
        return $this->getActions('financials');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('financials');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('financials');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('financials');
    }
}
