<?php

namespace Core\B2B\Models;

use Core\Settings\Models\CoreModel;
use App\Observers\GlobalModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalModelObserver::class])]
class B2BFinancial extends CoreModel
{
    protected $table    = 'b2b_financials';
    protected $fillable = ['company_id', 'reference_id', 'amount', 'collection_date', 'note', 'creator_id', 'updater_id'];
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

    public function scopeSearch($query)
    {
        if (request()->has('filters.reference_id') && !empty(request('filters.reference_id'))) {
            $query->where('reference_id', 'LIKE', '%' . request('filters.reference_id') . '%');
        }
        if (request()->has('filters.company_id') && !empty(request('filters.company_id'))) {
            $query->where('company_id', request('filters.company_id'));
        }
    }

    // ─── Actions Attributes ───────────────────────────────────────────────────

    public function getActionsAttribute()
    {
        return $this->getActions('b2b-financials');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('b2b-financials');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('b2b-financials');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('b2b-financials');
    }
}
